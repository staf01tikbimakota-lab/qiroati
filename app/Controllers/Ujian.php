<?php

namespace App\Controllers;

use App\Libraries\Kenaikan;

/**
 * Peserta ujian kenaikan jilid. Hasil "lulus" otomatis: mencatat progress (lulus jilid),
 * menaikkan siswa ke jilid di atasnya, dan memperbarui penempatan.
 */
class Ujian extends CrudController
{
    protected string $table = 'ujian';
    protected string $path = 'ujian';
    protected string $title = 'Ujian Kenaikan Jilid';
    protected array $fields = ['siswa_id', 'tgl_ujian', 'hasil', 'nilai', 'catatan'];

    protected function query()
    {
        return db_connect()->table('ujian')->select('ujian.*, siswa.nama AS siswa_nama, siswa.nis')
            ->join('siswa', 'siswa.id = ujian.siswa_id', 'left');
    }

    protected function order(): string
    {
        return 'ujian.tgl_ujian DESC, ujian.id DESC';
    }

    protected function rules(?int $id): array
    {
        return [
            'siswa_id' => 'required|is_natural_no_zero',
            'tgl_ujian' => 'required|valid_date[Y-m-d]',
            'hasil' => 'required|in_list[menunggu,lulus,tidak_lulus]',
            'nilai' => 'permit_empty|in_list[A,B+,B,C,D]',
            'catatan' => 'permit_empty|max_length[200]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $db = db_connect();
        $tid = $this->tpqId();
        $this->own('siswa', $d['siswa_id'], 'Siswa');
        $s = $db->table('siswa')->where(['id' => $d['siswa_id'], 'tpq_id' => $tid])->get()->getRowArray();
        $old = $id ? $db->table('ujian')->where(['id' => $id, 'tpq_id' => $tid])->get()->getRowArray() : null;

        if ($old && (int) $old['diproses'] === 1) {
            if ((int) $old['siswa_id'] !== (int) $d['siswa_id'] || $d['hasil'] !== 'lulus') {
                throw new \RuntimeException('Hasil lulus sudah diproses (siswa sudah naik jilid); siswa dan hasil tidak dapat diubah.');
            }
            $d['jilid'] = $old['jilid'];
        } else {
            // Jilid yang diujikan = jilid siswa saat ini
            if ($s['status'] !== 'aktif') {
                throw new \RuntimeException('Hanya siswa aktif yang dapat mengikuti ujian.');
            }
            $d['jilid'] = $s['jilid'];
        }
        return $d;
    }

    protected function afterSave(int $id, array $d, bool $isNew): void
    {
        $db = db_connect();
        $tid = $this->tpqId();
        $u = $db->table('ujian')->where('id', $id)->get()->getRowArray();
        if ($u['hasil'] !== 'lulus' || (int) $u['diproses'] === 1) {
            return;
        }

        $s = $db->table('siswa')->select('nama, halaman')->where('id', $u['siswa_id'])->get()->getRowArray();
        $p = [
            'tpq_id' => $tid, 'siswa_id' => $u['siswa_id'], 'guru_id' => null, 'tanggal' => $u['tgl_ujian'],
            'jilid' => $u['jilid'], 'halaman' => max(1, (int) $s['halaman']), 'nilai' => $u['nilai'],
            'status' => 'lulus_jilid', 'catatan' => 'Lulus ujian jilid ' . $u['jilid'] . ($u['catatan'] ? ' — ' . $u['catatan'] : ''),
        ];
        $db->table('progress')->insert($p);
        $next = Kenaikan::proses($tid, $p);
        $db->table('ujian')->where('id', $id)->update(['diproses' => 1]);

        $this->notice = $next
            ? "{$s['nama']} lulus ujian jilid {$u['jilid']} dan naik ke jilid {$next}."
            : "{$s['nama']} lulus ujian jilid {$u['jilid']} (tahap akhir) dan dinyatakan lulus TPQ.";
    }

    protected function beforeDelete(int $id): void
    {
        $r = db_connect()->table('ujian')->where(['id' => $id, 'tpq_id' => $this->tpqId()])->get()->getRowArray();
        if ($r && (int) $r['diproses'] === 1) {
            throw new \RuntimeException('Ujian yang sudah menaikkan jilid tidak dapat dihapus.');
        }
    }

    protected function ui(): array
    {
        $u1 = site_url('laporan/ujian-peserta');
        $u2 = site_url('laporan/ujian-lulus');
        $extra = <<<HTML
<div class="card card-outline card-primary"><div class="card-body form-inline">
  <label class="mr-2">Tanggal ujian</label>
  <input type="date" id="u_dari" class="form-control mr-2"> <span class="mr-2">s/d</span>
  <input type="date" id="u_sampai" class="form-control mr-3">
  <a id="u_peserta" target="_blank" class="btn btn-outline-danger mr-2" href="{$u1}"><i class="fas fa-file-pdf"></i> Daftar Peserta Ujian</a>
  <a id="u_lulus" target="_blank" class="btn btn-outline-success" href="{$u2}"><i class="fas fa-file-pdf"></i> Pengumuman Kelulusan</a>
  <small class="text-muted ml-3">Kosongkan tanggal untuk mencetak semua.</small>
</div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var d = document.getElementById('u_dari'), s = document.getElementById('u_sampai');
  var a = {u_peserta: "{$u1}", u_lulus: "{$u2}"};
  function upd() {
    var q = '?dari=' + encodeURIComponent(d.value) + '&sampai=' + encodeURIComponent(s.value);
    for (var k in a) document.getElementById(k).href = a[k] + q;
  }
  d.addEventListener('change', upd); s.addEventListener('change', upd);
});
</script>
HTML;
        return [
            'extra' => $extra,
            'columns' => [
                ['data' => 'tgl_ujian', 'title' => 'Tgl Ujian', 'render' => 'date'],
                ['data' => 'nis', 'title' => 'NIS'],
                ['data' => 'siswa_nama', 'title' => 'Nama Santri'],
                ['data' => 'jilid', 'title' => 'Jilid Diujikan'],
                ['data' => 'nilai', 'title' => 'Nilai'],
                ['data' => 'hasil', 'title' => 'Hasil', 'render' => 'badge'],
            ],
            'fields' => [
                ['name' => 'siswa_id', 'label' => 'Santri', 'type' => 'select', 'lookup' => 'siswa', 'required' => true, 'hint' => 'Jilid yang diujikan = jilid santri saat ini'],
                ['name' => 'tgl_ujian', 'label' => 'Tanggal Ujian', 'type' => 'date', 'required' => true, 'default' => 'today'],
                ['name' => 'hasil', 'label' => 'Hasil', 'type' => 'select', 'required' => true, 'default' => 'menunggu', 'options' => $this->opts(['menunggu' => 'Menunggu / belum diuji', 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak lulus']),
                    'hint' => 'Lulus: santri otomatis naik jilid & progress diperbarui'],
                ['name' => 'nilai', 'label' => 'Nilai', 'type' => 'select', 'options' => $this->opts(['A' => 'A (Sangat Baik)', 'B+' => 'B+', 'B' => 'B (Baik)', 'C' => 'C (Cukup)', 'D' => 'D (Kurang)'])],
                ['name' => 'catatan', 'label' => 'Catatan', 'type' => 'text', 'col' => 12],
            ],
        ];
    }
}

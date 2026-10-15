<?php

namespace App\Controllers;

/**
 * Penempatan Kelas, Jilid & Halaman siswa (riwayat). Terpisah dari Master Data Siswa.
 * Satu siswa punya satu penempatan aktif; nilainya disinkronkan ke siswa.kelas_id/jilid/halaman
 * (dipakai absensi, laporan, dan rekap).
 */
class Penempatan extends CrudController
{
    protected string $table = 'penempatan';
    protected string $path = 'penempatan';
    protected string $title = 'Penempatan Kelas & Jilid';
    protected array $fields = ['siswa_id', 'kelas_id', 'jilid', 'halaman', 'tgl_mulai', 'aktif', 'catatan'];

    protected function query()
    {
        return db_connect()->table('penempatan')
            ->select('penempatan.*, siswa.nama AS siswa_nama, siswa.nis, kelas.nama AS kelas_nama')
            ->join('siswa', 'siswa.id = penempatan.siswa_id', 'left')
            ->join('kelas', 'kelas.id = penempatan.kelas_id', 'left');
    }

    protected function order(): string
    {
        return 'penempatan.aktif DESC, penempatan.tgl_mulai DESC, penempatan.id DESC';
    }

    protected function rules(?int $id): array
    {
        return [
            'siswa_id' => 'required|is_natural_no_zero',
            'kelas_id' => 'required|is_natural_no_zero',
            'jilid' => 'required|in_list[' . implode(',', array_keys(jilid_options())) . ']',
            'halaman' => 'required|is_natural_no_zero|less_than_equal_to[60]',
            'tgl_mulai' => 'required|valid_date[Y-m-d]',
            'aktif' => 'permit_empty|in_list[0,1]',
            'catatan' => 'permit_empty|max_length[200]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $this->own('siswa', $d['siswa_id'] ?? null, 'Siswa');
        $this->own('kelas', $d['kelas_id'] ?? null, 'Kelas');
        $allowed = array_map('strval', array_column(db_connect()->table('kelas_jilid')->where('kelas_id', $d['kelas_id'])->get()->getResultArray(), 'jilid'));
        if ($allowed && !in_array((string) $d['jilid'], $allowed, true)) {
            throw new \RuntimeException('Jilid ' . $d['jilid'] . ' tidak ada pada kelas ini (jilid kelas: ' . implode(', ', $allowed) . ').');
        }
        if ($id) {
            $old = db_connect()->table('penempatan')->where(['id' => $id, 'tpq_id' => $this->tpqId()])->get()->getRowArray();
            if ($old && (int) $old['siswa_id'] !== (int) $d['siswa_id']) {
                throw new \RuntimeException('Siswa pada penempatan tidak dapat diganti; hapus lalu buat penempatan baru.');
            }
        }
        $d['aktif'] ??= 1;
        return $d;
    }

    protected function afterSave(int $id, array $d, bool $isNew): void
    {
        $db = db_connect();
        $row = $db->table('penempatan')->where('id', $id)->get()->getRowArray();
        if ($row && (int) $row['aktif'] === 1) {
            // Penempatan aktif sebelumnya ditutup
            $db->table('penempatan')
                ->where(['siswa_id' => $row['siswa_id'], 'tpq_id' => $this->tpqId(), 'aktif' => 1])->where('id !=', $id)
                ->update(['aktif' => 0, 'tgl_selesai' => $row['tgl_mulai']]);
        }
        $this->sync((int) $row['siswa_id']);
    }

    public function delete(int $id)
    {
        $row = db_connect()->table('penempatan')->where(['id' => $id, 'tpq_id' => $this->tpqId()])->get()->getRowArray();
        $res = parent::delete($id);
        if ($row && $res->getStatusCode() === 200) {
            $this->sync((int) $row['siswa_id']);
        }
        return $res;
    }

    /** Salin penempatan aktif ke data siswa; tanpa penempatan aktif → siswa tanpa kelas. */
    private function sync(int $sid): void
    {
        $db = db_connect();
        $a = $db->table('penempatan')->where(['siswa_id' => $sid, 'tpq_id' => $this->tpqId(), 'aktif' => 1])->orderBy('id', 'DESC')->get()->getRowArray();
        $upd = $a ? ['kelas_id' => $a['kelas_id'], 'jilid' => $a['jilid'], 'halaman' => $a['halaman']] : ['kelas_id' => null];
        $db->table('siswa')->where(['id' => $sid, 'tpq_id' => $this->tpqId()])->update($upd);
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'tgl_mulai', 'title' => 'Mulai', 'render' => 'date'],
                ['data' => 'nis', 'title' => 'NIS'],
                ['data' => 'siswa_nama', 'title' => 'Siswa'],
                ['data' => 'kelas_nama', 'title' => 'Kelas'],
                ['data' => 'jilid', 'title' => 'Jilid'],
                ['data' => 'halaman', 'title' => 'Hal'],
                ['data' => 'tgl_selesai', 'title' => 'Selesai', 'render' => 'date'],
                ['data' => 'aktif', 'title' => 'Status', 'render' => 'aktif'],
            ],
            'fields' => [
                ['name' => 'siswa_id', 'label' => 'Siswa', 'type' => 'select', 'lookup' => 'siswa', 'required' => true],
                ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'lookup' => 'kelas', 'required' => true],
                ['name' => 'jilid', 'label' => 'Jilid', 'type' => 'select', 'required' => true, 'default' => (string) array_key_first(jilid_options()), 'options' => $this->opts(jilid_options())],
                ['name' => 'halaman', 'label' => 'Halaman', 'type' => 'number', 'required' => true, 'default' => '1'],
                ['name' => 'tgl_mulai', 'label' => 'Tanggal Mulai', 'type' => 'date', 'required' => true, 'default' => 'today'],
                ['name' => 'aktif', 'label' => 'Status', 'type' => 'select', 'default' => '1', 'options' => $this->opts(['1' => 'Aktif (penempatan saat ini)', '0' => 'Riwayat'])],
                ['name' => 'catatan', 'label' => 'Catatan', 'type' => 'text', 'col' => 12],
            ],
        ];
    }
}

<?php

namespace App\Controllers;

/** Progress Jilid Qiroati: urutan jilid mengikuti tabel `jilid` */
class Progress extends CrudController
{
    protected string $table = 'progress';
    protected string $path = 'progress';
    protected string $title = 'Progress Jilid';
    protected array $fields = ['siswa_id', 'guru_id', 'tanggal', 'jilid', 'halaman', 'nilai', 'status', 'catatan'];

    protected function query()
    {
        return db_connect()->table('progress')->select('progress.*, siswa.nama AS siswa_nama, guru.nama AS guru_nama')
            ->join('siswa', 'siswa.id = progress.siswa_id', 'left')->join('guru', 'guru.id = progress.guru_id', 'left');
    }

    protected function order(): string
    {
        return 'progress.tanggal DESC, progress.id DESC';
    }

    protected function rules(?int $id): array
    {
        return [
            'siswa_id' => 'required|is_natural_no_zero',
            'tanggal' => 'required|valid_date[Y-m-d]',
            'jilid' => 'required|in_list[' . implode(',', array_keys(jilid_options())) . ']',
            'halaman' => 'required|is_natural_no_zero|less_than_equal_to[60]',
            'nilai' => 'permit_empty|in_list[A,B+,B,C,D]',
            'status' => 'required|in_list[lanjut,ulang,lulus_jilid]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $this->own('siswa', $d['siswa_id'] ?? null, 'Siswa');
        $this->own('guru', $d['guru_id'] ?? null, 'Guru');
        return $d;
    }

    /** Capaian terbaru memperbarui siswa & penempatan aktif (lulus jilid = naik jilid) — lihat Libraries\Kenaikan. */
    protected function afterSave(int $id, array $d, bool $isNew): void
    {
        if ($isNew) {
            \App\Libraries\Kenaikan::proses($this->tpqId(), $d);
        }
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'tanggal', 'title' => 'Tanggal', 'render' => 'date'],
                ['data' => 'siswa_nama', 'title' => 'Siswa'],
                ['data' => 'jilid', 'title' => 'Jilid'],
                ['data' => 'halaman', 'title' => 'Hal'],
                ['data' => 'nilai', 'title' => 'Nilai'],
                ['data' => 'status', 'title' => 'Status', 'render' => 'badge'],
                ['data' => 'guru_nama', 'title' => 'Guru'],
            ],
            'fields' => [
                ['name' => 'siswa_id', 'label' => 'Siswa', 'type' => 'select', 'lookup' => 'siswa', 'required' => true],
                ['name' => 'tanggal', 'label' => 'Tanggal', 'type' => 'date', 'required' => true, 'default' => 'today'],
                ['name' => 'jilid', 'label' => 'Jilid', 'type' => 'select', 'required' => true, 'options' => $this->opts(jilid_options())],
                ['name' => 'halaman', 'label' => 'Halaman', 'type' => 'number', 'required' => true, 'default' => '1'],
                ['name' => 'nilai', 'label' => 'Nilai', 'type' => 'select', 'options' => $this->opts(['A' => 'A (Sangat Baik)', 'B+' => 'B+', 'B' => 'B (Baik)', 'C' => 'C (Cukup)', 'D' => 'D (Kurang)'])],
                ['name' => 'status', 'label' => 'Hasil', 'type' => 'select', 'required' => true, 'default' => 'lanjut', 'options' => $this->opts(['lanjut' => 'Lanjut halaman berikutnya', 'ulang' => 'Ulang halaman', 'lulus_jilid' => 'Lulus jilid (naik jilid)'])],
                ['name' => 'guru_id', 'label' => 'Guru Penyimak', 'type' => 'select', 'lookup' => 'guru'],
                ['name' => 'catatan', 'label' => 'Catatan', 'type' => 'textarea', 'col' => 12],
            ],
        ];
    }
}

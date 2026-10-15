<?php

namespace App\Controllers;

/**
 * Kelas dapat berisi beberapa jilid atau semua jilid (kelas campuran).
 * Jilid yang diajarkan disimpan di tabel kelas_jilid; kosong = semua jilid.
 * Jilid tiap siswa tetap dicatat per siswa (menu Penempatan).
 */
class Kelas extends CrudController
{
    protected string $table = 'kelas';
    protected string $path = 'kelas';
    protected string $title = 'Data Kelas';
    protected array $fields = ['nama', 'guru_id', 'tahun_ajaran'];

    private array $jilidSel = [];

    protected function query()
    {
        return db_connect()->table('kelas')
            ->select("kelas.*, guru.nama AS guru_nama,
                (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = kelas.id AND s.status = 'aktif') AS jml_siswa,
                (SELECT GROUP_CONCAT(kj.jilid ORDER BY j.urutan SEPARATOR '|') FROM kelas_jilid kj JOIN jilid j ON j.kode = kj.jilid WHERE kj.kelas_id = kelas.id) AS jilid_list", false)
            ->join('guru', 'guru.id = kelas.guru_id', 'left');
    }

    protected function order(): string
    {
        return 'kelas.nama ASC';
    }

    protected function rules(?int $id): array
    {
        return [
            'nama' => 'required|max_length[60]',
            'tahun_ajaran' => 'permit_empty|max_length[15]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $this->own('guru', $d['guru_id'] ?? null, 'Guru');

        $sel = $this->request->getPost('jilid');
        $sel = is_array($sel) ? array_values(array_unique(array_map('strval', $sel))) : [];
        $valid = array_map('strval', array_keys(jilid_options()));
        foreach ($sel as $k) {
            if (!in_array($k, $valid, true)) {
                throw new \RuntimeException('Jilid yang dipilih tidak valid.');
            }
        }
        $this->jilidSel = $sel;
        return $d;
    }

    protected function afterSave(int $id, array $d, bool $isNew): void
    {
        $db = db_connect();
        $db->table('kelas_jilid')->where('kelas_id', $id)->delete();
        foreach ($this->jilidSel as $k) {
            $db->table('kelas_jilid')->insert(['kelas_id' => $id, 'jilid' => $k]);
        }
    }

    protected function ui(): array
    {
        $th = (int) date('Y');
        return [
            'columns' => [
                ['data' => 'nama', 'title' => 'Nama Kelas'],
                ['data' => 'jilid_list', 'title' => 'Jilid dalam Kelas', 'render' => 'jilidlist'],
                ['data' => 'guru_nama', 'title' => 'Guru / Wali Kelas'],
                ['data' => 'jml_siswa', 'title' => 'Siswa Aktif'],
                ['data' => 'tahun_ajaran', 'title' => 'Tahun Ajaran'],
            ],
            'fields' => [
                ['name' => 'nama', 'label' => 'Nama Kelas', 'type' => 'text', 'required' => true],
                ['name' => 'guru_id', 'label' => 'Guru / Wali Kelas', 'type' => 'select', 'lookup' => 'guru'],
                ['name' => 'tahun_ajaran', 'label' => 'Tahun Ajaran', 'type' => 'text', 'default' => $th . '/' . ($th + 1)],
                ['name' => 'jilid', 'label' => 'Jilid dalam Kelas', 'type' => 'multiselect', 'options' => $this->opts(jilid_options()), 'col' => 12,
                    'hint' => 'Pilih beberapa jilid (tahan Ctrl/Shift). Kosongkan = kelas berisi semua jilid.'],
            ],
        ];
    }
}

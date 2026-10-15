<?php

namespace App\Controllers;

class Siswa extends CrudController
{
    protected string $table = 'siswa';
    protected string $path = 'siswa';
    protected string $title = 'Data Siswa';
    protected array $fields = ['nis', 'nama', 'jk', 'tempat_lahir', 'tgl_lahir', 'alamat', 'nama_wali', 'telp_wali', 'tgl_masuk', 'status'];
    protected array $files = ['foto' => 'siswa'];

    protected function query()
    {
        return db_connect()->table('siswa')->select('siswa.*, kelas.nama AS kelas_nama')->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
    }

    protected function rules(?int $id): array
    {
        return [
            'nama' => 'required|max_length[100]',
            'jk' => 'required|in_list[L,P]',
            'tgl_lahir' => 'permit_empty|valid_date[Y-m-d]',
            'tgl_masuk' => 'permit_empty|valid_date[Y-m-d]',
            'status' => 'required|in_list[aktif,lulus,keluar]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $db = db_connect();
        $tid = $this->tpqId();

        if (empty($d['nis'])) {
            $n = $db->table('siswa')->where('tpq_id', $tid)->countAllResults() + 1;
            do {
                $nis = sprintf('%d%s%04d', $tid, date('y'), $n++);
            } while ($db->table('siswa')->where(['tpq_id' => $tid, 'nis' => $nis])->countAllResults());
            $d['nis'] = $nis;
        }
        $dup = $db->table('siswa')->where(['tpq_id' => $tid, 'nis' => $d['nis']]);
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults()) {
            throw new \RuntimeException('NIS sudah digunakan siswa lain.');
        }

        return $d;
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'foto', 'title' => 'Foto', 'render' => 'foto:siswa'],
                ['data' => 'nis', 'title' => 'NIS'],
                ['data' => 'nama', 'title' => 'Nama'],
                ['data' => 'jk', 'title' => 'JK', 'render' => 'jk'],
                ['data' => 'kelas_nama', 'title' => 'Kelas*'],
                ['data' => 'jilid', 'title' => 'Jilid*'],
                ['data' => 'halaman', 'title' => 'Hal*'],
                ['data' => 'nama_wali', 'title' => 'Wali'],
                ['data' => 'status', 'title' => 'Status', 'render' => 'badge'],
            ],
            'fields' => [
                ['name' => 'nis', 'label' => 'NIS', 'type' => 'text', 'hint' => 'Kosongkan untuk dibuat otomatis'],
                ['name' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                ['name' => 'jk', 'label' => 'Jenis Kelamin', 'type' => 'select', 'required' => true, 'options' => $this->opts(['L' => 'Laki-laki', 'P' => 'Perempuan'])],
                ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'type' => 'text'],
                ['name' => 'tgl_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date'],
                ['name' => 'tgl_masuk', 'label' => 'Tanggal Masuk', 'type' => 'date', 'default' => 'today'],
                ['name' => 'nama_wali', 'label' => 'Nama Wali', 'type' => 'text'],
                ['name' => 'telp_wali', 'label' => 'Telp/WA Wali', 'type' => 'text'],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => 'aktif', 'options' => $this->opts(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'keluar' => 'Keluar'])],
                ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea', 'col' => 12],
                ['name' => 'foto', 'label' => 'Foto Siswa', 'type' => 'file', 'kind' => 'siswa', 'col' => 12],
            ],
        ];
    }
}

<?php

namespace App\Controllers;

class Guru extends CrudController
{
    protected string $table = 'guru';
    protected string $path = 'guru';
    protected string $title = 'Data Guru / Ustadz';
    protected array $fields = ['nama', 'jk', 'telp', 'alamat', 'sertifikat', 'tgl_masuk', 'status'];
    protected array $files = ['foto' => 'guru'];

    protected function rules(?int $id): array
    {
        return [
            'nama' => 'required|max_length[100]',
            'jk' => 'required|in_list[L,P]',
            'sertifikat' => 'required|in_list[Sudah,Belum]',
            'tgl_masuk' => 'permit_empty|valid_date[Y-m-d]',
            'status' => 'required|in_list[aktif,nonaktif]',
        ];
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'foto', 'title' => 'Foto', 'render' => 'foto:guru'],
                ['data' => 'nama', 'title' => 'Nama'],
                ['data' => 'jk', 'title' => 'JK', 'render' => 'jk'],
                ['data' => 'telp', 'title' => 'Telp/WA'],
                ['data' => 'sertifikat', 'title' => 'Syahadah Qiroati'],
                ['data' => 'tgl_masuk', 'title' => 'Mulai Mengajar', 'render' => 'date'],
                ['data' => 'status', 'title' => 'Status', 'render' => 'badge'],
            ],
            'fields' => [
                ['name' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                ['name' => 'jk', 'label' => 'Jenis Kelamin', 'type' => 'select', 'required' => true, 'options' => $this->opts(['L' => 'Laki-laki', 'P' => 'Perempuan'])],
                ['name' => 'telp', 'label' => 'Telp/WA', 'type' => 'text'],
                ['name' => 'sertifikat', 'label' => 'Syahadah/Sertifikat Qiroati', 'type' => 'select', 'required' => true, 'default' => 'Belum', 'options' => $this->opts(['Sudah' => 'Sudah', 'Belum' => 'Belum'])],
                ['name' => 'tgl_masuk', 'label' => 'Mulai Mengajar', 'type' => 'date', 'default' => 'today'],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => 'aktif', 'options' => $this->opts(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])],
                ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea', 'col' => 12],
                ['name' => 'foto', 'label' => 'Foto Guru', 'type' => 'file', 'kind' => 'guru', 'col' => 12],
            ],
        ];
    }
}

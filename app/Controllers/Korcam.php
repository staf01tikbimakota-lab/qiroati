<?php

namespace App\Controllers;

class Korcam extends CrudController
{
    protected string $table = 'korcam';
    protected string $path = 'master/korcam';
    protected string $title = 'Data Korcam (Koordinator Kecamatan)';
    protected bool $tpqScoped = false;
    protected array $fields = ['korcab_id', 'nama', 'kecamatan', 'telp'];

    protected function query()
    {
        return db_connect()->table('korcam')->select('korcam.*, korcab.nama AS korcab_nama')->join('korcab', 'korcab.id = korcam.korcab_id', 'left');
    }

    protected function order(): string
    {
        return 'korcab.nama ASC, korcam.nama ASC';
    }

    protected function rules(?int $id): array
    {
        return ['korcab_id' => 'required|is_natural_no_zero|is_not_unique[korcab.id]', 'nama' => 'required|max_length[100]', 'kecamatan' => 'permit_empty|max_length[100]', 'telp' => 'permit_empty|max_length[30]'];
    }

    protected function ui(): array
    {
        return [
            'columns' => [['data' => 'korcab_nama', 'title' => 'Korcab'], ['data' => 'nama', 'title' => 'Nama Korcam'], ['data' => 'kecamatan', 'title' => 'Kecamatan'], ['data' => 'telp', 'title' => 'Telp']],
            'fields' => [
                ['name' => 'korcab_id', 'label' => 'Korcab', 'type' => 'select', 'lookup' => 'korcab', 'required' => true],
                ['name' => 'nama', 'label' => 'Nama Korcam', 'type' => 'text', 'required' => true],
                ['name' => 'kecamatan', 'label' => 'Kecamatan', 'type' => 'text'],
                ['name' => 'telp', 'label' => 'Telp', 'type' => 'text'],
            ],
        ];
    }
}

<?php

namespace App\Controllers;

class Korcab extends CrudController
{
    protected string $table = 'korcab';
    protected string $path = 'master/korcab';
    protected string $title = 'Data Korcab (Koordinator Cabang)';
    protected bool $tpqScoped = false;
    protected array $fields = ['nama', 'wilayah', 'telp', 'alamat'];

    protected function rules(?int $id): array
    {
        return ['nama' => 'required|max_length[100]', 'wilayah' => 'permit_empty|max_length[100]', 'telp' => 'permit_empty|max_length[30]'];
    }

    protected function order(): string
    {
        return 'korcab.nama ASC';
    }

    protected function ui(): array
    {
        return [
            'columns' => [['data' => 'nama', 'title' => 'Nama Korcab'], ['data' => 'wilayah', 'title' => 'Wilayah'], ['data' => 'telp', 'title' => 'Telp']],
            'fields' => [
                ['name' => 'nama', 'label' => 'Nama Korcab', 'type' => 'text', 'required' => true],
                ['name' => 'wilayah', 'label' => 'Wilayah (Kab/Kota)', 'type' => 'text'],
                ['name' => 'telp', 'label' => 'Telp', 'type' => 'text'],
                ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea', 'col' => 12],
            ],
        ];
    }
}

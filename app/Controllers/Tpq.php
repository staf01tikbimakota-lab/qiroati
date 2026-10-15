<?php

namespace App\Controllers;

class Tpq extends CrudController
{
    protected string $table = 'tpq';
    protected string $path = 'master/tpq';
    protected string $title = 'Data TPQ';
    protected bool $tpqScoped = false;
    protected array $fields = ['korcam_id', 'nama', 'alamat', 'desa', 'kepala', 'telp', 'tarif_spp'];

    protected function query()
    {
        return db_connect()->table('tpq')->select('tpq.*, korcam.nama AS korcam_nama')->join('korcam', 'korcam.id = tpq.korcam_id', 'left');
    }

    protected function order(): string
    {
        return 'korcam.nama ASC, tpq.nama ASC';
    }

    protected function rules(?int $id): array
    {
        return ['korcam_id' => 'required|is_natural_no_zero|is_not_unique[korcam.id]', 'nama' => 'required|max_length[120]', 'tarif_spp' => 'permit_empty|is_natural'];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $d['tarif_spp'] ??= 0;
        return $d;
    }

    protected function ui(): array
    {
        return [
            'columns' => [['data' => 'korcam_nama', 'title' => 'Korcam'], ['data' => 'nama', 'title' => 'Nama TPQ'], ['data' => 'desa', 'title' => 'Desa'], ['data' => 'kepala', 'title' => 'Kepala TPQ'], ['data' => 'tarif_spp', 'title' => 'Tarif SPP', 'render' => 'rupiah']],
            'fields' => [
                ['name' => 'korcam_id', 'label' => 'Korcam', 'type' => 'select', 'lookup' => 'korcam', 'required' => true],
                ['name' => 'nama', 'label' => 'Nama TPQ', 'type' => 'text', 'required' => true],
                ['name' => 'desa', 'label' => 'Desa/Kelurahan', 'type' => 'text'],
                ['name' => 'kepala', 'label' => 'Kepala TPQ', 'type' => 'text'],
                ['name' => 'telp', 'label' => 'Telp', 'type' => 'text'],
                ['name' => 'tarif_spp', 'label' => 'Tarif SPP / bulan (Rp)', 'type' => 'number', 'default' => '0'],
                ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea', 'col' => 12],
            ],
        ];
    }
}

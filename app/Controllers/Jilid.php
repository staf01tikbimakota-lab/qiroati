<?php

namespace App\Controllers;

/** Master Jilid Qiroati (1A, 1B, ... Juz 27, 6, Ghorib, Tajwid, Finishing, dst). Urutan menentukan alur naik jilid. */
class Jilid extends CrudController
{
    protected string $table = 'jilid';
    protected string $path = 'master/jilid';
    protected string $title = 'Master Jilid Qiroati';
    protected bool $tpqScoped = false;
    protected array $fields = ['kode', 'nama', 'urutan', 'keterangan'];

    protected function order(): string
    {
        return 'jilid.urutan ASC, jilid.id ASC';
    }

    protected function rules(?int $id): array
    {
        return [
            'kode' => 'required|max_length[20]|alpha_numeric_space',
            'nama' => 'required|max_length[50]',
            'urutan' => 'required|is_natural_no_zero|less_than[1000]',
            'keterangan' => 'permit_empty|max_length[150]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $dup = db_connect()->table('jilid')->where('kode', $d['kode']);
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults()) {
            throw new \RuntimeException('Kode jilid sudah ada.');
        }
        return $d;
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'urutan', 'title' => 'Urutan'],
                ['data' => 'kode', 'title' => 'Kode'],
                ['data' => 'nama', 'title' => 'Nama Jilid'],
                ['data' => 'keterangan', 'title' => 'Keterangan'],
            ],
            'fields' => [
                ['name' => 'kode', 'label' => 'Kode', 'type' => 'text', 'required' => true, 'hint' => 'Contoh: 1A, Juz 27, Ghorib. Mengubah kode ikut mengubah data siswa.'],
                ['name' => 'nama', 'label' => 'Nama Jilid', 'type' => 'text', 'required' => true],
                ['name' => 'urutan', 'label' => 'Urutan', 'type' => 'number', 'required' => true, 'hint' => 'Jilid dengan urutan terbesar = tahap akhir (lulus TPQ)'],
                ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'text'],
            ],
        ];
    }
}

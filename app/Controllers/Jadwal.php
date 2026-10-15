<?php

namespace App\Controllers;

class Jadwal extends CrudController
{
    private const HARI = ['Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu', 'Ahad' => 'Ahad'];

    protected string $table = 'jadwal';
    protected string $path = 'jadwal';
    protected string $title = 'Jadwal Pelajaran';
    protected array $fields = ['kelas_id', 'hari', 'jam_mulai', 'jam_selesai', 'guru_id', 'keterangan'];

    protected function query()
    {
        return db_connect()->table('jadwal')->select('jadwal.*, kelas.nama AS kelas_nama, guru.nama AS guru_nama')
            ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')->join('guru', 'guru.id = jadwal.guru_id', 'left');
    }

    protected function order(): string
    {
        return "FIELD(jadwal.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'), jadwal.jam_mulai";
    }

    protected function rules(?int $id): array
    {
        return [
            'kelas_id' => 'required|is_natural_no_zero',
            'hari' => 'required|in_list[' . implode(',', array_keys(self::HARI)) . ']',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan' => 'permit_empty|max_length[150]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $this->own('kelas', $d['kelas_id'] ?? null, 'Kelas');
        $this->own('guru', $d['guru_id'] ?? null, 'Guru');
        if (strcmp(substr($d['jam_selesai'], 0, 5), substr($d['jam_mulai'], 0, 5)) <= 0) {
            throw new \RuntimeException('Jam selesai harus lebih besar dari jam mulai.');
        }
        return $d;
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'hari', 'title' => 'Hari'],
                ['data' => 'jam_mulai', 'title' => 'Jam', 'render' => 'jam'],
                ['data' => 'kelas_nama', 'title' => 'Kelas'],
                ['data' => 'guru_nama', 'title' => 'Guru'],
                ['data' => 'keterangan', 'title' => 'Keterangan'],
            ],
            'fields' => [
                ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'lookup' => 'kelas', 'required' => true],
                ['name' => 'hari', 'label' => 'Hari', 'type' => 'select', 'required' => true, 'options' => $this->opts(self::HARI)],
                ['name' => 'jam_mulai', 'label' => 'Jam Mulai', 'type' => 'time', 'required' => true, 'default' => '15:30'],
                ['name' => 'jam_selesai', 'label' => 'Jam Selesai', 'type' => 'time', 'required' => true, 'default' => '17:00'],
                ['name' => 'guru_id', 'label' => 'Guru Pengajar', 'type' => 'select', 'lookup' => 'guru'],
                ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'text'],
            ],
        ];
    }
}

<?php

namespace App\Controllers;

class Users extends CrudController
{
    protected string $table = 'users';
    protected string $path = 'master/users';
    protected string $title = 'Manajemen User';
    protected bool $tpqScoped = false;
    protected array $fields = ['nama', 'username', 'password', 'role', 'korcab_id', 'korcam_id', 'tpq_id', 'aktif'];

    protected function query()
    {
        return db_connect()->table('users')->select('users.id, users.nama, users.username, users.role, users.korcab_id, users.korcam_id, users.tpq_id, users.aktif, tpq.nama AS tpq_nama, korcam.nama AS korcam_nama, korcab.nama AS korcab_nama')
            ->join('tpq', 'tpq.id = users.tpq_id', 'left')
            ->join('korcam', 'korcam.id = users.korcam_id', 'left')
            ->join('korcab', 'korcab.id = users.korcab_id', 'left');
    }

    protected function order(): string
    {
        return 'users.role ASC, users.nama ASC';
    }

    protected function rules(?int $id): array
    {
        return [
            'nama' => 'required|max_length[100]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct',
            'password' => ($id ? 'permit_empty' : 'required') . '|min_length[6]',
            'role' => 'required|in_list[superadmin,korcab,korcam,tpq]',
            'aktif' => 'required|in_list[0,1]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $db = db_connect();
        $dup = $db->table('users')->where('username', $d['username']);
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults()) {
            throw new \RuntimeException('Username sudah dipakai.');
        }

        // Role menentukan unit; kolom unit lain dikosongkan.
        $need = ['korcab' => 'korcab_id', 'korcam' => 'korcam_id', 'tpq' => 'tpq_id'];
        foreach ($need as $r => $col) {
            if ($d['role'] === $r) {
                if (empty($d[$col])) {
                    throw new \RuntimeException('Unit ' . strtoupper($r) . ' wajib dipilih untuk role ini.');
                }
            } else {
                $d[$col] = null;
            }
        }

        if (!empty($d['password'])) {
            $d['password'] = password_hash($d['password'], PASSWORD_DEFAULT);
        } else {
            unset($d['password']);
        }
        return $d;
    }

    protected function beforeDelete(int $id): void
    {
        if ($id === (int) session('user')['id']) {
            throw new \RuntimeException('Tidak dapat menghapus akun yang sedang dipakai.');
        }
    }

    protected function ui(): array
    {
        return [
            'columns' => [
                ['data' => 'nama', 'title' => 'Nama'],
                ['data' => 'username', 'title' => 'Username'],
                ['data' => 'role', 'title' => 'Role', 'render' => 'badge'],
                ['data' => 'tpq_nama', 'title' => 'TPQ'],
                ['data' => 'korcam_nama', 'title' => 'Korcam'],
                ['data' => 'korcab_nama', 'title' => 'Korcab'],
                ['data' => 'aktif', 'title' => 'Status', 'render' => 'aktif'],
            ],
            'fields' => [
                ['name' => 'nama', 'label' => 'Nama', 'type' => 'text', 'required' => true],
                ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true],
                ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'hint' => 'Kosongkan jika tidak diubah (min. 6 karakter)'],
                ['name' => 'role', 'label' => 'Level User', 'type' => 'select', 'required' => true, 'options' => $this->opts(['tpq' => 'Admin TPQ', 'korcam' => 'Korcam', 'korcab' => 'Korcab', 'superadmin' => 'Super Admin'])],
                ['name' => 'tpq_id', 'label' => 'TPQ (untuk level TPQ)', 'type' => 'select', 'lookup' => 'tpq'],
                ['name' => 'korcam_id', 'label' => 'Korcam (untuk level Korcam)', 'type' => 'select', 'lookup' => 'korcam'],
                ['name' => 'korcab_id', 'label' => 'Korcab (untuk level Korcab)', 'type' => 'select', 'lookup' => 'korcab'],
                ['name' => 'aktif', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => '1', 'options' => $this->opts(['1' => 'Aktif', '0' => 'Nonaktif'])],
            ],
        ];
    }
}

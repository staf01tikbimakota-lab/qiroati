<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** php spark db:seed InitSeeder  — membuat 1 jalur Korcab → Korcam → TPQ + 4 akun contoh. */
class InitSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $db->table('korcab')->insert(['nama' => 'Korcab Contoh', 'wilayah' => 'Kabupaten Contoh']);
        $korcab = $db->insertID();
        $db->table('korcam')->insert(['korcab_id' => $korcab, 'nama' => 'Korcam Contoh', 'kecamatan' => 'Kecamatan Contoh']);
        $korcam = $db->insertID();
        $db->table('tpq')->insert(['korcam_id' => $korcam, 'nama' => 'TPQ Al-Ikhlas', 'kepala' => 'Ust. Ahmad', 'alamat' => 'Jl. Masjid No. 1', 'tarif_spp' => 25000]);
        $tpq = $db->insertID();

        $users = [
            ['Super Admin', 'admin', 'admin123', 'superadmin', null, null, null],
            ['Admin Korcab', 'korcab', 'korcab123', 'korcab', $korcab, null, null],
            ['Admin Korcam', 'korcam', 'korcam123', 'korcam', null, $korcam, null],
            ['Admin TPQ Al-Ikhlas', 'tpq', 'tpq123', 'tpq', null, null, $tpq],
        ];
        foreach ($users as [$nama, $user, $pass, $role, $cb, $cm, $tp]) {
            $db->table('users')->insert([
                'nama' => $nama, 'username' => $user, 'password' => password_hash($pass, PASSWORD_DEFAULT),
                'role' => $role, 'korcab_id' => $cb, 'korcam_id' => $cm, 'tpq_id' => $tp, 'aktif' => 1,
            ]);
        }
    }
}

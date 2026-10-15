<?php

namespace App\Controllers;

/** Sumber data dropdown untuk form CRUD: [{id, text}] */
class Lookup extends BaseController
{
    public function get(string $name)
    {
        $u = session('user');
        $db = db_connect();
        $tpq = (int) ($u['tpq_id'] ?? 0);
        $rows = [];

        switch ($name) {
            case 'kelas':
            case 'guru':
            case 'siswa':
                if ($u['role'] !== 'tpq') {
                    return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
                }
                if ($name === 'kelas') {
                    $rows = $db->query('SELECT id, nama AS text FROM kelas WHERE tpq_id=? ORDER BY nama', [$tpq])->getResultArray();
                } elseif ($name === 'guru') {
                    $rows = $db->query("SELECT id, nama AS text FROM guru WHERE tpq_id=? AND status='aktif' ORDER BY nama", [$tpq])->getResultArray();
                } else {
                    $rows = $db->query("SELECT id, CONCAT(nama, ' (', jilid, ' hal ', halaman, ')') AS text FROM siswa WHERE tpq_id=? AND status='aktif' ORDER BY nama", [$tpq])->getResultArray();
                }
                break;
            case 'korcab':
            case 'korcam':
            case 'tpq':
                if ($u['role'] !== 'superadmin') {
                    return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
                }
                $rows = $db->query("SELECT id, nama AS text FROM {$name} ORDER BY nama")->getResultArray();
                break;
            default:
                return $this->response->setStatusCode(404)->setJSON(['message' => 'Tidak ditemukan.']);
        }
        return $this->response->setJSON($rows);
    }
}

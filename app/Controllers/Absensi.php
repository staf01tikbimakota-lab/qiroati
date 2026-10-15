<?php

namespace App\Controllers;

class Absensi extends BaseController
{
    public function index()
    {
        return view('absensi/index', ['title' => 'Absensi Siswa']);
    }

    /** Daftar siswa aktif satu kelas beserta status absen pada tanggal tertentu. */
    public function data()
    {
        $tid = (int) session('user')['tpq_id'];
        $kelas = (int) $this->request->getGet('kelas_id');
        $tgl = (string) $this->request->getGet('tanggal');
        if (!\DateTime::createFromFormat('Y-m-d', $tgl)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tanggal tidak valid.']);
        }
        $rows = db_connect()->query(
            "SELECT s.id, s.nis, s.nama, a.status, a.keterangan
             FROM siswa s LEFT JOIN absensi a ON a.siswa_id = s.id AND a.tanggal = ?
             WHERE s.tpq_id = ? AND s.kelas_id = ? AND s.status = 'aktif' ORDER BY s.nama",
            [$tgl, $tid, $kelas]
        )->getResultArray();
        return $this->response->setJSON(['data' => $rows]);
    }

    public function simpan()
    {
        $tid = (int) session('user')['tpq_id'];
        $in = $this->request->getJSON(true) ?? [];
        $tgl = (string) ($in['tanggal'] ?? '');
        $items = $in['items'] ?? [];
        if (!\DateTime::createFromFormat('Y-m-d', $tgl) || !is_array($items) || !$items) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Data absensi tidak valid.']);
        }

        $db = db_connect();
        $ids = array_map('intval', array_column($items, 'siswa_id'));
        $valid = array_column($db->table('siswa')->select('id')->where('tpq_id', $tid)->whereIn('id', $ids)->get()->getResultArray(), 'id');
        $valid = array_flip(array_map('intval', $valid));

        $db->transStart();
        $n = 0;
        foreach ($items as $it) {
            $sid = (int) ($it['siswa_id'] ?? 0);
            $st = (string) ($it['status'] ?? '');
            if (!isset($valid[$sid]) || !in_array($st, ['H', 'I', 'S', 'A'], true)) {
                continue;
            }
            $db->query(
                'INSERT INTO absensi (tpq_id, siswa_id, tanggal, status, keterangan) VALUES (?,?,?,?,?)
                 ON DUPLICATE KEY UPDATE status = VALUES(status), keterangan = VALUES(keterangan)',
                [$tid, $sid, $tgl, $st, mb_substr((string) ($it['keterangan'] ?? ''), 0, 150) ?: null]
            );
            $n++;
        }
        $db->transComplete();

        return $db->transStatus()
            ? $this->response->setJSON(['message' => "Absensi {$n} siswa tersimpan."])
            : $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan absensi.']);
    }
}

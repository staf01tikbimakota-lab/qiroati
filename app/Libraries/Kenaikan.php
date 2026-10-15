<?php

namespace App\Libraries;

/**
 * Logika kenaikan jilid, dipakai Progress dan Ujian.
 * - Non-lulus: perbarui jilid/halaman di siswa & penempatan aktif.
 * - Lulus jilid: tutup penempatan aktif, buka penempatan baru di jilid berikutnya (kelas sama),
 *   perbarui siswa. Lulus di jilid terakhir = siswa berstatus 'lulus'.
 * Mengembalikan kode jilid berikutnya bila lulus jilid, selain itu null.
 */
class Kenaikan
{
    public static function proses(int $tid, array $d): ?string
    {
        helper('app');
        $db = db_connect();
        $act = $db->table('penempatan')->where(['siswa_id' => $d['siswa_id'], 'tpq_id' => $tid, 'aktif' => 1])->orderBy('id', 'DESC')->get()->getRowArray();
        $next = null;

        if ($d['status'] === 'lulus_jilid') {
            $next = jilid_next((string) $d['jilid']);
            if ($act) {
                $db->table('penempatan')->where('id', $act['id'])->update(['aktif' => 0, 'tgl_selesai' => $d['tanggal']]);
                if ($next) {
                    $ok = self::diajarkan((int) $act['kelas_id'], $next);
                    $db->table('penempatan')->insert([
                        'tpq_id' => $tid, 'siswa_id' => $d['siswa_id'], 'kelas_id' => $act['kelas_id'], 'jilid' => $next, 'halaman' => 1,
                        'tgl_mulai' => $d['tanggal'], 'aktif' => 1,
                        'catatan' => $ok ? 'Naik jilid' : 'Naik jilid — jilid ini belum ada di kelas, mohon pindahkan kelas',
                    ]);
                }
            }
            $upd = $next ? ['jilid' => $next, 'halaman' => 1] : ['status' => 'lulus'];
        } else {
            if ($act) {
                $db->table('penempatan')->where('id', $act['id'])->update(['jilid' => $d['jilid'], 'halaman' => $d['halaman']]);
            }
            $upd = ['jilid' => $d['jilid'], 'halaman' => $d['halaman']];
        }
        $db->table('siswa')->where(['id' => $d['siswa_id'], 'tpq_id' => $tid])->update($upd);

        return $d['status'] === 'lulus_jilid' ? $next : null;
    }

    /** Kelas tanpa batasan jilid = semua jilid diajarkan. */
    private static function diajarkan(int $kelasId, string $jilid): bool
    {
        $rows = db_connect()->table('kelas_jilid')->where('kelas_id', $kelasId)->get()->getResultArray();
        return !$rows || in_array($jilid, array_map('strval', array_column($rows, 'jilid')), true);
    }
}

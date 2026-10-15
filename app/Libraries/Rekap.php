<?php

namespace App\Libraries;

/**
 * Rekap perkembangan. TPQ dihitung langsung dari data operasional;
 * saat dikirim ke Korcam disimpan sebagai snapshot (kirim_laporan.data_json),
 * dan Korcam/Korcab hanya membaca snapshot tersebut.
 */
class Rekap
{
    public const KEYS = ['siswa_aktif', 'laki', 'perempuan', 'guru', 'kelas', 'absensi_total', 'absensi_hadir', 'naik_jilid', 'lulus', 'spp_masuk', 'infaq_masuk', 'menunggak'];

    public static function periode(int $b, int $t): array
    {
        $from = sprintf('%04d-%02d-01', $t, $b);
        return [$from, date('Y-m-t', strtotime($from))];
    }

    public static function tpq(int $id, int $b, int $t): array
    {
        helper('app');
        $db = db_connect();
        [$from, $to] = self::periode($b, $t);

        $s = $db->query("SELECT COUNT(*) n, COALESCE(SUM(jk='L'),0) l, COALESCE(SUM(jk='P'),0) p FROM siswa WHERE tpq_id=? AND status='aktif'", [$id])->getRowArray();
        $guru = (int) $db->query("SELECT COUNT(*) n FROM guru WHERE tpq_id=? AND status='aktif'", [$id])->getRow()->n;
        $kelas = (int) $db->query('SELECT COUNT(*) n FROM kelas WHERE tpq_id=?', [$id])->getRow()->n;
        $a = $db->query("SELECT COUNT(*) t, COALESCE(SUM(status='H'),0) h FROM absensi WHERE tpq_id=? AND tanggal BETWEEN ? AND ?", [$id, $from, $to])->getRowArray();

        $per = [];
        foreach ($db->query("SELECT jilid, COUNT(*) n FROM siswa WHERE tpq_id=? AND status='aktif' GROUP BY jilid", [$id])->getResultArray() as $r) {
            $per[$r['jilid']] = (int) $r['n'];
        }

        $naik = (int) $db->query("SELECT COUNT(*) n FROM progress WHERE tpq_id=? AND status='lulus_jilid' AND tanggal BETWEEN ? AND ?", [$id, $from, $to])->getRow()->n;
        $lulus = (int) $db->query("SELECT COUNT(*) n FROM progress WHERE tpq_id=? AND status='lulus_jilid' AND jilid=? AND tanggal BETWEEN ? AND ?", [$id, (string) jilid_last_kode(), $from, $to])->getRow()->n;

        $k = $db->query("SELECT COALESCE(SUM(CASE WHEN jenis='SPP' THEN jumlah END),0) spp, COALESCE(SUM(CASE WHEN jenis='Infaq' THEN jumlah END),0) infaq FROM pembayaran WHERE tpq_id=? AND tgl_bayar BETWEEN ? AND ?", [$id, $from, $to])->getRowArray();
        $tunggak = (int) $db->query("SELECT COUNT(*) n FROM siswa s WHERE s.tpq_id=? AND s.status='aktif' AND NOT EXISTS (SELECT 1 FROM pembayaran p WHERE p.siswa_id=s.id AND p.jenis='SPP' AND p.bulan=? AND p.tahun=?)", [$id, $b, $t])->getRow()->n;

        $out = [
            'siswa_aktif' => (int) $s['n'], 'laki' => (int) $s['l'], 'perempuan' => (int) $s['p'],
            'guru' => $guru, 'kelas' => $kelas,
            'absensi_total' => (int) $a['t'], 'absensi_hadir' => (int) $a['h'],
            'naik_jilid' => $naik, 'lulus' => $lulus,
            'spp_masuk' => (int) $k['spp'], 'infaq_masuk' => (int) $k['infaq'], 'menunggak' => $tunggak,
            'per_jilid' => $per,
        ];
        $out['hadir_persen'] = $out['absensi_total'] ? round($out['absensi_hadir'] * 100 / $out['absensi_total'], 1) : 0;
        return $out;
    }

    public static function sum(array $list): array
    {
        $o = array_fill_keys(self::KEYS, 0);
        $o['per_jilid'] = [];
        foreach ($list as $r) {
            if (!$r) continue;
            foreach (self::KEYS as $k) $o[$k] += (int) ($r[$k] ?? 0);
            foreach (($r['per_jilid'] ?? []) as $j => $n) $o['per_jilid'][$j] = ($o['per_jilid'][$j] ?? 0) + (int) $n;
        }
        $o['hadir_persen'] = $o['absensi_total'] ? round($o['absensi_hadir'] * 100 / $o['absensi_total'], 1) : 0;
        return $o;
    }

    public static function kirimRow(string $level, int $ref, int $b, int $t): ?array
    {
        return db_connect()->table('kirim_laporan')->where(['level' => $level, 'ref_id' => $ref, 'bulan' => $b, 'tahun' => $t])->get()->getRowArray();
    }

    public static function saveKirim(string $level, int $ref, int $b, int $t, array $data): void
    {
        db_connect()->query(
            "INSERT INTO kirim_laporan (level,ref_id,bulan,tahun,status,tgl_kirim,data_json) VALUES (?,?,?,?, 'terkirim', NOW(), ?)
             ON DUPLICATE KEY UPDATE status='terkirim', tgl_kirim=NOW(), data_json=VALUES(data_json)",
            [$level, $ref, $b, $t, json_encode($data)]
        );
    }

    /** Daftar TPQ dalam satu korcam + snapshot yang sudah dikirim. */
    public static function korcamTpqRows(int $korcamId, int $b, int $t): array
    {
        return self::withSnapshots('tpq', 'korcam_id', $korcamId, 'tpq', $b, $t);
    }

    /** Daftar Korcam dalam satu korcab + snapshot yang sudah dikirim. */
    public static function korcabKorcamRows(int $korcabId, int $b, int $t): array
    {
        return self::withSnapshots('korcam', 'korcab_id', $korcabId, 'korcam', $b, $t);
    }

    private static function withSnapshots(string $table, string $parentCol, int $parentId, string $level, int $b, int $t): array
    {
        $db = db_connect();
        $rows = $db->table($table)->where($parentCol, $parentId)->orderBy('nama')->get()->getResultArray();
        $sent = [];
        if ($rows) {
            $q = $db->table('kirim_laporan')->where(['level' => $level, 'bulan' => $b, 'tahun' => $t])->whereIn('ref_id', array_column($rows, 'id'));
            foreach ($q->get()->getResultArray() as $r) $sent[$r['ref_id']] = $r;
        }
        foreach ($rows as &$p) {
            $r = $sent[$p['id']] ?? null;
            $p['terkirim'] = (bool) $r;
            $p['tgl_kirim'] = $r['tgl_kirim'] ?? null;
            $p['data'] = $r ? json_decode($r['data_json'], true) : null;
        }
        unset($p);
        return $rows;
    }
}

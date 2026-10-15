<?php

namespace App\Controllers;

use App\Libraries\PdfMaker;
use App\Libraries\Rekap;

/** Laporan bulanan TPQ: rekap, pengiriman ke Korcam, dan cetak PDF. */
class Laporan extends BaseController
{
    private function tpqRow(): array
    {
        return db_connect()->query(
            'SELECT t.*, kc.nama AS korcam_nama FROM tpq t LEFT JOIN korcam kc ON kc.id = t.korcam_id WHERE t.id = ?',
            [(int) session('user')['tpq_id']]
        )->getRowArray() ?? [];
    }

    private function periode(): array
    {
        $b = (int) ($this->request->getGet('bulan') ?: date('n'));
        $t = (int) ($this->request->getGet('tahun') ?: date('Y'));
        return [min(12, max(1, $b)), min(2100, max(2000, $t))];
    }

    public function index()
    {
        [$b, $t] = $this->periode();
        $tid = (int) session('user')['tpq_id'];
        return view('laporan/index', [
            'title' => 'Laporan Bulanan',
            'b' => $b, 't' => $t,
            'rekap' => Rekap::tpq($tid, $b, $t),
            'kirim' => Rekap::kirimRow('tpq', $tid, $b, $t),
            'kelas' => db_connect()->table('kelas')->where('tpq_id', $tid)->orderBy('nama')->get()->getResultArray(),
        ]);
    }

    /** Kirim (atau kirim ulang) rekap bulan tersebut ke Korcam sebagai snapshot. */
    public function kirim()
    {
        $b = (int) $this->request->getPost('bulan');
        $t = (int) $this->request->getPost('tahun');
        if ($b < 1 || $b > 12 || $t < 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Periode tidak valid.']);
        }
        $tid = (int) session('user')['tpq_id'];
        $korcam = (int) db_connect()->table('tpq')->select('korcam_id')->where('id', $tid)->get()->getRow()->korcam_id;
        if (Rekap::kirimRow('korcam', $korcam, $b, $t)) {
            return $this->response->setStatusCode(409)->setJSON(['message' => 'Korcam sudah meneruskan rekap bulan ini ke Korcab. Hubungi Korcam untuk perubahan.']);
        }
        Rekap::saveKirim('tpq', $tid, $b, $t, Rekap::tpq($tid, $b, $t));
        return $this->response->setJSON(['message' => 'Laporan ' . bulan_nama($b) . " {$t} berhasil dikirim ke Korcam."]);
    }

    /** PDF: daftar siswa per kelas dengan rekap kehadiran bulan terpilih. */
    public function kelasSiswa()
    {
        [$b, $t] = $this->periode();
        [$from, $to] = Rekap::periode($b, $t);
        $tid = (int) session('user')['tpq_id'];
        $kelasId = (int) $this->request->getGet('kelas_id');

        $sql = "SELECT COALESCE(k.id,0) AS kelas_id, COALESCE(k.nama,'Tanpa Kelas') AS kelas_nama, g.nama AS wali,
                       s.id, s.nis, s.nama, s.jk, s.jilid, s.halaman,
                       COALESCE(SUM(a.status='H'),0) h, COALESCE(SUM(a.status='I'),0) i, COALESCE(SUM(a.status='S'),0) sk, COALESCE(SUM(a.status='A'),0) al
                FROM siswa s
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN guru g ON g.id = k.guru_id
                LEFT JOIN absensi a ON a.siswa_id = s.id AND a.tanggal BETWEEN ? AND ?
                WHERE s.tpq_id = ? AND s.status = 'aktif'" . ($kelasId ? ' AND k.id = ?' : '') . '
                GROUP BY k.id, k.nama, g.nama, s.id, s.nis, s.nama, s.jk, s.jilid, s.halaman
                ORDER BY kelas_nama, s.nama';
        $params = [$from, $to, $tid];
        if ($kelasId) {
            $params[] = $kelasId;
        }

        $groups = [];
        foreach (db_connect()->query($sql, $params)->getResultArray() as $r) {
            $groups[$r['kelas_id']]['nama'] = $r['kelas_nama'];
            $groups[$r['kelas_id']]['wali'] = $r['wali'];
            $groups[$r['kelas_id']]['siswa'][] = $r;
        }
        $html = view('pdf/kelas_siswa', ['tpq' => $this->tpqRow(), 'groups' => $groups, 'bulan' => $b, 'tahun' => $t]);
        return PdfMaker::stream($html, "kelas-siswa-{$t}-{$b}.pdf");
    }

    /** PDF: status SPP + infaq seluruh siswa aktif pada satu bulan. */
    public function sppBulanan()
    {
        [$b, $t] = $this->periode();
        $tid = (int) session('user')['tpq_id'];
        $rows = db_connect()->query(
            "SELECT s.nis, s.nama, COALESCE(k.nama,'-') AS kelas,
                    (SELECT SUM(jumlah) FROM pembayaran p WHERE p.siswa_id=s.id AND p.jenis='SPP' AND p.bulan=? AND p.tahun=?) AS spp,
                    (SELECT MAX(tgl_bayar) FROM pembayaran p WHERE p.siswa_id=s.id AND p.jenis='SPP' AND p.bulan=? AND p.tahun=?) AS tgl,
                    (SELECT SUM(jumlah) FROM pembayaran p WHERE p.siswa_id=s.id AND p.jenis='Infaq' AND p.bulan=? AND p.tahun=?) AS infaq
             FROM siswa s LEFT JOIN kelas k ON k.id = s.kelas_id
             WHERE s.tpq_id = ? AND s.status = 'aktif' ORDER BY k.nama, s.nama",
            [$b, $t, $b, $t, $b, $t, $tid]
        )->getResultArray();
        $html = view('pdf/spp_bulanan', ['tpq' => $this->tpqRow(), 'rows' => $rows, 'bulan' => $b, 'tahun' => $t]);
        return PdfMaker::stream($html, "spp-bulanan-{$t}-{$b}.pdf");
    }

    /** PDF landscape: matriks SPP 12 bulan per siswa dalam satu tahun. */
    public function sppTahunan()
    {
        [, $t] = $this->periode();
        $tid = (int) session('user')['tpq_id'];
        $db = db_connect();

        $siswa = $db->query(
            "SELECT s.id, s.nis, s.nama, COALESCE(k.nama,'-') AS kelas FROM siswa s LEFT JOIN kelas k ON k.id = s.kelas_id
             WHERE s.tpq_id = ? AND (s.status = 'aktif' OR EXISTS (SELECT 1 FROM pembayaran p WHERE p.siswa_id = s.id AND p.tahun = ?))
             ORDER BY k.nama, s.nama",
            [$tid, $t]
        )->getResultArray();

        $paid = [];
        foreach ($db->query("SELECT siswa_id, bulan, SUM(jumlah) j FROM pembayaran WHERE tpq_id = ? AND tahun = ? AND jenis = 'SPP' GROUP BY siswa_id, bulan", [$tid, $t])->getResultArray() as $r) {
            $paid[$r['siswa_id']][(int) $r['bulan']] = (int) $r['j'];
        }
        $html = view('pdf/spp_tahunan', ['tpq' => $this->tpqRow(), 'siswa' => $siswa, 'paid' => $paid, 'tahun' => $t]);
        return PdfMaker::stream($html, "spp-tahunan-{$t}.pdf", 'A4', 'landscape');
    }

    /** Ambil peserta ujian (opsional rentang tanggal) — $lulus: hanya yang lulus. */
    private function ujianRows(bool $lulus): array
    {
        $tid = (int) session('user')['tpq_id'];
        $valid = static fn ($v) => ($v && \DateTime::createFromFormat('Y-m-d', (string) $v)) ? (string) $v : null;
        $dari = $valid($this->request->getGet('dari'));
        $sampai = $valid($this->request->getGet('sampai'));

        $q = db_connect()->table('ujian u')->select('u.tgl_ujian, u.jilid, u.hasil, s.nama')
            ->join('siswa s', 's.id = u.siswa_id')->join('jilid j', 'j.kode = u.jilid', 'left')
            ->where('u.tpq_id', $tid);
        if ($lulus) {
            $q->where('u.hasil', 'lulus');
        }
        if ($dari) {
            $q->where('u.tgl_ujian >=', $dari);
        }
        if ($sampai) {
            $q->where('u.tgl_ujian <=', $sampai);
        }
        $rows = $q->orderBy('u.tgl_ujian', 'ASC')->orderBy('j.urutan', 'ASC')->orderBy('s.nama', 'ASC')->get()->getResultArray();

        if ($dari && $sampai) {
            $periode = $dari === $sampai ? 'Tanggal ' . tgl_id($dari) : 'Tanggal ' . tgl_id($dari) . ' s/d ' . tgl_id($sampai);
        } elseif ($dari) {
            $periode = 'Mulai tanggal ' . tgl_id($dari);
        } elseif ($sampai) {
            $periode = 'Sampai tanggal ' . tgl_id($sampai);
        } else {
            $periode = 'Seluruh periode';
        }
        return [$rows, $periode];
    }

    /** PDF: daftar santri peserta ujian (nama, jilid, tanggal ujian). */
    public function ujianPeserta()
    {
        [$rows, $periode] = $this->ujianRows(false);
        $html = view('pdf/ujian_peserta', ['tpq' => $this->tpqRow(), 'rows' => $rows, 'periode' => $periode]);
        return PdfMaker::stream($html, 'peserta-ujian.pdf');
    }

    /** PDF pengumuman: hanya santri yang lulus. */
    public function ujianLulus()
    {
        [$rows, $periode] = $this->ujianRows(true);
        $html = view('pdf/ujian_lulus', ['tpq' => $this->tpqRow(), 'rows' => $rows, 'periode' => $periode]);
        return PdfMaker::stream($html, 'pengumuman-kelulusan.pdf');
    }

    public function kwitansi(int $id)
    {
        $row = db_connect()->query(
            "SELECT p.*, s.nama AS siswa_nama, s.nis, s.nama_wali, COALESCE(k.nama,'-') AS kelas
             FROM pembayaran p JOIN siswa s ON s.id = p.siswa_id LEFT JOIN kelas k ON k.id = s.kelas_id
             WHERE p.id = ? AND p.tpq_id = ?",
            [$id, (int) session('user')['tpq_id']]
        )->getRowArray();
        if (!$row) {
            return redirect()->to('/pembayaran')->with('error', 'Kwitansi tidak ditemukan.');
        }
        $html = view('pdf/kwitansi', ['tpq' => $this->tpqRow(), 'p' => $row]);
        return PdfMaker::stream($html, 'kwitansi-' . str_replace('/', '-', $row['no_kwitansi']) . '.pdf', 'A5', 'landscape');
    }
}

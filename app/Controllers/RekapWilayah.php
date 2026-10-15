<?php

namespace App\Controllers;

use App\Libraries\Rekap;

/** Rekap perkembangan untuk Korcam (per TPQ) dan Korcab (per Korcam). */
class RekapWilayah extends BaseController
{
    private function periode(): array
    {
        $b = (int) ($this->request->getGet('bulan') ?: $this->request->getPost('bulan') ?: date('n'));
        $t = (int) ($this->request->getGet('tahun') ?: $this->request->getPost('tahun') ?: date('Y'));
        return [min(12, max(1, $b)), min(2100, max(2000, $t))];
    }

    public function korcam()
    {
        [$b, $t] = $this->periode();
        $kid = (int) session('user')['korcam_id'];
        $rows = Rekap::korcamTpqRows($kid, $b, $t);
        return view('rekap/korcam', [
            'title' => 'Rekap TPQ per Korcam', 'b' => $b, 't' => $t, 'rows' => $rows,
            'total' => Rekap::sum(array_column($rows, 'data')),
            'kirim' => Rekap::kirimRow('korcam', $kid, $b, $t),
        ]);
    }

    /** Korcam meneruskan rekap seluruh TPQ yang sudah mengirim ke Korcab. */
    public function kirimKorcam()
    {
        [$b, $t] = $this->periode();
        $kid = (int) session('user')['korcam_id'];
        $rows = Rekap::korcamTpqRows($kid, $b, $t);
        $sent = array_values(array_filter($rows, static fn ($r) => $r['terkirim']));
        if (!$sent) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Belum ada TPQ yang mengirim laporan bulan ini.']);
        }
        $snap = [
            'tpq_total' => count($rows),
            'tpq_terkirim' => count($sent),
            'total' => Rekap::sum(array_column($sent, 'data')),
            'tpq' => array_map(static fn ($r) => ['nama' => $r['nama'], 'data' => $r['data']], $sent),
        ];
        Rekap::saveKirim('korcam', $kid, $b, $t, $snap);
        return $this->response->setJSON(['message' => 'Rekap ' . bulan_nama($b) . " {$t} berhasil dikirim ke Korcab ({$snap['tpq_terkirim']} dari {$snap['tpq_total']} TPQ)."]);
    }

    public function korcab()
    {
        [$b, $t] = $this->periode();
        $rows = Rekap::korcabKorcamRows((int) session('user')['korcab_id'], $b, $t);
        return view('rekap/korcab', [
            'title' => 'Rekap Korcam per Korcab', 'b' => $b, 't' => $t, 'rows' => $rows,
            'total' => Rekap::sum(array_map(static fn ($r) => $r['data']['total'] ?? null, $rows)),
        ]);
    }
}

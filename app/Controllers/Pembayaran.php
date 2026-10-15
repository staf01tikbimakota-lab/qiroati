<?php

namespace App\Controllers;

class Pembayaran extends CrudController
{
    protected string $table = 'pembayaran';
    protected string $path = 'pembayaran';
    protected string $title = 'Pembayaran SPP / Infaq';
    protected array $fields = ['siswa_id', 'jenis', 'bulan', 'tahun', 'jumlah', 'tgl_bayar', 'keterangan'];

    protected function query()
    {
        return db_connect()->table('pembayaran')->select('pembayaran.*, siswa.nama AS siswa_nama')
            ->join('siswa', 'siswa.id = pembayaran.siswa_id', 'left');
    }

    protected function order(): string
    {
        return 'pembayaran.tgl_bayar DESC, pembayaran.id DESC';
    }

    protected function rules(?int $id): array
    {
        return [
            'siswa_id' => 'required|is_natural_no_zero',
            'jenis' => 'required|in_list[SPP,Infaq]',
            'bulan' => 'permit_empty|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
            'tahun' => 'permit_empty|is_natural_no_zero|greater_than[2000]|less_than[2100]',
            'jumlah' => 'required|is_natural_no_zero',
            'tgl_bayar' => 'required|valid_date[Y-m-d]',
            'keterangan' => 'permit_empty|max_length[150]',
        ];
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        $db = db_connect();
        $tid = $this->tpqId();
        $this->own('siswa', $d['siswa_id'] ?? null, 'Siswa');

        $ts = strtotime($d['tgl_bayar']);
        $d['bulan'] ??= (int) date('n', $ts);
        $d['tahun'] ??= (int) date('Y', $ts);

        if ($d['jenis'] === 'SPP') {
            $dup = $db->table('pembayaran')->where(['tpq_id' => $tid, 'siswa_id' => $d['siswa_id'], 'jenis' => 'SPP', 'bulan' => $d['bulan'], 'tahun' => $d['tahun']]);
            if ($id) {
                $dup->where('id !=', $id);
            }
            if ($dup->countAllResults()) {
                throw new \RuntimeException('SPP ' . bulan_nama((int) $d['bulan']) . ' ' . $d['tahun'] . ' untuk siswa ini sudah tercatat.');
            }
        }

        if (!$id) {
            $yr = (int) date('Y', $ts);
            $n = $db->table('pembayaran')->where('tpq_id', $tid)->where("YEAR(tgl_bayar) = {$yr}", null, false)->countAllResults() + 1;
            do {
                $no = sprintf('KW/%d/%d/%04d', $tid, $yr, $n++);
            } while ($db->table('pembayaran')->where(['tpq_id' => $tid, 'no_kwitansi' => $no])->countAllResults());
            $d['no_kwitansi'] = $no;
        }
        return $d;
    }

    protected function ui(): array
    {
        $tarif = (int) (db_connect()->table('tpq')->select('tarif_spp')->where('id', $this->tpqId())->get()->getRow()->tarif_spp ?? 0);
        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan[$i] = bulan_nama($i);
        }
        return [
            'columns' => [
                ['data' => 'no_kwitansi', 'title' => 'No. Kwitansi'],
                ['data' => 'tgl_bayar', 'title' => 'Tgl Bayar', 'render' => 'date'],
                ['data' => 'siswa_nama', 'title' => 'Siswa'],
                ['data' => 'jenis', 'title' => 'Jenis', 'render' => 'badge'],
                ['data' => 'bulan', 'title' => 'Periode', 'render' => 'periode'],
                ['data' => 'jumlah', 'title' => 'Jumlah', 'render' => 'rupiah'],
            ],
            'fields' => [
                ['name' => 'siswa_id', 'label' => 'Siswa', 'type' => 'select', 'lookup' => 'siswa', 'required' => true],
                ['name' => 'jenis', 'label' => 'Jenis', 'type' => 'select', 'required' => true, 'default' => 'SPP', 'options' => $this->opts(['SPP' => 'SPP', 'Infaq' => 'Infaq'])],
                ['name' => 'bulan', 'label' => 'Untuk Bulan', 'type' => 'select', 'default' => 'month', 'options' => $this->opts($bulan)],
                ['name' => 'tahun', 'label' => 'Tahun', 'type' => 'number', 'default' => 'year'],
                ['name' => 'jumlah', 'label' => 'Jumlah (Rp)', 'type' => 'number', 'required' => true, 'default' => (string) $tarif],
                ['name' => 'tgl_bayar', 'label' => 'Tanggal Bayar', 'type' => 'date', 'required' => true, 'default' => 'today'],
                ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'text', 'col' => 12],
            ],
            'actions' => [
                ['title' => 'Cetak Kwitansi', 'icon' => 'fas fa-print', 'class' => 'info', 'href' => '/laporan/kwitansi/{id}', 'target' => '_blank'],
            ],
        ];
    }
}

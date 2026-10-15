<!DOCTYPE html><html><head><?= view('pdf/_head') ?>
<style>
  body { font-size: 11px; }
  .box { border: 2px solid #000; padding: 12px 16px; }
  .row td { padding: 4px 2px; vertical-align: top; }
  .rp { border: 2px solid #000; display: inline-block; padding: 5px 14px; font-size: 15px; font-weight: bold; }
</style></head><body>
<div class="box">
  <?= view('pdf/_kop', ['tpq' => $tpq]) ?>
  <table style="width:100%"><tr>
    <td><h3 style="font-size:16px;letter-spacing:2px">KWITANSI</h3></td>
    <td class="r">No: <b><?= esc($p['no_kwitansi']) ?></b></td>
  </tr></table>
  <table style="width:100%;margin-top:6px">
    <tr class="row"><td width="130">Telah terima dari</td><td>: <b><?= esc($p['nama_wali'] ?: $p['siswa_nama']) ?></b> (wali dari <?= esc($p['siswa_nama']) ?> — NIS <?= esc($p['nis']) ?>, kelas <?= esc($p['kelas']) ?>)</td></tr>
    <tr class="row"><td>Uang sejumlah</td><td>: <i><?= esc(ucfirst(trim(preg_replace('/\s+/', ' ', terbilang($p['jumlah']))))) ?> rupiah</i></td></tr>
    <tr class="row"><td>Untuk pembayaran</td><td>: <?= esc($p['jenis']) ?> <?= $p['jenis'] === 'SPP' ? 'bulan ' . bulan_nama((int) $p['bulan']) . ' ' . $p['tahun'] : 'TPQ (' . bulan_nama((int) $p['bulan']) . ' ' . $p['tahun'] . ')' ?><?= $p['keterangan'] ? ' — ' . esc($p['keterangan']) : '' ?></td></tr>
  </table>
  <table style="width:100%;margin-top:10px"><tr>
    <td><span class="rp"><?= rupiah($p['jumlah']) ?></span></td>
    <td class="c" width="45%">............., <?= tgl_id($p['tgl_bayar']) ?><br>Penerima / Bendahara<br><br><br><u><b><?= esc($tpq['kepala'] ?? '') ?></b></u></td>
  </tr></table>
</div>
</body></html>

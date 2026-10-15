<!DOCTYPE html><html><head><?= view('pdf/_head') ?></head><body>
<?= view('pdf/_kop', ['tpq' => $tpq]) ?>
<h4 class="c">LAPORAN SPP & INFAQ SISWA — <?= strtoupper(bulan_nama($bulan)) ?> <?= $tahun ?></h4>
<?php $sumSpp = $sumInfaq = $lunas = 0; ?>
<table class="t" style="margin-top:6px">
  <thead><tr><th width="22">No</th><th width="60">NIS</th><th>Nama Siswa</th><th width="60">Kelas</th><th width="62">Tgl Bayar</th><th width="70">SPP</th><th width="70">Infaq</th><th width="50">Status</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $i => $r): $sumSpp += (int) $r['spp']; $sumInfaq += (int) $r['infaq']; if ($r['spp']) $lunas++; ?>
    <tr><td class="c"><?= $i + 1 ?></td><td><?= esc($r['nis']) ?></td><td><?= esc($r['nama']) ?></td><td><?= esc($r['kelas']) ?></td>
      <td class="c"><?= $r['tgl'] ? date('d/m/Y', strtotime($r['tgl'])) : '-' ?></td>
      <td class="r"><?= $r['spp'] ? number_format($r['spp'], 0, ',', '.') : '-' ?></td>
      <td class="r"><?= $r['infaq'] ? number_format($r['infaq'], 0, ',', '.') : '-' ?></td>
      <td class="c"><?= $r['spp'] ? 'Lunas' : 'Belum' ?></td></tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="5" class="r">TOTAL</th><th class="r"><?= number_format($sumSpp, 0, ',', '.') ?></th><th class="r"><?= number_format($sumInfaq, 0, ',', '.') ?></th><th class="c"><?= $lunas ?>/<?= count($rows) ?></th></tr></tfoot>
</table>
<p style="margin-top:6px">Total penerimaan: <b><?= rupiah($sumSpp + $sumInfaq) ?></b> — Siswa lunas SPP: <?= $lunas ?> dari <?= count($rows) ?> siswa.</p>
<?= view('pdf/_ttd', ['tpq' => $tpq]) ?>
</body></html>

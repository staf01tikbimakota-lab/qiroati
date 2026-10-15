<!DOCTYPE html><html><head><?= view('pdf/_head') ?><style>table.t td, table.t th { font-size: 8.5px; padding: 2px 3px; }</style></head><body>
<?= view('pdf/_kop', ['tpq' => $tpq]) ?>
<h4 class="c">REKAPITULASI SPP SISWA TAHUN <?= $tahun ?></h4>
<?php $colTot = array_fill(1, 12, 0); $grand = 0; ?>
<table class="t" style="margin-top:6px">
  <thead><tr><th width="18">No</th><th>Nama Siswa</th><th width="48">Kelas</th>
    <?php for ($m = 1; $m <= 12; $m++): ?><th width="40"><?= substr(bulan_nama($m), 0, 3) ?></th><?php endfor; ?><th width="52">Total</th><th width="26">Bln</th></tr></thead>
  <tbody>
  <?php foreach ($siswa as $i => $s): $row = $paid[$s['id']] ?? []; $tot = array_sum($row); $grand += $tot; ?>
    <tr><td class="c"><?= $i + 1 ?></td><td><?= esc($s['nama']) ?></td><td><?= esc($s['kelas']) ?></td>
      <?php for ($m = 1; $m <= 12; $m++): $v = $row[$m] ?? 0; $colTot[$m] += $v; ?><td class="r"><?= $v ? number_format($v / 1000, 0, ',', '.') . 'k' : '-' ?></td><?php endfor; ?>
      <td class="r"><b><?= number_format($tot, 0, ',', '.') ?></b></td><td class="c"><?= count($row) ?></td></tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="3" class="r">TOTAL</th>
    <?php for ($m = 1; $m <= 12; $m++): ?><th class="r"><?= $colTot[$m] ? number_format($colTot[$m] / 1000, 0, ',', '.') . 'k' : '-' ?></th><?php endfor; ?>
    <th class="r"><?= number_format($grand, 0, ',', '.') ?></th><th></th></tr></tfoot>
</table>
<p style="margin-top:4px"><i>Keterangan: angka dalam ribuan rupiah (k = × 1.000); "-" berarti belum membayar. Total keseluruhan: <b><?= rupiah($grand) ?></b></i></p>
<?= view('pdf/_ttd', ['tpq' => $tpq]) ?>
</body></html>

<!DOCTYPE html><html><head><?= view('pdf/_head') ?></head><body>
<?= view('pdf/_kop', ['tpq' => $tpq]) ?>
<h4 class="c">DAFTAR SISWA PER KELAS — <?= strtoupper(bulan_nama($bulan)) ?> <?= $tahun ?></h4>
<?php if (!$groups): ?><p class="c">Tidak ada data siswa.</p><?php endif; ?>
<?php foreach ($groups as $g): ?>
  <p style="margin-top:10px"><b>Kelas: <?= esc($g['nama']) ?></b> &nbsp;|&nbsp; Wali kelas/guru: <?= esc($g['wali'] ?: '-') ?> &nbsp;|&nbsp; Jumlah siswa: <?= count($g['siswa']) ?><br>Sebaran jilid: <?= esc(jilid_ringkas(array_count_values(array_column($g['siswa'], 'jilid')))) ?></p>
  <table class="t">
    <thead><tr><th rowspan="2" width="22">No</th><th rowspan="2" width="60">NIS</th><th rowspan="2">Nama Siswa</th><th rowspan="2" width="22">JK</th><th rowspan="2" width="55">Jilid / Hal</th><th colspan="4">Kehadiran</th><th rowspan="2" width="38">%</th></tr>
      <tr><th width="20">H</th><th width="20">I</th><th width="20">S</th><th width="20">A</th></tr></thead>
    <tbody>
    <?php foreach ($g['siswa'] as $i => $s): $tot = $s['h'] + $s['i'] + $s['sk'] + $s['al']; ?>
      <tr><td class="c"><?= $i + 1 ?></td><td><?= esc($s['nis']) ?></td><td><?= esc($s['nama']) ?></td><td class="c"><?= $s['jk'] ?></td>
        <td class="c"><?= esc($s['jilid']) ?> / <?= $s['halaman'] ?></td>
        <td class="c"><?= $s['h'] ?></td><td class="c"><?= $s['i'] ?></td><td class="c"><?= $s['sk'] ?></td><td class="c"><?= $s['al'] ?></td>
        <td class="c"><?= $tot ? round($s['h'] * 100 / $tot) . '%' : '-' ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endforeach; ?>
<?= view('pdf/_ttd', ['tpq' => $tpq]) ?>
</body></html>

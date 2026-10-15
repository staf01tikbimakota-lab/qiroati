<!DOCTYPE html><html><head><?= view('pdf/_head') ?></head><body>
<?= view('pdf/_kop', ['tpq' => $tpq]) ?>
<h4 class="c">DAFTAR SANTRI PESERTA UJIAN KENAIKAN JILID</h4>
<p class="c"><?= esc($periode) ?></p>
<table class="t" style="margin-top:8px">
  <thead><tr><th width="28">No</th><th>Nama Santri</th><th width="90">Jilid</th><th width="90">Tgl Ujian</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $i => $r): ?>
    <tr><td class="c"><?= $i + 1 ?></td><td><?= esc($r['nama']) ?></td><td class="c"><?= esc($r['jilid']) ?></td><td class="c"><?= date('d/m/Y', strtotime($r['tgl_ujian'])) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="4" class="c">Tidak ada peserta ujian.</td></tr><?php endif; ?>
  </tbody>
</table>
<p style="margin-top:6px">Jumlah peserta: <b><?= count($rows) ?></b> santri.</p>
<?= view('pdf/_ttd', ['tpq' => $tpq]) ?>
</body></html>

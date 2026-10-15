<!DOCTYPE html><html><head><?= view('pdf/_head') ?></head><body>
<?= view('pdf/_kop', ['tpq' => $tpq]) ?>
<h4 class="c">PENGUMUMAN HASIL UJIAN KENAIKAN JILID</h4>
<p class="c"><?= esc($periode) ?></p>
<p style="margin:8px 0">Alhamdulillah, berdasarkan hasil ujian, santri berikut dinyatakan <b>LULUS</b> dan naik ke jilid berikutnya:</p>
<table class="t">
  <thead><tr><th width="28">No</th><th>Nama Santri</th><th width="90">Jilid</th><th width="90">Tgl Ujian</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $i => $r): ?>
    <tr><td class="c"><?= $i + 1 ?></td><td><?= esc($r['nama']) ?></td><td class="c"><?= esc($r['jilid']) ?></td><td class="c"><?= date('d/m/Y', strtotime($r['tgl_ujian'])) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="4" class="c">Belum ada santri yang dinyatakan lulus.</td></tr><?php endif; ?>
  </tbody>
</table>
<p style="margin-top:6px">Jumlah santri lulus: <b><?= count($rows) ?></b>. Selamat, semoga istiqamah dalam belajar Al-Qur'an.</p>
<?= view('pdf/_ttd', ['tpq' => $tpq]) ?>
</body></html>

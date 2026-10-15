<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<form class="form-inline mb-3" method="get">
  <select name="bulan" class="form-control mr-2"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= $i == $b ? 'selected' : '' ?>><?= bulan_nama($i) ?></option><?php endfor; ?></select>
  <input type="number" name="tahun" value="<?= $t ?>" class="form-control mr-2" style="width:100px">
  <button class="btn btn-primary">Tampilkan</button>
</form>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Perkembangan TPQ — <?= bulan_nama($b) ?> <?= $t ?></h3>
    <div class="card-tools">
      <?= $kirim ? '<span class="badge badge-success mr-2">Sudah dikirim ke Korcab ' . esc($kirim['tgl_kirim']) . '</span>' : '' ?>
      <button id="btnKirim" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> <?= $kirim ? 'Kirim Ulang' : 'Kirim' ?> ke Korcab</button>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-bordered table-sm table-hover mb-0">
      <thead class="thead-light"><tr><th>TPQ</th><th>Status</th><?= view('rekap/_head') ?></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= esc($r['nama']) ?></td>
          <td><?= $r['terkirim'] ? '<span class="badge badge-success">Terkirim</span><br><small>' . esc($r['tgl_kirim']) . '</small>' : '<span class="badge badge-secondary">Belum</span>' ?></td>
          <?= view('rekap/_cells', ['d' => $r['data']]) ?>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="13" class="text-center text-muted">Belum ada TPQ pada korcam ini.</td></tr><?php endif; ?>
      </tbody>
      <tfoot class="bg-light font-weight-bold"><tr><td colspan="2">TOTAL KORCAM</td><?= view('rekap/_cells', ['d' => $total]) ?></tr></tfoot>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#btnKirim').on('click', async function () {
  const c = await Swal.fire({ title: 'Kirim rekap ke Korcab?', text: 'Hanya TPQ yang sudah mengirim yang ikut direkap.', icon: 'question', showCancelButton: true, confirmButtonText: 'Kirim', cancelButtonText: 'Batal' });
  if (!c.isConfirmed) return;
  const fd = new FormData(); fd.append('bulan', <?= $b ?>); fd.append('tahun', <?= $t ?>);
  const r = await App.api(App.BASE + '/rekap/korcam/kirim', { method: 'POST', body: fd });
  await Swal.fire({ icon: r.ok ? 'success' : 'error', text: r.json.message });
  if (r.ok) location.reload();
});
</script>
<?= $this->endSection() ?>

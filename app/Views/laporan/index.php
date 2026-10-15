<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $qs = 'bulan=' . $b . '&tahun=' . $t; ?>
<form class="form-inline mb-3" method="get">
  <select name="bulan" class="form-control mr-2"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= $i == $b ? 'selected' : '' ?>><?= bulan_nama($i) ?></option><?php endfor; ?></select>
  <input type="number" name="tahun" value="<?= $t ?>" class="form-control mr-2" style="width:100px">
  <button class="btn btn-primary">Tampilkan</button>
</form>

<div class="row">
  <div class="col-lg-7">
    <div class="card"><div class="card-header"><h3 class="card-title">Rekap <?= bulan_nama($b) ?> <?= $t ?></h3></div>
      <table class="table table-sm mb-0"><tbody>
        <tr><td>Siswa aktif (L / P)</td><td class="text-right"><b><?= $rekap['siswa_aktif'] ?></b> (<?= $rekap['laki'] ?> / <?= $rekap['perempuan'] ?>)</td></tr>
        <tr><td>Guru aktif / Kelas</td><td class="text-right"><?= $rekap['guru'] ?> / <?= $rekap['kelas'] ?></td></tr>
        <tr><td>Kehadiran siswa</td><td class="text-right"><?= $rekap['hadir_persen'] ?>%</td></tr>
        <tr><td>Siswa naik jilid / lulus (tamat jilid akhir)</td><td class="text-right"><?= $rekap['naik_jilid'] ?> / <?= $rekap['lulus'] ?></td></tr>
        <tr><td>Sebaran jilid</td><td class="text-right"><?= esc(jilid_ringkas($rekap['per_jilid'])) ?></td></tr>
        <tr><td>Penerimaan SPP</td><td class="text-right"><?= rupiah($rekap['spp_masuk']) ?></td></tr>
        <tr><td>Penerimaan Infaq</td><td class="text-right"><?= rupiah($rekap['infaq_masuk']) ?></td></tr>
        <tr><td>Siswa belum bayar SPP</td><td class="text-right"><?= $rekap['menunggak'] ?></td></tr>
      </tbody></table>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card card-outline card-primary"><div class="card-header"><h3 class="card-title">Cetak PDF</h3></div>
      <div class="card-body">
        <div class="form-group"><label>Kelas (opsional)</label>
          <select id="kelas" class="form-control"><option value="">Semua kelas</option><?php foreach ($kelas as $k): ?><option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option><?php endforeach; ?></select></div>
        <a id="lnkKelas" target="_blank" class="btn btn-block btn-outline-danger" href="<?= site_url('laporan/kelas-siswa') . '?' . $qs ?>"><i class="fas fa-file-pdf"></i> Kelas Siswa per Bulan</a>
        <a target="_blank" class="btn btn-block btn-outline-danger" href="<?= site_url('laporan/spp-bulanan') . '?' . $qs ?>"><i class="fas fa-file-pdf"></i> SPP Siswa Bulanan</a>
        <a target="_blank" class="btn btn-block btn-outline-danger" href="<?= site_url('laporan/spp-tahunan') . '?tahun=' . $t ?>"><i class="fas fa-file-pdf"></i> SPP Siswa Tahunan <?= $t ?></a>
        <small class="text-muted">Kwitansi dicetak dari menu SPP / Infaq.</small>
      </div>
    </div>
    <div class="card card-outline card-success"><div class="card-header"><h3 class="card-title">Kirim ke Korcam</h3></div>
      <div class="card-body">
        <?php if ($kirim): ?><p class="mb-2"><span class="badge badge-success">Terkirim</span> <?= esc($kirim['tgl_kirim']) ?></p><?php else: ?><p class="mb-2"><span class="badge badge-warning">Belum dikirim</span></p><?php endif; ?>
        <button id="btnKirim" class="btn btn-success btn-block"><i class="fas fa-paper-plane"></i> <?= $kirim ? 'Kirim Ulang' : 'Kirim' ?> Laporan <?= bulan_nama($b) ?> <?= $t ?></button>
        <small class="text-muted">Data yang dikirim adalah rekap saat tombol ditekan.</small>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#kelas').on('change', function () {
  const base = <?= json_encode(site_url('laporan/kelas-siswa') . '?' . $qs) ?>;
  $('#lnkKelas').attr('href', base + ($(this).val() ? '&kelas_id=' + $(this).val() : ''));
});
$('#btnKirim').on('click', async function () {
  const c = await Swal.fire({ title: 'Kirim laporan ke Korcam?', text: 'Periode <?= bulan_nama($b) . ' ' . $t ?>', icon: 'question', showCancelButton: true, confirmButtonText: 'Kirim', cancelButtonText: 'Batal' });
  if (!c.isConfirmed) return;
  const fd = new FormData(); fd.append('bulan', <?= $b ?>); fd.append('tahun', <?= $t ?>);
  const r = await App.api(App.BASE + '/laporan/kirim', { method: 'POST', body: fd });
  await Swal.fire({ icon: r.ok ? 'success' : 'error', text: r.json.message });
  if (r.ok) location.reload();
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$role = session('user')['role'];
$box = static function (string $color, $val, string $label, string $icon): string {
    return '<div class="col-md-3 col-6"><div class="small-box bg-' . $color . '"><div class="inner"><h3>' . $val . '</h3><p>' . $label . '</p></div><div class="icon"><i class="' . $icon . '"></i></div></div></div>';
};
?>
<?php if ($role === 'tpq'): ?>
  <p class="text-muted">Ringkasan <?= bulan_nama($b) ?> <?= $t ?></p>
  <div class="row">
    <?= $box('info', $rekap['siswa_aktif'], 'Siswa Aktif', 'fas fa-user-graduate') ?>
    <?= $box('success', $rekap['hadir_persen'] . '%', 'Kehadiran Bulan Ini', 'fas fa-clipboard-check') ?>
    <?= $box('warning', rupiah($rekap['spp_masuk']), 'SPP Masuk Bulan Ini', 'fas fa-money-bill-wave') ?>
    <?= $box('danger', $rekap['menunggak'], 'Belum Bayar SPP', 'fas fa-exclamation-circle') ?>
  </div>
  <div class="card"><div class="card-body">
    <b>Sebaran jilid:</b> <?= esc(jilid_ringkas($rekap['per_jilid'])) ?><br>
    <b>Laporan <?= bulan_nama($b) ?>:</b>
    <?= $kirim ? '<span class="badge badge-success">Terkirim ke Korcam ' . esc($kirim['tgl_kirim']) . '</span>' : '<span class="badge badge-warning">Belum dikirim</span>' ?>
    <a href="<?= site_url('laporan') ?>" class="btn btn-sm btn-outline-primary ml-2">Buka Laporan Bulanan</a>
  </div></div>

<?php elseif ($role === 'korcam'): ?>
  <?php $sent = count(array_filter($rows, static fn ($r) => $r['terkirim'])); ?>
  <p class="text-muted">Periode <?= bulan_nama($b) ?> <?= $t ?></p>
  <div class="row">
    <?= $box('primary', count($rows), 'Jumlah TPQ', 'fas fa-mosque') ?>
    <?= $box('success', $sent . ' / ' . count($rows), 'TPQ Sudah Kirim', 'fas fa-paper-plane') ?>
    <?= $box('info', $total['siswa_aktif'], 'Total Siswa Aktif', 'fas fa-user-graduate') ?>
    <?= $box('warning', $total['hadir_persen'] . '%', 'Rata-rata Kehadiran', 'fas fa-clipboard-check') ?>
  </div>
  <a href="<?= site_url('rekap/korcam') ?>" class="btn btn-primary">Lihat Rekap per TPQ</a>

<?php elseif ($role === 'korcab'): ?>
  <?php $sent = count(array_filter($rows, static fn ($r) => $r['terkirim'])); ?>
  <p class="text-muted">Periode <?= bulan_nama($b) ?> <?= $t ?></p>
  <div class="row">
    <?= $box('primary', count($rows), 'Jumlah Korcam', 'fas fa-map-marked-alt') ?>
    <?= $box('success', $sent . ' / ' . count($rows), 'Korcam Sudah Kirim', 'fas fa-paper-plane') ?>
    <?= $box('info', $total['siswa_aktif'], 'Total Siswa Aktif', 'fas fa-user-graduate') ?>
    <?= $box('warning', $total['hadir_persen'] . '%', 'Rata-rata Kehadiran', 'fas fa-clipboard-check') ?>
  </div>
  <a href="<?= site_url('rekap/korcab') ?>" class="btn btn-primary">Lihat Rekap per Korcam</a>

<?php else: ?>
  <div class="row">
    <?php $ic = ['Korcab' => 'fas fa-sitemap', 'Korcam' => 'fas fa-map-marked-alt', 'TPQ' => 'fas fa-mosque', 'User' => 'fas fa-users']; $cl = ['primary', 'info', 'success', 'warning']; $i = 0; ?>
    <?php foreach ($cnt as $k => $v): ?><?= $box($cl[$i++ % 4], $v, $k, $ic[$k]) ?><?php endforeach; ?>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

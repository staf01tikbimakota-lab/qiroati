<?php
helper('app');
$u = session('user');
$uri = uri_string();
$menu = [
    'tpq' => [
        ['Dashboard', 'dashboard', 'fas fa-tachometer-alt'],
        ['MASTER DATA'],
        ['Data Siswa', 'siswa', 'fas fa-user-graduate'],
        ['Data Guru / Ustadz', 'guru', 'fas fa-chalkboard-teacher'],
        ['Kelas', 'kelas', 'fas fa-school'],
        ['Profil & Logo TPQ', 'profil', 'fas fa-id-card'],
        ['AKADEMIK'],
        ['Penempatan Kelas & Jilid', 'penempatan', 'fas fa-layer-group'],
        ['Jadwal', 'jadwal', 'fas fa-calendar-alt'],
        ['Absensi', 'absensi', 'fas fa-clipboard-check'],
        ['Progress Jilid', 'progress', 'fas fa-book-reader'],
        ['Ujian & Kelulusan', 'ujian', 'fas fa-award'],
        ['KEUANGAN'],
        ['SPP / Infaq', 'pembayaran', 'fas fa-money-bill-wave'],
        ['LAPORAN'],
        ['Laporan Bulanan', 'laporan', 'fas fa-file-pdf'],
    ],
    'korcam' => [['Dashboard', 'dashboard', 'fas fa-tachometer-alt'], ['REKAP'], ['Rekap per TPQ', 'rekap/korcam', 'fas fa-chart-line']],
    'korcab' => [['Dashboard', 'dashboard', 'fas fa-tachometer-alt'], ['REKAP'], ['Rekap per Korcam', 'rekap/korcab', 'fas fa-chart-line']],
    'superadmin' => [
        ['Dashboard', 'dashboard', 'fas fa-tachometer-alt'],
        ['MASTER WILAYAH'],
        ['Korcab', 'master/korcab', 'fas fa-sitemap'],
        ['Korcam', 'master/korcam', 'fas fa-map-marked-alt'],
        ['TPQ', 'master/tpq', 'fas fa-mosque'],
        ['MASTER KURIKULUM'],
        ['Jilid Qiroati', 'master/jilid', 'fas fa-book-open'],
        ['PENGGUNA'],
        ['User', 'master/users', 'fas fa-users-cog'],
    ],
][$u['role']] ?? [];
$roleLabel = ['tpq' => 'Admin TPQ', 'korcam' => 'Korcam', 'korcab' => 'Korcab', 'superadmin' => 'Super Admin'][$u['role']] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <title><?= esc($title ?? 'TPQ Qiroati') ?> | TPQ Qiroati</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li></ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-none d-md-block"><span class="nav-link"><i class="fas fa-user-circle"></i> <?= esc($u['nama']) ?> <small class="text-muted">(<?= $roleLabel ?>)</small></span></li>
      <li class="nav-item"><a class="nav-link text-danger" href="<?= site_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= site_url('dashboard') ?>" class="brand-link"><i class="fas fa-quran brand-image mt-1 ml-3"></i><span class="brand-text font-weight-light">TPQ Qiroati</span></a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3"><div class="info"><a href="#" class="d-block"><?= esc($u['unit']) ?></a></div></div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <?php foreach ($menu as $m): ?>
            <?php if (count($m) === 1): ?>
              <li class="nav-header"><?= $m[0] ?></li>
            <?php else: $active = ($uri === $m[1] || strpos($uri, $m[1] . '/') === 0); ?>
              <li class="nav-item"><a href="<?= site_url($m[1]) ?>" class="nav-link <?= $active ? 'active' : '' ?>"><i class="nav-icon <?= $m[2] ?>"></i><p><?= $m[0] ?></p></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1><?= esc($title ?? '') ?></h1></div></section>
    <section class="content"><div class="container-fluid">
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
      <?= $this->renderSection('content') ?>
    </div></section>
  </div>
  <footer class="main-footer"><small>Aplikasi Manajemen TPQ — Metode Qiroati</small></footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>window.BASE_URL = <?= json_encode(rtrim(base_url(), '/')) ?>;</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>

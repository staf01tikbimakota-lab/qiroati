<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | TPQ Qiroati</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center"><h1 class="h3"><i class="fas fa-quran"></i> <b>TPQ</b> Qiroati</h1></div>
    <div class="card-body">
      <p class="login-box-msg">Masuk untuk memulai sesi</p>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
      <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>
        <div class="input-group mb-3"><input name="username" class="form-control" placeholder="Username" value="<?= esc(old('username')) ?>" required autofocus><div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div></div>
        <div class="input-group mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required><div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div></div>
        <button class="btn btn-primary btn-block">Masuk</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card"><div class="card-body">
  <form id="frm" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-3 text-center">
        <div class="mb-2"><img id="prev" src="<?= !empty($tpq['logo']) ? base_url('berkas/logo/' . $tpq['logo']) : '' ?>" alt="" style="max-height:140px;max-width:100%;<?= empty($tpq['logo']) ? 'display:none' : '' ?>"></div>
        <div id="nologo" class="text-muted mb-2" <?= !empty($tpq['logo']) ? 'style="display:none"' : '' ?>><i class="fas fa-image fa-3x"></i><br>Belum ada logo</div>
        <input type="file" name="logo" id="logo" class="form-control-file" accept="image/png,image/jpeg">
        <small class="text-muted">PNG/JPG, maks. 1 MB</small>
        <?php if (!empty($tpq['logo'])): ?><div class="custom-control custom-checkbox mt-2"><input type="checkbox" class="custom-control-input" id="hapus" name="hapus_logo" value="1"><label class="custom-control-label" for="hapus">Hapus logo</label></div><?php endif; ?>
      </div>
      <div class="col-md-9">
        <div class="form-group"><label>Nama TPQ</label><input class="form-control" value="<?= esc($tpq['nama'] ?? '') ?>" disabled><small class="text-muted">Diubah oleh Super Admin.</small></div>
        <div class="form-row">
          <div class="form-group col-md-6"><label>Kepala TPQ</label><input name="kepala" class="form-control" value="<?= esc($tpq['kepala'] ?? '') ?>"></div>
          <div class="form-group col-md-6"><label>Telp</label><input name="telp" class="form-control" value="<?= esc($tpq['telp'] ?? '') ?>"></div>
          <div class="form-group col-md-6"><label>Desa/Kelurahan</label><input name="desa" class="form-control" value="<?= esc($tpq['desa'] ?? '') ?>"></div>
          <div class="form-group col-md-12"><label>Alamat</label><textarea name="alamat" rows="2" class="form-control"><?= esc($tpq['alamat'] ?? '') ?></textarea></div>
        </div>
        <button class="btn btn-primary" id="btn"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </div>
  </form>
</div></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#logo').on('change', function () {
  const f = this.files[0]; if (!f) return;
  $('#prev').attr('src', URL.createObjectURL(f)).show(); $('#nologo').hide();
});
$('#frm').on('submit', async function (e) {
  e.preventDefault();
  $('#btn').prop('disabled', true);
  const r = await App.api(App.BASE + '/profil/simpan', { method: 'POST', body: new FormData(this) });
  $('#btn').prop('disabled', false);
  await Swal.fire({ icon: r.ok ? 'success' : 'error', text: r.json.message });
  if (r.ok) location.reload();
});
</script>
<?= $this->endSection() ?>

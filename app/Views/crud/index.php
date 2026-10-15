<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $extra ?? '' ?>
<div class="card">
  <div class="card-header">
    <button id="btnAdd" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Data</button>
  </div>
  <div class="card-body"><table id="tbl" class="table table-bordered table-striped table-sm w-100"></table></div>
</div>

<div class="modal fade" id="modalForm" tabindex="-1" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <form id="frm" class="modal-content" autocomplete="off">
      <div class="modal-header"><h5 class="modal-title" id="modalTitle"></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div id="frmErr" class="alert alert-danger d-none"></div>
        <input type="hidden" name="id" id="f_id">
        <div class="row" id="fields"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSave"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>window.CRUD_CFG = <?= json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="<?= base_url('assets/js/crud.js') ?>"></script>
<?= $this->endSection() ?>

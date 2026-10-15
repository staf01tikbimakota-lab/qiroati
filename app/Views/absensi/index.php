<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-body">
    <div class="form-row align-items-end">
      <div class="form-group col-md-4"><label>Kelas</label><select id="kelas" class="form-control"></select></div>
      <div class="form-group col-md-3"><label>Tanggal</label><input type="date" id="tgl" class="form-control"></div>
      <div class="form-group col-md-5">
        <button id="btnMuat" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
        <button id="btnHadir" class="btn btn-outline-success">Semua Hadir</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <thead class="thead-light"><tr><th width="40">No</th><th>NIS</th><th>Nama</th><th class="text-center">Hadir</th><th class="text-center">Izin</th><th class="text-center">Sakit</th><th class="text-center">Alpa</th><th>Keterangan</th></tr></thead>
        <tbody id="rows"><tr><td colspan="8" class="text-center text-muted">Pilih kelas dan tanggal, lalu klik Tampilkan.</td></tr></tbody>
      </table>
    </div>
    <button id="btnSimpan" class="btn btn-success d-none"><i class="fas fa-save"></i> Simpan Absensi</button>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(async function () {
  const esc = App.esc;
  const kelas = await App.lookup('kelas');
  $('#kelas').html(kelas.length ? kelas.map((k) => `<option value="${k.id}">${esc(k.text)}</option>`).join('') : '<option value="">(belum ada kelas)</option>');
  $('#tgl').val(App.today());

  async function muat() {
    if (!$('#kelas').val()) return;
    const r = await App.api(`${App.BASE}/absensi/data?kelas_id=${$('#kelas').val()}&tanggal=${$('#tgl').val()}`);
    if (!r.ok) return App.toast('error', r.json.message || 'Gagal memuat');
    const rows = r.json.data;
    if (!rows.length) {
      $('#rows').html('<tr><td colspan="8" class="text-center text-muted">Tidak ada siswa aktif di kelas ini.</td></tr>');
      return $('#btnSimpan').addClass('d-none');
    }
    $('#rows').html(rows.map((s, i) => {
      const st = s.status || 'H';
      const radio = (v) => `<td class="text-center"><input type="radio" name="st_${s.id}" value="${v}" ${st === v ? 'checked' : ''}></td>`;
      return `<tr data-id="${s.id}"><td>${i + 1}</td><td>${esc(s.nis)}</td><td>${esc(s.nama)}</td>${radio('H')}${radio('I')}${radio('S')}${radio('A')}<td><input class="form-control form-control-sm ket" maxlength="150" value="${esc(s.keterangan || '')}"></td></tr>`;
    }).join(''));
    $('#btnSimpan').removeClass('d-none');
  }

  $('#btnMuat').on('click', muat);
  $('#btnHadir').on('click', () => $('#rows input[value="H"]').prop('checked', true));

  $('#btnSimpan').on('click', async function () {
    const items = $('#rows tr[data-id]').map(function () {
      const id = $(this).data('id');
      return { siswa_id: id, status: $(`input[name="st_${id}"]:checked`).val(), keterangan: $(this).find('.ket').val() };
    }).get();
    const r = await App.api(App.BASE + '/absensi/simpan', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ tanggal: $('#tgl').val(), items }) });
    App.toast(r.ok ? 'success' : 'error', r.json.message);
  });
})();
</script>
<?= $this->endSection() ?>

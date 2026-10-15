/* Mesin CRUD generik: DataTables + modal form + fetch() ke endpoint JSON controller */
(function ($) {
  const cfg = window.CRUD_CFG;
  const BASE = App.BASE;
  const URL_ = (p) => `${BASE}/${cfg.path}${p}`;
  const esc = App.esc;

  const BULAN = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const COLOR = { aktif: 'success', lulus: 'info', keluar: 'secondary', nonaktif: 'secondary', lanjut: 'primary', ulang: 'warning', lulus_jilid: 'success', menunggu: 'warning', tidak_lulus: 'danger', SPP: 'primary', Infaq: 'success', superadmin: 'danger', korcab: 'dark', korcam: 'info', tpq: 'primary' };
  const hm = (v) => (v ? String(v).substring(0, 5) : '');

  // Renderer bernama (dipanggil dari config PHP: 'render' => 'nama')
  const R = {
    jk: (v) => (v === 'L' ? 'Laki-laki' : v === 'P' ? 'Perempuan' : ''),
    date: (v) => (v ? String(v).split('-').reverse().join('/') : ''),
    rupiah: (v) => (v == null ? '' : 'Rp ' + Number(v).toLocaleString('id-ID')),
    badge: (v) => (v ? `<span class="badge badge-${COLOR[v] || 'light'}">${esc(String(v).replace('_', ' '))}</span>` : ''),
    aktif: (v) => (String(v) === '1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'),
    foto: (v, r, kind) => (v ? `<img src="${BASE}/berkas/${kind}/${esc(v)}" class="img-circle" style="width:34px;height:34px;object-fit:cover">` : '<i class="fas fa-user-circle fa-2x text-muted"></i>'),
    jilidlist: (v) => (v ? esc(String(v).split('|').join(', ')) : '<span class="badge badge-info">Semua jilid</span>'),
    jam: (v, r) => `${hm(r.jam_mulai)} - ${hm(r.jam_selesai)}`,
    periode: (v, r) => (r.bulan ? `${BULAN[r.bulan]} ${r.tahun}` : ''),
  };

  let table, modal;

  function inputHtml(f, opts) {
    const attr = `name="${f.name}" id="f_${f.name}" class="form-control"`;
    if (f.type === 'select') {
      return `<select ${attr}><option value="">-- Pilih --</option>${opts.map((o) => `<option value="${esc(o.v)}">${esc(o.t)}</option>`).join('')}</select>`;
    }
    if (f.type === 'multiselect') return `<select multiple size="8" name="${f.name}[]" id="f_${f.name}" class="form-control">${opts.map((o) => `<option value="${esc(o.v)}">${esc(o.t)}</option>`).join('')}</select>`;
    if (f.type === 'file') return `<input type="file" name="${f.name}" id="f_${f.name}" class="form-control-file" accept="image/png,image/jpeg"><div id="pv_${f.name}" class="mt-2"></div><small class="text-muted">PNG/JPG, maks. 2 MB</small>`;
    if (f.type === 'textarea') return `<textarea ${attr} rows="2"></textarea>`;
    return `<input type="${f.type || 'text'}" ${attr} autocomplete="off">`;
  }

  async function buildFields() {
    let html = '';
    for (const f of cfg.fields) {
      let opts = f.options || [];
      if (f.lookup) opts = (await App.lookup(f.lookup)).map((o) => ({ v: o.id, t: o.text }));
      html += `<div class="form-group col-md-${f.col || 6}">
        <label for="f_${f.name}">${esc(f.label)}${f.required ? ' <span class="text-danger">*</span>' : ''}</label>
        ${inputHtml(f, opts)}
        <div class="invalid-feedback" data-err="${f.name}"></div>
        ${f.hint ? `<small class="text-muted">${esc(f.hint)}</small>` : ''}
      </div>`;
    }
    $('#fields').html(html);
  }

  function defaultValue(f) {
    const d = new Date();
    if (f.default === 'today') return App.today();
    if (f.default === 'year') return d.getFullYear();
    if (f.default === 'month') return d.getMonth() + 1;
    return f.default ?? '';
  }

  function clearErrors() {
    $('#frmErr').addClass('d-none').text('');
    $('#frm .is-invalid').removeClass('is-invalid');
    $('#frm [data-err]').text('');
  }

  async function openForm(id) {
    clearErrors();
    await buildFields();
    $('#frm')[0].reset();
    $('#f_id').val('');
    cfg.fields.forEach((f) => $('#f_' + f.name).val(defaultValue(f)));
    $('#modalTitle').text((id ? 'Ubah ' : 'Tambah ') + cfg.title);

    if (id) {
      const r = await App.api(URL_('/show/' + id));
      if (!r.ok) return App.toast('error', r.json.message || 'Gagal memuat data');
      $('#f_id').val(id);
      cfg.fields.forEach((f) => {
        if (f.type === 'multiselect') $('#f_' + f.name).val(r.json[f.name + '_list'] ? String(r.json[f.name + '_list']).split('|') : []);
        else if (f.type !== 'password' && f.type !== 'file') $('#f_' + f.name).val(r.json[f.name] ?? '');
      });
      cfg.fields.filter((f) => f.type === 'file').forEach((f) => {
        const v = r.json[f.name];
        $('#pv_' + f.name).html(v ? `<img src="${BASE}/berkas/${f.kind}/${esc(v)}" style="max-height:90px" class="img-thumbnail"><div class="custom-control custom-checkbox mt-1"><input type="checkbox" class="custom-control-input" id="hp_${f.name}" name="hapus_${f.name}" value="1"><label class="custom-control-label" for="hp_${f.name}">Hapus foto</label></div>` : '');
      });
    }
    modal.modal('show');
  }

  async function load() {
    const r = await App.api(URL_('/data'));
    if (!r.ok) return App.toast('error', r.json.message || 'Gagal memuat data');
    table.clear().rows.add(r.json.data).draw();
  }

  function actionButtons(row) {
    let h = '<div class="btn-group btn-group-sm">';
    (cfg.actions || []).forEach((a) => {
      h += `<a class="btn btn-${a.class || 'secondary'}" title="${esc(a.title)}" href="${BASE}${a.href.replace('{id}', row.id)}" target="${a.target || '_self'}"><i class="${a.icon}"></i></a>`;
    });
    h += `<button class="btn btn-warning btn-edit" data-id="${row.id}" title="Ubah"><i class="fas fa-edit"></i></button>`;
    h += `<button class="btn btn-danger btn-del" data-id="${row.id}" title="Hapus"><i class="fas fa-trash"></i></button></div>`;
    return h;
  }

  async function save(e) {
    e.preventDefault();
    clearErrors();
    const btn = $('#btnSave').prop('disabled', true);
    const r = await App.api(URL_('/save'), { method: 'POST', body: new FormData($('#frm')[0]) });
    btn.prop('disabled', false);

    if (r.ok) {
      modal.modal('hide');
      App.toast('success', r.json.message);
      return load();
    }
    $('#frmErr').removeClass('d-none').text(r.json.message || 'Gagal menyimpan.');
    Object.entries(r.json.errors || {}).forEach(([k, msg]) => {
      $('#f_' + k).addClass('is-invalid');
      $(`[data-err="${k}"]`).text(msg);
    });
  }

  async function del(id) {
    const c = await Swal.fire({ title: 'Hapus data ini?', text: 'Tindakan tidak dapat dibatalkan.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal', confirmButtonColor: '#dc3545' });
    if (!c.isConfirmed) return;
    const r = await App.api(URL_('/delete/' + id), { method: 'POST' });
    App.toast(r.ok ? 'success' : 'error', r.json.message);
    if (r.ok) load();
  }

  $(function () {
    modal = $('#modalForm');
    const cols = [{ title: 'No', orderable: false, className: 'text-center', width: '40px', data: null, render: (d, t, r, m) => m.row + 1 }];
    cfg.columns.forEach((c) => cols.push({
      data: c.data, title: c.title, defaultContent: '',
      render: (d, type, row) => {
        if (type !== 'display') return d ?? '';
        if (!c.render) return esc(d);
        const [rn, arg] = c.render.split(':');
        return R[rn] ? R[rn](d, row, arg) : esc(d);
      },
    }));
    cols.push({ title: 'Aksi', orderable: false, className: 'text-center text-nowrap', data: null, render: (d, t, r) => actionButtons(r) });

    table = $('#tbl').DataTable({
      data: [], columns: cols, responsive: false, scrollX: true, order: [],
      language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    });

    $('#fields').on('change', 'input[type=file]', function () {
      const f = this.files[0];
      if (f) $('#pv_' + this.name).html('<img src="' + URL.createObjectURL(f) + '" style="max-height:90px" class="img-thumbnail">');
    });
    $('#btnAdd').on('click', () => openForm());
    $('#frm').on('submit', save);
    $('#tbl').on('click', '.btn-edit', function () { openForm($(this).data('id')); });
    $('#tbl').on('click', '.btn-del', function () { del($(this).data('id')); });
    load();
  });
})(jQuery);

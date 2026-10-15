<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<form class="form-inline mb-3" method="get">
  <select name="bulan" class="form-control mr-2"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= $i == $b ? 'selected' : '' ?>><?= bulan_nama($i) ?></option><?php endfor; ?></select>
  <input type="number" name="tahun" value="<?= $t ?>" class="form-control mr-2" style="width:100px">
  <button class="btn btn-primary">Tampilkan</button>
</form>

<div class="card">
  <div class="card-header"><h3 class="card-title">Perkembangan Korcam — <?= bulan_nama($b) ?> <?= $t ?></h3></div>
  <div class="card-body table-responsive p-0">
    <table class="table table-bordered table-sm table-hover mb-0">
      <thead class="thead-light"><tr><th>Korcam</th><th>Status</th><?= view('rekap/_head') ?><th>Rincian</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): $snap = $r['data'] ?? null; ?>
        <tr>
          <td><?= esc($r['nama']) ?></td>
          <td><?php if ($r['terkirim']): ?><span class="badge badge-success">Terkirim</span><br><small><?= esc($r['tgl_kirim']) ?></small><br><small><?= $snap['tpq_terkirim'] ?>/<?= $snap['tpq_total'] ?> TPQ</small><?php else: ?><span class="badge badge-secondary">Belum</span><?php endif; ?></td>
          <?= view('rekap/_cells', ['d' => $snap['total'] ?? null]) ?>
          <td class="text-center"><?php if ($snap): ?><button class="btn btn-xs btn-info btn-detail" data-nama="<?= esc($r['nama'], 'attr') ?>" data-json="<?= esc(json_encode($snap['tpq']), 'attr') ?>"><i class="fas fa-list"></i> TPQ</button><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="14" class="text-center text-muted">Belum ada Korcam pada korcab ini.</td></tr><?php endif; ?>
      </tbody>
      <tfoot class="bg-light font-weight-bold"><tr><td colspan="2">TOTAL KORCAB</td><?= view('rekap/_cells', ['d' => $total]) ?><td></td></tr></tfoot>
    </table>
  </div>
</div>

<div class="modal fade" id="mDetail" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title" id="dTitle"></h5><button class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body table-responsive p-0"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>TPQ</th><?= view('rekap/_head') ?></tr></thead><tbody id="dBody"></tbody></table></div>
</div></div></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const J = <?= json_encode(array_map('strval', array_column(jilid_all(), 'kode'))) ?>;
const rp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
$('.btn-detail').on('click', function () {
  const list = JSON.parse($(this).attr('data-json'));
  $('#dTitle').text('Rincian TPQ — Korcam ' + $(this).data('nama'));
  $('#dBody').html(list.map((x) => {
    const d = x.data, per = J.filter((j) => d.per_jilid && d.per_jilid[j]).map((j) => j + ':' + d.per_jilid[j]).join(' · ') || '-';
    return `<tr><td>${App.esc(x.nama)}</td><td class="text-right">${d.siswa_aktif}</td><td class="text-right">${d.laki}</td><td class="text-right">${d.perempuan}</td><td class="text-right">${d.guru}</td><td class="text-right">${d.hadir_persen}%</td><td class="text-right">${d.naik_jilid}</td><td class="text-right">${d.lulus}</td><td><small>${per}</small></td><td class="text-right">${rp(d.spp_masuk)}</td><td class="text-right">${rp(d.infaq_masuk)}</td><td class="text-right">${d.menunggak}</td></tr>`;
  }).join(''));
  $('#mDetail').modal('show');
});
</script>
<?= $this->endSection() ?>

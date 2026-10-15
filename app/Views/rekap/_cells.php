<?php /** @var array|null $d */ ?>
<?php if (!$d): ?>
  <td colspan="11" class="text-center text-muted"><em>Belum mengirim laporan</em></td>
<?php else: ?>
  <td class="text-right"><?= $d['siswa_aktif'] ?></td>
  <td class="text-right"><?= $d['laki'] ?></td>
  <td class="text-right"><?= $d['perempuan'] ?></td>
  <td class="text-right"><?= $d['guru'] ?></td>
  <td class="text-right"><?= $d['hadir_persen'] ?>%</td>
  <td class="text-right"><?= $d['naik_jilid'] ?></td>
  <td class="text-right"><?= $d['lulus'] ?></td>
  <td><small><?= esc(jilid_ringkas($d['per_jilid'] ?? [])) ?></small></td>
  <td class="text-right"><?= rupiah($d['spp_masuk']) ?></td>
  <td class="text-right"><?= rupiah($d['infaq_masuk']) ?></td>
  <td class="text-right"><?= $d['menunggak'] ?></td>
<?php endif; ?>

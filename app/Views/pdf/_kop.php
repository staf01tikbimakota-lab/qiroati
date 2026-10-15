<?php
helper('app');
$logoQ = logo_data_uri('qiroati');                        // kiri: logo Qiroati
$logoTpq = logo_data_uri('tpq', $tpq['logo'] ?? null);   // kanan: logo TPQ (dinamis per TPQ)
?>
<table class="kop"><tr>
  <td style="width:80px;text-align:left"><?php if ($logoQ): ?><img src="<?= $logoQ ?>" alt=""><?php endif; ?></td>
  <td class="c">
    <h3><?= esc(strtoupper($tpq['nama'] ?? '')) ?></h3>
    <p><?= esc($tpq['alamat'] ?? '') ?><?= !empty($tpq['desa']) ? ', ' . esc($tpq['desa']) : '' ?><?= !empty($tpq['telp']) ? ' — Telp. ' . esc($tpq['telp']) : '' ?></p>
    <p>Korcam <?= esc($tpq['korcam_nama'] ?? '') ?> · Metode Qiroati</p>
  </td>
  <td style="width:80px;text-align:right"><?php if ($logoTpq): ?><img src="<?= $logoTpq ?>" alt=""><?php endif; ?></td>
</tr></table>

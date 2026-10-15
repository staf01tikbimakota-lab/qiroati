<?php

function bulan_nama(int $n): string
{
    $b = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $b[$n] ?? '';
}

function rupiah($n): string
{
    return 'Rp ' . number_format((float) $n, 0, ',', '.');
}

function tgl_id($d): string
{
    if (!$d) return '';
    $t = strtotime($d);
    return date('j', $t) . ' ' . bulan_nama((int) date('n', $t)) . ' ' . date('Y', $t);
}

function terbilang($x): string
{
    $x = abs((int) $x);
    $a = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    if ($x < 12) return $a[$x];
    if ($x < 20) return terbilang($x - 10) . ' belas';
    if ($x < 100) return terbilang(intdiv($x, 10)) . ' puluh ' . terbilang($x % 10);
    if ($x < 200) return 'seratus ' . terbilang($x - 100);
    if ($x < 1000) return terbilang(intdiv($x, 100)) . ' ratus ' . terbilang($x % 100);
    if ($x < 2000) return 'seribu ' . terbilang($x - 1000);
    if ($x < 1000000) return terbilang(intdiv($x, 1000)) . ' ribu ' . terbilang($x % 1000);
    if ($x < 1000000000) return terbilang(intdiv($x, 1000000)) . ' juta ' . terbilang($x % 1000000);
    return terbilang(intdiv($x, 1000000000)) . ' milyar ' . terbilang($x % 1000000000);
}

/** Seluruh jilid dari tabel `jilid`, urut sesuai kurikulum (di-cache per request). */
function jilid_all(): array
{
    static $rows = null;
    if ($rows === null) {
        $rows = db_connect()->table('jilid')->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
    }
    return $rows;
}

/** [kode => nama] urut kurikulum, untuk dropdown & validasi. */
function jilid_options(): array
{
    $o = [];
    foreach (jilid_all() as $r) {
        $o[$r['kode']] = $r['nama'];
    }
    return $o;
}

/** Kode jilid sesudah $j; null bila $j tahap terakhir (artinya lulus). */
function jilid_next(string $j): ?string
{
    $k = array_map('strval', array_column(jilid_all(), 'kode'));
    $i = array_search((string) $j, $k, true);
    return ($i === false || !isset($k[$i + 1])) ? null : $k[$i + 1];
}

function jilid_last_kode(): ?string
{
    $k = array_column(jilid_all(), 'kode');
    return $k ? (string) end($k) : null;
}

/** "1A:3 · 1B:2 · Ghorib:1" — urut kurikulum; kode yang sudah tidak ada ditaruh di belakang. */
function jilid_ringkas(array $per): string
{
    $o = [];
    $seen = [];
    foreach (jilid_all() as $r) {
        $k = $r['kode'];
        if (!empty($per[$k])) {
            $o[] = $k . ':' . $per[$k];
            $seen[(string) $k] = true;
        }
    }
    foreach ($per as $k => $n) {
        if ($n && !isset($seen[(string) $k])) {
            $o[] = $k . ':' . $n;
        }
    }
    return $o ? implode(' · ', $o) : '-';
}

/** Logo untuk kop PDF sebagai data-URI (aman untuk dompdf). $type: 'tpq' | 'qiroati'. Kosong jika file tidak ada. */
function logo_data_uri(string $type, ?string $file = null): string
{
    $path = $type === 'qiroati'
        ? FCPATH . 'assets/img/logo-qiroati.jpg'
        : ($file ? upload_dir('logo') . '/' . basename($file) : '');
    if (!$path || !is_file($path)) {
        return '';
    }
    $mime = strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'png' ? 'image/png' : 'image/jpeg';
    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}

/** CSS @font-face Roboto dari writable/fonts (hanya varian yang file-nya ada). */
function pdf_font_face(): string
{
    $variants = [
        ['Roboto-Regular.ttf', 'normal', 'normal'],
        ['Roboto-Bold.ttf', 'bold', 'normal'],
        ['Roboto-Italic.ttf', 'normal', 'italic'],
        ['Roboto-BoldItalic.ttf', 'bold', 'italic'],
    ];
    $css = '';
    foreach ($variants as [$f, $w, $s]) {
        $p = WRITEPATH . 'fonts/' . $f;
        if (is_file($p)) {
            $url = 'file:///' . ltrim(str_replace('\\', '/', realpath($p)), '/');
            $css .= "@font-face{font-family:'Roboto';font-weight:{$w};font-style:{$s};src:url('{$url}') format('truetype');}\n";
        }
    }
    return $css;
}

/** Folder penyimpanan upload di writable/uploads (dibuat otomatis). $kind: siswa | guru | logo */
function upload_dir(string $kind): string
{
    $map = ['siswa' => 'foto/siswa', 'guru' => 'foto/guru', 'logo' => 'logo'];
    $dir = WRITEPATH . 'uploads/' . ($map[$kind] ?? 'lain');
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

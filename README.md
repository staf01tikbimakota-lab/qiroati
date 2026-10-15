# Aplikasi TPQ — Metode Qiroati (CodeIgniter 4 + MySQL + AdminLTE)

## Instalasi
1. Buat project CI4 baru: `composer create-project codeigniter4/appstarter tpq`
2. Di folder project: `composer require dompdf/dompdf`
3. Salin isi folder ini ke project (timpa `app/Config/Routes.php`, tambahkan `app/*`, `public/assets/*`, `database/*`).
4. Daftarkan filter di `app/Config/Filters.php` → pada `$aliases` tambahkan:
   `'auth' => \App\Filters\AuthFilter::class,`
5. Salin `env` → `.env` lalu atur:
   ```
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   app.indexPage = ''
   database.default.hostname = localhost
   database.default.database = tpq_qiroati
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```
6. Impor `database/tpq_qiroati.sql` ke MySQL, lalu `php spark db:seed InitSeeder`
7. Jalankan `php spark serve` → http://localhost:8080

## Akun contoh (ganti setelah login!)
| Level | Username | Password |
|---|---|---|
| Super Admin | admin | admin123 |
| Korcab | korcab | korcab123 |
| Korcam | korcam | korcam123 |
| Admin TPQ | tpq | tpq123 |

## Alur
- **Super Admin**: kelola Korcab → Korcam → TPQ dan akun user (tiap user level TPQ/Korcam/Korcab terikat ke unitnya).
- **Admin TPQ**: Siswa, Guru, Kelas, Jadwal, Absensi (input massal per kelas), Progress Jilid (lulus jilid otomatis menaikkan jilid siswa), SPP/Infaq (+ kwitansi PDF), Laporan Bulanan (PDF + tombol **Kirim ke Korcam**).
- **Korcam**: melihat rekap perkembangan tiap TPQ di korcamnya (hanya TPQ yang sudah mengirim), lalu **Kirim ke Korcab**.
- **Korcab**: melihat rekap tiap Korcam di korcabnya beserta rincian TPQ.
- Data yang dikirim disimpan sebagai *snapshot* (`kirim_laporan.data_json`), jadi rekap Korcam/Korcab tidak berubah walau data TPQ diedit kemudian. TPQ boleh kirim ulang selama Korcam belum meneruskan ke Korcab.

## Catatan
- CRUD berjalan lewat `public/assets/js/crud.js` (DataTables + modal + fetch JSON). Menambah modul baru cukup membuat controller turunan `CrudController` + satu baris route.
- Untuk CSRF: aktifkan `'csrf'` di `Filters.php` (`$globals['before']`) dan set `public bool $regenerate = false;` di `app/Config/Security.php`; JS sudah mengirim header `X-CSRF-TOKEN`.
- Template memakai AdminLTE 3 (Bootstrap 4) via CDN; ganti ke file lokal bila server tanpa internet.

## Logo & font PDF
- **Logo TPQ (kanan kop)**: dinamis per TPQ. Admin TPQ upload lewat menu *Profil & Logo TPQ*.
- **Logo Qiroati (kiri kop)**: letakkan `public/assets/img/logo-qiroati.png`.
- **Font Roboto**: letakkan `Roboto-Regular.ttf`, `Roboto-Bold.ttf`, `Roboto-Italic.ttf` di `writable/fonts/`. Tanpa file ini PDF memakai DejaVu Sans.
- Database lama: jalankan `database/upgrade_logo.sql` (kolom `tpq.logo`) dan `database/upgrade_foto.sql` (kolom `siswa.foto`, `guru.foto`).

## Upload foto & logo
- Foto siswa & guru diunggah dari form Data Siswa / Data Guru (PNG/JPG, maks. 2 MB; centang "Hapus foto" saat ubah untuk menghapus).
- Lokasi penyimpanan (di luar folder publik): `writable/uploads/foto/siswa/`, `writable/uploads/foto/guru/`, `writable/uploads/logo/`. Folder dibuat otomatis; pastikan `writable/` dapat ditulis.
- Berkas ditampilkan lewat route `berkas/{siswa|guru|logo}/{nama}` yang mengecek login dan kepemilikan TPQ. Nama berkas diacak (`t{id_tpq}_{acak}.png|jpg`).
- Berkas lama otomatis dihapus saat foto diganti/dihapus atau datanya dihapus.
- Jika sebelumnya sudah mengunggah logo ke `public/uploads/logo`, unggah ulang lewat menu Profil & Logo TPQ.

## Penempatan Kelas, Jilid & Halaman
- Master **Data Siswa** hanya berisi biodata. Kelas, jilid, dan halaman diatur di menu **Penempatan Kelas & Jilid** (riwayat per siswa; satu penempatan aktif).
- Penempatan aktif otomatis disalin ke data siswa sehingga absensi, laporan, dan rekap tetap berjalan. Kolom Kelas/Jilid/Hal di tabel siswa bertanda * bersifat baca-saja.
- Input *Progress Jilid* ikut memperbarui halaman penempatan aktif; "Lulus jilid" menutup penempatan lama dan membuat penempatan baru di jilid berikutnya (kelas sama).
- Data kelas tetap di menu **Kelas**; daftar jilid (Jilid 1–6, Ghorib, Tajwid) bersifat tetap sesuai kurikulum Qiroati.
- Database lama: jalankan `database/upgrade_penempatan.sql`.

## Master Jilid
- Jilid disimpan di tabel `jilid` (kode, nama, urutan): 1A, 1B, 2A, 2B, … 5A, 5B, Juz 27, 6, Ghorib, Tajwid, Finishing. Super Admin dapat menambah/mengubah/menghapus lewat menu **Jilid Qiroati**.
- **Urutan** menentukan alur "naik jilid". Jilid dengan urutan terbesar = tahap akhir; lulus di tahap ini dihitung sebagai lulus TPQ.
- Jilid yang sudah dipakai siswa/kelas/progress tidak bisa dihapus; mengubah *kode* otomatis ikut mengubah data terkait.
- Database lama: jalankan `database/upgrade_jilid.sql` (setelah upgrade lain). Jilid lama 1–5 dipetakan ke 1A–5A.

## Kelas dengan banyak jilid
- Satu kelas dapat berisi beberapa atau semua jilid. Di menu **Kelas**, pilih jilid yang ada dalam kelas (multi-pilih); **kosong = semua jilid**.
- Jilid tiap siswa tetap dicatat per siswa di menu **Penempatan**. Jika kelas dibatasi ke jilid tertentu, penempatan di luar daftar itu ditolak.
- Laporan PDF kelas siswa menampilkan sebaran jilid di setiap kelas.
- Database lama: jalankan `database/upgrade_kelas_jilid.sql` (setelah `upgrade_jilid.sql`).

## Ujian kenaikan jilid
- Menu **Ujian & Kelulusan**: daftarkan santri sebagai peserta (jilid yang diujikan = jilid santri saat ini), isi tanggal, lalu hasil: Menunggu / Lulus / Tidak lulus.
- Hasil **Lulus** otomatis: mencatat progress "lulus jilid", menaikkan santri ke jilid di atasnya (urutan tabel `jilid`), menutup penempatan lama dan membuat penempatan baru. Lulus di jilid terakhir = santri berstatus lulus TPQ. Hasil lulus yang sudah diproses tidak dapat diubah/dihapus.
- PDF **Daftar Peserta Ujian** (nama, jilid, tgl ujian) dan **Pengumuman Kelulusan** (hanya yang lulus) dapat dicetak per rentang tanggal.
- Database lama: jalankan `database/upgrade_ujian.sql`.

CREATE DATABASE IF NOT EXISTS tpq_qiroati CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tpq_qiroati;

CREATE TABLE korcab (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL, wilayah VARCHAR(100), telp VARCHAR(30), alamat TEXT
) ENGINE=InnoDB;

CREATE TABLE korcam (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  korcab_id INT UNSIGNED NOT NULL,
  nama VARCHAR(100) NOT NULL, kecamatan VARCHAR(100), telp VARCHAR(30),
  FOREIGN KEY (korcab_id) REFERENCES korcab(id)
) ENGINE=InnoDB;

CREATE TABLE tpq (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  korcam_id INT UNSIGNED NOT NULL,
  nama VARCHAR(120) NOT NULL, alamat TEXT, desa VARCHAR(100), kepala VARCHAR(100), telp VARCHAR(30),
  tarif_spp INT UNSIGNED NOT NULL DEFAULT 0,
  logo VARCHAR(100) NULL,
  FOREIGN KEY (korcam_id) REFERENCES korcam(id)
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','korcab','korcam','tpq') NOT NULL,
  korcab_id INT UNSIGNED NULL, korcam_id INT UNSIGNED NULL, tpq_id INT UNSIGNED NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (korcab_id) REFERENCES korcab(id),
  FOREIGN KEY (korcam_id) REFERENCES korcam(id),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id)
) ENGINE=InnoDB;

-- Master jilid Qiroati; urutan = alur naik jilid (urutan terbesar = tahap akhir)
CREATE TABLE jilid (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(50) NOT NULL,
  urutan SMALLINT UNSIGNED NOT NULL,
  keterangan VARCHAR(150) NULL,
  KEY idx_urutan (urutan)
) ENGINE=InnoDB;

CREATE TABLE guru (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL,
  nama VARCHAR(100) NOT NULL, jk ENUM('L','P') NOT NULL, telp VARCHAR(30), alamat TEXT,
  sertifikat ENUM('Sudah','Belum') NOT NULL DEFAULT 'Belum',
  tgl_masuk DATE NULL, status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  foto VARCHAR(100) NULL,
  FOREIGN KEY (tpq_id) REFERENCES tpq(id)
) ENGINE=InnoDB;

CREATE TABLE kelas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL,
  nama VARCHAR(60) NOT NULL, guru_id INT UNSIGNED NULL, tahun_ajaran VARCHAR(15),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (guru_id) REFERENCES guru(id)
) ENGINE=InnoDB;

-- Jilid yang diajarkan dalam satu kelas (boleh beberapa/semua). Tanpa baris = semua jilid.
CREATE TABLE kelas_jilid (
  kelas_id INT UNSIGNED NOT NULL,
  jilid VARCHAR(20) NOT NULL,
  PRIMARY KEY (kelas_id, jilid),
  FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE siswa (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL,
  nis VARCHAR(30) NOT NULL, nama VARCHAR(100) NOT NULL, jk ENUM('L','P') NOT NULL,
  tempat_lahir VARCHAR(60), tgl_lahir DATE NULL, alamat TEXT, nama_wali VARCHAR(100), telp_wali VARCHAR(30),
  kelas_id INT UNSIGNED NULL,
  jilid VARCHAR(20) NOT NULL DEFAULT '1A', halaman TINYINT UNSIGNED NOT NULL DEFAULT 1,
  tgl_masuk DATE NULL, status ENUM('aktif','lulus','keluar') NOT NULL DEFAULT 'aktif',
  foto VARCHAR(100) NULL,
  UNIQUE KEY uq_nis (tpq_id, nis),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (kelas_id) REFERENCES kelas(id),
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Riwayat penempatan kelas/jilid/halaman siswa; yang aktif disinkronkan ke siswa.kelas_id/jilid/halaman
CREATE TABLE penempatan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL, kelas_id INT UNSIGNED NOT NULL,
  jilid VARCHAR(20) NOT NULL DEFAULT '1A', halaman TINYINT UNSIGNED NOT NULL DEFAULT 1,
  tgl_mulai DATE NOT NULL, tgl_selesai DATE NULL, aktif TINYINT(1) NOT NULL DEFAULT 1, catatan VARCHAR(200),
  KEY idx_siswa_aktif (siswa_id, aktif),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (kelas_id) REFERENCES kelas(id),
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jadwal (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, kelas_id INT UNSIGNED NOT NULL,
  hari VARCHAR(10) NOT NULL, jam_mulai TIME NOT NULL, jam_selesai TIME NOT NULL,
  guru_id INT UNSIGNED NULL, keterangan VARCHAR(150),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (kelas_id) REFERENCES kelas(id),
  FOREIGN KEY (guru_id) REFERENCES guru(id)
) ENGINE=InnoDB;

CREATE TABLE absensi (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL,
  tanggal DATE NOT NULL, status ENUM('H','I','S','A') NOT NULL DEFAULT 'H', keterangan VARCHAR(150),
  UNIQUE KEY uq_absen (siswa_id, tanggal),
  KEY idx_tpq_tgl (tpq_id, tanggal),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE progress (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL, guru_id INT UNSIGNED NULL,
  tanggal DATE NOT NULL, jilid VARCHAR(20) NOT NULL, halaman TINYINT UNSIGNED NOT NULL DEFAULT 1,
  nilai VARCHAR(3) NULL, status ENUM('lanjut','ulang','lulus_jilid') NOT NULL DEFAULT 'lanjut', catatan TEXT,
  KEY idx_tpq_tgl (tpq_id, tanggal),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id),
  FOREIGN KEY (guru_id) REFERENCES guru(id),
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL,
  no_kwitansi VARCHAR(40) NOT NULL, jenis ENUM('SPP','Infaq') NOT NULL,
  bulan TINYINT UNSIGNED NOT NULL, tahun SMALLINT UNSIGNED NOT NULL,
  jumlah INT UNSIGNED NOT NULL, tgl_bayar DATE NOT NULL, keterangan VARCHAR(150),
  UNIQUE KEY uq_kw (tpq_id, no_kwitansi),
  KEY idx_tpq_thn (tpq_id, tahun, bulan),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id)
) ENGINE=InnoDB;

-- Peserta ujian kenaikan jilid; hasil 'lulus' otomatis menaikkan jilid (diproses = sudah dijalankan)
CREATE TABLE ujian (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL,
  jilid VARCHAR(20) NOT NULL, tgl_ujian DATE NOT NULL,
  hasil ENUM('menunggu','lulus','tidak_lulus') NOT NULL DEFAULT 'menunggu',
  nilai VARCHAR(3) NULL, catatan VARCHAR(200) NULL,
  diproses TINYINT(1) NOT NULL DEFAULT 0,
  KEY idx_tpq_tgl (tpq_id, tgl_ujian),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Pengiriman laporan: level 'tpq' (TPQ -> Korcam) dan 'korcam' (Korcam -> Korcab).
-- data_json = snapshot rekap saat dikirim.
CREATE TABLE kirim_laporan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  level ENUM('tpq','korcam') NOT NULL, ref_id INT UNSIGNED NOT NULL,
  bulan TINYINT UNSIGNED NOT NULL, tahun SMALLINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'terkirim', tgl_kirim DATETIME NOT NULL,
  data_json LONGTEXT NOT NULL,
  UNIQUE KEY uq_kirim (level, ref_id, bulan, tahun)
) ENGINE=InnoDB;

INSERT INTO `jilid` (`id`, `kode`, `nama`, `urutan`, `keterangan`) VALUES
(1, '0A', 'Jilid PraTK A', 1, NULL),
(2, '0B', 'Jilid PraTK B', 2, NULL),
(3, '0C', 'Jilid PraTK C', 3, NULL),
(4, '1A', 'Jilid 1A', 4, NULL),
(5, '1B', 'Jilid 1B', 5, NULL),
(6, '2A', 'Jilid 2A', 6, NULL),
(7, '2B', 'Jilid 2B', 7, NULL),
(8, '3A', 'Jilid 3A', 8, NULL),
(9, '3B', 'Jilid 3B', 9, NULL),
(10, '4A', 'Jilid 4A', 10, NULL),
(11, '4B', 'Jilid 4B', 11, NULL),
(12, '5A', 'Jilid 5A', 12, NULL),
(13, '5B', 'Jilid 5B', 13, NULL),
(14, 'Juz 27', 'Juz 27', 14, NULL),
(15, '6', 'Jilid 6', 15, NULL),
(16, 'Ghorib', 'Ghorib', 16, NULL),
(17, 'Tajwid', 'Tajwid', 17, NULL),
(18, 'Finishing', 'Finishing', 18, NULL);

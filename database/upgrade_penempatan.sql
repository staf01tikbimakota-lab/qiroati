-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum fitur Penempatan ada.
USE tpq_qiroati;
CREATE TABLE penempatan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tpq_id INT UNSIGNED NOT NULL, siswa_id INT UNSIGNED NOT NULL, kelas_id INT UNSIGNED NOT NULL,
  jilid VARCHAR(10) NOT NULL DEFAULT '1', halaman TINYINT UNSIGNED NOT NULL DEFAULT 1,
  tgl_mulai DATE NOT NULL, tgl_selesai DATE NULL, aktif TINYINT(1) NOT NULL DEFAULT 1, catatan VARCHAR(200),
  KEY idx_siswa_aktif (siswa_id, aktif),
  FOREIGN KEY (tpq_id) REFERENCES tpq(id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (kelas_id) REFERENCES kelas(id)
) ENGINE=InnoDB;

-- Salin kelas/jilid/halaman siswa yang sekarang menjadi penempatan aktif pertama
INSERT INTO penempatan (tpq_id, siswa_id, kelas_id, jilid, halaman, tgl_mulai, aktif, catatan)
SELECT tpq_id, id, kelas_id, jilid, halaman, COALESCE(tgl_masuk, CURDATE()), 1, 'Data awal'
FROM siswa WHERE status = 'aktif' AND kelas_id IS NOT NULL;

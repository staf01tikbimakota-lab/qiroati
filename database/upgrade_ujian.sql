-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum fitur Ujian ada.
USE tpq_qiroati;

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

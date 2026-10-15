-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum fitur "banyak jilid per kelas".
-- Jalankan setelah upgrade_jilid.sql.
USE tpq_qiroati;

CREATE TABLE kelas_jilid (
  kelas_id INT UNSIGNED NOT NULL,
  jilid VARCHAR(20) NOT NULL,
  PRIMARY KEY (kelas_id, jilid),
  FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
  FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Jilid tunggal kelas yang lama dipindahkan ke tabel baru
INSERT INTO kelas_jilid (kelas_id, jilid) SELECT id, jilid FROM kelas WHERE jilid IS NOT NULL;

-- Hapus foreign key (nama otomatis) lalu kolom lama
SET @fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kelas' AND COLUMN_NAME = 'jilid' AND REFERENCED_TABLE_NAME = 'jilid' LIMIT 1);
SET @sql := IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE kelas DROP FOREIGN KEY `', @fk, '`'));
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
ALTER TABLE kelas DROP COLUMN jilid;

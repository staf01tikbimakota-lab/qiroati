-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum tabel jilid ada.
-- Jilid lama '1'..'5' dipetakan ke '1A'..'5A' (sesuaikan manual bila ada yang seharusnya 'B').
USE tpq_qiroati;

-- Master jilid Qiroati; urutan = alur naik jilid (urutan terbesar = tahap akhir)
CREATE TABLE jilid (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(50) NOT NULL,
  urutan SMALLINT UNSIGNED NOT NULL,
  keterangan VARCHAR(150) NULL,
  KEY idx_urutan (urutan)
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

ALTER TABLE kelas       MODIFY jilid VARCHAR(20) NULL;
ALTER TABLE siswa       MODIFY jilid VARCHAR(20) NOT NULL DEFAULT '1A';
ALTER TABLE progress    MODIFY jilid VARCHAR(20) NOT NULL;
ALTER TABLE penempatan  MODIFY jilid VARCHAR(20) NOT NULL DEFAULT '1A';

UPDATE kelas      SET jilid = CASE jilid WHEN '1' THEN '1A' WHEN '2' THEN '2A' WHEN '3' THEN '3A' WHEN '4' THEN '4A' WHEN '5' THEN '5A' ELSE jilid END;
UPDATE siswa      SET jilid = CASE jilid WHEN '1' THEN '1A' WHEN '2' THEN '2A' WHEN '3' THEN '3A' WHEN '4' THEN '4A' WHEN '5' THEN '5A' ELSE jilid END;
UPDATE progress   SET jilid = CASE jilid WHEN '1' THEN '1A' WHEN '2' THEN '2A' WHEN '3' THEN '3A' WHEN '4' THEN '4A' WHEN '5' THEN '5A' ELSE jilid END;
UPDATE penempatan SET jilid = CASE jilid WHEN '1' THEN '1A' WHEN '2' THEN '2A' WHEN '3' THEN '3A' WHEN '4' THEN '4A' WHEN '5' THEN '5A' ELSE jilid END;

ALTER TABLE kelas      ADD CONSTRAINT fk_kelas_jilid      FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE;
ALTER TABLE siswa      ADD CONSTRAINT fk_siswa_jilid      FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE;
ALTER TABLE progress   ADD CONSTRAINT fk_progress_jilid   FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE;
ALTER TABLE penempatan ADD CONSTRAINT fk_penempatan_jilid FOREIGN KEY (jilid) REFERENCES jilid(kode) ON UPDATE CASCADE;

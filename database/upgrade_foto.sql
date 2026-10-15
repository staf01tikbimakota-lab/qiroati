-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum fitur foto ada.
USE tpq_qiroati;
ALTER TABLE siswa ADD COLUMN foto VARCHAR(100) NULL AFTER status;
ALTER TABLE guru  ADD COLUMN foto VARCHAR(100) NULL AFTER status;
-- Kolom tpq.logo: lihat upgrade_logo.sql (lewati jika sudah dijalankan).

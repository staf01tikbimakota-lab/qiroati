-- Jalankan HANYA jika database sudah terlanjur diimpor sebelum fitur logo ada.
USE tpq_qiroati;
ALTER TABLE tpq ADD COLUMN logo VARCHAR(100) NULL AFTER tarif_spp;

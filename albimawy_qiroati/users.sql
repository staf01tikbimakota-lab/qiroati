/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `users`;
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role_level`, `wilayah_id`, `created_at`, `updated_at`) VALUES
	(1, 'Superadmin', 'qiroati_admin@qiroati.id', '$2y$10$i2rOZPH8Jw8oywi/furKJuokBt5rlH8NPL8n2k1zV9ydFOgAhjlMK', 0, 0, '2026-07-15 10:13:29', '2026-07-18 07:07:25'),
	(2, 'Korcab NTB', 'korcab_ntb@qiroati.id', '$2y$10$9VCgPqqe3fpVpnBZnoTNYuoP0uFahQ71iqszCsoH2andeRUPHM91m', 3, 1, '2026-07-15 11:08:31', '2026-07-15 11:12:40'),
	(3, 'Korcam Bima NTB', 'korcam_bima@qiroati.id', '$2y$10$M/6eDsRKMsTLKnDD5HytReaU333DEnTPgrGuWXFqhjvD5lcOGpzP6', 4, 1, '2026-07-15 11:09:14', '2026-07-15 11:12:54'),
	(4, 'TPQ001 Bima', 'tpq01_bima@qiroati.id', '$2y$10$Ei3huEzABTj5vPrlVqgul.XjdbUEA1H8gQPTztOw5tAIBZnMsNdoS', 5, 1, '2026-07-15 11:09:48', '2026-07-28 09:57:16'),
	(5, 'TPQ002 Bima', 'tpq02_bima@qiroati.id', '$2y$10$M1ABnWzGdDihWOluqQ6nM.tUO5GP.yi1lKcezvIMMkvXiY7O66QdC', 5, 2, '2026-07-15 11:10:51', '2026-07-15 11:10:51'),
	(6, 'Korcam Ampenan NTB', 'korcam_ampenan@qiroati.id', '$2y$10$6UhSPpKIRW9zDRwk91JdhuDMuj.aeyPS1Plv/ZiPIMVnxHT5NKaDS', 4, 2, '2026-07-15 11:12:19', '2026-07-15 11:12:19'),
	(7, 'TPQ001 Ampenan NTB', 'tpq001_ampenan@qiroati.id', '$2y$10$KciYstxGqG50sTFtHpuURepvAkKGJ7yRYZtdvfivV6QQYkpkS3YbC', 5, 1, '2026-07-15 11:14:24', '2026-07-15 11:14:24');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

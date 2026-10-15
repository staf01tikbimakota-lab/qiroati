/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `user`;
INSERT INTO `user` (`id`, `level_id`, `wilayah_id`, `username`, `password`, `nama`, `telp`, `email`, `aktif`, `created_at`, `updated_at`) VALUES
	(1, 0, 0, 'admin', '$2y$10$p6p428KRBGapvhHoF0jkiepvedy6imMlV7kIFd2sIfa92Mkq1oyFi', 'Super Admin', '085239851234', 'abdulgafar@bimakota.go.id', 'Y', '2025-02-18 02:50:40', '2026-08-20 22:52:44'),
	(2, 2, 1, 'korcab01', '$2y$10$otTrvo.3500MV5JwDTX3EOrop4tqEU2OtSEIIXq1VN3tqt37CfWP6', 'Admin Korcab 01', '085239851234', 'ihya@gmail.com', 'Y', '2025-02-18 02:50:17', '2026-08-22 00:42:28'),
	(3, 4, 2, 'tpq01037', '$2y$10$hkYB2TnCYn5qHyV9DQVp9u1sZmrQF7SzBih5LeXnRUvUNH96NzfXC', 'Bidayatul Islam', '', 'inaya@gmail.com', 'Y', '2026-04-18 14:50:17', '2026-08-24 08:09:04'),
	(4, 4, 3, 'tpq01001', '$2y$10$HuOicRm73ifxSVofzakNOOXeO6pPOE7YoREtlWvogTLhfuKdzivLq', 'Bidayatul Hidayah', '-', 'tpq01001@qiroati.id', 'Y', '2026-08-04 03:05:19', '2026-08-24 08:08:44'),
	(5, 3, 1, 'korcam0101', '$2y$10$hxMrb8.X.AmYoc1gG6uCPunsTv56gV3NBz0cszkrHUsYCWDRtRIta', 'Admin Korcam 01', '', 'korcam0101@qiroati.id', 'Y', '2026-08-20 12:31:35', '2026-08-22 00:43:27'),
	(6, 1, 1, 'korwil01', '$2y$10$Aw8QqJUHIyxsVoj1H2JZd.YBH5hTlJNUex3ZZQ3tQm5gn8LrpsPm6', 'Admin Korwil 01', NULL, 'korwil01@qiroati.id', 'Y', '2026-08-20 11:33:11', '2026-08-24 06:02:01'),
	(7, 2, 2, 'korcab02', '$2y$10$TvzEGP4GGZp3uF5Hz4pWfuqWav6rNRl8iJn9owwnc118FGxNj22Bu', 'Admin Korcab 02', '-', 'korcab02@qiroati.id', 'Y', '2026-08-21 10:15:42', '2026-08-24 06:01:05'),
	(8, 3, 9, 'korcambima', '$2y$10$bfHSLhnRunwZfniXzZ/Dz.DBMAOCSBZjYwRCugwBCY2ZQGPyipY0G', 'Korcam Bima', '-', 'korcambima@qiroati.id', 'Y', '2026-08-21 10:17:32', '2026-08-24 06:01:23'),
	(9, 4, 1, 'igee', '$2y$10$QZZieYPBTVepkG4/TYIDy.deqJjVkER1xrKeiu85Gu.edBB/kPuUq', 'Siti Rosmiyati', '085239851234', 'igee@qiroati.id', 'Y', '2026-08-21 10:18:44', '2026-09-07 03:37:42'),
	(10, 3, 8, 'korcamdompu', '$2y$10$HOgTo3PqsEAWAcqe5Xl3SOsdp43225hOWVwtdQCGF/5RxgBZNABeq', 'Admin Korcam Dompu', '-', 'korcamdompu@qiroati.id', 'Y', '2026-08-22 00:39:05', '2026-08-24 06:01:23'),
	(11, 4, 1, 'tpq02009', '$2y$10$ZXczpb3JsKCUFiCw.KIr3evNa./SViIVBplz/BMEZLHxEuXjzVuvC', 'TPQ 02009', '-', 'tpq02009@qiroati.id', 'Y', '2026-08-22 00:46:11', '2026-08-24 06:01:23');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

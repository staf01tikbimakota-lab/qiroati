/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `users2`;
INSERT INTO `users2` (`id`, `nama`, `username`, `email`, `password`, `role`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Administrator', 'admin', 'admin@gmail.com', '$2y$10$OJf7pHUdoHZg2VRRTSocr.JmTA3PYl5NaFIq2vJZcfrI40a7DXU4C', 'superadmin', 0, 1, '2026-05-12 03:28:55', '2026-05-12 03:28:55'),
	(2, 'Korcab NTB', 'korcab23', 'korcab-ntb@gmail.com', '$2y$10$S.LZe3EBSfvGp5v/ffIe0uUnLJNBRam9p9tjsq.wXkZkXcetral6C', 'korcab', 23, 1, '2026-05-11 02:55:31', '2026-05-12 07:10:43'),
	(3, 'Korcam Bima', 'korcam9', 'korcam-bima@gmail.com', '$2y$10$0S3nidI.TLqcgfd2JIYYpeb2QuL5OXBgdvbudcN2I786mSLIi8hRK', 'korcam', 9, 1, '2026-05-11 02:55:50', '2026-05-13 11:22:03'),
	(4, 'TPQ 001', 'tpq001', 'tpq-satu@gmail.com', '$2y$10$ymzWn5pnaCa4/vR0w4Hi7uXhAfyh2wWJWHz0Uw5Mx/JmPY25EZsJK', 'lembaga', 1, 1, '2026-05-11 02:56:07', '2026-05-17 03:28:56'),
	(5, 'Korcam Ampenan', 'korcam1', 'korcam-ampenan@gmail.com', '$2y$10$9C6W1W3z5.6mI9WbXHbKreXZizYBbXKJ9NZ96c2dJYf3R0ycUO64O', 'korcam', 1, 1, '2026-05-13 11:16:55', '2026-05-13 11:16:55'),
	(6, 'TPQ 002', 'tpq002', 'tpq-dua@gmail.com', '$2y$10$2RDcuBhiNIH4Moq46tSoYu3k/9.PmRyrHKmOp21AKYMory4fdOve.', 'lembaga', 2, 1, '2026-05-17 03:30:03', '2026-05-17 03:30:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `kelas`;
INSERT INTO `kelas` (`id`, `nama`) VALUES
	(1, 'Pra TK A'),
	(2, 'Pra TK B'),
	(3, 'Pra TK C'),
	(4, '1 A'),
	(5, '1 B'),
	(6, '1 C'),
	(7, '2 A'),
	(8, '2 B'),
	(9, '3 A'),
	(10, '3 B'),
	(11, '4 A'),
	(12, '4 B'),
	(13, '5 A'),
	(14, '5 B'),
	(15, 'Juz 27'),
	(16, '6'),
	(17, 'Quran'),
	(18, 'Gharib'),
	(20, 'Tajwid'),
	(21, 'Finishing'),
	(22, 'Khotaman'),
	(23, 'Pra PTPT'),
	(24, 'PTPT'),
	(25, 'Tahfidz'),
	(26, 'Penguji');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

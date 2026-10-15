/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `ketamanah`;
INSERT INTO `ketamanah` (`id`, `status`) VALUES
	(1, 'Guru'),
	(2, 'Kepala TPQ'),
	(3, 'Kepala PTPT'),
	(4, 'Kepala Tahfidz'),
	(5, 'Sekretaris Korcam'),
	(6, 'Buku Korcam'),
	(7, 'Metodologi Korcam'),
	(8, 'Pentashih Korcam'),
	(9, 'Sekretaris Cabang'),
	(10, 'Buku Cabang'),
	(11, 'Metodologi Cabang'),
	(12, 'Pentashih Cabang');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

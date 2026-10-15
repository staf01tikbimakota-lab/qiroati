/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `korcam`;
INSERT INTO `korcam` (`id`, `kode_induk`, `nama`, `alamat`, `telp`, `jml_guru`, `jml_santri`, `jml_tpq`, `korcab_id`) VALUES
	(1, '01.23.01', 'Ampenan', '', '', 0, 2311, 12, 1),
	(2, '01.23.02', 'Kopang', '', '', 0, 1216, 7, 1),
	(3, '01.23.03', 'Taliwang', '', '', 0, 538, 4, 2),
	(4, '01.23.04', 'Narmada', '', '', 0, 523, 4, 1),
	(5, '01.23.05', 'Tanjung', '', '', 0, 252, 3, 1),
	(6, '01.23.06', 'Alas', '', '', 0, 489, 4, 2),
	(7, '01.23.07', 'Praya', '', '', 0, 734, 4, 1),
	(8, '01.23.08', 'Dompu', '', '', 0, 0, 1, 2),
	(9, '01.23.09', 'Bima', '', '', 0, 0, 1, 2);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

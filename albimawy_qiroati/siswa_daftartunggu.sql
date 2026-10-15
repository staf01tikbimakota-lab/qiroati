/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `siswa_daftartunggu`;
INSERT INTO `siswa_daftartunggu` (`id`, `lembaga_id`, `program_id`, `nik`, `nama`, `wali`, `lp`, `alamat`, `telp`, `lahirtmp`, `lahirtgl`, `tgl_antri`, `created_at`, `updated_at`) VALUES
	(4, 2, 1, '-', 'Mohammad Alif Sabunijan', 'Mohammad Husni Thamrin', 'L', 'Jalan Khairil Anwar-Seganteng', '087865653555/087786948989', 'Mataram', '2016-01-25', '2026-07-02 17:08:17', '2026-07-02 17:09:44', '2026-07-02 17:09:44');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

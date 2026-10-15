/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELETE FROM `lembaga`;
INSERT INTO `lembaga` (`id`, `kode_induk`, `nama`, `alamat`, `kepala`, `telp`, `email`, `korcam_id`, `jml_guru`, `jml_santri`, `jml_antrian`, `jml_lulus`, `uang_bulanan`, `logo`) VALUES
	(1, '01.23.08.068', 'Riyadhush Shalihin', 'RT 002 RW 001 Manggemaci Kec. Mpunda Kota Bima, NTB', 'Abdul Gafar, ST, M.Eng.', '+62 853 3875 2324', 'albimawy.biz.id@gmail.com', 9, 6, 86, 0, 3, 100000, '8fca7eeed599fa92fe1ada1d39e5cc8e.jpg'),
	(2, '01.23.01.037', 'Bidayatul Islam', 'Jalan Dewaruci No. 3 Seganteng Gubuk Pande, Cakranegara', 'Inaya', '+62 878-6583-3041', 'email-admintpq@gmail.com', 1, 6, 50, 0, 0, 100000, 'ca62431363b7afa08f4945cdc72776ac.jpg'),
	(3, '01.23.01.001', 'Bidayatul Hidayah', 'Ampenan', 'Khalid Mulki', '123456789012', '-', 1, 29, 0, 0, 0, 100000, '8d185fe352b8718e0a1f9e15e18e3a26.jpg');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

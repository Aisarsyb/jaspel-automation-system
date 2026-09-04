-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Sep 2026 pada 21.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rsgm_jaspel`
--

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `dpjp_aliases`;
DROP TABLE IF EXISTS `dpjp`;
DROP TABLE IF EXISTS `departments`;

--
-- Struktur dari tabel `dpjp`
--

CREATE TABLE IF NOT EXISTS `dpjp` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(150) NOT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dpjp`
--

INSERT INTO `dpjp` (`id`, `doctor_name`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Prof.R.M.Coen Pramono D, drg., SU., Sp.B.M.M., Subsp.Ortognat-D (K)., FICS.', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(2, 'Prof. Dr. David B. Kamadjaja, drg., MDS., Sp.B.M.M., Subsp.Ortognat-D (K)', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(3, 'Dr. Ni Putu Mira Sumarta, drg., Sp.B.M.M., Subsp.T.M.T.M.J. (K)', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(4, 'Andra Rizqiawan, drg., Ph.D., Sp.B.M.M., Subsp.T.M.T.M.J. (K)., FICS', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(5, 'Dr. R.Aries Muharram, drg., M.Kes., Sp.B.M.M., Subs.C.O.M. (K)', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(6, 'Dr. Indra Mulyawan, drg., MMRS., Sp.B.M.M., Subsp.T.M.T.M.J. (K)., FICS', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(7, 'Ganendra Anugraha, drg., Sp.B.M.M., Subs.C.O.M. (K)', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(8, 'Reza Al Fessi, drg., M.Ked.Klin., Sp.B.M.M., Subs.C.O.M. (K)', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(9, 'Liska Barus, drg., M.Ked.Klin., Sp.B.M.M., Subs.C.O.M. (K)., FICS', 1, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(10, 'Prof. Dr. Chiquita Prahasanti S., drg., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(11, 'Prof. Dr. Ernie Maduratna S., drg., M.Kes., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(12, 'Prof. Dr. Agung Krismariono, drg., M.Kes., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(13, 'Irma Josefina Savitri, drg., Ph.D., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(14, 'Dr. Eka Fitria Augustina, drg., M.Kes., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(15, 'Dr. Shafira Kurnia S., drg., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(16, 'Noer Ulfah, drg., M.Kes., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(17, 'Lambang Bargowo, drg., M.Kes., Sp.Perio(K)', 2, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(18, 'Prof. Dr. Tamara Yuanita, drg., MS., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(19, 'Prof. Dr. Kun Ismiyatin, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(20, 'Prof. Dr. Ira Widjiastuti, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(21, 'Dr. Dian Agustin W., drg., Sp.KG (K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(22, 'Nirawati Pribadi, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(23, 'Dr. Eric Priyo Prasetyo, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(24, 'Dr. Galih Sampoerno, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(25, 'Setyabudi, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(26, 'Dr. Widya Saraswati, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(27, 'Dr. Devi Eka Juniarti, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(28, 'Dr. Febriastuti Cahyani, drg., M.Kes., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(29, 'Nanik Zubaidah, drg., Sp.KG(K)', 3, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(30, 'Dr. Prawati Nuraini, drg., M.Kes., Sp.KGA, K-KKA', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(31, 'Prof. Dr. Soegeng Wahluyo, drg., M.Kes., Sp.KGA, K-KKA', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(32, 'Prof. Udijanto Tedjosasongko, drg., Ph.D., Sp.KGA, K-PKOA', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(33, 'Prof. Tania Saskianti, drg., Ph.D., Sp.KGA, K-AIBK', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(34, 'Prof. Dr. Sindy Cornelia Nelwan, drg., Sp.KGA, K-KKA', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(35, 'Mega Moeharyono Puteri, drg., Ph.D., Sp.KGA(K)', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(36, 'Betadion Rizki Sinaredi, drg., M.Kes., Sp.KGA(K)', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(37, 'Ardianti Maartrina Dewi, drg., M.Kes., Sp.KGA(K)', 4, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(38, 'Adiastuti Endah Parmadiati, drg., M.Kes., Sp.PM(K)', 5, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(39, 'Dr. Desiana Radithia, drg., SpPM(K)', 5, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(40, 'Nurina Febriyanti Ayuningtyas, drg., M.Kes., Ph.D., Sp.PM', 5, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(41, 'Fatma Yasmin Mahdani, drg., M.Kes., Sp.PM', 5, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(42, 'Aulya Setyo Pratiwi, drg., Sp.PM', 5, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(43, 'Imam Safari Azhar, drg., M.Kes., Sp.Pros (K)., CPDI', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(44, 'Dr. Mefina Kuntjoro, drg., M.Kes., Sp.Pros.(K)', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(45, 'Maretaningtias Dwi Ariani, drg., M.Kes., Ph.D., Sp.Pros', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(46, 'Karina Mundiratri, drg., Sp.Pros', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(47, 'Primanda Nur Rahmania, drg., Sp.Pros', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(48, 'Ratri Maya Sitalaksmi, drg., M.Kes., Ph.D., Sp.Pros(K)', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(49, 'Abil Kurdi, drg., Sp.Pros', 6, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(50, 'Dr. Eha Renwi Astuti, drg., M.Kes., Sp.RKG(K)', 7, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(51, 'Aga Satria Nurrachman, drg., Sp.RKG', 7, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(52, 'Alhidayati Asymal, drg., M.Kes., Sp.RKG', 7, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(53, 'Fajarin Nova, drg., Sp.KG', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(54, 'Bella Ayu Paramitha, dr', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(55, 'Deafitri Puspitasari, drg', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(56, 'Ulyasari Rachamaningayu, drg', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(57, 'Clara Vica Herrera, drg', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(58, 'Vankalayya Yastriza Dayusmara, drg', 8, '2026-07-05 14:30:26', '2026-07-05 14:30:26'),
(59, 'Ameliana Nuraeni, drg., Sp.B.M.M', 1, '2026-07-18 14:23:53', '2026-07-18 14:23:53'),
(60, 'Prof. Dr. Sri Kunarti, drg., MS., SpKG., Subsp.KR(K)', 3, '2026-07-18 14:24:35', '2026-07-18 14:24:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_dept_unique` (`doctor_name`,`department_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  ADD CONSTRAINT `dpjp_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Sep 2026 pada 21.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rsgm_jaspel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `status`, `created_at`) VALUES
(1, 'Bedah Mulut dan Maksilofasial', 'active', '2026-07-05 14:30:26'),
(2, 'Periodonsia', 'active', '2026-07-05 14:30:26'),
(3, 'Konservasi', 'active', '2026-07-05 14:30:26'),
(4, 'IKGA', 'active', '2026-07-05 14:30:26'),
(5, 'IPM', 'active', '2026-07-05 14:30:26'),
(6, 'Prostodonsia', 'active', '2026-07-05 14:30:26'),
(7, 'Radiologi Kedokteran Gigi', 'active', '2026-07-05 14:30:26'),
(8, 'ORGANIK RSGM', 'active', '2026-07-05 14:30:26');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Sep 2026 pada 21.56
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rsgm_jaspel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dpjp_aliases`
--

CREATE TABLE IF NOT EXISTS `dpjp_aliases` (
  `id` int(11) NOT NULL,
  `dpjp_id` int(11) NOT NULL,
  `alias_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dpjp_aliases`
--

INSERT INTO `dpjp_aliases` (`id`, `dpjp_id`, `alias_name`, `created_at`) VALUES
(1, 1, 'RMCOEN PRAMONO D', '2026-07-05 14:30:26'),
(2, 2, 'DAVID B KAMADJAJA', '2026-07-05 14:30:26'),
(3, 3, 'NI PUTU MIRA SUMARTA', '2026-07-05 14:30:26'),
(4, 4, 'ANDRA RIZQIAWAN', '2026-07-05 14:30:26'),
(5, 5, 'RARIES MUHARRAM', '2026-07-05 14:30:26'),
(6, 6, 'INDRA MULYAWAN', '2026-07-05 14:30:26'),
(7, 7, 'GANENDRA ANUGRAHA', '2026-07-05 14:30:26'),
(8, 8, 'REZA AL FESSI', '2026-07-05 14:30:26'),
(9, 9, 'LISKA BARUS', '2026-07-05 14:30:26'),
(10, 10, 'CHIQUITA PRAHASANTI S', '2026-07-05 14:30:26'),
(11, 11, 'ERNIE MADURATNA S', '2026-07-05 14:30:26'),
(12, 12, 'AGUNG KRISMARIONO', '2026-07-05 14:30:26'),
(13, 13, 'IRMA JOSEFINA SAVITRI', '2026-07-05 14:30:26'),
(14, 14, 'EKA FITRIA AUGUSTINA', '2026-07-05 14:30:26'),
(15, 15, 'SHAFIRA KURNIA S', '2026-07-05 14:30:26'),
(16, 16, 'NOER ULFAH', '2026-07-05 14:30:26'),
(17, 17, 'LAMBANG BARGOWO', '2026-07-05 14:30:26'),
(18, 18, 'TAMARA YUANITA', '2026-07-05 14:30:26'),
(19, 19, 'KUN ISMIYATIN', '2026-07-05 14:30:26'),
(20, 20, 'IRA WIDJIASTUTI', '2026-07-05 14:30:26'),
(21, 21, 'DIAN AGUSTIN W', '2026-07-05 14:30:26'),
(22, 22, 'NIRAWATI PRIBADI', '2026-07-05 14:30:26'),
(23, 23, 'ERIC PRIYO PRASETYO', '2026-07-05 14:30:26'),
(24, 24, 'GALIH SAMPOERNO', '2026-07-05 14:30:26'),
(25, 25, 'SETYABUDI', '2026-07-05 14:30:26'),
(26, 26, 'WIDYA SARASWATI', '2026-07-05 14:30:26'),
(27, 27, 'DEVI EKA JUNIARTI', '2026-07-05 14:30:26'),
(28, 28, 'FEBRIASTUTI CAHYANI', '2026-07-05 14:30:26'),
(29, 29, 'NANIK ZUBAIDAH', '2026-07-05 14:30:26'),
(30, 30, 'PRAWATI NURAINI', '2026-07-05 14:30:26'),
(31, 31, 'SOEGENG WAHLUYO', '2026-07-05 14:30:26'),
(32, 32, 'UDIJANTO TEDJOSASONGKO', '2026-07-05 14:30:26'),
(33, 33, 'TANIA SASKIANTI', '2026-07-05 14:30:26'),
(34, 34, 'SINDY CORNELIA NELWAN', '2026-07-05 14:30:26'),
(35, 35, 'MEGA MOEHARYONO PUTERI', '2026-07-05 14:30:26'),
(36, 36, 'BETADION RIZKI SINAREDI', '2026-07-05 14:30:26'),
(37, 37, 'ARDIANTI MAARTRINA DEWI', '2026-07-05 14:30:26'),
(38, 38, 'ADIASTUTI ENDAH PARMADIATI', '2026-07-05 14:30:26'),
(39, 39, 'DESIANA RADITHIA', '2026-07-05 14:30:26'),
(40, 40, 'NURINA FEBRIYANTI AYUNINGTYAS', '2026-07-05 14:30:26'),
(41, 41, 'FATMA YASMIN MAHDANI', '2026-07-05 14:30:26'),
(42, 42, 'AULYA SETYO PRATIWI', '2026-07-05 14:30:26'),
(43, 43, 'IMAM SAFARI AZHAR', '2026-07-05 14:30:26'),
(44, 44, 'MEFINA KUNTJORO', '2026-07-05 14:30:26'),
(45, 45, 'MARETANINGTIAS DWI ARIANI', '2026-07-05 14:30:26'),
(46, 46, 'KARINA MUNDIRATRI', '2026-07-05 14:30:26'),
(47, 47, 'PRIMANDA NUR RAHMANIA', '2026-07-05 14:30:26'),
(48, 48, 'RATRI MAYA SITALAKSMI', '2026-07-05 14:30:26'),
(49, 49, 'ABIL KURDI', '2026-07-05 14:30:26'),
(50, 50, 'EHA RENWI ASTUTI', '2026-07-05 14:30:26'),
(51, 51, 'AGA SATRIA NURRACHMAN', '2026-07-05 14:30:26'),
(52, 52, 'ALHIDAYATI ASYMAL', '2026-07-05 14:30:26'),
(53, 53, 'FAJARIN NOVA', '2026-07-05 14:30:26'),
(54, 54, 'BELLA AYU PARAMITHA', '2026-07-05 14:30:26'),
(55, 55, 'DEAFITRI PUSPITASARI', '2026-07-05 14:30:26'),
(56, 56, 'ULYASARI RACHAMANINGAYU', '2026-07-05 14:30:26'),
(57, 57, 'CLARA VICA HERRERA', '2026-07-05 14:30:26'),
(58, 58, 'VANKALAYYA YASTRIZA DAYUSMARA', '2026-07-05 14:30:26'),
(59, 59, 'AMELIANA NURAENI', '2026-07-18 14:23:53'),
(60, 60, 'SRI KUNARTI', '2026-07-18 14:24:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alias_name` (`alias_name`),
  ADD KEY `dpjp_id` (`dpjp_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  ADD CONSTRAINT `dpjp_aliases_ibfk_1` FOREIGN KEY (`dpjp_id`) REFERENCES `dpjp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
S E T   F O R E I G N _ K E Y _ C H E C K S   =   1 ;  
 
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Sep 2026 pada 14.51
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
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'Update Settings', 'Memperbarui konfigurasi sistem', '::1', '2026-07-05 13:52:39'),
(2, 1, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 14:45:21'),
(3, 1, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 14:48:50'),
(4, 1, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 14:48:57'),
(5, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 14:49:04'),
(6, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 14:51:41'),
(7, 2, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 14:57:46'),
(8, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 14:58:29'),
(9, 2, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 15:30:43'),
(10, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 15:30:48'),
(11, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 20:55:33'),
(12, 2, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 21:03:45'),
(13, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 21:04:06'),
(14, 2, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 22:10:55'),
(15, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 22:11:03'),
(16, 2, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-05 23:15:10'),
(17, 2, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-05 23:15:15'),
(18, 1, 'Create DPJP', 'Menambah DPJP baru: Ameliana Nuraeni, drg., Sp.B.M.M (Dept ID: 1)', '::1', '2026-07-18 14:23:53'),
(19, 1, 'Create DPJP', 'Menambah DPJP baru: Prof. Dr. Sri Kunarti, drg., MS., SpKG., Subsp.KR(K) (Dept ID: 3)', '::1', '2026-07-18 14:24:36'),
(20, 1, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-18 14:46:50'),
(21, 1, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-18 14:46:55'),
(22, 1, 'Logout', 'Admin keluar dari sistem', '::1', '2026-07-19 08:44:55'),
(23, 1, 'Login', 'Admin berhasil masuk ke sistem', '::1', '2026-07-19 08:45:02'),
(24, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: REKAP IRJA JANUARI 2026.xlsx (History ID: 1)', '::1', '2026-07-19 10:12:53'),
(25, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1784455968_REKAP_IRJA_JANUARI_2026.xlsx (excel)', '::1', '2026-07-19 10:13:07'),
(26, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: REKAP IRJA JANUARI 2026.xlsx (History ID: 2)', '::1', '2026-07-19 11:36:57'),
(27, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1784461007_REKAP_IRJA_JANUARI_2026.xlsx (excel)', '::1', '2026-07-19 11:37:07'),
(28, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: REKAP IRJA JANUARI 2026.xlsx (History ID: 3)', '::1', '2026-07-19 14:39:36'),
(29, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1784471963_REKAP_IRJA_JANUARI_2026.xlsx (excel)', '::1', '2026-07-19 14:40:01'),
(30, 1, 'Delete History', 'Menghapus riwayat import ID 3 (File: REKAP IRJA JANUARI 2026.xlsx)', '::1', '2026-07-23 15:08:50'),
(31, 1, 'Delete History', 'Menghapus riwayat import ID 2 (File: REKAP IRJA JANUARI 2026.xlsx)', '::1', '2026-07-23 15:08:53'),
(32, 1, 'Delete History', 'Menghapus riwayat import ID 1 (File: REKAP IRJA JANUARI 2026.xlsx)', '::1', '2026-07-23 15:08:56'),
(33, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: REKAP IRJA JANUARI 2026.xlsx (History ID: 4)', '::1', '2026-07-24 08:16:20'),
(34, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1784880932_REKAP_IRJA_JANUARI_2026.xlsx (excel)', '::1', '2026-07-24 08:16:34'),
(35, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: REKAP IRJA JANUARI 2026.xlsx (History ID: 5)', '::1', '2026-07-24 15:55:57'),
(36, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: IRJA FEB 26.xlsx (History ID: 6)', '::1', '2026-08-30 17:40:06'),
(37, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1788111589_IRJA_FEB_26.xlsx (excel)', '::1', '2026-08-30 17:40:11'),
(38, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: IRJA APRIL 26.xlsx (History ID: 7)', '::1', '2026-09-04 19:22:41'),
(39, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1788549749_IRJA_APRIL_26.xlsx (excel)', '::1', '2026-09-04 19:23:38'),
(40, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: IRJA APRIL 26.xlsx (History ID: 8)', '::1', '2026-09-05 09:20:55'),
(41, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788608627_Setyabudi__drg___M_Kes___Sp_KG_K_.xlsx (temp_excel)', '::1', '2026-09-05 11:43:47'),
(42, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788608696_Andra_Rizqiawan__drg___Ph_D___Sp_B_M_M___Subsp_T_M_T_M_J___K____FICS.xlsx (temp_excel)', '::1', '2026-09-05 11:44:56'),
(43, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788608700_Andra_Rizqiawan__drg___Ph_D___Sp_B_M_M___Subsp_T_M_T_M_J___K____FICS__TLB_.xlsx (temp_excel)', '::1', '2026-09-05 11:45:00'),
(44, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: IRJA APRIL 26.xlsx (History ID: 9)', '::1', '2026-09-05 11:46:24'),
(45, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1788608767_IRJA_APRIL_26.xlsx (excel)', '::1', '2026-09-05 11:46:37'),
(46, 1, 'Delete History', 'Menghapus riwayat import ID 9 (File: IRJA APRIL 26.xlsx)', '::1', '2026-09-05 11:57:39'),
(47, 1, 'Delete History', 'Menghapus riwayat import ID 8 (File: IRJA APRIL 26.xlsx)', '::1', '2026-09-05 11:57:40'),
(48, 1, 'Delete History', 'Menghapus riwayat import ID 7 (File: IRJA APRIL 26.xlsx)', '::1', '2026-09-05 11:57:42'),
(49, 1, 'Delete History', 'Menghapus riwayat import ID 6 (File: IRJA FEB 26.xlsx)', '::1', '2026-09-05 11:57:43'),
(50, 1, 'Delete History', 'Menghapus riwayat import ID 5 (File: REKAP IRJA JANUARI 2026.xlsx)', '::1', '2026-09-05 11:57:45'),
(51, 1, 'Delete History', 'Menghapus riwayat import ID 4 (File: REKAP IRJA JANUARI 2026.xlsx)', '::1', '2026-09-05 11:57:46'),
(52, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788610242_Andra_Rizqiawan__drg___Ph_D___Sp_B_M_M___Subsp_T_M_T_M_J___K____FICS.xlsx (temp_excel)', '::1', '2026-09-05 12:10:42'),
(53, 1, 'Import Excel', 'Sukses menyelesaikan rekapitulasi file: IRJA APRIL 26.xlsx (History ID: 10)', '::1', '2026-09-05 12:15:46'),
(54, 1, 'Download Output', 'Mendownload file hasil rekap: REKAP_JASPEL_1788610524_IRJA_APRIL_26.xlsx (excel)', '::1', '2026-09-05 12:21:16'),
(55, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788610966_Adiastuti_Endah_Parmadiati__drg___M_Kes___Sp_PM_K_.xlsx (temp_excel)', '::1', '2026-09-05 12:22:46'),
(56, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788611935_Andra_Rizqiawan__drg___Ph_D___Sp_B_M_M___Subsp_T_M_T_M_J___K____FICS.xlsx (temp_excel)', '::1', '2026-09-05 12:38:56'),
(57, 1, 'Download Output', 'Mendownload file hasil rekap: Temp_1788612516_Andra_Rizqiawan__drg___Ph_D___Sp_B_M_M___Subsp_T_M_T_M_J___K____FICS.xlsx (temp_excel)', '::1', '2026-09-05 12:48:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `dpjp`
--

CREATE TABLE `dpjp` (
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `dpjp_aliases`
--

CREATE TABLE `dpjp_aliases` (
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `import_errors`
--

CREATE TABLE `import_errors` (
  `id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `row_number` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `error_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `import_history`
--

CREATE TABLE `import_history` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `output_file` varchar(255) NOT NULL,
  `total_rows` int(11) DEFAULT 0,
  `success_rows` int(11) DEFAULT 0,
  `failed_rows` int(11) DEFAULT 0,
  `total_departments` int(11) DEFAULT 0,
  `total_doctors` int(11) DEFAULT 0,
  `total_jaspel` decimal(15,2) DEFAULT 0.00,
  `duration_seconds` decimal(8,2) DEFAULT 0.00,
  `file_size_mb` decimal(8,2) DEFAULT 0.00,
  `imported_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `import_history`
--

INSERT INTO `import_history` (`id`, `file_name`, `output_file`, `total_rows`, `success_rows`, `failed_rows`, `total_departments`, `total_doctors`, `total_jaspel`, `duration_seconds`, `file_size_mb`, `imported_by`, `created_at`) VALUES
(10, 'IRJA APRIL 26.xlsx', 'REKAP_JASPEL_1788610524_IRJA_APRIL_26.xlsx', 2339, 2339, 0, 7, 45, 157337167.00, 22.19, 1.04, 1, '2026-09-05 12:15:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`) VALUES
(1, 'JASPEL_PERCENTAGE', '20', '2026-07-05 04:55:59'),
(2, 'MAX_UPLOAD_SIZE', '20', '2026-07-05 04:55:59'),
(3, 'ALLOWED_EXTENSION', 'xlsx', '2026-07-05 04:55:59'),
(4, 'APP_NAME', 'RSGM Jaspel', '2026-07-05 04:55:59'),
(5, 'COMPANY', 'RSGM Universitas Airlangga', '2026-07-05 04:55:59'),
(10, 'RKG_JASPEL_PERCENTAGE', '15', '2026-07-24 16:07:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `severity` varchar(20) DEFAULT 'error',
  `error_message` text NOT NULL,
  `stack_trace` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `system_logs`
--

INSERT INTO `system_logs` (`id`, `severity`, `error_message`, `stack_trace`, `created_at`) VALUES
(1, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\modules\\import\\process.php(39): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-05 15:07:55'),
(2, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\modules\\import\\process.php(58): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 08:45:47'),
(3, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\test_red_skip.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:12:16'),
(4, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\test_red_skip.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:12:32'),
(5, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\test_red_skip.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:12:52'),
(6, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\test_red_skip.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:13:37'),
(7, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\test_red_skip.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:14:13'),
(8, 'error', 'Excel reading failed: Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.', '#0 C:\\xampp\\htdocs\\Excel_Automation_System\\debug_ex.php(9): ExcelReader::read(\'C:\\\\xampp\\\\htdocs...\')\n#1 {main}', '2026-07-19 15:15:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$NTA4k4P2VMRiNo1mH77C3eV4Br9VtBVgoY.q3tjqzT1E8Y5Cbz5GO', 'admin', '2026-07-05 04:55:59'),
(2, 'admin1', '$2y$10$NTA4k4P2VMRiNo1mH77C3eV4Br9VtBVgoY.q3tjqzT1E8Y5Cbz5GO', 'admin', '2026-07-05 14:44:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indeks untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_dept_unique` (`doctor_name`,`department_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indeks untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alias_name` (`alias_name`),
  ADD KEY `dpjp_id` (`dpjp_id`);

--
-- Indeks untuk tabel `import_errors`
--
ALTER TABLE `import_errors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_id` (`history_id`);

--
-- Indeks untuk tabel `import_history`
--
ALTER TABLE `import_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imported_by` (`imported_by`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `import_errors`
--
ALTER TABLE `import_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `import_history`
--
ALTER TABLE `import_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dpjp`
--
ALTER TABLE `dpjp`
  ADD CONSTRAINT `dpjp_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dpjp_aliases`
--
ALTER TABLE `dpjp_aliases`
  ADD CONSTRAINT `dpjp_aliases_ibfk_1` FOREIGN KEY (`dpjp_id`) REFERENCES `dpjp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `import_errors`
--
ALTER TABLE `import_errors`
  ADD CONSTRAINT `import_errors_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `import_history` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `import_history`
--
ALTER TABLE `import_history`
  ADD CONSTRAINT `import_history_ibfk_1` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2026 at 12:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peminjaman_alat`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjamen`
--

CREATE TABLE `detail_peminjamen` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `tool_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `qty_baik` int DEFAULT NULL,
  `qty_rusak` int DEFAULT NULL,
  `kondisi_detail` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `is_tambahan` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'FALSE: Peminjaman Pertama/Utama, TRUE: Tambahan Peminjaman',
  `tahap` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '1: Peminjaman Pertama, 2+: Tahap Tambahan',
  `catatan` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Catatan keperluan peminjaman tambahan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_peminjamen`
--

INSERT INTO `detail_peminjamen` (`id`, `peminjaman_id`, `tool_id`, `qty`, `qty_baik`, `qty_rusak`, `kondisi_detail`, `is_tambahan`, `tahap`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, NULL, NULL, 'baik', 0, 1, NULL, '2026-09-19 04:32:36', '2026-09-19 04:32:36'),
(2, 1, 2, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-09-19 04:32:36', '2026-09-19 04:32:36'),
(3, 2, 13, 3, NULL, NULL, 'baik', 0, 1, NULL, '2026-09-25 16:39:58', '2026-09-25 16:39:58'),
(4, 3, 10, 3, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-08 19:37:21', '2026-10-08 19:37:21'),
(5, 4, 7, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-08 20:22:21', '2026-10-08 20:22:21'),
(6, 4, 8, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-08 20:22:21', '2026-10-08 20:22:21'),
(7, 4, 23, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-08 20:22:21', '2026-10-08 20:22:21'),
(8, 5, 10, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-16 18:19:11', '2026-10-16 18:19:11'),
(9, 5, 6, 1, NULL, NULL, 'baik', 1, 2, 'kgbj', '2026-10-16 18:25:45', '2026-10-16 18:25:45'),
(10, 6, 13, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-16 20:09:48', '2026-10-16 20:09:48'),
(11, 7, 10, 1, NULL, NULL, 'baik', 0, 1, NULL, '2026-10-16 22:01:11', '2026-10-16 22:01:11'),
(12, 8, 13, 5, NULL, NULL, 'baik', 0, 1, NULL, '2026-11-04 23:35:59', '2026-11-04 23:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_19_040213_add_role_department_to_users_table', 1),
(5, '2026_09_19_040255_create_tools_table', 1),
(6, '2026_09_19_040305_create_peminjamen_table', 1),
(7, '2026_09_19_040427_create_detail_peminjamen_table', 1),
(8, '2026_09_19_040440_create_pengembalians_table', 1),
(9, '2026_09_19_050000_add_pic_to_users_table', 2),
(10, '2026_09_26_060000_add_repair_and_confirmation_fields_to_tables', 3),
(11, '2026_10_09_044224_add_tambahan_fields_to_detail_peminjamen_table', 4),
(12, '2026_10_17_070000_create_perpanjangans_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjamen`
--

CREATE TABLE `peminjamen` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dipinjam',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjamen`
--

INSERT INTO `peminjamen` (`id`, `user_id`, `department`, `pic`, `tanggal_pinjam`, `tanggal_kembali`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-09-19', '2026-09-22', 'rusak_dikonfirmasi', '2026-09-19 04:32:36', '2026-10-08 19:35:22'),
(2, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-09-25', '2026-09-28', 'rusak_dikonfirmasi', '2026-09-25 16:39:57', '2026-10-06 05:48:55'),
(3, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-10-09', '2026-10-12', 'selesai', '2026-10-08 19:37:21', '2026-10-08 19:39:19'),
(4, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-10-09', '2026-10-31', 'selesai', '2026-10-08 20:22:21', '2026-10-18 21:36:48'),
(5, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-10-17', '2026-10-20', 'selesai', '2026-10-16 18:19:11', '2026-10-18 21:37:09'),
(6, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-10-17', '2026-10-17', 'selesai', '2026-10-16 20:09:48', '2026-10-18 21:36:56'),
(7, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-10-17', '2026-10-20', 'selesai', '2026-10-16 22:01:11', '2026-10-18 21:37:02'),
(8, 1, 'Quality Control (QC)', 'Budi Santoso', '2026-11-05', '2026-11-18', 'dipinjam', '2026-11-04 23:35:58', '2026-11-04 23:35:58');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalians`
--

CREATE TABLE `pengembalians` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `teknisi_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal_dikembalikan` date NOT NULL,
  `kondisi_alat` enum('baik','rusak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `jumlah_baik` int NOT NULL DEFAULT '0',
  `jumlah_rusak` int NOT NULL DEFAULT '0',
  `tanda_kerusakan_berat` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Otomatis TRUE jika jumlah rusak >= 3 unit',
  `bisa_direpair` tinyint(1) DEFAULT NULL COMMENT 'TRUE: bisa direpair, FALSE: tidak bisa direpair',
  `status_repair` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tidak_perlu' COMMENT 'tidak_perlu, dalam_perbaikan, selesai_direpair, tidak_bisa_direpair',
  `catatan_teknisi` text COLLATE utf8mb4_unicode_ci,
  `catatan_perbaikan` text COLLATE utf8mb4_unicode_ci,
  `konfirmasi_admin` tinyint(1) NOT NULL DEFAULT '0',
  `konfirmasi_admin_at` timestamp NULL DEFAULT NULL,
  `konfirmasi_admin_by` bigint UNSIGNED DEFAULT NULL,
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `tindakan_admin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'afkir_aset, bebankan_ganti_rugi, toleransi, lainnya',
  `konfirmasi_user` tinyint(1) NOT NULL DEFAULT '0',
  `konfirmasi_user_at` timestamp NULL DEFAULT NULL,
  `catatan_user` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengembalians`
--

INSERT INTO `pengembalians` (`id`, `peminjaman_id`, `teknisi_id`, `tanggal_dikembalikan`, `kondisi_alat`, `jumlah_baik`, `jumlah_rusak`, `tanda_kerusakan_berat`, `bisa_direpair`, `status_repair`, `catatan_teknisi`, `catatan_perbaikan`, `konfirmasi_admin`, `konfirmasi_admin_at`, `konfirmasi_admin_by`, `catatan_admin`, `tindakan_admin`, `konfirmasi_user`, `konfirmasi_user_at`, `catatan_user`, `created_at`, `updated_at`) VALUES
(1, 1, 4, '2026-10-05', 'rusak', 2, 1, 0, 0, 'tidak_bisa_direpair', 'test wvjqnejvfqeav', NULL, 1, '2026-10-08 19:35:22', 3, 'laefdjcnsj', 'afkir_aset', 1, '2026-10-08 19:33:35', 'kuhjadkfhjajldc', '2026-10-05 04:41:20', '2026-10-08 19:35:22'),
(2, 2, 4, '2026-10-05', 'rusak', 2, 1, 0, 0, 'tidak_bisa_direpair', 'wirkufbqiwkrb', NULL, 1, '2026-10-06 05:48:55', 3, 'ssasas', 'toleransi', 0, NULL, NULL, '2026-10-05 05:02:58', '2026-10-06 05:48:55'),
(3, 3, 4, '2026-10-09', 'rusak', 1, 2, 0, 1, 'selesai_direpair', 'danza nya belok', 'menunggu sparpart alt\n---\n[Dibenarkan pada 09 October 2026, 02:39 oleh Teknisi Kalibrasi Joko]: selesa di perbaiki', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-08 19:38:50', '2026-10-08 19:39:19'),
(4, 4, 4, '2026-10-19', 'rusak', 2, 1, 0, 1, 'selesai_direpair', 'SLEJFilfUO', 'ndsdsdc', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-18 21:36:48', '2026-10-18 21:36:48'),
(5, 6, 4, '2026-10-19', 'baik', 1, 0, 0, NULL, 'tidak_perlu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-18 21:36:56', '2026-10-18 21:36:56'),
(6, 7, 4, '2026-10-19', 'baik', 1, 0, 0, NULL, 'tidak_perlu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-18 21:37:02', '2026-10-18 21:37:02'),
(7, 5, 4, '2026-10-19', 'baik', 2, 0, 0, NULL, 'tidak_perlu', NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-18 21:37:09', '2026-10-18 21:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `perpanjangans`
--

CREATE TABLE `perpanjangans` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `tanggal_kembali_lama` date NOT NULL,
  `tanggal_kembali_baru` date NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1YHG397301Y1ltUpSbiwmueEHILI5YkZSAwaBmuR', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJMNk9hNlpRRG15WHlLMFRGd2M2ZTRsVDdHQ3pDVEk1OU5BQldxSlpOIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792834892),
('cS9ci1evut4RJnJXuvcXj3uGidV5JdhSpDgn1rCC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', 'eyJfdG9rZW4iOiJZbnh4RmIzRXBOQ3d3RlNWcEhFd1lqdktnVUwxZ09pT29GNU5qN1NnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1793859574),
('f0s9tYQSigrNWzKWtdfbeCb9ZOQMo61w3HLcWaUK', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJjd2NuOGJCYjFaYm1XRHBUUzExRHl6aTVWcE5zNU1UVm1NemFUdjJoIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3VzZXJcL3BpbmphbVwvOCIsInJvdXRlIjoidXNlci5waW5qYW0uc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1793864118),
('I35FQe4WTwDBFQuWg46hCaSs0Cx188EpJIOqDrpO', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJsejdQd0k3YlJuVFBFQ0dxc0dWbXB6Wnl1a2hzZUgwQ000bkdvaGZmIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3Rla25pc2lcL2hpc3Rvcnk/a2VydXNha2FuX2JlcmF0PSZrb25kaXNpPWJhaWsiLCJyb3V0ZSI6InRla25pc2kuaGlzdG9yeSJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6NH0=', 1792839340),
('iHLO21btcabgPwIpnU4uXrhffy3UEMVeRbjIk7KK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJKRzJaWEhxRHdoREt4bzJqa1Vyb1Q1cGtFZks1VExYUnhQemhFSzBXIiwic3VjY2VzcyI6IkFuZGEgdGVsYWggYmVyaGFzaWwgbG9nb3V0LiIsIl9mbGFzaCI6eyJuZXciOltdLCJvbGQiOlsic3VjY2VzcyJdfX0=', 1793860450),
('McA0RgVbs32KsbtTo3336xcLHHJTsqECLLWc6Kqx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJXRHNnTFBtVnNRbDQ3V3J3eEhBblVIRTZtbUtPY0JoYmRjelZSY1prIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792834891),
('VlS7DKNFsTveqHfr3qzB9eq5bXiLoDFF3SdDyFlZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIyUFZUQUhWN0dOSVM4V3BCSmJzaUE3NUJyams0ZHJhWnI4VWpUTzdXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792834892),
('vsmmCKt0zF6D1DnrEuPQyYAeXDVsmYO07XShftIB', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5N2ZYaVQwdXVCb3JNdGk4dG1kWmQyRDlwVk8xczlaRTFTdmM5clFuIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjQsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvdGVrbmlzaVwvaGlzdG9yeT9rZXJ1c2FrYW5fYmVyYXQ9JmtvbmRpc2k9Iiwicm91dGUiOiJ0ZWtuaXNpLmhpc3RvcnkifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1793765875),
('wNEMf2GRPfiYg2Ws2AH1uZwF9eZp3YvDaYiFN11J', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ1SmR2aFV2cjFsQ1NITzNzdnpBRlJ6cElyN1VZMXhPRDRIN1kxSE1xIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792834892),
('Z4kbnN3H18LoTN2614NOKzbDJoXuCx6AqIsq0A7f', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI2dHJBOVllS2NsWkFyTmJqSUxLQ3pzR3lTbWR6VklEZ2Zid1FSNWJGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792834892);

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE `tools` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_alat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_alat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_stock` int NOT NULL,
  `available_stock` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tools`
--

INSERT INTO `tools` (`id`, `nama_alat`, `kode_alat`, `total_stock`, `available_stock`, `created_at`, `updated_at`) VALUES
(1, 'Dial Gauge Mitutoyo 0-10mm (0.01mm)', 'DG-001', 11, 12, '2026-09-19 04:24:37', '2026-10-08 19:35:22'),
(2, 'Small Hole Bore / Ball Gauge Set 18-35mm', 'BG-002', 5, 5, '2026-09-19 04:24:37', '2026-09-25 16:05:22'),
(3, 'Inside Micrometer Tubular Type 50-300mm', 'MIC-003', 5, 5, '2026-09-19 04:24:37', '2026-09-25 16:05:22'),
(4, 'Vernier Caliper Mitutoyo 150mm (0.02mm)', 'CAL-004', 15, 15, '2026-09-19 04:24:37', '2026-09-19 04:24:37'),
(5, 'Dial Test Indicator Lever Type 0.8mm', 'DTI-005', 5, 5, '2026-09-19 04:24:37', '2026-09-19 04:24:37'),
(6, 'Magnetic Stand Flexible Base Holder', 'MAG-006', 12, 12, '2026-09-19 04:24:37', '2026-10-18 21:37:09'),
(7, 'Height Gauge Vernier 300mm', 'HG-007', 4, 4, '2026-09-19 04:24:37', '2026-10-18 21:36:48'),
(8, 'Gauge Block Set (Slip Gauge 47 pcs)', 'GB-008', 3, 3, '2026-09-19 04:24:37', '2026-10-18 21:36:48'),
(9, 'Digital Dial Indicator Mitutoyo ID-C 12.7mm (0.001mm)', 'DG-002', 8, 8, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(10, 'Bore Gauge / Ball Gauge Set 50-160mm (0.01mm)', 'BG-001', 6, 6, '2026-09-25 16:05:22', '2026-10-18 21:37:09'),
(11, 'Digital Outside Micrometer 0-25mm (0.001mm)', 'MIC-001', 10, 10, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(12, 'Outside Micrometer Mitutoyo 25-50mm (0.01mm)', 'MIC-002', 8, 8, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(13, 'Depth Micrometer Interchangeable Rod 0-150mm', 'MIC-004', 5, 0, '2026-09-25 16:05:22', '2026-11-04 23:35:59'),
(14, 'Vernier Caliper Mitutoyo 150mm (0.02mm)', 'CAL-001', 15, 15, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(15, 'Digital Vernier Caliper Absolute 200mm (0.01mm)', 'CAL-002', 10, 10, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(16, 'Dial Test Indicator Lever Type 0.8mm (0.01mm)', 'DTI-001', 8, 8, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(17, 'High-Accuracy Dial Test Indicator 0.14mm (0.002mm)', 'DTI-002', 4, 4, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(18, 'Magnetic Stand Flexible Articulated Arm Holder', 'MAG-001', 14, 14, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(19, 'Digital Height Gauge Mitutoyo 300mm (0.01mm)', 'HG-001', 4, 4, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(20, 'Vernier Height Gauge Heavy Duty 600mm', 'HG-002', 3, 3, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(21, 'Precision Gauge Block Set (Grade 0 - 87 Pcs)', 'GB-001', 3, 3, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(22, 'Pin Gauge Set Metric 1.000 - 10.000mm (Step 0.01mm)', 'PG-001', 4, 4, '2026-09-25 16:05:22', '2026-09-25 16:05:22'),
(23, 'Feeler Gauge Thickness Leaf Set (0.03 - 1.00mm)', 'FG-001', 10, 10, '2026-09-25 16:05:22', '2026-10-18 21:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin','teknisi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `department`, `pic`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'JAYA ABADI(QC Staff)', 'user@example.com', NULL, '$2y$12$JajYQ9oATqbQWNWTN88dEez82.ZedxyFQ16zS0.2KegXPDXWWjmSG', 'user', 'Quality Control (QC)', 'Budi Santoso', 'xb5B2mrPnM9IJsmEoEl4iQYldRat7v00WiwC9AvDiDQPfpHkVMyIjZOccTSe', '2026-09-19 04:24:36', '2026-10-16 19:57:09'),
(2, 'Siti Rahma (Machining Staff)', 'user2@example.com', NULL, '$2y$12$IUBinfFAorhosDi91L0fKe3xKbI384sHRZpuzsKYarJ9RCFmu0/fS', 'user', 'Produksi Machining', 'Siti Rahma', NULL, '2026-09-19 04:24:36', '2026-09-19 21:37:58'),
(3, 'Admin Tool Crib', 'admin@example.com', NULL, '$2y$12$s7hGEDdRvEYQpHzT11HI/u9SxNITYE.41Eia39LhhgjQwym3zQ8Fm', 'admin', 'Tool Crib & Inventory', 'Ahmad Fauzi', 'CyHWx1VibKGnVJwhcR6XBk1BORYF8qQapuOR5QZaTJLXY8bVZJwq6SlBJhdg', '2026-09-19 04:24:36', '2026-09-19 21:37:58'),
(4, 'Teknisi Kalibrasi Joko', 'teknisi@example.com', NULL, '$2y$12$IZzPbSORSHnTIq4OefVBN.eE2GZb9i5rDD9W0FCDodC5RNPYY7ai2', 'teknisi', 'Maintenance & Calibration', 'Joko Susilo', 'ZTHnjncKhupcv7BzmInbSU6pgaadmeZ9cWOVbI3x3WgYG6xgIgJdM3nGLugi', '2026-09-19 04:24:36', '2026-09-19 21:37:59'),
(5, 'arya', 'wooin709@gmail.com', NULL, '$2y$12$miTgaRfpu8F28HBO6BAine.zBcgMzME4zcL8yReiKppwsszugLgq6', 'user', 'painting', 'bu dian', 'LZhvCCfQmE2LLsUivZqqRJhzoaTc6t3dX3SO3T3NGLO16KzVJ7uZkCzJK29m', '2026-09-19 21:10:41', '2026-09-19 21:10:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_peminjamen_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `detail_peminjamen_tool_id_foreign` (`tool_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjamen`
--
ALTER TABLE `peminjamen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjamen_user_id_foreign` (`user_id`);

--
-- Indexes for table `pengembalians`
--
ALTER TABLE `pengembalians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengembalians_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `pengembalians_teknisi_id_foreign` (`teknisi_id`),
  ADD KEY `pengembalians_konfirmasi_admin_by_foreign` (`konfirmasi_admin_by`);

--
-- Indexes for table `perpanjangans`
--
ALTER TABLE `perpanjangans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perpanjangans_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `perpanjangans_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tools_kode_alat_unique` (`kode_alat`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `peminjamen`
--
ALTER TABLE `peminjamen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pengembalians`
--
ALTER TABLE `pengembalians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `perpanjangans`
--
ALTER TABLE `perpanjangans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  ADD CONSTRAINT `detail_peminjamen_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjamen` (`id`),
  ADD CONSTRAINT `detail_peminjamen_tool_id_foreign` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`id`);

--
-- Constraints for table `peminjamen`
--
ALTER TABLE `peminjamen`
  ADD CONSTRAINT `peminjamen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pengembalians`
--
ALTER TABLE `pengembalians`
  ADD CONSTRAINT `pengembalians_konfirmasi_admin_by_foreign` FOREIGN KEY (`konfirmasi_admin_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengembalians_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjamen` (`id`),
  ADD CONSTRAINT `pengembalians_teknisi_id_foreign` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `perpanjangans`
--
ALTER TABLE `perpanjangans`
  ADD CONSTRAINT `perpanjangans_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perpanjangans_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjamen` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

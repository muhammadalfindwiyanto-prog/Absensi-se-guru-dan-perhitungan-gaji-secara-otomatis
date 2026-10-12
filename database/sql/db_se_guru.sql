-- ===============================================================
-- SE-GURU | Database MySQL / MariaDB untuk Laravel 12
-- Database: db_se_guru
-- Instalasi: impor berkas ini SATU KALI ke database kosong melalui phpMyAdmin.
-- PERINGATAN: Jangan impor ke DB yang sudah terisi data penting tanpa backup.
-- Sudah berisi pengguna, guru, jadwal dan komponen gaji CONTOH (bukan data riil).
-- Migrasi Laravel ditandai selesai; setelah impor TIDAK perlu migrate:fresh.
-- ===============================================================

CREATE DATABASE IF NOT EXISTS `db_se_guru`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_se_guru`;

SET NAMES utf8mb4;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'guru',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gurus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `telepon` varchar(25) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jabatan` varchar(255) NOT NULL DEFAULT 'Guru',
  `status_pegawai` varchar(255) NOT NULL DEFAULT 'Tetap',
  `tanggal_mulai` date DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gurus_user_id_unique` (`user_id`),
  UNIQUE KEY `gurus_kode_unique` (`kode`),
  CONSTRAINT `gurus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jadwals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `jam_masuk` time NOT NULL DEFAULT '07:00:00',
  `jam_pulang` time NOT NULL DEFAULT '14:00:00',
  `toleransi_menit` smallint unsigned NOT NULL DEFAULT 0,
  `hari_kerja` varchar(255) NOT NULL DEFAULT '1,2,3,4,5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jadwals_guru_id_unique` (`guru_id`),
  CONSTRAINT `jadwals_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `absensis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status` enum('hadir','terlambat','izin','sakit','alpa') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensis_guru_id_tanggal_unique` (`guru_id`,`tanggal`),
  KEY `absensis_tanggal_status_index` (`tanggal`,`status`),
  CONSTRAINT `absensis_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `komponengajis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `gaji_pokok` bigint unsigned NOT NULL DEFAULT 0,
  `tunjangan` bigint unsigned NOT NULL DEFAULT 0,
  `bonus` bigint unsigned NOT NULL DEFAULT 0,
  `honor` bigint unsigned NOT NULL DEFAULT 0,
  `potongan_lain` bigint unsigned NOT NULL DEFAULT 0,
  `potongan_alpa` bigint unsigned NOT NULL DEFAULT 0,
  `potongan_terlambat` bigint unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `komponengajis_guru_id_unique` (`guru_id`),
  CONSTRAINT `komponengajis_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `penggajians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `bulan` smallint unsigned NOT NULL,
  `tahun` smallint unsigned NOT NULL,
  `hadir` int unsigned NOT NULL DEFAULT 0,
  `terlambat` int unsigned NOT NULL DEFAULT 0,
  `izin` int unsigned NOT NULL DEFAULT 0,
  `sakit` int unsigned NOT NULL DEFAULT 0,
  `alpa` int unsigned NOT NULL DEFAULT 0,
  `gaji_pokok` bigint unsigned NOT NULL DEFAULT 0,
  `tunjangan` bigint unsigned NOT NULL DEFAULT 0,
  `bonus` bigint unsigned NOT NULL DEFAULT 0,
  `honor` bigint unsigned NOT NULL DEFAULT 0,
  `potongan_kehadiran` bigint unsigned NOT NULL DEFAULT 0,
  `potongan_lain` bigint unsigned NOT NULL DEFAULT 0,
  `total_pendapatan` bigint unsigned NOT NULL DEFAULT 0,
  `total_potongan` bigint unsigned NOT NULL DEFAULT 0,
  `gaji_bersih` bigint unsigned NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `disahkan_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penggajians_guru_id_bulan_tahun_unique` (`guru_id`,`bulan`,`tahun`),
  CONSTRAINT `penggajians_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data migrasi Laravel agar php artisan migrate tidak membuat tabel yang sama lagi
INSERT INTO `migrations` (`migration`,`batch`) VALUES
 ('0001_01_01_000000_create_users_table',1),
 ('0001_01_01_000001_create_cache_table',1),
 ('0001_01_01_000002_create_jobs_table',1),
 ('2026_10_12_000001_create_se_guru_tables',1);

-- Akun DEMO: sandi sama untuk semua akun = SeguruDemo!2026
-- Sandi disimpan sebagai bcrypt hash, bukan teks biasa.
INSERT INTO `users` (`name`,`email`,`email_verified_at`,`password`,`role`,`created_at`,`updated_at`) VALUES
 ('Administrator','admin@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','admin',NOW(),NOW()),
 ('Bendahara Sekolah','bendahara@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','bendahara',NOW(),NOW()),
 ('Ahmad Fauzi','guru@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','guru',NOW(),NOW()),
 ('Siti Aisyah','guru2@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','guru',NOW(),NOW()),
 ('Rizky Pratama','guru3@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','guru',NOW(),NOW()),
 ('Nurul Hidayah','guru4@seguru.test',NOW(),'$2y$12$vhbyiv5laNPH2TIkcr/16edB9Th43I1Tq7DapLZn4irfYSGznOWoC','guru',NOW(),NOW());

INSERT INTO `gurus` (`user_id`,`kode`,`nama`,`jenis_kelamin`,`telepon`,`jabatan`,`status_pegawai`,`tanggal_mulai`,`aktif`,`created_at`,`updated_at`) VALUES
 ((SELECT id FROM users WHERE email='guru@seguru.test'),'GR-001','Ahmad Fauzi','L','08XXXXXXXXXX','Guru Matematika','Tetap','2024-10-01',1,NOW(),NOW()),
 ((SELECT id FROM users WHERE email='guru2@seguru.test'),'GR-002','Siti Aisyah','P','08XXXXXXXXXX','Guru Bahasa Indonesia','Tetap','2024-10-01',1,NOW(),NOW()),
 ((SELECT id FROM users WHERE email='guru3@seguru.test'),'GR-003','Rizky Pratama','L','08XXXXXXXXXX','Guru IPA','Tetap','2024-10-01',1,NOW(),NOW()),
 ((SELECT id FROM users WHERE email='guru4@seguru.test'),'GR-004','Nurul Hidayah','P','08XXXXXXXXXX','Guru Bahasa Inggris','Tetap','2024-10-01',1,NOW(),NOW());

INSERT INTO `jadwals` (`guru_id`,`jam_masuk`,`jam_pulang`,`toleransi_menit`,`hari_kerja`,`created_at`,`updated_at`)
 SELECT `id`,'07:00:00','14:00:00',10,'1,2,3,4,5',NOW(),NOW() FROM `gurus`;

INSERT INTO `komponengajis` (`guru_id`,`gaji_pokok`,`tunjangan`,`bonus`,`honor`,`potongan_alpa`,`potongan_terlambat`,`potongan_lain`,`created_at`,`updated_at`)
 SELECT `id`,CASE `kode` WHEN 'GR-001' THEN 2500000 WHEN 'GR-002' THEN 2300000 WHEN 'GR-003' THEN 2200000 ELSE 2400000 END,
 350000,100000,150000,50000,10000,25000,NOW(),NOW() FROM `gurus`;

-- Data absensi & payroll belum diisi. Silakan menggunakan aplikasi untuk membuatnya.
-- Semua data pengguna/gaji di atas adalah CONTOH untuk demonstrasi tugas kuliah.

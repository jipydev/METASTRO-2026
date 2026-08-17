-- =============================================================================
-- METASTRO 2026: upgrade DB hosting (skema lama) -> skema app sekarang
-- Jalankan SEKALI di phpMyAdmin, database produksi, SETELAH backup.
-- Jangan dijalankan jika tabel `divisis` atau `kegiatans` sudah ada.
--
-- Yang dipertahankan: users (id, nim, email, password, foto, QR),
--                     rapat -> kegiatan, tap absen, pengajuan izin, pengumuman.
-- Yang dibuang: absensi/jadwal/hukuman/penilaian/role_requests (kosong / tidak dipakai).
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- -----------------------------------------------------------------------------
-- 0. Tabel lama yang tidak dipakai app baru
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `absensi`;
DROP TABLE IF EXISTS `hukuman`;
DROP TABLE IF EXISTS `penilaian`;
DROP TABLE IF EXISTS `jadwal`;
DROP TABLE IF EXISTS `role_requests`;
DROP TABLE IF EXISTS `model_has_permissions`;
DROP TABLE IF EXISTS `role_has_permissions`;

-- stub presensis lama hanya punya id/timestamps
DROP TABLE IF EXISTS `presensis`;

-- -----------------------------------------------------------------------------
-- 1. Divisi: nama_divisi -> nama, koordinator_divisi_nim -> koordinator_id
--    Divisi "Pengawas" (id 15) tidak ada di app baru: pindah ke Stakeholder
-- -----------------------------------------------------------------------------
UPDATE `users` SET `jabatan_id` = 6, `divisi_id` = 1 WHERE `divisi_id` = 15;
DELETE FROM `divisi` WHERE `id` = 15;

ALTER TABLE `divisi`
  DROP FOREIGN KEY `divisi_koordinator_divisi_nim_foreign`;

ALTER TABLE `divisi`
  CHANGE `nama_divisi` `nama` varchar(255) NOT NULL,
  CHANGE `koordinator_divisi_nim` `koordinator_id` bigint UNSIGNED NULL;

RENAME TABLE `divisi` TO `divisis`;

-- -----------------------------------------------------------------------------
-- 2. Jabatan: map ke nama yang dipakai app (Ketua / Wakil / Anggota / Pengawas)
-- -----------------------------------------------------------------------------
UPDATE `users` SET `jabatan_id` = 7 WHERE `jabatan_id` IN (1, 3);      -- Ketua Pelaksana / Koordinator -> Ketua
UPDATE `users` SET `jabatan_id` = 8 WHERE `jabatan_id` IN (2, 4);      -- Wakil Ketua Pelaksana / Wakil Koordinator -> Wakil
UPDATE `users` SET `jabatan_id` = 10 WHERE `jabatan_id` = 5;           -- Staff -> Anggota

DELETE FROM `jabatan` WHERE `id` IN (1, 2, 3, 4, 5);

ALTER TABLE `jabatan`
  CHANGE `nama_jabatan` `nama` varchar(100) NOT NULL;

RENAME TABLE `jabatan` TO `jabatans`;

-- -----------------------------------------------------------------------------
-- 3. Users
-- -----------------------------------------------------------------------------
ALTER TABLE `users`
  DROP FOREIGN KEY `users_divisi_id_foreign`,
  DROP FOREIGN KEY `users_jabatan_id_foreign`,
  DROP FOREIGN KEY `users_role_id_foreign`;

ALTER TABLE `users`
  CHANGE `name` `nama` varchar(255) NOT NULL,
  CHANGE `status_aktif` `status` tinyint(1) NOT NULL DEFAULT 1,
  DROP COLUMN `role_id`,
  DROP COLUMN `alamat`;

ALTER TABLE `users`
  MODIFY `jenis_kelamin` varchar(20) NULL;

UPDATE `users`
SET `jenis_kelamin` = CASE
    WHEN `jenis_kelamin` IN ('Laki-laki', 'laki-laki') THEN 'laki-laki'
    WHEN `jenis_kelamin` IN ('Perempuan', 'perempuan') THEN 'perempuan'
    ELSE NULL
END;

ALTER TABLE `users`
  MODIFY `jenis_kelamin` enum('laki-laki', 'perempuan') NULL;

ALTER TABLE `users`
  ADD COLUMN `qr_updated_at` timestamp NULL AFTER `qr_token`;

UPDATE `users` SET `qr_updated_at` = `updated_at` WHERE `qr_token` IS NOT NULL;

-- -----------------------------------------------------------------------------
-- 4. Rapat -> kegiatan (ID tetap, biar presensi/izin nyambung)
-- -----------------------------------------------------------------------------
CREATE TABLE `kegiatans` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text NULL,
  `tipe` varchar(50) NOT NULL DEFAULT 'rapat',
  `tempat` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NULL,
  `presensi_mulai` datetime NULL,
  `presensi_selesai` datetime NULL,
  `presensi_dibuka_pada` datetime NULL,
  `presensi_ditutup_pada` datetime NULL,
  `created_by` bigint UNSIGNED NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `kegiatans_waktu_selesai_index` (`waktu_selesai`),
  KEY `kegiatans_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kegiatans` (
  `id`, `nama`, `deskripsi`, `tipe`, `tempat`, `tanggal`, `waktu_mulai`, `waktu_selesai`,
  `presensi_mulai`, `presensi_selesai`, `created_by`, `created_at`, `updated_at`
)
SELECT
  `id`,
  `judul`,
  NULL,
  'rapat',
  `tempat`,
  `tanggal`,
  `jam`,
  NULL,
  CASE
    WHEN `waktu_buka` IS NOT NULL THEN TIMESTAMP(`tanggal`, `waktu_buka`)
    ELSE TIMESTAMP(`tanggal`, `jam`)
  END,
  CASE
    WHEN `waktu_tutup` IS NOT NULL THEN TIMESTAMP(`tanggal`, `waktu_tutup`)
    ELSE TIMESTAMP(`tanggal`, ADDTIME(`jam`, '04:00:00'))
  END,
  NULL,
  `created_at`,
  `updated_at`
FROM `rapats`;

-- -----------------------------------------------------------------------------
-- 5. list_panitias -> presensis
-- -----------------------------------------------------------------------------
CREATE TABLE `presensis` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `kegiatan_id` bigint UNSIGNED NOT NULL,
  `pengajuan_izin_id` bigint UNSIGNED NULL,
  `scanned_by` bigint UNSIGNED NULL,
  `jam_tap` timestamp NULL,
  `status` enum('hadir', 'izin', 'sakit', 'alpa') NOT NULL DEFAULT 'hadir',
  `keterangan` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `presensis_user_id_kegiatan_id_unique` (`user_id`, `kegiatan_id`),
  KEY `presensis_kegiatan_id_foreign` (`kegiatan_id`),
  KEY `presensis_pengajuan_izin_id_foreign` (`pengajuan_izin_id`),
  KEY `presensis_scanned_by_foreign` (`scanned_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `presensis` (
  `id`, `user_id`, `kegiatan_id`, `pengajuan_izin_id`, `scanned_by`,
  `jam_tap`, `status`, `keterangan`, `created_at`, `updated_at`
)
SELECT
  lp.`id`,
  lp.`user_id`,
  lp.`rapat_id`,
  NULL,
  lp.`scanned_by`,
  TIMESTAMP(r.`tanggal`, lp.`jam_tap`),
  CASE LOWER(lp.`status`)
    WHEN 'hadir' THEN 'hadir'
    WHEN 'telat' THEN 'hadir'
    WHEN 'izin' THEN 'izin'
    WHEN 'sakit' THEN 'sakit'
    WHEN 'alpha' THEN 'alpa'
    WHEN 'alpa' THEN 'alpa'
    ELSE 'hadir'
  END,
  CASE WHEN LOWER(lp.`status`) = 'telat' THEN 'Telat' ELSE NULL END,
  lp.`created_at`,
  lp.`updated_at`
FROM `list_panitias` lp
INNER JOIN `rapats` r ON r.`id` = lp.`rapat_id`;

-- -----------------------------------------------------------------------------
-- 6. pengajuan_izin -> pengajuan_izins
-- -----------------------------------------------------------------------------
ALTER TABLE `pengajuan_izin`
  DROP FOREIGN KEY `pengajuan_izin_rapat_id_foreign`,
  DROP FOREIGN KEY `pengajuan_izin_reviewed_by_koordinator_foreign`,
  DROP FOREIGN KEY `pengajuan_izin_reviewed_by_ranger_foreign`,
  DROP FOREIGN KEY `pengajuan_izin_user_id_foreign`;

ALTER TABLE `pengajuan_izin`
  CHANGE `rapat_id` `kegiatan_id` bigint UNSIGNED NULL;

ALTER TABLE `pengajuan_izin`
  MODIFY `jenis_izin` varchar(20) NOT NULL,
  MODIFY `status_koordinator` varchar(20) NOT NULL DEFAULT 'pending',
  MODIFY `status_ranger` varchar(20) NOT NULL DEFAULT 'pending',
  MODIFY `status` varchar(20) NOT NULL DEFAULT 'pending';

UPDATE `pengajuan_izin` SET
  `jenis_izin` = LOWER(`jenis_izin`),
  `status_koordinator` = LOWER(`status_koordinator`),
  `status_ranger` = LOWER(`status_ranger`),
  `status` = LOWER(`status`);

ALTER TABLE `pengajuan_izin`
  MODIFY `jenis_izin` enum('sakit', 'izin') NOT NULL DEFAULT 'izin',
  MODIFY `status_koordinator` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  MODIFY `status_ranger` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  MODIFY `status` enum('pending', 'diproses', 'approved', 'rejected') NOT NULL DEFAULT 'pending';

RENAME TABLE `pengajuan_izin` TO `pengajuan_izins`;

-- -----------------------------------------------------------------------------
-- 7. pengumuman -> pengumumans
-- -----------------------------------------------------------------------------
ALTER TABLE `pengumuman`
  DROP FOREIGN KEY `pengumuman_pembuat_id_foreign`;

ALTER TABLE `pengumuman`
  ADD COLUMN `target` enum('semua', 'panitia', 'peserta') NOT NULL DEFAULT 'panitia' AFTER `lampiran`;

ALTER TABLE `pengumuman`
  MODIFY `status` varchar(20) NOT NULL DEFAULT 'draft';

UPDATE `pengumuman` SET
  `status` = CASE
    WHEN LOWER(`status`) IN ('publish', 'published') THEN 'published'
    ELSE 'draft'
  END;

ALTER TABLE `pengumuman`
  MODIFY `status` enum('draft', 'published') NOT NULL DEFAULT 'draft';

RENAME TABLE `pengumuman` TO `pengumumans`;

-- -----------------------------------------------------------------------------
-- 8. notulensi -> notulensis
-- -----------------------------------------------------------------------------
ALTER TABLE `notulensi`
  DROP FOREIGN KEY `notulensi_pembuat_id_foreign`;

ALTER TABLE `notulensi`
  CHANGE `isi_notulensi` `isi` longtext NULL,
  DROP COLUMN `keputusan_rapat`,
  DROP COLUMN `tindak_lanjut`,
  ADD COLUMN `kegiatan_id` bigint UNSIGNED NULL AFTER `id`;

RENAME TABLE `notulensi` TO `notulensis`;

-- -----------------------------------------------------------------------------
-- 9. Roles Spatie: Admin/Panitia/Peserta -> lowercase, role lain jadi panitia
-- -----------------------------------------------------------------------------
UPDATE `model_has_roles` SET `role_id` = 2 WHERE `role_id` IN (4, 5, 6, 7, 8);
DELETE FROM `roles` WHERE `id` IN (4, 5, 6, 7, 8);

UPDATE `roles` SET `name` = 'admin' WHERE `id` = 1;
UPDATE `roles` SET `name` = 'panitia' WHERE `id` = 2;
UPDATE `roles` SET `name` = 'peserta' WHERE `id` = 3;

-- Chiper = admin di app baru
INSERT IGNORE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
SELECT 1, 'App\\Models\\User', u.`id`
FROM `users` u
INNER JOIN `divisis` d ON d.`id` = u.`divisi_id`
WHERE d.`nama` = 'Chiper';

DELETE FROM `permissions`;

-- -----------------------------------------------------------------------------
-- 10. Notifications (baru)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. Foreign keys sesuai skema baru
-- -----------------------------------------------------------------------------
ALTER TABLE `divisis`
  ADD CONSTRAINT `divisis_koordinator_id_foreign`
    FOREIGN KEY (`koordinator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `users`
  ADD CONSTRAINT `users_divisi_id_foreign`
    FOREIGN KEY (`divisi_id`) REFERENCES `divisis` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_jabatan_id_foreign`
    FOREIGN KEY (`jabatan_id`) REFERENCES `jabatans` (`id`) ON DELETE SET NULL;

ALTER TABLE `kegiatans`
  ADD CONSTRAINT `kegiatans_created_by_foreign`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `pengajuan_izins`
  ADD CONSTRAINT `pengajuan_izins_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengajuan_izins_kegiatan_id_foreign`
    FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuan_izins_reviewed_by_koordinator_foreign`
    FOREIGN KEY (`reviewed_by_koordinator`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuan_izins_reviewed_by_ranger_foreign`
    FOREIGN KEY (`reviewed_by_ranger`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `presensis`
  ADD CONSTRAINT `presensis_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensis_kegiatan_id_foreign`
    FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensis_pengajuan_izin_id_foreign`
    FOREIGN KEY (`pengajuan_izin_id`) REFERENCES `pengajuan_izins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `presensis_scanned_by_foreign`
    FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `pengumumans`
  ADD CONSTRAINT `pengumumans_pembuat_id_foreign`
    FOREIGN KEY (`pembuat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `notulensis`
  ADD CONSTRAINT `notulensis_pembuat_id_foreign`
    FOREIGN KEY (`pembuat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notulensis_kegiatan_id_foreign`
    FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatans` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 12. Buang tabel sumber yang sudah dipindah
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `list_panitias`;
DROP TABLE IF EXISTS `rapats`;

-- -----------------------------------------------------------------------------
-- 13. Catat migrations app baru supaya `php artisan migrate` tidak bikin tabel ulang
-- -----------------------------------------------------------------------------
DELETE FROM `migrations`;
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_divisis_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000001_create_jabatans_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('0001_01_01_000002_create_users_table', 1),
('2024_01_01_000000_create_passkeys_table', 1),
('2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
('2026_07_17_032738_create_permission_tables', 1),
('2026_07_17_033000_create_kegiatans_table', 1),
('2026_07_17_033010_create_pengumumans_table', 1),
('2026_07_17_033041_create_notulensis_table', 1),
('2026_07_31_142250_create_pengajuan_izins_table', 1),
('2026_07_31_142257_create_presensis_table', 1),
('2026_08_05_213500_create_notifications_table', 1),
('2026_08_13_000001_add_email_to_password_reset_tokens_table', 1),
('2026_08_15_130656_add_qr_updated_at_to_users_table', 1),
('2026_08_17_011054_add_presensi_schedule_to_kegiatans_table', 1),
('2026_08_17_013239_change_presensi_columns_to_datetime_in_kegiatans_table', 1),
('2026_08_17_073937_drop_status_presensi_from_kegiatans_table', 1),
('2026_08_17_105500_add_surat_izin_to_pengajuan_izins_table', 1);

SET FOREIGN_KEY_CHECKS = 1;

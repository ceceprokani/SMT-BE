/*
 Navicat Premium Data Transfer

 Source Server         : Server Tugasin
 Source Server Type    : MySQL
 Source Server Version : 90200
 Source Host           : autorack.proxy.rlwy.net:52258
 Source Schema         : railway

 Target Server Type    : MySQL
 Target Server Version : 90200
 File Encoding         : 65001

 Date: 14/04/2025 11:26:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for authentication_log
-- ----------------------------
DROP TABLE IF EXISTS `authentication_log`;
CREATE TABLE `authentication_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `users_id` int NULL DEFAULT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `method` enum('normal','secretkey','unknown') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `device` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `time` datetime NULL DEFAULT NULL,
  `status` enum('success','wrong_password','critical_failure') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `type` enum('signin','signout','change_password','edit_profile','twofactor') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `authentication_log_ibfk_1`(`users_id` ASC) USING BTREE,
  CONSTRAINT `authentication_log_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of authentication_log
-- ----------------------------
INSERT INTO `authentication_log` VALUES (1, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:42:29', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (2, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:43:33', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (3, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:44:26', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (4, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:44:31', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (5, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:45:53', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (6, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:47:59', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (7, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:50:36', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (8, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:52:02', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (9, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:54:21', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (10, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:56:03', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (11, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 01:56:10', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (12, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 02:00:32', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (13, 2, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 02:01:50', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (14, 2, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 02:01:53', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (15, 3, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 02:02:10', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (18, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 04:42:51', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (19, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 14:16:22', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (20, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 14:24:13', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (21, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 14:26:16', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (22, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '132.0.0.0', '2025-02-09 14:26:23', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (23, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-18 14:31:02', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (24, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-18 14:31:34', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (25, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-18 14:34:30', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (26, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-18 14:34:51', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (27, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-18 14:52:00', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (28, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-24 10:13:40', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (29, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-24 10:45:46', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (30, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-24 11:12:18', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (31, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-24 11:15:56', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (32, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-26 10:06:48', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (33, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '133.0.0.0', '2025-02-27 16:25:22', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (34, 2, '114.10.11.162', 'normal', 'Mobile', 'iOS', 'Safari', '15.5', '2025-03-18 03:54:27', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (35, 2, '103.113.50.210', 'normal', 'Desktop', 'Windows', 'Chrome', '134.0.0.0', '2025-03-18 05:41:50', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (36, 2, '103.113.50.210', 'normal', 'Desktop', 'Windows', 'Chrome', '134.0.0.0', '2025-03-18 05:41:59', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (37, 2, '103.113.50.210', 'normal', 'Desktop', 'Windows', 'Chrome', '134.0.0.0', '2025-03-18 05:42:08', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (38, 1, '103.113.50.210', 'normal', 'Desktop', 'Windows', 'Chrome', '134.0.0.0', '2025-03-18 05:42:46', 'success', 'signin', NULL);

-- ----------------------------
-- Table structure for pembayaran
-- ----------------------------
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaksi_id` int NULL DEFAULT NULL,
  `total` double NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `bukti` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `tipe_pembayaran` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `transaksi_id`(`transaksi_id` ASC) USING BTREE,
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pembayaran
-- ----------------------------

-- ----------------------------
-- Table structure for setting_harga_air
-- ----------------------------
DROP TABLE IF EXISTS `setting_harga_air`;
CREATE TABLE `setting_harga_air`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `rw` int NULL DEFAULT NULL,
  `harga_air` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `jatuh_tempo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `beban` double NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of setting_harga_air
-- ----------------------------
INSERT INTO `setting_harga_air` VALUES (2, 1, '[{\"price\":4000,\"limit\":10},{\"price\":5000,\"limit\":20},{\"price\":6000,\"limit\":100},{\"price\":10000,\"limit\":250}]', '25', 10000, '2025-02-24 13:14:37', '2025-02-26 12:01:47');
INSERT INTO `setting_harga_air` VALUES (3, 2, '[{\"price\":1000,\"limit\":100},{\"price\":1500,\"limit\":150},{\"price\":2000,\"limit\":200},{\"price\":2500,\"limit\":250}]', '25', 3000, '2025-02-24 13:16:02', NULL);

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `value` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings
-- ----------------------------

-- ----------------------------
-- Table structure for transaksi
-- ----------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelanggan_id` int NULL DEFAULT NULL,
  `meteran_awal` int NULL DEFAULT NULL,
  `meteran_akhir` int NULL DEFAULT NULL,
  `petugas_id` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `total_tagihan` double NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pelanggan_id`(`pelanggan_id` ASC) USING BTREE,
  INDEX `petugas_id`(`petugas_id` ASC) USING BTREE,
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `users_pelanggan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users_petugas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transaksi
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password_raw` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `role` enum('superadmin','admin','staff','customer') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'superadmin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$d2tyUGJqVTBjN3lhWkpDWQ$vLUWyX3kxFco0TMecdk83eH7LQWYhB9eieZonrx9JvQ', 'superadmin', 'superadmin', '2025-02-09 01:24:54', '2025-02-18 15:02:01');
INSERT INTO `users` VALUES (2, 'admin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$SGtUVzlaak1yR1pKTGVaZA$xFFua8mtmeve3biEJuSFvFPY0PQyaiM6vQTEMUab5Ks', 'admin', 'admin', '2025-02-09 01:24:54', '2025-02-18 15:02:04');
INSERT INTO `users` VALUES (3, 'petugas@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$cXprd2RlOHN5cC9VenYvYw$DwvTpzDTUvKV3PDgk/10w9DQftwYm1yzdA0Btocrosg', 'petugas', 'staff', '2025-02-09 01:24:54', '2025-02-18 15:02:06');
INSERT INTO `users` VALUES (19, 'petugasrw02@gmail.com', '$2y$10$yh9K03HtL1DPuj2HJZ9ZxOsaV8Mqm0O0pI.mOqnNdK54k8TfQeIYW', '123123123', 'staff', '2025-02-18 15:58:45', NULL);
INSERT INTO `users` VALUES (20, 'adminrw02@gmail.com', '$2y$10$C5eVmypRUpl84MCCfDpjfOf4rp32ABHBK.gvucTnam4yldLvVAbvm', '123123123', 'admin', '2025-02-18 16:23:36', NULL);
INSERT INTO `users` VALUES (22, '1312123@gmail.com', '$2y$10$nPuDeNfSty43RRLhDt0leu4miEQSPvD3hhMVKtKXNxti9hidQrUjS', 'abcd1234', 'customer', '2025-02-24 11:00:35', '2025-02-24 11:01:04');
INSERT INTO `users` VALUES (24, 'yuni@gmail.com', '$2y$10$w2a0PeJkhFOeTpUkr74CQOMko795IRQzn7SuFKZrpXSaQEASjWXka', 'abcd1234', 'customer', '2025-02-24 14:19:35', NULL);
INSERT INTO `users` VALUES (25, 'pelanggan03@gmail.com', '$2y$10$5C0xXzIIwmdbrELufOA/z.yKyVgjf1dZfIui4n9ZY2h3ugi/yCsSm', '123123123', 'customer', '2025-02-26 10:27:04', NULL);

-- ----------------------------
-- Table structure for users_pelanggan
-- ----------------------------
DROP TABLE IF EXISTS `users_pelanggan`;
CREATE TABLE `users_pelanggan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `rw` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_pel` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `no_rek` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` enum('active','non-active') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'active',
  `category_id` int NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `category_id`(`category_id` ASC) USING BTREE,
  CONSTRAINT `users_pelanggan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `users_pelanggan_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `users_pelanggan_category` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users_pelanggan
-- ----------------------------
INSERT INTO `users_pelanggan` VALUES (2, 22, 'pelanggan 1', '1312123@gmail.com', '6291283917419', 'TEST', '1', '41029301842', '5588585', 'active', 1, '2025-02-24 11:00:35', '2025-02-24 14:24:31');
INSERT INTO `users_pelanggan` VALUES (4, 24, 'Yuni', 'yuni@gmail.com', '6287282374223', 'Jl. Sukahaji', '1', '5482301294', '1239482394', 'active', 1, '2025-02-24 14:19:35', '2025-02-24 14:24:36');
INSERT INTO `users_pelanggan` VALUES (5, 25, 'Pelanggan RW 2', 'pelanggan03@gmail.com', '6284827382472', 'Jl. Sesama', '2', '12039124012039', '123123124123', 'active', 1, '2025-02-26 10:27:04', NULL);

-- ----------------------------
-- Table structure for users_pelanggan_category
-- ----------------------------
DROP TABLE IF EXISTS `users_pelanggan_category`;
CREATE TABLE `users_pelanggan_category`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users_pelanggan_category
-- ----------------------------
INSERT INTO `users_pelanggan_category` VALUES (1, 'R1', 'R1', '2025-02-24 13:56:43', '2025-02-24 13:56:46');
INSERT INTO `users_pelanggan_category` VALUES (2, 'R2', 'R2', '2025-02-24 13:56:43', '2025-02-24 13:56:47');

-- ----------------------------
-- Table structure for users_petugas
-- ----------------------------
DROP TABLE IF EXISTS `users_petugas`;
CREATE TABLE `users_petugas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `rw` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `users_petugas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users_petugas
-- ----------------------------
INSERT INTO `users_petugas` VALUES (1, 1, 'Super Admin', 'superadmin@gmail.com', '08782739284', 'Jl. Panembangan', '1', '2025-02-09 01:43:23', NULL);
INSERT INTO `users_petugas` VALUES (2, 2, 'Admin', 'admin@gmail.com', '08984529324', 'Jl. Bahagia', '1', '2025-02-09 01:43:23', NULL);
INSERT INTO `users_petugas` VALUES (3, 3, 'Petugas Lapangan', 'petugas@gmail.com', '089238492384', 'Jl. Cinta', '1', '2025-02-09 01:43:23', NULL);
INSERT INTO `users_petugas` VALUES (15, 19, 'Petugas RW 02', 'petugasrw02@gmail.com', '6289237428342', 'Jl. Bahagia II', '1', '2025-02-18 15:58:45', '2025-02-18 16:11:07');
INSERT INTO `users_petugas` VALUES (16, 20, 'Admin RW 02', 'adminrw02@gmail.com', '6284923894823', 'Jl. Cinta', '2', '2025-02-18 16:23:36', NULL);

SET FOREIGN_KEY_CHECKS = 1;

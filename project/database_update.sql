-- ============================================================
-- database_update.sql
-- Jalankan di phpMyAdmin atau CLI:
--   mysql -u root toko_sayur2 < database_update.sql
-- ============================================================

USE `toko_sayur2`;

-- 1. Tambah kolom payment_proof untuk menyimpan path bukti transfer
ALTER TABLE `orders`
  ADD COLUMN `payment_proof` varchar(255) DEFAULT NULL AFTER `payment_method`;

-- 2. Extend enum status: tambah 'valid' dan 'ditolak'
ALTER TABLE `orders`
  MODIFY COLUMN `status` enum('pending','processing','shipped','delivered','cancelled','valid','ditolak') NOT NULL DEFAULT 'pending';

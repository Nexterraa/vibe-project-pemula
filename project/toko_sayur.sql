-- ============================================================
-- Toko Sayur Online - Database Schema & Seed Data
-- Import via phpMyAdmin atau: mysql -u root -p < toko_sayur2.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- Buat database
DROP DATABASE IF EXISTS `toko_sayur2`;
CREATE DATABASE IF NOT EXISTS `toko_sayur2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `toko_sayur2`;

-- --------------------------------------------------------
-- Tabel: categories
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: products
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(30) NOT NULL DEFAULT 'kg',
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: product_ratings
-- --------------------------------------------------------
CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`product_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_rating_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: orders
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping_name` varchar(150) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` enum('cod','transfer') NOT NULL DEFAULT 'cod',
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','valid','ditolak') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel: order_items
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(14,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin default: admin@tokosayur.com / admin123
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `role`) VALUES
('Administrator', 'admin@tokosayur.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'Jl. Admin No. 1, Jakarta', 'admin'),
('Budi Santoso', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'Jl. Mawar No. 5, Bogor', 'customer'),
('Siti Rahayu', 'siti@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'Jl. Melati No. 10, Depok', 'customer');
-- Password semua akun di atas: password

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Sayuran Hijau', 'sayuran-hijau', 'Bayam, kangkung, sawi, dan sayuran daun hijau segar lainnya'),
('Umbi-Umbian', 'umbi-umbian', 'Wortel, kentang, singkong, ubi, dan umbi-umbian berkualitas'),
('Buah Sayur', 'buah-sayur', 'Tomat, cabai, paprika, terong, dan buah-sayur pilihan'),
('Kacang-Kacangan', 'kacang-kacangan', 'Buncis, kacang panjang, edamame, dan kacang-kacangan segar'),
('Jamur & Rempah', 'jamur-rempah', 'Jamur tiram, jahe, kunyit, lengkuas, dan rempah-rempah pilihan');

-- Products
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `price`, `stock`, `unit`, `image`, `is_featured`, `is_active`) VALUES
(1, 'Bayam Hijau Segar', 'bayam-hijau-segar', 'Bayam hijau organik pilihan, dipanen segar dari kebun. Kaya akan zat besi dan vitamin A. Cocok untuk tumisan, sup, dan salad.', 5000, 50, 'ikat', 'bayam.jpg', 1, 1),
(1, 'Kangkung Segar', 'kangkung-segar', 'Kangkung segar berkualitas tinggi, cocok untuk tumis kangkung atau pecel. Dipanen setiap hari untuk menjaga kesegaran.', 4000, 60, 'ikat', 'kangkung.jpg', 1, 1),
(1, 'Sawi Hijau', 'sawi-hijau', 'Sawi hijau segar pilihan, cocok untuk berbagai olahan masakan. Tekstur renyah dan rasanya tidak pahit.', 5000, 40, 'ikat', 'sawi.jpg', 0, 1),
(1, 'Selada Keriting', 'selada-keriting', 'Selada keriting segar untuk salad dan pelengkap burger. Dipetik langsung dari kebun hidroponik organik.', 8000, 30, 'pack', 'selada.jpg', 1, 1),
(2, 'Wortel Lokal Premium', 'wortel-lokal-premium', 'Wortel lokal segar ukuran besar, manis dan renyah. Kaya beta-karoten dan vitamin A. Cocok untuk sup, jus, atau acar.', 12000, 45, 'kg', 'wortel.jpg', 1, 1),
(2, 'Kentang Granola', 'kentang-granola', 'Kentang granola kualitas premium dari Dieng. Tekstur padat dan tidak mudah hancur saat dimasak. Cocok untuk perkedel, sup, atau kentang goreng.', 15000, 55, 'kg', 'kentang.jpg', 1, 1),
(2, 'Ubi Jalar Ungu', 'ubi-jalar-ungu', 'Ubi jalar ungu manis alami, kaya antioksidan dan serat. Cocok untuk berbagai olahan kue, kolak, atau dimakan langsung.', 10000, 35, 'kg', 'ubi.jpg', 0, 1),
(3, 'Tomat Merah Segar', 'tomat-merah-segar', 'Tomat merah segar berkualitas tinggi, manis dan segar. Cocok untuk sambal, saus, sup, atau dimakan langsung sebagai lalapan.', 8000, 70, 'kg', 'tomat.jpg', 1, 1),
(3, 'Cabai Merah Besar', 'cabai-merah-besar', 'Cabai merah besar segar, tingkat kepedasan sedang. Cocok untuk bumbu masak, sambal, dan aneka masakan pedas favorit.', 25000, 25, 'kg', 'cabai.jpg', 0, 1),
(3, 'Paprika Merah', 'paprika-merah', 'Paprika merah importir berkualitas, manis dan renyah. Cocok untuk tumisan, pizza, salad, atau dimakan langsung.', 30000, 20, 'kg', 'paprika.jpg', 1, 1),
(3, 'Terong Ungu', 'terong-ungu', 'Terong ungu segar dan mulus, cocok untuk balado, tumis, atau digoreng tepung. Dipilih dari hasil panen terbaik.', 7000, 40, 'kg', 'terong.jpg', 0, 1),
(4, 'Buncis Segar', 'buncis-segar', 'Buncis segar renyah, cocok untuk tumisan atau sup. Kaya serat dan vitamin K yang baik untuk kesehatan tulang.', 10000, 30, 'kg', 'buncis.jpg', 0, 1),
(4, 'Kacang Panjang', 'kacang-panjang', 'Kacang panjang segar dan muda, tekstur renyah. Cocok untuk tumisan, pecel, gado-gado, atau lodeh.', 8000, 45, 'ikat', 'kacang_panjang.jpg', 0, 1),
(5, 'Jamur Tiram Putih', 'jamur-tiram-putih', 'Jamur tiram putih segar, kaya protein nabati. Cocok untuk tumisan, sup, atau digoreng tepung sebagai camilan sehat.', 15000, 30, 'pack', 'jamur_tiram.jpg', 1, 1),
(5, 'Jahe Merah Segar', 'jahe-merah-segar', 'Jahe merah organik segar, aroma kuat dan berkhasiat tinggi. Bagus untuk minuman herbal, jamu, atau bumbu masakan.', 20000, 25, 'kg', 'jahe.jpg', 0, 1);

-- Sample orders
INSERT INTO `orders` (`order_code`,`user_id`,`total_amount`,`shipping_fee`,`grand_total`,`shipping_name`,`shipping_phone`,`shipping_address`,`payment_method`,`status`) VALUES
('ORD-20260301-001', 2, 45000, 10000, 55000, 'Budi Santoso', '081234567891', 'Jl. Mawar No. 5, Bogor', 'cod', 'delivered'),
('ORD-20260302-001', 3, 62000, 10000, 72000, 'Siti Rahayu', '081234567892', 'Jl. Melati No. 10, Depok', 'transfer', 'processing');

INSERT INTO `order_items` (`order_id`,`product_id`,`product_name`,`price`,`quantity`,`subtotal`) VALUES
(1, 1, 'Bayam Hijau Segar', 5000, 3, 15000),
(1, 5, 'Wortel Lokal Premium', 12000, 1, 12000),
(1, 8, 'Tomat Merah Segar', 8000, 1, 8000),
(1, 6, 'Kentang Granola', 15000, 2, 10000),
(2, 10, 'Paprika Merah', 30000, 1, 30000),
(2, 14, 'Jamur Tiram Putih', 15000, 2, 32000);

-- Sample ratings
INSERT INTO `product_ratings` (`product_id`,`user_id`,`rating`,`review`) VALUES
(1, 2, 5, 'Bayamnya segar banget! Pengiriman cepat dan packagingnya rapi.'),
(5, 2, 4, 'Wortel besar-besar dan manis. Recommended!'),
(10, 3, 5, 'Paprikanya segar dan manis. Puas banget belanja disini!');

COMMIT;

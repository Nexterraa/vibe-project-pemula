<?php
// ============================================================
// config/koneksi.php - Koneksi Database
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'toko_sayur2');
define('DB_CHARSET', 'utf8mb4');

// Base URL
define('BASE_URL', 'http://localhost/project');

// Upload path
define('UPLOAD_PATH', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');

// Default product image
define('DEFAULT_IMAGE', BASE_URL . '/assets/images/default-product.png');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;border-radius:8px;margin:20px;">
            <h3>❌ Koneksi Database Gagal</h3>
            <p>' . htmlspecialchars($e->getMessage()) . '</p>
            <p>Pastikan XAMPP MySQL sudah aktif dan database <strong>toko_sayur</strong> sudah diimport.</p>
         </div>');
}

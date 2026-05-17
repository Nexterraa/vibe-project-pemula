<?php
// upload_transfer_handler.php - Proses Upload Bukti Transfer
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL);
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
if (!$orderId) {
    setFlash('danger', 'Order tidak valid.');
    header('Location: ' . BASE_URL);
    exit;
}

// Pastikan order milik user ini dan metode = transfer
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? AND payment_method = "transfer"');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan.');
    header('Location: ' . BASE_URL);
    exit;
}

// Validasi file upload
if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
    setFlash('danger', 'Gagal mengupload file. Silakan coba lagi.');
    header('Location: ' . BASE_URL . '/upload_transfer.php?order_id=' . $orderId);
    exit;
}

$file = $_FILES['payment_proof'];
$maxSize = 5 * 1024 * 1024; // 5MB
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

// Validasi tipe file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    setFlash('danger', 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
    header('Location: ' . BASE_URL . '/upload_transfer.php?order_id=' . $orderId);
    exit;
}

if ($file['size'] > $maxSize) {
    setFlash('danger', 'Ukuran file terlalu besar. Maksimal 5MB.');
    header('Location: ' . BASE_URL . '/upload_transfer.php?order_id=' . $orderId);
    exit;
}

// Buat folder jika belum ada
$uploadDir = __DIR__ . '/uploads/transfer/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate nama file unik
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'transfer_' . $order['order_code'] . '_' . time() . '.' . strtolower($ext);
$destination = $uploadDir . $filename;

// Pindahkan file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    setFlash('danger', 'Gagal menyimpan file. Silakan coba lagi.');
    header('Location: ' . BASE_URL . '/upload_transfer.php?order_id=' . $orderId);
    exit;
}

// Update database
try {
    $stmt = $pdo->prepare('UPDATE orders SET payment_proof = ? WHERE id = ?');
    $stmt->execute([$filename, $orderId]);
    
    // Set session untuk order_success
    $_SESSION['last_order_code'] = $order['order_code'];
    $_SESSION['last_order_id'] = $order['id'];
    
    setFlash('success', 'Bukti transfer berhasil diupload! Menunggu verifikasi admin.');
    header('Location: ' . BASE_URL . '/order_success.php');
    exit;
} catch (Exception $e) {
    // Hapus file jika gagal update DB
    if (file_exists($destination)) unlink($destination);
    setFlash('danger', 'Gagal menyimpan data: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/upload_transfer.php?order_id=' . $orderId);
    exit;
}

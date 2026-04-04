<?php
// admin/orders/update_status.php - Update Status Pesanan
require_once __DIR__ . '/../../config/functions.php';
startSession();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/orders/index.php');
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$status  = $_POST['status'] ?? '';
$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'valid', 'ditolak'];

if (!$orderId || !in_array($status, $validStatuses)) {
    setFlash('danger', 'Data tidak valid.');
    header('Location: ' . BASE_URL . '/admin/orders/index.php');
    exit;
}

// Pastikan order ada
$stmt = $pdo->prepare('SELECT id FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
if (!$stmt->fetch()) {
    setFlash('danger', 'Pesanan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/admin/orders/index.php');
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$status, $orderId]);
    
    $statusLabels = [
        'pending' => 'Pending',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Diterima',
        'cancelled' => 'Dibatalkan',
        'valid' => 'Valid',
        'ditolak' => 'Ditolak',
    ];
    $label = $statusLabels[$status] ?? $status;
    
    setFlash('success', "Status pesanan berhasil diubah menjadi \"{$label}\".");
} catch (Exception $e) {
    setFlash('danger', 'Gagal mengubah status: ' . $e->getMessage());
}

header('Location: ' . BASE_URL . '/admin/orders/detail.php?id=' . $orderId);
exit;

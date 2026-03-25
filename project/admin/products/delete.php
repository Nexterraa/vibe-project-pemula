<?php
// admin/products/delete.php
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('SELECT image FROM products WHERE id=?'); $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists(UPLOAD_PATH . $img)) @unlink(UPLOAD_PATH . $img);
    $pdo->prepare('UPDATE products SET is_active=0 WHERE id=?')->execute([$id]);
    setFlash('success', 'Produk berhasil dihapus.');
} else {
    setFlash('danger', 'ID produk tidak valid.');
}
header('Location: ' . BASE_URL . '/admin/products/index.php'); exit;

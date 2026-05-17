<?php
// admin/categories/delete.php
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
    setFlash('success','Kategori berhasil dihapus.');
} else { setFlash('danger','ID tidak valid.'); }
header('Location:'.BASE_URL.'/admin/categories/index.php'); exit;

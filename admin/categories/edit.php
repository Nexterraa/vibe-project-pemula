<?php
// admin/categories/edit.php
$pageTitle = 'Edit Kategori';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id=?'); $stmt->execute([$id]);
$cat = $stmt->fetch();
if (!$cat) { setFlash('danger','Kategori tidak ditemukan.'); header('Location:'.BASE_URL.'/admin/categories/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if (empty($name)) { setFlash('danger','Nama wajib diisi.'); }
    else {
        $pdo->prepare('UPDATE categories SET name=?, description=? WHERE id=?')->execute([$name, $desc, $id]);
        setFlash('success','Kategori berhasil diperbarui!');
        header('Location:'.BASE_URL.'/admin/categories/index.php'); exit;
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-edit me-2 text-success"></i>Edit Kategori</h4>
  <a href="<?= BASE_URL ?>/admin/categories/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>
<div class="admin-card p-4" style="max-width:600px;">
  <form method="POST">
    <div class="mb-3"><label class="form-label fw-600">Nama Kategori *</label><input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required></div>
    <div class="mb-3"><label class="form-label fw-600">Deskripsi</label><textarea name="description" class="form-control" rows="3"><?= e($cat['description']) ?></textarea></div>
    <button type="submit" class="btn btn-success-custom px-5 fw-700"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

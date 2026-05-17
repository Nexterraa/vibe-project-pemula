<?php
// admin/categories/add.php
$pageTitle = 'Tambah Kategori';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if (empty($name)) { setFlash('danger','Nama kategori wajib diisi.'); }
    else {
        $sl = slug($name);
        $chk = $pdo->prepare('SELECT id FROM categories WHERE slug=?'); $chk->execute([$sl]);
        if ($chk->fetch()) $sl .= '-' . time();
        $pdo->prepare('INSERT INTO categories (name,slug,description) VALUES (?,?,?)')->execute([$name,$sl,$desc]);
        setFlash('success','Kategori berhasil ditambahkan!');
        header('Location:'.BASE_URL.'/admin/categories/index.php'); exit;
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-plus me-2 text-success"></i>Tambah Kategori</h4>
  <a href="<?= BASE_URL ?>/admin/categories/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>
<div class="admin-card p-4" style="max-width:600px;">
  <form method="POST">
    <div class="mb-3"><label class="form-label fw-600">Nama Kategori *</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label fw-600">Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
    <button type="submit" class="btn btn-success-custom px-5 fw-700"><i class="fas fa-save me-2"></i>Simpan</button>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

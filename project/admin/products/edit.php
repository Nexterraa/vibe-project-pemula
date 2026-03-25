<?php
// admin/products/edit.php
$pageTitle = 'Edit Produk';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?'); $stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { setFlash('danger','Produk tidak ditemukan.'); header('Location:'.BASE_URL.'/admin/products/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $catId    = (int)($_POST['category_id'] ?? 0);
    $price    = (float)str_replace(['.',','],['','.'], $_POST['price'] ?? 0);
    $stock    = (int)($_POST['stock'] ?? 0);
    $unit     = trim($_POST['unit'] ?? 'kg');
    $desc     = trim($_POST['description'] ?? '');
    $featured = isset($_POST['is_featured']) ? 1 : 0;
    $active   = isset($_POST['is_active']) ? 1 : 0;

    // Handle image
    $imgName = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            // Remove old
            if ($imgName && file_exists(UPLOAD_PATH . $imgName)) @unlink(UPLOAD_PATH . $imgName);
            $imgName = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $imgName);
        }
    }

    $pdo->prepare('UPDATE products SET category_id=?,name=?,description=?,price=?,stock=?,unit=?,image=?,is_featured=?,is_active=?,updated_at=NOW() WHERE id=?')
        ->execute([$catId, $name, $desc, $price, $stock, $unit, $imgName, $featured, $active, $id]);
    setFlash('success', 'Produk berhasil diperbarui!');
    header('Location: '.BASE_URL.'/admin/products/index.php'); exit;
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-edit me-2 text-success"></i>Edit Produk</h4>
  <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card p-4">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-600">Nama Produk *</label>
            <input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Kategori *</label>
            <select name="category_id" class="form-select" required>
              <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Satuan</label>
            <select name="unit" class="form-select">
              <?php foreach (['kg','gram','ikat','pack','buah','liter'] as $u): ?><option value="<?= $u ?>" <?= $u===$product['unit']?'selected':'' ?>><?= $u ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Harga (Rp) *</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Stok</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" min="0">
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"><?= e($product['description']) ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Gambar Produk</label>
            <div class="d-flex gap-3 align-items-start">
              <img src="<?= getProductImage($product['image']) ?>" id="imagePreview" style="width:100px;height:80px;object-fit:cover;border-radius:8px;" alt="">
              <div class="flex-grow-1"><input type="file" name="image" id="productImageInput" class="form-control" accept="image/*"><small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small></div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_featured" id="featuredCheck" <?= $product['is_featured']?'checked':'' ?>>
              <label class="form-check-label fw-600" for="featuredCheck">⭐ Produk Unggulan</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" <?= $product['is_active']?'checked':'' ?>>
              <label class="form-check-label fw-600" for="activeCheck">✅ Produk Aktif</label>
            </div>
          </div>
          <div class="col-12 mt-2">
            <button type="submit" class="btn btn-success-custom px-5 fw-700"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

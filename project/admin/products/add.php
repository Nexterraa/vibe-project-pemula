<?php
// admin/products/add.php
$pageTitle = 'Tambah Produk';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $catId    = (int)($_POST['category_id'] ?? 0);
    $price    = (float)str_replace(['.',','], ['','.'], $_POST['price'] ?? 0);
    $stock    = (int)($_POST['stock'] ?? 0);
    $unit     = trim($_POST['unit'] ?? 'kg');
    $desc     = trim($_POST['description'] ?? '');
    $featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name) || $catId < 1 || $price < 0) {
        setFlash('danger', 'Nama, kategori, dan harga wajib diisi.');
    } else {
        $sl = slug($name);
        // Check duplicate slug
        $chk = $pdo->prepare('SELECT id FROM products WHERE slug = ?'); $chk->execute([$sl]);
        if ($chk->fetch()) $sl .= '-' . time();

        // Handle image upload
        $imgName = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
                setFlash('danger', 'Format gambar tidak valid (jpg/png/webp).');
                goto render;
            }
            $imgName = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $imgName);
        }

        $pdo->prepare('INSERT INTO products (category_id,name,slug,description,price,stock,unit,image,is_featured) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$catId, $name, $sl, $desc, $price, $stock, $unit, $imgName, $featured]);
        setFlash('success', 'Produk berhasil ditambahkan!');
        header('Location: ' . BASE_URL . '/admin/products/index.php'); exit;
    }
}
render:
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-plus me-2 text-success"></i>Tambah Produk</h4>
  <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card p-4">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-600">Nama Produk *</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Bayam Hijau Segar" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Kategori *</label>
            <select name="category_id" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Satuan</label>
            <select name="unit" class="form-select">
              <?php foreach (['kg','gram','ikat','pack','buah','liter'] as $u): ?><option value="<?= $u ?>"><?= $u ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Harga (Rp) *</label>
            <input type="number" name="price" class="form-control" placeholder="5000" min="0" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600">Stok</label>
            <input type="number" name="stock" class="form-control" placeholder="50" min="0" value="0">
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Deskripsi produk..."></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-600">Gambar Produk</label>
            <input type="file" name="image" id="productImageInput" class="form-control" accept="image/*">
            <img id="imagePreview" src="" style="display:none;width:120px;height:100px;object-fit:cover;border-radius:8px;margin-top:10px;" alt="Preview">
          </div>
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_featured" id="featuredCheck">
              <label class="form-check-label fw-600" for="featuredCheck">⭐ Produk Unggulan (tampil di Home)</label>
            </div>
          </div>
          <div class="col-12 mt-2">
            <button type="submit" class="btn btn-success-custom px-5 fw-700"><i class="fas fa-save me-2"></i>Simpan Produk</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
// admin/products/add.php - Tambah Produk Sayur (Simple)
$pageTitle = 'Tambah Produk';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

// Ambil kategori pertama yang tersedia (default)
$defaultCat = $pdo->query('SELECT id FROM categories ORDER BY id LIMIT 1')->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $unit  = trim($_POST['unit'] ?? 'kg');
    $desc  = trim($_POST['description'] ?? '');
    $catId = $defaultCat ?: 1;

    if (empty($name) || $price < 0) {
        setFlash('danger', 'Nama produk dan harga wajib diisi.');
    } else {
        $sl = slug($name);
        $chk = $pdo->prepare('SELECT id FROM products WHERE slug = ?'); $chk->execute([$sl]);
        if ($chk->fetch()) $sl .= '-' . time();

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

        $pdo->prepare('INSERT INTO products (category_id,name,slug,description,price,stock,unit,image,is_featured,is_active) VALUES (?,?,?,?,?,?,?,?,1,1)')
            ->execute([$catId, $name, $sl, $desc, $price, $stock, $unit, $imgName]);
        setFlash('success', 'Produk berhasil ditambahkan!');
        header('Location: ' . BASE_URL . '/admin/products/index.php'); exit;
    }
}
render:
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-plus me-2 text-success"></i>Tambah Produk Sayur</h4>
  <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="admin-card p-4">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">

          <div class="col-12">
            <label class="form-label fw-600">Nama Produk Sayur *</label>
            <input type="text" name="name" class="form-control form-control-lg" placeholder="Contoh: Bayam Hijau Segar" required>
          </div>

          <div class="col-sm-6">
            <label class="form-label fw-600">Harga (Rp) *</label>
            <input type="number" name="price" class="form-control" placeholder="5000" min="0" required>
          </div>

          <div class="col-sm-3">
            <label class="form-label fw-600">Stok</label>
            <input type="number" name="stock" class="form-control" placeholder="50" min="0" value="0">
          </div>

          <div class="col-sm-3">
            <label class="form-label fw-600">Satuan</label>
            <select name="unit" class="form-select">
              <?php foreach (['kg','gram','ikat','pack','buah','liter'] as $u): ?>
              <option value="<?= $u ?>"><?= $u ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-600">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi produk sayuran..."></textarea>
          </div>

          <div class="col-12">
            <label class="form-label fw-600">Foto Produk</label>
            <input type="file" name="image" id="productImageInput" class="form-control" accept="image/*">
            <div class="mt-2">
              <img id="imagePreview" src="" style="display:none;width:120px;height:100px;object-fit:cover;border-radius:10px;" alt="Preview">
            </div>
          </div>

          <div class="col-12 mt-2 d-grid">
            <button type="submit" class="btn btn-success-custom btn-lg fw-700">
              <i class="fas fa-save me-2"></i>Simpan Produk
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('productImageInput').addEventListener('change', function(e) {
  const preview = document.getElementById('imagePreview');
  if (e.target.files[0]) {
    preview.src = URL.createObjectURL(e.target.files[0]);
    preview.style.display = 'block';
  }
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>

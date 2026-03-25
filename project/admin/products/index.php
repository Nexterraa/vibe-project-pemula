<?php
// admin/products/index.php - Product List & Management
$pageTitle = 'Manajemen Produk';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$search = trim($_GET['q'] ?? '');
$catId  = (int)($_GET['cat'] ?? 0);
$where  = ['p.is_active = 1'];
$params = [];
if ($search) { $where[] = 'p.name LIKE ?'; $params[] = "%$search%"; }
if ($catId)  { $where[] = 'p.category_id = ?'; $params[] = $catId; }
$whereClause = implode(' AND ', $where);
$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE $whereClause ORDER BY p.id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-box me-2 text-success"></i>Manajemen Produk</h4>
  <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-success-custom fw-600 px-4">
    <i class="fas fa-plus me-2"></i>Tambah Produk
  </a>
</div>

<!-- Filter Bar -->
<div class="admin-card mb-4">
  <div class="p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-5">
        <input type="text" name="q" class="form-control" placeholder="Cari produk..." value="<?= e($search) ?>">
      </div>
      <div class="col-sm-4">
        <select name="cat" class="form-select">
          <option value="">Semua Kategori</option>
          <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $catId==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-3 d-flex gap-2">
        <button type="submit" class="btn btn-success-custom flex-grow-1"><i class="fas fa-search"></i></button>
        <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-secondary"><i class="fas fa-refresh"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h5><i class="fas fa-list me-2 text-success"></i>Daftar Produk (<?= count($products) ?>)</h5>
  </div>
  <div class="table-responsive">
    <table class="table admin-table">
      <thead>
        <tr><th>Produk</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              <img src="<?= getProductImage($p['image']) ?>" style="width:50px;height:42px;object-fit:cover;border-radius:8px;" alt="">
              <div>
                <div class="fw-700"><?= e($p['name']) ?></div>
                <small class="text-muted">/<?= e($p['unit']) ?> <?= $p['is_featured']?'<span class="badge bg-warning text-dark">⭐ Unggulan</span>':'' ?></small>
              </div>
            </div>
          </td>
          <td><span class="badge bg-success-subtle text-success"><?= e($p['cat_name']) ?></span></td>
          <td class="text-end fw-700"><?= rupiah($p['price']) ?></td>
          <td class="text-center">
            <span class="badge <?= $p['stock'] == 0 ? 'bg-danger' : ($p['stock'] < 5 ? 'bg-warning text-dark' : 'bg-success') ?>">
              <?= $p['stock'] ?>
            </span>
          </td>
          <td class="text-center">
            <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
              <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="<?= BASE_URL ?>/admin/products/delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada produk ditemukan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

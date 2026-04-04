<?php
// admin/products/index.php - Manajemen Produk (Simple)
$pageTitle = 'Manajemen Produk';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$search = trim($_GET['q'] ?? '');
$where  = ['is_active = 1'];
$params = [];
if ($search) { $where[] = 'name LIKE ?'; $params[] = "%$search%"; }
$whereClause = implode(' AND ', $where);
$stmt = $pdo->prepare("SELECT * FROM products WHERE $whereClause ORDER BY id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-box me-2 text-success"></i>Manajemen Produk</h4>
  <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-success-custom fw-600 px-4">
    <i class="fas fa-plus me-2"></i>Tambah Produk
  </a>
</div>

<!-- Search Bar -->
<div class="admin-card mb-4">
  <div class="p-3">
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="q" class="form-control" placeholder="Cari nama produk sayuran..." value="<?= e($search) ?>">
      <button type="submit" class="btn btn-success-custom px-4"><i class="fas fa-search"></i></button>
      <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-secondary"><i class="fas fa-refresh"></i></a>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h5><i class="fas fa-list me-2 text-success"></i>Daftar Produk Sayur (<?= count($products) ?>)</h5>
  </div>
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Produk</th>
          <th class="text-center">Satuan</th>
          <th class="text-end">Harga</th>
          <th class="text-center">Stok</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-box-open fs-3 d-block mb-2"></i>Belum ada produk.</td></tr>
        <?php else: ?>
        <?php foreach ($products as $i => $p): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td>
            <div class="d-flex align-items-center gap-3">
              <img src="<?= getProductImage($p['image']) ?>" style="width:48px;height:40px;object-fit:cover;border-radius:8px;" alt="">
              <div>
                <div class="fw-700"><?= e($p['name']) ?></div>
                <?php if ($p['is_featured']): ?><small><span class="badge bg-warning text-dark">⭐ Unggulan</span></small><?php endif; ?>
              </div>
            </div>
          </td>
          <td class="text-center"><span class="badge bg-secondary"><?= e($p['unit']) ?></span></td>
          <td class="text-end fw-700"><?= rupiah($p['price']) ?></td>
          <td class="text-center">
            <span class="badge <?= $p['stock'] == 0 ? 'bg-danger' : ($p['stock'] < 5 ? 'bg-warning text-dark' : 'bg-success') ?>">
              <?= $p['stock'] ?>
            </span>
          </td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="<?= BASE_URL ?>/admin/products/delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

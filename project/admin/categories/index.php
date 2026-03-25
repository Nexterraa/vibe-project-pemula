<?php
// admin/categories/index.php
$pageTitle = 'Manajemen Kategori';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();
$cats = $pdo->query('SELECT c.*, COUNT(p.id) as prod_count FROM categories c LEFT JOIN products p ON c.id=p.category_id AND p.is_active=1 GROUP BY c.id ORDER BY c.name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-tags me-2 text-success"></i>Manajemen Kategori</h4>
  <a href="<?= BASE_URL ?>/admin/categories/add.php" class="btn btn-success-custom fw-600 px-4"><i class="fas fa-plus me-2"></i>Tambah Kategori</a>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table">
      <thead><tr><th>Nama Kategori</th><th>Slug</th><th>Deskripsi</th><th class="text-center">Jumlah Produk</th><th class="text-center">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
        <tr>
          <td class="fw-700"><?= e($c['name']) ?></td>
          <td><code><?= e($c['slug']) ?></code></td>
          <td class="text-muted small"><?= e(truncate($c['description'] ?? '-', 60)) ?></td>
          <td class="text-center"><span class="badge bg-success"><?= $c['prod_count'] ?></span></td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <a href="<?= BASE_URL ?>/admin/categories/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="<?= BASE_URL ?>/admin/categories/delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus kategori ini? Semua produk di dalamnya juga akan terhapus!')"><i class="fas fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

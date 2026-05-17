<?php
// admin/index.php - Admin Dashboard
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../config/functions.php';
startSession(); 
requireAdmin();

// Count stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(grand_total) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?? 0;

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="admin-stats-card bg-primary text-white p-4 rounded-4 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="opacity-75 mb-1">Total Produk</h6>
          <h3 class="fw-800 mb-0"><?= $totalProducts ?></h3>
        </div>
        <i class="fas fa-box fs-1 opacity-25"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="admin-stats-card bg-success text-white p-4 rounded-4 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="opacity-75 mb-1">Total Pesanan</h6>
          <h3 class="fw-800 mb-0"><?= $totalOrders ?></h3>
        </div>
        <i class="fas fa-shopping-cart fs-1 opacity-25"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="admin-stats-card bg-info text-white p-4 rounded-4 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="opacity-75 mb-1">Total Pengguna</h6>
          <h3 class="fw-800 mb-0"><?= $totalUsers ?></h3>
        </div>
        <i class="fas fa-users fs-1 opacity-25"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="admin-stats-card bg-warning text-white p-4 rounded-4 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="opacity-75 mb-1">Total Pendapatan</h6>
          <h3 class="fw-800 mb-0"><?= rupiah($totalRevenue) ?></h3>
        </div>
        <i class="fas fa-money-bill-wave fs-1 opacity-25"></i>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-700 mb-0">Pesanan Terbaru</h5>
        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-sm btn-outline-success">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Pelanggan</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
              <td>#<?= $o['id'] ?></td>
              <td class="fw-600"><?= e($o['user_name']) ?></td>
              <td><?= rupiah($o['grand_total']) ?></td>
              <td>
                <span class="badge bg-<?= $o['status'] === 'completed' ? 'success' : ($o['status'] === 'pending' ? 'warning' : 'info') ?>">
                  <?= ucfirst($o['status']) ?>
                </span>
              </td>
              <td>
                <a href="<?= BASE_URL ?>/admin/orders/detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-light">Detail</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentOrders)): ?>
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan terbaru.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="admin-card">
      <h5 class="fw-700 mb-4">Quick Links</h5>
      <div class="d-grid gap-2">
        <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-success-custom py-3 text-start">
          <i class="fas fa-plus-circle me-2"></i> Tambah Produk Baru
        </a>
        <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline-success py-3 text-start">
          <i class="fas fa-box me-2"></i> Daftar Produk
        </a>
        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-outline-secondary py-3 text-start">
          <i class="fas fa-receipt me-2"></i> Riwayat Pesanan
        </a>
        <a href="<?= BASE_URL ?>/admin/users/index.php" class="btn btn-outline-secondary py-3 text-start">
          <i class="fas fa-users me-2"></i> Pengguna
        </a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

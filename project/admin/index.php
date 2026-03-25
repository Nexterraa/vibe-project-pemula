<?php
// admin/index.php - Dashboard
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../config/functions.php';
startSession();
requireAdmin();

// Stats
$totalProducts  = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories= $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalOrders    = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalRevenue   = $pdo->query('SELECT COALESCE(SUM(grand_total),0) FROM orders WHERE status NOT IN ("cancelled")')->fetchColumn();
$totalUsers     = $pdo->query('SELECT COUNT(*) FROM users WHERE role="customer"')->fetchColumn();
$lowStock       = $pdo->query('SELECT COUNT(*) FROM products WHERE stock < 5 AND is_active=1')->fetchColumn();

// Recent orders
$recentOrders = $pdo->query('SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 8')->fetchAll();

// Low stock products
$lowStockProds = $pdo->query('SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.stock < 5 AND p.is_active=1 ORDER BY p.stock ASC LIMIT 5')->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="mb-4">
  <h4 class="admin-page-title"><i class="fas fa-gauge me-2 text-success"></i>Dashboard</h4>
  <p class="text-muted mb-0">Selamat datang, <strong><?= e($_SESSION['user_name']) ?></strong> 👋</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card-green">
      <div class="stat-icon stat-icon-green"><i class="fas fa-box"></i></div>
      <div class="stat-value"><?= $totalProducts ?></div>
      <div class="stat-label">Total Produk</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card-blue">
      <div class="stat-icon stat-icon-blue"><i class="fas fa-receipt"></i></div>
      <div class="stat-value"><?= $totalOrders ?></div>
      <div class="stat-label">Total Pesanan</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card-orange">
      <div class="stat-icon stat-icon-orange"><i class="fas fa-users"></i></div>
      <div class="stat-value"><?= $totalUsers ?></div>
      <div class="stat-label">Pelanggan</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card-red">
      <div class="stat-icon" style="background:linear-gradient(135deg,#2d6a4f,#1b4332);"><i class="fas fa-wallet"></i></div>
      <div class="stat-value" style="font-size:1.3rem;"><?= rupiah($totalRevenue) ?></div>
      <div class="stat-label">Total Pendapatan</div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Recent Orders -->
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="admin-card-header">
        <h5><i class="fas fa-receipt me-2 text-success"></i>Pesanan Terbaru</h5>
        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-sm btn-outline-success">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table admin-table">
          <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr></thead>
          <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
              <td><a href="<?= BASE_URL ?>/admin/orders/detail.php?id=<?= $o['id'] ?>" class="fw-700 text-success"><?= e($o['order_code']) ?></a></td>
              <td><?= e($o['user_name']) ?></td>
              <td class="fw-600"><?= rupiah($o['grand_total']) ?></td>
              <td><span class="badge bg-secondary"><?= strtoupper($o['payment_method']) ?></span></td>
              <td><span class="badge badge-status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
              <td class="text-muted small"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Low Stock / Quick Actions -->
  <div class="col-lg-4">
    <?php if ($lowStock > 0): ?>
    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Stok Menipis</h5>
        <span class="badge bg-danger"><?= $lowStock ?></span>
      </div>
      <div class="p-3">
        <?php foreach ($lowStockProds as $lp): ?>
        <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded-3" style="background:#fff8f0;">
          <div>
            <div class="fw-600 small"><?= e($lp['name']) ?></div>
            <div class="text-muted" style="font-size:.75rem;"><?= e($lp['cat_name']) ?></div>
          </div>
          <span class="badge <?= $lp['stock'] == 0 ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $lp['stock'] ?></span>
        </div>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-sm btn-outline-warning w-100 mt-2">Kelola Stok</a>
      </div>
    </div>
    <?php endif; ?>
    <!-- Quick Actions -->
    <div class="admin-card">
      <div class="admin-card-header"><h5><i class="fas fa-bolt me-2 text-success"></i>Aksi Cepat</h5></div>
      <div class="p-3 d-flex flex-column gap-2">
        <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-success-custom fw-600"><i class="fas fa-plus me-2"></i>Tambah Produk Baru</a>
        <a href="<?= BASE_URL ?>/admin/categories/add.php" class="btn btn-outline-success fw-600"><i class="fas fa-tag me-2"></i>Tambah Kategori</a>
        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-outline-primary fw-600"><i class="fas fa-receipt me-2"></i>Kelola Pesanan</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

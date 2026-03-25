<?php
// admin/orders/index.php
$pageTitle = 'Manajemen Pesanan';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$status = $_GET['status'] ?? '';
$where = $status ? 'WHERE o.status = ?' : '';
$params = $status ? [$status] : [];
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id $where ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['pending','processing','shipped','delivered','cancelled'];
$counts = [];
foreach ($statuses as $s) {
    $c = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE status=?'); $c->execute([$s]);
    $counts[$s] = $c->fetchColumn();
}
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-receipt me-2 text-success"></i>Manajemen Pesanan</h4>
</div>

<!-- Status Tabs -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-sm <?= !$status?'btn-success':'btn-outline-secondary' ?>">Semua (<?= array_sum($counts) ?>)</a>
  <?php foreach ($statuses as $s): ?>
  <a href="?status=<?= $s ?>" class="btn btn-sm <?= $status===$s?'btn-success':'btn-outline-secondary' ?>">
    <?= ucfirst($s) ?> <span class="badge bg-white text-dark ms-1"><?= $counts[$s] ?></span>
  </a>
  <?php endforeach; ?>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table">
      <thead><tr><th>Kode Pesanan</th><th>Pelanggan</th><th>Pengiriman</th><th class="text-end">Total</th><th class="text-center">Metode</th><th class="text-center">Status</th><th>Tanggal</th><th class="text-center">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><a href="<?= BASE_URL ?>/admin/orders/detail.php?id=<?= $o['id'] ?>" class="fw-700 text-success"><?= e($o['order_code']) ?></a></td>
          <td><?= e($o['user_name']) ?></td>
          <td><small class="text-muted"><?= e(truncate($o['shipping_address'],40)) ?></small></td>
          <td class="text-end fw-700"><?= rupiah($o['grand_total']) ?></td>
          <td class="text-center"><span class="badge bg-secondary"><?= strtoupper($o['payment_method']) ?></span></td>
          <td class="text-center"><span class="badge badge-status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
          <td class="text-center">
            <a href="<?= BASE_URL ?>/admin/orders/detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada pesanan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

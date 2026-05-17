<?php
// admin/orders/index.php - Pesanan
$pageTitle = 'Pesanan';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

// Ambil semua pesanan dengan item-itemnya
$stmt = $pdo->query("
    SELECT o.id, o.order_code, o.shipping_address, o.payment_method, o.grand_total, o.created_at,
           o.notes, o.status, o.payment_proof,
           u.name as user_name,
           GROUP_CONCAT(oi.product_name ORDER BY oi.id SEPARATOR ', ') as items,
           SUM(oi.quantity) as total_qty
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON oi.order_id = o.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-receipt me-2 text-success"></i>Pesanan</h4>
  <span class="badge bg-success fs-6"><?= count($orders) ?> Pesanan</span>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>No</th>
          <th>No. Pesanan</th>
          <th>Nama Pembeli</th>
          <th>Produk Dipesan</th>
          <th class="text-center">Jumlah</th>
          <th class="text-center">Pembayaran</th>
          <th class="text-center">Status</th>
          <th>Tanggal</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
        <tr><td colspan="9" class="text-center py-5 text-muted"><i class="fas fa-inbox fs-3 d-block mb-2"></i>Belum ada pesanan.</td></tr>
        <?php else: ?>
        <?php foreach ($orders as $i => $o): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td>
            <span class="fw-700 text-success font-monospace"><?= e($o['order_code']) ?></span>
          </td>
          <td class="fw-600"><?= e($o['user_name']) ?></td>
          <td>
            <small class="text-dark"><?= e($o['items']) ?></small>
          </td>
          <td class="text-center">
            <span class="badge bg-success"><?= $o['total_qty'] ?> item</span>
          </td>
          <td class="text-center">
            <span class="badge bg-secondary"><?= strtoupper(e($o['payment_method'])) ?></span>
          </td>
          <td class="text-center">
            <?php
            $statusConfig = [
                'pending'    => ['bg' => 'bg-warning text-dark', 'label' => 'Pending'],
                'processing' => ['bg' => 'bg-info text-dark',    'label' => 'Diproses'],
                'shipped'    => ['bg' => 'bg-primary',           'label' => 'Dikirim'],
                'delivered'  => ['bg' => 'bg-success',           'label' => 'Diterima'],
                'cancelled'  => ['bg' => 'bg-danger',            'label' => 'Dibatalkan'],
                'valid'      => ['bg' => 'bg-success',           'label' => 'Valid'],
                'ditolak'    => ['bg' => 'bg-danger',            'label' => 'Ditolak'],
            ];
            $sc = $statusConfig[$o['status']] ?? ['bg' => 'bg-secondary', 'label' => ucfirst($o['status'])];
            ?>
            <span class="badge <?= $sc['bg'] ?>"><?= $sc['label'] ?></span>
          </td>
          <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
          <td class="text-center">
            <a href="<?= BASE_URL ?>/admin/orders/detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary fw-600">
              <i class="fas fa-search me-1"></i>Cek
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
// my_orders.php - Pesanan Saya (User)
$pageTitle = 'Pesanan Saya';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

// Query JOIN: ambil pesanan user + jumlah item dari order_items
$stmt = $pdo->prepare("
    SELECT o.id, o.order_code, o.grand_total, o.status, o.created_at,
           SUM(oi.quantity) as total_qty
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4">
  <h2 class="fw-800 mb-4"><i class="fas fa-receipt me-2 text-success"></i>Pesanan Saya</h2>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info">Belum ada pesanan. <a href="<?= BASE_URL ?>/products.php">Belanja sekarang</a></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover bg-white">
        <thead class="table-success">
          <tr>
            <th>No</th>
            <th>Kode Pesanan</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Status Pesanan</th>
            <th>Diterima / Tidak</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $i => $o): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= e($o['order_code']) ?></strong></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td class="fw-bold text-success"><?= rupiah($o['grand_total']) ?></td>
            <td>
              <?php
              // Tampilkan badge status
              switch ($o['status']) {
                case 'pending':    echo '<span class="badge bg-warning text-dark">Menunggu</span>'; break;
                case 'processing': echo '<span class="badge bg-info text-dark">Diproses</span>'; break;
                case 'shipped':    echo '<span class="badge bg-primary">Dikirim</span>'; break;
                case 'delivered':  echo '<span class="badge bg-success">Diterima</span>'; break;
                case 'cancelled':  echo '<span class="badge bg-danger">Dibatalkan</span>'; break;
                case 'valid':      echo '<span class="badge bg-success">Valid</span>'; break;
                case 'ditolak':    echo '<span class="badge bg-danger">Ditolak</span>'; break;
                default:           echo '<span class="badge bg-secondary">' . e($o['status']) . '</span>';
              }
              ?>
            </td>
            <td>
              <?php
              // Cek apakah pesanan diterima atau tidak
              if (in_array($o['status'], ['delivered', 'valid'])) {
                echo '<span class="text-success fw-bold">✅ Diterima</span>';
              } elseif (in_array($o['status'], ['cancelled', 'ditolak'])) {
                echo '<span class="text-danger fw-bold">❌ Tidak Diterima</span>';
              } else {
                echo '<span class="text-muted">⏳ Menunggu</span>';
              }
              ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

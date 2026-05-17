<?php
// my_orders.php - Pesanan Saya (User)
$pageTitle = 'Pesanan Saya';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

// Query JOIN: ambil pesanan user + jumlah item dari order_items
$stmt = $pdo->prepare("
    SELECT o.id, o.order_code, o.grand_total, o.status, o.created_at,
           o.payment_method, o.notes,
           SUM(oi.quantity) as total_qty,
           GROUP_CONCAT(oi.product_name ORDER BY oi.id SEPARATOR ', ') as items_list
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
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="fw-800 mb-0"><i class="fas fa-receipt me-2 text-success"></i>Pesanan Saya</h2>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom fw-600">
      <i class="fas fa-store me-1"></i> Belanja Lagi
    </a>
  </div>

  <?php if (empty($orders)): ?>
    <div class="bg-white rounded-4 shadow-sm p-5 text-center">
      <div style="font-size:4rem;" class="mb-3">🛒</div>
      <h5 class="fw-700 mb-2">Belum Ada Pesanan</h5>
      <p class="text-muted mb-4">Anda belum melakukan pemesanan apapun.</p>
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-4 fw-600">
        <i class="fas fa-store me-2"></i>Mulai Belanja
      </a>
    </div>
  <?php else: ?>

    <div class="row g-3">
      <?php foreach ($orders as $i => $o):
        $status = $o['status'];

        // Tentukan tampilan berdasarkan status
        $isAccepted  = in_array($status, ['valid', 'delivered']);
        $isRejected  = in_array($status, ['ditolak', 'cancelled']);
        $inProgress  = in_array($status, ['processing', 'shipped']);
        $isPending   = $status === 'pending';

        // Badge status pesanan
        $statusBadge = match($status) {
          'pending'    => ['class' => 'bg-warning text-dark',  'icon' => 'fa-clock',        'label' => 'Menunggu Konfirmasi'],
          'processing' => ['class' => 'bg-info text-dark',     'icon' => 'fa-cog',          'label' => 'Sedang Diproses'],
          'shipped'    => ['class' => 'bg-primary',            'icon' => 'fa-truck',        'label' => 'Sedang Dikirim'],
          'valid'      => ['class' => 'bg-success',            'icon' => 'fa-check-circle', 'label' => 'Diterima'],
          'delivered'  => ['class' => 'bg-success',            'icon' => 'fa-box-open',     'label' => 'Selesai'],
          'ditolak'    => ['class' => 'bg-danger',             'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
          'cancelled'  => ['class' => 'bg-danger',             'icon' => 'fa-ban',          'label' => 'Dibatalkan'],
          default      => ['class' => 'bg-secondary',          'icon' => 'fa-question',     'label' => ucfirst($status)],
        };

        // Border card berdasarkan status
        $cardBorder = ($isAccepted || $inProgress) ? 'border-success' : ($isRejected ? 'border-danger' : 'border-0');
      ?>
      <div class="col-12">
        <div class="bg-white rounded-4 shadow-sm p-4 border <?= $cardBorder ?>" style="border-width: 2px !important;">
          <div class="row g-3 align-items-center">

            <!-- No & Kode Pesanan -->
            <div class="col-md-3">
              <div class="text-muted small mb-1">#<?= $i + 1 ?> · <?= date('d M Y', strtotime($o['created_at'])) ?></div>
              <div class="fw-800 text-success font-monospace fs-6"><?= e($o['order_code']) ?></div>
              <div class="text-muted small mt-1">
                <i class="fas fa-<?= $o['payment_method'] === 'cod' ? 'money-bill-wave' : 'university' ?> me-1"></i>
                <?= $o['payment_method'] === 'cod' ? 'Bayar di Tempat' : 'Transfer Bank' ?>
              </div>
            </div>

            <!-- Item Dipesan -->
            <div class="col-md-3">
              <div class="text-muted small mb-1"><i class="fas fa-box me-1"></i><?= $o['total_qty'] ?> item</div>
              <div class="small text-dark fw-600" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;"
                   title="<?= e($o['items_list']) ?>">
                <?= e($o['items_list']) ?>
              </div>
            </div>

            <!-- Total -->
            <div class="col-md-2">
              <div class="text-muted small mb-1">Total Bayar</div>
              <div class="fw-800 text-success"><?= rupiah($o['grand_total']) ?></div>
            </div>

            <!-- Status Keputusan Admin -->
            <div class="col-md-3">
              <?php if ($isPending): ?>
                <!-- Belum ada keputusan admin: tampilkan status netral -->
                <span class="badge <?= $statusBadge['class'] ?> px-3 py-2 fs-6 mb-2">
                  <i class="fas <?= $statusBadge['icon'] ?> me-1"></i><?= $statusBadge['label'] ?>
                </span>
                <div class="text-muted small mt-1">
                  <i class="fas fa-info-circle me-1"></i>Menunggu konfirmasi dari admin
                </div>
              <?php elseif ($inProgress): ?>
                <!-- Admin sudah TERIMA, sedang dalam proses -->
                <span class="badge <?= $statusBadge['class'] ?> px-3 py-2 fs-6 mb-2">
                  <i class="fas <?= $statusBadge['icon'] ?> me-1"></i><?= $statusBadge['label'] ?>
                </span>
                <div class="text-muted small fw-600 mt-1">
                  <i class="fas fa-shipping-fast me-1"></i>Pesanan sedang diproses
                </div>
              <?php elseif ($isAccepted): ?>
                <!-- Admin sudah TERIMA / SELESAI -->
                <span class="badge <?= $statusBadge['class'] ?> px-3 py-2 fs-6 mb-2">
                  <i class="fas <?= $statusBadge['icon'] ?> me-1"></i><?= $statusBadge['label'] ?>
                </span>
                <div class="text-success small fw-600 mt-1">
                  <i class="fas fa-thumbs-up me-1"></i>
                  <?= $status === 'delivered' ? 'Pesanan telah selesai!' : 'Pesanan Anda telah dikonfirmasi!' ?>
                </div>
              <?php elseif ($isRejected): ?>
                <!-- Admin sudah TOLAK -->
                <span class="badge bg-danger px-3 py-2 fs-6 mb-2">
                  <i class="fas fa-times-circle me-1"></i>
                  <?= $status === 'ditolak' ? 'Ditolak' : 'Dibatalkan' ?>
                </span>
                <div class="text-danger small fw-600 mt-1">
                  <i class="fas fa-thumbs-down me-1"></i>
                  <?= $status === 'ditolak' ? 'Pesanan Anda ditolak oleh admin.' : 'Pesanan dibatalkan.' ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- Aksi -->
            <div class="col-md-1 text-end">
              <a href="<?= BASE_URL ?>/my_orders_detail.php?id=<?= $o['id'] ?>"
                 class="btn btn-outline-success btn-sm fw-600 rounded-3"
                 title="Lihat Detail">
                <i class="fas fa-eye"></i>
              </a>
            </div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Legenda -->
    <div class="mt-4 p-3 bg-white rounded-4 shadow-sm">
      <div class="small fw-700 text-muted mb-2"><i class="fas fa-info-circle me-1"></i>Keterangan Status:</div>
      <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>
        <span class="badge bg-info text-dark px-3 py-2"><i class="fas fa-spinner me-1"></i>Sedang Diproses</span>
        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>Diterima oleh Admin</span>
        <span class="badge bg-danger px-3 py-2"><i class="fas fa-times-circle me-1"></i>Ditolak oleh Admin</span>
      </div>
    </div>

  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

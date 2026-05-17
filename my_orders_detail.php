<?php
// my_orders_detail.php - Detail Pesanan User
$pageTitle = 'Detail Pesanan';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . '/my_orders.php'); exit; }

// Ambil pesanan milik user ini saja (keamanan)
$stmt = $pdo->prepare('SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=? AND o.user_id=?');
$stmt->execute([$id, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/my_orders.php');
    exit;
}

// Ambil item pesanan
$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
$items->execute([$id]);
$orderItems = $items->fetchAll();

$status     = $order['status'];
$isPending  = $status === 'pending';
$inProgress = in_array($status, ['processing', 'shipped']);
$isAccepted = in_array($status, ['valid', 'processing', 'shipped', 'delivered']);
$isFinished = $status === 'delivered';
$isRejected = in_array($status, ['ditolak', 'cancelled']);

$statusBadge = match($status) {
    'pending'    => ['class' => 'bg-warning text-dark', 'icon' => 'fa-clock',        'label' => 'Menunggu Konfirmasi'],
    'processing' => ['class' => 'bg-info text-dark',    'icon' => 'fa-cog',          'label' => 'Sedang Diproses'],
    'shipped'    => ['class' => 'bg-primary',           'icon' => 'fa-truck',        'label' => 'Sedang Dikirim'],
    'valid'      => ['class' => 'bg-success',           'icon' => 'fa-check-circle', 'label' => 'Diterima'],
    'delivered'  => ['class' => 'bg-success',           'icon' => 'fa-box-open',     'label' => 'Pesanan Selesai'],
    'ditolak'    => ['class' => 'bg-danger',            'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
    'cancelled'  => ['class' => 'bg-danger',            'icon' => 'fa-ban',          'label' => 'Dibatalkan'],
    default      => ['class' => 'bg-secondary',         'icon' => 'fa-question',     'label' => ucfirst($status)],
};

// Progress steps (untuk order yang sudah diterima)
$progressSteps = [
    'valid'      => ['icon' => 'fa-check-circle', 'label' => 'Pesanan Diterima'],
    'processing' => ['icon' => 'fa-cog',          'label' => 'Sedang Diproses'],
    'shipped'    => ['icon' => 'fa-truck',        'label' => 'Sedang Dikirim'],
    'delivered'  => ['icon' => 'fa-box-open',     'label' => 'Pesanan Selesai'],
];
$stepKeys   = array_keys($progressSteps);
$currentIdx = array_search($status, $stepKeys);
if ($currentIdx === false) $currentIdx = -1;

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h2 class="fw-800 mb-1"><i class="fas fa-receipt me-2 text-success"></i>Detail Pesanan</h2>
      <span class="fw-700 text-success font-monospace"><?= e($order['order_code']) ?></span>
    </div>
    <a href="<?= BASE_URL ?>/my_orders.php" class="btn btn-outline-secondary fw-600">
      <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php showFlash(); ?>

  <!-- Status Banner -->
  <?php if ($isPending): ?>
  <div class="alert alert-warning d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #ffc107;">
    <div style="font-size:2.5rem;">⏳</div>
    <div>
      <div class="fw-800 fs-5">Menunggu Konfirmasi Admin</div>
      <div class="text-muted">Pesanan Anda sedang ditinjau. Status akan diperbarui setelah admin mengambil keputusan.</div>
    </div>
  </div>
  <?php elseif ($status === 'processing'): ?>
  <div class="alert alert-info d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #0dcaf0;">
    <div style="font-size:2.5rem;">⚙️</div>
    <div>
      <div class="fw-800 fs-5">Pesanan Sedang Diproses</div>
      <div class="text-muted">Admin sedang memproses pesanan Anda. Mohon ditunggu.</div>
    </div>
  </div>
  <?php elseif ($status === 'shipped'): ?>
  <div class="alert alert-primary d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #0d6efd;">
    <div style="font-size:2.5rem;">🚚</div>
    <div>
      <div class="fw-800 fs-5">Pesanan Sedang Dikirim!</div>
      <div class="text-muted">Pesanan Anda dalam perjalanan. Harap siapkan diri untuk menerima.</div>
    </div>
  </div>
  <?php elseif ($status === 'delivered'): ?>
  <div class="alert alert-success d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #198754;">
    <div style="font-size:2.5rem;">🎉</div>
    <div>
      <div class="fw-800 fs-5">Pesanan Selesai!</div>
      <div class="text-muted">Pesanan telah sampai. Terima kasih telah berbelanja di Toko Sayur Online!</div>
    </div>
  </div>
  <?php elseif ($isAccepted): ?>
  <div class="alert alert-success d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #198754;">
    <div style="font-size:2.5rem;">✅</div>
    <div>
      <div class="fw-800 fs-5">Pesanan Anda Diterima!</div>
      <div class="text-muted">Admin telah mengkonfirmasi pesanan Anda. Sedang menunggu proses selanjutnya.</div>
    </div>
  </div>
  <?php elseif ($isRejected): ?>
  <div class="alert alert-danger d-flex align-items-center gap-3 rounded-4 mb-4 p-4" style="border-left:5px solid #dc3545;">
    <div style="font-size:2.5rem;">❌</div>
    <div>
      <div class="fw-800 fs-5">Pesanan <?= $status === 'ditolak' ? 'Ditolak' : 'Dibatalkan' ?></div>
      <div class="text-muted">Maaf, pesanan Anda <?= $status === 'ditolak' ? 'ditolak oleh admin' : 'telah dibatalkan' ?>. Hubungi kami untuk info lebih lanjut.</div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Tabel Item Pesanan -->
    <div class="col-lg-8">
      <div class="bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center gap-2">
          <i class="fas fa-box text-success fs-5"></i>
          <h5 class="fw-800 mb-0">Produk yang Dipesan</h5>
        </div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4">Nama Produk</th>
                <th class="text-center">Harga Satuan</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end pe-4">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orderItems as $oi): ?>
              <tr>
                <td class="fw-600 ps-4"><?= e($oi['product_name']) ?></td>
                <td class="text-center text-muted"><?= rupiah($oi['price']) ?></td>
                <td class="text-center"><span class="badge bg-success"><?= $oi['quantity'] ?></span></td>
                <td class="text-end fw-700 pe-4"><?= rupiah($oi['subtotal']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="table-light">
                <td colspan="3" class="text-end fw-700 ps-4">Subtotal</td>
                <td class="text-end fw-700 pe-4"><?= rupiah($order['total_amount']) ?></td>
              </tr>
              <tr>
                <td colspan="3" class="text-end text-muted ps-4">Ongkos Kirim</td>
                <td class="text-end pe-4"><?= rupiah($order['shipping_fee']) ?></td>
              </tr>
              <tr style="background: #d1e7dd;">
                <td colspan="3" class="text-end fw-800 fs-6 ps-4">Total Bayar</td>
                <td class="text-end fw-800 fs-6 text-success pe-4"><?= rupiah($order['grand_total']) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Bukti Transfer -->
      <?php if ($order['payment_method'] === 'transfer'): ?>
      <div class="bg-white rounded-4 shadow-sm mt-4 overflow-hidden">
        <div class="p-4 border-bottom d-flex align-items-center gap-2">
          <i class="fas fa-image text-success fs-5"></i>
          <h5 class="fw-800 mb-0">Bukti Transfer</h5>
        </div>
        <div class="p-4">
          <?php if (!empty($order['payment_proof'])): ?>
            <div class="text-center">
              <a href="<?= BASE_URL ?>/uploads/transfer/<?= e($order['payment_proof']) ?>" target="_blank">
                <img src="<?= BASE_URL ?>/uploads/transfer/<?= e($order['payment_proof']) ?>"
                     alt="Bukti Transfer"
                     class="img-fluid rounded-3 border shadow-sm"
                     style="max-height: 350px; object-fit: contain;">
              </a>
              <p class="text-muted small mt-2"><i class="fas fa-search-plus me-1"></i>Klik gambar untuk memperbesar</p>
            </div>
          <?php else: ?>
            <div class="alert alert-warning mb-0">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Bukti transfer belum diupload. Silakan hubungi admin.
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">

      <!-- Status Pesanan -->
      <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
        <h6 class="fw-800 mb-3 text-success"><i class="fas fa-clipboard-check me-1"></i>Status Pesanan</h6>

        <?php if ($isPending): ?>
          <!-- Belum ada keputusan -->
          <div class="text-center py-3">
            <div style="font-size:3rem;" class="mb-2">⏳</div>
            <span class="badge bg-warning text-dark fs-6 px-3 py-2 mb-2">
              <i class="fas fa-clock me-1"></i>Menunggu Konfirmasi
            </span>
            <p class="text-muted small mt-2 mb-0">Status akan diperbarui setelah admin mengkonfirmasi.</p>
          </div>

        <?php elseif ($isAccepted): ?>
          <!-- Progress Tracker -->
          <div class="d-flex flex-column gap-2">
            <?php foreach ($progressSteps as $stepStatus => $step):
              $stepIdx   = array_search($stepStatus, $stepKeys);
              $isDone    = $stepIdx <= $currentIdx;
              $isCurrent = $stepStatus === $status;
            ?>
            <div class="d-flex align-items-center gap-2 p-2 rounded-3"
                 style="background:<?= $isCurrent ? 'rgba(25,135,84,.12)' : ($isDone ? 'rgba(25,135,84,.05)' : '#f8f9fa') ?>;
                        border:1px solid <?= $isCurrent ? '#198754' : ($isDone ? '#a3cfbb' : '#dee2e6') ?>;">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                   style="width:30px;height:30px;background:<?= $isDone ? '#198754' : '#dee2e6' ?>;">
                <i class="fas <?= $step['icon'] ?> text-white" style="font-size:.7rem;"></i>
              </div>
              <span class="fw-600 small <?= $isCurrent ? 'text-success' : ($isDone ? 'text-dark' : 'text-muted') ?>">
                <?= $step['label'] ?>
                <?php if ($isCurrent): ?>
                  <span class="badge bg-success ms-1" style="font-size:.58rem;">Sekarang</span>
                <?php endif; ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>

        <?php elseif ($isRejected): ?>
          <!-- Admin Tolak -->
          <div class="text-center py-3">
            <div style="font-size:3rem;" class="mb-2">❌</div>
            <span class="badge bg-danger fs-6 px-3 py-2 mb-2">
              <i class="fas fa-times-circle me-1"></i><?= $status === 'ditolak' ? 'Ditolak' : 'Dibatalkan' ?>
            </span>
            <p class="text-danger small fw-600 mt-2 mb-0">
              <?= $status === 'ditolak' ? 'Pesanan Anda ditolak oleh admin.' : 'Pesanan dibatalkan.' ?>
            </p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Info Pesanan -->
      <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
        <h6 class="fw-800 mb-3 text-success"><i class="fas fa-info-circle me-1"></i>Info Pesanan</h6>
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Kode Pesanan</small>
          <span class="fw-800 font-monospace text-success"><?= e($order['order_code']) ?></span>
        </div>
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Tanggal Pesanan</small>
          <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB
        </div>
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Metode Pembayaran</small>
          <span class="badge bg-secondary fs-6 px-3 py-2">
            <i class="fas fa-<?= $order['payment_method'] === 'cod' ? 'money-bill-wave' : 'university' ?> me-1"></i>
            <?= $order['payment_method'] === 'cod' ? 'Bayar di Tempat (COD)' : 'Transfer Bank' ?>
          </span>
        </div>
        <div>
          <small class="text-muted d-block mb-1">Total Bayar</small>
          <span class="fw-800 text-success fs-5"><?= rupiah($order['grand_total']) ?></span>
        </div>
      </div>

      <!-- Info Pengiriman -->
      <div class="bg-white rounded-4 shadow-sm p-4">
        <h6 class="fw-800 mb-3 text-success"><i class="fas fa-map-marker-alt me-1"></i>Info Pengiriman</h6>
        <div class="mb-2">
          <small class="text-muted d-block mb-1">Penerima</small>
          <strong><?= e($order['shipping_name']) ?></strong>
        </div>
        <div class="mb-2">
          <small class="text-muted d-block mb-1">Telepon</small>
          <?= e($order['shipping_phone']) ?>
        </div>
        <div class="mb-2">
          <small class="text-muted d-block mb-1">Alamat Pengiriman</small>
          <span class="text-dark"><?= nl2br(e($order['shipping_address'])) ?></span>
        </div>
        <?php if ($order['notes']): ?>
        <div>
          <small class="text-muted d-block mb-1">Catatan</small>
          <em class="text-dark"><?= e($order['notes']) ?></em>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

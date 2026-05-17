<?php
// admin/orders/detail.php - Detail Pesanan + Verifikasi
$pageTitle = 'Detail Pesanan';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { setFlash('danger','Pesanan tidak ditemukan.'); header('Location:'.BASE_URL.'/admin/orders/index.php'); exit; }

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $items->execute([$id]);
$orderItems = $items->fetchAll();

// Status config
$statusConfig = [
    'pending'    => ['bg' => 'bg-warning text-dark', 'label' => 'Menunggu'],
    'processing' => ['bg' => 'bg-info text-dark',    'label' => 'Diproses'],
    'shipped'    => ['bg' => 'bg-primary',           'label' => 'Dikirim'],
    'delivered'  => ['bg' => 'bg-success',           'label' => 'Diterima'],
    'cancelled'  => ['bg' => 'bg-danger',            'label' => 'Dibatalkan'],
    'valid'      => ['bg' => 'bg-success',           'label' => 'Diterima (Valid)'],
    'ditolak'    => ['bg' => 'bg-danger',            'label' => 'Ditolak'],
];
$sc = $statusConfig[$order['status']] ?? ['bg' => 'bg-secondary', 'label' => ucfirst($order['status'])];

$isPending    = in_array($order['status'], ['pending']);
$isAccepted   = in_array($order['status'], ['valid', 'processing', 'shipped', 'delivered']);
$isRejected   = in_array($order['status'], ['ditolak', 'cancelled']);
$isCompleted  = $order['status'] === 'delivered';

// Progress steps setelah diterima
$progressSteps = [
    'valid'      => ['icon' => 'fa-check-circle',  'label' => 'Diterima',  'color' => 'success'],
    'processing' => ['icon' => 'fa-cog',           'label' => 'Diproses',  'color' => 'info'],
    'shipped'    => ['icon' => 'fa-truck',          'label' => 'Dikirim',   'color' => 'primary'],
    'delivered'  => ['icon' => 'fa-box-open',       'label' => 'Selesai',   'color' => 'success'],
];
// Tombol aksi berikutnya
$nextAction = [
    'valid'      => ['status' => 'processing', 'label' => 'Tandai Diproses', 'icon' => 'fa-cog',    'btn' => 'btn-info'],
    'processing' => ['status' => 'shipped',    'label' => 'Tandai Dikirim',  'icon' => 'fa-truck',  'btn' => 'btn-primary'],
    'shipped'    => ['status' => 'delivered',  'label' => 'Tandai Selesai',  'icon' => 'fa-box-open','btn' => 'btn-success'],
];

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-receipt me-2 text-success"></i>Detail Pesanan</h4>
  <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>

<?php showFlash(); ?>

<div class="row g-4">
  <!-- Item Pesanan -->
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="admin-card-header"><h5><i class="fas fa-box me-2 text-success"></i>Produk yang Dipesan</h5></div>
      <div class="table-responsive">
        <table class="table admin-table mb-0">
          <thead>
            <tr>
              <th>Nama Produk</th>
              <th class="text-center">Harga Satuan</th>
              <th class="text-center">Jumlah</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orderItems as $oi): ?>
            <tr>
              <td class="fw-600"><?= e($oi['product_name']) ?></td>
              <td class="text-center"><?= rupiah($oi['price']) ?></td>
              <td class="text-center"><span class="badge bg-success"><?= $oi['quantity'] ?></span></td>
              <td class="text-end fw-700"><?= rupiah($oi['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="table-light">
              <td colspan="3" class="text-end fw-700">Subtotal</td>
              <td class="text-end fw-700"><?= rupiah($order['total_amount']) ?></td>
            </tr>
            <tr>
              <td colspan="3" class="text-end">Ongkos Kirim</td>
              <td class="text-end"><?= rupiah($order['shipping_fee']) ?></td>
            </tr>
            <tr class="table-success">
              <td colspan="3" class="text-end fw-800 fs-6">Total Bayar</td>
              <td class="text-end fw-800 fs-6 text-success"><?= rupiah($order['grand_total']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Bukti Transfer (jika transfer) -->
    <?php if ($order['payment_method'] === 'transfer'): ?>
    <div class="admin-card mt-4">
      <div class="admin-card-header"><h5><i class="fas fa-image me-2 text-success"></i>Bukti Transfer</h5></div>
      <div class="p-4">
        <?php if (!empty($order['payment_proof'])): ?>
          <div class="text-center">
            <a href="<?= BASE_URL ?>/uploads/transfer/<?= e($order['payment_proof']) ?>" target="_blank">
              <img src="<?= BASE_URL ?>/uploads/transfer/<?= e($order['payment_proof']) ?>"
                   alt="Bukti Transfer"
                   class="img-fluid rounded-3 border shadow-sm"
                   style="max-height: 400px; object-fit: contain;">
            </a>
            <p class="text-muted small mt-2"><i class="fas fa-search-plus me-1"></i>Klik gambar untuk memperbesar</p>
          </div>
        <?php else: ?>
          <div class="alert alert-warning mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-triangle fs-4"></i>
            <div>
              <strong>Belum ada bukti transfer.</strong><br>
              <small>User belum mengupload bukti pembayaran untuk pesanan ini.</small>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar: Verifikasi + Info -->
  <div class="col-lg-4">

    <!-- ===== VERIFIKASI PESANAN ===== -->
    <div class="admin-card mb-4 p-4" style="border: 2px solid #198754;">
      <h6 class="fw-800 mb-3 text-success"><i class="fas fa-clipboard-check me-1"></i> Verifikasi Pesanan</h6>

      <!-- Status saat ini -->
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Status Saat Ini</small>
        <span class="badge <?= $sc['bg'] ?> fs-6 px-3 py-2">
          <i class="fas fa-circle-dot me-1"></i><?= $sc['label'] ?>
        </span>
      </div>

      <?php if ($isPending): ?>
        <!-- Pesanan belum diputuskan — tampilkan tombol Terima / Tolak -->
        <p class="text-muted small mb-3">
          <i class="fas fa-info-circle me-1"></i>
          Pilih tindakan untuk pesanan ini. Status akan langsung terlihat oleh pelanggan.
        </p>
        <div class="d-grid gap-2">
          <form action="<?= BASE_URL ?>/admin/orders/update_status.php" method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="status" value="valid">
            <button type="submit" class="btn btn-success w-100 fw-700 py-2">
              <i class="fas fa-check-circle me-2"></i>Terima Pesanan
            </button>
          </form>
          <form action="<?= BASE_URL ?>/admin/orders/update_status.php" method="POST"
                onsubmit="return confirm('Yakin ingin menolak pesanan <?= e(addslashes($order['order_code'])) ?>?');">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="status" value="ditolak">
            <button type="submit" class="btn btn-danger w-100 fw-700 py-2">
              <i class="fas fa-times-circle me-2"></i>Tolak Pesanan
            </button>
          </form>
        </div>

      <?php elseif ($isAccepted): ?>
        <!-- PROGRESS TRACKER setelah diterima -->
        <div class="mb-3">
          <small class="text-muted fw-600 d-block mb-2">Progress Pengiriman</small>
          <div class="d-flex flex-column gap-2">
            <?php
            $stepKeys = array_keys($progressSteps);
            $currentIdx = array_search($order['status'], $stepKeys);
            foreach ($progressSteps as $stepStatus => $step):
              $stepIdx = array_search($stepStatus, $stepKeys);
              $isDone    = $stepIdx <= $currentIdx;
              $isCurrent = $stepStatus === $order['status'];
            ?>
            <div class="d-flex align-items-center gap-2 p-2 rounded-3"
                 style="background:<?= $isCurrent ? 'rgba(25,135,84,.12)' : ($isDone ? 'rgba(25,135,84,.05)' : '#f8f9fa') ?>;
                        border: 1px solid <?= $isCurrent ? '#198754' : ($isDone ? '#a3cfbb' : '#dee2e6') ?>;">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                   style="width:32px;height:32px;background:<?= $isDone ? '#198754' : '#dee2e6' ?>;">
                <i class="fas <?= $step['icon'] ?> text-white" style="font-size:.75rem;"></i>
              </div>
              <span class="fw-600 small <?= $isCurrent ? 'text-success' : ($isDone ? 'text-dark' : 'text-muted') ?>">
                <?= $step['label'] ?>
                <?php if ($isCurrent): ?><span class="badge bg-success ms-1" style="font-size:.6rem;">Sekarang</span><?php endif; ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if (isset($nextAction[$order['status']])): ?>
          <!-- Tombol tahap berikutnya -->
          <?php $next = $nextAction[$order['status']]; ?>
          <hr class="my-3">
          <p class="text-muted small mb-2">
            <i class="fas fa-arrow-right me-1"></i>Lanjutkan ke tahap berikutnya:
          </p>
          <form action="<?= BASE_URL ?>/admin/orders/update_status.php" method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="status" value="<?= $next['status'] ?>">
            <button type="submit" class="btn <?= $next['btn'] ?> w-100 fw-700 py-2">
              <i class="fas <?= $next['icon'] ?> me-2"></i><?= $next['label'] ?>
            </button>
          </form>
        <?php else: ?>
          <!-- Selesai total -->
          <div class="alert alert-success mb-0 mt-2 text-center">
            <i class="fas fa-trophy me-1"></i><strong>Pesanan Selesai!</strong><br>
            <small>Semua tahap pengiriman telah selesai.</small>
          </div>
        <?php endif; ?>

      <?php elseif ($isRejected): ?>
        <!-- Sudah DITOLAK -->
        <div class="alert alert-danger mb-0 d-flex align-items-center gap-2">
          <i class="fas fa-times-circle fs-4"></i>
          <div>
            <strong>Pesanan Ditolak</strong><br>
            <small>Anda telah menolak pesanan ini. Pelanggan sudah mendapat konfirmasi.</small>
          </div>
        </div>

      <?php else: ?>
        <!-- Status lain (shipped, dll) — tampilkan dropdown -->
        <form action="<?= BASE_URL ?>/admin/orders/update_status.php" method="POST">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <div class="mb-3">
            <label class="form-label small text-muted fw-600">Ubah Status</label>
            <select name="status" class="form-select">
              <?php
              $allStatuses = [
                  'pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim',
                  'delivered' => 'Diterima', 'cancelled' => 'Dibatalkan', 'valid' => 'Valid', 'ditolak' => 'Ditolak'
              ];
              foreach ($allStatuses as $val => $lbl):
              ?>
                <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100 fw-600">
            <i class="fas fa-save me-1"></i> Simpan Status
          </button>
        </form>
      <?php endif; ?>
    </div>

    <!-- Info Pesanan -->
    <div class="admin-card mb-4 p-4">
      <h6 class="fw-800 mb-3 text-success"><i class="fas fa-info-circle me-1"></i> Info Pesanan</h6>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Kode Pesanan</small>
        <span class="fw-800 font-monospace text-success fs-6"><?= e($order['order_code']) ?></span>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Nama Pembeli</small>
        <strong><?= e($order['user_name']) ?></strong>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Metode Pembayaran</small>
        <span class="badge bg-secondary fs-6 px-3 py-2"><?= strtoupper(e($order['payment_method'])) ?></span>
      </div>
      <div class="mb-3">
        <small class="text-muted d-block mb-1">Total Bayar</small>
        <span class="fw-800 text-success fs-5"><?= rupiah($order['grand_total']) ?></span>
      </div>
      <div>
        <small class="text-muted d-block mb-1">Tanggal Pesanan</small>
        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB
      </div>
    </div>

    <!-- Info Pengiriman -->
    <div class="admin-card p-4">
      <h6 class="fw-800 mb-3 text-success"><i class="fas fa-map-marker-alt me-1"></i> Info Pengiriman</h6>
      <div class="mb-2">
        <small class="text-muted d-block mb-1">Penerima</small>
        <strong><?= e($order['shipping_name']) ?></strong>
      </div>
      <div class="mb-2">
        <small class="text-muted d-block mb-1">Telepon</small>
        <?= e($order['shipping_phone']) ?>
      </div>
      <div class="mb-2">
        <small class="text-muted d-block mb-1">Alamat Lengkap</small>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
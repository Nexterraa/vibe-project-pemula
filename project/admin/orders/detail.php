<?php
// admin/orders/detail.php
$pageTitle = 'Detail Pesanan';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { setFlash('danger','Pesanan tidak ditemukan.'); header('Location:'.BASE_URL.'/admin/orders/index.php'); exit; }

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $valid = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($newStatus, $valid)) {
        $pdo->prepare('UPDATE orders SET status=?,updated_at=NOW() WHERE id=?')->execute([$newStatus, $id]);
        setFlash('success', 'Status pesanan diperbarui ke: ' . ucfirst($newStatus));
        header('Location:'.BASE_URL.'/admin/orders/detail.php?id='.$id); exit;
    }
}

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $items->execute([$id]);
$orderItems = $items->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-receipt me-2 text-success"></i>Detail Pesanan</h4>
  <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card mb-4">
      <div class="admin-card-header"><h5><i class="fas fa-box me-2 text-success"></i>Item Pesanan</h5></div>
      <div class="table-responsive">
        <table class="table admin-table">
          <thead><tr><th>Produk</th><th class="text-center">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
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
  </div>
  <div class="col-lg-4">
    <!-- Order Info -->
    <div class="admin-card mb-4 p-4">
      <h6 class="fw-800 mb-3 text-green">Info Pesanan</h6>
      <div class="mb-2"><small class="text-muted d-block">Kode</small><strong><?= e($order['order_code']) ?></strong></div>
      <div class="mb-2"><small class="text-muted d-block">Tanggal</small><?= date('d M Y H:i', strtotime($order['created_at'])) ?></div>
      <div class="mb-2"><small class="text-muted d-block">Pembayaran</small><?= strtoupper($order['payment_method']) ?></div>
      <div class="mb-3"><small class="text-muted d-block">Status</small><span class="badge badge-status-<?= $order['status'] ?> px-3 py-2"><?= ucfirst($order['status']) ?></span></div>
      <!-- Update Status -->
      <form method="POST">
        <label class="form-label fw-600 small">Update Status:</label>
        <div class="d-flex gap-2">
          <select name="status" class="form-select form-select-sm">
            <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $s===$order['status']?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-success-custom btn-sm px-3"><i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
    <!-- Customer Info -->
    <div class="admin-card p-4">
      <h6 class="fw-800 mb-3 text-green">Info Pengiriman</h6>
      <div class="mb-2"><small class="text-muted d-block">Penerima</small><strong><?= e($order['shipping_name']) ?></strong></div>
      <div class="mb-2"><small class="text-muted d-block">Telepon</small><?= e($order['shipping_phone']) ?></div>
      <div class="mb-2"><small class="text-muted d-block">Alamat</small><?= e($order['shipping_address']) ?></div>
      <?php if ($order['notes']): ?><div><small class="text-muted d-block">Catatan</small><em><?= e($order['notes']) ?></em></div><?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
// order_success.php
$pageTitle = 'Pesanan Berhasil';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

$orderCode = $_SESSION['last_order_code'] ?? null;
$orderId   = $_SESSION['last_order_id'] ?? null;
if (!$orderId) { header('Location: ' . BASE_URL); exit; }

$stmt = $pdo->prepare('SELECT o.*, GROUP_CONCAT(oi.product_name SEPARATOR ", ") as items_list FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.id = ? AND o.user_id = ? GROUP BY o.id');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: ' . BASE_URL); exit; }

// Clear session order refs
unset($_SESSION['last_order_code'], $_SESSION['last_order_id']);

include 'includes/header.php';
?>
<div class="container py-5 text-center">
  <div class="bg-white rounded-4 shadow-sm p-5 mx-auto" style="max-width:580px;">
    <div class="mb-4" style="font-size:5rem;">🎉</div>
    <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width:80px;height:80px;font-size:2.5rem;">
      <i class="fas fa-check"></i>
    </div>
    <h2 class="fw-800 text-success mb-2">Pesanan Berhasil!</h2>
    <p class="text-muted mb-4">Terima kasih telah berbelanja di <strong>Toko Sayur Online</strong>. Pesanan Anda sedang diproses.</p>
    <div class="border rounded-3 p-3 mb-4 text-start bg-light-subtle">
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted small">Kode Pesanan</span>
        <strong class="text-success"><?= e($order['order_code']) ?></strong>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted small">Metode Bayar</span>
        <strong><?= $order['payment_method'] === 'cod' ? '💵 Bayar di Tempat' : '🏦 Transfer Bank' ?></strong>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted small">Total Bayar</span>
        <strong class="text-success"><?= rupiah($order['grand_total']) ?></strong>
      </div>
      <div class="d-flex justify-content-between">
        <span class="text-muted small">Dikirim ke</span>
        <strong class="text-end" style="max-width:55%;"><?= e($order['shipping_name']) ?></strong>
      </div>
    </div>
    <?php if ($order['payment_method'] === 'transfer'): ?>
    <div class="alert alert-info text-start mb-4">
      <strong><i class="fas fa-info-circle me-1"></i>Info Transfer:</strong><br>
      Silakan transfer ke <strong>BCA 1234567890 a.n. Toko Sayur Online</strong> sebesar <strong><?= rupiah($order['grand_total']) ?></strong> dan konfirmasi via WhatsApp ke <strong>081234567890</strong>.
    </div>
    <?php endif; ?>
    <div class="d-flex gap-3 justify-content-center">
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-4 fw-600">
        <i class="fas fa-store me-2"></i>Belanja Lagi
      </a>
      <a href="<?= BASE_URL ?>" class="btn btn-outline-success px-4 fw-600">
        <i class="fas fa-home me-2"></i>Home
      </a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>

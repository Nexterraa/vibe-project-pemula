<?php
// checkout.php
$pageTitle = 'Checkout';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

$cart = getCart();
if (empty($cart)) { header('Location: ' . BASE_URL . '/cart.php'); exit; }

$user = getCurrentUser();
$cartTotal = getCartTotal();
$shippingFee = 10000;
$grandTotal  = $cartTotal + $shippingFee;

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/cart.php">Keranjang</a></li>
      <li class="breadcrumb-item active">Checkout</li>
    </ol>
  </nav>
  <h2 class="fw-800 mb-4"><i class="fas fa-credit-card me-2 text-success"></i>Checkout</h2>

  <form action="<?= BASE_URL ?>/checkout_handler.php" method="POST" id="checkoutForm">
    <div class="row g-4">
      <!-- Shipping Info -->
      <div class="col-lg-7">
        <div class="checkout-card">
          <h5><i class="fas fa-map-marker-alt me-2 text-success"></i>Informasi Pengiriman</h5>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Nama Penerima *</label>
              <input type="text" name="shipping_name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">No. Telepon *</label>
              <input type="tel" name="shipping_phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">Alamat Lengkap *</label>
              <textarea name="shipping_address" class="form-control" rows="3" required><?= e($user['address'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Misal: Tolong ditaruh di pos satpam..."></textarea>
            </div>
          </div>
        </div>

        <div class="checkout-card">
          <h5><i class="fas fa-wallet me-2 text-success"></i>Metode Pembayaran</h5>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="d-flex align-items-start gap-3 border rounded-3 p-3 cursor-pointer payment-option" style="cursor:pointer;">
                <input type="radio" name="payment_method" value="cod" checked class="mt-1 form-check-input">
                <div>
                  <div class="fw-700">💵 Bayar di Tempat (COD)</div>
                  <small class="text-muted">Bayar saat barang tiba di rumah Anda</small>
                </div>
              </label>
            </div>
            <div class="col-sm-6">
              <label class="d-flex align-items-start gap-3 border rounded-3 p-3" style="cursor:pointer;">
                <input type="radio" name="payment_method" value="transfer" class="mt-1 form-check-input">
                <div>
                  <div class="fw-700">🏦 Transfer Bank</div>
                  <small class="text-muted">Transfer ke rekening yang akan kami kirimkan</small>
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-5">
        <div class="checkout-card">
          <h5><i class="fas fa-receipt me-2 text-success"></i>Ringkasan Pesanan</h5>
          <div class="table-responsive">
            <table class="table table-sm mb-3">
              <thead class="table-light">
                <tr>
                  <th>Produk</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cart as $item): ?>
                <tr>
                  <td>
                    <small class="fw-600"><?= e($item['name']) ?></small><br>
                    <small class="text-muted"><?= rupiah($item['price']) ?>/<?= e($item['unit']) ?></small>
                  </td>
                  <td class="text-center align-middle"><span class="badge bg-success-subtle text-success"><?= $item['qty'] ?></span></td>
                  <td class="text-end align-middle fw-700 text-success"><?= rupiah($item['price'] * $item['qty']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="border-top pt-3">
            <div class="d-flex justify-content-between mb-2 small">
              <span class="text-muted">Subtotal</span>
              <span class="fw-600"><?= rupiah($cartTotal) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 small">
              <span class="text-muted">Ongkos Kirim</span>
              <span class="fw-600"><?= rupiah($shippingFee) ?></span>
            </div>
            <div class="d-flex justify-content-between fw-800" style="font-size:1.15rem;">
              <span>Total Bayar</span>
              <span class="text-success"><?= rupiah($grandTotal) ?></span>
            </div>
          </div>
          <button type="submit" class="btn btn-success-custom w-100 py-3 mt-4 fw-700 rounded-3 fs-5" id="submitOrder">
            <i class="fas fa-check-circle me-2"></i>Konfirmasi Pesanan
          </button>
          <p class="text-center text-muted small mt-2 mb-0"><i class="fas fa-lock me-1"></i>Transaksi aman & terenkripsi</p>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitOrder');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});
</script>

<?php include 'includes/footer.php'; ?>

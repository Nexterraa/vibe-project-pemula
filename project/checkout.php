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
$freeShippingMin = 150000;
$shippingFee = ($cartTotal >= $freeShippingMin) ? 0 : 10000;
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

          <!-- Info Rekening (muncul saat Transfer dipilih) -->
          <div id="transferInfo" class="mt-3" style="display:none; transition: all 0.3s ease;">
            <div class="border rounded-3 p-3" style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);">
              <h6 class="fw-700 text-success mb-3"><i class="fas fa-university me-2"></i>Informasi Rekening Tujuan</h6>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Bank</span>
                <strong>BCA</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">No. Rekening</span>
                <strong class="font-monospace" style="letter-spacing:1px;">1234567890</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Atas Nama</span>
                <strong>Toko Sayur Online</strong>
              </div>
              <hr>
              <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Setelah checkout, Anda akan diminta upload bukti transfer.</small>
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
            <?php if ($cartTotal >= $freeShippingMin): ?>
            <div class="alert alert-success py-2 px-3 mb-3 small d-flex align-items-center gap-2">
              <i class="fas fa-truck"></i>
              <span><strong>Selamat!</strong> Kamu mendapatkan <strong>gratis ongkir</strong> karena belanja di atas <?= rupiah($freeShippingMin) ?>.</span>
            </div>
            <?php else: ?>
            <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-center gap-2">
              <i class="fas fa-info-circle"></i>
              <span>Tambah belanja <strong><?= rupiah($freeShippingMin - $cartTotal) ?></strong> lagi untuk gratis ongkir!</span>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between mb-2 small">
              <span class="text-muted">Subtotal</span>
              <span class="fw-600"><?= rupiah($cartTotal) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 small">
              <span class="text-muted">Ongkos Kirim</span>
              <?php if ($shippingFee == 0): ?>
              <span class="fw-600 text-success"><s class="text-muted me-1"><?= rupiah(10000) ?></s> Gratis</span>
              <?php else: ?>
              <span class="fw-600"><?= rupiah($shippingFee) ?></span>
              <?php endif; ?>
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
// Toggle info rekening
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
const transferInfo = document.getElementById('transferInfo');
paymentRadios.forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.value === 'transfer') {
      transferInfo.style.display = 'block';
      setTimeout(() => transferInfo.style.opacity = '1', 10);
    } else {
      transferInfo.style.opacity = '0';
      setTimeout(() => transferInfo.style.display = 'none', 300);
    }
  });
});

document.getElementById('checkoutForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitOrder');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});
</script>

<?php include 'includes/footer.php'; ?>

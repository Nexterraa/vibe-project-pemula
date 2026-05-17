<?php
// cart.php - Shopping Cart Page
$pageTitle = 'Keranjang Belanja';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

$cart        = getCart();
$cartTotal   = getCartTotal();
$shippingFee = 10000;
$grandTotal  = $cartTotal + $shippingFee;

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>



<!-- ===== Page Content ===== -->
<div class="container py-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
      <li class="breadcrumb-item active">Keranjang Belanja</li>
    </ol>
  </nav>

  <h2 class="fw-800 mb-4">
    <i class="fas fa-shopping-basket me-2 text-success"></i>Keranjang Belanja
    <span class="badge bg-success ms-2"><?= getCartCount() ?> item</span>
  </h2>

  <?php if (empty($cart)): ?>
  <!-- Empty State -->
  <div class="empty-state py-5 bg-white rounded-4 shadow-sm">
    <i class="fas fa-shopping-basket"></i>
    <h5 class="fw-700 mt-3">Keranjang Anda Kosong</h5>
    <p class="text-muted">Yuk mulai belanja sayur segar pilihan Anda!</p>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-5 py-2">
      <i class="fas fa-store me-2"></i>Mulai Belanja
    </a>
  </div>

  <?php else: ?>

  <!-- ===== Stock Warning Banner (inline, muncul di atas tabel) ===== -->
  <div id="stockWarningBanner"
       class="alert alert-danger d-none d-flex align-items-start gap-2 rounded-3 shadow-sm mb-3"
       role="alert"
       style="border-left:5px solid #dc3545;">
    <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0"></i>
    <span id="stockWarningText">Beberapa produk melebihi stok tersedia. Kurangi jumlah sebelum checkout.</span>
  </div>

  <div class="row g-4">

    <!-- ── Cart Items ── -->
    <div class="col-lg-8">
      <div class="bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
          <table class="table cart-table mb-0">
            <thead>
              <tr>
                <th class="ps-4">Produk</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Subtotal</th>
                <th class="text-center">Hapus</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart as $pid => $item): ?>
              <tr id="cart-row-<?= $pid ?>">
                <td class="ps-4">
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= getProductImage($item['image']) ?>" class="cart-img" alt="<?= e($item['name']) ?>">
                    <div>
                      <div class="fw-700"><?= e($item['name']) ?></div>
                      <small class="text-muted">per <?= e($item['unit']) ?></small>
                      <small class="d-block text-muted">
                        <i class="fas fa-boxes me-1"></i>Stok: <span class="fw-600"><?= (int)$item['stock'] ?></span>
                      </small>
                    </div>
                  </div>
                </td>
                <td class="text-center align-middle fw-600"><?= rupiah($item['price']) ?></td>
                <td class="text-center align-middle">
                  <div class="d-flex align-items-center justify-content-center gap-1">
                    <button class="qty-btn cart-qty-minus" data-pid="<?= $pid ?>"
                            style="width:28px;height:28px;font-size:.9rem;">−</button>
                    <input type="number" class="qty-input cart-qty-input"
                           value="<?= $item['qty'] ?>" min="1" max="<?= (int)$item['stock'] ?>"
                           data-pid="<?= $pid ?>">
                    <button class="qty-btn cart-qty-plus" data-pid="<?= $pid ?>"
                            style="width:28px;height:28px;font-size:.9rem;">+</button>
                  </div>
                </td>
                <td class="text-center align-middle fw-700 text-success" id="subtotal-<?= $pid ?>">
                  <?= rupiah($item['price'] * $item['qty']) ?>
                </td>
                <td class="text-center align-middle">
                  <button class="btn btn-sm btn-outline-danger rounded-3 cart-remove"
                          data-pid="<?= $pid ?>" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light-subtle">
          <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline-success btn-sm px-3">
            <i class="fas fa-arrow-left me-1"></i>Lanjut Belanja
          </a>
          <button class="btn btn-outline-danger btn-sm px-3" onclick="clearCart()">
            <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
          </button>
        </div>
      </div>
    </div>

    <!-- ── Order Summary ── -->
    <div class="col-lg-4">
      <div class="cart-summary">
        <h5 class="fw-800 mb-3 text-green">Ringkasan Pesanan</h5>
        <div class="cart-summary-row">
          <span class="text-muted">Subtotal</span>
          <span class="fw-600 cart-subtotal"><?= rupiah($cartTotal) ?></span>
        </div>
        <div class="cart-summary-row">
          <span class="text-muted">Ongkos Kirim</span>
          <span class="fw-600"><?= rupiah($shippingFee) ?></span>
        </div>
        <div class="cart-summary-row text-muted small">
          <span><i class="fas fa-tag me-1 text-success"></i>Kode Promo</span>
          <span>-</span>
        </div>
        <hr class="my-3">
        <div class="cart-summary-row cart-summary-total">
          <span>Total</span>
          <span class="cart-grand-total"><?= rupiah($grandTotal) ?></span>
        </div>

        <button id="checkoutBtn" onclick="goCheckout()"
                class="btn btn-success-custom w-100 py-3 mt-3 fw-700 rounded-3">
          <i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout
        </button>

        <div class="mt-3 p-3 bg-success-subtle rounded-3 small text-success fw-500">
          <i class="fas fa-shield-alt me-1"></i>Pembayaran aman &amp; terjamin
        </div>
      </div>
    </div>

  </div><!-- /row -->
  <?php endif; ?>
</div><!-- /container -->

<script>


// ── Stock validity check → enable/disable checkout ─────────────────────────
function checkStockValidity() {
  const inputs = document.querySelectorAll('.cart-qty-input');
  const overLimit = [];

  inputs.forEach(inp => {
    const qty   = parseInt(inp.value) || 0;
    const stock = parseInt(inp.max)   || 0;
    if (qty > stock) {
      const name = inp.closest('tr')?.querySelector('.fw-700')?.textContent?.trim() || '';
      overLimit.push({ name, stock });
    }
  });

  const btn    = document.getElementById('checkoutBtn');
  const banner = document.getElementById('stockWarningBanner');
  if (!btn) return; // page may be in empty-cart state

  if (overLimit.length > 0) {
    btn.disabled = true;
    btn.classList.add('btn-checkout-blocked');
    btn.title = 'Kurangi jumlah produk yang melebihi stok terlebih dahulu';
    if (banner) {
      const detail = overLimit.map(i => `<strong>${i.name}</strong> (maks. ${i.stock})`).join(', ');
      document.getElementById('stockWarningText').innerHTML =
        `Stok tidak cukup: ${detail}. Sesuaikan jumlah sebelum checkout.`;
      banner.classList.remove('d-none');
    }
  } else {
    btn.disabled = false;
    btn.classList.remove('btn-checkout-blocked');
    btn.title = '';
    banner?.classList.add('d-none');
  }
}

// ── Checkout ────────────────────────────────────────────────────────────────
function goCheckout() {
  const inputs = document.querySelectorAll('.cart-qty-input');
  for (const inp of inputs) {
    if (parseInt(inp.value) > parseInt(inp.max)) {
      showToast('danger', 'Harap kurangi jumlah produk yang melebihi stok sebelum checkout.');
      return;
    }
  }
  window.location.href = BASE_URL + '/checkout.php';
}

// ── Update qty via AJAX ─────────────────────────────────────────────────────
async function updateQty(pid, qty, stockMax) {
  if (qty < 1) qty = 1;
  if (qty > stockMax) {
    showToast('danger', `Stok tersedia hanya ${stockMax}. Jumlah tidak bisa melebihi stok.`);
    checkStockValidity();
    return;
  }
  try {
    const res  = await fetch(BASE_URL + '/cart_handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=update&product_id=${pid}&qty=${qty}`
    });
    const data = await res.json();
    if (data.success) {
      const sub = document.getElementById(`subtotal-${pid}`);
      if (sub) sub.textContent = data.subtotal;
      document.querySelector('.cart-subtotal').textContent    = data.total;
      document.querySelector('.cart-grand-total').textContent = data.grand_total;
      _updateBadge(data.count);
    } else {
      showToast('danger', data.message || 'Gagal memperbarui keranjang.');
      const inp = document.querySelector(`.cart-qty-input[data-pid="${pid}"]`);
      if (inp) { inp.value = parseInt(inp.max); inp.classList.remove('input-over-stock'); }
    }
  } catch {
    showToast('danger', 'Terjadi kesalahan koneksi.');
  }
  checkStockValidity();
}

// ── Cart badge ──────────────────────────────────────────────────────────────
function _updateBadge(count) {
  ['cartBadge', 'cartBadgeMobile'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = count;
    el.style.display = count > 0 ? '' : 'none';
  });
  // also update window.updateCartBadge from main.js if available
  if (typeof window.updateCartBadge === 'function') window.updateCartBadge(count);
}

// ── Clear cart ──────────────────────────────────────────────────────────────
async function clearCart() {
  if (!confirm('Yakin ingin mengosongkan semua keranjang?')) return;
  const res  = await fetch(BASE_URL + '/cart_handler.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=clear'
  });
  const data = await res.json();
  if (data.success) location.reload();
}

// ── Event listeners ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

  // + button
  document.querySelectorAll('.cart-qty-plus').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid    = btn.dataset.pid;
      const inp    = document.querySelector(`.cart-qty-input[data-pid="${pid}"]`);
      const stock  = parseInt(inp.max);
      const newQty = parseInt(inp.value) + 1;
      inp.value = newQty;
      inp.classList.toggle('input-over-stock', newQty > stock);
      if (newQty > stock) {
        showToast('danger', `Stok tersedia hanya ${stock} untuk produk ini.`);
        checkStockValidity();
      } else {
        updateQty(pid, newQty, stock);
      }
    });
  });

  // - button
  document.querySelectorAll('.cart-qty-minus').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid    = btn.dataset.pid;
      const inp    = document.querySelector(`.cart-qty-input[data-pid="${pid}"]`);
      const stock  = parseInt(inp.max);
      const newQty = Math.max(1, parseInt(inp.value) - 1);
      inp.value = newQty;
      inp.classList.remove('input-over-stock');
      updateQty(pid, newQty, stock);
    });
  });

  // Direct input
  document.querySelectorAll('.cart-qty-input').forEach(inp => {
    inp.addEventListener('input', () => {
      const stock = parseInt(inp.max);
      inp.classList.toggle('input-over-stock', (parseInt(inp.value) || 0) > stock);
      checkStockValidity();
    });
    inp.addEventListener('change', () => {
      const pid   = inp.dataset.pid;
      const stock = parseInt(inp.max);
      let   val   = parseInt(inp.value) || 1;
      if (val < 1) val = 1;
      inp.value = val;
      inp.classList.toggle('input-over-stock', val > stock);
      if (val > stock) {
        showToast('danger', `Stok tersedia hanya ${stock}. Jumlah tidak bisa melebihi stok.`);
        checkStockValidity();
      } else {
        updateQty(pid, val, stock);
      }
    });
  });

  // Remove button
  document.querySelectorAll('.cart-remove').forEach(btn => {
    btn.addEventListener('click', async () => {
      const pid = btn.dataset.pid;
      const res = await fetch(BASE_URL + '/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&product_id=${pid}`
      });
      const data = await res.json();
      if (data.success) {
        document.getElementById(`cart-row-${pid}`)?.remove();
        const sub = document.querySelector('.cart-subtotal');
        const grd = document.querySelector('.cart-grand-total');
        if (sub) sub.textContent = data.total;
        if (grd) grd.textContent = data.grand_total;
        _updateBadge(data.count);
        checkStockValidity();
        if (data.count === 0) location.reload();
      }
    });
  });

  // Run on page load
  checkStockValidity();
});
</script>

<?php include 'includes/footer.php'; ?>
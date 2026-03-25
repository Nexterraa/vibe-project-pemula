// ============================================================
// assets/js/main.js - Toko Sayur Online Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // ─── Navbar Scroll Effect ───
  const nav = document.getElementById('mainNav');
  if (nav) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // ─── Back To Top ───
  const btt = document.getElementById('backToTop');
  if (btt) {
    window.addEventListener('scroll', () => btt.classList.toggle('show', window.scrollY > 400));
    btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ─── Fade-In Sections on Scroll ───
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.15 });
  document.querySelectorAll('.fade-in-section').forEach(el => observer.observe(el));

  // ─── Add to Cart via AJAX ───
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', async function(e) {
      e.preventDefault();
      const productId = this.dataset.id;
      const qty = Number(document.getElementById(`qty-${productId}`)?.value || 1);
      if (!productId) return;

      const origHtml = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menambahkan...';

      try {
        const res = await fetch(BASE_URL + '/cart_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=add&product_id=${productId}&qty=${qty}`
        });
        const data = await res.json();
        showToast(data.success ? 'success' : 'danger', data.message);
        if (data.success) {
          // Update cart badges
          document.querySelectorAll('#cartBadge, .cart-badge').forEach(el => {
            el.textContent = data.count;
            el.style.display = data.count > 0 ? 'flex' : 'none';
          });
          this.innerHTML = '<i class="fas fa-check me-1"></i>Ditambahkan!';
          setTimeout(() => { this.innerHTML = origHtml; this.disabled = false; }, 2000);
        } else {
          this.innerHTML = origHtml;
          this.disabled = false;
        }
      } catch {
        showToast('danger', 'Terjadi kesalahan. Coba lagi.');
        this.innerHTML = origHtml;
        this.disabled = false;
      }
    });
  });

  // ─── Quantity Selector (Detail Page) ───
  const qtyDisplay = document.querySelector('.qty-display');
  if (qtyDisplay) {
    const maxStock = parseInt(qtyDisplay.dataset.max || 99);
    document.querySelector('.qty-minus')?.addEventListener('click', () => {
      const v = parseInt(qtyDisplay.textContent);
      if (v > 1) qtyDisplay.textContent = v - 1;
      updateQtyInput();
    });
    document.querySelector('.qty-plus')?.addEventListener('click', () => {
      const v = parseInt(qtyDisplay.textContent);
      if (v < maxStock) qtyDisplay.textContent = v + 1;
      updateQtyInput();
    });
    function updateQtyInput() {
      const pid = qtyDisplay.dataset.pid;
      const input = document.getElementById(`qty-${pid}`);
      if (input) input.value = qtyDisplay.textContent;
    }
  }

  // ─── Cart Quantity Update ───
  document.querySelectorAll('.cart-qty-input').forEach(input => {
    input.addEventListener('change', async function() {
      const pid = this.dataset.pid;
      const qty = parseInt(this.value);
      if (isNaN(qty) || qty < 1) { this.value = 1; return; }
      await updateCart(pid, qty);
    });
  });

  document.querySelectorAll('.cart-qty-minus').forEach(btn => {
    btn.addEventListener('click', async function() {
      const pid = this.dataset.pid;
      const input = document.querySelector(`.cart-qty-input[data-pid="${pid}"]`);
      const newQty = Math.max(1, parseInt(input.value) - 1);
      input.value = newQty;
      await updateCart(pid, newQty);
    });
  });

  document.querySelectorAll('.cart-qty-plus').forEach(btn => {
    btn.addEventListener('click', async function() {
      const pid = this.dataset.pid;
      const input = document.querySelector(`.cart-qty-input[data-pid="${pid}"]`);
      const newQty = parseInt(input.value) + 1;
      input.value = newQty;
      await updateCart(pid, newQty);
    });
  });

  async function updateCart(pid, qty) {
    try {
      const res = await fetch(BASE_URL + '/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&product_id=${pid}&qty=${qty}`
      });
      const data = await res.json();
      if (data.success) {
        // Update subtotal for this item
        const subtotalEl = document.getElementById(`subtotal-${pid}`);
        if (subtotalEl && data.subtotal) subtotalEl.textContent = data.subtotal;
        // Update totals
        if (data.total) {
          document.querySelectorAll('.cart-subtotal').forEach(el => el.textContent = data.total);
          const grand = document.querySelector('.cart-grand-total');
          if (grand && data.grand_total) grand.textContent = data.grand_total;
        }
        if (data.count !== undefined) {
          document.querySelectorAll('#cartBadge, .cart-badge').forEach(el => {
            el.textContent = data.count;
          });
        }
      } else {
        showToast('danger', data.message || 'Update gagal.');
      }
    } catch {
      showToast('danger', 'Terjadi kesalahan.');
    }
  }

  // ─── Cart Remove ───
  document.querySelectorAll('.cart-remove').forEach(btn => {
    btn.addEventListener('click', async function() {
      if (!confirm('Hapus item ini dari keranjang?')) return;
      const pid = this.dataset.pid;
      try {
        const res = await fetch(BASE_URL + '/cart_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=remove&product_id=${pid}`
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById(`cart-row-${pid}`)?.remove();
          showToast('success', data.message);
          document.querySelectorAll('#cartBadge, .cart-badge').forEach(el => {
            el.textContent = data.count || 0;
          });
          if (data.count === 0 || !document.querySelector('.cart-qty-input')) {
            setTimeout(() => location.reload(), 800);
          }
          // Update totals
          document.querySelectorAll('.cart-subtotal').forEach(el => {
            if (data.total) el.textContent = data.total;
          });
        }
      } catch {
        showToast('danger', 'Gagal menghapus item.');
      }
    });
  });

  // ─── Product Filter (on products.php) ───
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      filterBtns.forEach(b => b.classList.remove('active', 'btn-success'));
      filterBtns.forEach(b => b.classList.add('btn-outline-secondary'));
      this.classList.add('active', 'btn-success');
      this.classList.remove('btn-outline-secondary');

      const cat = this.dataset.category;
      document.querySelectorAll('.product-col').forEach(col => {
        if (cat === 'all' || col.dataset.category === cat) {
          col.style.display = 'block';
          col.classList.add('fade-in-section', 'visible');
        } else {
          col.style.display = 'none';
        }
      });
    });
  });

  // ─── Sort Products ───
  const sortSelect = document.getElementById('sortProducts');
  if (sortSelect) {
    sortSelect.addEventListener('change', function() {
      const url = new URL(window.location);
      url.searchParams.set('sort', this.value);
      window.location = url.toString();
    });
  }

  // ─── Admin Sidebar Toggle (Mobile) ───
  const sidebarToggle = document.getElementById('sidebarToggle');
  const adminSidebar  = document.querySelector('.admin-sidebar');
  if (sidebarToggle && adminSidebar) {
    sidebarToggle.addEventListener('click', () => adminSidebar.classList.toggle('open'));
  }

  // ─── Image Preview on Admin Product Form ───
  const imageInput = document.getElementById('productImageInput');
  const imagePreview = document.getElementById('imagePreview');
  if (imageInput && imagePreview) {
    imageInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => { imagePreview.src = e.target.result; imagePreview.style.display = 'block'; };
        reader.readAsDataURL(file);
      }
    });
  }

  // ─── Toast Notification ───
  function showToast(type, message) {
    const toastContainer = getOrCreateToastContainer();
    const id = 'toast-' + Date.now();
    const icons = { success: 'fas fa-check-circle', danger: 'fas fa-times-circle', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
    const colors = { success: '#2d6a4f', danger: '#e63946', warning: '#f4a261', info: '#3b82f6' };
    const toastEl = document.createElement('div');
    toastEl.id = id;
    toastEl.className = 'toast align-items-center text-white border-0 show';
    toastEl.setAttribute('role', 'alert');
    toastEl.style.cssText = `background:${colors[type]||'#333'};border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.2);min-width:280px;`;
    toastEl.innerHTML = `
      <div class="d-flex align-items-center gap-2 p-3">
        <i class="${icons[type]||'fas fa-info-circle'}" style="font-size:1.1rem;flex-shrink:0;"></i>
        <div class="flex-grow-1" style="font-size:.9rem;font-weight:600;">${message}</div>
        <button type="button" class="btn-close btn-close-white ms-2" onclick="document.getElementById('${id}').remove()"></button>
      </div>`;
    toastContainer.appendChild(toastEl);
    toastEl.style.animation = 'fadeInRight .3s ease';
    setTimeout(() => toastEl.style.opacity = '0', 3500);
    setTimeout(() => toastEl.remove(), 4000);
  }

  function getOrCreateToastContainer() {
    let c = document.getElementById('toastContainer');
    if (!c) {
      c = document.createElement('div');
      c.id = 'toastContainer';
      c.style.cssText = 'position:fixed;top:80px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;';
      document.body.appendChild(c);
    }
    return c;
  }

  // Make showToast globally accessible
  window.showToast = showToast;
});

// BASE_URL injected by PHP via a <script> tag in header

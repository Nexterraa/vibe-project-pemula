<?php
// index.php - Home Page
$pageTitle = 'Home';
require_once __DIR__ . '/config/functions.php';
startSession();

// Get featured products
$featured = $pdo->query('SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8')->fetchAll();

// Get categories
$categories = $pdo->query('SELECT c.*, COUNT(p.id) as prod_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.is_active=1 GROUP BY c.id')->fetchAll();

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<!-- ══════════ HERO SECTION ══════════ -->
<section class="hero-section">
  <div class="container position-relative" style="z-index:2;">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="hero-badge"><i class="fas fa-leaf"></i> 100% Organik & Segar</div>
        <h1 class="hero-title">Sayur <span>Segar Pilihan</span><br>Langsung ke Pintu Anda</h1>
        <p class="hero-text">Belanja sayuran segar berkualitas dari petani lokal terpercaya. Dikirim setiap hari agar selalu segar sampai di meja makan Anda.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="<?= BASE_URL ?>/products.php" class="btn btn-light fw-700 px-4 py-3 rounded-3">
            <i class="fas fa-store me-2 text-success"></i>Belanja Sekarang
          </a>
          <a href="<?= BASE_URL ?>/about.php" class="btn btn-outline-light fw-600 px-4 py-3 rounded-3">
            <i class="fas fa-info-circle me-2"></i>Pelajari Lebih
          </a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="hero-stat-value">500+</div>
            <div class="hero-stat-label">Pelanggan Puas</div>
          </div>
          <div>
            <div class="hero-stat-value">50+</div>
            <div class="hero-stat-label">Jenis Sayuran</div>
          </div>
          <div>
            <div class="hero-stat-value">4.9⭐</div>
            <div class="hero-stat-label">Rating Toko</div>
          </div>
        </div>
      </div>
      <div class="col-lg-5 position-relative">
        <div class="hero-image-wrap">
          <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500&q=80" alt="Sayuran Segar" loading="eager">
          <div class="hero-float hero-float-1">
            <div style="font-size:1.5rem;">🥬</div>
            <div><div class="fw-700 small">Segar Tiap Hari</div><div class="text-muted" style="font-size:.75rem;">Dipanen pagi hari</div></div>
          </div>
          <div class="hero-float hero-float-2">
            <div style="font-size:1.5rem;">🚚</div>
            <div><div class="fw-700 small">Kirim Cepat</div><div class="text-muted" style="font-size:.75rem;">Sampai dalam 2 jam</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ FEATURES ══════════ -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row g-4">
      <?php 
      $features = [
        ['icon'=>'fa-leaf','title'=>'100% Organik','desc'=>'Sayuran bebas pestisida, ditanam secara alami oleh petani lokal'],
        ['icon'=>'fa-truck','title'=>'Kirim Hari Ini','desc'=>'Pemesanan sebelum jam 12 siang dikirim di hari yang sama'],
        ['icon'=>'fa-shield-alt','title'=>'Jaminan Segar','desc'=>'Garansi uang kembali jika sayuran tidak segar saat tiba'],
        ['icon'=>'fa-headset','title'=>'CS 7x24 Jam','desc'=>'Tim kami siap membantu kapanpun Anda butuhkan'],
      ];
      foreach ($features as $f): ?>
      <div class="col-sm-6 col-lg-3 fade-in-section">
        <div class="feature-item h-100">
          <div class="feature-icon"><i class="fas <?= $f['icon'] ?>"></i></div>
          <div>
            <div class="feature-title"><?= $f['title'] ?></div>
            <p class="feature-desc"><?= $f['desc'] ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════ CATEGORIES ══════════ -->
<section class="py-5" style="background:var(--green-50);">
  <div class="container">
    <div class="text-center mb-4 fade-in-section">
      <h2 class="section-title">Kategori <span>Sayuran</span></h2>
      <div class="section-line mx-auto"></div>
      <p class="section-subtitle mt-2">Temukan jenis sayuran yang Anda cari dengan mudah</p>
    </div>
    <div class="row g-3">
      <?php 
      $catIcons = ['sayuran-hijau'=>'🥬','umbi-umbian'=>'🥕','buah-sayur'=>'🍅','kacang-kacangan'=>'🫘','jamur-rempah'=>'🍄'];
      foreach ($categories as $cat): ?>
      <div class="col-6 col-md-4 col-lg-2 fade-in-section">
        <a href="<?= BASE_URL ?>/products.php?category=<?= e($cat['slug']) ?>" class="category-card">
          <div class="category-icon"><?= $catIcons[$cat['slug']] ?? '🥗' ?></div>
          <div class="category-name"><?= e($cat['name']) ?></div>
          <div class="text-muted small mt-1"><?= $cat['prod_count'] ?> produk</div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════ FEATURED PRODUCTS ══════════ -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-section">
      <div>
        <h2 class="section-title">Produk <span>Unggulan</span></h2>
        <div class="section-line section-line-left"></div>
      </div>
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom btn-sm px-4 d-none d-sm-inline-flex align-items-center gap-2">
        <i class="fas fa-th"></i>Lihat Semua
      </a>
    </div>
    <div class="row g-3">
      <?php foreach ($featured as $p):
        $rating = getAverageRating($p['id']);
      ?>
      <div class="col-sm-6 col-md-4 col-xl-3 fade-in-section">
        <div class="product-card">
          <div class="product-img-wrap">
            <img src="<?= getProductImage($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
            <div class="product-badge"><i class="fas fa-star me-1"></i>Unggulan</div>
            <?php if ($p['stock'] < 1): ?>
              <div class="product-badge product-badge-out" style="top:36px;">Habis</div>
            <?php endif; ?>
            <div class="product-actions">
              <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $p['id'] ?>" class="action-btn" title="Lihat Detail">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </div>
          <div class="product-body">
            <div class="product-category"><?= e($p['cat_name']) ?></div>
            <div class="product-name"><?= e($p['name']) ?></div>
            <div class="d-flex align-items-center gap-1 mb-2">
              <?= getStarRating($rating['avg']) ?>
              <span class="product-rating ms-1">(<?= $rating['count'] ?>)</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="product-price"><?= rupiah($p['price']) ?></span>
                <span class="product-unit"> / <?= e($p['unit']) ?></span>
              </div>
              <span class="product-stock <?= $p['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                <i class="fas fa-box me-1"></i><?= $p['stock'] ?>
              </span>
            </div>
            <input type="hidden" id="qty-<?= $p['id'] ?>" value="1">
            <button class="btn-add-cart" data-id="<?= $p['id'] ?>" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
              <i class="fas fa-shopping-basket me-1"></i>
              <?= $p['stock'] > 0 ? 'Tambah Keranjang' : 'Stok Habis' ?>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4 d-sm-none">
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-5">Lihat Semua Produk</a>
    </div>
  </div>
</section>


<!-- ══════════ TESTIMONIALS ══════════ -->
<section class="py-5" style="background:var(--green-50);">
  <div class="container">
    <div class="text-center mb-4 fade-in-section">
      <h2 class="section-title">Kata <span>Pelanggan</span></h2>
      <div class="section-line mx-auto"></div>
    </div>
    <div class="row g-4">
      <?php
      $testimonials = [
        ['name'=>'Rina W.', 'city'=>'Jakarta', 'text'=>'Sayurannya segar banget! Bayam yang saya pesan masih crispy dan hijau meskipun dikirim siang hari. Sangat recommended!', 'rating'=>5, 'avatar'=>'R'],
        ['name'=>'Ahmad F.', 'city'=>'Bogor', 'text'=>'Harganya bersaing, kualitas tidak mengecewakan. Wortel dan kentangnya besar-besar dan segar. Pengiriman juga tepat waktu.', 'rating'=>5, 'avatar'=>'A'],
        ['name'=>'Dewi S.', 'city'=>'Depok', 'text'=>'Saya sudah langganan 3 bulan. Kualitas selalu konsisten dan paketernya rapi. Customer service juga responsif banget!', 'rating'=>4, 'avatar'=>'D'],
      ];
      foreach ($testimonials as $t): ?>
      <div class="col-md-4 fade-in-section">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border border-light">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:44px;height:44px;background:var(--green-700);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;"><?= $t['avatar'] ?></div>
            <div>
              <div class="fw-700"><?= $t['name'] ?></div>
              <div class="text-muted small"><i class="fas fa-map-marker-alt me-1 text-success"></i><?= $t['city'] ?></div>
            </div>
            <div class="ms-auto"><?= getStarRating($t['rating']) ?></div>
          </div>
          <p class="text-muted mb-0 small" style="line-height:1.7;">"<?= $t['text'] ?>"</p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

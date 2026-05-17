<?php
// index.php - Home Page
$pageTitle = 'Beranda';
require_once __DIR__ . '/config/functions.php';
startSession();

// Get featured products
$featured = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 LIMIT 8")->fetchAll();

// Get all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>


<!-- Hero Section -->
<section class="hero-section">
  <div class="container h-100" style="position:relative;z-index:1;">
    <div class="row h-100 align-items-center g-4">
      <div class="col-lg-6 hero-text text-white">
        <h1 class="display-4 fw-800 mb-3 animate__animated animate__fadeInUp">Sayur Segar Langsung dari <span class="text-warning">Kebun</span></h1>
        <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s opacity-90">Nikmati kualitas sayuran premium dengan harga terbaik. Kami menjamin kesegaran setiap produk yang sampai ke depan pintu Anda.</p>
        <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-2s">
          <a href="<?= BASE_URL ?>/products.php" class="btn btn-warning btn-lg fw-700 px-4 rounded-pill shadow-sm">Belanja Sekarang <i class="fas fa-arrow-right ms-2"></i></a>
          <a href="<?= BASE_URL ?>/about.php" class="btn btn-outline-light btn-lg fw-700 px-4 rounded-pill">Tentang Kami</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Bar -->
<section class="container py-4">
  <div class="row g-3 text-center">
    <div class="col-6 col-md-3">
      <div class="p-3 bg-white rounded-4 shadow-sm h-100">
        <i class="fas fa-leaf text-success fs-3 mb-2"></i>
        <h6 class="fw-700 mb-0">100% Organik</h6>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 bg-white rounded-4 shadow-sm h-100">
        <i class="fas fa-truck-fast text-success fs-3 mb-2"></i>
        <h6 class="fw-700 mb-0">Pengiriman Cepat</h6>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 bg-white rounded-4 shadow-sm h-100">
        <i class="fas fa-wallet text-success fs-3 mb-2"></i>
        <h6 class="fw-700 mb-0">Harga Terbaik</h6>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 bg-white rounded-4 shadow-sm h-100">
        <i class="fas fa-headset text-success fs-3 mb-2"></i>
        <h6 class="fw-700 mb-0">Dukungan 24/7</h6>
      </div>
    </div>
  </div>
</section>

<!-- Categories -->
<section class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h2 class="fw-800 text-green-800 mb-0">Kategori Pilihan</h2>
      <p class="text-muted mb-0">Temukan sayuran favorit berdasarkan kategori.</p>
    </div>
    <a href="<?= BASE_URL ?>/products.php" class="btn btn-link text-success fw-700 text-decoration-none p-0">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
  </div>
  <div class="row g-4">
    <?php
    $catEmoji = ['sayuran-hijau'=>'🥬','umbi-umbian'=>'🥕','buah-sayur'=>'🍅','kacang-kacangan'=>'🫘','jamur-rempah'=>'🍄'];
    $catDesc  = ['sayuran-hijau'=>'Bayam, kangkung, sawi & lainnya','umbi-umbian'=>'Wortel, kentang, ubi & lainnya','buah-sayur'=>'Tomat, cabai, terong & lainnya','kacang-kacangan'=>'Buncis, kacang panjang & lainnya','jamur-rempah'=>'Jamur, jahe, kunyit & lainnya'];
    foreach ($categories as $cat):
      $prodCount = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1");
      $prodCount->execute([$cat['id']]);
      $count = $prodCount->fetchColumn();
    ?>
    <div class="col-sm-6 col-lg-3">
      <a href="<?= BASE_URL ?>/products.php?category=<?= e($cat['slug']) ?>" class="cat-box-card">
        <div class="cat-box-icon">
          <span><?= $catEmoji[$cat['slug']] ?? '🥗' ?></span>
        </div>
        <div class="cat-box-body">
          <h6 class="cat-box-name"><?= e($cat['name']) ?></h6>
          <p class="cat-box-desc"><?= $catDesc[$cat['slug']] ?? '' ?></p>
          <span class="cat-box-count"><i class="fas fa-box-open me-1"></i><?= $count ?> Produk</span>
        </div>
        <div class="cat-box-arrow"><i class="fas fa-chevron-right"></i></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Featured Products -->
<section class="bg-light-green py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="fw-800 text-green-800 mb-0">Produk Unggulan</h2>
        <p class="text-muted mb-0">Produk paling segar pilihan para pelanggan.</p>
      </div>
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-link text-success fw-700 text-decoration-none p-0">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4">
      <?php foreach ($featured as $p): 
        $rating = getAverageRating($p['id']);
      ?>
      <div class="col-sm-6 col-lg-3">
        <div class="product-card">
          <div class="product-img-wrap">
            <img src="<?= getProductImage($p['image']) ?>" alt="<?= e($p['name']) ?>">
            <div class="product-badge"><i class="fas fa-star me-1"></i>Unggulan</div>
            <div class="product-actions">
              <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $p['id'] ?>" class="action-btn" title="Lihat Detail">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </div>
          <div class="product-body">
            <div class="product-category"><?= e($p['cat_name']) ?></div>
            <div class="product-name text-truncate"><?= e($p['name']) ?></div>
            <div class="d-flex align-items-center gap-1 mb-2">
              <?= getStarRating($rating['avg']) ?>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="product-price"><?= rupiah($p['price']) ?></span>
                <span class="product-unit"> / <?= e($p['unit']) ?></span>
              </div>
            </div>
            <input type="hidden" id="qty-<?= $p['id'] ?>" value="1">
            <button class="btn-add-cart w-100" data-id="<?= $p['id'] ?>" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
              <i class="fas fa-shopping-basket me-1"></i>
              <?= $p['stock'] > 0 ? 'Tambah' : 'Habis' ?>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

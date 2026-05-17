<?php
// product_detail.php
require_once __DIR__ . '/config/functions.php';
startSession();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . '/products.php'); exit; }

$stmt = $pdo->prepare('SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { header('Location: ' . BASE_URL . '/products.php'); exit; }

// Get ratings
$ratings = $pdo->prepare('SELECT r.*, u.name as user_name FROM product_ratings r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC');
$ratings->execute([$id]);
$reviews = $ratings->fetchAll();
$avgRating = getAverageRating($id);

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rate') {
    if (!isLoggedIn()) {
        setFlash('warning', 'Anda harus login untuk memberikan rating.');
    } else {
        $rVal = (int)($_POST['rating'] ?? 5);
        $rText = trim($_POST['review'] ?? '');
        $rVal = max(1, min(5, $rVal));
        $stmt2 = $pdo->prepare('INSERT INTO product_ratings (product_id, user_id, rating, review) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating), review=VALUES(review)');
        $stmt2->execute([$id, $_SESSION['user_id'], $rVal, $rText]);
        setFlash('success', 'Terima kasih atas ulasan Anda!');
        header('Location: ' . BASE_URL . '/product_detail.php?id=' . $id . '#reviews');
        exit;
    }
}

// Related products
$rel = $pdo->prepare('SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 LIMIT 4');
$rel->execute([$product['category_id'], $id]);
$related = $rel->fetchAll();

$pageTitle = $product['name'];
include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products.php">Produk</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products.php?category=<?= e($product['cat_slug']) ?>"><?= e($product['cat_name']) ?></a></li>
      <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
    </ol>
  </nav>

  <!-- Product Detail -->
  <div class="row g-4 mb-5">
    <div class="col-md-5">
      <img src="<?= getProductImage($product['image']) ?>" alt="<?= e($product['name']) ?>" class="product-detail-img">
    </div>
    <div class="col-md-7">
      <div class="badge bg-success-subtle text-success mb-2 px-3 py-1 rounded-pill"><?= e($product['cat_name']) ?></div>
      <h1 class="fw-800 mb-2" style="font-size:1.8rem;"><?= e($product['name']) ?></h1>
      <!-- Rating -->
      <div class="d-flex align-items-center gap-2 mb-3">
        <?= getStarRating($avgRating['avg']) ?>
        <span class="fw-600"><?= $avgRating['avg'] ?></span>
        <span class="text-muted small">(<?= $avgRating['count'] ?> ulasan)</span>
      </div>
      <div class="detail-price mb-1"><?= rupiah($product['price']) ?></div>
      <div class="text-muted mb-3">per <?= e($product['unit']) ?></div>
      <!-- Stock -->
      <div class="mb-3">
        <?php if ($product['stock'] > 0): ?>
          <span class="stock-badge stock-available"><i class="fas fa-check-circle me-1"></i>Stok tersedia: <?= $product['stock'] ?> <?= e($product['unit']) ?></span>
        <?php else: ?>
          <span class="stock-badge stock-out"><i class="fas fa-times-circle me-1"></i>Stok habis</span>
        <?php endif; ?>
      </div>
      <!-- Description -->
      <p class="text-muted mb-4" style="line-height:1.8;"><?= nl2br(e($product['description'])) ?></p>
      <!-- Qty + Add Cart -->
      <?php if ($product['stock'] > 0): ?>
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="qty-control">
          <button class="qty-btn qty-minus">−</button>
          <span class="qty-display" data-max="<?= $product['stock'] ?>" data-pid="<?= $product['id'] ?>">1</span>
          <button class="qty-btn qty-plus">+</button>
        </div>
        <span class="text-muted small">Maks. <?= $product['stock'] ?> <?= e($product['unit']) ?></span>
      </div>
      <input type="hidden" id="qty-<?= $product['id'] ?>" value="1">
      <div class="d-flex gap-2">
        <button class="btn-add-cart flex-grow-1" data-id="<?= $product['id'] ?>">
          <i class="fas fa-shopping-basket me-2"></i>Tambah ke Keranjang
        </button>
        <a href="<?= BASE_URL ?>/cart.php" class="btn btn-outline-success px-3 rounded-3">
          <i class="fas fa-shopping-cart"></i>
        </a>
      </div>
      <?php else: ?>
      <button class="btn-add-cart" disabled>
        <i class="fas fa-times-circle me-2"></i>Stok Habis
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reviews Section -->
  <div id="reviews" class="row g-4">
    <div class="col-md-8">
      <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
        <h5 class="fw-800 mb-4"><i class="fas fa-star text-warning me-2"></i>Ulasan Pembeli (<?= count($reviews) ?>)</h5>
        <?php if (empty($reviews)): ?>
          <p class="text-muted">Belum ada ulasan untuk produk ini. Jadilah yang pertama!</p>
        <?php else: ?>
          <?php foreach ($reviews as $rv): ?>
          <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
            <div style="width:40px;height:40px;background:var(--green-700);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0;"><?= strtoupper(substr($rv['user_name'],0,1)) ?></div>
            <div class="flex-grow-1">
              <div class="fw-700"><?= e($rv['user_name']) ?></div>
              <div class="d-flex gap-1 my-1"><?= getStarRating($rv['rating']) ?></div>
              <p class="text-muted small mb-1"><?= e($rv['review']) ?></p>
              <span class="text-muted" style="font-size:.78rem;"><?= timeAgo($rv['created_at']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <!-- Add Review Form -->
      <?php if (isLoggedIn()): ?>
      <div class="bg-white rounded-4 shadow-sm p-4">
        <h6 class="fw-800 mb-3">Tulis Ulasan Anda</h6>
        <form method="POST">
          <input type="hidden" name="action" value="rate">
          <div class="mb-3">
            <label class="form-label fw-600">Rating anda</label>
            <div class="star-rating" id="starRating">
              <?php for ($s=5;$s>=1;$s--): ?>
              <input type="radio" id="star<?= $s ?>" name="rating" value="<?= $s ?>" required>
              <label for="star<?= $s ?>" title="<?= $s ?> bintang"><i class="fas fa-star"></i></label>
              <?php endfor; ?>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600">Ulasan</label>
            <textarea name="review" class="form-control" rows="3" placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
          </div>
          <button type="submit" class="btn btn-success-custom px-4 fw-600">
            <i class="fas fa-paper-plane me-2"></i>Kirim Ulasan
          </button>
        </form>
      </div>
      <?php else: ?>
      <div class="alert alert-light border text-center">
        <a href="<?= BASE_URL ?>/auth/login.php" class="text-success fw-700">Login</a> untuk memberikan ulasan.
      </div>
      <?php endif; ?>
    </div>
    <!-- Related Products -->
    <div class="col-md-4">
      <div class="bg-white rounded-4 shadow-sm p-4">
        <h6 class="fw-800 mb-3"><i class="fas fa-th me-2 text-success"></i>Produk Terkait</h6>
        <?php foreach ($related as $rp): ?>
        <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $rp['id'] ?>" class="d-flex gap-3 mb-3 text-decoration-none card-hover p-2 rounded-3">
          <img src="<?= getProductImage($rp['image']) ?>" style="width:60px;height:50px;object-fit:cover;border-radius:8px;" alt="<?= e($rp['name']) ?>">
          <div>
            <div class="fw-600 small text-dark"><?= e($rp['name']) ?></div>
            <div class="text-success fw-700 small"><?= rupiah($rp['price']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

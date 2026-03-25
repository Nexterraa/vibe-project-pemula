<?php
// products.php - Product Catalog
$pageTitle = 'Produk';
require_once __DIR__ . '/config/functions.php';
startSession();

// Filters
$search   = trim($_GET['q'] ?? '');
$catSlug  = trim($_GET['category'] ?? '');
$sort     = $_GET['sort'] ?? 'newest';

// Build query
$where = ['p.is_active = 1'];
$params = [];
if ($search) { $where[] = 'p.name LIKE ?'; $params[] = "%$search%"; }
if ($catSlug) { $where[] = 'c.slug = ?'; $params[] = $catSlug; }

$orderBy = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc'   => 'p.name ASC',
    default      => 'p.created_at DESC'
};

$whereClause = implode(' AND ', $where);
$sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug
        FROM products p JOIN categories c ON p.category_id = c.id
        WHERE $whereClause ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$currentCat = null;
if ($catSlug) {
    foreach ($categories as $c) {
        if ($c['slug'] === $catSlug) { $currentCat = $c; break; }
    }
}

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
      <li class="breadcrumb-item active"><?= $currentCat ? e($currentCat['name']) : 'Semua Produk' ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <!-- Sidebar Filter -->
    <div class="col-lg-3">
      <div class="bg-white rounded-4 shadow-sm p-4 sticky-top" style="top:80px;">
        <h6 class="fw-800 text-green-800 mb-3"><i class="fas fa-filter me-2 text-success"></i>Filter Kategori</h6>
        <a href="<?= BASE_URL ?>/products.php<?= $search ? '?q=' . urlencode($search) : '' ?>"
           class="category-card text-start mb-2 d-flex align-items-center gap-2 p-3 <?= !$catSlug ? 'active' : '' ?>">
          <span>🥗</span> <span class="category-name">Semua Produk</span>
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/products.php?category=<?= e($cat['slug']) ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
           class="category-card text-start mb-2 d-flex align-items-center gap-2 p-3 <?= $catSlug === $cat['slug'] ? 'active' : '' ?>">
          <span><?= ['sayuran-hijau'=>'🥬','umbi-umbian'=>'🥕','buah-sayur'=>'🍅','kacang-kacangan'=>'🫘','jamur-rempah'=>'🍄'][$cat['slug']] ?? '🥗' ?></span>
          <span class="category-name"><?= e($cat['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="col-lg-9">
      <!-- Toolbar -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
          <h5 class="fw-800 mb-0"><?= $currentCat ? e($currentCat['name']) : 'Semua Produk' ?></h5>
          <small class="text-muted"><?= count($products) ?> produk ditemukan<?= $search ? ' untuk "<strong>' . e($search) . '</strong>"' : '' ?></small>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <!-- Search -->
          <form action="<?= BASE_URL ?>/products.php" method="GET" class="d-flex">
            <?php if ($catSlug) echo '<input type="hidden" name="category" value="' . e($catSlug) . '">'; ?>
            <div class="input-group input-group-sm">
              <input type="text" name="q" class="form-control" placeholder="Cari produk..." value="<?= e($search) ?>">
              <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </form>
          <!-- Sort -->
          <select id="sortProducts" class="form-select form-select-sm" style="width:auto;">
            <option value="newest"    <?= $sort==='newest'    ? 'selected' : '' ?>>Terbaru</option>
            <option value="price_asc" <?= $sort==='price_asc' ? 'selected' : '' ?>>Harga ↑</option>
            <option value="price_desc"<?= $sort==='price_desc'? 'selected' : '' ?>>Harga ↓</option>
            <option value="name_asc"  <?= $sort==='name_asc'  ? 'selected' : '' ?>>A–Z</option>
          </select>
        </div>
      </div>

      <?php if (empty($products)): ?>
      <div class="empty-state py-5">
        <i class="fas fa-search-minus d-block mb-3 fs-1 text-success"></i>
        <h5 class="fw-700">Produk Tidak Ditemukan</h5>
        <p class="text-muted">Coba kata kunci atau kategori lain.</p>
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-4">Lihat Semua Produk</a>
      </div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($products as $p):
          $rating = getAverageRating($p['id']);
        ?>
        <div class="col-sm-6 col-xl-4 product-col fade-in-section" data-category="<?= e($p['cat_slug']) ?>">
          <div class="product-card">
            <div class="product-img-wrap">
              <img src="<?= getProductImage($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
              <?php if ($p['is_featured']): ?>
                <div class="product-badge"><i class="fas fa-star me-1"></i>Unggulan</div>
              <?php endif; ?>
              <?php if ($p['stock'] < 1): ?>
                <div class="product-badge product-badge-out" style="<?= $p['is_featured']?'top:36px':'' ?>">Habis</div>
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
                <span class="small <?= $p['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
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
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

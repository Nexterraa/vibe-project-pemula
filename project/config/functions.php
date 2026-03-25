<?php
// ============================================================
// config/functions.php - Helper Functions
// ============================================================

require_once __DIR__ . '/koneksi.php';

// ──────────────── SESSION ────────────────
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

// ──────────────── FORMAT ────────────────
function rupiah(float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function truncate(string $text, int $limit = 80): string {
    return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
}

function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60)         return 'Baru saja';
    if ($diff < 3600)       return floor($diff/60) . ' menit lalu';
    if ($diff < 86400)      return floor($diff/3600) . ' jam lalu';
    if ($diff < 2592000)    return floor($diff/86400) . ' hari lalu';
    return date('d M Y', $time);
}

// ──────────────── FLASH MESSAGES ────────────────
function setFlash(string $type, string $message) {
    startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function showFlash() {
    $flash = getFlash();
    if (!$flash) return;
    $icons = ['success' => '✅', 'danger' => '❌', 'warning' => '⚠️', 'info' => 'ℹ️'];
    $icon = $icons[$flash['type']] ?? 'ℹ️';
    echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' alert-dismissible fade show shadow-sm" role="alert">';
    echo $icon . ' ' . htmlspecialchars($flash['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
}

// ──────────────── PRODUCTS ────────────────
function getProductImage(string $image = ''): string {
    if ($image && file_exists(UPLOAD_PATH . $image)) {
        return UPLOAD_URL . $image;
    }
    $placeholders = [
        'bayam.jpg'         => 'https://placehold.co/400x300/2d6a4f/white?text=Bayam+Segar',
        'kangkung.jpg'      => 'https://placehold.co/400x300/40916c/white?text=Kangkung',
        'sawi.jpg'          => 'https://placehold.co/400x300/52b788/white?text=Sawi+Hijau',
        'selada.jpg'        => 'https://placehold.co/400x300/74c69d/white?text=Selada',
        'wortel.jpg'        => 'https://placehold.co/400x300/e76f51/white?text=Wortel',
        'kentang.jpg'       => 'https://placehold.co/400x300/e9c46a/white?text=Kentang',
        'ubi.jpg'           => 'https://placehold.co/400x300/7b2d8b/white?text=Ubi+Ungu',
        'tomat.jpg'         => 'https://placehold.co/400x300/e63946/white?text=Tomat',
        'cabai.jpg'         => 'https://placehold.co/400x300/d62828/white?text=Cabai',
        'paprika.jpg'       => 'https://placehold.co/400x300/f4a261/white?text=Paprika',
        'terong.jpg'        => 'https://placehold.co/400x300/6a0572/white?text=Terong',
        'buncis.jpg'        => 'https://placehold.co/400x300/6ab187/white?text=Buncis',
        'kacang_panjang.jpg'=> 'https://placehold.co/400x300/3a7d44/white?text=Kacang+Panjang',
        'jamur_tiram.jpg'   => 'https://placehold.co/400x300/adb5bd/white?text=Jamur+Tiram',
        'jahe.jpg'          => 'https://placehold.co/400x300/b5838d/white?text=Jahe+Merah',
    ];
    return $placeholders[$image] ?? 'https://placehold.co/400x300/2d6a4f/white?text=Sayur+Segar';
}

function getStarRating(float $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating
            ? '<i class="fas fa-star text-warning"></i>'
            : ($i - 0.5 <= $rating
                ? '<i class="fas fa-star-half-alt text-warning"></i>'
                : '<i class="far fa-star text-warning"></i>');
    }
    return $html;
}

function getAverageRating(int $productId): array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT AVG(rating) as avg_r, COUNT(*) as count FROM product_ratings WHERE product_id = ?');
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    return ['avg' => round((float)$row['avg_r'], 1), 'count' => (int)$row['count']];
}

// ──────────────── CART ────────────────
function getCart(): array {
    startSession();
    return $_SESSION['cart'] ?? [];
}

function getCartCount(): int {
    return array_sum(array_column(getCart(), 'qty'));
}

function getCartTotal(): float {
    $total = 0;
    foreach (getCart() as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function addToCart(int $productId, int $qty = 1): array {
    global $pdo;
    startSession();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
    if ($product['stock'] < 1) return ['success' => false, 'message' => 'Stok produk habis.'];

    $cart = getCart();
    $currentQty = isset($cart[$productId]) ? $cart[$productId]['qty'] : 0;
    $newQty = $currentQty + $qty;
    if ($newQty > $product['stock']) {
        return ['success' => false, 'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product['stock']];
    }

    $_SESSION['cart'][$productId] = [
        'id'    => $productId,
        'name'  => $product['name'],
        'price' => (float)$product['price'],
        'image' => $product['image'],
        'unit'  => $product['unit'],
        'stock' => (int)$product['stock'],
        'qty'   => $newQty,
    ];
    return ['success' => true, 'message' => $product['name'] . ' ditambahkan ke keranjang!', 'count' => getCartCount()];
}

function generateOrderCode(): string {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
}

// ──────────────── SANITIZE ────────────────
function clean(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

<?php
// cart_handler.php - AJAX Cart API
header('Content-Type: application/json');
require_once __DIR__ . '/config/functions.php';
startSession();

$action    = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$qty       = max(1, (int)($_POST['qty'] ?? 1));

const SHIPPING_FEE = 10000;

function jsonResponse(array $data) {
    echo json_encode($data);
    exit;
}

function calcTotals(): array {
    $total = getCartTotal();
    $grand = $total + SHIPPING_FEE;
    return [
        'count'       => getCartCount(),
        'total'       => rupiah($total),
        'grand_total' => rupiah($grand),
        'raw_total'   => $total,
    ];
}

switch ($action) {
    case 'add':
        if (!isLoggedIn()) {
            jsonResponse(['success'=>false,'message'=>'Silakan login terlebih dahulu untuk berbelanja.','redirect'=>BASE_URL.'/auth/login.php']);
        }
        $result = addToCart($productId, $qty);
        $result = array_merge($result, calcTotals());
        jsonResponse($result);

    case 'update':
        $cart = getCart();
        if (!isset($cart[$productId])) jsonResponse(['success'=>false,'message'=>'Item tidak ada di keranjang.']);
        global $pdo;
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id=?');
        $stmt->execute([$productId]);
        $stock = (int)($stmt->fetchColumn() ?: 0);
        if ($qty > $stock) jsonResponse(['success'=>false,'message'=>"Stok tidak cukup. Tersedia: $stock."]);
        $_SESSION['cart'][$productId]['qty'] = $qty;
        $item = $_SESSION['cart'][$productId];
        $subtotal = $item['price'] * $qty;
        $r = calcTotals();
        jsonResponse(['success'=>true,'message'=>'','subtotal'=>rupiah($subtotal)] + $r);

    case 'remove':
        if (isset($_SESSION['cart'][$productId])) {
            $name = $_SESSION['cart'][$productId]['name'];
            unset($_SESSION['cart'][$productId]);
            $r = calcTotals();
            jsonResponse(['success'=>true,'message'=>"$name dihapus dari keranjang."] + $r);
        }
        jsonResponse(['success'=>false,'message'=>'Item tidak ditemukan.']);

    case 'clear':
        $_SESSION['cart'] = [];
        jsonResponse(['success'=>true,'message'=>'Keranjang dikosongkan.','count'=>0]);

    default:
        jsonResponse(['success'=>false,'message'=>'Aksi tidak valid.']);
}

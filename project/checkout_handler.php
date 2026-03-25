<?php
// checkout_handler.php - Process Order
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/checkout.php');
    exit;
}

$cart = getCart();
if (empty($cart)) {
    setFlash('warning', 'Keranjang Anda kosong.');
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

$name    = trim($_POST['shipping_name'] ?? '');
$phone   = trim($_POST['shipping_phone'] ?? '');
$address = trim($_POST['shipping_address'] ?? '');
$notes   = trim($_POST['notes'] ?? '');
$payment = in_array($_POST['payment_method'] ?? '', ['cod','transfer']) ? $_POST['payment_method'] : 'cod';

if (empty($name) || empty($phone) || empty($address)) {
    setFlash('danger', 'Lengkapi semua data pengiriman.');
    header('Location: ' . BASE_URL . '/checkout.php');
    exit;
}

$shippingFee = 10000;
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}
$grandTotal = $total + $shippingFee;
$orderCode  = generateOrderCode();

try {
    $pdo->beginTransaction();

    // Validate stock & reduce
    foreach ($cart as $pid => $item) {
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
        $stmt->execute([$pid]);
        $row = $stmt->fetch();
        if (!$row || $row['stock'] < $item['qty']) {
            throw new Exception("Stok {$item['name']} tidak mencukupi.");
        }
        $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')->execute([$item['qty'], $pid]);
    }

    // Insert order
    $pdo->prepare('INSERT INTO orders (order_code,user_id,total_amount,shipping_fee,grand_total,shipping_name,shipping_phone,shipping_address,notes,payment_method,status) VALUES (?,?,?,?,?,?,?,?,?,?,"pending")')
        ->execute([$orderCode, $_SESSION['user_id'], $total, $shippingFee, $grandTotal, $name, $phone, $address, $notes, $payment]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    $insItem = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,price,quantity,subtotal) VALUES (?,?,?,?,?,?)');
    foreach ($cart as $pid => $item) {
        $insItem->execute([$orderId, $pid, $item['name'], $item['price'], $item['qty'], $item['price'] * $item['qty']]);
    }

    $pdo->commit();

    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['last_order_code'] = $orderCode;
    $_SESSION['last_order_id']   = $orderId;

    header('Location: ' . BASE_URL . '/order_success.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('danger', 'Gagal memproses pesanan: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/checkout.php');
    exit;
}

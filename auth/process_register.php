<?php
// auth/process_register.php
require_once __DIR__ . '/../config/functions.php';
startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/register.php');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

// Validasi
$errors = [];
if (strlen($name) < 3)          $errors[] = 'Nama minimal 3 karakter.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
if (strlen($password) < 6)      $errors[] = 'Password minimal 6 karakter.';
if ($password !== $confirm)     $errors[] = 'Konfirmasi password tidak cocok.';

// Cek email duplikat
if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = 'Email sudah terdaftar.';
}

if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    header('Location: ' . BASE_URL . '/auth/register.php');
    exit;
}

$hashed = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, "customer")');
$stmt->execute([$name, $email, $hashed, $phone, $address]);

$userId = $pdo->lastInsertId();
$_SESSION['user_id']    = $userId;
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;
$_SESSION['role']       = 'customer';

setFlash('success', 'Selamat datang, ' . $name . '! Akun Anda berhasil dibuat.');
header('Location: ' . BASE_URL . '/index.php');
exit;

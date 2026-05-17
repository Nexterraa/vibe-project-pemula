<?php
// auth/process_login.php
require_once __DIR__ . '/../config/functions.php';
startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);
$redirect = $_POST['redirect'] ?? BASE_URL;

if (empty($email) || empty($password)) {
    setFlash('danger', 'Email dan password wajib diisi.');
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    setFlash('danger', 'Email atau password salah.');
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Set session
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email']= $user['email'];
$_SESSION['role']      = $user['role'];

if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/admin/index.php');
} else {
    // Redirect ke halaman sebelumnya jika ada
    $safe = filter_var($redirect, FILTER_VALIDATE_URL) ? $redirect : BASE_URL;
    header('Location: ' . $safe);
}
exit;

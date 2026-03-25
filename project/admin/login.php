<?php
// admin/login.php
require_once __DIR__ . '/../config/functions.php';
startSession();
if (isAdmin()) { header('Location: ' . BASE_URL . '/admin/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt  = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "admin" LIMIT 1');
    $stmt->execute([$email]);
    $user  = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role']       = 'admin';
        header('Location: ' . BASE_URL . '/admin/index.php');
        exit;
    }
    setFlash('danger', 'Email atau password admin salah.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Toko Sayur Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body style="background:linear-gradient(135deg,#1b4332,#40916c);">
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-header" style="background:linear-gradient(135deg,#1b4332,#2d6a4f);">
      <div class="brand-icon"><i class="fas fa-gauge"></i></div>
      <h4 class="mb-1 fw-800">Admin Panel</h4>
      <p class="mb-0 opacity-75 small">Toko Sayur Online</p>
    </div>
    <div class="auth-body">
      <?php showFlash(); ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Email Admin</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
            <input type="email" name="email" class="form-control" value="admin@tokosayur.com" required autofocus>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt me-2"></i>Masuk sebagai Admin</button>
      </form>
      <div class="text-center mt-3">
        <a href="<?= BASE_URL ?>/index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Kembali ke toko</a>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

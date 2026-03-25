<?php
// auth/login.php
require_once __DIR__ . '/../config/functions.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . (isAdmin() ? '/admin/index.php' : '/index.php'));
    exit;
}
$redirect = $_GET['redirect'] ?? BASE_URL . '/index.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk | Toko Sayur Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-header">
      <div class="brand-icon"><i class="fas fa-leaf"></i></div>
      <h4 class="mb-1 fw-800">Selamat Datang Kembali!</h4>
      <p class="mb-0 opacity-75 small">Masuk ke akun Toko Sayur Online Anda</p>
    </div>
    <div class="auth-body">
      <?php showFlash(); ?>
      <form action="<?= BASE_URL ?>/auth/process_login.php" method="POST">
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required autofocus>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Masukkan password" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePwd()">
              <i class="fas fa-eye" id="pwdEye"></i>
            </button>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
            <label class="form-check-label small" for="rememberMe">Ingat saya</label>
          </div>
        </div>
        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>
      </form>
      <div class="auth-divider"><span>atau</span></div>
      <div class="text-center">
        <p class="mb-1 small text-muted">Belum punya akun?</p>
        <a href="<?= BASE_URL ?>/auth/register.php" class="btn btn-outline-success w-100 fw-600">
          <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
        </a>
      </div>
      <hr class="my-3">
      <div class="alert alert-light border small p-2 mb-0">
        <strong>Demo Login:</strong><br>
        👑 Admin: <code>admin@tokosayur.com</code> / <code>password</code><br>
        👤 User: <code>budi@example.com</code> / <code>password</code>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
  const inp = document.getElementById('passwordInput');
  const eye = document.getElementById('pwdEye');
  if (inp.type === 'password') { inp.type = 'text'; eye.classList.replace('fa-eye','fa-eye-slash'); }
  else { inp.type = 'password'; eye.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>
</body>
</html>

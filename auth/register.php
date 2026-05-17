<?php
// auth/register.php
require_once __DIR__ . '/../config/functions.php';
startSession();
if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar | Toko Sayur Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:520px;">
    <div class="auth-header">
      <div class="brand-icon"><i class="fas fa-leaf"></i></div>
      <h4 class="mb-1 fw-800">Buat Akun Baru</h4>
      <p class="mb-0 opacity-75 small">Daftar dan nikmati belanja sayur segar!</p>
    </div>
    <div class="auth-body">
      <?php showFlash(); ?>
      <form action="<?= BASE_URL ?>/auth/process_register.php" method="POST">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
              <input type="text" name="name" class="form-control" placeholder="Nama lengkap Anda" required minlength="3">
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" name="password" id="pwdField" class="form-control" placeholder="Min 6 karakter" required minlength="6">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwdField','eye1')">
                <i class="fas fa-eye" id="eye1"></i>
              </button>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" name="confirm_password" id="pwdField2" class="form-control" placeholder="Ulangi password" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwdField2','eye2')">
                <i class="fas fa-eye" id="eye2"></i>
              </button>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label">No. Telepon</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
              <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Alamat</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
              <input type="text" name="address" class="form-control" placeholder="Alamat pengiriman">
            </div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-login">
              <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </button>
          </div>
        </div>
      </form>
      <div class="auth-divider"><span>atau</span></div>
      <div class="text-center">
        <p class="mb-0 small text-muted">Sudah punya akun?
          <a href="<?= BASE_URL ?>/auth/login.php" class="text-success fw-600">Masuk di sini</a>
        </p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd(fieldId, eyeId) {
  const f = document.getElementById(fieldId), e = document.getElementById(eyeId);
  if (f.type === 'password') { f.type = 'text'; e.classList.replace('fa-eye','fa-eye-slash'); }
  else { f.type = 'password'; e.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>
</body>
</html>

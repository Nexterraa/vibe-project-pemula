<?php
// admin/includes/header.php
require_once __DIR__ . '/../../config/functions.php';
startSession();
requireAdmin();
$adminPage = basename($_SERVER['PHP_SELF']);
$adminDir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle).' | ' : '' ?>Admin | Toko Sayur Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-brand">
    <div class="d-flex align-items-center gap-2">
      <div class="brand-icon brand-icon-sm"><i class="fas fa-leaf"></i></div>
      <div>
        <div class="text-white fw-700" style="font-size:.95rem;">Toko Sayur</div>
        <div class="text-success fw-600" style="font-size:.7rem;">Admin Panel</div>
      </div>
    </div>
  </div>
  <nav class="admin-nav">
    <div class="admin-nav-label">Utama</div>
    <a href="<?= BASE_URL ?>/admin/index.php" class="admin-nav-item <?= ($adminPage==='index.php'&&$adminDir==='admin')?'active':'' ?>">
      <i class="fas fa-gauge"></i> Dashboard
    </a>
    <div class="admin-nav-label">Produk</div>
    <a href="<?= BASE_URL ?>/admin/products/index.php" class="admin-nav-item <?= ($adminDir==='products')?'active':'' ?>">
      <i class="fas fa-box"></i> Produk
    </a>
    <div class="admin-nav-label">Transaksi</div>
    <a href="<?= BASE_URL ?>/admin/orders/index.php" class="admin-nav-item <?= ($adminDir==='orders')?'active':'' ?>">
      <i class="fas fa-receipt"></i> Riwayat Pesanan
    </a>
    <div class="admin-nav-label">Pengguna</div>
    <a href="<?= BASE_URL ?>/admin/users/index.php" class="admin-nav-item <?= ($adminDir==='users')?'active':'' ?>">
      <i class="fas fa-users"></i> Pengguna
    </a>
    <div class="admin-nav-label">Akun</div>
    <a href="<?= BASE_URL ?>/auth/logout.php" class="admin-nav-item" style="color:#ff9999!important;">
      <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
  </nav>
</aside>
<!-- Content Wrapper -->
<div class="admin-content">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary d-lg-none">
        <i class="fas fa-bars"></i>
      </button>
      <div>
        <h6 class="mb-0 fw-700"><?= $pageTitle ?? 'Dashboard' ?></h6>
        <small class="text-muted"><?= date('l, d F Y') ?></small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <div class="me-2 text-end d-none d-sm-block">
        <div class="fw-700 small"><?= e($_SESSION['user_name']) ?></div>
        <div class="text-muted" style="font-size:.75rem;">Administrator</div>
      </div>
      <div class="brand-icon brand-icon-sm"><i class="fas fa-user-shield"></i></div>
    </div>
  </div>
  <main class="admin-main">
  <?php showFlash(); ?>

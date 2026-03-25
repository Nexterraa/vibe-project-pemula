<?php
// includes/header.php - Site-wide Navbar/Header
require_once __DIR__ . '/../config/functions.php';
startSession();
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF']);

// Get all categories for nav
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Toko Sayur Online - Belanja sayur segar berkualitas, dikirim langsung ke rumah Anda.">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>Toko Sayur Online 🥬</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <small><i class="fas fa-truck me-1"></i> Gratis ongkir pembelian di atas Rp 150.000</small>
      <small><i class="fas fa-clock me-1"></i> Buka setiap hari: 07.00 - 20.00 WIB</small>
    </div>
  </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg sticky-top navbar-custom" id="mainNav">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
      <div class="brand-icon"><i class="fas fa-leaf"></i></div>
      <div>
        <span class="brand-name">Toko Sayur</span>
        <span class="brand-sub d-block">Online</span>
      </div>
    </a>

    <!-- Mobile Cart + Toggler -->
    <div class="d-flex align-items-center gap-2 d-lg-none">
      <a href="<?= BASE_URL ?>/cart.php" class="btn btn-cart-mobile position-relative">
        <i class="fas fa-shopping-basket"></i>
        <?php if ($cartCount > 0): ?>
          <span class="cart-badge-mobile"><?= $cartCount ?></span>
        <?php endif; ?>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <div class="collapse navbar-collapse" id="navMenu">
      <!-- Nav Links -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= $currentPage === 'products.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/products.php" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-store me-1"></i>Produk
          </a>
          <ul class="dropdown-menu dropdown-menu-custom">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/products.php"><i class="fas fa-th me-2 text-success"></i>Semua Produk</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php foreach ($cats as $c): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/products.php?category=<?= e($c['slug']) ?>">
              <i class="fas fa-tag me-2 text-success"></i><?= e($c['name']) ?>
            </a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/about.php">
            <i class="fas fa-info-circle me-1"></i>Tentang Kami
          </a>
        </li>
      </ul>


      <!-- Cart & Auth -->
      <div class="d-flex align-items-center gap-2">
        <!-- Cart Button -->
        <a href="<?= BASE_URL ?>/cart.php" class="btn btn-cart position-relative">
          <i class="fas fa-shopping-basket me-1"></i>
          <span class="d-none d-xl-inline">Keranjang</span>
          <?php if ($cartCount > 0): ?>
            <span class="cart-badge" id="cartBadge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>

        <?php if (isLoggedIn()): ?>
          <!-- User Dropdown -->
          <div class="dropdown">
            <button class="btn btn-user dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="fas fa-user-circle me-1"></i>
              <span class="d-none d-lg-inline"><?= e(explode(' ', $_SESSION['user_name'])[0]) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
              <li><h6 class="dropdown-header"><?= e($_SESSION['user_name']) ?></h6></li>
              <li><small class="dropdown-item-text text-muted"><?= e($_SESSION['user_email']) ?></small></li>
              <li><hr class="dropdown-divider"></li>
              <?php if (isAdmin()): ?>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php"><i class="fas fa-gauge me-2 text-success"></i>Dashboard Admin</a></li>
              <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i>Keluar</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-outline-success btn-sm fw-600">
            <i class="fas fa-sign-in-alt me-1"></i>Masuk
          </a>
          <a href="<?= BASE_URL ?>/auth/register.php" class="btn btn-success btn-sm fw-600">
            <i class="fas fa-user-plus me-1"></i>Daftar
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Flash Message Container -->
<div class="container mt-3" id="flashContainer">
  <?php showFlash(); ?>
</div>

<main>

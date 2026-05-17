<?php // includes/footer.php ?>
</main>

<!-- Footer -->
<footer class="footer mt-5">
  <div class="footer-top">
    <div class="container">
      <div class="row g-4">
        <!-- Brand -->
        <div class="col-lg-4 col-md-6">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="brand-icon brand-icon-sm"><i class="fas fa-leaf"></i></div>
            <div>
              <span class="fw-bold text-white fs-5">Toko Sayur Online</span>
              <div class="text-success-light small">Segar dari Kebun, Langsung ke Meja Anda</div>
            </div>
          </div>
          <p class="text-muted-light mb-3">Kami menyediakan sayuran segar berkualitas tinggi yang dipanen langsung dari petani lokal terpercaya.</p>
          <div class="d-flex gap-3">
            <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-btn"><i class="fab fa-whatsapp"></i></a>
            <a href="#" class="social-btn"><i class="fab fa-tiktok"></i></a>
          </div>
        </div>
        <!-- Navigasi -->
        <div class="col-lg-2 col-md-6 col-6">
          <h6 class="footer-heading">Navigasi</h6>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/products.php">Produk</a></li>
            <li><a href="<?= BASE_URL ?>/about.php">Tentang Kami</a></li>
            <li><a href="#">Blog</a></li>
          </ul>
        </div>
        <!-- Kategori -->
        <div class="col-lg-3 col-md-6 col-6">
          <h6 class="footer-heading">Kategori</h6>
          <ul class="footer-links">
            <?php
            global $pdo;
            $fcats = $pdo->query('SELECT * FROM categories LIMIT 5')->fetchAll();
            foreach ($fcats as $fc):
            ?>
            <li><a href="<?= BASE_URL ?>/products.php?category=<?= e($fc['slug']) ?>"><?= e($fc['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <!-- Kontak -->
        <div class="col-lg-3 col-md-6">
          <h6 class="footer-heading">Hubungi Kami</h6>
          <ul class="footer-contact">
            <li><i class="fas fa-map-marker-alt"></i> Jl. Sayur Segar No. 1, Jakarta</li>
            <li><i class="fas fa-phone"></i> +62 812-3456-7890</li>
            <li><i class="fas fa-envelope"></i> hello@tokosayur.com</li>
            <li><i class="fas fa-clock"></i> Senin–Minggu, 07.00–20.00</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <p class="mb-0 text-muted-light small">© <?= date('Y') ?> Toko Sayur Online. All rights reserved.</p>
        <div class="d-flex gap-3">
          <img src="https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white" alt="PHP" height="20">
          <img src="https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL" height="20">
          <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white" alt="Bootstrap" height="20">
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" title="Kembali ke atas">
  <i class="fas fa-chevron-up"></i>
</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>

<?php
// about.php
$pageTitle = 'Tentang Kami';
require_once __DIR__ . '/config/functions.php';
startSession();
include 'includes/header.php';
?>
<div class="container py-5">
  <!-- Hero About -->
  <div class="row align-items-center g-5 mb-5">
    <div class="col-md-6 fade-in-section">
      <div class="badge bg-success-subtle text-success mb-2 px-3 py-2 rounded-pill fw-600">Tentang Kami</div>
      <h1 class="fw-800 mb-3" style="font-size:2.2rem;">Membangun <span class="text-success">Meja Makan Sehat</span> Indonesia</h1>
      <p class="text-muted mb-4" style="line-height:1.8;">Toko Sayur Online lahir dari kecintaan kami terhadap gaya hidup sehat dan keinginan untuk membantu petani lokal menjangkau pembeli langsung. Kami percaya bahwa sayuran segar adalah fondasi kesehatan keluarga Indonesia.</p>
      <div class="d-flex gap-3">
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-success-custom px-4"><i class="fas fa-store me-2"></i>Belanja Sekarang</a>
        <a href="#contact" class="btn btn-outline-success px-4"><i class="fas fa-envelope me-2"></i>Hubungi Kami</a>
      </div>
    </div>
    <div class="col-md-6 fade-in-section">
      <img src="https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=600&q=80" class="w-100 rounded-4 shadow" alt="Tentang Kami" style="max-height:380px;object-fit:cover;">
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-4 text-center mb-5">
    <?php $stats = [['500+','Pelanggan Aktif'],['15+','Jenis Sayuran'],['4.9★','Rating Rata-rata'],['2+','Tahun Beroperasi']];
    foreach ($stats as $s): ?>
    <div class="col-6 col-md-3 fade-in-section">
      <div class="bg-white rounded-4 shadow-sm p-4 border">
        <div class="fw-800 text-success" style="font-size:2rem;"><?= $s[0] ?></div>
        <div class="text-muted small fw-600"><?= $s[1] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Values -->
  <div class="row g-4 mb-5">
    <div class="col-12 text-center mb-2 fade-in-section">
      <h2 class="section-title">Nilai <span>Kami</span></h2>
      <div class="section-line mx-auto"></div>
    </div>
    <?php $values = [['🌱','Organik','Semua produk kami dipilih dari petani organik bersertifikat'],['🤝','Bermitra','Kami bermitra langsung dengan petani lokal demi harga terbaik'],['🚀','Cepat','Pengiriman hari yang sama untuk area Jabodetabek'],['♻️','Ramah Lingkungan','Kemasan kami menggunakan bahan yang bisa didaur ulang']];
    foreach ($values as $v): ?>
    <div class="col-sm-6 col-lg-3 fade-in-section">
      <div class="bg-white rounded-4 shadow-sm p-4 text-center h-100 border card-hover">
        <div style="font-size:2.5rem;" class="mb-3"><?= $v[0] ?></div>
        <div class="fw-700 mb-2"><?= $v[1] ?></div>
        <p class="text-muted small mb-0"><?= $v[2] ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Contact -->
  <div id="contact" class="bg-white rounded-4 shadow-sm p-5 fade-in-section">
    <div class="row align-items-center g-4">
      <div class="col-md-6">
        <h3 class="fw-800 mb-3">Hubungi <span class="text-success">Kami</span></h3>
        <ul class="list-unstyled footer-contact" style="color:var(--text-dark)!important;">
          <li class="mb-3"><i class="fas fa-map-marker-alt text-success" style="width:20px;"></i> Jl. Sayur Segar No. 1, Jakarta Selatan</li>
          <li class="mb-3"><i class="fas fa-phone text-success" style="width:20px;"></i> +62 812-3456-7890</li>
          <li class="mb-3"><i class="fas fa-envelope text-success" style="width:20px;"></i> hello@tokosayur.com</li>
          <li><i class="fas fa-clock text-success" style="width:20px;"></i> Senin–Minggu, 07.00–20.00 WIB</li>
        </ul>
      </div>
      <div class="col-md-6">
        <form>
          <div class="mb-3"><input type="text" class="form-control" placeholder="Nama Anda"></div>
          <div class="mb-3"><input type="email" class="form-control" placeholder="Email Anda"></div>
          <div class="mb-3"><textarea class="form-control" rows="3" placeholder="Pesan Anda..."></textarea></div>
          <button type="submit" class="btn btn-success-custom px-4 fw-600">Kirim Pesan</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>

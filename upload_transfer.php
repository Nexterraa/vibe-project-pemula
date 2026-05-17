<?php
// upload_transfer.php - Upload Bukti Transfer
$pageTitle = 'Upload Bukti Transfer';
require_once __DIR__ . '/config/functions.php';
startSession();
requireLogin();

$orderId = (int)($_GET['order_id'] ?? 0);
if (!$orderId) { header('Location: ' . BASE_URL); exit; }

// Pastikan order milik user ini dan metode pembayaran transfer
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? AND payment_method = "transfer"');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: ' . BASE_URL); exit; }

include 'includes/header.php';
?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>

<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
      <li class="breadcrumb-item active">Upload Bukti Transfer</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="checkout-card text-center">
        <div style="font-size:3.5rem;" class="mb-3">🏦</div>
        <h3 class="fw-800 text-success mb-2">Transfer Pembayaran</h3>
        <p class="text-muted mb-4">Silakan transfer ke rekening berikut lalu upload bukti transfer.</p>

        <!-- Info Rekening -->
        <div class="border rounded-3 p-4 mb-4 text-start" style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);">
          <h6 class="fw-700 text-success mb-3"><i class="fas fa-university me-2"></i>Informasi Rekening</h6>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Bank</span>
            <strong>BCA</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">No. Rekening</span>
            <strong class="font-monospace" style="letter-spacing:1px;">1234567890</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Atas Nama</span>
            <strong>Toko Sayur Online</strong>
          </div>
          <hr>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Jumlah Transfer</span>
            <strong class="text-success fs-5"><?= rupiah($order['grand_total']) ?></strong>
          </div>
        </div>

        <!-- Info Pesanan -->
        <div class="border rounded-3 p-3 mb-4 text-start bg-light-subtle">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted small">Kode Pesanan</span>
            <strong class="text-success font-monospace"><?= e($order['order_code']) ?></strong>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted small">Status</span>
            <span class="badge bg-warning text-dark">Menunggu Bukti Transfer</span>
          </div>
        </div>

        <!-- Form Upload -->
        <form action="<?= BASE_URL ?>/upload_transfer_handler.php" method="POST" enctype="multipart/form-data" id="uploadForm">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          
          <div class="text-start mb-3">
            <label class="form-label fw-700"><i class="fas fa-image me-1 text-success"></i>Upload Bukti Transfer *</label>
            <div class="upload-area border border-2 border-dashed rounded-3 p-4 text-center" id="uploadArea" style="cursor:pointer; border-color: #2d6a4f !important; background: #f8fdf9; transition: all 0.3s ease;">
              <div id="uploadPlaceholder">
                <i class="fas fa-cloud-upload-alt fs-1 text-success mb-2 d-block"></i>
                <p class="mb-1 fw-600">Klik atau drag gambar ke sini</p>
                <small class="text-muted">Format: JPG, PNG, GIF (maks. 5MB)</small>
              </div>
              <img id="previewImage" src="" alt="Preview" class="img-fluid rounded-3 d-none" style="max-height:250px;">
              <input type="file" name="payment_proof" id="fileInput" accept="image/jpeg,image/png,image/gif" class="d-none" required>
            </div>
          </div>

          <button type="submit" class="btn btn-success-custom w-100 py-3 fw-700 rounded-3 fs-5" id="submitBtn" disabled>
            <i class="fas fa-paper-plane me-2"></i>Kirim Bukti Transfer
          </button>
          <p class="text-center text-muted small mt-2 mb-0">
            <i class="fas fa-lock me-1"></i>Bukti transfer akan diverifikasi oleh admin
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.upload-area:hover, .upload-area.dragover {
  background: #e8f5e9 !important;
  border-color: #1b5e20 !important;
  transform: scale(1.01);
}
.border-dashed { border-style: dashed !important; }
</style>

<script>
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const previewImage = document.getElementById('previewImage');
const placeholder = document.getElementById('uploadPlaceholder');
const submitBtn = document.getElementById('submitBtn');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('dragover'); });
uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
uploadArea.addEventListener('drop', e => {
  e.preventDefault();
  uploadArea.classList.remove('dragover');
  if (e.dataTransfer.files.length) {
    fileInput.files = e.dataTransfer.files;
    handleFile(e.dataTransfer.files[0]);
  }
});

fileInput.addEventListener('change', function() {
  if (this.files.length) handleFile(this.files[0]);
});

function handleFile(file) {
  const maxSize = 5 * 1024 * 1024;
  const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!validTypes.includes(file.type)) { alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.'); return; }
  if (file.size > maxSize) { alert('Ukuran file terlalu besar. Maksimal 5MB.'); return; }
  const reader = new FileReader();
  reader.onload = e => {
    previewImage.src = e.target.result;
    previewImage.classList.remove('d-none');
    placeholder.classList.add('d-none');
    submitBtn.disabled = false;
  };
  reader.readAsDataURL(file);
}

document.getElementById('uploadForm').addEventListener('submit', function() {
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
});
</script>

<?php include 'includes/footer.php'; ?>

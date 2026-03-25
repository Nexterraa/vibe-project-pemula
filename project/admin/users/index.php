<?php
// admin/users/index.php
$pageTitle = 'Manajemen Pengguna';
require_once __DIR__ . '/../../config/functions.php';
startSession(); requireAdmin();

$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) as order_count FROM users u ORDER BY u.created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="admin-page-title"><i class="fas fa-users me-2 text-success"></i>Manajemen Pengguna</h4>
  <span class="badge bg-success fs-6"><?= count($users) ?> pengguna</span>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table">
      <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th class="text-center">Role</th><th class="text-center">Pesanan</th><th>Bergabung</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div style="width:36px;height:36px;background:<?= $u['role']==='admin'?'var(--green-900)':'var(--green-700)' ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;">
                <?= strtoupper(substr($u['name'],0,1)) ?>
              </div>
              <div class="fw-600"><?= e($u['name']) ?></div>
            </div>
          </td>
          <td class="text-muted"><?= e($u['email']) ?></td>
          <td class="text-muted"><?= e($u['phone'] ?? '-') ?></td>
          <td class="text-center">
            <span class="badge <?= $u['role']==='admin'?'bg-danger':'bg-success' ?>">
              <?= $u['role']==='admin'?'👑 Admin':'👤 Customer' ?>
            </span>
          </td>
          <td class="text-center"><span class="badge bg-primary"><?= $u['order_count'] ?></span></td>
          <td class="text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

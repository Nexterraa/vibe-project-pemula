# Fitur Transfer & Bukti Pembayaran — Transfer Bank + Konfirmasi Admin

Menambahkan alur lengkap pembayaran via Transfer Bank: user upload bukti transfer setelah checkout, dan admin dapat melihat + memverifikasi bukti tersebut dari dashboard.

---

## Ringkasan Perubahan

### Alur User (Transfer)
1. Di halaman `checkout.php`: saat pilih **Transfer Bank** → muncul info rekening tujuan.
2. Setelah submit → order dibuat → redirect ke halaman **upload bukti transfer** (bukan langsung `order_success`).
3. User upload gambar bukti transfer → tersimpan di `uploads/transfer/`.
4. Setelah upload → redirect ke `order_success.php`.

### Alur Admin
1. Di `admin/orders/index.php`: nama halaman ganti jadi **Pesanan**, tampilkan kolom **No. Pesanan** + tombol **"Cek"**.
2. Saat klik **Cek** → buka `admin/orders/detail.php` yang telah diperbarui:
   - Tampil detail pesanan + item.
   - Tampil gambar bukti transfer (jika ada).
   - Dropdown **Status Pesanan** (pending, processing, shipped, delivered, cancelled) + tombol **Save**.
   - Tombol **Terima** (set status = `valid`) dan **Tolak** (set status = `ditolak`).

---

## Perubahan Database

### [MODIFY] `toko_sayur.sql` — Kolom baru di tabel `orders`
```sql
ALTER TABLE `orders`
  ADD COLUMN `payment_proof` varchar(255) DEFAULT NULL AFTER `payment_method`,
  MODIFY COLUMN `status` enum('pending','processing','shipped','delivered','cancelled','valid','ditolak') NOT NULL DEFAULT 'pending';
```

> Didistribusikan sebagai file SQL terpisah: `database_update.sql` — agar tidak perlu reimport keseluruhan.

---

## File yang Akan Dibuat / Diubah

### 1. Database
#### [NEW] `database_update.sql`
SQL ALTER TABLE untuk menambah kolom `payment_proof` dan extend enum `status`.

---

### 2. Checkout & Upload

#### [MODIFY] `checkout.php`
- Tambah JS: saat radio "transfer" dipilih → tampilkan info nomor rekening.
- Form action tetap ke `checkout_handler.php`.

#### [MODIFY] `checkout_handler.php`
- Setelah order berhasil dibuat dan payment_method = transfer → redirect ke `upload_transfer.php?order_id=xxx`.
- Jika COD → redirect ke `order_success.php` seperti biasa.

#### [NEW] `upload_transfer.php`
- Halaman untuk upload bukti transfer setelah checkout berhasil.
- Tampilkan info rekening, nomor pesanan, total bayar.
- Form upload gambar (PNG/JPG/JPEG/GIF maks 5MB).

#### [NEW] `upload_transfer_handler.php`
- Proses upload file gambar ke `uploads/transfer/`.
- Update kolom `payment_proof` di tabel `orders`.
- Redirect ke `order_success.php`.

#### [NEW] `uploads/transfer/` (folder)
- Folder penyimpanan bukti transfer (dibuat dengan `.htaccess` agar aman).

---

### 3. Admin Dashboard

#### [MODIFY] `admin/orders/index.php`
- Ubah judul halaman dari "Riwayat Pesanan" ke **"Pesanan"**.
- Tambah kolom **No. Pesanan** (order_code).
- Tambah kolom **Status** dengan badge berwarna.
- Ganti kolom-kolom berlebih dengan tombol **"Cek"** yang link ke `detail.php?id=xxx`.

#### [MODIFY] `admin/orders/detail.php`
- Tampilkan detail pesanan lengkap.
- Tampilkan gambar bukti transfer (jika ada).
- Tambah form **dropdown status** (semua enum value) + tombol **Save**.
- Tambah tombol **Terima** (AJAX/POST → set status = `valid`).
- Tambah tombol **Tolak** (AJAX/POST → set status = `ditolak`).

#### [NEW] `admin/orders/update_status.php`
- Handler POST untuk update `status` pesanan dari admin.
- Digunakan oleh tombol Save, Terima, dan Tolak.

---

## Open Questions

> [!IMPORTANT]
> **Nomor Rekening Bank**: Saat ini menggunakan `BCA 1234567890 a.n. Toko Sayur Online`. Apakah sama tetap, atau ingin diubah?

> [!NOTE]
> Upload bukti transfer hanya bisa dilakukan **sekali** langsung setelah checkout. Jika user menutup halaman sebelum upload, admin tetap bisa melihat pesanan (status: pending, bukti: kosong).

---

## Verification Plan

### Automated
- Jalankan SQL `database_update.sql` → pastikan kolom baru ada.
- Buka `http://localhost/project/checkout.php` → pilih Transfer → pastikan info rekening muncul.
- Submit checkout → pastikan diredirect ke `upload_transfer.php`.
- Upload gambar → pastikan file tersimpan dan DB terupdate.
- Buka `http://localhost/project/admin/orders/index.php` → pastikan ada tombol "Cek".
- Klik "Cek" → pastikan detail + gambar bukti muncul.
- Test tombol Terima/Tolak → pastikan status berubah.

### Manual
- Test dengan akun customer (checkout → upload → lihat di admin).
- Test tombol Terima dan Tolak di admin detail.

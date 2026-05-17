# 🥬 Presentasi Project — Toko Sayur Online

## 1. Gambaran Umum Project

**Toko Sayur Online** adalah aplikasi e-commerce berbasis web untuk penjualan sayuran segar secara online. Dibangun menggunakan arsitektur **PHP Native + MySQL** dengan pola **MVC sederhana**, berjalan di lingkungan **XAMPP** lokal.

| Aspek | Detail |
|-------|--------|
| **Bahasa** | PHP 8.x, JavaScript (Vanilla) |
| **Database** | MySQL (PDO) — `toko_sayur2` |
| **Styling** | Bootstrap 5 + Custom CSS |
| **Server** | Apache via XAMPP |
| **URL** | `http://localhost/project` |

---

## 2. Arsitektur Sistem

```mermaid
graph TB
    subgraph "CLIENT (Browser)"
        A["🖥️ User Interface<br/>HTML + CSS + JS"]
    end

    subgraph "SERVER (Apache/XAMPP)"
        B["📄 PHP Pages<br/>index.php, products.php, dll"]
        C["⚙️ Config Layer<br/>koneksi.php + functions.php"]
        D["🔐 Auth System<br/>login, register, session"]
        E["🛒 Cart System<br/>Session-based"]
        F["📦 Admin Panel<br/>CRUD Products, Orders, Users"]
    end

    subgraph "DATABASE (MySQL)"
        G["💾 toko_sayur2<br/>6 Tabel Utama"]
    end

    subgraph "FILE STORAGE"
        H["📁 uploads/<br/>products/ & transfer/"]
    end

    A <-->|HTTP Request/Response| B
    B --> C
    C --> G
    B --> D
    B --> E
    B --> F
    F --> H
```

---

## 3. Struktur Folder & Tata Letak Project

```mermaid
graph LR
    subgraph "📁 project/"
        A["index.php<br/>🏠 Beranda"]
        B["products.php<br/>🛍️ Katalog"]
        C["product_detail.php<br/>📋 Detail Produk"]
        D["cart.php<br/>🛒 Keranjang"]
        E["checkout.php<br/>💳 Checkout"]
        F["my_orders.php<br/>📦 Pesanan Saya"]
        G["about.php<br/>ℹ️ Tentang"]

        subgraph "📁 config/"
            H["koneksi.php<br/>🔌 Koneksi DB"]
            I["functions.php<br/>⚙️ Helper"]
        end

        subgraph "📁 auth/"
            J["login.php"]
            K["register.php"]
            L["process_login.php"]
            M["process_register.php"]
            N["logout.php"]
        end

        subgraph "📁 admin/"
            O["index.php<br/>📊 Dashboard"]
            P["login.php<br/>🔐 Login Admin"]

            subgraph "📁 products/"
                Q["index.php - List"]
                R["add.php - Tambah"]
                S["edit.php - Edit"]
                T["delete.php - Hapus"]
            end

            subgraph "📁 categories/"
                U["index.php - List"]
                V["add.php - Tambah"]
                W["edit.php - Edit"]
                X["delete.php - Hapus"]
            end

            subgraph "📁 orders/"
                Y["index.php - List"]
                Z["detail.php - Detail"]
                AA["update_status.php"]
            end
        end

        subgraph "📁 includes/"
            AB["header.php<br/>🔝 Navbar"]
            AC["footer.php<br/>🔻 Footer"]
        end

        subgraph "📁 uploads/"
            AD["products/ - Foto Produk"]
            AE["transfer/ - Bukti TF"]
        end
    end
```

---

## 4. ERD — Entity Relationship Diagram

```mermaid
erDiagram
    USERS {
        int id PK
        varchar name
        varchar email UK
        varchar password
        varchar phone
        text address
        enum role "admin | customer"
        varchar avatar
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        int id PK
        varchar name
        varchar slug UK
        text description
        varchar image
        timestamp created_at
    }

    PRODUCTS {
        int id PK
        int category_id FK
        varchar name
        varchar slug UK
        text description
        decimal price
        int stock
        varchar unit
        varchar image
        tinyint is_featured
        tinyint is_active
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        int id PK
        varchar order_code UK
        int user_id FK
        decimal total_amount
        decimal shipping_fee
        decimal grand_total
        varchar shipping_name
        varchar shipping_phone
        text shipping_address
        text notes
        enum payment_method "cod | transfer"
        varchar payment_proof
        enum status "pending|processing|shipped|delivered|cancelled|valid|ditolak"
        timestamp created_at
        timestamp updated_at
    }

    ORDER_ITEMS {
        int id PK
        int order_id FK
        int product_id FK
        varchar product_name
        decimal price
        int quantity
        decimal subtotal
    }

    PRODUCT_RATINGS {
        int id PK
        int product_id FK
        int user_id FK
        tinyint rating
        text review
        timestamp created_at
    }

    USERS ||--o{ ORDERS : "membuat"
    USERS ||--o{ PRODUCT_RATINGS : "memberikan"
    CATEGORIES ||--o{ PRODUCTS : "memiliki"
    PRODUCTS ||--o{ ORDER_ITEMS : "dipesan di"
    PRODUCTS ||--o{ PRODUCT_RATINGS : "dinilai di"
    ORDERS ||--o{ ORDER_ITEMS : "berisi"
```

### Hubungan Antar Tabel

| Relasi | Tipe | Penjelasan |
|--------|------|------------|
| `users` → `orders` | One-to-Many | 1 user bisa punya banyak pesanan |
| `users` → `product_ratings` | One-to-Many | 1 user bisa memberi banyak rating |
| `categories` → `products` | One-to-Many | 1 kategori memiliki banyak produk |
| `orders` → `order_items` | One-to-Many | 1 pesanan berisi banyak item |
| `products` → `order_items` | One-to-Many | 1 produk bisa ada di banyak order item |
| `products` → `product_ratings` | One-to-Many | 1 produk bisa punya banyak rating |
| `product_ratings` (`product_id`, `user_id`) | Unique | 1 user hanya bisa 1 rating per produk |

---

## 5. Kategori Sayuran

| No | Kategori | Slug | Contoh Produk |
|----|----------|------|---------------|
| 1 | 🥬 Sayuran Hijau | `sayuran-hijau` | Bayam, Kangkung, Sawi, Selada |
| 2 | 🥕 Umbi-Umbian | `umbi-umbian` | Wortel, Kentang, Ubi Jalar |
| 3 | 🍅 Buah Sayur | `buah-sayur` | Tomat, Cabai, Paprika, Terong |
| 4 | 🫘 Kacang-Kacangan | `kacang-kacangan` | Buncis, Kacang Panjang |
| 5 | 🍄 Jamur & Rempah | `jamur-rempah` | Jamur Tiram, Jahe Merah |

---

## 6. Alur Pengguna (Customer Flow)

```mermaid
flowchart TD
    START(["🏠 Buka Website"]) --> HOME["Halaman Beranda<br/>index.php"]
    HOME --> CAT["Lihat Kategori Sayuran"]
    HOME --> FEAT["Lihat Produk Unggulan"]
    CAT --> PROD["Halaman Produk<br/>products.php"]
    FEAT --> PROD

    PROD --> FILTER["Filter: Kategori / Cari / Sort"]
    FILTER --> PROD
    PROD --> DETAIL["Detail Produk<br/>product_detail.php"]

    DETAIL --> REVIEW["Baca & Tulis Ulasan"]
    DETAIL --> ADDCART{"Tambah ke Keranjang"}

    ADDCART -->|Belum Login| LOGIN["Login / Register<br/>auth/login.php"]
    LOGIN --> DETAIL
    ADDCART -->|Sudah Login| CART["Keranjang Belanja<br/>cart.php"]

    CART --> UPDATEQTY["Ubah Jumlah / Hapus Item"]
    UPDATEQTY --> CART
    CART --> CHECKOUT["Halaman Checkout<br/>checkout.php"]

    CHECKOUT --> ISIDATA["Isi Data Pengiriman<br/>+ Pilih Metode Bayar"]
    ISIDATA --> PROSES["Proses Pesanan<br/>checkout_handler.php"]

    PROSES -->|COD| SUCCESS["✅ Pesanan Berhasil<br/>order_success.php"]
    PROSES -->|Transfer| UPLOAD["Upload Bukti Transfer<br/>upload_transfer.php"]
    UPLOAD --> SUCCESS

    SUCCESS --> ORDERS["Pesanan Saya<br/>my_orders.php"]
    ORDERS --> TRACKSTATUS["Pantau Status Pesanan"]

    style START fill:#2d6a4f,color:#fff
    style SUCCESS fill:#40916c,color:#fff
    style LOGIN fill:#e76f51,color:#fff
```

---

## 7. Alur Admin (Admin Flow)

```mermaid
flowchart TD
    ADMINLOGIN(["🔐 Login Admin<br/>admin/login.php"]) --> DASHBOARD["📊 Dashboard Admin<br/>admin/index.php"]

    DASHBOARD --> MENU{Menu Admin}

    MENU --> PRODMGMT["📦 Manajemen Produk"]
    MENU --> ORDERMGMT["📋 Riwayat Pesanan"]
    MENU --> USERMGMT["👥 Manajemen Pengguna"]

    subgraph "CRUD Produk"
        PRODMGMT --> PRODLIST["Daftar Produk<br/>+ Kolom Kategori"]
        PRODLIST --> PRODADD["➕ Tambah Produk<br/>+ Pilih Kategori Sayuran<br/>+ Upload Foto"]
        PRODLIST --> PRODEDIT["✏️ Edit Produk<br/>+ Ubah Kategori<br/>+ Ganti Foto"]
        PRODLIST --> PRODDEL["🗑️ Hapus Produk"]
    end

    subgraph "Kelola Pesanan"
        ORDERMGMT --> ORDERLIST["Daftar Semua Pesanan"]
        ORDERLIST --> ORDERDETAIL["Detail Pesanan<br/>+ Cek Bukti Transfer"]
        ORDERDETAIL --> VERIFSTATUS["Verifikasi & Ubah Status"]
        VERIFSTATUS --> TERIMA["✅ Terima / Valid"]
        VERIFSTATUS --> TOLAK["❌ Tolak"]
        VERIFSTATUS --> STATUSLAIN["📝 Status Lainnya<br/>Pending → Diproses → Dikirim → Diterima"]
    end

    subgraph "Kelola User"
        USERMGMT --> USERLIST["Daftar Pengguna Terdaftar"]
    end

    style ADMINLOGIN fill:#1b4332,color:#fff
    style DASHBOARD fill:#2d6a4f,color:#fff
```

---

## 8. Alur Pemesanan (Order Flow)

```mermaid
sequenceDiagram
    actor Customer
    participant Web as Website
    participant Cart as Keranjang (Session)
    participant DB as Database
    participant Admin as Admin Panel

    Customer->>Web: Pilih Produk & Klik "Tambah Keranjang"
    Web->>Cart: Simpan item di SESSION

    Customer->>Web: Buka Keranjang
    Web->>Cart: Ambil data keranjang
    Cart-->>Web: Tampilkan item & total

    Customer->>Web: Klik Checkout
    Web->>Customer: Form data pengiriman

    Customer->>Web: Isi data & pilih metode bayar
    Web->>DB: Validasi stok (FOR UPDATE)
    DB-->>Web: Stok cukup ✅
    Web->>DB: INSERT orders + order_items
    Web->>DB: UPDATE stok produk (kurangi)
    Web->>Cart: Kosongkan keranjang

    alt Metode COD
        Web-->>Customer: Halaman Sukses
    else Metode Transfer
        Web-->>Customer: Halaman Upload Bukti TF
        Customer->>Web: Upload foto bukti transfer
        Web->>DB: UPDATE payment_proof
        Web-->>Customer: Halaman Sukses
    end

    Customer->>Web: Cek "Pesanan Saya"
    Web->>DB: SELECT orders WHERE user_id
    DB-->>Web: Data pesanan
    Web-->>Customer: Status pesanan

    Admin->>DB: Lihat pesanan masuk
    Admin->>DB: Verifikasi & ubah status
    Note over Admin,DB: pending → processing → shipped → delivered
```

---

## 9. Alur Autentikasi

```mermaid
flowchart LR
    subgraph "REGISTER"
        R1["Form Register<br/>auth/register.php"] --> R2["Validasi Input<br/>Email unik?"]
        R2 -->|Valid| R3["password_hash()<br/>INSERT users"]
        R3 --> R4["Redirect → Login"]
        R2 -->|Tidak Valid| R1
    end

    subgraph "LOGIN"
        L1["Form Login<br/>auth/login.php"] --> L2["process_login.php<br/>password_verify()"]
        L2 -->|Sukses + Admin| L3["🔐 Admin Dashboard"]
        L2 -->|Sukses + Customer| L4["🏠 Home / Redirect"]
        L2 -->|Gagal| L1
    end

    subgraph "SESSION"
        S1["$_SESSION['user_id']"]
        S2["$_SESSION['role']"]
        S3["$_SESSION['cart']"]
    end

    subgraph "ACCESS CONTROL"
        AC1["requireLogin()<br/>→ Perlu login"]
        AC2["requireAdmin()<br/>→ Perlu admin"]
    end

    L3 --> S1
    L4 --> S1
    S1 --> AC1
    S2 --> AC2

    style L3 fill:#1b4332,color:#fff
    style L4 fill:#40916c,color:#fff
```

---

## 10. Hubungan Antar File

```mermaid
flowchart TD
    subgraph "CORE CONFIG"
        KON["config/koneksi.php<br/>🔌 PDO + Constants"]
        FUN["config/functions.php<br/>⚙️ All Helpers"]
        KON --> FUN
    end

    subgraph "SHARED LAYOUT"
        HEAD["includes/header.php<br/>🔝 Navbar + CSS"]
        FOOT["includes/footer.php<br/>🔻 JS + Scripts"]
    end

    subgraph "PUBLIC PAGES"
        IDX["index.php"]
        PRD["products.php"]
        DET["product_detail.php"]
        CRT["cart.php"]
        CHK["checkout.php"]
        CHK_H["checkout_handler.php"]
        ORD["my_orders.php"]
        UPL["upload_transfer.php"]
        UPL_H["upload_transfer_handler.php"]
        ABT["about.php"]
    end

    subgraph "AUTH"
        ALG["auth/login.php"]
        ARG["auth/register.php"]
        APL["auth/process_login.php"]
        APR["auth/process_register.php"]
    end

    subgraph "CART API"
        CAPI["cart_handler.php<br/>🛒 AJAX JSON API"]
    end

    subgraph "ADMIN PAGES"
        ADM["admin/index.php"]
        ADP["admin/products/*"]
        ADC["admin/categories/*"]
        ADO["admin/orders/*"]
        ADU["admin/users/*"]
    end

    FUN -->|require_once| IDX
    FUN -->|require_once| PRD
    FUN -->|require_once| DET
    FUN -->|require_once| CRT
    FUN -->|require_once| CHK
    FUN -->|require_once| ADM

    IDX --> HEAD
    IDX --> FOOT
    PRD --> HEAD
    DET --> HEAD
    CRT --> HEAD

    IDX -->|"AJAX fetch()"| CAPI
    PRD -->|"AJAX fetch()"| CAPI
    DET -->|"AJAX fetch()"| CAPI

    CHK --> CHK_H
    UPL --> UPL_H
    ALG --> APL
    ARG --> APR
```

---

## 11. Fitur-Fitur Utama

### 🛍️ Sisi Customer (Pembeli)

| No | Fitur | Halaman | Deskripsi |
|----|-------|---------|-----------|
| 1 | Beranda | `index.php` | Hero section, kategori, produk unggulan |
| 2 | Katalog Produk | `products.php` | Filter kategori, search, sorting |
| 3 | Detail Produk | `product_detail.php` | Info lengkap, rating & ulasan, produk terkait |
| 4 | Keranjang | `cart.php` | AJAX add/update/remove, kalkulasi otomatis |
| 5 | Checkout | `checkout.php` | Form pengiriman, pilih metode bayar |
| 6 | Upload Bukti TF | `upload_transfer.php` | Drag & drop upload, preview gambar |
| 7 | Pesanan Saya | `my_orders.php` | Riwayat pesanan + status tracking |
| 8 | Rating & Ulasan | `product_detail.php` | Beri bintang 1-5 + komentar |
| 9 | Register & Login | `auth/*` | Autentikasi dengan `password_hash` |

### 🔐 Sisi Admin

| No | Fitur | Halaman | Deskripsi |
|----|-------|---------|-----------|
| 1 | Dashboard | `admin/index.php` | Statistik total produk, pesanan, pendapatan |
| 2 | CRUD Produk | `admin/products/*` | Tambah/edit/hapus produk + kategori + upload foto |
| 3 | CRUD Kategori | `admin/categories/*` | Kelola kategori sayuran |
| 4 | Kelola Pesanan | `admin/orders/*` | Lihat detail, verifikasi, ubah status |
| 5 | Lihat Pengguna | `admin/users/*` | Daftar pengguna terdaftar |

---

## 12. Teknologi & Keamanan

```mermaid
mindmap
  root((Toko Sayur Online))
    Backend
      PHP 8.x Native
      PDO Prepared Statements
      password_hash / password_verify
      Session Management
      CSRF via Session
    Frontend
      HTML5 Semantic
      Bootstrap 5
      Custom CSS
      Vanilla JavaScript
      AJAX Fetch API
      Animate.css
    Database
      MySQL / MariaDB
      utf8mb4 charset
      Foreign Key Constraints
      Transaction with ROLLBACK
      FOR UPDATE lock
    Keamanan
      htmlspecialchars XSS
      PDO Prepared Statement SQL Injection
      password_hash bcrypt
      requireLogin requireAdmin
      File upload validation
```

---

## 13. Akun Demo

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@tokosayur.com | password |
| **Customer** | budi@example.com | password |
| **Customer** | siti@example.com | password |

---

## 14. Cara Menjalankan Project

1. Pastikan **XAMPP** sudah terinstall dan **Apache + MySQL** aktif
2. Copy folder `project` ke `C:\xampp\htdocs\`
3. Buka **phpMyAdmin** → Import file `toko_sayur.sql`
4. Akses `http://localhost/project` di browser
5. Login sebagai **Admin** atau **Customer** menggunakan akun demo di atas

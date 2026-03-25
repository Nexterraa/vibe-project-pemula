# Task: Toko Sayur Online E-Commerce Website

## Phase 1: Database & Config
- [ ] Create `toko_sayur.sql` - full SQL schema + seed data
- [ ] Create `config/koneksi.php` - DB connection
- [ ] Create `config/session.php` - session helpers
- [ ] Create `config/functions.php` - shared utility functions

## Phase 2: Auth System
- [ ] `auth/login.php` - login page
- [ ] `auth/register.php` - register page
- [ ] `auth/logout.php` - logout handler
- [ ] `auth/process_login.php` - login backend
- [ ] `auth/process_register.php` - register backend

## Phase 3: User-Facing Frontend
- [ ] `includes/header.php` - navbar with cart badge
- [ ] `includes/footer.php` - footer
- [ ] `index.php` - home page (banner + featured products)
- [ ] `products.php` - catalog with category filter + search
- [ ] `product_detail.php` - single product detail + rating
- [ ] `cart.php` - shopping cart page
- [ ] `checkout.php` - checkout form & summary
- [ ] `order_success.php` - order confirmation

## Phase 4: Cart & Order Backend
- [ ] `cart_handler.php` - add/remove/update cart (session-based)
- [ ] `checkout_handler.php` - process order & save transaction

## Phase 5: Admin Dashboard
- [ ] `admin/index.php` - dashboard overview (stats)
- [ ] `admin/login.php` - admin login
- [ ] `admin/includes/header.php` - admin navbar/sidebar
- [ ] `admin/includes/footer.php` - admin footer
- [ ] `admin/products/index.php` - list & manage products
- [ ] `admin/products/add.php` - add product form
- [ ] `admin/products/edit.php` - edit product form
- [ ] `admin/products/delete.php` - delete product handler
- [ ] `admin/categories/index.php` - list & manage categories
- [ ] `admin/categories/add.php` - add category
- [ ] `admin/categories/edit.php` - edit category
- [ ] `admin/categories/delete.php` - delete category handler
- [ ] `admin/orders/index.php` - view all orders
- [ ] `admin/orders/detail.php` - order detail
- [ ] `admin/users/index.php` - list users

## Phase 6: Assets & Styling
- [ ] `assets/css/style.css` - custom CSS with animations
- [ ] `assets/js/main.js` - cart JS, filter JS
- [ ] Upload placeholder product images

## Phase 7: Verification
- [ ] Test SQL import in phpMyAdmin
- [ ] Test user registration and login
- [ ] Test product browsing, filtering, search
- [ ] Test cart add/remove/update
- [ ] Test checkout flow
- [ ] Test admin CRUD operations

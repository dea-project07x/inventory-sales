# Inventory & Sales Management System

Aplikasi web terintegrasi (Laravel + MySQL) untuk mengelola **master data**, **pembelian (stock-in)**, **penjualan**, dan **konsistensi stok**, dengan dua role: **Admin/Owner** dan **Staff**.
Dibuat untuk UTS Web Programming 2 — Integrated Laravel Project.

## 1. Project Overview

| Modul | Keterangan |
|---|---|
| Authentication | Login/logout, password di-hash (bcrypt), semua halaman dilindungi middleware `auth` |
| Master data | Kategori, Produk (SKU unik, harga, stok, minimum stok), Pemasok |
| Purchase | Header + banyak item; stok **bertambah** saat transaksi berhasil |
| Sales | Header + banyak item; stok **berkurang** hanya jika berhasil; stok kurang → ditolak |
| Dashboard | Total produk, penjualan hari ini/bulan ini, jumlah transaksi, produk stok rendah (query agregat dari DB) |
| Search & report | Cari SKU/nama, filter kategori, filter tanggal & status, pagination, detail transaksi |
| Pembatalan | Admin dapat membatalkan transaksi; stok dipulihkan secara konsisten |
| Riwayat & laporan | Admin melihat riwayat pergerakan stok (masuk/keluar), ringkasan penjualan & pembelian per periode, dan 5 produk terlaris |

## 2. Requirements

- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer 2
- MySQL 8 / MariaDB 10.4+ (mis. lewat XAMPP)
- Git
- Node.js **tidak wajib**: tampilan memakai Bootstrap, Bootstrap Icons, dan Google Fonts dari CDN (butuh koneksi internet)
- Dikembangkan dan diuji dengan Laravel 12 dan PHP 8.2

## 3. Installation

```bash
# 1. Clone repository
git clone https://github.com/dea-project07x/inventory-sales.git
cd inventory-sales

# 2. Pasang dependency PHP
composer install

# 3. Salin file environment (Windows PowerShell: Copy-Item .env.example .env)
cp .env.example .env

# 4. Buat application key
php artisan key:generate
```

Langkah selanjutnya (database dan menjalankan aplikasi) ada di bagian 4 dan 5.

## 4. Environment Setup

1. Buat database kosong, contoh: `CREATE DATABASE inventory_sales CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
2. Edit `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_sales
DB_USERNAME=root
DB_PASSWORD=<password MySQL lokal Anda>
```

`.env` **tidak** di-commit (sudah ada di `.gitignore` bawaan Laravel). Yang di-commit hanya `.env.example`.

## 5. Migration & Seeding

```bash
php artisan migrate --seed
php artisan serve
```

Buka http://127.0.0.1:8000. Seluruh tabel dibuat lewat migration (tidak bergantung pada database manual). Seeder menyiapkan 5 kategori, 3 pemasok, 12 produk (beberapa sengaja stok rendah), dan 2 akun demo. Seeder aman dijalankan ulang.

Menjalankan test (memakai SQLite in-memory, tidak menyentuh database MySQL):

```bash
php artisan test
```

## 6. Demo Account

Akun berikut hanya data dummy untuk demo (bukan kredensial pribadi):

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |

## 7. Features

- **Role & hak akses (server-side)** — Gate + middleware `can:`; menyembunyikan tombol di UI hanyalah tambahan.
  - Admin: kelola pengguna, CRUD kategori/produk/pemasok, lihat semua transaksi, dashboard, batalkan transaksi.
  - Staff: lihat produk, buat pembelian & penjualan, lihat transaksi miliknya sendiri. Akses URL admin → **403**.
- **Business rules**
  - SKU unik (validasi Laravel + unique index database).
  - Kuantitas > 0, harga >= 0 (validasi server).
  - Penjualan tidak boleh melebihi stok; stok tidak pernah negatif (cek aplikasi + kolom `unsigned` di DB).
  - Total & harga dihitung server dari database — input harga/total dari browser diabaikan.
  - Header, item, dan perubahan stok disimpan dalam satu `DB::transaction()`; satu item gagal → rollback semua.
  - `lockForUpdate()` pada baris produk mencegah overselling pada transaksi bersamaan; produk diproses berurutan `product_id` untuk menghindari deadlock.
  - Transaksi selesai tidak diedit; hanya bisa **dibatalkan** (stok dipulihkan). Pembatalan pembelian ditolak jika stok hasil pembelian itu sudah terpakai.
  - Produk/kategori/pemasok/pengguna yang sudah dipakai tidak bisa dihapus (larangan hapus + foreign key `RESTRICT`).
- **UI** — layout Blade reusable (Bootstrap 5 via CDN) dengan tampilan ceria dan efek sparkle saat menu diklik, navigasi, pesan sukses/error, input lama dipertahankan saat validasi gagal, form multi-item dinamis, responsive.
- **Keamanan dasar** — CSRF (`@csrf`), escaping output (`{{ }}`), password hashing, rate-limit login, mass-assignment protection (`$fillable`), query berparameter.

## 8. ERD

```mermaid
erDiagram
    users ||--o{ sales : "mencatat"
    users ||--o{ purchases : "mencatat"
    categories ||--o{ products : "memiliki"
    suppliers ||--o{ purchases : "memasok"
    purchases ||--|{ purchase_items : "berisi"
    sales ||--|{ sale_items : "berisi"
    products ||--o{ purchase_items : "dibeli"
    products ||--o{ sale_items : "dijual"

    users { bigint id PK
            string name
            string email UK
            string password
            string role }
    categories { bigint id PK
                 string name UK }
    suppliers { bigint id PK
                string name
                string phone
                string email }
    products { bigint id PK
               bigint category_id FK
               string sku UK
               string name
               decimal purchase_price
               decimal selling_price
               int stock
               int minimum_stock }
    purchases { bigint id PK
                bigint supplier_id FK
                bigint user_id FK
                string purchase_number UK
                decimal total
                string status
                timestamp purchased_at }
    purchase_items { bigint id PK
                     bigint purchase_id FK
                     bigint product_id FK
                     int quantity
                     decimal unit_cost
                     decimal subtotal }
    sales { bigint id PK
            bigint user_id FK
            string sale_number UK
            decimal total
            string status
            timestamp sold_at }
    sale_items { bigint id PK
                 bigint sale_id FK
                 bigint product_id FK
                 int quantity
                 decimal unit_price
                 decimal subtotal }
```

Kolom tambahan di luar skema minimal: `cancelled_at` dan `cancelled_by` pada `purchases`/`sales` untuk jejak pembatalan.

### Relasi Eloquent

| Model | Relasi |
|---|---|
| Category | `hasMany(Product)` |
| Product | `belongsTo(Category)`, `hasMany(SaleItem)`, `hasMany(PurchaseItem)` |
| Supplier | `hasMany(Purchase)` |
| Purchase | `belongsTo(Supplier)`, `belongsTo(User)`, `hasMany(PurchaseItem)` |
| PurchaseItem | `belongsTo(Purchase)`, `belongsTo(Product)` |
| Sale | `belongsTo(User)`, `hasMany(SaleItem)` |
| SaleItem | `belongsTo(Sale)`, `belongsTo(Product)` |
| User | `hasMany(Sale)`, `hasMany(Purchase)` |

`hasMany` dipasang di sisi "satu" (mis. satu Sale punya banyak SaleItem), `belongsTo` di sisi "banyak". Foreign key (`sale_id`) berada di tabel detail karena satu sel tidak dapat menyimpan banyak nilai — tiap baris item menunjuk ke induknya.

## 9. Struktur Kode Inti

```
Route (routes/web.php) → Middleware (auth, can:) → Controller
   → Form Request (validasi) → Service (business logic + DB::transaction)
   → Eloquent Model → MySQL → Blade
```

| Lokasi | Isi |
|---|---|
| `app/Services/SaleService.php` | Logika penjualan & pembatalan |
| `app/Services/PurchaseService.php` | Logika pembelian & pembatalan |
| `app/Http/Controllers/ReportController.php` | Riwayat pergerakan stok & laporan ringkas (Admin) |
| `app/Http/Requests/*` | Validasi server-side |
| `app/Providers/AppServiceProvider.php` | Definisi Gate (hak akses) |
| `database/migrations/*` | Skema, FK, index |
| `tests/Feature/*` | Skenario pengujian |

## 10. Testing

`php artisan test` menjalankan skenario minimum: guest ditolak, password salah ditolak, SKU duplikat & harga negatif ditolak, pembelian menambah stok, penjualan mengurangi stok, stok tidak cukup ditolak tanpa perubahan data, rollback saat satu item gagal, staff tidak bisa mengakses fungsi Admin, serta search/filter/pagination dan dashboard. Hasil terakhir: **39 test lulus (126 assertions)**.

## Screenshots

Bukti tampilan dan pengujian ada di folder [`docs/screenshots`](docs/screenshots).

| | |
|---|---|
| **Login** | **Dashboard** |
| ![Login](docs/screenshots/01-login.png) | ![Dashboard](docs/screenshots/02-dashboard.png) |
| **Daftar produk** | **Daftar pembelian** |
| ![Produk](docs/screenshots/03-produk.png) | ![Pembelian](docs/screenshots/04-pembelian.png) |
| **Detail pembelian** | **Daftar penjualan** |
| ![Detail pembelian](docs/screenshots/05-detail-pembelian.png) | ![Penjualan](docs/screenshots/06-penjualan.png) |
| **Detail penjualan** | **Penjualan ditolak: stok tidak cukup** |
| ![Detail penjualan](docs/screenshots/07-detail-penjualan.png) | ![Stok tidak cukup](docs/screenshots/08-stok-tidak-cukup.png) |
| **Staff mengakses halaman Admin: 403** | **Hasil `php artisan test` (39 test lulus)** |
| ![403](docs/screenshots/09-akses-staff-403.png) | ![Hasil test](docs/screenshots/10-hasil-test.png) |
| **Riwayat & laporan (Admin)** | |
| ![Riwayat dan laporan](docs/screenshots/11-riwayat-laporan.png) | |

## 11. Known Limitations

- Pembelian langsung berstatus *completed* (tidak ada alur draft → konfirmasi).
- Tidak ada payment gateway, multi-warehouse, multi-currency, laporan PDF/Excel (di luar ruang lingkup).
- Staff hanya melihat transaksi miliknya sendiri.
- UI memakai Bootstrap, Bootstrap Icons, dan Google Fonts dari CDN sehingga membutuhkan koneksi internet saat dijalankan.
- `lockForUpdate()` hanya efektif pada engine yang mendukung row locking (MySQL/InnoDB); pada SQLite (test) tidak berpengaruh.

## Atribusi

Bootstrap 5 (MIT) dimuat dari CDN jsDelivr. Selebihnya memakai dokumentasi resmi Laravel.

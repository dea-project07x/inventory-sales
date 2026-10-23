# Pemetaan Persyaratan PPT → Implementasi

Dokumen ini membantu verifikasi dan persiapan demo / code ownership interview.

## Definition of Done (slide 25)

| # | Kriteria | Status | Lokasi bukti |
|---|---|---|---|
| 1 | Dapat dijalankan dari README di environment bersih | Siap (verifikasi di mesin Anda, lihat checklist bawah) | `README.md`, `.env.example` |
| 2 | Login & role-based access | ✔ | `AuthController`, `AppServiceProvider` (Gate), `routes/web.php` |
| 3 | Admin kelola kategori, produk, pemasok | ✔ | `CategoryController`, `ProductController`, `SupplierController` |
| 4 | Purchase menambah stok | ✔ | `PurchaseService::create`, `TransactionTest` |
| 5 | Sales mengurangi stok | ✔ | `SaleService::create`, `TransactionTest` |
| 6 | Stok tak cukup ditolak tanpa mengubah data | ✔ | `SaleService` (throw di dalam `DB::transaction`), `test_sale_with_insufficient_stock...` |
| 7 | Relasi, migration, seeder, search, pagination | ✔ | `app/Models/*`, `database/*`, `ProductController::index` |
| 8 | Dashboard dari data aktual DB | ✔ | `DashboardController` (query agregat) |
| 9 | Tanpa kredensial rahasia di repo | ✔ | `.env` di `.gitignore`; hanya `.env.example`; akun demo dummy |
| 10 | Mahasiswa dapat menjelaskan & memodifikasi kode inti | **Tugas Anda** | Lihat bagian "Persiapan interview" |

## Modul wajib (slide 6) & halaman minimal (slide 13)

| Halaman | View |
|---|---|
| Login | `auth/login` |
| Dashboard | `dashboard` |
| Product index/create/edit/detail | `products/index, create, edit, show` |
| Category index/create/edit | `categories/*` |
| Supplier index/create/edit | `suppliers/*` |
| User/role management (Admin) | `users/*` |
| Low-stock list | `products/low-stock` |
| Laporan & riwayat pergerakan stok (Admin) | `reports/index`, `ReportController`, `tests/Feature/ReportTest.php` |
| Purchase list/create/detail | `purchases/*` |
| Sales list/create/detail | `sales/*` |
| Multi-item per transaksi | `partials/transaction-items` + `item-row` |
| Search & filter tanggal/status, pagination | `products/index`, `sales/index`, `purchases/index` |
| Validation/error states, responsive | `layouts/app` + `@error` pada setiap form |

## Business rules (slide 8)

| Aturan | Implementasi |
|---|---|
| SKU unik | `ProductRequest` (`Rule::unique`) + `unique` index di migration |
| Qty > 0, harga valid | `StoreSaleRequest`, `StorePurchaseRequest`, `ProductRequest` |
| Jual ≤ stok | `SaleService::create` |
| Stok berubah setelah sukses | `increment`/`decrement` di dalam `DB::transaction` |
| Total dihitung server | Request sale tidak menerima harga/total; harga dari `products.selling_price` |
| Database transaction | `DB::transaction` di kedua service |
| Pembatalan memulihkan stok | `SaleService::cancel`, `PurchaseService::cancel` |
| Tidak hard delete produk terpakai | `ProductController::destroy` + FK `restrictOnDelete` |

## Rubrik (slide 22) → bukti

Fungsionalitas 25% · Business logic 20% · Database & Eloquent 15% · Arsitektur & validasi 15% · Auth & authorization 10% · UI 5% · Testing & security 5% · GitHub/README/demo 5% — seluruhnya tercakup oleh bagian di atas.

## Deliverables (slide 20) — yang masih harus Anda lengkapi sendiri

- [ ] Push ke repository GitHub (`git init`, commit, push). Pastikan `.env` tidak ikut.
- [ ] Screenshot halaman utama & transaksi → simpan di `docs/screenshots/`
- [ ] Bukti hasil `php artisan test` → screenshot/log di `docs/`
- [ ] Uji setup bersih: clone repo ke folder baru, ikuti README dari awal
- [ ] Siapkan demo & latihan code interview

## Persiapan interview (slide 24) — jawaban singkat

1. **Mengapa Sale hasMany SaleItem?** Satu penjualan berisi banyak item; FK `sale_id` ada di tabel `sale_items`.
2. **Di mana stok dijaga agar tidak negatif?** `SaleService::create` (cek `stock < qty` sebelum `decrement`), plus kolom `unsigned` di DB.
3. **Mengapa total dihitung di server?** Input browser bisa dimanipulasi; harga diambil dari `products.selling_price`.
4. **Risiko jika stok dikurangi di luar `DB::transaction`?** Header tersimpan tapi stok tidak berubah (atau sebaliknya) bila terjadi error di tengah jalan → data tidak konsisten.
5. **Mencegah Staff akses fungsi Admin lewat URL?** Middleware `can:manage-master` / `can:manage-users` di `routes/web.php` → 403, bukan sekadar menyembunyikan tombol.
6. **Dua penjualan bersamaan pada produk yang sama?** `lockForUpdate()` membuat transaksi kedua menunggu; setelah itu stok dibaca ulang sehingga overselling tercegah.
7. **Validasi SKU unik & peran constraint DB?** `Rule::unique` memberi pesan ramah; unique index menjadi pengaman terakhir saat terjadi race condition.
8. **Ubah aturan minimum stock?** Ubah kolom `minimum_stock` per produk (form edit) atau scope `Product::scopeLowStock`; dampaknya terlihat di dashboard & halaman Stok Rendah.

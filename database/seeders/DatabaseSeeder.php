<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** Data dummy (bukan data pribadi / kredensial asli). Aman dijalankan berulang. */
    public function run(): void
    {
        // Akun demo
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin Demo',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
        User::updateOrCreate(['email' => 'staff@example.com'], [
            'name' => 'Staff Demo',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF,
        ]);

        // Kategori
        $categories = [];
        foreach (['Makanan', 'Minuman', 'Alat Tulis', 'Elektronik', 'Kebersihan'] as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name]);
        }

        // Pemasok
        $suppliers = [
            ['name' => 'CV Sumber Makmur', 'phone' => '081200000001', 'email' => 'sumbermakmur@example.com'],
            ['name' => 'PT Nusantara Distribusi', 'phone' => '081200000002', 'email' => 'nusantara@example.com'],
            ['name' => 'UD Jaya Abadi', 'phone' => '081200000003', 'email' => 'jayaabadi@example.com'],
        ];
        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['email' => $s['email']], $s);
        }

        // Produk: beberapa sengaja berada di bawah minimum stock untuk demo "stok rendah"
        $products = [
            ['MKN-001', 'Mie Instan Goreng', 'Makanan', 2500, 3500, 120, 30],
            ['MKN-002', 'Biskuit Cokelat 200g', 'Makanan', 7000, 9500, 60, 20],
            ['MKN-003', 'Keripik Singkong 150g', 'Makanan', 6000, 8500, 4, 10],
            ['MNM-001', 'Air Mineral 600ml', 'Minuman', 2000, 3000, 200, 50],
            ['MNM-002', 'Teh Botol 450ml', 'Minuman', 3500, 5000, 80, 24],
            ['MNM-003', 'Kopi Sachet (10 pcs)', 'Minuman', 9000, 12500, 3, 15],
            ['ATK-001', 'Pulpen Hitam', 'Alat Tulis', 1500, 2500, 150, 40],
            ['ATK-002', 'Buku Tulis 38 Lembar', 'Alat Tulis', 3000, 4500, 90, 30],
            ['ELK-001', 'Kabel USB-C 1m', 'Elektronik', 18000, 28000, 25, 10],
            ['ELK-002', 'Baterai AA (isi 4)', 'Elektronik', 12000, 17000, 8, 10],
            ['KBS-001', 'Sabun Cuci Piring 400ml', 'Kebersihan', 9000, 13000, 40, 12],
            ['KBS-002', 'Tisu Gulung (4 roll)', 'Kebersihan', 11000, 15500, 35, 10],
        ];
        foreach ($products as [$sku, $name, $cat, $buy, $sell, $stock, $min]) {
            Product::updateOrCreate(['sku' => $sku], [
                'name' => $name,
                'category_id' => $categories[$cat]->id,
                'purchase_price' => $buy,
                'selling_price' => $sell,
                'stock' => $stock,
                'minimum_stock' => $min,
            ]);
        }
    }
}

<?php

return [
    'array' => ':Attribute harus berupa array.',
    'between' => [
        'numeric' => ':Attribute harus di antara :min dan :max.',
        'string' => ':Attribute harus di antara :min dan :max karakter.',
        'array' => ':Attribute harus memiliki :min sampai :max item.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'after_or_equal' => ':Attribute harus berupa tanggal setelah atau sama dengan :date.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
        'array' => ':Attribute tidak boleh lebih dari :max item.',
    ],
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
        'array' => ':Attribute minimal memiliki :min item.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',

    'custom' => [
        'items' => [
            'required' => 'Tambahkan minimal satu item produk.',
            'min' => 'Tambahkan minimal satu item produk.',
        ],
    ],

    'attributes' => [
        'sku' => 'SKU',
        'name' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'role' => 'role',
        'phone' => 'telepon',
        'category_id' => 'kategori',
        'supplier_id' => 'pemasok',
        'purchase_price' => 'harga beli',
        'selling_price' => 'harga jual',
        'stock' => 'stok',
        'minimum_stock' => 'minimum stok',
        'items' => 'item',
        'items.*.product_id' => 'produk',
        'items.*.quantity' => 'kuantitas',
        'items.*.unit_cost' => 'harga beli',
        'from' => 'tanggal awal',
        'to' => 'tanggal akhir',
        'status' => 'status',
        'q' => 'kata kunci',
    ],
];

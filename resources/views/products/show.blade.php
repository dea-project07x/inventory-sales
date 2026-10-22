@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $product->name }}</h1>
    <div class="d-flex gap-2">
        @can('manage-master')
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">Ubah</a>
        @endcan
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>
<div class="card"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">SKU</dt><dd class="col-sm-9">{{ $product->sku }}</dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $product->category->name }}</dd>
        <dt class="col-sm-3">Harga Beli</dt><dd class="col-sm-9">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
        <dt class="col-sm-3">Harga Jual</dt><dd class="col-sm-9">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</dd>
        <dt class="col-sm-3">Stok</dt>
        <dd class="col-sm-9">{{ $product->stock }}
            @if ($product->isLowStock())<span class="badge text-bg-danger">di bawah/sama dengan minimum</span>@endif
        </dd>
        <dt class="col-sm-3">Minimum Stok</dt><dd class="col-sm-9">{{ $product->minimum_stock }}</dd>
    </dl>
</div></div>
@endsection

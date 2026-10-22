@extends('layouts.app')
@section('title', 'Penjualan Baru')
@section('content')
<h1 class="h3 mb-3">Penjualan Baru</h1>
<div class="card"><div class="card-body">
    <p class="text-muted small">Harga dan total dihitung otomatis oleh server dari data produk. Penjualan ditolak bila stok tidak cukup.</p>
    <form method="POST" action="{{ route('sales.store') }}">
        @csrf
        @include('partials.transaction-items', ['products' => $products, 'withCost' => false])
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Simpan Penjualan</button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection

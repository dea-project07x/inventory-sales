@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1 class="h3 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Total Produk</div>
            <div class="fs-3 fw-semibold">{{ number_format($stats['total_products'], 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Penjualan Hari Ini</div>
            <div class="fs-3 fw-semibold">Rp {{ number_format($stats['sales_today'], 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Penjualan Bulan Ini</div>
            <div class="fs-3 fw-semibold">Rp {{ number_format($stats['sales_month'], 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Transaksi Penjualan</div>
            <div class="fs-3 fw-semibold">{{ number_format($stats['sales_count'], 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Transaksi Pembelian</div>
            <div class="fs-3 fw-semibold">{{ number_format($stats['purchases_count'], 0, ',', '.') }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card h-100 {{ $stats['low_stock_count'] > 0 ? 'border-danger' : '' }}"><div class="card-body">
            <div class="text-muted small">Produk Stok Rendah</div>
            <div class="fs-3 fw-semibold {{ $stats['low_stock_count'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock_count'] }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span>Stok Rendah</span>
                <a href="{{ route('products.low-stock') }}" class="small">Lihat semua</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($lowStock as $p)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $p->name }} <small class="text-muted">({{ $p->sku }})</small></span>
                        <span class="badge text-bg-danger">{{ $p->stock }} / min {{ $p->minimum_stock }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Semua stok aman.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span>Penjualan Terbaru</span>
                <a href="{{ route('sales.index') }}" class="small">Lihat semua</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($recentSales as $s)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('sales.show', $s) }}">{{ $s->sale_number }}</a>
                        <span>Rp {{ number_format($s->total, 0, ',', '.') }}
                            @if (! $s->isCompleted())<span class="badge text-bg-secondary">dibatalkan</span>@endif
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada penjualan.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

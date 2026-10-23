@extends('layouts.app')
@section('title', 'Riwayat & Laporan')
@section('content')
<h1 class="h3 mb-1">Riwayat &amp; Laporan</h1>
<p class="text-muted mb-4">Pergerakan stok dari seluruh transaksi, lengkap dengan ringkasan per periode.</p>

<form method="GET" action="{{ route('reports.index') }}" class="card card-body mb-4">
    <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
            <label class="form-label" for="from">Dari tanggal</label>
            <input id="from" type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-sm-6 col-lg-3">
            <label class="form-label" for="to">Sampai tanggal</label>
            <input id="to" type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="col-lg-6">
            <label class="form-label" for="product_id">Produk</label>
            <select id="product_id" name="product_id" class="form-select">
                <option value="">Semua produk</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected((string) request('product_id') === (string) $p->id)>{{ $p->sku }} — {{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6 col-lg-3">
            <label class="form-label" for="direction">Jenis pergerakan</label>
            <select id="direction" name="direction" class="form-select">
                <option value="">Masuk &amp; keluar</option>
                <option value="in" @selected(request('direction') === 'in')>Stok masuk</option>
                <option value="out" @selected(request('direction') === 'out')>Stok keluar</option>
            </select>
        </div>
        <div class="col-sm-6 col-lg-3">
            <label class="form-label" for="status">Status transaksi</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
            </select>
        </div>
        <div class="col-lg-6 d-flex align-items-end gap-2">
            <button class="btn btn-primary">Terapkan</button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Total penjualan</div>
            <div class="fs-3 fw-semibold">Rp {{ number_format($salesSummary->t, 0, ',', '.') }}</div>
            <div class="small text-muted">{{ $salesSummary->c }} transaksi selesai</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Total pembelian</div>
            <div class="fs-3 fw-semibold">Rp {{ number_format($purchaseSummary->t, 0, ',', '.') }}</div>
            <div class="small text-muted">{{ $purchaseSummary->c }} transaksi selesai</div>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Produk terlaris</div>
            <ul class="list-group list-group-flush">
                @forelse ($topProducts as $tp)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $tp->name }} <small class="text-muted">({{ $tp->sku }})</small></span>
                        <span>{{ (int) $tp->qty }} terjual</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada penjualan pada periode ini.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat pergerakan stok</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Nomor</th><th>Produk</th><th>Jenis</th>
                    <th class="text-end">Jumlah</th><th class="text-end">Nilai</th><th>Oleh</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($movements as $m)
                @php $cancelled = $m->status === 'cancelled'; @endphp
                <tr class="{{ $cancelled ? 'text-muted' : '' }}">
                    <td>{{ \Illuminate\Support\Carbon::parse($m->occurred_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ $m->direction === 'in' ? route('purchases.show', $m->transaction_id) : route('sales.show', $m->transaction_id) }}">{{ $m->trx_number }}</a>
                    </td>
                    <td>{{ $m->sku }} — {{ $m->product_name }}</td>
                    <td>
                        <span class="badge {{ $m->direction === 'in' ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ $m->direction === 'in' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-end fw-semibold {{ $cancelled ? 'text-decoration-line-through' : '' }}">
                        {{ $m->direction === 'in' ? '+' : '−' }}{{ (int) $m->quantity }}
                    </td>
                    <td class="text-end">Rp {{ number_format($m->subtotal, 0, ',', '.') }}</td>
                    <td>{{ $m->actor }}</td>
                    <td>
                        <span class="badge {{ $cancelled ? 'text-bg-secondary' : 'text-bg-success' }}">
                            {{ $cancelled ? 'Dibatalkan' : 'Selesai' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada riwayat untuk filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $movements->links() }}</div>
@endsection

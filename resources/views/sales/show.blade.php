@extends('layouts.app')
@section('title', 'Detail Penjualan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $sale->sale_number }}</h1>
    <div class="d-flex gap-2">
        @can('cancel-transaction')
            @if ($sale->isCompleted())
                <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                      onsubmit="return confirm('Batalkan penjualan ini? Stok akan dipulihkan.')">
                    @csrf
                    <button class="btn btn-outline-danger">Batalkan Transaksi</button>
                </form>
            @endif
        @endcan
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="card mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9">{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
        <dt class="col-sm-3">Dicatat oleh</dt><dd class="col-sm-9">{{ $sale->user->name }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">
            <span class="badge {{ $sale->isCompleted() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $sale->isCompleted() ? 'Selesai' : 'Dibatalkan' }}</span>
            @if ($sale->cancelled_at) <small class="text-muted">pada {{ $sale->cancelled_at->format('d/m/Y H:i') }}</small>@endif
        </dd>
    </dl>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->sku }} — {{ $item->product->name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Total</th><th class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</th></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

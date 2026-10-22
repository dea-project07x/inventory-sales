@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $purchase->purchase_number }}</h1>
    <div class="d-flex gap-2">
        @can('cancel-transaction')
            @if ($purchase->isCompleted())
                <form method="POST" action="{{ route('purchases.cancel', $purchase) }}"
                      onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">
                    @csrf
                    <button class="btn btn-outline-danger">Batalkan Transaksi</button>
                </form>
            @endif
        @endcan
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="card mb-3"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9">{{ $purchase->purchased_at->format('d/m/Y H:i') }}</dd>
        <dt class="col-sm-3">Pemasok</dt><dd class="col-sm-9">{{ $purchase->supplier->name }}</dd>
        <dt class="col-sm-3">Dicatat oleh</dt><dd class="col-sm-9">{{ $purchase->user->name }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">
            <span class="badge {{ $purchase->isCompleted() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $purchase->isCompleted() ? 'Selesai' : 'Dibatalkan' }}</span>
            @if ($purchase->cancelled_at) <small class="text-muted">pada {{ $purchase->cancelled_at->format('d/m/Y H:i') }}</small>@endif
        </dd>
    </dl>
</div></div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Harga Beli</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach ($purchase->items as $item)
                <tr>
                    <td>{{ $item->product->sku }} — {{ $item->product->name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Total</th><th class="text-end">Rp {{ number_format($purchase->total, 0, ',', '.') }}</th></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

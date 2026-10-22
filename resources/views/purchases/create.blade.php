@extends('layouts.app')
@section('title', 'Pembelian Baru')
@section('content')
<h1 class="h3 mb-3">Pembelian / Stock-in Baru</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="mb-3 col-md-6">
            <label class="form-label" for="supplier_id">Pemasok</label>
            <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                <option value="">-- pilih pemasok --</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected((string) old('supplier_id') === (string) $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @include('partials.transaction-items', ['products' => $products, 'withCost' => true])

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Simpan Pembelian</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection

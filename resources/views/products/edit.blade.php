@extends('layouts.app')
@section('title', 'Ubah Produk')
@section('content')
<h1 class="h3 mb-3">Ubah Produk</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')
        @include('products._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Perbarui</button>
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection

@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('content')
<h1 class="h3 mb-3">Tambah Produk</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        @include('products._form')
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection

@extends('layouts.app')
@section('title', 'Tambah Pemasok')
@section('content')
<h1 class="h3 mb-3">Tambah Pemasok</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        @include('suppliers._form')
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
@endsection

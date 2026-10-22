@extends('layouts.app')
@section('title', 'Ubah Pemasok')
@section('content')
<h1 class="h3 mb-3">Ubah Pemasok</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf @method('PUT')
        @include('suppliers._form')
        <button class="btn btn-primary">Perbarui</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
@endsection

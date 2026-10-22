@extends('layouts.app')
@section('title', 'Ubah Kategori')
@section('content')
<h1 class="h3 mb-3">Ubah Kategori</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf @method('PUT')
        @include('categories._form')
        <button class="btn btn-primary">Perbarui</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
@endsection

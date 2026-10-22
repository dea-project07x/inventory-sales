@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('content')
<h1 class="h3 mb-3">Tambah Pengguna</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form')
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
@endsection

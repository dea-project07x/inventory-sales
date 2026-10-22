@extends('layouts.app')
@section('title', 'Ubah Pengguna')
@section('content')
<h1 class="h3 mb-3">Ubah Pengguna</h1>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        @include('users._form')
        <button class="btn btn-primary">Perbarui</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
@endsection

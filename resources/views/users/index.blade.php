@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Pengguna &amp; Role</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>Role</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge {{ $u->isAdmin() ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $u->role }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus pengguna ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection

@extends('layouts.app')
@section('title', 'Pemasok')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Pemasok</h1>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Tambah Pemasok</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Telepon</th><th>Email</th><th class="text-end">Pembelian</th><th></th></tr></thead>
            <tbody>
            @forelse ($suppliers as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->phone ?? '-' }}</td>
                    <td>{{ $s->email ?? '-' }}</td>
                    <td class="text-end">{{ $s->purchases_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline"
                              onsubmit="return confirm('Hapus pemasok ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pemasok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $suppliers->links() }}</div>
@endsection

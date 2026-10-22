@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Kategori</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th class="text-end">Jumlah Produk</th><th></th></tr></thead>
            <tbody>
            @forelse ($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td class="text-end">{{ $c->products_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('categories.edit', $c) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="POST" action="{{ route('categories.destroy', $c) }}" class="d-inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection

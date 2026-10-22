@php $isCreate = ! $product->exists; @endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="sku">SKU</label>
        <input id="sku" name="sku" maxlength="50" value="{{ old('sku', $product->sku) }}"
               class="form-control @error('sku') is-invalid @enderror" required>
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Produk</label>
        <input id="name" name="name" maxlength="150" value="{{ old('name', $product->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="category_id">Kategori</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- pilih kategori --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((string) old('category_id', $product->category_id) === (string) $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="purchase_price">Harga Beli</label>
        <input id="purchase_price" type="number" step="0.01" min="0" name="purchase_price"
               value="{{ old('purchase_price', $product->purchase_price) }}"
               class="form-control @error('purchase_price') is-invalid @enderror" required>
        @error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="selling_price">Harga Jual</label>
        <input id="selling_price" type="number" step="0.01" min="0" name="selling_price"
               value="{{ old('selling_price', $product->selling_price) }}"
               class="form-control @error('selling_price') is-invalid @enderror" required>
        @error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="stock">Stok {{ $isCreate ? 'Awal' : '' }}</label>
        @if ($isCreate)
            <input id="stock" type="number" step="1" min="0" name="stock" value="{{ old('stock', $product->stock) }}"
                   class="form-control @error('stock') is-invalid @enderror" required>
            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @else
            <input id="stock" class="form-control" value="{{ $product->stock }}" disabled>
            <div class="form-text">Stok berubah lewat transaksi pembelian/penjualan.</div>
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label" for="minimum_stock">Minimum Stok</label>
        <input id="minimum_stock" type="number" step="1" min="0" name="minimum_stock"
               value="{{ old('minimum_stock', $product->minimum_stock) }}"
               class="form-control @error('minimum_stock') is-invalid @enderror" required>
        @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

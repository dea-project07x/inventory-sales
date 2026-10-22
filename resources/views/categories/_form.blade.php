<div class="mb-3">
    <label class="form-label" for="name">Nama Kategori</label>
    <input id="name" name="name" maxlength="100" value="{{ old('name', $category->name) }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

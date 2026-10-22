<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Nama Pemasok</label>
        <input id="name" name="name" maxlength="150" value="{{ old('name', $supplier->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="phone">Telepon</label>
        <input id="phone" name="phone" maxlength="30" value="{{ old('phone', $supplier->phone) }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" type="email" name="email" maxlength="150" value="{{ old('email', $supplier->email) }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

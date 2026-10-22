@php $isCreate = ! $user->exists; @endphp
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Nama</label>
        <input id="name" name="name" maxlength="100" value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="email">Email</label>
        <input id="email" type="email" name="email" maxlength="150" value="{{ old('email', $user->email) }}"
               class="form-control @error('email') is-invalid @enderror" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="role">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach (\App\Models\User::ROLES as $r)
                <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="password">Password {{ $isCreate ? '' : '(kosongkan jika tidak diubah)' }}</label>
        <input id="password" type="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $isCreate ? 'required' : '' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
    </div>
</div>

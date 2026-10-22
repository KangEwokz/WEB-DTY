@extends('auth::layouts.app')

@section('title', 'Ubah Akun Pengguna')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Karyawan: <strong>{{ $user->id_karyawan }} - {{ $user->nama_karyawan }}</strong></p>
      <form method="POST" action="{{ route('admin.user.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" value="{{ old('username', $user->username) }}" maxlength="20"
            class="form-control @error('username') is-invalid @enderror">
          @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Password Baru</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
          <small class="text-muted">Kosongkan kalau password tidak diubah.</small>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Ulangi Password Baru</label>
          <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role" class="form-control @error('role') is-invalid @enderror">
            @foreach ($role as $r)
              <option value="{{ $r }}" {{ old('role', $user->role) == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
          </select>
          @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control @error('status') is-invalid @enderror">
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ old('status', $user->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.user.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

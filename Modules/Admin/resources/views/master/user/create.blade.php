@extends('auth::layouts.app')

@section('title', 'Tambah Akun Pengguna')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.user.store') }}">
        @csrf
        <div class="form-group">
          <label>Karyawan</label>
          <select name="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror">
            <option value="">- Pilih karyawan yang belum punya akun -</option>
            @foreach ($karyawan as $k)
              <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>{{ $k->id_karyawan }} - {{ $k->nama_karyawan }}</option>
            @endforeach
          </select>
          @error('id_karyawan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" value="{{ old('username') }}" maxlength="20"
            class="form-control @error('username') is-invalid @enderror">
          @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Ulangi Password</label>
          <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role" class="form-control @error('role') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach ($role as $r)
              <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
          </select>
          @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.user.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

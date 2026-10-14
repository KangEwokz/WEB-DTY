@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
  <div class="row">
    <div class="col-md-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah User</h4>
          <p class="card-description">Akun baru otomatis mendapat role <strong>User</strong> dengan password default <strong>password123@</strong>.</p>

          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
              <label for="id_karyawan">Karyawan</label>
              <select name="id_karyawan" id="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror" required>
                <option value="">-- Pilih karyawan --</option>
                @foreach ($karyawan as $k)
                  <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                    {{ $k->id_karyawan }} - {{ $k->nama_karyawan }}
                  </option>
                @endforeach
              </select>
              @error('id_karyawan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="nama@dty.local" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

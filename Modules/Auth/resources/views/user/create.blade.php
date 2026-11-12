@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Buat Akun') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Buat Akun</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Yang muncul di pilihan hanya karyawan aktif yang belum punya akun.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('auth.user.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
            <select name="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($karyawan as $k) {{-- ulangi karyawan yang belum punya akun --}}
                <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nama_karyawan }} ({{ $k->id_karyawan }})</option>
              @endforeach
            </select>
            @if ($karyawan->isEmpty()) {{-- isEmpty() = true jika daftar kosong --}}
              <small class="text-muted">Semua karyawan aktif sudah punya akun.</small>
            @endif
            @error('id_karyawan') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="id_role" class="form-control @error('id_role') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($role as $r) {{-- ulangi setiap role aktif --}}
                <option value="{{ $r->id_role }}" {{ old('id_role') == $r->id_role ? 'selected' : '' }}>{{ $r->nm_role }}</option>
              @endforeach
            </select>
            @error('id_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" value="{{ old('username') }}" maxlength="20" placeholder="contoh: op_tex" autocomplete="off" class="form-control @error('username') is-invalid @enderror"> {{-- autocomplete off = browser tidak mengisi otomatis --}}
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror"> {{-- type password = tulisan disamarkan; old() sengaja tidak dipakai agar password tidak muncul lagi --}}
            <small class="text-muted">Minimal 6 karakter.</small>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Ulangi Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control"> {{-- nama harus password_confirmation agar aturan 'confirmed' bekerja --}}
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('auth.user.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

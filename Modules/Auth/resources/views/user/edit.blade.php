@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Akun') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Akun</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Pemilik akun: <b>{{ $user->nama_karyawan }} ({{ $user->id_karyawan }})</b>, tidak bisa diganti.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('auth.user.update', $user->id) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" maxlength="20" autocomplete="off" class="form-control @error('username') is-invalid @enderror">
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="id_role" class="form-control @error('id_role') is-invalid @enderror">
              @foreach ($role as $r)
                <option value="{{ $r->id_role }}" {{ old('id_role', $user->id_role) == $r->id_role ? 'selected' : '' }}>{{ $r->nm_role }}</option> {{-- terpilih = role akun saat ini --}}
              @endforeach
            </select>
            @if ($user->id == auth()->id()) {{-- jika ini akun sendiri --}}
              <small class="text-muted">Ini akun Anda, role tidak bisa diganti sendiri.</small>
            @endif
            @error('id_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <p class="card-title mt-2">Reset Password</p> {{-- judul kelompok --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Password Baru</label> {{-- tanpa * = boleh kosong --}}
            <input type="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
            <small class="text-muted">Kosongkan jika password tidak diganti. Minimal 6 karakter.</small>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Ulangi Password Baru</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
          </div>
        </div>

        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('auth.user.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

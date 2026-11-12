@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Role') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Role</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode Role: <b>{{ $role->id_role }}</b> (tidak bisa diubah karena dipakai oleh akun login).</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('auth.role.update', $role->id_role) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-8 mb-3">
            <label class="form-label">Nama Role <span class="text-danger">*</span></label>
            <input type="text" name="nm_role" value="{{ old('nm_role', $role->nm_role) }}" class="form-control @error('nm_role') is-invalid @enderror">
            @error('nm_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('auth.role.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

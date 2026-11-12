@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Departemen') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Departemen</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID departemen dibuat otomatis (01, 02, 03, ...).</p> {{-- keterangan --}}
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.departemen.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-3 mb-3"> {{-- kolom 1/4 lebar --}}
            <label class="form-label">Kode <span class="text-danger">*</span></label> {{-- * = wajib --}}
            <input type="text" name="kd_departemen" value="{{ old('kd_departemen') }}" maxlength="5" placeholder="contoh: DTY" class="form-control @error('kd_departemen') is-invalid @enderror"> {{-- maxlength = maks 5 huruf bisa diketik --}}
            @error('kd_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- pesan error --}}
          </div>
          <div class="col-md-9 mb-3"> {{-- kolom 3/4 lebar --}}
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nm_departemen" value="{{ old('nm_departemen') }}" placeholder="contoh: Draw Textured Yarn" class="form-control @error('nm_departemen') is-invalid @enderror">
            @error('nm_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2"> {{-- baris tombol --}}
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('admin.departemen.index') }}" class="btn btn-light">Batal</a> {{-- kembali tanpa menyimpan --}}
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

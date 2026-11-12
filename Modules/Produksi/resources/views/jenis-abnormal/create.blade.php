@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Jenis Abnormal') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Jenis Abnormal</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode dibuat otomatis (VS01, VS02, ...).</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.jenis-abnormal.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Abnormal <span class="text-danger">*</span></label>
            <input type="text" name="nm_abnormal" value="{{ old('nm_abnormal') }}" maxlength="50" placeholder="contoh: Broken Filament" class="form-control @error('nm_abnormal') is-invalid @enderror">
            @error('nm_abnormal') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('produksi.jenis-abnormal.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

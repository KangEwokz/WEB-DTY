@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Role') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Role</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Setelah disimpan, Anda langsung diarahkan ke halaman untuk mencentang permission role ini.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('auth.role.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Kode Role <span class="text-danger">*</span></label>
            <input type="text" name="id_role" value="{{ old('id_role') }}" maxlength="20" placeholder="contoh: teknisi_elk" class="form-control @error('id_role') is-invalid @enderror">
            <small class="text-muted">Huruf kecil tanpa spasi. Tidak bisa diubah setelah disimpan.</small> {{-- keterangan --}}
            @error('id_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-8 mb-3">
            <label class="form-label">Nama Role <span class="text-danger">*</span></label>
            <input type="text" name="nm_role" value="{{ old('nm_role') }}" placeholder="contoh: Teknisi Elektrik" class="form-control @error('nm_role') is-invalid @enderror">
            @error('nm_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('auth.role.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

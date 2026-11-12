@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Divisi') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Divisi</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID divisi dibuat otomatis (01, 02, 03, ...).</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.divisi.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Kode <span class="text-danger">*</span></label>
            <input type="text" name="kd_divisi" value="{{ old('kd_divisi') }}" maxlength="5" placeholder="contoh: QCT" class="form-control @error('kd_divisi') is-invalid @enderror">
            @error('kd_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-5 mb-3">
            <label class="form-label">Nama Divisi <span class="text-danger">*</span></label>
            <input type="text" name="nm_divisi" value="{{ old('nm_divisi') }}" placeholder="contoh: Quality Control" class="form-control @error('nm_divisi') is-invalid @enderror">
            @error('nm_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Departemen <span class="text-danger">*</span></label>
            <select name="id_departemen" class="form-control @error('id_departemen') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($departemen as $d) {{-- ulangi setiap departemen aktif dari controller --}}
                <option value="{{ $d->id_departemen }}" {{ old('id_departemen') == $d->id_departemen ? 'selected' : '' }}>{{ $d->kd_departemen }} - {{ $d->nm_departemen }}</option>
              @endforeach
            </select>
            @error('id_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('admin.divisi.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

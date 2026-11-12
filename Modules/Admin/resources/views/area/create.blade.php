@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Area') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Area</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode area diketik sendiri (huruf/angka, contoh VIS) dan tidak bisa diubah setelah disimpan.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.area.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Kode Area <span class="text-danger">*</span></label>
            <input type="text" name="id_area" value="{{ old('id_area') }}" maxlength="10" placeholder="contoh: VIS" class="form-control @error('id_area') is-invalid @enderror">
            @error('id_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-5 mb-3">
            <label class="form-label">Nama Area <span class="text-danger">*</span></label>
            <input type="text" name="nm_area" value="{{ old('nm_area') }}" placeholder="contoh: Visual" class="form-control @error('nm_area') is-invalid @enderror">
            @error('nm_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Divisi <span class="text-danger">*</span></label>
            <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($divisi as $dv) {{-- ulangi setiap divisi aktif dari controller --}}
                <option value="{{ $dv->id_divisi }}" {{ old('id_divisi') == $dv->id_divisi ? 'selected' : '' }}>{{ $dv->kd_departemen }} - {{ $dv->nm_divisi }}</option> {{-- tampil: kode departemen - nama divisi --}}
              @endforeach
            </select>
            @error('id_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('admin.area.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

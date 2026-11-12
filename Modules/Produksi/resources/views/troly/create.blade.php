@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Troly') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Troly</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode dibuat otomatis: TRN-xxx untuk Normal, TRA-xxx untuk Abnormal. Jenis tidak bisa diubah setelah disimpan.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.troly.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Jenis <span class="text-danger">*</span></label>
            <select name="jenis" class="form-control @error('jenis') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach (['Normal', 'Abnormal'] as $j)
                <option value="{{ $j }}" {{ old('jenis') == $j ? 'selected' : '' }}>{{ $j }}</option>
              @endforeach
            </select>
            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
            <div class="input-group has-validation"> {{-- input + satuan menempel di kanan --}}
              <input type="number" name="kapasitas" value="{{ old('kapasitas', 20) }}" min="1" max="255" class="form-control @error('kapasitas') is-invalid @enderror"> {{-- bawaan 20 bobbin --}}
              <span class="input-group-text">bobbin</span>
              @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('produksi.troly.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

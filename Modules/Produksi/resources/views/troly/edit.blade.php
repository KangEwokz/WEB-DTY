@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Troly') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Troly {{ $troly->kode_troly }}</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Jenis: <b>{{ $troly->jenis }}</b> (tidak bisa diubah). Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.troly.update', $troly->kode_troly) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
            <div class="input-group has-validation">
              <input type="number" name="kapasitas" value="{{ old('kapasitas', $troly->kapasitas) }}" min="1" max="255" class="form-control @error('kapasitas') is-invalid @enderror">
              <span class="input-group-text">bobbin</span>
              @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.troly.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

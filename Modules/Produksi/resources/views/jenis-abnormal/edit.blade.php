@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Jenis Abnormal') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Jenis Abnormal {{ $jenis->id_jns_abn }}</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode tidak bisa diubah. Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.jenis-abnormal.update', $jenis->id_jns_abn) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Abnormal <span class="text-danger">*</span></label>
            <input type="text" name="nm_abnormal" value="{{ old('nm_abnormal', $jenis->nm_abnormal) }}" maxlength="50" class="form-control @error('nm_abnormal') is-invalid @enderror">
            @error('nm_abnormal') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.jenis-abnormal.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Jabatan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Jabatan</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID Jabatan: <b>{{ $jabatan->id_jabatan }}</b> (tidak bisa diubah). Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.jabatan.update', $jabatan->id_jabatan) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
            <input type="text" name="nm_jabatan" value="{{ old('nm_jabatan', $jabatan->nm_jabatan) }}" maxlength="30" class="form-control @error('nm_jabatan') is-invalid @enderror">
            @error('nm_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.jabatan.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

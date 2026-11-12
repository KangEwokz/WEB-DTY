@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Departemen') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Departemen</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID Departemen: <b>{{ $departemen->id_departemen }}</b> (tidak bisa diubah). Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.departemen.update', $departemen->id_departemen) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT agar cocok dengan route update --}}
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Kode <span class="text-danger">*</span></label>
            <input type="text" name="kd_departemen" value="{{ old('kd_departemen', $departemen->kd_departemen) }}" maxlength="5" class="form-control @error('kd_departemen') is-invalid @enderror"> {{-- old('x', data lama) = isi sebelumnya, jika tidak ada pakai data database --}}
            @error('kd_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-9 mb-3">
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nm_departemen" value="{{ old('nm_departemen', $departemen->nm_departemen) }}" class="form-control @error('nm_departemen') is-invalid @enderror">
            @error('nm_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.departemen.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

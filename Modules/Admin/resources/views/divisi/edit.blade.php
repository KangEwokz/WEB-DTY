@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Divisi') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Divisi</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID Divisi: <b>{{ $divisi->id_divisi }}</b> (tidak bisa diubah). Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.divisi.update', $divisi->id_divisi) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Kode <span class="text-danger">*</span></label>
            <input type="text" name="kd_divisi" value="{{ old('kd_divisi', $divisi->kd_divisi) }}" maxlength="5" class="form-control @error('kd_divisi') is-invalid @enderror">
            @error('kd_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-5 mb-3">
            <label class="form-label">Nama Divisi <span class="text-danger">*</span></label>
            <input type="text" name="nm_divisi" value="{{ old('nm_divisi', $divisi->nm_divisi) }}" class="form-control @error('nm_divisi') is-invalid @enderror">
            @error('nm_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Departemen <span class="text-danger">*</span></label>
            <select name="id_departemen" class="form-control @error('id_departemen') is-invalid @enderror">
              @foreach ($departemen as $d)
                <option value="{{ $d->id_departemen }}" {{ old('id_departemen', $divisi->id_departemen) == $d->id_departemen ? 'selected' : '' }}>{{ $d->kd_departemen }} - {{ $d->nm_departemen }}</option> {{-- terpilih = departemen divisi saat ini --}}
              @endforeach
            </select>
            @error('id_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.divisi.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

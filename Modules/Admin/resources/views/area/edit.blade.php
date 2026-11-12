@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Area') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Area</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode Area: <b>{{ $area->id_area }}</b> (tidak bisa diubah). Status diubah dari tombol di halaman daftar.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.area.update', $area->id_area) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Area <span class="text-danger">*</span></label>
            <input type="text" name="nm_area" value="{{ old('nm_area', $area->nm_area) }}" class="form-control @error('nm_area') is-invalid @enderror">
            @error('nm_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Divisi <span class="text-danger">*</span></label>
            <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
              @foreach ($divisi as $dv)
                <option value="{{ $dv->id_divisi }}" {{ old('id_divisi', $area->id_divisi) == $dv->id_divisi ? 'selected' : '' }}>{{ $dv->kd_departemen }} - {{ $dv->nm_divisi }}</option> {{-- terpilih = divisi area saat ini --}}
              @endforeach
            </select>
            @error('id_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.area.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

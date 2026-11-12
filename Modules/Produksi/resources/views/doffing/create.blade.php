@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Doffing') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Doffing</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Satu data = satu troly hasil doffing. Jumlah doffing di laporan shift dihitung dari data ini.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.doffing.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tgl_doffing" value="{{ old('tgl_doffing', date('Y-m-d')) }}" class="form-control @error('tgl_doffing') is-invalid @enderror"> {{-- bawaan: hari ini --}}
            @error('tgl_doffing') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Shift <span class="text-danger">*</span></label>
            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
              @foreach (['S1' => 'Shift 1 (07.00 - 15.00)', 'S2' => 'Shift 2 (15.00 - 23.00)', 'S3' => 'Shift 3 (23.00 - 07.00)'] as $kode => $nama)
                <option value="{{ $kode }}" {{ old('shift', $shift_sekarang) == $kode ? 'selected' : '' }}>{{ $nama }}</option> {{-- bawaan: shift sesuai jam sekarang --}}
              @endforeach
            </select>
            @error('shift') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        @include('produksi::doffing._form', ['doffing' => null]) {{-- isian troly, merge, bobbin, berat --}}

        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('produksi.doffing.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

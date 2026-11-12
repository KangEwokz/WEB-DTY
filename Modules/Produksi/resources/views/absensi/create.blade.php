@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Absensi & Penempatan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Absensi & Penempatan Operator</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Diisi supervisor di awal shift. Area kerja wajib dipilih untuk operator yang Hadir.</p>
  </div>

  <form action="{{ route('produksi.absensi.store') }}" method="POST"> {{-- kirim ke route store --}}
    @csrf {{-- token keamanan --}}

    <div class="card mb-4"> {{-- kartu tanggal & shift --}}
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3 mb-md-0">
            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tgl_absen" value="{{ old('tgl_absen', date('Y-m-d')) }}" class="form-control @error('tgl_absen') is-invalid @enderror"> {{-- bawaan: tanggal hari ini --}}
            @error('tgl_absen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Shift <span class="text-danger">*</span></label>
            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
              @foreach (['S1' => 'Shift 1 (07.00 - 15.00)', 'S2' => 'Shift 2 (15.00 - 23.00)', 'S3' => 'Shift 3 (23.00 - 07.00)'] as $kode => $nama) {{-- daftar shift: kode => tulisan --}}
                <option value="{{ $kode }}" {{ old('shift', $shift_sekarang) == $kode ? 'selected' : '' }}>{{ $nama }}</option> {{-- bawaan: shift sesuai jam sekarang --}}
              @endforeach
            </select>
            @error('shift') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card"> {{-- kartu daftar operator --}}
      <div class="card-body">
        <p class="card-title">Daftar Operator Produksi ({{ $operator->count() }} orang)</p> {{-- count() = jumlah operator --}}
        @include('produksi::absensi._tabel-operator', ['tersimpan' => collect()]) {{-- tabel operator; collect() = daftar kosong karena belum ada data tersimpan --}}

        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Absensi</button>
          <a href="{{ route('produksi.absensi.index') }}" class="btn btn-light">Batal</a>
        </div>
      </div>
    </div>
  </form>
@endsection {{-- akhir isi halaman --}}

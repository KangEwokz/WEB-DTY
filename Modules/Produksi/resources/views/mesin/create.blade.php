@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Mesin') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Mesin</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode mesin dibuat otomatis: <b>{{ $kode_baru }}</b>.</p> {{-- kode yang akan dipakai --}}
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.mesin.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        @include('produksi::mesin._form', ['mesin' => null]) {{-- isian mesin; null = form kosong --}}
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('produksi.mesin.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

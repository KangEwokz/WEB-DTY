@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Tambah Benang') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Tambah Benang</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Kode merge mengikuti kode dari pabrik dan tidak bisa diubah setelah disimpan.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.benang.store') }}" method="POST"> {{-- kirim ke route store --}}
        @csrf {{-- token keamanan --}}
        @include('produksi::benang._form', ['benang' => null]) {{-- isian benang; null = form kosong --}}
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button>
          <a href="{{ route('produksi.benang.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

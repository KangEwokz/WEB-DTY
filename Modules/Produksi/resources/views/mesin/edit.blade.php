@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Mesin') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Mesin {{ $mesin->kode_mesin }}</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Saat ini di <b>{{ $mesin->line }} posisi {{ $mesin->posisi }}</b>. Untuk memindah mesin, ganti line dan/atau posisinya. Kode mesin tidak berubah.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.mesin.update', $mesin->kode_mesin) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}
        @include('produksi::mesin._form') {{-- isian mesin, terisi dari $mesin --}}
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.mesin.index') }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

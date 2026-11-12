@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Doffing') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Doffing {{ $doffing->id_doffing }}</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">{{ date('d-m-Y', strtotime($doffing->tgl_doffing)) }} · Shift {{ substr($doffing->shift, 1) }}. Tanggal dan shift tidak bisa diubah.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.doffing.update', $doffing->id_doffing) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}

        @include('produksi::doffing._form') {{-- isian, terisi dari $doffing --}}

        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.doffing.index', ['tanggal' => $doffing->tgl_doffing, 'shift' => $doffing->shift]) }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

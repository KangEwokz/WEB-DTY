@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Laporan Shift') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Laporan Shift</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">{{ date('d-m-Y', strtotime($laporan->tgl_laporan)) }} · Shift {{ substr($laporan->shift, 1) }} · dicatat oleh {{ $laporan->nama_spv }}. Tanggal dan shift tidak bisa diubah.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.laporan.update', $laporan->id_laporan) }}" method="POST"> {{-- kirim ke route update --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- ditandai sebagai PUT --}}

        @include('produksi::laporan._form') {{-- isian angka, terisi dari $laporan --}}

        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.laporan.show', $laporan->id_laporan) }}" class="btn btn-light">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

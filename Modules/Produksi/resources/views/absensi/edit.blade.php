@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Ubah Absensi') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Ubah Absensi</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">{{ date('d-m-Y', strtotime($absensi->tgl_absen)) }} · Shift {{ substr($absensi->shift, 1) }} · dicatat oleh {{ $absensi->nama_spv }}. Tanggal dan shift tidak bisa diubah.</p> {{-- substr('S1', 1) = '1' --}}
  </div>

  <form action="{{ route('produksi.absensi.update', $absensi->id_absensi) }}" method="POST"> {{-- kirim ke route update --}}
    @csrf {{-- token keamanan --}}
    @method('PUT') {{-- ditandai sebagai PUT --}}

    <div class="card">
      <div class="card-body">
        <p class="card-title">Daftar Operator Produksi ({{ $operator->count() }} orang)</p>
        @include('produksi::absensi._tabel-operator') {{-- tabel operator; $tersimpan dikirim dari controller --}}

        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
          <a href="{{ route('produksi.absensi.show', $absensi->id_absensi) }}" class="btn btn-light">Batal</a>
        </div>
      </div>
    </div>
  </form>
@endsection {{-- akhir isi halaman --}}

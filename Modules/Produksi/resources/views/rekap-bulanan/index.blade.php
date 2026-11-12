@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Rekap Produksi Bulanan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan form --}}
    <div>
      <h4 class="font-weight-bold mb-1">Rekap Produksi Bulanan</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Pilih bulan, periksa pratinjau, lalu cetak atau simpan sebagai PDF.</p>
    </div>
    <form action="{{ route('produksi.rekap-bulanan.index') }}" method="GET" class="d-flex gap-2"> {{-- form pilih bulan --}}
      <input type="month" name="bulan" value="{{ $bulan }}" class="form-control" style="width: 190px;"> {{-- type month = pilihan bulan & tahun --}}
      <button type="submit" class="btn btn-light"><i class="ti-reload"></i> Tampilkan</button>
      @if (auth()->user()->punyaAkses('produksi.rekap-bulanan.cetak'))
        <a href="{{ route('produksi.rekap-bulanan.cetak', ['bulan' => $bulan, 'langsung' => 1]) }}" target="_blank" class="btn btn-primary"><i class="ti-printer me-1"></i> Cetak</a> {{-- target _blank = buka di tab baru; langsung=1 = dialog print otomatis muncul --}}
      @endif
    </form>
  </div>

  <div class="card">
    <div class="card-body p-2"> {{-- pratinjau laporan --}}
      <iframe src="{{ route('produksi.rekap-bulanan.cetak', ['bulan' => $bulan, 'pratinjau' => 1]) }}" style="width: 100%; height: 820px; border: 0;" title="Pratinjau rekap"></iframe> {{-- iframe = menampilkan halaman cetak di dalam halaman ini --}}
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

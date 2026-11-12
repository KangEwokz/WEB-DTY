@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Detail Absensi') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .badge-kuning { background: #fff4de; color: #b7791f; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label kuning untuk Sakit/Ijin/Cuti */
  .kotak-angka { border: 1px solid #eef0f4; border-radius: 10px; padding: .8rem 1rem; text-align: center; } /* kotak ringkasan kehadiran */
  .kotak-angka .angka { font-size: 1.4rem; font-weight: 700; } /* angka besar */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul --}}
    <div>
      <h4 class="font-weight-bold mb-1">Absensi {{ date('d-m-Y', strtotime($absensi->tgl_absen)) }} · Shift {{ substr($absensi->shift, 1) }}</h4> {{-- judul: tanggal + shift --}}
      <p class="text-muted mb-0">
        Dicatat oleh {{ $absensi->nama_spv }}
        @if ($absensi->nama_pengubah) {{-- tampil hanya jika pernah diubah --}}
          · diubah oleh {{ $absensi->nama_pengubah }} ({{ date('d-m-Y H:i', strtotime($absensi->updated_at)) }})
        @endif
      </p>
    </div>
    <div class="d-flex gap-2">
      @if (auth()->user()->punyaAkses('produksi.absensi.edit'))
        <a href="{{ route('produksi.absensi.edit', $absensi->id_absensi) }}" class="btn btn-primary"><i class="ti-pencil me-1"></i> Ubah</a>
      @endif
      <a href="{{ route('produksi.absensi.index') }}" class="btn btn-light">Kembali</a>
    </div>
  </div>

  <div class="row mb-4"> {{-- ringkasan jumlah per kehadiran --}}
    @foreach (['Hadir' => 'text-success', 'Sakit' => 'text-warning', 'Ijin' => 'text-warning', 'Cuti' => 'text-warning', 'Alfa' => 'text-danger'] as $k => $warna) {{-- kehadiran => warna angka --}}
      <div class="col">
        <div class="kotak-angka bg-white">
          <div class="angka {{ $warna }}">{{ $ringkasan[$k] ?? 0 }}</div> {{-- jumlah; ?? 0 = tampil 0 jika tidak ada --}}
          <small class="text-muted">{{ $k }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Operator</th>
              <th>Kehadiran</th>
              <th>Area Kerja</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($detail as $d) {{-- ulangi setiap operator --}}
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  <div class="font-weight-bold">{{ $d->nama_karyawan }}</div>
                  <small class="text-muted">{{ $d->id_karyawan }} · Grup {{ $d->grup }}</small>
                </td>
                <td>
                  @if ($d->kehadiran == 'Hadir')
                    <span class="badge-aktif">Hadir</span>
                  @elseif ($d->kehadiran == 'Alfa') {{-- elseif = jika tidak, cek kondisi berikutnya --}}
                    <span class="badge-nonaktif">Alfa</span>
                  @else
                    <span class="badge-kuning">{{ $d->kehadiran }}</span> {{-- Sakit / Ijin / Cuti --}}
                  @endif
                </td>
                <td>{{ $d->nm_area ?? '-' }}</td> {{-- '-' jika tidak hadir --}}
                <td>{{ $d->keterangan ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

@extends('auth::layouts.app')

@section('title', 'Rekap Absensi QCT '.\Carbon\Carbon::parse($rekap->periode.'-01')->translatedFormat('F Y'))

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        Diverifikasi oleh {{ $rekap->verifikator ?? '-' }}
        pada {{ $rekap->waktu_verifikasi ? date('d-m-Y H:i', strtotime($rekap->waktu_verifikasi)) : '-' }}
      </p>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Karyawan</th>
              <th>Nama</th>
              <th>Grup</th>
              <th>Hadir</th>
              <th>Sakit</th>
              <th>Ijin</th>
              <th>Alfa</th>
              <th>Cuti</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $d)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->id_karyawan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->grup }}</td>
                <td>{{ $d->hadir }}</td>
                <td>{{ $d->sakit }}</td>
                <td>{{ $d->ijin }}</td>
                <td>{{ $d->alfa }}</td>
                <td>{{ $d->cuti }}</td>
                <td>{{ $d->total }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center">Tidak ada data absensi pada periode ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        @if ($rekap->file_pdf)
          <a href="{{ asset('storage/'.$rekap->file_pdf) }}" target="_blank" class="btn btn-primary me-2"><i class="ti-download"></i> Unduh PDF</a>
        @endif
        <a href="{{ route('admin.rekap-absensi.index') }}" class="btn btn-light">Kembali</a>
      </div>
    </div>
  </div>
@endsection

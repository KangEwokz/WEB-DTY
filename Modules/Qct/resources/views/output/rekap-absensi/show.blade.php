@extends('auth::layouts.app')

@section('title', 'Rekap Absensi QCT '.\Carbon\Carbon::parse($periode.'-01')->translatedFormat('F Y'))

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <p class="card-title mb-1">{{ $id }}</p>
          @if ($rekap)
            <label class="badge badge-outline-success">Terverifikasi</label>
            <span class="text-muted ms-2">oleh {{ $rekap->verifikator }}, {{ date('d-m-Y H:i', strtotime($rekap->waktu_verifikasi)) }}</span>
          @else
            <label class="badge badge-outline-warning">Belum diverifikasi</label>
          @endif
        </div>
        <div>
          @if ($rekap && $rekap->file_pdf)
            <a href="{{ asset('storage/'.$rekap->file_pdf) }}" target="_blank" class="btn btn-inverse-primary btn-sm"><i class="ti-download"></i> PDF</a>
          @endif
          @if ($bisa_verifikasi)
            <form method="POST" action="{{ route('qct.rekap-absensi.verifikasi', $id) }}" class="d-inline"
              onsubmit="return confirm('Verifikasi rekap absensi bulan ini? Absensi bulan ini akan terkunci.')">
              @csrf
              <button type="submit" class="btn btn-primary btn-sm"><i class="ti-check"></i> Verifikasi</button>
            </form>
          @endif
        </div>
      </div>
      @if (! $rekap && ! $bisa_verifikasi)
        <div class="alert alert-warning">Periode belum selesai, rekap baru bisa diverifikasi setelah akhir bulan.</div>
      @endif

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
            @foreach ($data as $d)
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
            @endforeach
          </tbody>
        </table>
      </div>
      <a href="{{ route('qct.rekap-absensi.index') }}" class="btn btn-light mt-3">Kembali</a>
    </div>
  </div>
@endsection

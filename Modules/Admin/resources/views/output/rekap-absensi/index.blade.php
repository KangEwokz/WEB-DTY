@extends('auth::layouts.app')

@section('title', 'Rekap Absensi QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Hanya rekap yang sudah diverifikasi HOD QCT yang tampil di sini.</p>
      <form method="GET" action="{{ route('admin.rekap-absensi.index') }}" class="d-flex mb-3">
        <input type="number" name="tahun" value="{{ request('tahun') }}" class="form-control me-2" style="width: 150px" placeholder="Tahun, cth 2026">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Periode</th>
              <th>Diverifikasi Oleh</th>
              <th>Waktu Verifikasi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($rekap as $r)
              <tr>
                <td>{{ $rekap->firstItem() + $loop->index }}</td>
                <td>{{ \Carbon\Carbon::parse($r->periode.'-01')->translatedFormat('F Y') }}</td>
                <td>{{ $r->verifikator ?? '-' }}</td>
                <td>{{ $r->waktu_verifikasi ? date('d-m-Y H:i', strtotime($r->waktu_verifikasi)) : '-' }}</td>
                <td>
                  <a href="{{ route('admin.rekap-absensi.show', $r->id_rekap) }}" class="btn btn-inverse-info btn-sm">Lihat</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada rekap absensi yang terverifikasi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $rekap->links() }}</div>
    </div>
  </div>
@endsection

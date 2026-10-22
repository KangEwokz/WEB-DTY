@extends('auth::layouts.app')

@section('title', 'Rekap Absensi Bulanan QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Rekap bisa diverifikasi setelah bulannya selesai. Setelah diverifikasi, absensi bulan itu terkunci dan rekapnya tampil untuk Staf Admin.</p>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Periode</th>
              <th>Jumlah Data Absensi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($periode as $p)
              <tr>
                <td>{{ \Carbon\Carbon::parse($p->periode.'-01')->translatedFormat('F Y') }}</td>
                <td>{{ $p->jml_absen }}</td>
                <td>
                  @if (($status[$p->periode] ?? '') == 'Terverifikasi')
                    <label class="badge badge-outline-success">Terverifikasi</label>
                  @else
                    <label class="badge badge-outline-warning">Belum diverifikasi</label>
                  @endif
                </td>
                <td>
                  <a href="{{ route('qct.rekap-absensi.show', buat_kode('RAB', $p->periode)) }}" class="btn btn-inverse-info btn-sm">Lihat</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">Belum ada data absensi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $periode->links() }}</div>
    </div>
  </div>
@endsection

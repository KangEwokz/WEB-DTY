@extends('auth::layouts.app')

@section('title', 'Rekap Check QCT Bulanan')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Rekap bisa diverifikasi setelah semua laporan shift pada bulan itu diverifikasi HOD. Saat diverifikasi, sistem membuat arsip PDF.</p>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Periode</th>
              <th>Troly</th>
              <th>Bobbin</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($periode as $p)
              <tr>
                <td>{{ \Carbon\Carbon::parse($p->periode.'-01')->translatedFormat('F Y') }}</td>
                <td>{{ $p->jml_troly }}</td>
                <td>{{ $p->jml_bobbin }}</td>
                <td>
                  @if (($status[$p->periode] ?? '') == 'Terverifikasi')
                    <label class="badge badge-outline-success">Terverifikasi</label>
                  @else
                    <label class="badge badge-outline-warning">Belum diverifikasi</label>
                  @endif
                </td>
                <td>
                  <a href="{{ route('qct.rekap-check.show', buat_kode('RCK', $p->periode)) }}" class="btn btn-inverse-info btn-sm">Lihat</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $periode->links() }}</div>
    </div>
  </div>
@endsection

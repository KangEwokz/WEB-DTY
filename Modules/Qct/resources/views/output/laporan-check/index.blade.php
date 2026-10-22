@extends('auth::layouts.app')

@section('title', 'Laporan Check QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Laporan per tanggal dan shift. Setelah diverifikasi, data cek troly, abnormal, dan sampel pada tanggal dan shift itu terkunci.</p>
      <form method="GET" action="{{ route('qct.laporan-check.index') }}" class="d-flex flex-wrap mb-3">
        <input type="month" name="bulan" value="{{ request('bulan') }}" class="form-control me-2 mb-2" style="width: 180px">
        <select name="status" class="form-control me-2 mb-2" style="width: 190px">
          <option value="">Semua status</option>
          <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum diverifikasi</option>
          <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Troly</th>
              <th>Bobbin</th>
              <th>Status</th>
              <th>Waktu Verifikasi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($laporan as $l)
              <tr>
                <td>{{ date('d-m-Y', strtotime($l->tgl_doffing)) }}</td>
                <td>{{ $l->shift }}</td>
                <td>{{ $l->jml_troly }}</td>
                <td>{{ $l->jml_bobbin }}</td>
                <td>
                  @if ($l->status == 'Terverifikasi')
                    <label class="badge badge-outline-success">Terverifikasi</label>
                  @else
                    <label class="badge badge-outline-warning">Belum diverifikasi</label>
                  @endif
                </td>
                <td>{{ $l->waktu_verifikasi ? date('d-m-Y H:i', strtotime($l->waktu_verifikasi)) : '-' }}</td>
                <td>
                  <a href="{{ route('qct.laporan-check.show', buat_kode('LAP', $l->tgl_doffing, $l->shift)) }}" class="btn btn-inverse-info btn-sm">Lihat</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $laporan->links() }}</div>
    </div>
  </div>
@endsection

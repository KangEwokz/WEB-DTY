@extends('auth::layouts.app')

@section('title', 'Absensi & Penugasan Area QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('qct.absensi.index') }}" class="d-flex flex-wrap">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
          <select name="shift" class="form-control me-2 mb-2" style="width: 140px">
            <option value="">Semua shift</option>
            @foreach (['S1', 'S2', 'S3', 'GS'] as $s)
              <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('qct.absensi.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Karyawan</th>
              <th>Grup</th>
              <th>Kehadiran</th>
              <th>Area Tugas</th>
              <th>Dokumen</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($absensi as $a)
              <tr>
                <td>{{ $absensi->firstItem() + $loop->index }}</td>
                <td>{{ date('d-m-Y', strtotime($a->tgl_absen)) }}</td>
                <td>{{ $a->shift }}</td>
                <td>{{ $a->nama_karyawan }}</td>
                <td>{{ $a->grup }}</td>
                <td>
                  <label class="badge {{ $a->kehadiran == 'Hadir' ? 'badge-outline-success' : 'badge-outline-warning' }}">{{ $a->kehadiran }}</label>
                </td>
                <td>{{ $a->nm_area ?? '-' }}</td>
                <td>
                  @if ($a->dok_file)
                    <a href="{{ asset('storage/'.$a->dok_file) }}" target="_blank">Lihat</a>
                  @else
                    -
                  @endif
                </td>
                <td>
                  {{-- Absensi bulan yang sudah diverifikasi HOD terkunci --}}
                  @if (sudah_terkunci($a->tgl_absen))
                    <label class="badge badge-outline-dark">Terkunci</label>
                  @else
                    <a href="{{ route('qct.absensi.edit', $a->id_absen) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('qct.absensi.destroy', $a->id_absen) }}" class="d-inline"
                      onsubmit="return confirm('Hapus data absensi ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $absensi->links() }}</div>
    </div>
  </div>
@endsection

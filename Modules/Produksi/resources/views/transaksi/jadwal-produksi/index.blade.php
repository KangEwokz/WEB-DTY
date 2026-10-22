@extends('auth::layouts.app')

@section('title', 'Jadwal Produksi')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('produksi.jadwal-produksi.index') }}" class="d-flex flex-wrap">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
          <select name="line" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua line</option>
            @foreach ([401, 402, 403] as $l)
              <option value="{{ $l }}" {{ request('line') == $l ? 'selected' : '' }}>Line {{ $l }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('produksi.jadwal-produksi.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Jadwal</th>
              <th>Tanggal</th>
              <th>Merge</th>
              <th>Denier</th>
              <th>Filamen</th>
              <th>Warna PPT</th>
              <th>Line</th>
              <th>Posisi</th>
              <th>Customer</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($jadwal as $j)
              <tr>
                <td>{{ $jadwal->firstItem() + $loop->index }}</td>
                <td>{{ $j->id_jadwal }}</td>
                <td>{{ date('d-m-Y', strtotime($j->tgl_produksi)) }}</td>
                <td>{{ $j->merge }}</td>
                <td>{{ $j->denier }}</td>
                <td>{{ $j->filamen }}</td>
                <td>{{ $j->warna_ppt ?? '-' }}</td>
                <td>{{ $j->line }}</td>
                <td>{{ $j->pos_awal }} - {{ $j->pos_akhir }}</td>
                <td>{{ $j->customer ?? '-' }}</td>
                <td>
                  <a href="{{ route('produksi.jadwal-produksi.edit', $j->id_jadwal) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  <form method="POST" action="{{ route('produksi.jadwal-produksi.destroy', $j->id_jadwal) }}" class="d-inline"
                    onsubmit="return confirm('Hapus jadwal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $jadwal->links() }}</div>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Data Doffing')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('produksi.doffing.index') }}" class="d-flex flex-wrap">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
          <select name="shift" class="form-control me-2 mb-2" style="width: 140px">
            <option value="">Semua shift</option>
            @foreach (['S1', 'S2', 'S3'] as $s)
              <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('produksi.doffing.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Doffing</th>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Troly</th>
              <th>Merge</th>
              <th>Denier</th>
              <th>Filamen</th>
              <th>Warna PPT</th>
              <th>Bobbin</th>
              <th>Berat (kg)</th>
              <th>Cek QCT</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($doffing as $d)
              <tr>
                <td>{{ $doffing->firstItem() + $loop->index }}</td>
                <td>{{ $d->id_doffing }}</td>
                <td>{{ date('d-m-Y', strtotime($d->tgl_doffing)) }}</td>
                <td>{{ $d->shift }}</td>
                <td>{{ $d->nmr_troly }}</td>
                <td>{{ $d->merge }}</td>
                <td>{{ $d->denier }}</td>
                <td>{{ $d->filamen }}</td>
                <td>{{ $d->warna_ppt ?? '-' }}</td>
                <td>{{ $d->jml_bobbin }}</td>
                <td>{{ $d->berat_troly ?? '-' }}</td>
                <td>
                  @if ($d->id_check)
                    <label class="badge badge-outline-success">Sudah dicek</label>
                  @else
                    <label class="badge badge-outline-warning">Belum dicek</label>
                  @endif
                </td>
                <td>
                  {{-- Troly yang sudah dicek QCT tidak bisa diubah/dihapus --}}
                  @if (! $d->id_check)
                    <a href="{{ route('produksi.doffing.edit', $d->id_doffing) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('produksi.doffing.destroy', $d->id_doffing) }}" class="d-inline"
                      onsubmit="return confirm('Hapus data doffing ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $doffing->links() }}</div>
    </div>
  </div>
@endsection

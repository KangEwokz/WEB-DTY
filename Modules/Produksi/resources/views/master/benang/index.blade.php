@extends('auth::layouts.app')

@section('title', 'Data Benang')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('produksi.benang.index') }}" class="d-flex flex-wrap">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2 mb-2" style="width: 180px" placeholder="Cari kode merge">
          <select name="status_aktif" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua status</option>
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ request('status_aktif') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('produksi.benang.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Merge</th>
              <th>Denier</th>
              <th>Filamen</th>
              <th>Warna PPT</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($benang as $b)
              <tr>
                <td>{{ $benang->firstItem() + $loop->index }}</td>
                <td>{{ $b->merge }}</td>
                <td>{{ $b->denier }}</td>
                <td>{{ $b->filamen }}</td>
                <td>{{ $b->warna_ppt ?? '-' }}</td>
                <td>
                  <label class="badge {{ $b->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $b->status_aktif }}</label>
                </td>
                <td>
                  <a href="{{ route('produksi.benang.edit', $b->merge) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($b->status_aktif == 'Aktif')
                    <form method="POST" action="{{ route('produksi.benang.destroy', $b->merge) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan merge ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Nonaktifkan</button>
                    </form>
                  @endif
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
      <div class="mt-3">{{ $benang->links() }}</div>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Data Area QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('qct.area.index') }}" class="d-flex">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2" placeholder="Cari nama area">
          <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <a href="{{ route('qct.area.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Area</th>
              <th>Nama Area</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($area as $a)
              <tr>
                <td>{{ $area->firstItem() + $loop->index }}</td>
                <td>{{ $a->id_area }}</td>
                <td>{{ $a->nm_area }}</td>
                <td>
                  <label class="badge {{ $a->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $a->status_aktif }}</label>
                </td>
                <td>
                  <a href="{{ route('qct.area.edit', $a->id_area) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($a->status_aktif == 'Aktif')
                    <form method="POST" action="{{ route('qct.area.destroy', $a->id_area) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan area ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Nonaktifkan</button>
                    </form>
                  @endif
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
      <div class="mt-3">{{ $area->links() }}</div>
    </div>
  </div>
@endsection

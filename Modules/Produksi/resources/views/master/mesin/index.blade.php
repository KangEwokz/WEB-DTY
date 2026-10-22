@extends('auth::layouts.app')

@section('title', 'Data Mesin')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="GET" action="{{ route('produksi.mesin.index') }}" class="d-flex flex-wrap mb-3">
        <select name="line" class="form-control me-2 mb-2" style="width: 150px">
          <option value="">Semua line</option>
          @foreach ([401, 402, 403] as $l)
            <option value="{{ $l }}" {{ request('line') == $l ? 'selected' : '' }}>Line {{ $l }}</option>
          @endforeach
        </select>
        <select name="status" class="form-control me-2 mb-2" style="width: 160px">
          <option value="">Semua status</option>
          @foreach (['Running', 'Stop', 'Maintenance'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Mesin</th>
              <th>Line</th>
              <th>Posisi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($mesin as $m)
              <tr>
                <td>{{ $mesin->firstItem() + $loop->index }}</td>
                <td>{{ $m->kode_mesin }}</td>
                <td>{{ $m->line }}</td>
                <td>{{ $m->posisi }}</td>
                <td>
                  <label class="badge {{ $m->status == 'Running' ? 'badge-outline-success' : ($m->status == 'Stop' ? 'badge-outline-danger' : 'badge-outline-warning') }}">{{ $m->status }}</label>
                </td>
                <td>
                  <a href="{{ route('produksi.mesin.edit', $m->kode_mesin) }}" class="btn btn-inverse-primary btn-sm">Ubah Status</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $mesin->links() }}</div>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Data Divisi')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.divisi.index') }}" class="d-flex">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2" placeholder="Cari nama divisi">
          <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <a href="{{ route('admin.divisi.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID</th>
              <th>Kode</th>
              <th>Nama Divisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($divisi as $d)
              <tr>
                <td>{{ $divisi->firstItem() + $loop->index }}</td>
                <td>{{ $d->id_divisi }}</td>
                <td>{{ $d->kd_divisi }}</td>
                <td>{{ $d->nm_divisi }}</td>
                <td>
                  <a href="{{ route('admin.divisi.edit', $d->id_divisi) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
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
      <div class="mt-3">{{ $divisi->links() }}</div>
    </div>
  </div>
@endsection

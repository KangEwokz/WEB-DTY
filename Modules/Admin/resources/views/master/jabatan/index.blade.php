@extends('auth::layouts.app')

@section('title', 'Data Jabatan')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.jabatan.index') }}" class="d-flex">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2" placeholder="Cari nama jabatan">
          <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <a href="{{ route('admin.jabatan.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID</th>
              <th>Nama Jabatan</th>
              <th>Divisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($jabatan as $j)
              <tr>
                <td>{{ $jabatan->firstItem() + $loop->index }}</td>
                <td>{{ $j->id_jabatan }}</td>
                <td>{{ $j->nm_jabatan }}</td>
                <td>{{ $j->nm_divisi ?? '-' }}</td>
                <td>
                  <a href="{{ route('admin.jabatan.edit', $j->id_jabatan) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
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
      <div class="mt-3">{{ $jabatan->links() }}</div>
    </div>
  </div>
@endsection

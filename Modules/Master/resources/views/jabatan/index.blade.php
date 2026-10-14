@extends('layouts.app')

@section('title', 'Data Jabatan')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
      <h4 class="font-weight-bold mb-0">Data Jabatan</h4>
      @can('jabatan.create')
        <a href="{{ route('jabatan.create') }}" class="btn btn-primary"><i class="ti-plus"></i> Tambah Jabatan</a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-description">Total: <strong>{{ $jabatan->count() }}</strong> jabatan</p>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Jabatan</th>
                  @canany(['jabatan.edit', 'jabatan.delete'])
                    <th>Aksi</th>
                  @endcanany
                </tr>
              </thead>
              <tbody>
                @forelse ($jabatan as $row)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->id_jabatan }}</td>
                    <td>{{ $row->nm_jabatan }}</td>
                    @canany(['jabatan.edit', 'jabatan.delete'])
                      <td class="text-nowrap">
                        @can('jabatan.edit')
                          <a href="{{ route('jabatan.edit', $row->id_jabatan) }}" class="btn btn-warning btn-xs">Edit</a>
                        @endcan
                        @can('jabatan.delete')
                          <form method="POST" action="{{ route('jabatan.destroy', $row->id_jabatan) }}" class="d-inline" onsubmit="return confirm(@js('Hapus jabatan '.$row->id_jabatan.' - '.$row->nm_jabatan.'?'))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                          </form>
                        @endcan
                      </td>
                    @endcanany
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center">Belum ada data jabatan.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

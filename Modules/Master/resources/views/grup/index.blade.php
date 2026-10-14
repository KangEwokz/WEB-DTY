@extends('layouts.app')

@section('title', 'Data Grup')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
      <h4 class="font-weight-bold mb-0">Data Grup</h4>
      @can('grup.create')
        <a href="{{ route('grup.create') }}" class="btn btn-primary"><i class="ti-plus"></i> Tambah Grup</a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-description">Total: <strong>{{ $grup->count() }}</strong> grup</p>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Grup</th>
                  @canany(['grup.edit', 'grup.delete'])
                    <th>Aksi</th>
                  @endcanany
                </tr>
              </thead>
              <tbody>
                @forelse ($grup as $row)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->id_grup }}</td>
                    <td>{{ $row->nm_grup }}</td>
                    @canany(['grup.edit', 'grup.delete'])
                      <td class="text-nowrap">
                        @can('grup.edit')
                          <a href="{{ route('grup.edit', $row->id_grup) }}" class="btn btn-warning btn-xs">Edit</a>
                        @endcan
                        @can('grup.delete')
                          <form method="POST" action="{{ route('grup.destroy', $row->id_grup) }}" class="d-inline" onsubmit="return confirm(@js('Hapus grup '.$row->id_grup.' - '.$row->nm_grup.'?'))">
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
                    <td colspan="4" class="text-center">Belum ada data grup.</td>
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

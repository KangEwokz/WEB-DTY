@extends('layouts.app')

@section('title', 'Data Divisi')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
      <h4 class="font-weight-bold mb-0">Data Divisi</h4>
      @can('divisi.create')
        <a href="{{ route('divisi.create') }}" class="btn btn-primary"><i class="ti-plus"></i> Tambah Divisi</a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-description">Total: <strong>{{ $divisi->count() }}</strong> divisi</p>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Divisi</th>
                  @canany(['divisi.edit', 'divisi.delete'])
                    <th>Aksi</th>
                  @endcanany
                </tr>
              </thead>
              <tbody>
                @forelse ($divisi as $row)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->id_divisi }}</td>
                    <td>{{ $row->nm_divisi }}</td>
                    @canany(['divisi.edit', 'divisi.delete'])
                      <td class="text-nowrap">
                        @can('divisi.edit')
                          <a href="{{ route('divisi.edit', $row->id_divisi) }}" class="btn btn-warning btn-xs">Edit</a>
                        @endcan
                        @can('divisi.delete')
                          <form method="POST" action="{{ route('divisi.destroy', $row->id_divisi) }}" class="d-inline" onsubmit="return confirm(@js('Hapus divisi '.$row->id_divisi.' - '.$row->nm_divisi.'?'))">
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
                    <td colspan="4" class="text-center">Belum ada data divisi.</td>
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

@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
      <h4 class="font-weight-bold mb-0">Data Shift</h4>
      @can('shift.create')
        <a href="{{ route('shift.create') }}" class="btn btn-primary"><i class="ti-plus"></i> Tambah Shift</a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-lg-10 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-description">Total: <strong>{{ $shift->count() }}</strong> shift</p>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Shift</th>
                  <th>Jam Masuk</th>
                  <th>Jam Pulang</th>
                  @canany(['shift.edit', 'shift.delete'])
                    <th>Aksi</th>
                  @endcanany
                </tr>
              </thead>
              <tbody>
                @forelse ($shift as $row)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->kode_shift }}</td>
                    <td>{{ $row->nama_shift }}</td>
                    <td>{{ $row->jam_masuk ? substr($row->jam_masuk, 0, 5) : '-' }}</td>
                    <td>{{ $row->jam_pulang ? substr($row->jam_pulang, 0, 5) : '-' }}</td>
                    @canany(['shift.edit', 'shift.delete'])
                      <td class="text-nowrap">
                        @can('shift.edit')
                          <a href="{{ route('shift.edit', $row->kode_shift) }}" class="btn btn-warning btn-xs">Edit</a>
                        @endcan
                        @can('shift.delete')
                          <form method="POST" action="{{ route('shift.destroy', $row->kode_shift) }}" class="d-inline" onsubmit="return confirm(@js('Hapus shift '.$row->kode_shift.' - '.$row->nama_shift.'?'))">
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
                    <td colspan="6" class="text-center">Belum ada data shift.</td>
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

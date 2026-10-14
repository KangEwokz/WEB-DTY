@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
      <h4 class="font-weight-bold mb-0">Data Karyawan</h4>
      @can('karyawan.create')
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary"><i class="ti-plus"></i> Tambah Karyawan</a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form method="GET" action="{{ route('karyawan.index') }}" class="filter-form row g-2 mb-4">
            <div class="col-md-3">
              <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari ID / nama karyawan">
            </div>
            <div class="col-md-2">
              <select name="id_divisi" class="form-select">
                <option value="">Semua divisi</option>
                @foreach ($divisi as $d)
                  <option value="{{ $d->id_divisi }}" {{ request('id_divisi') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nm_divisi }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <select name="id_jabatan" class="form-select">
                <option value="">Semua jabatan</option>
                @foreach ($jabatan as $j)
                  <option value="{{ $j->id_jabatan }}" {{ request('id_jabatan') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nm_jabatan }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <select name="id_grup" class="form-select">
                <option value="">Semua grup</option>
                @foreach ($grup as $g)
                  <option value="{{ $g->id_grup }}" {{ request('id_grup') == $g->id_grup ? 'selected' : '' }}>{{ $g->nm_grup }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
              <a href="{{ route('karyawan.index') }}" class="btn btn-light">Reset</a>
            </div>
          </form>

          <p class="card-description">Total: <strong>{{ $karyawan->total() }}</strong> karyawan</p>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>ID Karyawan</th>
                  <th>Nama Karyawan</th>
                  <th>Jenis Kelamin</th>
                  <th>Departemen</th>
                  <th>Divisi</th>
                  <th>Jabatan</th>
                  <th>Grup</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($karyawan as $k)
                  <tr>
                    <td>{{ $karyawan->firstItem() + $loop->index }}</td>
                    <td>{{ $k->id_karyawan }}</td>
                    <td>{{ $k->nama_karyawan }}</td>
                    <td>{{ $k->jns_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $k->departemen }}</td>
                    <td>{{ $k->nm_divisi }}</td>
                    <td>{{ $k->nm_jabatan }}</td>
                    <td>{{ $k->nm_grup }}</td>
                    <td class="text-nowrap">
                      <a href="{{ route('karyawan.show', $k->id_karyawan) }}" class="btn btn-info btn-xs">Detail</a>
                      @can('karyawan.edit')
                        <a href="{{ route('karyawan.edit', $k->id_karyawan) }}" class="btn btn-warning btn-xs">Edit</a>
                      @endcan
                      @can('karyawan.delete')
                        <form method="POST" action="{{ route('karyawan.destroy', $k->id_karyawan) }}" class="d-inline" onsubmit="return confirm(@js('Hapus karyawan '.$k->id_karyawan.' - '.$k->nama_karyawan.'?'))">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                        </form>
                      @endcan
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center">Data karyawan tidak ditemukan.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $karyawan->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

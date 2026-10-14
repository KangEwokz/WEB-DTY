@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
  <div class="row">
    <div class="col-md-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Detail Karyawan</h4>

          <table class="table table-bordered">
            <tr>
              <th style="width: 35%">ID Karyawan</th>
              <td>{{ $karyawan->id_karyawan }}</td>
            </tr>
            <tr>
              <th>Nama Karyawan</th>
              <td>{{ $karyawan->nama_karyawan }}</td>
            </tr>
            <tr>
              <th>Jenis Kelamin</th>
              <td>{{ $karyawan->jns_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
              <th>Departemen</th>
              <td>{{ $karyawan->departemen }}</td>
            </tr>
            <tr>
              <th>Divisi</th>
              <td>{{ $karyawan->id_divisi }} - {{ $karyawan->nm_divisi }}</td>
            </tr>
            <tr>
              <th>Jabatan</th>
              <td>{{ $karyawan->id_jabatan }} - {{ $karyawan->nm_jabatan }}</td>
            </tr>
            <tr>
              <th>Grup</th>
              <td>{{ $karyawan->id_grup }} - {{ $karyawan->nm_grup }}</td>
            </tr>
            <tr>
              <th>Akun User</th>
              <td>{{ $karyawan->email ?? 'Belum punya akun' }}</td>
            </tr>
          </table>

          <div class="mt-4">
            @can('karyawan.edit')
              <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}" class="btn btn-primary me-2">Edit</a>
            @endcan
            <a href="{{ route('karyawan.index') }}" class="btn btn-light">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
  <div class="card">
    <div class="card-body">
      <table class="table">
        <tr>
          <th style="width: 200px">ID Karyawan</th>
          <td>{{ $karyawan->id_karyawan }}</td>
        </tr>
        <tr>
          <th>Nama</th>
          <td>{{ $karyawan->nama_karyawan }}</td>
        </tr>
        <tr>
          <th>Jenis Kelamin</th>
          <td>{{ $karyawan->jns_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
          <th>Divisi</th>
          <td>{{ $karyawan->nm_divisi ?? '-' }}</td>
        </tr>
        <tr>
          <th>Jabatan</th>
          <td>{{ $karyawan->nm_jabatan }}</td>
        </tr>
        <tr>
          <th>Area Default</th>
          <td>{{ $karyawan->nm_area ?? '-' }}</td>
        </tr>
        <tr>
          <th>Grup</th>
          <td>{{ $karyawan->grup }}</td>
        </tr>
        <tr>
          <th>Tanggal Masuk</th>
          <td>{{ date('d-m-Y', strtotime($karyawan->tgl_masuk)) }}</td>
        </tr>
        <tr>
          <th>No. HP</th>
          <td>{{ $karyawan->no_hp ?? '-' }}</td>
        </tr>
        <tr>
          <th>Email</th>
          <td>{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
          <th>Akun</th>
          <td>{{ $karyawan->username ?? 'Belum punya akun' }}</td>
        </tr>
        <tr>
          <th>Status</th>
          <td>
            <label class="badge {{ $karyawan->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $karyawan->status_aktif }}</label>
          </td>
        </tr>
      </table>
      <div class="mt-3">
        <a href="{{ route('admin.karyawan.edit', $karyawan->id_karyawan) }}" class="btn btn-primary me-2">Ubah</a>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Kembali</a>
      </div>
    </div>
  </div>
@endsection

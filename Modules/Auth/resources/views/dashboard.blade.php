@extends('auth::layouts.app')

@section('title', 'Dashboard')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Selamat datang, {{ $karyawan->nama_karyawan }}</p>
          <table class="table">
            <tr>
              <th>Username</th>
              <td>{{ auth()->user()->username }}</td>
            </tr>
            <tr>
              <th>ID Karyawan</th>
              <td>{{ $karyawan->id_karyawan }}</td>
            </tr>
            <tr>
              <th>Jabatan</th>
              <td>{{ $karyawan->nm_jabatan }}</td>
            </tr>
            <tr>
              <th>Divisi</th>
              <td>{{ $karyawan->nm_divisi ?? '-' }}</td>
            </tr>
            <tr>
              <th>Role</th>
              <td>{{ auth()->user()->getRoleNames()->first() }}</td>
            </tr>
            <tr>
              <th>Area Aktif</th>
              <td>{{ $area ?? '-' }}</td>
            </tr>
            <tr>
              <th>Shift Sekarang</th>
              <td>{{ $shift['shift'] }} ({{ date('d-m-Y', strtotime($shift['tanggal'])) }})</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

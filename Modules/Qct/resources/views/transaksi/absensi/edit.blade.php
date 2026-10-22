@extends('auth::layouts.app')

@section('title', 'Ubah Absensi QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        {{ $absensi->nama_karyawan }}, tanggal {{ date('d-m-Y', strtotime($absensi->tgl_absen)) }} shift {{ $absensi->shift }}.
        Kalau karyawan/tanggal/shift salah, hapus data ini lalu input ulang.
      </p>
      <form method="POST" action="{{ route('qct.absensi.update', $absensi->id_absen) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('qct::transaksi.absensi.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.absensi.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

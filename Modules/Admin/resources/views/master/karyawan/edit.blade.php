@extends('auth::layouts.app')

@section('title', 'Ubah Karyawan')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">ID Karyawan: <strong>{{ $karyawan->id_karyawan }}</strong></p>
      <form method="POST" action="{{ route('admin.karyawan.update', $karyawan->id_karyawan) }}">
        @csrf
        @method('PUT')
        @include('admin::master.karyawan.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

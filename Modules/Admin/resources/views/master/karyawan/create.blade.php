@extends('auth::layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">ID karyawan dibuat otomatis dari tahun masuk + nomor urut.</p>
      <form method="POST" action="{{ route('admin.karyawan.store') }}">
        @csrf
        @include('admin::master.karyawan.form', ['karyawan' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

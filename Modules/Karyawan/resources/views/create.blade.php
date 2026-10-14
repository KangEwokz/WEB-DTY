@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
  <div class="row">
    <div class="col-md-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah Karyawan</h4>

          <form method="POST" action="{{ route('karyawan.store') }}">
            @csrf

            @include('karyawan::_form', ['karyawan' => null])

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('karyawan.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

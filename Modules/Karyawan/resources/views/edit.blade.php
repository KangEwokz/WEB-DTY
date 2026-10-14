@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
  <div class="row">
    <div class="col-md-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Karyawan</h4>

          <form method="POST" action="{{ route('karyawan.update', $karyawan->id_karyawan) }}">
            @csrf
            @method('PUT')

            @include('karyawan::_form')

            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
            <a href="{{ route('karyawan.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

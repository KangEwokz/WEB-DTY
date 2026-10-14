@extends('layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah Jabatan</h4>

          <form method="POST" action="{{ route('jabatan.store') }}">
            @csrf

            @include('master::jabatan._form', ['jabatan' => null])

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('jabatan.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

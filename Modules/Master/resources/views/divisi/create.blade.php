@extends('layouts.app')

@section('title', 'Tambah Divisi')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah Divisi</h4>

          <form method="POST" action="{{ route('divisi.store') }}">
            @csrf

            @include('master::divisi._form', ['divisi' => null])

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('divisi.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

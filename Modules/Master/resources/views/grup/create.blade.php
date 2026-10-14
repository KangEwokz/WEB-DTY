@extends('layouts.app')

@section('title', 'Tambah Grup')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah Grup</h4>

          <form method="POST" action="{{ route('grup.store') }}">
            @csrf

            @include('master::grup._form', ['grup' => null])

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('grup.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

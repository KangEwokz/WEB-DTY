@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tambah Shift</h4>

          <form method="POST" action="{{ route('shift.store') }}">
            @csrf

            @include('master::shift._form', ['shift' => null])

            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('shift.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

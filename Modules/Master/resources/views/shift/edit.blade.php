@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Shift</h4>

          <form method="POST" action="{{ route('shift.update', $shift->kode_shift) }}">
            @csrf
            @method('PUT')

            @include('master::shift._form')

            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
            <a href="{{ route('shift.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

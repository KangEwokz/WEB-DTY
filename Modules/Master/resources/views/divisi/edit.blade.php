@extends('layouts.app')

@section('title', 'Edit Divisi')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Divisi</h4>

          <form method="POST" action="{{ route('divisi.update', $divisi->id_divisi) }}">
            @csrf
            @method('PUT')

            @include('master::divisi._form')

            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
            <a href="{{ route('divisi.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

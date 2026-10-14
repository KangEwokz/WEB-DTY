@extends('layouts.app')

@section('title', 'Edit Jabatan')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Jabatan</h4>

          <form method="POST" action="{{ route('jabatan.update', $jabatan->id_jabatan) }}">
            @csrf
            @method('PUT')

            @include('master::jabatan._form')

            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
            <a href="{{ route('jabatan.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Grup')

@section('content')
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Edit Grup</h4>

          <form method="POST" action="{{ route('grup.update', $grup->id_grup) }}">
            @csrf
            @method('PUT')

            @include('master::grup._form')

            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
            <a href="{{ route('grup.index') }}" class="btn btn-light">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

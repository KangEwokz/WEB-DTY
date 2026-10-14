@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin">
      <h4 class="font-weight-bold mb-0">Dashboard</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>.</p>
        </div>
      </div>
    </div>
  </div>
@endsection

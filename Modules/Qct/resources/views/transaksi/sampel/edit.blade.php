@extends('auth::layouts.app')

@section('title', 'Ubah Sampel')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        {{ $sampel->id_sampel }}: merge {{ $sampel->merge }}, {{ date('d-m-Y', strtotime($sampel->tgl_sampel)) }} shift {{ $sampel->shift }}
      </p>
      <form method="POST" action="{{ route('qct.sampel.update', $sampel->id_sampel) }}">
        @csrf
        @method('PUT')
        @include('qct::transaksi.sampel.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.sampel.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

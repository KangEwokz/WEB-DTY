@extends('auth::layouts.app')

@section('title', 'Ubah Jadwal Produksi')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">ID Jadwal: <strong>{{ $jadwal->id_jadwal }}</strong></p>
      <form method="POST" action="{{ route('produksi.jadwal-produksi.update', $jadwal->id_jadwal) }}">
        @csrf
        @method('PUT')
        @include('produksi::transaksi.jadwal-produksi.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.jadwal-produksi.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

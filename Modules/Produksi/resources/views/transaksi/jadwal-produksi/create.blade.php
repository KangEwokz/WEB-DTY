@extends('auth::layouts.app')

@section('title', 'Tambah Jadwal Produksi')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('produksi.jadwal-produksi.store') }}">
        @csrf
        @include('produksi::transaksi.jadwal-produksi.form', ['jadwal' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.jadwal-produksi.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

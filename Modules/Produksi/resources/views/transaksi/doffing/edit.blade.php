@extends('auth::layouts.app')

@section('title', 'Ubah Data Doffing')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        ID Doffing: <strong>{{ $doffing->id_doffing }}</strong>,
        tanggal {{ date('d-m-Y', strtotime($doffing->tgl_doffing)) }} shift {{ $doffing->shift }}
        (tanggal dan shift tidak bisa diubah karena menjadi bagian ID).
      </p>
      <form method="POST" action="{{ route('produksi.doffing.update', $doffing->id_doffing) }}">
        @csrf
        @method('PUT')
        @include('produksi::transaksi.doffing.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.doffing.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

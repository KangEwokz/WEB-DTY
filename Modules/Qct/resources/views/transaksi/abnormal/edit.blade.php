@extends('auth::layouts.app')

@section('title', 'Ubah Bobbin Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        {{ $abnormal->id_abnormal }}, troly {{ $abnormal->nmr_troly }} ({{ date('d-m-Y', strtotime($abnormal->tgl_doffing)) }} {{ $abnormal->shift }})
      </p>
      <form method="POST" action="{{ route('qct.abnormal.update', $abnormal->id_abnormal) }}">
        @csrf
        @method('PUT')
        @include('qct::transaksi.abnormal.form')
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.abnormal.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

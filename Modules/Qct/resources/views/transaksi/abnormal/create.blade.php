@extends('auth::layouts.app')

@section('title', 'Tambah Bobbin Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Satu isian = satu bobbin abnormal. Status otomatis Pending sampai diuji Lab.</p>
      <form method="POST" action="{{ route('qct.abnormal.store') }}">
        @csrf
        <div class="form-group">
          <label>Troly (yang sudah dicek visual)</label>
          <select name="id_doffing" class="form-control @error('id_doffing') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach ($troly as $t)
              <option value="{{ $t->id_doffing }}" {{ old('id_doffing', $pilih) == $t->id_doffing ? 'selected' : '' }}>
                {{ $t->nmr_troly }} - {{ $t->merge }} ({{ date('d-m-Y', strtotime($t->tgl_doffing)) }} {{ $t->shift }})
              </option>
            @endforeach
          </select>
          @error('id_doffing')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @include('qct::transaksi.abnormal.form', ['abnormal' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.abnormal.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Tambah Data Doffing')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('produksi.doffing.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Tanggal Doffing</label>
            <input type="date" name="tgl_doffing" value="{{ old('tgl_doffing', $sekarang['tanggal']) }}"
              class="form-control @error('tgl_doffing') is-invalid @enderror">
            @error('tgl_doffing')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 form-group">
            <label>Shift</label>
            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
              @foreach (['S1', 'S2', 'S3'] as $s)
                <option value="{{ $s }}" {{ old('shift', $sekarang['shift']) == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
            @error('shift')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        @include('produksi::transaksi.doffing.form', ['doffing' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.doffing.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

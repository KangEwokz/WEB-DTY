@extends('auth::layouts.app')

@section('title', 'Tambah Benang')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('produksi.benang.store') }}">
        @csrf
        <div class="form-group">
          <label>Kode Merge</label>
          <input type="text" name="merge" value="{{ old('merge') }}" maxlength="10"
            class="form-control @error('merge') is-invalid @enderror">
          @error('merge')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Denier</label>
          <input type="number" name="denier" value="{{ old('denier') }}"
            class="form-control @error('denier') is-invalid @enderror">
          @error('denier')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Filamen</label>
          <input type="number" name="filamen" value="{{ old('filamen') }}"
            class="form-control @error('filamen') is-invalid @enderror">
          @error('filamen')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Warna PPT</label>
          <input type="text" name="warna_ppt" value="{{ old('warna_ppt') }}" maxlength="20"
            class="form-control @error('warna_ppt') is-invalid @enderror">
          @error('warna_ppt')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.benang.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Tambah Area QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.area.store') }}">
        @csrf
        <div class="form-group">
          <label>Kode Area</label>
          <input type="text" name="id_area" value="{{ old('id_area') }}" maxlength="10"
            class="form-control @error('id_area') is-invalid @enderror" placeholder="Contoh: VIS">
          @error('id_area')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Nama Area</label>
          <input type="text" name="nm_area" value="{{ old('nm_area') }}" maxlength="50"
            class="form-control @error('nm_area') is-invalid @enderror">
          @error('nm_area')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.area.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

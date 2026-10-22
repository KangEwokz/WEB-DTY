@extends('auth::layouts.app')

@section('title', 'Tambah Jenis Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Kode (VS01, VS02, ...) dibuat otomatis.</p>
      <form method="POST" action="{{ route('qct.jenis-abnormal.store') }}">
        @csrf
        <div class="form-group">
          <label>Nama Abnormal</label>
          <input type="text" name="nm_abnormal" value="{{ old('nm_abnormal') }}" maxlength="50"
            class="form-control @error('nm_abnormal') is-invalid @enderror">
          @error('nm_abnormal')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.jenis-abnormal.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

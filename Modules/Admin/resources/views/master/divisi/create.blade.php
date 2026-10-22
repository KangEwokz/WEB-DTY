@extends('auth::layouts.app')

@section('title', 'Tambah Divisi')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.divisi.store') }}">
        @csrf
        <div class="form-group">
          <label>Kode Divisi</label>
          <input type="text" name="kd_divisi" value="{{ old('kd_divisi') }}" maxlength="5"
            class="form-control @error('kd_divisi') is-invalid @enderror" placeholder="Contoh: ADM">
          @error('kd_divisi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Nama Divisi</label>
          <input type="text" name="nm_divisi" value="{{ old('nm_divisi') }}" maxlength="50"
            class="form-control @error('nm_divisi') is-invalid @enderror">
          @error('nm_divisi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.divisi.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

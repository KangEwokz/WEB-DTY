@extends('auth::layouts.app')

@section('title', 'Tambah Grade')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.grade.store') }}">
        @csrf
        <div class="form-group">
          <label>Kode Grade</label>
          <input type="text" name="id_grade" value="{{ old('id_grade') }}" maxlength="5"
            class="form-control @error('id_grade') is-invalid @enderror" placeholder="Contoh: AX">
          @error('id_grade')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" name="keterangan" value="{{ old('keterangan') }}" maxlength="100"
            class="form-control @error('keterangan') is-invalid @enderror">
          @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.grade.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

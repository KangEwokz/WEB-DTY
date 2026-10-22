@extends('auth::layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.jabatan.store') }}">
        @csrf
        <div class="form-group">
          <label>Nama Jabatan</label>
          <input type="text" name="nm_jabatan" value="{{ old('nm_jabatan') }}" maxlength="30"
            class="form-control @error('nm_jabatan') is-invalid @enderror">
          @error('nm_jabatan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Divisi</label>
          <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
            <option value="">- Tanpa divisi (contoh: Direktur) -</option>
            @foreach ($divisi as $d)
              <option value="{{ $d->id_divisi }}" {{ old('id_divisi') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nm_divisi }}</option>
            @endforeach
          </select>
          @error('id_divisi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('admin.jabatan.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

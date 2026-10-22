@extends('auth::layouts.app')

@section('title', 'Ubah Grade')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.grade.update', $grade->id_grade) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Kode Grade</label>
          <input type="text" value="{{ $grade->id_grade }}" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" name="keterangan" value="{{ old('keterangan', $grade->keterangan) }}" maxlength="100"
            class="form-control @error('keterangan') is-invalid @enderror">
          @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ old('status_aktif', $grade->status_aktif) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status_aktif')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.grade.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

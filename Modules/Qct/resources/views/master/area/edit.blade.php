@extends('auth::layouts.app')

@section('title', 'Ubah Area QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.area.update', $area->id_area) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Kode Area</label>
          <input type="text" value="{{ $area->id_area }}" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Nama Area</label>
          <input type="text" name="nm_area" value="{{ old('nm_area', $area->nm_area) }}" maxlength="50"
            class="form-control @error('nm_area') is-invalid @enderror">
          @error('nm_area')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ old('status_aktif', $area->status_aktif) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status_aktif')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.area.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

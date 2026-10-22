@extends('auth::layouts.app')

@section('title', 'Ubah Benang')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('produksi.benang.update', $benang->merge) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Kode Merge</label>
          <input type="text" value="{{ $benang->merge }}" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Denier</label>
          <input type="number" name="denier" value="{{ old('denier', $benang->denier) }}"
            class="form-control @error('denier') is-invalid @enderror">
          @error('denier')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Filamen</label>
          <input type="number" name="filamen" value="{{ old('filamen', $benang->filamen) }}"
            class="form-control @error('filamen') is-invalid @enderror">
          @error('filamen')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Warna PPT</label>
          <input type="text" name="warna_ppt" value="{{ old('warna_ppt', $benang->warna_ppt) }}" maxlength="20"
            class="form-control @error('warna_ppt') is-invalid @enderror">
          @error('warna_ppt')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ old('status_aktif', $benang->status_aktif) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status_aktif')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.benang.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

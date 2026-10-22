@extends('auth::layouts.app')

@section('title', 'Ubah Divisi')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.divisi.update', $divisi->id_divisi) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>ID Divisi</label>
          <input type="text" value="{{ $divisi->id_divisi }}" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Kode Divisi</label>
          <input type="text" name="kd_divisi" value="{{ old('kd_divisi', $divisi->kd_divisi) }}" maxlength="5"
            class="form-control @error('kd_divisi') is-invalid @enderror">
          @error('kd_divisi')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Nama Divisi</label>
          <input type="text" name="nm_divisi" value="{{ old('nm_divisi', $divisi->nm_divisi) }}" maxlength="50"
            class="form-control @error('nm_divisi') is-invalid @enderror">
          <small class="text-muted">Nama divisi Admin, Produksi, dan QCT dipakai untuk hak akses menu. Jangan diganti.</small>
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

@extends('auth::layouts.app')

@section('title', 'Ubah Jenis Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.jenis-abnormal.update', $jenis->id_jns_abn) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Kode</label>
          <input type="text" value="{{ $jenis->id_jns_abn }}" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Nama Abnormal</label>
          <input type="text" name="nm_abnormal" value="{{ old('nm_abnormal', $jenis->nm_abnormal) }}" maxlength="50"
            class="form-control @error('nm_abnormal') is-invalid @enderror">
          @error('nm_abnormal')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ old('status_aktif', $jenis->status_aktif) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status_aktif')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.jenis-abnormal.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

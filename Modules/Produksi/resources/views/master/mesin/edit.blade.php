@extends('auth::layouts.app')

@section('title', 'Ubah Status Mesin')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Mesin <strong>{{ $mesin->kode_mesin }}</strong> (line {{ $mesin->line }}, posisi {{ $mesin->posisi }})</p>
      <form method="POST" action="{{ route('produksi.mesin.update', $mesin->kode_mesin) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control @error('status') is-invalid @enderror">
            @foreach (['Running', 'Stop', 'Maintenance'] as $s)
              <option value="{{ $s }}" {{ old('status', $mesin->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('produksi.mesin.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

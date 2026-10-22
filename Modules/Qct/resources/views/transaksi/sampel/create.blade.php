@extends('auth::layouts.app')

@section('title', 'Tambah Sampel')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">Satu sampel per merge pada setiap tanggal dan shift.</p>
      <form method="POST" action="{{ route('qct.sampel.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-4 form-group">
            <label>Tanggal</label>
            <input type="date" name="tgl_sampel" value="{{ old('tgl_sampel', $sekarang['tanggal']) }}"
              class="form-control @error('tgl_sampel') is-invalid @enderror">
            @error('tgl_sampel')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4 form-group">
            <label>Shift</label>
            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
              @foreach (['S1', 'S2', 'S3'] as $s)
                <option value="{{ $s }}" {{ old('shift', $sekarang['shift']) == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
            @error('shift')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4 form-group">
            <label>Merge</label>
            <select name="merge" class="form-control @error('merge') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($benang as $b)
                <option value="{{ $b->merge }}" {{ old('merge') == $b->merge ? 'selected' : '' }}>{{ $b->merge }} ({{ $b->denier }}/{{ $b->filamen }})</option>
              @endforeach
            </select>
            @error('merge')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        @include('qct::transaksi.sampel.form', ['sampel' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.sampel.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

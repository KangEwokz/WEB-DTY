@extends('auth::layouts.app')

@section('title', 'Tambah Absensi QCT')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('qct.absensi.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Tanggal</label>
            <input type="date" name="tgl_absen" value="{{ old('tgl_absen', $sekarang['tanggal']) }}"
              class="form-control @error('tgl_absen') is-invalid @enderror">
            @error('tgl_absen')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 form-group">
            <label>Shift</label>
            <select name="shift" class="form-control @error('shift') is-invalid @enderror">
              @foreach (['S1', 'S2', 'S3', 'GS'] as $s)
                <option value="{{ $s }}" {{ old('shift', $sekarang['shift']) == $s ? 'selected' : '' }}>{{ $s }}{{ $s == 'GS' ? ' (non-grup)' : '' }}</option>
              @endforeach
            </select>
            @error('shift')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="form-group">
          <label>Karyawan</label>
          <select name="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach ($karyawan as $k)
              <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nama_karyawan }} (grup {{ $k->grup }})</option>
            @endforeach
          </select>
          @error('id_karyawan')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @include('qct::transaksi.absensi.form', ['absensi' => null])
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.absensi.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

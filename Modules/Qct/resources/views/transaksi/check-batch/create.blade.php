@extends('auth::layouts.app')

@section('title', 'Cek Visual Troly')

@section('content')
  <div class="card">
    <div class="card-body">
      <table class="table mb-4">
        <tr>
          <th style="width: 200px">Troly</th>
          <td>{{ $doffing->nmr_troly }} ({{ $doffing->id_doffing }})</td>
        </tr>
        <tr>
          <th>Tanggal / Shift</th>
          <td>{{ date('d-m-Y', strtotime($doffing->tgl_doffing)) }} / {{ $doffing->shift }}</td>
        </tr>
        <tr>
          <th>Merge</th>
          <td>@include('auth::layouts.partials.info-benang', ['baris' => $doffing])</td>
        </tr>
        <tr>
          <th>Jumlah Bobbin</th>
          <td>{{ $doffing->jml_bobbin }}</td>
        </tr>
      </table>

      <form method="POST" action="{{ route('qct.check-batch.store') }}">
        @csrf
        <input type="hidden" name="id_doffing" value="{{ $doffing->id_doffing }}">
        <div class="form-group">
          <label>Hasil Visual</label>
          <select name="hasil_visual" class="form-control @error('hasil_visual') is-invalid @enderror">
            <option value="">- Pilih -</option>
            <option value="OK" {{ old('hasil_visual') == 'OK' ? 'selected' : '' }}>OK</option>
            <option value="NG" {{ old('hasil_visual') == 'NG' ? 'selected' : '' }}>NG (ada bobbin abnormal)</option>
          </select>
          @error('hasil_visual')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          @error('id_doffing')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.check-batch.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

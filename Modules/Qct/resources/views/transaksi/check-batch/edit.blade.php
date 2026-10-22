@extends('auth::layouts.app')

@section('title', 'Ubah Hasil Visual')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        Troly {{ $check->nmr_troly }} ({{ $check->id_doffing }}), {{ date('d-m-Y', strtotime($check->tgl_doffing)) }} shift {{ $check->shift }}
      </p>
      <form method="POST" action="{{ route('qct.check-batch.update', $check->id_check) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Hasil Visual</label>
          <select name="hasil_visual" class="form-control @error('hasil_visual') is-invalid @enderror">
            @foreach (['OK', 'NG'] as $h)
              <option value="{{ $h }}" {{ old('hasil_visual', $check->hasil_visual) == $h ? 'selected' : '' }}>{{ $h }}</option>
            @endforeach
          </select>
          @error('hasil_visual')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.check-batch.show', $check->id_check) }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Cek Denier Troly')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        Troly {{ $check->nmr_troly }} ({{ $check->id_doffing }}), merge {{ $check->merge }},
        {{ date('d-m-Y', strtotime($check->tgl_doffing)) }} shift {{ $check->shift }}. Hasil visual: <strong>{{ $check->hasil_visual }}</strong>
      </p>
      <form method="POST" action="{{ route('qct.check-batch.denier.update', $check->id_check) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Hasil Denier</label>
          <select name="hasil_denier" class="form-control @error('hasil_denier') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach (['OK', 'NG'] as $h)
              <option value="{{ $h }}" {{ old('hasil_denier', $check->hasil_denier) == $h ? 'selected' : '' }}>{{ $h }}</option>
            @endforeach
          </select>
          @error('hasil_denier')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Grade</label>
          <select name="id_grade" class="form-control @error('id_grade') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach ($grade as $g)
              <option value="{{ $g->id_grade }}" {{ old('id_grade', $check->id_grade) == $g->id_grade ? 'selected' : '' }}>{{ $g->id_grade }}</option>
            @endforeach
          </select>
          @error('id_grade')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-group">
          <label>Status Troly</label>
          <select name="status" class="form-control @error('status') is-invalid @enderror">
            <option value="">- Pilih -</option>
            @foreach (['Packing', 'Hold'] as $s)
              <option value="{{ $s }}" {{ old('status', $check->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.check-batch.show', $check->id_check) }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

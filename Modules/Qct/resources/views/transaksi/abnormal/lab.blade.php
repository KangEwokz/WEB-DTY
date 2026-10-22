@extends('auth::layouts.app')

@section('title', 'Uji Lab Bobbin Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        {{ $abnormal->id_abnormal }}: troly {{ $abnormal->nmr_troly }}, merge {{ $abnormal->merge }}, mesin {{ $abnormal->kode_mesin }},
        jenis <strong>{{ $abnormal->nm_abnormal }}</strong>
      </p>
      <form method="POST" action="{{ route('qct.abnormal.lab.update', $abnormal->id_abnormal) }}">
        @csrf
        @method('PUT')
        <div class="row">
          @foreach (['act_denier' => 'Actual Denier', 'elong' => 'Elongation (%)', 'tenacity' => 'Tenacity', 'dti' => 'DTI'] as $kolom => $label)
            <div class="col-md-3 form-group">
              <label>{{ $label }}</label>
              <input type="number" step="0.01" name="{{ $kolom }}" value="{{ old($kolom, $abnormal->$kolom) }}"
                class="form-control @error($kolom) is-invalid @enderror">
              @error($kolom)
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          @endforeach
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Grade</label>
            <select name="id_grade" class="form-control @error('id_grade') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($grade as $g)
                <option value="{{ $g->id_grade }}" {{ old('id_grade', $abnormal->id_grade) == $g->id_grade ? 'selected' : '' }}>{{ $g->id_grade }}</option>
              @endforeach
            </select>
            @error('id_grade')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 form-group">
            <label>Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach (['Packing', 'Cutting'] as $s)
                <option value="{{ $s }}" {{ old('status', $abnormal->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <button type="submit" class="btn btn-primary me-2">Simpan</button>
        <a href="{{ route('qct.abnormal.index') }}" class="btn btn-light">Batal</a>
      </form>
    </div>
  </div>
@endsection

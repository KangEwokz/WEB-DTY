{{-- Hasil uji sampel; dipakai create ($sampel = null) dan edit --}}
<div class="form-group">
  <label>Jenis Abnormal (kosongkan jika sampel OK)</label>
  <select name="id_jns_abn" class="form-control @error('id_jns_abn') is-invalid @enderror">
    <option value="">OK (tidak ada abnormal)</option>
    @foreach ($jenis as $j)
      <option value="{{ $j->id_jns_abn }}" {{ old('id_jns_abn', $sampel->id_jns_abn ?? '') == $j->id_jns_abn ? 'selected' : '' }}>{{ $j->nm_abnormal }}</option>
    @endforeach
  </select>
  @error('id_jns_abn')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="row">
  @foreach (['act_denier' => 'Actual Denier', 'elong' => 'Elongation (%)', 'tenacity' => 'Tenacity', 'dti' => 'DTI'] as $kolom => $label)
    <div class="col-md-3 form-group">
      <label>{{ $label }}</label>
      <input type="number" step="0.01" name="{{ $kolom }}" value="{{ old($kolom, $sampel->$kolom ?? '') }}"
        class="form-control @error($kolom) is-invalid @enderror">
      @error($kolom)
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  @endforeach
</div>
<div class="form-group">
  <label>Grade</label>
  <select name="id_grade" class="form-control @error('id_grade') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($grade as $g)
      <option value="{{ $g->id_grade }}" {{ old('id_grade', $sampel->id_grade ?? '') == $g->id_grade ? 'selected' : '' }}>{{ $g->id_grade }}</option>
    @endforeach
  </select>
  @error('id_grade')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

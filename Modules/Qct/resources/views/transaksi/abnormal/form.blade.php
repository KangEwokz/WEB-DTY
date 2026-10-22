{{-- Bagian visual abnormal: mesin asal dan jenis; dipakai create ($abnormal = null) dan edit --}}
<div class="form-group">
  <label>Mesin Asal</label>
  <select name="kode_mesin" class="form-control @error('kode_mesin') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($mesin as $m)
      <option value="{{ $m->kode_mesin }}" {{ old('kode_mesin', $abnormal->kode_mesin ?? '') == $m->kode_mesin ? 'selected' : '' }}>{{ $m->kode_mesin }}</option>
    @endforeach
  </select>
  @error('kode_mesin')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Jenis Abnormal</label>
  <select name="id_jns_abn" class="form-control @error('id_jns_abn') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($jenis as $j)
      <option value="{{ $j->id_jns_abn }}" {{ old('id_jns_abn', $abnormal->id_jns_abn ?? '') == $j->id_jns_abn ? 'selected' : '' }}>{{ $j->nm_abnormal }}</option>
    @endforeach
  </select>
  @error('id_jns_abn')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

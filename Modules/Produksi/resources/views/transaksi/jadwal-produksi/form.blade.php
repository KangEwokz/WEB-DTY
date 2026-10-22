{{-- Isian form jadwal produksi, dipakai create ($jadwal = null) dan edit --}}
<div class="form-group">
  <label>Tanggal Produksi</label>
  <input type="date" name="tgl_produksi" value="{{ old('tgl_produksi', $jadwal->tgl_produksi ?? date('Y-m-d')) }}"
    class="form-control @error('tgl_produksi') is-invalid @enderror">
  @error('tgl_produksi')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Merge</label>
  <select name="merge" class="form-control @error('merge') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($benang as $b)
      <option value="{{ $b->merge }}" {{ old('merge', $jadwal->merge ?? '') == $b->merge ? 'selected' : '' }}>{{ $b->merge }} ({{ $b->denier }}/{{ $b->filamen }})</option>
    @endforeach
  </select>
  @error('merge')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Line</label>
  <select name="line" class="form-control @error('line') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ([401, 402, 403] as $l)
      <option value="{{ $l }}" {{ old('line', $jadwal->line ?? '') == $l ? 'selected' : '' }}>{{ $l }}</option>
    @endforeach
  </select>
  @error('line')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="row">
  <div class="col-md-6 form-group">
    <label>Posisi Awal</label>
    <input type="number" name="pos_awal" min="1" max="10" value="{{ old('pos_awal', $jadwal->pos_awal ?? 1) }}"
      class="form-control @error('pos_awal') is-invalid @enderror">
    @error('pos_awal')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
  <div class="col-md-6 form-group">
    <label>Posisi Akhir</label>
    <input type="number" name="pos_akhir" min="1" max="10" value="{{ old('pos_akhir', $jadwal->pos_akhir ?? 10) }}"
      class="form-control @error('pos_akhir') is-invalid @enderror">
    @error('pos_akhir')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>
<div class="form-group">
  <label>Customer</label>
  <input type="text" name="customer" value="{{ old('customer', $jadwal->customer ?? '') }}" maxlength="100"
    class="form-control @error('customer') is-invalid @enderror">
  @error('customer')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

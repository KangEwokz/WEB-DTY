{{-- Isian troly, dipakai create dan edit. Tanggal & shift ada di masing-masing halaman. --}}
<div class="form-group">
  <label>Nomor Troly</label>
  <input type="text" name="nmr_troly" value="{{ old('nmr_troly', $doffing->nmr_troly ?? '') }}" maxlength="10"
    class="form-control @error('nmr_troly') is-invalid @enderror">
  @error('nmr_troly')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Merge</label>
  <select name="merge" class="form-control @error('merge') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($benang as $b)
      <option value="{{ $b->merge }}" {{ old('merge', $doffing->merge ?? '') == $b->merge ? 'selected' : '' }}>{{ $b->merge }} ({{ $b->denier }}/{{ $b->filamen }})</option>
    @endforeach
  </select>
  @error('merge')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="row">
  <div class="col-md-6 form-group">
    <label>Jumlah Bobbin</label>
    <input type="number" name="jml_bobbin" min="1" value="{{ old('jml_bobbin', $doffing->jml_bobbin ?? '') }}"
      class="form-control @error('jml_bobbin') is-invalid @enderror">
    @error('jml_bobbin')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
  <div class="col-md-6 form-group">
    <label>Berat Troly (kg, boleh kosong)</label>
    <input type="number" step="0.01" name="berat_troly" value="{{ old('berat_troly', $doffing->berat_troly ?? '') }}"
      class="form-control @error('berat_troly') is-invalid @enderror">
    @error('berat_troly')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

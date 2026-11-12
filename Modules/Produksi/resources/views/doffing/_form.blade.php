<div class="row"> {{-- isian doffing, dipakai oleh create & edit lewat @include; $doffing = null saat tambah --}}
  <div class="col-md-4 mb-3">
    <label class="form-label">Troly <span class="text-danger">*</span></label>
    <select name="kode_troly" class="form-control @error('kode_troly') is-invalid @enderror">
      <option value="">- Pilih -</option>
      @foreach ($pilihan['troly'] as $t) {{-- hanya troly berjenis Normal --}}
        <option value="{{ $t->kode_troly }}" {{ old('kode_troly', $doffing->kode_troly ?? '') == $t->kode_troly ? 'selected' : '' }}>{{ $t->kode_troly }} (kapasitas {{ $t->kapasitas }})</option>
      @endforeach
    </select>
    @error('kode_troly') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- termasuk pesan "sudah dipakai di shift ini" --}}
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Merge <span class="text-danger">*</span></label>
    <select name="merge" class="form-control @error('merge') is-invalid @enderror">
      <option value="">- Pilih -</option>
      @foreach ($pilihan['benang'] as $b)
        <option value="{{ $b->merge }}" {{ old('merge', $doffing->merge ?? '') == $b->merge ? 'selected' : '' }}>{{ $b->merge }} ({{ $b->denier }}D/{{ $b->filamen }}F)</option> {{-- contoh: M150A (150D/48F) --}}
      @endforeach
    </select>
    @error('merge') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label">Jumlah Bobbin <span class="text-danger">*</span></label>
    <input type="number" name="jml_bobbin" value="{{ old('jml_bobbin', $doffing->jml_bobbin ?? '') }}" min="1" placeholder="cth: 20" class="form-control @error('jml_bobbin') is-invalid @enderror">
    @error('jml_bobbin') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- termasuk pesan melebihi kapasitas --}}
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label">Berat <span class="text-danger">*</span></label>
    <div class="input-group has-validation"> {{-- input + satuan kg --}}
      <input type="number" name="berat_troly" value="{{ old('berat_troly', $doffing->berat_troly ?? '') }}" min="0.01" step="0.01" class="form-control @error('berat_troly') is-invalid @enderror"> {{-- step 0.01 = 2 angka di belakang koma --}}
      <span class="input-group-text">kg</span>
      @error('berat_troly') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
  <div class="col-md-12 mb-3">
    <label class="form-label">Keterangan</label>
    <input type="text" name="keterangan" value="{{ old('keterangan', $doffing->keterangan ?? '') }}" maxlength="100" placeholder="opsional" class="form-control @error('keterangan') is-invalid @enderror">
    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
</div>

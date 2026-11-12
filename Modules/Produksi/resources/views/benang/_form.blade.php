<div class="row"> {{-- isian benang, dipakai oleh create & edit lewat @include; $benang = null saat tambah --}}
  <div class="col-md-4 mb-3">
    <label class="form-label">Kode Merge <span class="text-danger">*</span></label>
    @if ($benang) {{-- saat ubah: kode merge hanya ditampilkan (tidak bisa diubah) --}}
      <input type="text" value="{{ $benang->merge }}" class="form-control" disabled> {{-- disabled = tidak bisa diketik & tidak ikut terkirim --}}
    @else {{-- saat tambah: kode merge diketik --}}
      <input type="text" name="merge" value="{{ old('merge') }}" maxlength="10" placeholder="contoh: M150A" class="form-control @error('merge') is-invalid @enderror">
      @error('merge') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @endif
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Denier <span class="text-danger">*</span></label>
    <input type="number" name="denier" value="{{ old('denier', $benang->denier ?? '') }}" min="1" placeholder="contoh: 150" class="form-control @error('denier') is-invalid @enderror">
    @error('denier') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Filamen <span class="text-danger">*</span></label>
    <input type="number" name="filamen" value="{{ old('filamen', $benang->filamen ?? '') }}" min="1" placeholder="contoh: 48" class="form-control @error('filamen') is-invalid @enderror">
    @error('filamen') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Toleransi Denier <span class="text-danger">*</span></label>
    <div class="input-group has-validation"> {{-- input + tanda % menempel di kanan --}}
      <input type="number" name="toleransi_denier" value="{{ old('toleransi_denier', $benang->toleransi_denier ?? '3.00') }}" min="0.01" max="50" step="0.01" class="form-control @error('toleransi_denier') is-invalid @enderror"> {{-- bawaan 3.00 --}}
      <span class="input-group-text">%</span>
      @error('toleransi_denier') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <small class="text-muted">Denier aktual dianggap OK jika selisihnya maksimal sebesar persen ini.</small>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Warna PPT</label>
    <input type="text" name="warna_ppt" value="{{ old('warna_ppt', $benang->warna_ppt ?? '') }}" maxlength="20" placeholder="contoh: Merah (opsional)" class="form-control @error('warna_ppt') is-invalid @enderror"> {{-- warna kertas penanda merge di troly --}}
    @error('warna_ppt') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
</div>

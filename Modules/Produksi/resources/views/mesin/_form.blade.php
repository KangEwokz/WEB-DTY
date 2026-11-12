<div class="row"> {{-- isian mesin, dipakai oleh create & edit lewat @include; $mesin = null saat tambah --}}
  <div class="col-md-4 mb-3">
    <label class="form-label">Line <span class="text-danger">*</span></label>
    <select name="line" class="form-control @error('line') is-invalid @enderror">
      <option value="">- Pilih -</option>
      @foreach (['L1', 'L2', 'L3'] as $l) {{-- daftar line --}}
        <option value="{{ $l }}" {{ old('line', $mesin->line ?? '') == $l ? 'selected' : '' }}>{{ $l }}</option> {{-- terpilih = line sekarang (saat ubah) --}}
      @endforeach
    </select>
    @error('line') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Posisi <span class="text-danger">*</span></label>
    <input type="number" name="posisi" value="{{ old('posisi', $mesin->posisi ?? '') }}" min="1" max="99" placeholder="contoh: 3" class="form-control @error('posisi') is-invalid @enderror">
    @error('posisi') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- termasuk pesan jika posisi sudah ditempati mesin lain --}}
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Status <span class="text-danger">*</span></label>
    <select name="status" class="form-control @error('status') is-invalid @enderror">
      @foreach (['Running', 'Stop', 'Maintenance'] as $s) {{-- pilihan status --}}
        <option value="{{ $s }}" {{ old('status', $mesin->status ?? 'Running') == $s ? 'selected' : '' }}>{{ $s }}</option> {{-- bawaan Running saat tambah --}}
      @endforeach
    </select>
    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
</div>

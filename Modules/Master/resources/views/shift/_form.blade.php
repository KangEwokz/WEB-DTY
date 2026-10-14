{{-- Form shift, dipakai oleh create dan edit. $shift bernilai null saat create. --}}
<div class="form-group">
  <label for="kode_shift">Kode Shift</label>
  <input type="text" name="kode_shift" id="kode_shift" value="{{ old('kode_shift', $shift->kode_shift ?? '') }}" class="form-control @error('kode_shift') is-invalid @enderror" maxlength="10" placeholder="Contoh: S1" required>
  @error('kode_shift')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="nama_shift">Nama Shift</label>
  <input type="text" name="nama_shift" id="nama_shift" value="{{ old('nama_shift', $shift->nama_shift ?? '') }}" class="form-control @error('nama_shift') is-invalid @enderror" maxlength="50" placeholder="Contoh: Shift Pagi" required>
  @error('nama_shift')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="jam_masuk">Jam Masuk</label>
  <input type="time" name="jam_masuk" id="jam_masuk" value="{{ old('jam_masuk', isset($shift->jam_masuk) ? substr($shift->jam_masuk, 0, 5) : '') }}" class="form-control @error('jam_masuk') is-invalid @enderror">
  <small class="text-muted">Kosongkan jika shift libur.</small>
  @error('jam_masuk')
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="jam_pulang">Jam Pulang</label>
  <input type="time" name="jam_pulang" id="jam_pulang" value="{{ old('jam_pulang', isset($shift->jam_pulang) ? substr($shift->jam_pulang, 0, 5) : '') }}" class="form-control @error('jam_pulang') is-invalid @enderror">
  <small class="text-muted">Kosongkan jika shift libur.</small>
  @error('jam_pulang')
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>

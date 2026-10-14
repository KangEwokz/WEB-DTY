{{-- Form jabatan, dipakai oleh create dan edit. $jabatan bernilai null saat create. --}}
<div class="form-group">
  <label for="id_jabatan">Kode Jabatan</label>
  <input type="text" name="id_jabatan" id="id_jabatan" value="{{ old('id_jabatan', $jabatan->id_jabatan ?? '') }}" class="form-control @error('id_jabatan') is-invalid @enderror" maxlength="10" placeholder="Contoh: M1 (paling tinggi)" required>
  @error('id_jabatan')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="nm_jabatan">Nama Jabatan</label>
  <input type="text" name="nm_jabatan" id="nm_jabatan" value="{{ old('nm_jabatan', $jabatan->nm_jabatan ?? '') }}" class="form-control @error('nm_jabatan') is-invalid @enderror" maxlength="50" placeholder="Contoh: Manager" required>
  @error('nm_jabatan')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

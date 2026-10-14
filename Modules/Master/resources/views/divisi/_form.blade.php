{{-- Form divisi, dipakai oleh create dan edit. $divisi bernilai null saat create. --}}
<div class="form-group">
  <label for="id_divisi">Kode Divisi</label>
  <input type="text" name="id_divisi" id="id_divisi" value="{{ old('id_divisi', $divisi->id_divisi ?? '') }}" class="form-control @error('id_divisi') is-invalid @enderror" maxlength="10" placeholder="Contoh: ADM" required>
  @error('id_divisi')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="nm_divisi">Nama Divisi</label>
  <input type="text" name="nm_divisi" id="nm_divisi" value="{{ old('nm_divisi', $divisi->nm_divisi ?? '') }}" class="form-control @error('nm_divisi') is-invalid @enderror" maxlength="20" placeholder="Contoh: Admin" required>
  @error('nm_divisi')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Form grup, dipakai oleh create dan edit. $grup bernilai null saat create. --}}
<div class="form-group">
  <label for="id_grup">Kode Grup</label>
  <input type="text" name="id_grup" id="id_grup" value="{{ old('id_grup', $grup->id_grup ?? '') }}" class="form-control @error('id_grup') is-invalid @enderror" maxlength="10" placeholder="Contoh: A" required>
  @error('id_grup')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="nm_grup">Nama Grup</label>
  <input type="text" name="nm_grup" id="nm_grup" value="{{ old('nm_grup', $grup->nm_grup ?? '') }}" class="form-control @error('nm_grup') is-invalid @enderror" maxlength="20" placeholder="Contoh: Senin" required>
  @error('nm_grup')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

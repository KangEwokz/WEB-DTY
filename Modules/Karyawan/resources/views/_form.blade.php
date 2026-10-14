{{-- Form karyawan, dipakai oleh create dan edit. $karyawan bernilai null saat create. --}}
<div class="form-group">
  <label for="id_karyawan">ID Karyawan</label>
  <input type="text" name="id_karyawan" id="id_karyawan" value="{{ old('id_karyawan', $karyawan->id_karyawan ?? '') }}" class="form-control @error('id_karyawan') is-invalid @enderror" maxlength="20" placeholder="Contoh: DTY-0006" required>
  @error('id_karyawan')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="nama_karyawan">Nama Karyawan</label>
  <input type="text" name="nama_karyawan" id="nama_karyawan" value="{{ old('nama_karyawan', $karyawan->nama_karyawan ?? '') }}" class="form-control @error('nama_karyawan') is-invalid @enderror" maxlength="50" placeholder="Nama lengkap tanpa gelar" required>
  @error('nama_karyawan')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="jns_kelamin">Jenis Kelamin</label>
  <select name="jns_kelamin" id="jns_kelamin" class="form-select @error('jns_kelamin') is-invalid @enderror" required>
    <option value="">-- Pilih jenis kelamin --</option>
    <option value="L" {{ old('jns_kelamin', $karyawan->jns_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
    <option value="P" {{ old('jns_kelamin', $karyawan->jns_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
  </select>
  @error('jns_kelamin')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="departemen">Departemen</label>
  <input type="text" name="departemen" id="departemen" value="DTY" class="form-control @error('departemen') is-invalid @enderror" readonly>
  @error('departemen')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="id_divisi">Divisi</label>
  <select name="id_divisi" id="id_divisi" class="form-select @error('id_divisi') is-invalid @enderror" required>
    <option value="">-- Pilih divisi --</option>
    @foreach ($divisi as $d)
      <option value="{{ $d->id_divisi }}" {{ old('id_divisi', $karyawan->id_divisi ?? '') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nm_divisi }}</option>
    @endforeach
  </select>
  @error('id_divisi')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="id_jabatan">Jabatan</label>
  <select name="id_jabatan" id="id_jabatan" class="form-select @error('id_jabatan') is-invalid @enderror" required>
    <option value="">-- Pilih jabatan --</option>
    @foreach ($jabatan as $j)
      <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $karyawan->id_jabatan ?? '') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->id_jabatan }} - {{ $j->nm_jabatan }}</option>
    @endforeach
  </select>
  @error('id_jabatan')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label for="id_grup">Grup</label>
  <select name="id_grup" id="id_grup" class="form-select @error('id_grup') is-invalid @enderror" required>
    <option value="">-- Pilih grup --</option>
    @foreach ($grup as $g)
      <option value="{{ $g->id_grup }}" {{ old('id_grup', $karyawan->id_grup ?? '') == $g->id_grup ? 'selected' : '' }}>{{ $g->id_grup }} - {{ $g->nm_grup }}</option>
    @endforeach
  </select>
  @error('id_grup')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

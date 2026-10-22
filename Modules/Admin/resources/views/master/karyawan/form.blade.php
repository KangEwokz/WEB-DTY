{{-- Isian form karyawan, dipakai create.blade.php ($karyawan = null) dan edit.blade.php --}}
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Nama Karyawan</label>
      <input type="text" name="nama_karyawan" value="{{ old('nama_karyawan', $karyawan->nama_karyawan ?? '') }}" maxlength="50"
        class="form-control @error('nama_karyawan') is-invalid @enderror">
      @error('nama_karyawan')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Jenis Kelamin</label>
      <select name="jns_kelamin" class="form-control @error('jns_kelamin') is-invalid @enderror">
        <option value="">- Pilih -</option>
        <option value="L" {{ old('jns_kelamin', $karyawan->jns_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
        <option value="P" {{ old('jns_kelamin', $karyawan->jns_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
      </select>
      @error('jns_kelamin')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Tanggal Masuk</label>
      <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk', $karyawan->tgl_masuk ?? '') }}"
        class="form-control @error('tgl_masuk') is-invalid @enderror">
      @error('tgl_masuk')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>No. HP</label>
      <input type="text" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp ?? '') }}" maxlength="15"
        class="form-control @error('no_hp') is-invalid @enderror">
      @error('no_hp')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email', $karyawan->email ?? '') }}" maxlength="100"
        class="form-control @error('email') is-invalid @enderror">
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label>Divisi</label>
      <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
        <option value="">- Tanpa divisi (Direktur) -</option>
        @foreach ($divisi as $d)
          <option value="{{ $d->id_divisi }}" {{ old('id_divisi', $karyawan->id_divisi ?? '') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nm_divisi }}</option>
        @endforeach
      </select>
      @error('id_divisi')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Jabatan</label>
      <select name="id_jabatan" class="form-control @error('id_jabatan') is-invalid @enderror">
        <option value="">- Pilih -</option>
        @foreach ($jabatan as $j)
          <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $karyawan->id_jabatan ?? '') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nm_jabatan }}</option>
        @endforeach
      </select>
      @error('id_jabatan')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Area Default</label>
      <select name="id_area" class="form-control @error('id_area') is-invalid @enderror">
        <option value="">- Tanpa area -</option>
        @foreach ($area as $a)
          <option value="{{ $a->id_area }}" {{ old('id_area', $karyawan->id_area ?? '') == $a->id_area ? 'selected' : '' }}>{{ $a->id_area }} - {{ $a->nm_area }}</option>
        @endforeach
      </select>
      @error('id_area')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="form-group">
      <label>Grup</label>
      <select name="grup" class="form-control @error('grup') is-invalid @enderror">
        <option value="">- Pilih -</option>
        @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'NG'] as $g)
          <option value="{{ $g }}" {{ old('grup', $karyawan->grup ?? '') == $g ? 'selected' : '' }}>{{ $g == 'NG' ? 'NG (non-grup)' : $g }}</option>
        @endforeach
      </select>
      @error('grup')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    @if ($karyawan)
      <div class="form-group">
        <label>Status</label>
        <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
          @foreach (['Aktif', 'Non-Aktif'] as $s)
            <option value="{{ $s }}" {{ old('status_aktif', $karyawan->status_aktif) == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        @error('status_aktif')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    @endif
  </div>
</div>

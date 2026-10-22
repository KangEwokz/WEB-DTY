{{-- Kehadiran, area tugas, dan dokumen; dipakai create ($absensi = null) dan edit --}}
<div class="form-group">
  <label>Kehadiran</label>
  <select name="kehadiran" class="form-control @error('kehadiran') is-invalid @enderror">
    @foreach (['Hadir', 'Sakit', 'Ijin', 'Alfa', 'Cuti'] as $h)
      <option value="{{ $h }}" {{ old('kehadiran', $absensi->kehadiran ?? 'Hadir') == $h ? 'selected' : '' }}>{{ $h }}</option>
    @endforeach
  </select>
  @error('kehadiran')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Area Tugas (wajib jika Hadir)</label>
  <select name="id_area" class="form-control @error('id_area') is-invalid @enderror">
    <option value="">- Pilih -</option>
    @foreach ($area as $a)
      <option value="{{ $a->id_area }}" {{ old('id_area', $absensi->id_area ?? '') == $a->id_area ? 'selected' : '' }}>{{ $a->id_area }} - {{ $a->nm_area }}</option>
    @endforeach
  </select>
  @error('id_area')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
<div class="form-group">
  <label>Surat Sakit / Ijin (PDF/JPG/PNG, maks 2 MB, boleh kosong)</label>
  <input type="file" name="dok_file" class="form-control @error('dok_file') is-invalid @enderror">
  @if ($absensi && $absensi->dok_file)
    <small class="text-muted">File sekarang: <a href="{{ asset('storage/'.$absensi->dok_file) }}" target="_blank">lihat</a>. Pilih file baru untuk mengganti.</small>
  @endif
  @error('dok_file')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

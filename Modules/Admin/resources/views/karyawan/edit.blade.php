@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Ubah Karyawan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4"> {{-- baris judul --}}
    <h4 class="font-weight-bold mb-1">Ubah Karyawan</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID Karyawan: <b>{{ $karyawan->id_karyawan }}</b> (tidak bisa diubah)</p> {{-- id ditampilkan saja, tidak ada inputnya --}}
  </div>

  <div class="card"> {{-- kotak kartu --}}
    <div class="card-body"> {{-- isi kartu --}}
      <form action="{{ route('admin.karyawan.update', $karyawan->id_karyawan) }}" method="POST" enctype="multipart/form-data"> {{-- kirim ke route update; enctype multipart = wajib jika form mengirim file --}}
        @csrf {{-- token keamanan --}}
        @method('PUT') {{-- form HTML hanya bisa GET/POST, jadi ditandai sebagai PUT agar cocok dengan route update --}}

        <p class="card-title">Data Pribadi</p> {{-- judul kelompok --}}
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
            <input type="text" name="nama_karyawan" value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}" class="form-control @error('nama_karyawan') is-invalid @enderror"> {{-- old('x', data lama) = isi dari input sebelumnya, jika tidak ada pakai data dari database --}}
            @error('nama_karyawan') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- pesan error di bawah input --}}
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jns_kelamin" class="form-control @error('jns_kelamin') is-invalid @enderror">
              <option value="L" {{ old('jns_kelamin', $karyawan->jns_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option> {{-- selected = terpilih jika nilainya L --}}
              <option value="P" {{ old('jns_kelamin', $karyawan->jns_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jns_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk', $karyawan->tgl_masuk) }}" class="form-control @error('tgl_masuk') is-invalid @enderror">
            @error('tgl_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" class="form-control @error('no_hp') is-invalid @enderror">
            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <p class="card-title mt-2">Posisi di Organisasi</p> {{-- judul kelompok --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Departemen</label>
            <select name="id_departemen" class="form-control @error('id_departemen') is-invalid @enderror">
              <option value="">- Tidak ada -</option>
              @foreach ($departemen as $d) {{-- ulangi setiap departemen dari controller --}}
                <option value="{{ $d->id_departemen }}" {{ old('id_departemen', $karyawan->id_departemen) == $d->id_departemen ? 'selected' : '' }}>{{ $d->kd_departemen }} - {{ $d->nm_departemen }}</option>
              @endforeach
            </select>
            @error('id_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Divisi</label>
            <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
              <option value="">- Tidak ada -</option>
              @foreach ($divisi as $dv)
                <option value="{{ $dv->id_divisi }}" {{ old('id_divisi', $karyawan->id_divisi) == $dv->id_divisi ? 'selected' : '' }}>{{ $dv->nm_divisi }}</option>
              @endforeach
            </select>
            @error('id_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
            <select name="id_jabatan" class="form-control @error('id_jabatan') is-invalid @enderror">
              @foreach ($jabatan as $j)
                <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $karyawan->id_jabatan) == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nm_jabatan }}</option>
              @endforeach
            </select>
            @error('id_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Area</label>
            <select name="id_area" class="form-control @error('id_area') is-invalid @enderror">
              <option value="">- Tidak ada -</option>
              @foreach ($area as $a)
                <option value="{{ $a->id_area }}" {{ old('id_area', $karyawan->id_area) == $a->id_area ? 'selected' : '' }}>{{ $a->id_area }} - {{ $a->nm_area }}</option>
              @endforeach
            </select>
            @error('id_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Grup <span class="text-danger">*</span></label>
            <select name="grup" class="form-control @error('grup') is-invalid @enderror">
              @foreach (['NG', 'A', 'B', 'C', 'D', 'E', 'F', 'G'] as $g) {{-- daftar grup ditulis langsung --}}
                <option value="{{ $g }}" {{ old('grup', $karyawan->grup) == $g ? 'selected' : '' }}>{{ $g == 'NG' ? 'NG (Non-Grup)' : 'Grup '.$g }}</option>
              @endforeach
            </select>
            @error('grup') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status_aktif" class="form-control @error('status_aktif') is-invalid @enderror">
              <option value="Aktif" {{ old('status_aktif', $karyawan->status_aktif) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
              <option value="Non-Aktif" {{ old('status_aktif', $karyawan->status_aktif) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option> {{-- Non-Aktif = karyawan tidak bisa login --}}
            </select>
            @error('status_aktif') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <p class="card-title mt-2">Foto</p> {{-- judul kelompok --}}
        <div class="d-flex align-items-center mb-3"> {{-- foto lama + input berjajar --}}
          <img src="{{ $karyawan->foto ? asset('uploads/'.$karyawan->foto) : asset('images/foto-default.svg') }}" alt="foto" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover;" class="me-4"> {{-- foto saat ini atau placeholder --}}
          <div class="flex-grow-1"> {{-- flex-grow-1 = mengisi sisa lebar --}}
            <input type="file" name="foto" accept="image/*" class="form-control @error('foto') is-invalid @enderror"> {{-- pilih foto baru --}}
            <small class="text-muted">Pilih file hanya jika ingin mengganti foto. Format gambar, maksimal 2 MB.</small> {{-- keterangan --}}
            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="d-flex gap-2 mt-3"> {{-- baris tombol --}}
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button> {{-- kirim form --}}
          <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Batal</a> {{-- kembali tanpa menyimpan --}}
        </div>
      </form> {{-- akhir form --}}
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

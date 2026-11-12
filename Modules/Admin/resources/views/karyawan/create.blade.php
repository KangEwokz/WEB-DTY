@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Tambah Karyawan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4"> {{-- baris judul --}}
    <h4 class="font-weight-bold mb-1">Tambah Karyawan</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">ID karyawan dibuat otomatis dari tahun masuk, contoh 2019-0004.</p> {{-- keterangan kecil --}}
  </div>

  <div class="card"> {{-- kotak kartu --}}
    <div class="card-body"> {{-- isi kartu --}}
      <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data"> {{-- kirim ke route store; enctype multipart = wajib jika form mengirim file (foto) --}}
        @csrf {{-- token keamanan --}}

        <p class="card-title">Data Pribadi</p> {{-- judul kelompok --}}
        <div class="row"> {{-- baris berisi beberapa kolom --}}
          <div class="col-md-6 mb-3"> {{-- kolom setengah lebar --}}
            <label class="form-label">Nama Karyawan <span class="text-danger">*</span></label> {{-- tanda * merah = wajib diisi --}}
            <input type="text" name="nama_karyawan" value="{{ old('nama_karyawan') }}" class="form-control @error('nama_karyawan') is-invalid @enderror"> {{-- old() = isi sebelumnya tetap ada jika gagal; is-invalid = garis merah jika error --}}
            @error('nama_karyawan') <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- pesan error di bawah input --}}
          </div>
          <div class="col-md-3 mb-3"> {{-- kolom 1/4 lebar --}}
            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jns_kelamin" class="form-control @error('jns_kelamin') is-invalid @enderror"> {{-- pilihan --}}
              <option value="">- Pilih -</option> {{-- pilihan kosong --}}
              <option value="L" {{ old('jns_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option> {{-- selected jika sebelumnya memilih L --}}
              <option value="P" {{ old('jns_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jns_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk') }}" class="form-control @error('tgl_masuk') is-invalid @enderror"> {{-- type="date" = muncul kalender --}}
            @error('tgl_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">No. HP</label> {{-- tanpa * = boleh kosong --}}
            <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror">
            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <p class="card-title mt-2">Posisi di Organisasi</p> {{-- judul kelompok --}}
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Departemen</label>
            <select name="id_departemen" class="form-control @error('id_departemen') is-invalid @enderror">
              <option value="">- Tidak ada -</option> {{-- kosong, untuk Direktur --}}
              @foreach ($departemen as $d) {{-- ulangi setiap departemen dari controller --}}
                <option value="{{ $d->id_departemen }}" {{ old('id_departemen') == $d->id_departemen ? 'selected' : '' }}>{{ $d->kd_departemen }} - {{ $d->nm_departemen }}</option> {{-- value = yang dikirim, teks = yang dilihat --}}
              @endforeach {{-- akhir perulangan --}}
            </select>
            @error('id_departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Divisi</label>
            <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
              <option value="">- Tidak ada -</option> {{-- kosong, untuk Direktur/HOD/Admin departemen --}}
              @foreach ($divisi as $dv)
                <option value="{{ $dv->id_divisi }}" {{ old('id_divisi') == $dv->id_divisi ? 'selected' : '' }}>{{ $dv->nm_divisi }}</option>
              @endforeach
            </select>
            @error('id_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
            <select name="id_jabatan" class="form-control @error('id_jabatan') is-invalid @enderror">
              <option value="">- Pilih -</option>
              @foreach ($jabatan as $j)
                <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nm_jabatan }}</option>
              @endforeach
            </select>
            @error('id_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Area</label>
            <select name="id_area" class="form-control @error('id_area') is-invalid @enderror">
              <option value="">- Tidak ada -</option> {{-- kosong, untuk selain operator --}}
              @foreach ($area as $a)
                <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>{{ $a->id_area }} - {{ $a->nm_area }}</option>
              @endforeach
            </select>
            @error('id_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Grup <span class="text-danger">*</span></label>
            <select name="grup" class="form-control @error('grup') is-invalid @enderror">
              @foreach (['NG', 'A', 'B', 'C', 'D', 'E', 'F', 'G'] as $g) {{-- daftar grup ditulis langsung --}}
                <option value="{{ $g }}" {{ old('grup', 'NG') == $g ? 'selected' : '' }}>{{ $g == 'NG' ? 'NG (Non-Grup)' : 'Grup '.$g }}</option> {{-- old('grup', 'NG') = bawaannya NG --}}
              @endforeach
            </select>
            @error('grup') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Foto</label>
            <input type="file" name="foto" accept="image/*" class="form-control @error('foto') is-invalid @enderror"> {{-- accept="image/*" = hanya bisa memilih file gambar --}}
            <small class="text-muted">Format gambar, maksimal 2 MB. Boleh dikosongkan.</small> {{-- keterangan --}}
            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="d-flex gap-2 mt-3"> {{-- baris tombol; gap-2 = jarak antar tombol --}}
          <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan</button> {{-- kirim form --}}
          <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Batal</a> {{-- kembali ke daftar tanpa menyimpan --}}
        </div>
      </form> {{-- akhir form --}}
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

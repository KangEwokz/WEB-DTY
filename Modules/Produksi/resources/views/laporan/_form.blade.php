<p class="card-title">Output Produksi</p> {{-- isian angka laporan, dipakai oleh create & edit lewat @include; $laporan = null saat tambah --}}
<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Jumlah Doffing</label>
    <div class="input-group"> {{-- input-group = kotak + tulisan satuan menempel di kanan --}}
      <input type="text" value="{{ $laporan ? $laporan->jml_doffing : 'otomatis' }}" class="form-control" disabled> {{-- disabled = hanya tampilan, tidak bisa diisi & tidak terkirim --}}
      <span class="input-group-text">troly</span>
    </div>
    <small class="text-muted">Dihitung otomatis dari menu Doffing (jumlah troly di tanggal & shift ini).</small>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Jumlah Break <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
      <input type="number" name="jml_break" value="{{ old('jml_break', $laporan->jml_break ?? 0) }}" min="0" class="form-control @error('jml_break') is-invalid @enderror">
      <span class="input-group-text">putus</span> {{-- jumlah benang putus --}}
      @error('jml_break') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Waste <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
      <input type="number" name="waste" value="{{ old('waste', $laporan->waste ?? 0) }}" min="0" step="0.01" class="form-control @error('waste') is-invalid @enderror"> {{-- step 0.01 = boleh 2 angka di belakang koma --}}
      <span class="input-group-text">kg</span>
      @error('waste') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Bleeding Time <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
      <input type="number" name="bleeding_time" value="{{ old('bleeding_time', $laporan->bleeding_time ?? 0) }}" min="0" class="form-control @error('bleeding_time') is-invalid @enderror">
      <span class="input-group-text">menit</span>
      @error('bleeding_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Suspend Time <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
      <input type="number" name="suspend_time" value="{{ old('suspend_time', $laporan->suspend_time ?? 0) }}" min="0" class="form-control @error('suspend_time') is-invalid @enderror">
      <span class="input-group-text">menit</span>
      @error('suspend_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

<p class="card-title mt-2">Problem Mesin</p>
<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Jumlah Problem <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
      <input type="number" name="jml_problem_mesin" value="{{ old('jml_problem_mesin', $laporan->jml_problem_mesin ?? 0) }}" min="0" class="form-control @error('jml_problem_mesin') is-invalid @enderror">
      <span class="input-group-text">kejadian</span>
      @error('jml_problem_mesin') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
  <div class="col-md-8 mb-3">
    <label class="form-label">Keterangan Problem</label>
    <textarea name="ket_problem_mesin" rows="3" class="form-control @error('ket_problem_mesin') is-invalid @enderror" placeholder="contoh: Mesin 401-03 heater error 20 menit">{{ old('ket_problem_mesin', $laporan->ket_problem_mesin ?? '') }}</textarea> {{-- textarea = kotak teks beberapa baris --}}
    @error('ket_problem_mesin') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Catatan Supervisor</label>
  <textarea name="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror" placeholder="opsional">{{ old('catatan', $laporan->catatan ?? '') }}</textarea>
  @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<p class="card-title mt-4">Temuan Benang Abnormal: <span id="total-abnormal">0</span> bobbin</p> {{-- angka total diisi JavaScript --}}
<p class="text-muted small">Catat satu baris untuk setiap bobbin abnormal. Totalnya otomatis tersimpan di laporan ini.</p>

@error('abnormal') <div class="alert alert-danger">{{ $message }}</div> @enderror {{-- pesan kapasitas troly penuh --}}
@if ($errors->has('abnormal.*')) {{-- jika ada baris yang salah --}}
  <div class="alert alert-danger mb-3">
    @foreach ($errors->get('abnormal.*') as $pesan) {{-- ulangi setiap pesan error baris --}}
      <div>{{ $pesan[0] }}</div>
    @endforeach
  </div>
@endif

<div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th style="width: 40px;">No</th>
        <th style="min-width: 170px;">Mesin Asal</th>
        <th style="min-width: 120px;">Merge</th>
        <th style="min-width: 140px;">Jenis Abnormal</th>
        <th style="min-width: 140px;">Troly Abnormal</th>
        <th style="min-width: 150px;">Keterangan</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="baris-abnormal"> {{-- tempat baris-baris bobbin --}}
      @foreach (old('abnormal', $abnormal_lama) as $i => $a) {{-- baris dari isi form sebelumnya (jika gagal simpan) atau dari database (saat ubah) --}}
        @include('produksi::laporan._baris-abnormal')
      @endforeach
    </tbody>
  </table>
</div>
<p id="baris-kosong" class="text-muted text-center py-3 mb-0">Tidak ada temuan abnormal di shift ini.</p> {{-- tampil jika belum ada baris --}}
<button type="button" class="btn btn-outline-primary btn-sm mt-2 mb-3" onclick="tambahBaris()"><i class="ti-plus me-1"></i> Tambah Bobbin</button> {{-- tambah 1 baris kosong --}}

<template id="cetakan-abnormal">@include('produksi::laporan._baris-abnormal', ['i' => '__I__', 'a' => []])</template> {{-- template = cetakan baris yang tidak tampil, disalin JavaScript saat Tambah Bobbin --}}

<script>
  function tambahBaris() {                                       // dipanggil tombol Tambah Bobbin
    const cetakan = document.getElementById('cetakan-abnormal').innerHTML; // ambil isi cetakan baris
    document.getElementById('baris-abnormal').insertAdjacentHTML('beforeend', cetakan); // tempel di akhir tabel
    hitungUlang();                                               // perbarui nomor & total
  }

  function hapusBaris(tombol) {                                  // dipanggil tombol hapus di tiap baris
    tombol.closest('tr').remove();                               // closest('tr') = baris tempat tombol berada, lalu dihapus
    hitungUlang();
  }

  function hitungUlang() {                                       // beri nomor urut & perbarui total
    const semua = document.querySelectorAll('#baris-abnormal tr'); // semua baris bobbin
    semua.forEach(function (baris, urutan) {                     // urutan = 0, 1, 2, ...
      baris.querySelector('.nomor-baris').textContent = urutan + 1; // tampilkan nomor 1, 2, 3, ...
      baris.querySelectorAll('[name^="abnormal["]').forEach(function (isian) { // setiap isian di baris ini
        isian.name = isian.name.replace(/abnormal\[[^\]]*\]/, 'abnormal[' + urutan + ']'); // ganti nomor di nama isian agar selalu berurutan: abnormal[0][...], abnormal[1][...]
      });
    });
    document.getElementById('total-abnormal').textContent = semua.length; // tampilkan total bobbin
    document.getElementById('baris-kosong').style.display = semua.length ? 'none' : ''; // sembunyikan tulisan "tidak ada temuan" jika ada baris
  }

  hitungUlang();                                                 // jalankan sekali saat halaman dibuka
</script>

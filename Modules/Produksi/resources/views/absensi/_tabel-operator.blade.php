<div class="table-responsive"> {{-- tabel operator, dipakai oleh create & edit lewat @include; butuh $operator, $area, $tersimpan; table-responsive = bisa digeser di layar kecil --}}
  <table class="table table-hover">
    <thead>
      <tr>
        <th style="width: 50px;">No</th>
        <th>Operator</th>
        <th style="width: 170px;">Kehadiran <span class="text-danger">*</span></th>
        <th style="width: 200px;">Area Kerja</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($operator as $o) {{-- ulangi setiap operator; $o = satu operator --}}
        @php
          $simpan = $tersimpan[$o->id_karyawan] ?? null;        // data tersimpan operator ini (saat edit); null jika belum ada / saat tambah
          $hadir = old('kehadiran.'.$o->id_karyawan, $simpan->kehadiran ?? 'Hadir'); // nilai kehadiran: isi form sebelumnya > data tersimpan > bawaan Hadir
          $area_pilih = old('area.'.$o->id_karyawan, $simpan ? $simpan->id_area : $o->id_area); // nilai area: isi form sebelumnya > data tersimpan > area tetap karyawan
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td> {{-- $loop->iteration = nomor urut perulangan mulai 1 --}}
          <td>
            <div class="font-weight-bold">{{ $o->nama_karyawan }}</div>
            <small class="text-muted">{{ $o->id_karyawan }} · Grup {{ $o->grup }}</small>
          </td>
          <td>
            <select name="kehadiran[{{ $o->id_karyawan }}]" class="form-control @error('kehadiran.'.$o->id_karyawan) is-invalid @enderror" onchange="document.getElementById('area-{{ $o->id_karyawan }}').disabled = this.value != 'Hadir'"> {{-- name="kehadiran[id]" = dikirim sebagai daftar berkunci id karyawan; onchange = jika bukan Hadir, pilihan area dimatikan --}}
              @foreach (['Hadir', 'Sakit', 'Ijin', 'Alfa', 'Cuti'] as $k) {{-- daftar pilihan kehadiran --}}
                <option value="{{ $k }}" {{ $hadir == $k ? 'selected' : '' }}>{{ $k }}</option>
              @endforeach
            </select>
            @error('kehadiran.'.$o->id_karyawan) <div class="invalid-feedback">{{ $message }}</div> @enderror
          </td>
          <td>
            <select name="area[{{ $o->id_karyawan }}]" id="area-{{ $o->id_karyawan }}" class="form-control @error('area.'.$o->id_karyawan) is-invalid @enderror" {{ $hadir != 'Hadir' ? 'disabled' : '' }}> {{-- disabled = tidak bisa dipilih & tidak ikut terkirim --}}
              <option value="">- Pilih -</option>
              @foreach ($area as $a)
                <option value="{{ $a->id_area }}" {{ $area_pilih == $a->id_area ? 'selected' : '' }}>{{ $a->nm_area }}</option>
              @endforeach
            </select>
            @error('area.'.$o->id_karyawan) <div class="invalid-feedback">{{ $message }}</div> @enderror
          </td>
          <td>
            <input type="text" name="keterangan[{{ $o->id_karyawan }}]" value="{{ old('keterangan.'.$o->id_karyawan, $simpan->keterangan ?? '') }}" maxlength="100" class="form-control" placeholder="opsional">
          </td>
        </tr>
      @empty {{-- jika tidak ada operator --}}
        <tr>
          <td colspan="5" class="text-center text-muted py-4">Belum ada operator aktif di divisi Produksi. Tambahkan dulu di menu Data Karyawan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

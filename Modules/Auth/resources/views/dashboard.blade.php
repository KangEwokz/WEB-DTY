@extends('auth::layouts.app') {{-- pakai kerangka layout Modules/Auth/resources/views/layouts/app.blade.php --}}

@section('title', 'Dashboard') {{-- isi @yield('title') di layout = judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini, dimasukkan ke @stack('styles') di layout --}}
<style>
  .profil-card { overflow: hidden; } /* sudut banner ikut melengkung mengikuti kartu */
  .profil-banner { height: 110px; background: linear-gradient(135deg, #248afd, #68afff); } /* latar biru di atas kartu */
  .profil-foto { width: 120px; height: 120px; margin-top: -60px; border-radius: 50%; border: 5px solid #fff; object-fit: cover; background: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, .12); } /* foto bulat, naik setengah ke atas banner */
  .profil-nama { font-size: 1.25rem; font-weight: 700; color: #282f3a; } /* nama karyawan tebal */
  .profil-ringkas { border-top: 1px solid #eef0f4; } /* garis pemisah di bawah kartu profil */
  .profil-ringkas .angka { font-size: 1.1rem; font-weight: 700; color: #248afd; } /* angka ringkasan biru */
  .info-item { display: flex; align-items: center; padding: .85rem 0; border-bottom: 1px dashed #eef0f4; } /* satu baris info: ikon + teks berjajar */
  .info-item:last-child { border-bottom: 0; } /* baris terakhir tanpa garis */
  .info-ikon { width: 40px; height: 40px; flex-shrink: 0; margin-right: 1rem; border-radius: 10px; background: #eaf3ff; color: #248afd; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; } /* kotak ikon biru muda */
  .info-label { font-size: .78rem; color: #a3a4a5; margin-bottom: .1rem; } /* tulisan kecil abu-abu di atas nilai */
  .info-nilai { font-weight: 500; color: #282f3a; } /* nilai data */
</style>
@endpush {{-- akhir CSS --}}

@section('content') {{-- awal isi @yield('content') di layout --}}
  <div class="mb-4"> {{-- baris sapaan --}}
    <h4 class="font-weight-bold mb-1">Data Pengguna</h4> {{-- judul halaman --}}
    <p class="text-muted mb-0">Informasi akun dan posisi Anda di {{ config('app.name') }}.</p> {{-- keterangan kecil; config('app.name') = APP_NAME di .env --}}
  </div>

  <div class="row"> {{-- baris utama: kiri kartu profil, kanan detail --}}

    <div class="col-lg-4 grid-margin stretch-card"> {{-- ===== KARTU PROFIL (kiri, 4/12 lebar) ===== --}}
      <div class="card profil-card"> {{-- kotak kartu --}}
        <div class="profil-banner"></div> {{-- latar biru di atas --}}
        <div class="card-body text-center pt-0"> {{-- isi kartu, rata tengah --}}
          <img class="profil-foto mb-3" src="{{ $karyawan->foto ? asset('uploads/'.$karyawan->foto) : asset('images/foto-default.svg') }}" alt="Foto {{ $karyawan->nama_karyawan }}"> {{-- jika kolom foto terisi tampilkan foto asli, jika kosong tampilkan placeholder --}}
          <div class="profil-nama">{{ $karyawan->nama_karyawan }}</div> {{-- nama karyawan --}}
          <p class="text-muted mb-3">{{ $karyawan->nm_jabatan }}{{ $karyawan->nm_divisi ? ' · '.$karyawan->nm_divisi : '' }}</p> {{-- jabatan, ditambah " · divisi" jika divisinya ada --}}
          @if (auth()->user()->status == 'Aktif') {{-- jika status akun Aktif --}}
            <span class="badge-aktif"><i class="ti-check me-1"></i> Akun Aktif</span> {{-- label hijau --}}
          @else {{-- jika tidak Aktif --}}
            <span class="badge-nonaktif"><i class="ti-close me-1"></i> Akun Non-Aktif</span> {{-- label merah --}}
          @endif {{-- akhir if --}}

          <div class="row profil-ringkas mt-4 pt-3"> {{-- ringkasan di bawah kartu --}}
            <div class="col-6"> {{-- kolom kiri --}}
              <div class="angka">{{ $karyawan->id_karyawan }}</div> {{-- ID karyawan --}}
              <small class="text-muted">ID Karyawan</small> {{-- keterangan --}}
            </div>
            <div class="col-6"> {{-- kolom kanan --}}
              <div class="angka">{{ (int) \Carbon\Carbon::parse($karyawan->tgl_masuk)->diffInYears(now()) }} tahun</div> {{-- masa kerja = selisih tahun dari tanggal masuk sampai sekarang --}}
              <small class="text-muted">Masa Kerja</small> {{-- keterangan --}}
            </div>
          </div>
        </div>
      </div>
    </div> {{-- akhir kartu profil --}}

    <div class="col-lg-8"> {{-- ===== DETAIL (kanan, 8/12 lebar) ===== --}}
      <div class="row"> {{-- baris berisi 2 kartu --}}

        <div class="col-md-6 grid-margin stretch-card"> {{-- kartu AKUN --}}
          <div class="card">
            <div class="card-body">
              <p class="card-title">Akun</p> {{-- judul kartu --}}
              <div class="info-item"> {{-- baris: username --}}
                <div class="info-ikon"><i class="ti-user"></i></div> {{-- ikon --}}
                <div><div class="info-label">Username</div><div class="info-nilai">{{ auth()->user()->username }}</div></div> {{-- label + nilai --}}
              </div>
              <div class="info-item"> {{-- baris: role --}}
                <div class="info-ikon"><i class="ti-key"></i></div>
                <div><div class="info-label">Role</div><div class="info-nilai">{{ $role->nm_role }}</div></div> {{-- nama role dari tabel role --}}
              </div>
              <div class="info-item"> {{-- baris: jenis kelamin --}}
                <div class="info-ikon"><i class="ti-face-smile"></i></div>
                <div><div class="info-label">Jenis Kelamin</div><div class="info-nilai">{{ $karyawan->jns_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div></div> {{-- L = Laki-laki, selain itu Perempuan --}}
              </div>
              <div class="info-item"> {{-- baris: no HP --}}
                <div class="info-ikon"><i class="ti-mobile"></i></div>
                <div><div class="info-label">No. HP</div><div class="info-nilai">{{ $karyawan->no_hp ?? '-' }}</div></div> {{-- ?? '-' = tampilkan '-' jika kosong --}}
              </div>
              <div class="info-item"> {{-- baris: email --}}
                <div class="info-ikon"><i class="ti-email"></i></div>
                <div><div class="info-label">Email</div><div class="info-nilai">{{ $karyawan->email ?? '-' }}</div></div>
              </div>
            </div>
          </div>
        </div> {{-- akhir kartu akun --}}

        <div class="col-md-6 grid-margin stretch-card"> {{-- kartu ORGANISASI --}}
          <div class="card">
            <div class="card-body">
              <p class="card-title">Organisasi</p> {{-- judul kartu --}}
              <div class="info-item"> {{-- baris: departemen --}}
                <div class="info-ikon"><i class="ti-home"></i></div>
                <div><div class="info-label">Departemen</div><div class="info-nilai">{{ $karyawan->nm_departemen ?? '-' }}</div></div> {{-- '-' untuk Direktur --}}
              </div>
              <div class="info-item"> {{-- baris: divisi --}}
                <div class="info-ikon"><i class="ti-layers"></i></div>
                <div><div class="info-label">Divisi</div><div class="info-nilai">{{ $karyawan->nm_divisi ?? '-' }}</div></div> {{-- '-' untuk Direktur, HOD, Admin departemen --}}
              </div>
              <div class="info-item"> {{-- baris: jabatan --}}
                <div class="info-ikon"><i class="ti-briefcase"></i></div>
                <div><div class="info-label">Jabatan</div><div class="info-nilai">{{ $karyawan->nm_jabatan }}</div></div>
              </div>
              <div class="info-item"> {{-- baris: area dan grup --}}
                <div class="info-ikon"><i class="ti-location-pin"></i></div>
                <div><div class="info-label">Area / Grup</div><div class="info-nilai">{{ $karyawan->nm_area ?? '-' }} / {{ $karyawan->grup }}</div></div> {{-- grup NG = non-grup --}}
              </div>
              <div class="info-item"> {{-- baris: tanggal masuk --}}
                <div class="info-ikon"><i class="ti-calendar"></i></div>
                <div><div class="info-label">Tanggal Masuk</div><div class="info-nilai">{{ date('d-m-Y', strtotime($karyawan->tgl_masuk)) }}</div></div> {{-- ubah 2016-08-01 jadi 01-08-2016 --}}
              </div>
            </div>
          </div>
        </div> {{-- akhir kartu organisasi --}}

      </div>
    </div> {{-- akhir detail --}}

  </div>
@endsection {{-- akhir isi content --}}

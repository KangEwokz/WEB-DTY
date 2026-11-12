<!DOCTYPE html> {{-- layout utama setelah login, dipakai dengan @extends('auth::layouts.app') --}}
<html lang="id"> {{-- awal HTML, bahasa Indonesia --}}

<head> {{-- bagian kepala: pengaturan halaman --}}
  <meta charset="utf-8"> {{-- format huruf UTF-8 --}}
  <meta name="viewport" content="width=device-width, initial-scale=1"> {{-- agar tampilan pas di HP --}}
  <title>@yield('title') - {{ config('app.name') }}</title> {{-- @yield('title') = diisi oleh @section('title') di tiap halaman --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/vendors/ti-icons/css/themify-icons.css') }}"> {{-- CSS ikon --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/vendors/base/vendor.bundle.base.css') }}"> {{-- CSS dasar (Bootstrap) --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/css/style.css') }}"> {{-- CSS utama template --}}
  <style>
    select.form-control { height: 2.875rem; padding: 0 1.375rem; border: 1px solid #c9ccd7; color: #212529; } /* select disamakan tinggi & garisnya dengan input (bawaan template lebih kecil) */
    .form-control.is-invalid { border-color: #ff4747; } /* garis merah saat ada error validasi */
    .btn.btn-sm { padding: .5rem .7rem; font-size: .8rem; line-height: 1; } /* bawaan template .btn-sm justru besar, dikecilkan untuk tombol aksi di tabel */
    .badge-aktif { background: #e6f6d5; color: #4e8a0c; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label status hijau */
    .badge-nonaktif { background: #ffe3e3; color: #d42f2f; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label status merah */
  </style>
  @stack('styles') {{-- tempat CSS tambahan dari halaman tertentu (diisi dengan @push('styles')) --}}
  <link rel="shortcut icon" href="{{ asset('Royal-UI/template/images/favicon.png') }}"> {{-- ikon tab browser --}}
</head>

<body>
  <div class="container-scroller"> {{-- pembungkus seluruh halaman --}}

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row"> {{-- ===== NAVBAR (bar atas, menempel di atas) ===== --}}
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"> {{-- kotak logo kiri atas --}}
        <a class="navbar-brand brand-logo me-5" href="/dashboard"><img src="{{ asset('Royal-UI/template/images/logo.svg') }}" alt="logo"></a> {{-- logo besar, klik = ke dashboard --}}
        <a class="navbar-brand brand-logo-mini" href="/dashboard"><img src="{{ asset('Royal-UI/template/images/logo-mini.svg') }}" alt="logo"></a> {{-- logo kecil (saat sidebar diperkecil) --}}
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end"> {{-- isi navbar sebelah kanan logo --}}
        <button class="navbar-toggler align-self-center" type="button" data-toggle="minimize"> {{-- tombol perkecil/perbesar sidebar --}}
          <span class="ti-view-list"></span> {{-- ikon garis tiga --}}
        </button>
        <ul class="navbar-nav navbar-nav-right"> {{-- menu di pojok kanan --}}
          <li class="nav-item nav-profile dropdown"> {{-- menu profil (dropdown) --}}
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"> {{-- klik untuk membuka dropdown --}}
              <i class="ti-user me-2"></i> {{-- ikon user --}}
              {{ auth()->user()->username }} ({{ auth()->user()->id_role }}) {{-- tampilkan username dan role user yang login --}}
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown"> {{-- isi dropdown --}}
              <form action="/logout" method="POST"> {{-- form logout dikirim ke route POST /logout --}}
                @csrf {{-- token keamanan --}}
                <button type="submit" class="dropdown-item"> {{-- tombol logout --}}
                  <i class="ti-power-off text-primary"></i> Logout {{-- ikon + tulisan Logout --}}
                </button>
              </form>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas"> {{-- tombol buka sidebar di HP (d-lg-none = hanya tampil di layar kecil) --}}
          <span class="ti-view-list"></span> {{-- ikon garis tiga --}}
        </button>
      </div>
    </nav> {{-- akhir navbar --}}

    <div class="container-fluid page-body-wrapper"> {{-- pembungkus sidebar + isi halaman --}}

      <nav class="sidebar sidebar-offcanvas" id="sidebar"> {{-- ===== SIDEBAR (menu samping) ===== --}}
        <ul class="nav"> {{-- daftar menu --}}
          <li class="nav-item"> {{-- satu menu --}}
            <a class="nav-link" href="/dashboard"> {{-- link ke dashboard --}}
              <i class="ti-home menu-icon"></i> {{-- ikon rumah --}}
              <span class="menu-title">Dashboard</span> {{-- tulisan menu --}}
            </a>
          </li>

          @if (auth()->user()->punyaAkses('admin.karyawan.index')) {{-- menu hanya tampil jika role user punya permission admin.karyawan.index --}}
            <li class="nav-item"> {{-- satu menu --}}
              <a class="nav-link" href="{{ route('admin.karyawan.index') }}"> {{-- route() = ubah nama route jadi alamat URL (/karyawan) --}}
                <i class="ti-id-badge menu-icon"></i> {{-- ikon kartu nama --}}
                <span class="menu-title">Data Karyawan</span> {{-- tulisan menu --}}
              </a>
            </li>
          @endif {{-- akhir if --}}

          @if (auth()->user()->punyaAkses('produksi.absensi.index') || auth()->user()->punyaAkses('produksi.doffing.index') || auth()->user()->punyaAkses('produksi.laporan.index') || auth()->user()->punyaAkses('produksi.rekap-bulanan.index')) {{-- grup Produksi (transaksi) tampil jika boleh membuka minimal satu menunya --}}
            <li class="nav-item {{ request()->is('produksi/absensi*', 'produksi/doffing*', 'produksi/laporan*', 'produksi/rekap-bulanan*') ? 'active' : '' }}"> {{-- ditandai aktif jika sedang di salah satu halaman produksi --}}
              <a class="nav-link" data-bs-toggle="collapse" href="#menu-produksi" aria-expanded="{{ request()->is('produksi/absensi*', 'produksi/doffing*', 'produksi/laporan*', 'produksi/rekap-bulanan*') ? 'true' : 'false' }}"> {{-- klik untuk buka/tutup submenu --}}
                <i class="ti-settings menu-icon"></i> {{-- ikon roda gigi --}}
                <span class="menu-title">Produksi</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse {{ request()->is('produksi/absensi*', 'produksi/doffing*', 'produksi/laporan*', 'produksi/rekap-bulanan*') ? 'show' : '' }}" id="menu-produksi"> {{-- isi submenu; show = langsung terbuka --}}
                <ul class="nav flex-column sub-menu">
                  @if (auth()->user()->punyaAkses('produksi.absensi.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/absensi*') ? 'active' : '' }}" href="{{ route('produksi.absensi.index') }}">Absensi & Penempatan</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.doffing.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/doffing*') ? 'active' : '' }}" href="{{ route('produksi.doffing.index') }}">Doffing</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.laporan.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/laporan*') ? 'active' : '' }}" href="{{ route('produksi.laporan.index') }}">Laporan Akhir Shift</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.rekap-bulanan.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/rekap-bulanan*') ? 'active' : '' }}" href="{{ route('produksi.rekap-bulanan.index') }}">Rekap Bulanan</a></li>
                  @endif
                </ul>
              </div>
            </li>
          @endif {{-- akhir grup Produksi --}}

          @if (auth()->user()->punyaAkses('admin.departemen.index') || auth()->user()->punyaAkses('admin.divisi.index') || auth()->user()->punyaAkses('admin.jabatan.index') || auth()->user()->punyaAkses('admin.area.index') || auth()->user()->punyaAkses('produksi.benang.index') || auth()->user()->punyaAkses('produksi.mesin.index') || auth()->user()->punyaAkses('produksi.troly.index') || auth()->user()->punyaAkses('produksi.jenis-abnormal.index')) {{-- grup tampil jika boleh membuka minimal satu menu di dalamnya (termasuk master produksi); || = atau --}}
            <li class="nav-item {{ request()->is('departemen*', 'divisi*', 'jabatan*', 'area*', 'produksi/benang*', 'produksi/mesin*', 'produksi/troly*', 'produksi/jenis-abnormal*') ? 'active' : '' }}"> {{-- request()->is() = cek alamat URL sekarang; jika di halaman master, grup ditandai aktif --}}
              <a class="nav-link" data-bs-toggle="collapse" href="#menu-master" aria-expanded="{{ request()->is('departemen*', 'divisi*', 'jabatan*', 'area*', 'produksi/benang*', 'produksi/mesin*', 'produksi/troly*', 'produksi/jenis-abnormal*') ? 'true' : 'false' }}"> {{-- klik untuk buka/tutup submenu #menu-master --}}
                <i class="ti-layout-list-thumb menu-icon"></i> {{-- ikon daftar --}}
                <span class="menu-title">Data Master</span> {{-- tulisan menu --}}
                <i class="menu-arrow"></i> {{-- panah kecil di kanan --}}
              </a>
              <div class="collapse {{ request()->is('departemen*', 'divisi*', 'jabatan*', 'area*', 'produksi/benang*', 'produksi/mesin*', 'produksi/troly*', 'produksi/jenis-abnormal*') ? 'show' : '' }}" id="menu-master"> {{-- isi submenu; class show = langsung terbuka --}}
                <ul class="nav flex-column sub-menu"> {{-- daftar submenu --}}
                  @if (auth()->user()->punyaAkses('admin.departemen.index')) {{-- tiap submenu dicek aksesnya sendiri --}}
                    <li class="nav-item"><a class="nav-link {{ request()->is('departemen*') ? 'active' : '' }}" href="{{ route('admin.departemen.index') }}">Departemen</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.divisi.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('divisi*') ? 'active' : '' }}" href="{{ route('admin.divisi.index') }}">Divisi</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.jabatan.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('jabatan*') ? 'active' : '' }}" href="{{ route('admin.jabatan.index') }}">Jabatan</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.area.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('area*') ? 'active' : '' }}" href="{{ route('admin.area.index') }}">Area</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.benang.index')) {{-- master produksi digabung di sini agar semua master ada di satu tempat --}}
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/benang*') ? 'active' : '' }}" href="{{ route('produksi.benang.index') }}">Benang</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.mesin.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/mesin*') ? 'active' : '' }}" href="{{ route('produksi.mesin.index') }}">Mesin</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.troly.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/troly*') ? 'active' : '' }}" href="{{ route('produksi.troly.index') }}">Troly</a></li>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.jenis-abnormal.index'))
                    <li class="nav-item"><a class="nav-link {{ request()->is('produksi/jenis-abnormal*') ? 'active' : '' }}" href="{{ route('produksi.jenis-abnormal.index') }}">Jenis Abnormal</a></li>
                  @endif
                </ul>
              </div>
            </li>
          @endif {{-- akhir grup Data Master --}}

          @if (auth()->user()->punyaAkses('auth.user.index')) {{-- menu hanya tampil jika boleh membuka daftar akun --}}
            <li class="nav-item {{ request()->is('akun*') ? 'active' : '' }}"> {{-- ditandai aktif jika sedang di halaman akun --}}
              <a class="nav-link" href="{{ route('auth.user.index') }}">
                <i class="ti-user menu-icon"></i> {{-- ikon orang --}}
                <span class="menu-title">Akun Pengguna</span> {{-- tulisan menu --}}
              </a>
            </li>
          @endif

          @if (auth()->user()->punyaAkses('auth.role.index')) {{-- menu hanya tampil jika boleh membuka daftar role (bawaan: superadmin saja) --}}
            <li class="nav-item {{ request()->is('role*') ? 'active' : '' }}"> {{-- ditandai aktif jika sedang di halaman role --}}
              <a class="nav-link" href="{{ route('auth.role.index') }}">
                <i class="ti-key menu-icon"></i> {{-- ikon kunci --}}
                <span class="menu-title">Role & Permission</span> {{-- tulisan menu --}}
              </a>
            </li>
          @endif
        </ul>
      </nav> {{-- akhir sidebar --}}

      <div class="main-panel"> {{-- ===== ISI HALAMAN ===== --}}
        <div class="content-wrapper"> {{-- area konten --}}

          @if (session('success')) {{-- jika controller mengirim pesan 'success' --}}
            <div class="alert alert-success">{{ session('success') }}</div> {{-- kotak hijau --}}
          @endif {{-- akhir if --}}
          @if (session('error')) {{-- jika controller mengirim pesan 'error' --}}
            <div class="alert alert-danger">{{ session('error') }}</div> {{-- kotak merah --}}
          @endif {{-- akhir if --}}

          @yield('content') {{-- tempat isi halaman, diisi oleh @section('content') di tiap view --}}
        </div>

        <footer class="footer"> {{-- ===== FOOTER (bagian bawah) ===== --}}
          <span class="text-muted">Copyright &copy; {{ date('Y') }} {{ config('app.name') }}</span> {{-- date('Y') = tahun sekarang --}}
        </footer>
      </div> {{-- akhir isi halaman --}}
    </div>
  </div>

  <script src="{{ asset('Royal-UI/template/vendors/base/vendor.bundle.base.js') }}"></script> {{-- JavaScript dasar (jQuery + Bootstrap) --}}
  <script src="{{ asset('Royal-UI/template/js/off-canvas.js') }}"></script> {{-- buka/tutup sidebar di HP --}}
  <script src="{{ asset('Royal-UI/template/js/hoverable-collapse.js') }}"></script> {{-- submenu saat sidebar diperkecil --}}
  <script src="{{ asset('Royal-UI/template/js/template.js') }}"></script> {{-- tombol perkecil sidebar & tanda menu aktif --}}
</body>

</html>

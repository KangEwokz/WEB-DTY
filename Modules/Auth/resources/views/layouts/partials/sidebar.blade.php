{{-- Menu tampil kalau lolos: permission (@can) + divisi user + area (khusus operator) --}}
{{-- Menu dikelompokkan: Data Master, lalu per modul (Admin, Produksi, QCT) --}}
@php
  $divisi = divisi_user();
  $area = area_aktif();
  $operator = auth()->user()->hasRole('operator');
  $semua = auth()->user()->hasRole('superadmin');   // akun demo: tampilkan menu semua divisi

  // Grup dibuka otomatis kalau halaman yang sedang dibuka ada di dalamnya
  $bukaMaster = request()->routeIs('admin.divisi.*', 'admin.jabatan.*', 'admin.area.*', 'admin.karyawan.*', 'admin.user.*', 'produksi.benang.*', 'produksi.mesin.*', 'qct.area.*', 'qct.grade.*', 'qct.jenis-abnormal.*');
  $bukaAdmin = request()->routeIs('admin.rekap-absensi.*');
  $bukaProduksi = request()->routeIs('produksi.jadwal-produksi.*', 'produksi.doffing.*');
  $bukaQct = request()->routeIs('qct.absensi.*', 'qct.rekap-absensi.*', 'qct.laporan-check.*', 'qct.check-batch.*', 'qct.abnormal.*', 'qct.sampel.*', 'qct.rekap-check.*');
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    {{-- Grup Data Master: master dari semua modul dikumpulkan di sini --}}
    @canany(['kelola-karyawan', 'kelola-akun', 'kelola-jadwal-produksi', 'kelola-master-qct'])
      <li class="nav-item {{ $bukaMaster ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-master" aria-expanded="{{ $bukaMaster ? 'true' : 'false' }}" aria-controls="menu-master">
          <i class="ti-server menu-icon"></i>
          <span class="menu-title">Data Master</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $bukaMaster ? 'show' : '' }}" id="menu-master">
          <ul class="nav flex-column sub-menu">
            @if ($divisi == 'Admin' || $semua)
              @can('kelola-karyawan')
                @include('auth::layouts.partials.submenu', ['route' => 'admin.divisi.index', 'judul' => 'Divisi'])
                @include('auth::layouts.partials.submenu', ['route' => 'admin.jabatan.index', 'judul' => 'Jabatan'])
                @include('auth::layouts.partials.submenu', ['route' => 'admin.area.index', 'judul' => 'Area'])
                @include('auth::layouts.partials.submenu', ['route' => 'admin.karyawan.index', 'judul' => 'Karyawan'])
              @endcan
              @can('kelola-akun')
                @include('auth::layouts.partials.submenu', ['route' => 'admin.user.index', 'judul' => 'Akun Pengguna'])
              @endcan
            @endif
            @if ($divisi == 'Produksi' || $semua)
              @can('kelola-jadwal-produksi')
                @include('auth::layouts.partials.submenu', ['route' => 'produksi.benang.index', 'judul' => 'Benang'])
                @include('auth::layouts.partials.submenu', ['route' => 'produksi.mesin.index', 'judul' => 'Mesin'])
              @endcan
            @endif
            @if ($divisi == 'QCT' || $semua)
              @can('kelola-master-qct')
                @include('auth::layouts.partials.submenu', ['route' => 'qct.area.index', 'judul' => 'Area QCT'])
                @include('auth::layouts.partials.submenu', ['route' => 'qct.grade.index', 'judul' => 'Grade'])
                @include('auth::layouts.partials.submenu', ['route' => 'qct.jenis-abnormal.index', 'judul' => 'Jenis Abnormal'])
              @endcan
            @endif
          </ul>
        </div>
      </li>
    @endcanany

    {{-- Modul Admin (Staf) --}}
    @if (($divisi == 'Admin' || $semua) && auth()->user()->can('lihat-rekap-absensi'))
      <li class="nav-item {{ $bukaAdmin ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-admin" aria-expanded="{{ $bukaAdmin ? 'true' : 'false' }}" aria-controls="menu-admin">
          <i class="ti-briefcase menu-icon"></i>
          <span class="menu-title">Admin</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $bukaAdmin ? 'show' : '' }}" id="menu-admin">
          <ul class="nav flex-column sub-menu">
            @include('auth::layouts.partials.submenu', ['route' => 'admin.rekap-absensi.index', 'judul' => 'Rekap Absensi QCT'])
          </ul>
        </div>
      </li>
    @endif

    {{-- Modul Produksi (Supervisor) --}}
    @if ($divisi == 'Produksi' || $semua)
      @canany(['kelola-jadwal-produksi', 'kelola-doffing'])
        <li class="nav-item {{ $bukaProduksi ? 'active' : '' }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#menu-produksi" aria-expanded="{{ $bukaProduksi ? 'true' : 'false' }}" aria-controls="menu-produksi">
            <i class="ti-settings menu-icon"></i>
            <span class="menu-title">Produksi</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ $bukaProduksi ? 'show' : '' }}" id="menu-produksi">
            <ul class="nav flex-column sub-menu">
              @can('kelola-jadwal-produksi')
                @include('auth::layouts.partials.submenu', ['route' => 'produksi.jadwal-produksi.index', 'judul' => 'Jadwal Produksi'])
              @endcan
              @can('kelola-doffing')
                @include('auth::layouts.partials.submenu', ['route' => 'produksi.doffing.index', 'judul' => 'Doffing'])
              @endcan
            </ul>
          </div>
        </li>
      @endcanany
    @endif

    {{-- Modul QCT: HOD, Operator, dan rekap Direktur (Direktur tidak terikat divisi) --}}
    @if ($divisi == 'QCT' || $semua || auth()->user()->can('lihat-rekap-check'))
      <li class="nav-item {{ $bukaQct ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-qct" aria-expanded="{{ $bukaQct ? 'true' : 'false' }}" aria-controls="menu-qct">
          <i class="ti-clipboard menu-icon"></i>
          <span class="menu-title">QCT</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $bukaQct ? 'show' : '' }}" id="menu-qct">
          <ul class="nav flex-column sub-menu">
            @if ($divisi == 'QCT' || $semua)
              {{-- HOD --}}
              @can('kelola-absensi')
                @include('auth::layouts.partials.submenu', ['route' => 'qct.absensi.index', 'judul' => 'Absensi QCT'])
              @endcan
              @can('verifikasi-rekap-absensi')
                @include('auth::layouts.partials.submenu', ['route' => 'qct.rekap-absensi.index', 'judul' => 'Rekap Absensi'])
              @endcan
              @can('verifikasi-laporan-check')
                @include('auth::layouts.partials.submenu', ['route' => 'qct.laporan-check.index', 'judul' => 'Laporan Check'])
              @endcan

              {{-- Operator (menu hanya tampil kalau area kerja shift ini Visual atau Lab) --}}
              @if (! $operator || in_array($area, ['VIS', 'LAB']))
                @canany(['kelola-cek-visual', 'kelola-cek-denier'])
                  @include('auth::layouts.partials.submenu', ['route' => 'qct.check-batch.index', 'judul' => 'Cek Troly'])
                @endcanany
                @can('kelola-abnormal')
                  @include('auth::layouts.partials.submenu', ['route' => 'qct.abnormal.index', 'judul' => 'Abnormal'])
                @endcan
                @can('kelola-sampel')
                  @include('auth::layouts.partials.submenu', ['route' => 'qct.sampel.index', 'judul' => 'Sampel'])
                @endcan
              @endif
            @endif

            {{-- Direktur --}}
            @can('lihat-rekap-check')
              @include('auth::layouts.partials.submenu', ['route' => 'qct.rekap-check.index', 'judul' => 'Rekap Check QCT'])
            @endcan
          </ul>
        </div>
      </li>
    @endif
  </ul>
</nav>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    @can('akses-karyawan')
      @can('karyawan.view')
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-karyawan" aria-expanded="false" aria-controls="menu-karyawan">
          <i class="ti-id-badge menu-icon"></i>
          <span class="menu-title">Karyawan</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="menu-karyawan">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ route('karyawan.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('absensi.index') }}">Absensi</a></li>
          </ul>
        </div>
      </li>
      @endcan
    @endcan

    @canany(['divisi.view', 'jabatan.view', 'grup.view', 'shift.view'])
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#menu-master" aria-expanded="false" aria-controls="menu-master">
          <i class="ti-layers menu-icon"></i>
          <span class="menu-title">Master Data</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="menu-master">
          <ul class="nav flex-column sub-menu">
            @can('divisi.view')
              <li class="nav-item"><a class="nav-link" href="{{ route('divisi.index') }}">Divisi</a></li>
            @endcan
            @can('jabatan.view')
              <li class="nav-item"><a class="nav-link" href="{{ route('jabatan.index') }}">Jabatan</a></li>
            @endcan
            @can('grup.view')
              <li class="nav-item"><a class="nav-link" href="{{ route('grup.index') }}">Grup</a></li>
            @endcan
            @can('shift.view')
              <li class="nav-item"><a class="nav-link" href="{{ route('shift.index') }}">Shift</a></li>
            @endcan
          </ul>
        </div>
      </li>
    @endcanany

    @role('SuperAdmin')
      <li class="nav-item">
        <a class="nav-link" href="{{ route('register') }}">
          <i class="ti-user menu-icon"></i>
          <span class="menu-title">Tambah User</span>
        </a>
      </li>
    @endrole
  </ul>
</nav>

{{--
  Satu item menu sidebar. Dipanggil dari sidebar.blade.php:
  @include('auth::layouts.partials.menu', ['route' => 'qct.grade.index', 'icon' => 'ti-medall', 'judul' => 'Grade'])
  Menu baru tampil kalau route-nya sudah dibuat (Route::has).
--}}
@if (Route::has($route))
  @php $grup = substr($route, 0, strrpos($route, '.')); @endphp
  <li class="nav-item {{ request()->routeIs($grup.'.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route($route) }}">
      <i class="{{ $icon }} menu-icon"></i>
      <span class="menu-title">{{ $judul }}</span>
    </a>
  </li>
@endif

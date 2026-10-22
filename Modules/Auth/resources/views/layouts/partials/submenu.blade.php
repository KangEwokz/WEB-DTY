{{--
  Satu item di dalam grup menu sidebar (sub-menu). Dipanggil dari sidebar.blade.php:
  @include('auth::layouts.partials.submenu', ['route' => 'qct.grade.index', 'judul' => 'Grade'])
  Tampil kalau route-nya sudah dibuat (Route::has).
--}}
@if (Route::has($route))
  @php $grup = substr($route, 0, strrpos($route, '.')); @endphp
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs($grup.'.*') ? 'active' : '' }}" href="{{ route($route) }}">{{ $judul }}</a>
  </li>
@endif

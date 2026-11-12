<form action="{{ $url }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengubah status data ini?')"> {{-- tombol Aktifkan/Nonaktifkan untuk semua data master; $url dan $status dikirim dari @include; confirm() = kotak konfirmasi --}}
  @csrf {{-- token keamanan --}}
  @method('PATCH') {{-- form HTML hanya bisa GET/POST, jadi ditandai sebagai PATCH agar cocok dengan route --}}
  @if ($status == 'Aktif') {{-- jika sekarang Aktif, tombolnya untuk menonaktifkan --}}
    <button type="submit" class="btn btn-sm btn-outline-danger" title="Nonaktifkan"><i class="ti-close"></i></button> {{-- tombol merah --}}
  @else {{-- jika sekarang Non-Aktif, tombolnya untuk mengaktifkan --}}
    <button type="submit" class="btn btn-sm btn-outline-success" title="Aktifkan"><i class="ti-check"></i></button> {{-- tombol hijau --}}
  @endif
</form>

{{--
  Kode merge beserta info benangnya (denier, filamen, warna ppt). Dipanggil dari sel tabel:
  @include('auth::layouts.partials.info-benang', ['baris' => $t])
  $baris harus punya kolom merge, denier, filamen, warna_ppt (query-nya join ke tabel benang).
--}}
{{ $baris->merge }}
<small class="d-block text-muted">Denier {{ $baris->denier }}, Filamen {{ $baris->filamen }}, Warna PPT {{ $baris->warna_ppt ?? '-' }}</small>

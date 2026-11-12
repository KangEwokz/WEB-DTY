@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Atur Permission') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .grup-judul { background: #f5f7fb; border-radius: 8px; padding: .6rem 1rem; } /* latar abu-abu muda untuk judul kelompok */
  .form-check { padding-left: 1.75rem; } /* jarak kotak centang ke tulisan */
  .form-check-input { width: 1.1rem; height: 1.1rem; margin-left: -1.75rem; } /* kotak centang sedikit diperbesar */
  .form-check-input:checked { background-color: #248afd; border-color: #248afd; } /* kotak tercentang warna biru utama */
</style>
@endpush {{-- akhir CSS --}}

@section('content') {{-- awal isi halaman --}}
  <div class="mb-4">
    <h4 class="font-weight-bold mb-1">Atur Permission: {{ $role->nm_role }}</h4> {{-- judul + nama role --}}
    <p class="text-muted mb-0">Centang halaman/aksi yang boleh dibuka oleh role <b>{{ $role->id_role }}</b>.</p>
  </div>

  <form action="{{ route('auth.role.simpan-permission', $role->id_role) }}" method="POST"> {{-- kirim ke route simpan-permission --}}
    @csrf {{-- token keamanan --}}
    @method('PUT') {{-- ditandai sebagai PUT --}}

    <div class="row">
      @foreach ($grup as $nama_grup => $daftar) {{-- ulangi setiap kelompok; $nama_grup contoh 'karyawan', $daftar = permission di kelompok itu --}}
        <div class="col-md-6 col-xl-4 grid-margin stretch-card"> {{-- satu kartu per kelompok, 3 kartu per baris di layar lebar --}}
          <div class="card">
            <div class="card-body">
              <div class="form-check grup-judul mb-3"> {{-- judul kelompok + centang semua --}}
                <input type="checkbox" class="form-check-input" id="semua-{{ $nama_grup }}" onclick="document.querySelectorAll('.grup-{{ $nama_grup }}').forEach(kotak => kotak.checked = this.checked)"> {{-- JavaScript: saat diklik, semua kotak di kelompok ini ikut dicentang/dikosongkan --}}
                <label class="form-check-label font-weight-bold" for="semua-{{ $nama_grup }}">{{ ucfirst($nama_grup) }}</label> {{-- ucfirst = huruf pertama besar, contoh 'karyawan' -> 'Karyawan' --}}
              </div>

              @foreach ($daftar as $p) {{-- ulangi setiap permission di kelompok ini --}}
                <div class="form-check mb-2"> {{-- satu baris centang --}}
                  <input type="checkbox" name="permission[]" value="{{ $p->id_permission }}" id="{{ $p->id_permission }}" class="form-check-input grup-{{ $nama_grup }}" {{ in_array($p->id_permission, $dimiliki) ? 'checked' : '' }}> {{-- name="permission[]" = dikirim sebagai daftar; in_array = cek apakah sudah dimiliki, jika ya langsung tercentang --}}
                  <label class="form-check-label" for="{{ $p->id_permission }}"> {{-- for = klik tulisan ikut mencentang kotak --}}
                    {{ $p->nm_permission }} {{-- keterangan, contoh 'Buka form tambah karyawan' --}}
                    @if ($p->status_aktif != 'Aktif') {{-- tandai jika permission sedang dinonaktifkan --}}
                      <span class="badge-nonaktif ms-1" style="font-size: .7rem; padding: .2rem .5rem;">Non-Aktif</span>
                    @endif
                    <br><small class="text-muted">{{ $p->id_permission }}</small> {{-- nama route di bawahnya --}}
                  </label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex gap-2 mb-4"> {{-- baris tombol --}}
      <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Permission</button>
      <a href="{{ route('auth.role.index') }}" class="btn btn-light">Batal</a>
    </div>
  </form>
@endsection {{-- akhir isi halaman --}}

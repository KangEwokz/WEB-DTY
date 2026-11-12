@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Akun Pengguna') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .foto-kecil { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; } /* foto bulat kecil di tabel */
  .table td, .table th { vertical-align: middle; } /* isi sel rata tengah vertikal */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Akun Pengguna</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Akun login milik karyawan. Satu karyawan hanya boleh punya satu akun.</p>
    </div>
    @if (auth()->user()->punyaAkses('auth.user.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('auth.user.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Buat Akun</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('auth.user.index') }}" method="GET" class="row g-2 mb-4"> {{-- form pencarian --}}
        <div class="col-md-5">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari username atau nama karyawan...">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-search"></i> Cari</button>
          @if (request('cari')) {{-- tombol reset hanya saat sedang mencari --}}
            <a href="{{ route('auth.user.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Karyawan</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status Akun</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($user as $u) {{-- ulangi setiap akun; $u = satu akun --}}
              <tr>
                <td>
                  <div class="d-flex align-items-center"> {{-- foto dan nama berjajar --}}
                    <img class="foto-kecil me-3" src="{{ $u->foto ? asset('uploads/'.$u->foto) : asset('images/foto-default.svg') }}" alt="foto"> {{-- foto asli atau placeholder --}}
                    <div>
                      <div class="font-weight-bold">{{ $u->nama_karyawan }}</div>
                      <small class="text-muted">{{ $u->id_karyawan }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  {{ $u->username }}
                  @if ($u->id == auth()->id()) {{-- tandai akun yang sedang login --}}
                    <small class="text-primary">(Anda)</small>
                  @endif
                </td>
                <td>{{ $u->nm_role }}</td>
                <td>
                  @if ($u->status == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if ($u->id_role != 'superadmin' || auth()->user()->id_role == 'superadmin') {{-- akun superadmin hanya bisa diutak-atik oleh superadmin --}}
                    @if (auth()->user()->punyaAkses('auth.user.edit'))
                      <a href="{{ route('auth.user.edit', $u->id) }}" class="btn btn-sm btn-outline-primary" title="Ubah / Reset Password"><i class="ti-pencil"></i></a>
                    @endif
                    @if ($u->id != auth()->id() && auth()->user()->punyaAkses('auth.user.status')) {{-- tidak ada tombol nonaktif untuk akun sendiri --}}
                      @include('admin::partials.tombol-status', ['url' => route('auth.user.status', $u->id), 'status' => $u->status])
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Akun tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3"> {{-- baris bawah tabel --}}
        <small class="text-muted">Menampilkan {{ $user->count() }} dari {{ $user->total() }} akun</small>
        {{ $user->links() }} {{-- link halaman 1, 2, 3, ... --}}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

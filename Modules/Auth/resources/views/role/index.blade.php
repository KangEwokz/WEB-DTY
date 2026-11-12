@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Role & Permission') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Role & Permission</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Role menentukan halaman apa saja yang boleh dibuka. Terpisah dari jabatan.</p>
    </div>
    @if (auth()->user()->punyaAkses('auth.role.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('auth.role.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Role</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Role</th>
              <th>Permission</th>
              <th>Akun</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($role as $r) {{-- ulangi setiap role; $r = satu role --}}
              <tr>
                <td class="font-weight-bold">{{ $r->id_role }}</td>
                <td>{{ $r->nm_role }}</td>
                <td>{{ $r->id_role == 'superadmin' ? 'Semua' : $r->jml_permission }}</td> {{-- superadmin otomatis semua --}}
                <td>{{ $r->jml_user }}</td> {{-- jumlah akun yang memakai role ini --}}
                <td>
                  @if ($r->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if ($r->id_role != 'superadmin' && auth()->user()->punyaAkses('auth.role.permission')) {{-- tombol atur permission (tidak untuk superadmin); && = dan --}}
                    <a href="{{ route('auth.role.permission', $r->id_role) }}" class="btn btn-sm btn-outline-info" title="Atur Permission"><i class="ti-key"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('auth.role.edit')) {{-- tombol ubah nama --}}
                    <a href="{{ route('auth.role.edit', $r->id_role) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if ($r->id_role != 'superadmin' && $r->id_role != auth()->user()->id_role && auth()->user()->punyaAkses('auth.role.status')) {{-- tombol aktif/nonaktif; tidak untuk superadmin dan role milik sendiri --}}
                    @include('admin::partials.tombol-status', ['url' => route('auth.role.status', $r->id_role), 'status' => $r->status_aktif]) {{-- pakai tombol yang sama dengan data master --}}
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Data Jabatan') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Data Jabatan</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Daftar jabatan umum, bisa dipakai di departemen atau divisi mana pun.</p>
    </div>
    @if (auth()->user()->punyaAkses('admin.jabatan.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('admin.jabatan.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Jabatan</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Jabatan</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($jabatan as $j) {{-- ulangi setiap jabatan; $j = satu jabatan --}}
              <tr>
                <td>{{ $j->id_jabatan }}</td>
                <td class="font-weight-bold">{{ $j->nm_jabatan }}</td>
                <td>
                  @if ($j->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('admin.jabatan.edit')) {{-- tombol ubah hanya jika punya akses --}}
                    <a href="{{ route('admin.jabatan.edit', $j->id_jabatan) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.jabatan.status')) {{-- tombol aktif/nonaktif hanya jika punya akses --}}
                    @include('admin::partials.tombol-status', ['url' => route('admin.jabatan.status', $j->id_jabatan), 'status' => $j->status_aktif])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Belum ada data jabatan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

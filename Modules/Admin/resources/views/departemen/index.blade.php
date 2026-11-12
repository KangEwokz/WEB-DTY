@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Data Departemen') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Data Departemen</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Departemen dipimpin oleh HOD dan membawahi beberapa divisi.</p> {{-- keterangan kecil --}}
    </div>
    @if (auth()->user()->punyaAkses('admin.departemen.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('admin.departemen.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Departemen</a>
    @endif
  </div>

  <div class="card"> {{-- kotak kartu --}}
    <div class="card-body">
      <div class="table-responsive"> {{-- tabel bisa digeser di layar kecil --}}
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Kode</th>
              <th>Nama Departemen</th>
              <th>Status</th>
              <th class="text-end">Aksi</th> {{-- text-end = rata kanan --}}
            </tr>
          </thead>
          <tbody>
            @forelse ($departemen as $d) {{-- ulangi setiap departemen; $d = satu departemen --}}
              <tr>
                <td>{{ $d->id_departemen }}</td>
                <td class="font-weight-bold">{{ $d->kd_departemen }}</td>
                <td>{{ $d->nm_departemen }}</td>
                <td>
                  @if ($d->status_aktif == 'Aktif') {{-- label status --}}
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap"> {{-- tombol aksi --}}
                  @if (auth()->user()->punyaAkses('admin.departemen.edit')) {{-- tombol ubah hanya jika punya akses --}}
                    <a href="{{ route('admin.departemen.edit', $d->id_departemen) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.departemen.status')) {{-- tombol aktif/nonaktif hanya jika punya akses --}}
                    @include('admin::partials.tombol-status', ['url' => route('admin.departemen.status', $d->id_departemen), 'status' => $d->status_aktif]) {{-- tombol dari file partials/tombol-status.blade.php --}}
                  @endif
                </td>
              </tr>
            @empty {{-- jika tidak ada data --}}
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Belum ada data departemen.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Data Divisi') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Data Divisi</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Divisi berada di bawah departemen dan dipimpin oleh Kadiv.</p> {{-- keterangan kecil --}}
    </div>
    @if (auth()->user()->punyaAkses('admin.divisi.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('admin.divisi.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Divisi</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Kode</th>
              <th>Nama Divisi</th>
              <th>Departemen</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($divisi as $dv) {{-- ulangi setiap divisi; $dv = satu divisi --}}
              <tr>
                <td>{{ $dv->id_divisi }}</td>
                <td class="font-weight-bold">{{ $dv->kd_divisi }}</td>
                <td>{{ $dv->nm_divisi }}</td>
                <td>{{ $dv->kd_departemen }} - {{ $dv->nm_departemen }}</td> {{-- departemen pemilik divisi (hasil join) --}}
                <td>
                  @if ($dv->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('admin.divisi.edit')) {{-- tombol ubah hanya jika punya akses --}}
                    <a href="{{ route('admin.divisi.edit', $dv->id_divisi) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.divisi.status')) {{-- tombol aktif/nonaktif hanya jika punya akses --}}
                    @include('admin::partials.tombol-status', ['url' => route('admin.divisi.status', $dv->id_divisi), 'status' => $dv->status_aktif])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data divisi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

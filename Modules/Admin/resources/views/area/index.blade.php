@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Data Area') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Data Area</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Area = tempat tugas di dalam divisi, contoh Visual dan Laboratorium.</p>
    </div>
    @if (auth()->user()->punyaAkses('admin.area.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('admin.area.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Area</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Area</th>
              <th>Divisi</th>
              <th>Departemen</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($area as $a) {{-- ulangi setiap area; $a = satu area --}}
              <tr>
                <td class="font-weight-bold">{{ $a->id_area }}</td>
                <td>{{ $a->nm_area }}</td>
                <td>{{ $a->nm_divisi }}</td> {{-- nama divisi (hasil join) --}}
                <td>{{ $a->kd_departemen }}</td> {{-- kode departemen (hasil join lewat divisi) --}}
                <td>
                  @if ($a->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('admin.area.edit')) {{-- tombol ubah hanya jika punya akses --}}
                    <a href="{{ route('admin.area.edit', $a->id_area) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('admin.area.status')) {{-- tombol aktif/nonaktif hanya jika punya akses --}}
                    @include('admin::partials.tombol-status', ['url' => route('admin.area.status', $a->id_area), 'status' => $a->status_aktif])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data area.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

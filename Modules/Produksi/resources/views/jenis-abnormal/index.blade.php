@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Jenis Abnormal') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Jenis Abnormal</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Jenis cacat benang yang dipakai saat mencatat temuan abnormal.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.jenis-abnormal.create'))
      <a href="{{ route('produksi.jenis-abnormal.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Jenis</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Abnormal</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($jenis as $j) {{-- ulangi setiap jenis abnormal --}}
              <tr>
                <td class="font-weight-bold">{{ $j->id_jns_abn }}</td>
                <td>{{ $j->nm_abnormal }}</td>
                <td>
                  @if ($j->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('produksi.jenis-abnormal.edit'))
                    <a href="{{ route('produksi.jenis-abnormal.edit', $j->id_jns_abn) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.jenis-abnormal.status'))
                    @include('admin::partials.tombol-status', ['url' => route('produksi.jenis-abnormal.status', $j->id_jns_abn), 'status' => $j->status_aktif])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Belum ada data jenis abnormal.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

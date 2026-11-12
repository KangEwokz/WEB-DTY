@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Master Benang') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Master Benang</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Daftar merge benang beserta denier, filamen, dan toleransinya.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.benang.create')) {{-- tombol hanya jika punya akses tambah --}}
      <a href="{{ route('produksi.benang.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Benang</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.benang.index') }}" method="GET" class="row g-2 mb-4"> {{-- form pencarian + filter status --}}
        <div class="col-md-4">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari kode merge atau warna...">
        </div>
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Semua status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-search"></i> Cari</button>
          @if (request('cari') || request('status')) {{-- tombol reset jika sedang mencari / memfilter --}}
            <a href="{{ route('produksi.benang.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Merge</th>
              <th class="text-end">Denier</th>
              <th class="text-end">Filamen</th>
              <th class="text-end">Toleransi</th>
              <th>Warna PPT</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($benang as $b) {{-- ulangi setiap benang --}}
              <tr>
                <td class="font-weight-bold">{{ $b->merge }}</td>
                <td class="text-end">{{ $b->denier }}</td>
                <td class="text-end">{{ $b->filamen }}</td>
                <td class="text-end">± {{ number_format($b->toleransi_denier, 2, ',', '.') }}%</td> {{-- contoh: ± 3,00% --}}
                <td>{{ $b->warna_ppt ?? '-' }}</td>
                <td>
                  @if ($b->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('produksi.benang.edit'))
                    <a href="{{ route('produksi.benang.edit', $b->merge) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.benang.status'))
                    @include('admin::partials.tombol-status', ['url' => route('produksi.benang.status', $b->merge), 'status' => $b->status_aktif]) {{-- tombol aktif/nonaktif yang sama dengan data master lain --}}
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Data benang tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $benang->count() }} dari {{ $benang->total() }} benang</small>
        {{ $benang->links() }}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

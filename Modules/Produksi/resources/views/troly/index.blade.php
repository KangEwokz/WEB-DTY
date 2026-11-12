@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Master Troly') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .badge-kuning { background: #fff4de; color: #b7791f; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label kuning untuk jenis Abnormal */
  .badge-biru { background: #eaf3ff; color: #248afd; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label biru untuk jenis Normal */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Master Troly</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Troly Normal untuk hasil doffing, troly Abnormal untuk menyimpan bobbin abnormal.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.troly.create'))
      <a href="{{ route('produksi.troly.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Troly</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.troly.index') }}" method="GET" class="row g-2 mb-4"> {{-- filter jenis & status --}}
        <div class="col-md-3">
          <select name="jenis" class="form-control">
            <option value="">Semua jenis</option>
            @foreach (['Normal', 'Abnormal'] as $j)
              <option value="{{ $j }}" {{ request('jenis') == $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Semua status</option>
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
          @if (request('jenis') || request('status'))
            <a href="{{ route('produksi.troly.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Kode Troly</th>
              <th>Jenis</th>
              <th class="text-end">Kapasitas</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($troly as $t) {{-- ulangi setiap troly --}}
              <tr>
                <td class="font-weight-bold">{{ $t->kode_troly }}</td>
                <td>
                  @if ($t->jenis == 'Normal')
                    <span class="badge-biru">Normal</span>
                  @else
                    <span class="badge-kuning">Abnormal</span>
                  @endif
                </td>
                <td class="text-end">{{ $t->kapasitas }} bobbin</td>
                <td>
                  @if ($t->status_aktif == 'Aktif')
                    <span class="badge-aktif">Aktif</span>
                  @else
                    <span class="badge-nonaktif">Non-Aktif</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('produksi.troly.edit'))
                    <a href="{{ route('produksi.troly.edit', $t->kode_troly) }}" class="btn btn-sm btn-outline-primary" title="Ubah Kapasitas"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.troly.status'))
                    @include('admin::partials.tombol-status', ['url' => route('produksi.troly.status', $t->kode_troly), 'status' => $t->status_aktif])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Data troly tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $troly->count() }} dari {{ $troly->total() }} troly</small>
        {{ $troly->links() }}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

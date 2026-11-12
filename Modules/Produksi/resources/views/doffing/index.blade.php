@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Doffing') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .kotak-angka { border: 1px solid #eef0f4; border-radius: 10px; padding: .8rem 1rem; text-align: center; background: #fff; } /* kotak ringkasan */
  .kotak-angka .angka { font-size: 1.4rem; font-weight: 700; color: #248afd; } /* angka besar biru */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Doffing</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Troly hasil doffing per shift. Nantinya diperiksa oleh QCT.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.doffing.create'))
      <a href="{{ route('produksi.doffing.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Doffing</a>
    @endif
  </div>

  <div class="row mb-4"> {{-- ringkasan total sesuai filter --}}
    <div class="col-4"><div class="kotak-angka"><div class="angka">{{ $total->troly }}</div><small class="text-muted">Troly (doffing)</small></div></div>
    <div class="col-4"><div class="kotak-angka"><div class="angka">{{ $total->bobbin ?? 0 }}</div><small class="text-muted">Bobbin</small></div></div> {{-- ?? 0 = 0 jika belum ada data --}}
    <div class="col-4"><div class="kotak-angka"><div class="angka">{{ number_format($total->berat ?? 0, 2, ',', '.') }}</div><small class="text-muted">Berat (kg)</small></div></div>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.doffing.index') }}" method="GET" class="row g-2 mb-4"> {{-- filter tanggal & shift --}}
        <div class="col-md-3">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
        </div>
        <div class="col-md-3">
          <select name="shift" class="form-control">
            <option value="">Semua shift</option>
            @foreach (['S1', 'S2', 'S3'] as $s)
              <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
          @if (request('tanggal') || request('shift'))
            <a href="{{ route('produksi.doffing.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID Doffing</th>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Troly</th>
              <th>Merge</th>
              <th class="text-end">Bobbin</th>
              <th class="text-end">Berat</th>
              <th>Dicatat Oleh</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($doffing as $d) {{-- ulangi setiap doffing --}}
              <tr>
                <td class="font-weight-bold">{{ $d->id_doffing }}</td>
                <td>{{ date('d-m-Y', strtotime($d->tgl_doffing)) }}</td>
                <td>{{ $d->shift }}</td>
                <td>{{ $d->kode_troly }}</td>
                <td>{{ $d->merge }}</td>
                <td class="text-end">{{ $d->jml_bobbin }}</td>
                <td class="text-end">{{ number_format($d->berat_troly, 2, ',', '.') }} kg</td>
                <td>{{ $d->nama_spv }}</td>
                <td class="text-end text-nowrap">
                  @if (auth()->user()->punyaAkses('produksi.doffing.edit'))
                    <a href="{{ route('produksi.doffing.edit', $d->id_doffing) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.doffing.destroy'))
                    <form action="{{ route('produksi.doffing.destroy', $d->id_doffing) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus doffing ini?')"> {{-- form hapus + konfirmasi --}}
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="ti-trash"></i></button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">Belum ada data doffing.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $doffing->count() }} dari {{ $doffing->total() }} doffing</small>
        {{ $doffing->links() }}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

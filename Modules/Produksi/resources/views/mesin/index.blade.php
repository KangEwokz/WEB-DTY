@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Master Mesin') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .badge-kuning { background: #fff4de; color: #b7791f; padding: .4rem .8rem; border-radius: 20px; font-weight: 500; } /* label kuning untuk Maintenance */
  .kotak-angka { border: 1px solid #eef0f4; border-radius: 10px; padding: .8rem 1rem; text-align: center; background: #fff; } /* kotak ringkasan */
  .kotak-angka .angka { font-size: 1.4rem; font-weight: 700; } /* angka besar */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Master Mesin</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Kode mesin tetap, sedangkan line dan posisinya bisa dipindah.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.mesin.create')) {{-- tombol hanya jika punya akses tambah --}}
      <a href="{{ route('produksi.mesin.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Mesin</a>
    @endif
  </div>

  <div class="row mb-4"> {{-- ringkasan jumlah mesin per status --}}
    @foreach (['Running' => 'text-success', 'Stop' => 'text-danger', 'Maintenance' => 'text-warning'] as $s => $warna) {{-- status => warna angka --}}
      <div class="col-4">
        <div class="kotak-angka">
          <div class="angka {{ $warna }}">{{ $jumlah[$s] ?? 0 }}</div> {{-- jumlah mesin dengan status ini --}}
          <small class="text-muted">{{ $s }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.mesin.index') }}" method="GET" class="row g-2 mb-4"> {{-- filter line & status --}}
        <div class="col-md-3">
          <select name="line" class="form-control">
            <option value="">Semua line</option>
            @foreach (['L1', 'L2', 'L3'] as $l) {{-- daftar line --}}
              <option value="{{ $l }}" {{ request('line') == $l ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Semua status</option>
            @foreach (['Running', 'Stop', 'Maintenance'] as $s)
              <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
          @if (request('line') || request('status'))
            <a href="{{ route('produksi.mesin.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Kode Mesin</th>
              <th>Line</th>
              <th>Posisi</th>
              <th>Status</th>
              <th>Terakhir Diubah</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($mesin as $m) {{-- ulangi setiap mesin --}}
              <tr>
                <td class="font-weight-bold">{{ $m->kode_mesin }}</td>
                <td>{{ $m->line }}</td>
                <td>{{ $m->posisi }}</td>
                <td>
                  @if ($m->status == 'Running')
                    <span class="badge-aktif">Running</span>
                  @elseif ($m->status == 'Stop')
                    <span class="badge-nonaktif">Stop</span>
                  @else
                    <span class="badge-kuning">Maintenance</span>
                  @endif
                </td>
                <td>
                  @if ($m->nama_pengubah) {{-- jika pernah diubah --}}
                    {{ $m->nama_pengubah }} <br><small class="text-muted">{{ date('d-m-Y H:i', strtotime($m->updated_at)) }}</small>
                  @else
                    -
                  @endif
                </td>
                <td class="text-end">
                  @if (auth()->user()->punyaAkses('produksi.mesin.edit'))
                    <a href="{{ route('produksi.mesin.edit', $m->kode_mesin) }}" class="btn btn-sm btn-outline-primary" title="Ubah / Pindah Mesin"><i class="ti-pencil"></i></a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Data mesin tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $mesin->count() }} dari {{ $mesin->total() }} mesin</small>
        {{ $mesin->links() }}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

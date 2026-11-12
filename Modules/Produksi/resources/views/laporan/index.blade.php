@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Laporan Shift') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Laporan Akhir Shift</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Total output produksi per shift.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.laporan.create')) {{-- tombol hanya untuk yang boleh mencatat --}}
      <a href="{{ route('produksi.laporan.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Isi Laporan</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.laporan.index') }}" method="GET" class="row g-2 mb-4"> {{-- filter tanggal --}}
        <div class="col-md-4">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
          @if (request('tanggal'))
            <a href="{{ route('produksi.laporan.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Shift</th>
              <th class="text-end">Doffing</th> {{-- angka rata kanan agar mudah dibandingkan --}}
              <th class="text-end">Break</th>
              <th class="text-end">Problem</th>
              <th class="text-end">Bleeding</th>
              <th class="text-end">Suspend</th>
              <th class="text-end">Waste</th>
              <th class="text-end">Abnormal</th>
              <th>Dicatat Oleh</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($laporan as $l) {{-- ulangi setiap laporan --}}
              <tr>
                <td class="font-weight-bold">{{ date('d-m-Y', strtotime($l->tgl_laporan)) }}</td>
                <td>{{ $l->shift }}</td>
                <td class="text-end">{{ $l->jml_doffing }}</td>
                <td class="text-end">{{ $l->jml_break }}</td>
                <td class="text-end">{{ $l->jml_problem_mesin }}</td>
                <td class="text-end">{{ $l->bleeding_time }} mnt</td>
                <td class="text-end">{{ $l->suspend_time }} mnt</td>
                <td class="text-end">{{ number_format($l->waste, 2, ',', '.') }} kg</td> {{-- number_format = 12.5 jadi 12,50 --}}
                <td class="text-end">{{ $l->jml_abnormal }} bob</td> {{-- total bobbin abnormal --}}
                <td>{{ $l->nama_spv }}</td>
                <td class="text-end text-nowrap">
                  <a href="{{ route('produksi.laporan.show', $l->id_laporan) }}" class="btn btn-sm btn-outline-info" title="Lihat"><i class="ti-eye"></i></a>
                  @if (auth()->user()->punyaAkses('produksi.laporan.edit'))
                    <a href="{{ route('produksi.laporan.edit', $l->id_laporan) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.laporan.destroy'))
                    <form action="{{ route('produksi.laporan.destroy', $l->id_laporan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus laporan shift ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="ti-trash"></i></button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-muted py-4">Belum ada laporan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $laporan->count() }} dari {{ $laporan->total() }} laporan</small>
        {{ $laporan->links() }}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

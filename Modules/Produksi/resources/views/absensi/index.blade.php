@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Absensi Produksi') {{-- judul tab browser --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div>
      <h4 class="font-weight-bold mb-1">Absensi & Penempatan</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Kehadiran dan area kerja operator produksi per shift.</p>
    </div>
    @if (auth()->user()->punyaAkses('produksi.absensi.create')) {{-- tombol hanya untuk yang boleh mencatat --}}
      <a href="{{ route('produksi.absensi.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Isi Absensi</a>
    @endif
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('produksi.absensi.index') }}" method="GET" class="row g-2 mb-4"> {{-- filter tanggal --}}
        <div class="col-md-4">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control"> {{-- request('tanggal') = isi filter sebelumnya --}}
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="ti-filter"></i> Filter</button>
          @if (request('tanggal'))
            <a href="{{ route('produksi.absensi.index') }}" class="btn btn-light">Reset</a>
          @endif
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Dicatat Oleh</th>
              <th>Hadir</th>
              <th>Tidak Hadir</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($absensi as $a) {{-- ulangi setiap absensi shift --}}
              <tr>
                <td class="font-weight-bold">{{ date('d-m-Y', strtotime($a->tgl_absen)) }}</td> {{-- ubah 2026-11-12 jadi 12-11-2026 --}}
                <td>{{ $a->shift }}</td>
                <td>{{ $a->nama_spv }}</td>
                <td><span class="badge-aktif">{{ $a->jml_hadir }}</span></td> {{-- jumlah hadir (hijau) --}}
                <td>
                  @if ($a->jml_tidak > 0) {{-- merah hanya jika ada yang tidak hadir --}}
                    <span class="badge-nonaktif">{{ $a->jml_tidak }}</span>
                  @else
                    0
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  <a href="{{ route('produksi.absensi.show', $a->id_absensi) }}" class="btn btn-sm btn-outline-info" title="Lihat"><i class="ti-eye"></i></a> {{-- semua yang bisa membuka daftar boleh melihat detail --}}
                  @if (auth()->user()->punyaAkses('produksi.absensi.edit'))
                    <a href="{{ route('produksi.absensi.edit', $a->id_absensi) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a>
                  @endif
                  @if (auth()->user()->punyaAkses('produksi.absensi.destroy'))
                    <form action="{{ route('produksi.absensi.destroy', $a->id_absensi) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus absensi shift ini?')"> {{-- form hapus + konfirmasi --}}
                      @csrf
                      @method('DELETE') {{-- ditandai sebagai DELETE --}}
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="ti-trash"></i></button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data absensi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
        <small class="text-muted">Menampilkan {{ $absensi->count() }} dari {{ $absensi->total() }} absensi</small>
        {{ $absensi->links() }} {{-- link halaman --}}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

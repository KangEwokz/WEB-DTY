@extends('auth::layouts.app') {{-- pakai layout utama --}}

@section('title', 'Detail Laporan Shift') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .kotak-angka { border: 1px solid #eef0f4; border-radius: 10px; padding: 1rem; background: #fff; height: 100%; } /* kotak satu angka output */
  .kotak-angka .label { font-size: .8rem; color: #a3a4a5; } /* tulisan kecil di atas angka */
  .kotak-angka .angka { font-size: 1.6rem; font-weight: 700; color: #282f3a; } /* angka besar */
  .kotak-angka .satuan { font-size: .85rem; color: #a3a4a5; font-weight: 400; } /* satuan di samping angka */
</style>
@endpush

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul --}}
    <div>
      <h4 class="font-weight-bold mb-1">Laporan {{ date('d-m-Y', strtotime($laporan->tgl_laporan)) }} · Shift {{ substr($laporan->shift, 1) }}</h4>
      <p class="text-muted mb-0">
        Dicatat oleh {{ $laporan->nama_spv }}
        @if ($laporan->nama_pengubah) {{-- tampil hanya jika pernah diubah --}}
          · diubah oleh {{ $laporan->nama_pengubah }} ({{ date('d-m-Y H:i', strtotime($laporan->updated_at)) }})
        @endif
      </p>
    </div>
    <div class="d-flex gap-2">
      @if (auth()->user()->punyaAkses('produksi.laporan.edit'))
        <a href="{{ route('produksi.laporan.edit', $laporan->id_laporan) }}" class="btn btn-primary"><i class="ti-pencil me-1"></i> Ubah</a>
      @endif
      <a href="{{ route('produksi.laporan.index') }}" class="btn btn-light">Kembali</a>
    </div>
  </div>

  <div class="row"> {{-- kotak-kotak angka output --}}
    @foreach ([
      ['Doffing', $laporan->jml_doffing, 'troly'],
      ['Break', $laporan->jml_break, 'putus'],
      ['Problem Mesin', $laporan->jml_problem_mesin, 'kejadian'],
      ['Bleeding Time', $laporan->bleeding_time, 'menit'],
      ['Suspend Time', $laporan->suspend_time, 'menit'],
      ['Waste', number_format($laporan->waste, 2, ',', '.'), 'kg'],
      ['Benang Abnormal', $laporan->jml_abnormal, 'bobbin'],
    ] as $kotak) {{-- daftar kotak: [label, angka, satuan] --}}
      <div class="col-6 col-md-3 mb-4"> {{-- 2 kotak per baris di HP, 4 di layar lebar --}}
        <div class="kotak-angka">
          <div class="label">{{ $kotak[0] }}</div> {{-- [0] = isi pertama (label) --}}
          <div class="angka">{{ $kotak[1] }} <span class="satuan">{{ $kotak[2] }}</span></div> {{-- angka + satuan --}}
        </div>
      </div>
    @endforeach
  </div>

  <div class="row">
    <div class="col-md-8 grid-margin stretch-card"> {{-- kartu keterangan --}}
      <div class="card">
        <div class="card-body">
          <p class="card-title">Keterangan Problem Mesin</p>
          <p class="mb-4" style="white-space: pre-line;">{{ $laporan->ket_problem_mesin ?: '-' }}</p> {{-- pre-line = baris baru (Enter) tetap tampil; ?: '-' = tampil '-' jika kosong --}}
          <p class="card-title">Catatan Supervisor</p>
          <p class="mb-0" style="white-space: pre-line;">{{ $laporan->catatan ?: '-' }}</p>
        </div>
      </div>
    </div>

    <div class="col-md-4 grid-margin stretch-card"> {{-- kartu ringkasan kehadiran shift yang sama --}}
      <div class="card">
        <div class="card-body">
          <p class="card-title">Kehadiran Shift Ini</p>
          @if ($absensi->isEmpty()) {{-- jika absensi shift ini belum diisi --}}
            <p class="text-muted mb-0">Absensi shift ini belum diisi.</p>
          @else
            @foreach (['Hadir', 'Sakit', 'Ijin', 'Cuti', 'Alfa'] as $k) {{-- tampilkan jumlah setiap kehadiran --}}
              <div class="d-flex justify-content-between py-1">
                <span>{{ $k }}</span>
                <b>{{ $absensi[$k] ?? 0 }}</b> {{-- jumlah, 0 jika tidak ada --}}
              </div>
            @endforeach
            @if (auth()->user()->punyaAkses('produksi.absensi.show'))
              <a href="{{ route('produksi.absensi.show', $id_absensi) }}" class="btn btn-sm btn-outline-primary mt-3">Lihat Absensi</a> {{-- ke detail absensi shift yang sama --}}
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4"> {{-- kartu daftar troly doffing shift ini --}}
    <div class="card-body">
      <p class="card-title">Doffing Shift Ini ({{ $doffing->count() }} troly)</p>
      @if ($doffing->isEmpty()) {{-- jika belum ada doffing --}}
        <p class="text-muted mb-0">Belum ada doffing di shift ini. Isi lewat menu Produksi → Doffing.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID Doffing</th>
                <th>Troly</th>
                <th>Merge</th>
                <th class="text-end">Bobbin</th>
                <th class="text-end">Berat</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($doffing as $d) {{-- ulangi setiap troly doffing --}}
                <tr>
                  <td>{{ $d->id_doffing }}</td>
                  <td>{{ $d->kode_troly }}</td>
                  <td>{{ $d->merge }}</td>
                  <td class="text-end">{{ $d->jml_bobbin }}</td>
                  <td class="text-end">{{ number_format($d->berat_troly, 2, ',', '.') }} kg</td>
                </tr>
              @endforeach
              <tr class="font-weight-bold"> {{-- baris total --}}
                <td colspan="3">Total</td>
                <td class="text-end">{{ $doffing->sum('jml_bobbin') }}</td> {{-- sum = jumlahkan kolom --}}
                <td class="text-end">{{ number_format($doffing->sum('berat_troly'), 2, ',', '.') }} kg</td>
              </tr>
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  <div class="card"> {{-- kartu temuan benang abnormal --}}
    <div class="card-body">
      <p class="card-title">Temuan Benang Abnormal ({{ $laporan->jml_abnormal }} bobbin)</p>

      @if ($abnormal->isEmpty()) {{-- jika tidak ada temuan --}}
        <p class="text-muted mb-0">Tidak ada temuan abnormal di shift ini.</p>
      @else
        <div class="row mb-3"> {{-- rekap per jenis & per troly --}}
          <div class="col-md-6 mb-3">
            <b class="d-block mb-2">Per jenis abnormal</b>
            @foreach ($rekap_jenis as $nama => $jumlah) {{-- contoh: Hairy 3 --}}
              <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $nama }}</span><b>{{ $jumlah }}</b></div>
            @endforeach
          </div>
          <div class="col-md-6 mb-3">
            <b class="d-block mb-2">Per troly abnormal</b>
            @foreach ($rekap_troly as $kode => $jumlah) {{-- contoh: TRA-001 5 / 20 --}}
              <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $kode }}</span><b>{{ $jumlah }} / {{ $kapasitas[$kode] }} bobbin</b></div> {{-- isi / kapasitas --}}
            @endforeach
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Mesin Asal</th>
                <th>Merge</th>
                <th>Jenis Abnormal</th>
                <th>Troly</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($abnormal as $a) {{-- ulangi setiap bobbin --}}
                <tr>
                  <td>{{ $a->no_bobbin }}</td>
                  <td>{{ $a->kode_mesin }} <small class="text-muted">({{ $a->line }}-{{ $a->posisi }})</small></td> {{-- lokasi mesin SAAT INI --}}
                  <td>{{ $a->merge }}</td>
                  <td>{{ $a->nm_abnormal }}</td>
                  <td>{{ $a->kode_troly }}</td>
                  <td>{{ $a->keterangan ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

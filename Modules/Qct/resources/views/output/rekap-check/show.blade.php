@extends('auth::layouts.app')

@section('title', 'Rekap Check QCT '.\Carbon\Carbon::parse($periode.'-01')->translatedFormat('F Y'))

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="card-title mb-1">{{ $id }}</p>
              @if ($rekap)
                <label class="badge badge-outline-success">Terverifikasi</label>
                <span class="text-muted ms-2">oleh {{ $rekap->verifikator }}, {{ date('d-m-Y H:i', strtotime($rekap->waktu_verifikasi)) }}</span>
              @else
                <label class="badge badge-outline-warning">Belum diverifikasi</label>
              @endif
            </div>
            <div>
              @if ($rekap && $rekap->file_pdf)
                <a href="{{ asset('storage/'.$rekap->file_pdf) }}" target="_blank" class="btn btn-inverse-primary btn-sm"><i class="ti-download"></i> Arsip PDF</a>
              @endif
              @if (! $rekap)
                <form method="POST" action="{{ route('qct.rekap-check.verifikasi', $id) }}" class="d-inline"
                  onsubmit="return confirm('Verifikasi dan arsipkan rekap bulan ini?')">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm" {{ $ringkasan['shift_belum'] > 0 ? 'disabled' : '' }}>
                    <i class="ti-check"></i> Verifikasi & Arsipkan
                  </button>
                </form>
              @endif
            </div>
          </div>
          @if (! $rekap && $ringkasan['shift_belum'] > 0)
            <div class="alert alert-warning mt-3 mb-0">
              {{ $ringkasan['shift_belum'] }} dari {{ $ringkasan['shift_total'] }} laporan shift belum diverifikasi HOD.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Ringkasan angka (dihitung, tidak disimpan) --}}
  <div class="row">
    @foreach ([
      'Troly' => $ringkasan['troly'],
      'Bobbin' => $ringkasan['bobbin'],
      'Bobbin Lolos' => $ringkasan['lolos'],
      'Bobbin Abnormal' => $ringkasan['abnormal'],
      'Troly Visual NG' => $ringkasan['visual_ng'],
      'Troly Denier NG' => $ringkasan['denier_ng'],
      'Packing / Hold' => $ringkasan['packing'].' / '.$ringkasan['hold'],
      'Sampel OK / Total' => $ringkasan['sampel_ok'].' / '.$ringkasan['sampel_total'],
    ] as $judul => $nilai)
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <p class="card-title text-md-left">{{ $judul }}</p>
            <h3 class="font-weight-bold mb-0">{{ $nilai }}</h3>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Rekap per Tanggal dan Shift</p>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Shift</th>
                  <th>Troly</th>
                  <th>Bobbin</th>
                  <th>Abnormal</th>
                  <th>Lolos</th>
                  <th>Laporan HOD</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($per_shift as $s)
                  <tr>
                    <td>{{ date('d-m-Y', strtotime($s->tgl_doffing)) }}</td>
                    <td>{{ $s->shift }}</td>
                    <td>{{ $s->jml_troly }}</td>
                    <td>{{ $s->jml_bobbin }}</td>
                    <td>{{ $s->jml_abnormal }}</td>
                    <td>{{ $s->jml_bobbin - $s->jml_abnormal }}</td>
                    <td>
                      @if ($s->status == 'Terverifikasi')
                        <label class="badge badge-outline-success">Terverifikasi</label>
                      @else
                        <label class="badge badge-outline-warning">Belum</label>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Abnormal per Jenis</p>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Packing</th>
                <th>Cutting</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($abnormal_jenis as $a)
                <tr>
                  <td>{{ $a->nm_abnormal }}</td>
                  <td>{{ $a->jumlah }}</td>
                  <td>{{ $a->packing }}</td>
                  <td>{{ $a->cutting }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center">Tidak ada bobbin abnormal.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Troly per Grade</p>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Grade</th>
                <th>Jumlah Troly</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($grade as $g)
                <tr>
                  <td>{{ $g->id_grade }}</td>
                  <td>{{ $g->jumlah }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center">Belum ada grade.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <a href="{{ route('qct.rekap-check.index') }}" class="btn btn-light">Kembali</a>
@endsection

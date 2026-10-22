@extends('auth::layouts.app')

@section('title', 'Laporan Check '.date('d-m-Y', strtotime($tanggal)).' Shift '.$shift)

@section('content')
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="card-title mb-1">{{ $id }}</p>
              @if ($laporan)
                <label class="badge badge-outline-success">Terverifikasi</label>
                <span class="text-muted ms-2">oleh {{ $laporan->verifikator }}, {{ date('d-m-Y H:i', strtotime($laporan->waktu_verifikasi)) }}</span>
              @else
                <label class="badge badge-outline-warning">Belum diverifikasi</label>
              @endif
            </div>
            @if (! $laporan)
              <form method="POST" action="{{ route('qct.laporan-check.verifikasi', $id) }}"
                onsubmit="return confirm('Verifikasi laporan ini? Data tanggal dan shift ini akan terkunci.')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" {{ count($kendala) > 0 ? 'disabled' : '' }}>
                  <i class="ti-check"></i> Verifikasi
                </button>
              </form>
            @endif
          </div>
          @if (! $laporan && count($kendala) > 0)
            <div class="alert alert-warning mt-3 mb-0">Belum bisa diverifikasi: {{ implode(', ', $kendala) }}.</div>
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
      'Visual OK / NG' => $ringkasan['visual_ok'].' / '.$ringkasan['visual_ng'],
      'Denier OK / NG' => $ringkasan['denier_ok'].' / '.$ringkasan['denier_ng'],
      'Packing / Hold' => $ringkasan['packing'].' / '.$ringkasan['hold'],
      'Sampel OK / NG' => $ringkasan['sampel_ok'].' / '.$ringkasan['sampel_ng'],
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
          <p class="card-title">Troly</p>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Troly</th>
                  <th>Merge</th>
                  <th>Denier</th>
                  <th>Filamen</th>
                  <th>Warna PPT</th>
                  <th>Bobbin</th>
                  <th>Abnormal</th>
                  <th>Lolos</th>
                  <th>Visual</th>
                  <th>Hasil Denier</th>
                  <th>Grade</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($troly as $t)
                  <tr>
                    <td>{{ $t->nmr_troly }}</td>
                    <td>{{ $t->merge }}</td>
                    <td>{{ $t->denier }}</td>
                    <td>{{ $t->filamen }}</td>
                    <td>{{ $t->warna_ppt ?? '-' }}</td>
                    <td>{{ $t->jml_bobbin }}</td>
                    <td>{{ $t->jml_abnormal }}</td>
                    <td>{{ $t->jml_bobbin - $t->jml_abnormal }}</td>
                    <td>{{ $t->hasil_visual ?? 'Belum' }}</td>
                    <td>{{ $t->hasil_denier ?? 'Belum' }}</td>
                    <td>{{ $t->id_grade ?? '-' }}</td>
                    <td>{{ $t->status ?? '-' }}</td>
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
                <th>Pending</th>
                <th>Packing</th>
                <th>Cutting</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($abnormal_jenis as $a)
                <tr>
                  <td>{{ $a->nm_abnormal }}</td>
                  <td>{{ $a->jumlah }}</td>
                  <td>{{ $a->pending }}</td>
                  <td>{{ $a->packing }}</td>
                  <td>{{ $a->cutting }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">Tidak ada bobbin abnormal.</td>
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
          <p class="card-title">Sampel</p>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Merge</th>
                  <th>Denier</th>
                  <th>Filamen</th>
                  <th>Warna PPT</th>
                  <th>Hasil</th>
                  <th>Act. Denier</th>
                  <th>Elong</th>
                  <th>Tenacity</th>
                  <th>DTI</th>
                  <th>Grade</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($sampel as $s)
                  <tr>
                    <td>{{ $s->merge }}</td>
                    <td>{{ $s->denier }}</td>
                    <td>{{ $s->filamen }}</td>
                    <td>{{ $s->warna_ppt ?? '-' }}</td>
                    <td>{{ $s->nm_abnormal ?? 'OK' }}</td>
                    <td>{{ $s->act_denier ?? '-' }}</td>
                    <td>{{ $s->elong ?? '-' }}</td>
                    <td>{{ $s->tenacity ?? '-' }}</td>
                    <td>{{ $s->dti ?? '-' }}</td>
                    <td>{{ $s->id_grade ?? '-' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="10" class="text-center">Belum ada sampel.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <a href="{{ route('qct.laporan-check.index') }}" class="btn btn-light">Kembali</a>
@endsection

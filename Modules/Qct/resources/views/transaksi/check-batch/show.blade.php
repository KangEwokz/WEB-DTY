@extends('auth::layouts.app')

@section('title', 'Detail Cek Troly')

@section('content')
  @php $super = auth()->user()->hasRole('superadmin'); @endphp {{-- superadmin (demo) boleh aksi Visual & Lab --}}
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Troly {{ $check->nmr_troly }}</p>
          @if ($terkunci)
            <div class="alert alert-warning">Data tanggal dan shift ini sudah diverifikasi HOD dan terkunci.</div>
          @endif
          <table class="table">
            <tr>
              <th style="width: 180px">ID Check</th>
              <td>{{ $check->id_check }}</td>
            </tr>
            <tr>
              <th>ID Doffing</th>
              <td>{{ $check->id_doffing }}</td>
            </tr>
            <tr>
              <th>Tanggal / Shift</th>
              <td>{{ date('d-m-Y', strtotime($check->tgl_doffing)) }} / {{ $check->shift }}</td>
            </tr>
            <tr>
              <th>Merge</th>
              <td>@include('auth::layouts.partials.info-benang', ['baris' => $check])</td>
            </tr>
            <tr>
              <th>Bobbin</th>
              <td>{{ $check->jml_bobbin }} (lolos {{ $jml_lolos }}, abnormal {{ count($abnormal) }})</td>
            </tr>
            <tr>
              <th>Hasil Visual</th>
              <td>{{ $check->hasil_visual }} <small class="text-muted">oleh {{ $check->nama_visual ?? '-' }}</small></td>
            </tr>
            <tr>
              <th>Hasil Denier</th>
              <td>{{ $check->hasil_denier ?? 'Belum dicek' }} <small class="text-muted">oleh {{ $check->nama_lab ?? '-' }}</small></td>
            </tr>
            <tr>
              <th>Grade</th>
              <td>{{ $check->id_grade ?? '-' }}</td>
            </tr>
            <tr>
              <th>Status Troly</th>
              <td>{{ $check->status ?? '-' }}</td>
            </tr>
          </table>
          <div class="mt-3">
            @if (! $terkunci && ($area == 'VIS' || $super))
              <a href="{{ route('qct.check-batch.edit', $check->id_check) }}" class="btn btn-inverse-primary btn-sm">Ubah Visual</a>
              <form method="POST" action="{{ route('qct.check-batch.destroy', $check->id_check) }}" class="d-inline"
                onsubmit="return confirm('Hapus hasil cek troly ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
              </form>
            @endif
            @if (! $terkunci && ($area == 'LAB' || $super))
              <a href="{{ route('qct.check-batch.denier', $check->id_check) }}" class="btn btn-inverse-primary btn-sm">Cek Denier</a>
            @endif
            <a href="{{ route('qct.check-batch.index') }}" class="btn btn-light btn-sm">Kembali</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="card-title mb-0">Bobbin Abnormal</p>
            @if (! $terkunci && ($area == 'VIS' || $super))
              <a href="{{ route('qct.abnormal.create', ['id_doffing' => $check->id_doffing]) }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
            @endif
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Mesin</th>
                  <th>Jenis</th>
                  <th>Grade</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($abnormal as $a)
                  <tr>
                    <td>{{ $a->id_abnormal }}</td>
                    <td>{{ $a->kode_mesin }}</td>
                    <td>{{ $a->nm_abnormal }}</td>
                    <td>{{ $a->id_grade ?? '-' }}</td>
                    <td>{{ $a->status }}</td>
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
    </div>
  </div>
@endsection

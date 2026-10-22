@extends('auth::layouts.app')

@section('title', 'Cek Troly (Visual & Denier)')

@section('content')
  @php $super = auth()->user()->hasRole('superadmin'); @endphp {{-- superadmin (demo) boleh aksi Visual & Lab --}}
  <div class="card">
    <div class="card-body">
      <p class="card-description">
        Area kerja Anda: <strong>{{ $area == 'VIS' ? 'Visual' : ($area == 'LAB' ? 'Lab' : '-') }}</strong>.
        Operator Visual mengisi hasil visual, operator Lab mengisi hasil denier, grade, dan status troly.
      </p>
      <form method="GET" action="{{ route('qct.check-batch.index') }}" class="d-flex flex-wrap mb-3">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
        <select name="shift" class="form-control me-2 mb-2" style="width: 140px">
          <option value="">Semua shift</option>
          @foreach (['S1', 'S2', 'S3'] as $s)
            <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
        <select name="tahap" class="form-control me-2 mb-2" style="width: 190px">
          <option value="">Semua tahap</option>
          <option value="belum" {{ request('tahap') == 'belum' ? 'selected' : '' }}>Belum cek visual</option>
          <option value="lab" {{ request('tahap') == 'lab' ? 'selected' : '' }}>Menunggu cek denier</option>
          <option value="selesai" {{ request('tahap') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
      </form>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Troly</th>
              <th>Merge</th>
              <th>Denier</th>
              <th>Filamen</th>
              <th>Warna PPT</th>
              <th>Bobbin</th>
              <th>Abnormal</th>
              <th>Visual</th>
              <th>Hasil Denier</th>
              <th>Grade</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($troly as $t)
              <tr>
                <td>{{ date('d-m-Y', strtotime($t->tgl_doffing)) }}</td>
                <td>{{ $t->shift }}</td>
                <td>{{ $t->nmr_troly }}</td>
                <td>{{ $t->merge }}</td>
                <td>{{ $t->denier }}</td>
                <td>{{ $t->filamen }}</td>
                <td>{{ $t->warna_ppt ?? '-' }}</td>
                <td>{{ $t->jml_bobbin }}</td>
                <td>{{ $t->jml_abnormal }}</td>
                <td>{{ $t->hasil_visual ?? '-' }}</td>
                <td>{{ $t->hasil_denier ?? '-' }}</td>
                <td>{{ $t->id_grade ?? '-' }}</td>
                <td>{{ $t->status ?? '-' }}</td>
                <td>
                  @if (! $t->id_check)
                    @if ($area == 'VIS' || $super)
                      <a href="{{ route('qct.check-batch.create', ['id_doffing' => $t->id_doffing]) }}" class="btn btn-inverse-primary btn-sm">Cek Visual</a>
                    @else
                      <label class="badge badge-outline-warning">Belum dicek visual</label>
                    @endif
                  @else
                    <a href="{{ route('qct.check-batch.show', $t->id_check) }}" class="btn btn-inverse-info btn-sm">Detail</a>
                    @if (($area == 'LAB' || $super) && ! sudah_terkunci($t->tgl_doffing, $t->shift))
                      <a href="{{ route('qct.check-batch.denier', $t->id_check) }}" class="btn btn-inverse-primary btn-sm">Cek Denier</a>
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="14" class="text-center">Belum ada data troly.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $troly->links() }}</div>
    </div>
  </div>
@endsection

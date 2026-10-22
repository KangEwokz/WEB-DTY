@extends('auth::layouts.app')

@section('title', 'Data Abnormal Benang')

@section('content')
  @php $super = auth()->user()->hasRole('superadmin'); @endphp {{-- superadmin (demo) boleh aksi Visual & Lab --}}
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('qct.abnormal.index') }}" class="d-flex flex-wrap">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
          <select name="shift" class="form-control me-2 mb-2" style="width: 140px">
            <option value="">Semua shift</option>
            @foreach (['S1', 'S2', 'S3'] as $s)
              <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <select name="status" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua status</option>
            @foreach (['Pending', 'Packing', 'Cutting'] as $s)
              <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        @if ($area == 'VIS' || $super)
          <a href="{{ route('qct.abnormal.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
        @endif
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID Abnormal</th>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Troly</th>
              <th>Mesin</th>
              <th>Jenis</th>
              <th>Denier</th>
              <th>Grade</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($abnormal as $a)
              <tr>
                <td>{{ $a->id_abnormal }}</td>
                <td>{{ date('d-m-Y', strtotime($a->tgl_doffing)) }}</td>
                <td>{{ $a->shift }}</td>
                <td>{{ $a->nmr_troly }}</td>
                <td>{{ $a->kode_mesin }}</td>
                <td>{{ $a->nm_abnormal }}</td>
                <td>{{ $a->act_denier ?? '-' }}</td>
                <td>{{ $a->id_grade ?? '-' }}</td>
                <td>
                  <label class="badge {{ $a->status == 'Pending' ? 'badge-outline-warning' : ($a->status == 'Packing' ? 'badge-outline-success' : 'badge-outline-danger') }}">{{ $a->status }}</label>
                </td>
                <td>
                  @if (sudah_terkunci($a->tgl_doffing, $a->shift))
                    <label class="badge badge-outline-dark">Terkunci</label>
                  @else
                    @if (($area == 'VIS' || $super) && $a->status == 'Pending')
                      <a href="{{ route('qct.abnormal.edit', $a->id_abnormal) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                      <form method="POST" action="{{ route('qct.abnormal.destroy', $a->id_abnormal) }}" class="d-inline"
                        onsubmit="return confirm('Hapus data abnormal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
                      </form>
                    @endif
                    @if ($area == 'LAB' || $super)
                      <a href="{{ route('qct.abnormal.lab', $a->id_abnormal) }}" class="btn btn-inverse-primary btn-sm">Uji Lab</a>
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $abnormal->links() }}</div>
    </div>
  </div>
@endsection

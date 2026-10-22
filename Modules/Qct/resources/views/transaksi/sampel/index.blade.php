@extends('auth::layouts.app')

@section('title', 'Data Sampel')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('qct.sampel.index') }}" class="d-flex flex-wrap">
          <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control me-2 mb-2" style="width: 180px">
          <select name="shift" class="form-control me-2 mb-2" style="width: 140px">
            <option value="">Semua shift</option>
            @foreach (['S1', 'S2', 'S3'] as $s)
              <option value="{{ $s }}" {{ request('shift') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <select name="merge" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua merge</option>
            @foreach ($benang as $b)
              <option value="{{ $b->merge }}" {{ request('merge') == $b->merge ? 'selected' : '' }}>{{ $b->merge }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('qct.sampel.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID Sampel</th>
              <th>Tanggal</th>
              <th>Shift</th>
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
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sampel as $s)
              <tr>
                <td>{{ $s->id_sampel }}</td>
                <td>{{ date('d-m-Y', strtotime($s->tgl_sampel)) }}</td>
                <td>{{ $s->shift }}</td>
                <td>{{ $s->merge }}</td>
                <td>{{ $s->denier }}</td>
                <td>{{ $s->filamen }}</td>
                <td>{{ $s->warna_ppt ?? '-' }}</td>
                <td>
                  {{-- Jenis abnormal kosong berarti sampel OK --}}
                  @if ($s->id_jns_abn)
                    <label class="badge badge-outline-danger">{{ $s->nm_abnormal }}</label>
                  @else
                    <label class="badge badge-outline-success">OK</label>
                  @endif
                </td>
                <td>{{ $s->act_denier ?? '-' }}</td>
                <td>{{ $s->elong ?? '-' }}</td>
                <td>{{ $s->tenacity ?? '-' }}</td>
                <td>{{ $s->dti ?? '-' }}</td>
                <td>{{ $s->id_grade ?? '-' }}</td>
                <td>
                  @if (sudah_terkunci($s->tgl_sampel, $s->shift))
                    <label class="badge badge-outline-dark">Terkunci</label>
                  @else
                    <a href="{{ route('qct.sampel.edit', $s->id_sampel) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('qct.sampel.destroy', $s->id_sampel) }}" class="d-inline"
                      onsubmit="return confirm('Hapus data sampel ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Hapus</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="14" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $sampel->links() }}</div>
    </div>
  </div>
@endsection

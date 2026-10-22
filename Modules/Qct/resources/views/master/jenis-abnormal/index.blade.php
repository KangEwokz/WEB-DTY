@extends('auth::layouts.app')

@section('title', 'Data Jenis Abnormal')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('qct.jenis-abnormal.index') }}" class="d-flex">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2" placeholder="Cari nama abnormal">
          <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <a href="{{ route('qct.jenis-abnormal.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode</th>
              <th>Nama Abnormal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($jenis as $j)
              <tr>
                <td>{{ $jenis->firstItem() + $loop->index }}</td>
                <td>{{ $j->id_jns_abn }}</td>
                <td>{{ $j->nm_abnormal }}</td>
                <td>
                  <label class="badge {{ $j->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $j->status_aktif }}</label>
                </td>
                <td>
                  <a href="{{ route('qct.jenis-abnormal.edit', $j->id_jns_abn) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($j->status_aktif == 'Aktif')
                    <form method="POST" action="{{ route('qct.jenis-abnormal.destroy', $j->id_jns_abn) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan jenis abnormal ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Nonaktifkan</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $jenis->links() }}</div>
    </div>
  </div>
@endsection

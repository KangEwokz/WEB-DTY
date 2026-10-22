@extends('auth::layouts.app')

@section('title', 'Data Grade')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('qct.grade.index') }}" class="d-flex">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2" placeholder="Cari kode grade">
          <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        </form>
        <a href="{{ route('qct.grade.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Grade</th>
              <th>Keterangan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($grade as $g)
              <tr>
                <td>{{ $grade->firstItem() + $loop->index }}</td>
                <td>{{ $g->id_grade }}</td>
                <td>{{ $g->keterangan ?? '-' }}</td>
                <td>
                  <label class="badge {{ $g->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $g->status_aktif }}</label>
                </td>
                <td>
                  <a href="{{ route('qct.grade.edit', $g->id_grade) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($g->status_aktif == 'Aktif')
                    <form method="POST" action="{{ route('qct.grade.destroy', $g->id_grade) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan grade ini?')">
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
      <div class="mt-3">{{ $grade->links() }}</div>
    </div>
  </div>
@endsection

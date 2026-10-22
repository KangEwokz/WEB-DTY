@extends('auth::layouts.app')

@section('title', 'Data Karyawan')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('admin.karyawan.index') }}" class="d-flex flex-wrap">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2 mb-2" style="width: 200px" placeholder="Cari nama karyawan">
          <select name="id_divisi" class="form-control me-2 mb-2" style="width: 160px">
            <option value="">Semua divisi</option>
            @foreach ($divisi as $d)
              <option value="{{ $d->id_divisi }}" {{ request('id_divisi') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nm_divisi }}</option>
            @endforeach
          </select>
          <select name="status_aktif" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua status</option>
            @foreach (['Aktif', 'Non-Aktif'] as $s)
              <option value="{{ $s }}" {{ request('status_aktif') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>ID Karyawan</th>
              <th>Nama</th>
              <th>Divisi</th>
              <th>Jabatan</th>
              <th>Area</th>
              <th>Grup</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($karyawan as $k)
              <tr>
                <td>{{ $karyawan->firstItem() + $loop->index }}</td>
                <td>{{ $k->id_karyawan }}</td>
                <td>{{ $k->nama_karyawan }}</td>
                <td>{{ $k->nm_divisi ?? '-' }}</td>
                <td>{{ $k->nm_jabatan }}</td>
                <td>{{ $k->id_area ?? '-' }}</td>
                <td>{{ $k->grup }}</td>
                <td>
                  <label class="badge {{ $k->status_aktif == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $k->status_aktif }}</label>
                </td>
                <td>
                  <a href="{{ route('admin.karyawan.show', $k->id_karyawan) }}" class="btn btn-inverse-info btn-sm">Detail</a>
                  <a href="{{ route('admin.karyawan.edit', $k->id_karyawan) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($k->status_aktif == 'Aktif')
                    <form method="POST" action="{{ route('admin.karyawan.destroy', $k->id_karyawan) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan karyawan ini? Akunnya tidak bisa login lagi.')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Nonaktifkan</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $karyawan->links() }}</div>
    </div>
  </div>
@endsection

@extends('auth::layouts.app')

@section('title', 'Akun Pengguna')

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <form method="GET" action="{{ route('admin.user.index') }}" class="d-flex flex-wrap">
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control me-2 mb-2" style="width: 220px" placeholder="Cari username / nama">
          <select name="role" class="form-control me-2 mb-2" style="width: 150px">
            <option value="">Semua role</option>
            @foreach ($role as $r)
              <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm mb-2">Filter</button>
        </form>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Tambah</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Username</th>
              <th>Karyawan</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($user as $u)
              <tr>
                <td>{{ $user->firstItem() + $loop->index }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->id_karyawan }} - {{ $u->nama_karyawan }}</td>
                <td>{{ $u->role ?? '-' }}</td>
                <td>
                  <label class="badge {{ $u->status == 'Aktif' ? 'badge-outline-success' : 'badge-outline-danger' }}">{{ $u->status }}</label>
                </td>
                <td>
                  <a href="{{ route('admin.user.edit', $u->id) }}" class="btn btn-inverse-primary btn-sm">Ubah</a>
                  @if ($u->status == 'Aktif' && $u->id != auth()->id())
                    <form method="POST" action="{{ route('admin.user.destroy', $u->id) }}" class="d-inline"
                      onsubmit="return confirm('Nonaktifkan akun ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-inverse-danger btn-sm">Nonaktifkan</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $user->links() }}</div>
    </div>
  </div>
@endsection

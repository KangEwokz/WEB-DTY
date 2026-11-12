@extends('auth::layouts.app') {{-- pakai layout utama (navbar, sidebar, footer) --}}

@section('title', 'Data Karyawan') {{-- judul tab browser --}}

@push('styles') {{-- CSS khusus halaman ini --}}
<style>
  .foto-kecil { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; } /* foto bulat kecil di tabel */
  .table td, .table th { vertical-align: middle; } /* isi sel tabel rata tengah secara vertikal */
</style>
@endpush {{-- akhir CSS --}}

@section('content') {{-- awal isi halaman --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-4"> {{-- baris judul: kiri judul, kanan tombol --}}
    <div> {{-- bagian kiri --}}
      <h4 class="font-weight-bold mb-1">Data Karyawan</h4> {{-- judul halaman --}}
      <p class="text-muted mb-0">Kelola data karyawan seluruh departemen.</p> {{-- keterangan kecil --}}
    </div>
    @if (auth()->user()->punyaAkses('admin.karyawan.create')) {{-- tombol hanya tampil jika punya akses tambah --}}
      <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary"><i class="ti-plus me-1"></i> Tambah Karyawan</a> {{-- tombol ke form tambah --}}
    @endif {{-- akhir if --}}
  </div>

  <div class="card"> {{-- kotak kartu --}}
    <div class="card-body"> {{-- isi kartu --}}

      <form action="{{ route('admin.karyawan.index') }}" method="GET" class="row g-2 mb-4"> {{-- form pencarian; GET = kata pencarian masuk ke URL (?cari=...) --}}
        <div class="col-md-5"> {{-- kolom input --}}
          <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari nama atau ID karyawan..."> {{-- request('cari') = isi pencarian sebelumnya tetap tampil --}}
        </div>
        <div class="col-auto"> {{-- kolom tombol --}}
          <button type="submit" class="btn btn-primary"><i class="ti-search"></i> Cari</button> {{-- tombol cari --}}
          @if (request('cari')) {{-- jika sedang mencari --}}
            <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light">Reset</a> {{-- tombol hapus pencarian --}}
          @endif {{-- akhir if --}}
        </div>
      </form> {{-- akhir form pencarian --}}

      <div class="table-responsive"> {{-- tabel bisa digeser ke samping di layar kecil --}}
        <table class="table table-hover"> {{-- table-hover = baris berwarna saat disorot mouse --}}
          <thead> {{-- kepala tabel --}}
            <tr>
              <th>No</th>
              <th>Karyawan</th>
              <th>Departemen</th>
              <th>Divisi</th>
              <th>Jabatan</th>
              <th>Grup</th>
              <th>Status</th>
              <th class="text-end">Aksi</th> {{-- text-end = rata kanan --}}
            </tr>
          </thead>
          <tbody> {{-- isi tabel --}}
            @forelse ($karyawan as $k) {{-- ulangi untuk setiap karyawan; $k = satu karyawan --}}
              <tr>
                <td>{{ $karyawan->firstItem() + $loop->index }}</td> {{-- nomor urut: nomor pertama di halaman ini + urutan perulangan (mulai 0) --}}
                <td> {{-- kolom foto + nama + id --}}
                  <div class="d-flex align-items-center"> {{-- foto dan teks berjajar --}}
                    <img class="foto-kecil me-3" src="{{ $k->foto ? asset('uploads/'.$k->foto) : asset('images/foto-default.svg') }}" alt="foto"> {{-- foto asli atau placeholder --}}
                    <div> {{-- teks --}}
                      <div class="font-weight-bold">{{ $k->nama_karyawan }}</div> {{-- nama --}}
                      <small class="text-muted">{{ $k->id_karyawan }}</small> {{-- id karyawan --}}
                    </div>
                  </div>
                </td>
                <td>{{ $k->kd_departemen ?? '-' }}</td> {{-- kode departemen, '-' jika kosong --}}
                <td>{{ $k->nm_divisi ?? '-' }}</td> {{-- nama divisi, '-' jika kosong --}}
                <td>{{ $k->nm_jabatan }}</td> {{-- nama jabatan --}}
                <td>{{ $k->grup }}</td> {{-- grup shift --}}
                <td> {{-- kolom status --}}
                  @if ($k->status_aktif == 'Aktif') {{-- jika Aktif --}}
                    <span class="badge-aktif">Aktif</span> {{-- label hijau --}}
                  @else {{-- jika Non-Aktif --}}
                    <span class="badge-nonaktif">Non-Aktif</span> {{-- label merah --}}
                  @endif {{-- akhir if --}}
                </td>
                <td class="text-end text-nowrap"> {{-- kolom tombol aksi; text-nowrap = tombol tidak turun ke baris baru --}}
                  @if (auth()->user()->punyaAkses('admin.karyawan.edit')) {{-- tombol ubah hanya jika punya akses --}}
                    <a href="{{ route('admin.karyawan.edit', $k->id_karyawan) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti-pencil"></i></a> {{-- ke /karyawan/{id}/edit --}}
                  @endif {{-- akhir if --}}
                  @if (auth()->user()->punyaAkses('admin.karyawan.destroy')) {{-- tombol hapus hanya jika punya akses --}}
                    <form action="{{ route('admin.karyawan.destroy', $k->id_karyawan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data karyawan ini?')"> {{-- form hapus; confirm() = muncul kotak konfirmasi, batal = tidak jadi dikirim --}}
                      @csrf {{-- token keamanan --}}
                      @method('DELETE') {{-- form HTML hanya bisa GET/POST, jadi ditandai sebagai DELETE agar cocok dengan route --}}
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="ti-trash"></i></button> {{-- tombol hapus --}}
                    </form>
                  @endif {{-- akhir if --}}
                </td>
              </tr>
            @empty {{-- jika tidak ada data sama sekali --}}
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Data karyawan tidak ditemukan.</td> {{-- colspan="8" = satu sel selebar 8 kolom --}}
              </tr>
            @endforelse {{-- akhir perulangan --}}
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap mt-3"> {{-- baris bawah tabel --}}
        <small class="text-muted">Menampilkan {{ $karyawan->count() }} dari {{ $karyawan->total() }} karyawan</small> {{-- count() = jumlah di halaman ini, total() = jumlah semua --}}
        {{ $karyawan->links() }} {{-- link halaman 1, 2, 3, ... (otomatis dari paginate) --}}
      </div>
    </div>
  </div>
@endsection {{-- akhir isi halaman --}}

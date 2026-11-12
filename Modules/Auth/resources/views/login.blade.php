<!DOCTYPE html> {{-- penanda dokumen HTML5 --}}
<html lang="id"> {{-- awal HTML, bahasa Indonesia --}}

<head> {{-- bagian kepala: pengaturan halaman, tidak tampil di layar --}}
  <meta charset="utf-8"> {{-- format huruf UTF-8 --}}
  <meta name="viewport" content="width=device-width, initial-scale=1"> {{-- agar tampilan pas di HP --}}
  <title>Login - {{ config('app.name') }}</title> {{-- judul tab browser; config('app.name') = APP_NAME di .env --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/vendors/ti-icons/css/themify-icons.css') }}"> {{-- CSS ikon; asset() = alamat ke folder public --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/vendors/base/vendor.bundle.base.css') }}"> {{-- CSS dasar (Bootstrap) --}}
  <link rel="stylesheet" href="{{ asset('Royal-UI/template/css/style.css') }}"> {{-- CSS utama template Royal-UI --}}
  <link rel="shortcut icon" href="{{ asset('Royal-UI/template/images/favicon.png') }}"> {{-- ikon kecil di tab browser --}}
</head>

<body> {{-- bagian isi yang tampil di layar --}}
  <div class="container-scroller"> {{-- pembungkus halaman (class dari template) --}}
    <div class="container-fluid page-body-wrapper full-page-wrapper"> {{-- halaman penuh tanpa sidebar --}}
      <div class="content-wrapper d-flex align-items-center auth px-0"> {{-- isi di tengah layar secara vertikal --}}
        <div class="row w-100 mx-0"> {{-- baris selebar layar --}}
          <div class="col-lg-4 mx-auto"> {{-- kotak selebar 4/12 layar, posisi di tengah --}}
            <div class="auth-form-light text-left py-5 px-4 px-sm-5"> {{-- kotak putih form login --}}
              <div class="brand-logo"> {{-- tempat logo --}}
                <img src="{{ asset('Royal-UI/template/images/logo.svg') }}" alt="logo"> {{-- gambar logo --}}
              </div>
              <h4>Aplikasi QCT Benang DTY</h4> {{-- judul --}}
              <h6 class="font-weight-light">Silakan masuk untuk melanjutkan.</h6> {{-- sub judul --}}

              @if (session('error')) {{-- jika controller mengirim pesan 'error' --}}
                <div class="alert alert-danger mt-3">{{ session('error') }}</div> {{-- tampilkan kotak merah berisi pesan --}}
              @endif {{-- akhir if --}}
              @if (session('success')) {{-- jika controller mengirim pesan 'success' --}}
                <div class="alert alert-success mt-3">{{ session('success') }}</div> {{-- tampilkan kotak hijau berisi pesan --}}
              @endif {{-- akhir if --}}

              <form class="pt-3" action="/login" method="POST"> {{-- form dikirim ke route POST /login --}}
                @csrf {{-- token keamanan, wajib ada di setiap form POST --}}

                <div class="form-group"> {{-- kelompok input username --}}
                  <input type="text" name="username" value="{{ old('username') }}" class="form-control form-control-lg" placeholder="Username" autofocus> {{-- name="username" dibaca controller lewat $request->username; old() = isi sebelumnya; autofocus = kursor langsung di sini --}}
                  @error('username') {{-- jika ada error validasi untuk username --}}
                    <small class="text-danger">{{ $message }}</small> {{-- tampilkan pesan error warna merah --}}
                  @enderror {{-- akhir error --}}
                </div>

                <div class="form-group"> {{-- kelompok input password --}}
                  <input type="password" name="password" class="form-control form-control-lg" placeholder="Password"> {{-- type="password" = huruf disembunyikan --}}
                  @error('password') {{-- jika ada error validasi untuk password --}}
                    <small class="text-danger">{{ $message }}</small> {{-- tampilkan pesan error warna merah --}}
                  @enderror {{-- akhir error --}}
                </div>

                <div class="mt-3"> {{-- jarak atas --}}
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">MASUK</button> {{-- tombol kirim form --}}
                </div>
              </form> {{-- akhir form --}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('Royal-UI/template/vendors/base/vendor.bundle.base.js') }}"></script> {{-- JavaScript dasar (jQuery + Bootstrap) --}}
</body>

</html>

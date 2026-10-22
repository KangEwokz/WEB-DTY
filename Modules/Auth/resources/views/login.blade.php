<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login - QCT DTY</title>
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('royal-ui/template/images/favicon.png') }}" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="{{ asset('royal-ui/template/images/logo.svg') }}" alt="logo">
              </div>
              <h4>Pencatatan QCT Benang DTY</h4>
              <h6 class="font-weight-light">Silakan login untuk melanjutkan.</h6>

              @include('auth::layouts.partials.alert')

              <form class="pt-3" action="{{ route('login.proses') }}" method="POST">
                @csrf
                <div class="form-group">
                  <input type="text" name="username" value="{{ old('username') }}"
                    class="form-control form-control-lg @error('username') is-invalid @enderror"
                    placeholder="Username" autofocus>
                  @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password" name="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    placeholder="Password">
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100">LOGIN</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/off-canvas.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/template.js') }}"></script>
</body>

</html>

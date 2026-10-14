<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title') - DTY</title>
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

              @yield('content')
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

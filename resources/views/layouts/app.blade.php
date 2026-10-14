<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title') - DTY</title>
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel="shortcut icon" href="{{ asset('royal-ui/template/images/favicon.png') }}" />
</head>

<body>
  <div class="container-scroller">
    @include('layouts.partials.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('layouts.partials.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          @yield('content')
        </div>

        @include('layouts.partials.footer')
      </div>
    </div>
  </div>

  <script src="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/off-canvas.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/template.js') }}"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') - QCT DTY</title>
  <!-- plugins:css (template Royal UI di public/royal-ui/template) -->
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('royal-ui/template/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('royal-ui/template/images/favicon.png') }}" />
  @stack('css')
</head>

<body>
  <div class="container-scroller">
    @include('auth::layouts.partials.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('auth::layouts.partials.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <h4 class="font-weight-bold mb-0">@yield('title', 'Dashboard')</h4>
            </div>
          </div>

          @include('auth::layouts.partials.alert')

          @yield('content')
        </div>
        <!-- content-wrapper ends -->

        @include('auth::layouts.partials.footer')
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="{{ asset('royal-ui/template/vendors/base/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/off-canvas.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('royal-ui/template/js/template.js') }}"></script>
  @stack('js')
</body>

</html>

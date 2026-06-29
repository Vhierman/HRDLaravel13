<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT Prima Komponen Indonesia | @yield('title')</title>
    <!--favicon-->
    <link rel="icon" href="{{ asset('template_admin/assets/images/favicon-32x32.png') }}" type="image/png">
    <!-- loader-->
    <link href="{{ asset('template_admin/assets/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('template_admin/assets/js/pace.min.js') }}"></script>

    <!--plugins-->
    <link href="{{ asset('template_admin/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('template_admin/assets/plugins/metismenu/metisMenu.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('template_admin/assets/plugins/metismenu/mm-vertical.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('template_admin/assets/plugins/simplebar/css/simplebar.css') }}">
    <!--bootstrap css-->
    <link href="{{ asset('template_admin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    <!--main css-->
    <link href="{{ asset('template_admin/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/main.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/dark-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/blue-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/semi-dark.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/bordered-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/responsive.css') }}" rel="stylesheet">

    @yield('css')

</head>

<body class="d-flex flex-column min-vh-100">

    <!--start navbar-->
    @include('admin.layouts.navbar')
    <!--end top navbar-->

    <!--start sidebar-->
    @include('admin.layouts.sidebar')
    <!--end sidebar-->

    {{-- Bungkus directive SweetAlert dengan pengecekan session. Laravel menyimpan pesan SweetAlert dalam key alert. Jika key ini tidak ada, script tidak akan dipanggil sama sekali. --}}
    @if (Session::has('alert.config'))
        @include('sweetalert::alert')
    @endif

    <!--start main wrapper-->
    <main class="main-wrapper">
        <div class="main-content">
            @yield('content')
        </div>
    </main>
    <!--end main wrapper-->

    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

    <!--start footer-->
    @include('admin.layouts.footer')
    <!--end footer-->

    <!--start cart-->
    {{-- @include('admin.layouts.cart') --}}
    <!--end cart-->

    <!--start switcher-->
    @include('admin.layouts.switcher')
    <!--start switcher-->

    <!--bootstrap js-->
    <script src="{{ asset('template_admin/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('template_admin/assets/js/jquery.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('template_admin/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/metismenu/metisMenu.min.js') }}"></script>
    {{-- <script src="{{ asset('template_admin/assets/plugins/apexchart/apexcharts.min.js') }}"></script> --}}
    <script src="{{ asset('template_admin/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    {{-- <script src="{{ asset('template_admin/assets/plugins/peity/jquery.peity.min.js') }}"></script>
    <script>
        $(".data-attributes span").peity("donut")
    </script> --}}
    {{-- <script src="{{ asset('template_admin/assets/js/dashboard2.js') }}"></script> --}}
    <script src="{{ asset('template_admin/assets/js/main.js') }}"></script>

    @yield('js')

</body>

</html>

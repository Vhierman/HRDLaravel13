<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bang BOR | @yield('title')</title>
    <!--favicon-->
    <link rel="icon" href="{{ asset('template_user/lama/assets/images/favicon-32x32.png') }}" type="image/png">

    <!--=============== BOXICONS ===============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="{{ asset('template_user/assets/css/styles.css') }}">

    @yield('css')

</head>

<body class="d-flex flex-column min-vh-100">
    <!--start navbar-->
    @include('user.layouts.navbar')
    <!--end top navbar-->

    @if (Session::has('alert.config'))
        @include('sweetalert::alert')
    @endif

    <main class="main-wrapper">
        <div class="main-content">
            @yield('content')
        </div>
    </main>


    <!--=============== MAIN JS ===============-->
    <script src="{{ asset('template_user/assets/js/main.js') }}"></script>
    @yield('js')
</body>

</html>

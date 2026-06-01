<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bang BOR | @yield('title')</title>
    <link rel="icon" href="{{ asset('template_user/lama/assets/images/favicon-32x32.png') }}" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('template_user/assets/css/styles.css') }}">
    @yield('css')
</head>

<body class="bg-light">
    @include('user.layouts.navbar')
    @include('sweetalert::alert')
    <main>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template_user/assets/js/main.js') }}"></script>
    @yield('js')
</body>

</html>

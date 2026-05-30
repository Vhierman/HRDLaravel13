<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRD | Login Page</title>
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
    <!--bootstrap css-->
    <link href="{{ asset('template_admin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    <!--main css-->
    <link href="{{ asset('template_admin/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/main.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/dark-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/blue-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('template_admin/sass/responsive.css') }}" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center px-3">
        <div class="card my-5 w-100 mx-auto rounded-4 overflow-hidden p-4" style="max-width:900px;">
            <div class="row g-4">
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <img src="{{ asset('template_admin/assets/images/logo/LogoPanjang.png') }}"
                            class="img-fluid mb-6 d-block mx-auto" style="max-width:100%; height:auto;" alt="">
                        <h5 class="fw-bold mt-4">Get Started Now</h5>

                        <div class="separator">
                            <div class="line"></div>
                            <p class="mb-0 fw-bold">HRD-GA SYSTEM</p>
                            <div class="line"></div>
                        </div>
                        <div class="form-body mt-4">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('login.auth') }}" method="post" class="row g-3">
                                @csrf
                                <div class="col-12">
                                    <label for="inputEmailAddress" class="form-label fs-6">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email') }}" id="inputEmailAddress" placeholder="Masukan Email">
                                </div>
                                <div class="col-12">
                                    <label for="inputChoosePassword" class="form-label fs-6">Password</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" name="password" class="form-control border-end-0"
                                            id="inputChoosePassword" placeholder="Masukan Password">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i
                                                class="bi bi-eye-slash-fill"></i></a>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-grd-info">Login</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="reset" class="btn btn-grd-danger">Reset</button>
                                    </div>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <small class="text-secondary">
                                        © {{ date('Y') }} BangBOR. All Rights Reserved.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-lg-flex d-none">
                    <div class="rounded-4 w-100 d-flex bg-grd-info overflow-hidden" style="height: 100%;">
                        <img src="{{ asset('template_admin/assets/images/bg-login/NextPrima.png') }}"
                            class="w-100 h-100" style="object-fit: cover;" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--plugins-->
    <script src="{{ asset('template_admin/assets/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bi-eye-slash-fill");
                    $('#show_hide_password i').removeClass("bi-eye-fill");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bi-eye-slash-fill");
                    $('#show_hide_password i').addClass("bi-eye-fill");
                }
            });
        });
    </script>

</body>

</html>

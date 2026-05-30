<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('template_user/login/style.css') }}" />
    <title>HRIS</title>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="{{ route('user.login.auth') }}" class="sign-in-form" method="post">
                    @csrf
                    <!-- LOGO BARU KHUSUS MOBILE -->

                    <img src="{{ asset('template_user/login/img/LogoPrima.png') }}" class="logo-mobile"
                        alt="Logo" />

                    <h2 class="title">Login Karyawan</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            placeholder="Masukan Email" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" name="password" placeholder="Masukan Password"
                            required>
                    </div>
                    <input type="submit" value="Login" class="btn solid" />
                    <button type="reset" class="btn btn-grd-danger">Reset</button>
                    @if ($errors->any())
                        <div
                            style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; width: 100%; max-width: 380px;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="copyright">
                        © {{ date('Y') }} BangBOR. All Rights Reserved.
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                </div>
                <img src="{{ asset('template_user/login/img/LogoPrima.png') }}" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us ?</h3>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum
                        laboriosam ad deleniti.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Sign in
                    </button>
                </div>
                <img src="{{ asset('template_user/login/img/LogoPrima.png') }}" class="image" alt="" />
            </div>
        </div>
    </div>

    {{-- <script src="{{ asset('template_user/login/app.js') }}"></script> --}}
</body>

</html>

<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>

    <!--=============== GSAP ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="{{ asset('template_login/assets/css/styles.css') }}">

</head>

<body>
    <div class="container">
        <video class="back-vid" autoplay loop muted plays-inline
            src="{{ asset('template_login/assets/video/galaxy.mp4') }}"></video>
        <section class="login">
            <div class="login__content">
                <div>
                    <h2 class="login__title">Welcome back 👋</h2>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('login.auth') }}" method="post" class="login__form">
                        @csrf
                        <div class="login__group">
                            <div class="login__box">
                                <i class="ri-mail-fill login__icon"></i>
                                <input type="email" name="email" autocomplete="email" required placeholder=" "
                                    class="login__input" id="email" value="{{ old('email') }}">
                                <label for="email" class="login__label">Email</label>
                            </div>

                            <div class="login__box">
                                <i class="ri-lock-2-fill login__icon"></i>
                                <input type="password" name="password" autocomplete="current-password" required
                                    placeholder=" " class="login__input" id="password" value="{{ old('password') }}">
                                <label for="password" class="login__label">Password</label>
                            </div>
                        </div>

                        <a href="#" class="login__forgot">Forgot Password?</a>

                        <button type="submit" class="login__button">
                            Log In <i class="ri-send-plane-2-fill"></i>
                        </button>

                        <p class="login__sign">
                            Don't have an account? <a href="#">Sign Up</a>
                        </p>
                    </form>
                </div>
                <div class="login__image">
                    <img src="{{ asset('template_login/assets/img/prima.jpg') }}" alt="" class="login__img">
                </div>
            </div>
        </section>
    </div>

    <!--=============== GSAP ===============-->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
    <!--=============== MAIN JS ===============-->
    <script src="{{ asset('template_login/assets/js/main.js') }}"></script>

</body>

</html>

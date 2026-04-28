<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Modern - Bootstrap & GSAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            opacity: 0;
            /* Awalnya sembunyi untuk animasi GSAP */
            transform: translateY(30px);
        }

        .btn-primary {
            background-color: #6c63ff;
            border: none;
            padding: 10px;
        }

        .btn-primary:hover {
            background-color: #5751d9;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <h3 class="text-center mb-4 fw-bold" id="title">Selamat Datang</h3>
                        <form id="loginForm" action="{{ route('user.login.auth') }}" method="post">
                            @csrf
                            <div class="mb-3 input-group-custom">
                                <label for="username" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" placeholder="Masukkan Email"
                                    required>
                                @error('email')
                                    <div style="color:red">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4 input-group-custom">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="********"
                                    required>
                                @error('password')
                                    <div style="color:red">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-bold" id="loginBtn">Masuk</button>
                            </div>
                            @error('credentials')
                                <div style="color:red">{{ $message }}</div>
                            @enderror
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Lupa password? <a href="#" class="text-decoration-none">Klik
                                    di sini</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        // Animasi Masuk saat halaman dimuat
        window.addEventListener('DOMContentLoaded', () => {
            const tl = gsap.timeline();

            tl.to(".login-card", {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: "power4.out"
                })
                .from("#title", {
                    opacity: 0,
                    y: -20,
                    duration: 0.5
                }, "-=0.5")
                .from(".input-group-custom", {
                    opacity: 0,
                    x: -20,
                    stagger: 0.2, // Animasi bergantian antar input
                    duration: 0.5
                }, "-=0.3")
                .from("#loginBtn", {
                    scale: 0.8,
                    opacity: 0,
                    duration: 0.4,
                    ease: "back.out(1.7)"
                }, "-=0.2");
        });

        // Animasi interaksi tombol saat hover (opsional melalui JS)
        const loginBtn = document.querySelector('#loginBtn');
        loginBtn.addEventListener('mouseenter', () => {
            gsap.to(loginBtn, {
                scale: 1.05,
                duration: 0.2
            });
        });
        loginBtn.addEventListener('mouseleave', () => {
            gsap.to(loginBtn, {
                scale: 1,
                duration: 0.2
            });
        });
    </script>

</body>

</html>

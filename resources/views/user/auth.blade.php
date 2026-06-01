<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HRIS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }

        .login-container {
            min-height: 100vh;
        }

        .brand-section {
            background: linear-gradient(-45deg, #4481eb 0%, #04befe 100%);
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(68, 129, 235, 0.25);
            border-color: #4481eb;
        }
    </style>
</head>

<body>

    <div class="container-fluid login-container d-flex align-items-center justify-content-center p-0">
        <div class="row g-0 w-100 h-100 min-vh-100 shadow">

            <div class="col-lg-5 col-md-6 bg-white d-flex flex-column justify-content-between p-4 p-md-5">

                <div class="text-center text-md-start mb-4">
                    <img src="{{ asset('template_user/login/img/LogoPrima.png') }}" class="d-md-none img-fluid mb-3"
                        style="max-height: 60px;" alt="Logo Mobile" />
                </div>

                <div class="w-100 mx-auto" style="max-width: 400px;">
                    <div class="mb-4 text-center text-md-start">
                        <h2 class="fw-bold text-dark mb-2">Login Karyawan</h2>
                        <p class="text-muted small">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4 small" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('user.login.auth') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-medium text-secondary">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i
                                        class="bi bi-envelope"></i></span>
                                <input type="email" id="email" class="form-control bg-light border-start-0 ps-0"
                                    name="email" value="{{ old('email') }}" placeholder="Masukan Alamat Email"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-medium text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i
                                        class="bi bi-lock"></i></span>
                                <input type="password" id="password" class="form-control bg-light border-start-0 ps-0"
                                    name="password" placeholder="Masukan Password" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn text-white fw-semibold py-2.5 shadow-sm"
                                style="background-color: #4481eb;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                            </button>
                            <button type="reset" class="btn btn-light text-danger btn-sm border-0 py-2 fw-medium">
                                Reset Form
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center text-muted small mt-4 pt-3 border-top w-100">
                    © {{ date('Y') }} <span class="fw-semibold text-dark">BangBOR</span>. All Rights Reserved.
                </div>
            </div>

            <div
                class="col-lg-7 col-md-6 brand-section d-none d-md-flex align-items-center justify-content-center p-5 text-white position-relative">
                <div class="text-center max-w-md z-index-10">
                    <img src="{{ asset('template_user/login/img/LogoPrima.png') }}" class="img-fluid mb-4 bounce"
                        style="max-height: 120px; filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.15));"
                        alt="Logo Prima" />
                    <h3 class="fw-bold mb-3">Sistem Informasi Sumber Daya Manusia</h3>
                    <p class="opacity-75 px-lg-5 small">
                        Kelola absensi, pengajuan cuti, lembur, dan data administrasi karyawan Anda dalam satu platform
                        terintegrasi yang cepat dan aman.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

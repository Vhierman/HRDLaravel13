@section('css')
    <style>
        .coming-soon-wrapper {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        /* Kotak ilustrasi ikon utama */
        .coming-soon-illustration {
            position: relative;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--first-color) 0%, var(--first-color-alt) 100%);
            color: #fff;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 0 auto 2rem;
            box-shadow: 0 15px 30px rgba(var(--hue), 63%, 40%, 0.2);
            /* Memanggil fungsi animasi melayang */
            animation: floatAnimation 4s ease-in-out infinite;
        }

        /* Efek bayangan dinamis di bawah ikon */
        .illustration-shadow {
            width: 80px;
            height: 10px;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 50%;
            margin: -1rem auto 2rem;
            filter: blur(2px);
            animation: shadowAnimation 4s ease-in-out infinite;
        }

        /* Badge aksen di atas judul */
        .status-badge {
            background-color: rgba(13, 110, 253, 0.05);
            /* Menyesuaikan var(--body-color) jika ingin murni variabel */
            color: var(--first-color);
            border: 1px solid rgba(var(--hue), 63%, 40%, 0.15);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 1px;
            display: inline-block;
        }

        /* Progress bar kustom tipis yang estetik */
        .custom-progress-bg {
            background-color: #e9ecef;
            height: 8px;
            border-radius: 50px;
            overflow: hidden;
            max-width: 300px;
            margin: 0 auto;
        }

        .custom-progress-bar {
            background: linear-gradient(90deg, var(--first-color), var(--first-color-alt));
            height: 100%;
            border-radius: 50px;
            width: 50%;
            /* Estimasi persentase progress pengerjaan */
            position: relative;
            overflow: hidden;
        }

        /* Efek kilatan cahaya (shimmer) pada progress bar */
        .custom-progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
            animation: shimmer 2s infinite;
        }

        /* KEYFRAMES ANIMASI */
        @keyframes floatAnimation {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        @keyframes shadowAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(0.7);
                opacity: 0.5;
                filter: blur(4px);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        @media screen and (max-width: 767px) {
            .section__height {
                padding-bottom: 6rem;
            }
        }

        .cuti-progress-container {
            max-width: 320px;
            margin: 2rem auto 0;
            background: var(--container-color);
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid #eee;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #ffc107;
            /* Warna kuning warning untuk 'In Progress' */
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
            animation: dotPulse 1.5s infinite;
        }
    </style>
@endsection

@extends('user.layouts.base')
@section('title', 'Skills - Coming Soon')

@section('content')
    <section class="container px-3 section section__height d-flex align-items-center justify-content-center" id="skill">

        <div class="coming-soon-wrapper py-4">

            <div class="coming-soon-illustration">
                <i class='bx bx-code-alt'></i>
            </div>
            <div class="illustration-shadow"></div>

            <div class="mb-3">
                <span class="status-badge text-uppercase mb-3">Under Development</span>
            </div>

            <h2 class="fw-bold mb-2" style="color: var(--title-color); letter-spacing: -0.5px;">
                Fitur Sedang Dipersiapkan
            </h2>

            <p class="text-muted mx-auto mb-4" style="max-width: 450px; font-size: 0.95rem; line-height: 1.6;">
                Halaman modul <span class="text-dark fw-semibold">Cuti</span> saat ini sedang dalam proses
                integrasi sistem sistem internal. Kami akan segera kembali!
            </p>

            <div class="cuti-progress-container shadow-sm">
                <div class="d-flex align-items-center justify-content-center gap-2 small">
                    <span class="pulse-dot"></span>
                    <span class="text-muted">Status:</span>
                    <span class="fw-bold text-dark">Tahap Pengujian Sistem</span>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('user.dashboard') }}" class="btn btn-sm px-4 py-2 fw-semibold rounded-pill shadow-sm"
                    style="background-color: var(--container-color); color: var(--first-color); border: 1px solid #dee2e6; font-size: 0.85rem;">
                    <i class='bx bx-arrow-back me-1'></i> Kembali ke Dashboard
                </a>
            </div>

        </div>

    </section>
@endsection

@section('js')
    <script></script>
@endsection

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Prima Komponen Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --main-color: #e63946;
            --dark-bg: #111111;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fdfdfd;
            overflow-x: hidden;
        }

        h1,
        h2,
        .nav-link {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        .navbar {
            transition: 0.4s;
            padding: 20px 0;
        }

        .navbar.scrolled {
            background: var(--dark-bg);
            padding: 10px 0;
            shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        section {
            padding: 80px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        /* Hero Styling */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2070') center/cover;
            color: white;
        }

        /* Card Styling */
        .custom-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.4s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .custom-card:hover {
            transform: translateY(-10px);
        }

        .btn-primary {
            background: var(--main-color);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#home">PRIMA<span class="text-danger">KOMPONEN
                    INDONESIA</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto text-uppercase">
                    <li class="nav-item"><a class="nav-link px-3" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#gallery">Fleet</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="{{ route('user.login') }}">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-2 mb-3 anim-up">ENGINEERED FOR <span class="text-danger">PERFECTION.</span></h1>
                    <p class="lead mb-4 anim-up">Solusi otomotif terintegrasi dengan standar kualitas kelas dunia dan
                        inovasi tanpa batas.</p>
                    <div class="anim-up">
                        <a href="#services" class="btn btn-primary btn-lg">Lihat Layanan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 reveal-left">
                    <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?q=80&w=1000"
                        class="img-fluid rounded-4 shadow" alt="Workshop">
                </div>
                <div class="col-md-6 ps-md-5 reveal-right">
                    <h2 class="display-5 mb-4">Tentang Kami</h2>
                    <p>Berdiri sejak 2004, kami berfokus pada penyediaan komponen berkualitas tinggi dan layanan teknis
                        profesional untuk industri otomotif di Indonesia.</p>
                    <div class="row mt-4">
                        <div class="col-6">
                            <h3 class="text-danger fw-bold">20+</h3>
                            <p>Tahun Pengalaman</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-danger fw-bold">150+</h3>
                            <p>Tenaga Ahli</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h2 class="display-5">Layanan Unggulan</h2>
                <p class="text-muted">Kami menjamin setiap detail dikerjakan dengan presisi tinggi.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 service-item">
                    <div class="card custom-card p-4 h-100">
                        <i class="bi bi-gear-wide-connected text-danger fs-1 mb-3"></i>
                        <h4>Manufacturing</h4>
                        <p>Produksi komponen presisi dengan standar ISO terbaru.</p>
                    </div>
                </div>
                <div class="col-md-4 service-item">
                    <div class="card custom-card p-4 h-100 bg-dark text-white">
                        <i class="bi bi-shield-check text-danger fs-1 mb-3"></i>
                        <h4>Quality Control</h4>
                        <p>Pengecekan kualitas ketat untuk performa keamanan maksimal.</p>
                    </div>
                </div>
                <div class="col-md-4 service-item">
                    <div class="card custom-card p-4 h-100">
                        <i class="bi bi-truck text-danger fs-1 mb-3"></i>
                        <h4>Distribution</h4>
                        <p>Logistik efisien ke seluruh mitra bisnis di tanah air.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="bg-light">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h2>Galeri & Armada</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4 gallery-img"><img
                        src="https://images.unsplash.com/photo-1502877338535-766e1452684a?q=80&w=1000"
                        class="img-fluid rounded shadow" alt="Car"></div>
                <div class="col-md-4 gallery-img"><img
                        src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=1000"
                        class="img-fluid rounded shadow" alt="Factory"></div>
                <div class="col-md-4 gallery-img"><img
                        src="https://images.unsplash.com/photo-1517524206127-48bbd362f39c?q=80&w=1000"
                        class="img-fluid rounded shadow" alt="Engine"></div>
            </div>
        </div>
    </section>

    <section id="contact">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 card custom-card p-5 reveal-up">
                    <h2 class="text-center mb-4">Hubungi Kami</h2>
                    <form action="" method="post">
                        <div class="mb-3"><input type="text" name="username" class="form-control"
                                placeholder="Username">
                        </div>
                        <div class="mb-3"><input type="password" name="password" class="form-control"
                                placeholder="Password">
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 text-center">
        <p>&copy; 2026 Prima Auto Indonesia. Built with Bootstrap & GSAP.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });

        // Hero Animation
        gsap.from(".anim-up", {
            duration: 1.2,
            y: 100,
            opacity: 0,
            stagger: 0.3,
            ease: "power4.out"
        });

        // Scroll Revelations
        const revealSettings = [{
                class: ".reveal-left",
                x: -100
            },
            {
                class: ".reveal-right",
                x: 100
            },
            {
                class: ".reveal-up",
                y: 80
            }
        ];

        revealSettings.forEach(set => {
            gsap.from(set.class, {
                scrollTrigger: {
                    trigger: set.class,
                    start: "top 85%",
                },
                x: set.x || 0,
                y: set.y || 0,
                opacity: 0,
                duration: 1,
                ease: "power2.out"
            });
        });

        // Staggered Cards Animation
        gsap.from(".service-item", {
            scrollTrigger: {
                trigger: "#services",
                start: "top 70%",
            },
            scale: 0.8,
            opacity: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: "back.out(1.7)"
        });
    </script>
</body>

</html>

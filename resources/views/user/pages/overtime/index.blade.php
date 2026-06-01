@section('css')
    <style>
        .overtime-card {
            background: var(--container-color, #fff);
            border-radius: 1.25rem;
            border: none;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1),
                box-shadow 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Hover effect halus yang seirama dengan dashboard utama */
        .overtime-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.06) !important;
        }

        .overtime-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background-color: var(--body-color, #f8f9fa);
            color: var(--first-color, #0d6efd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .input-group-icon {
            background-color: var(--body-color, #f8f9fa);
            color: var(--text-color, #6c757d);
            border-right: none;
        }

        .overtime-select {
            border-left: none;
        }

        .overtime-select:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-icon {
            border-color: var(--first-color);
            color: var(--first-color);
        }

        .input-group:focus-within .overtime-select {
            border-color: var(--first-color);
        }

        /* Info alert box di footer card */
        .info-note {
            background-color: rgba(13, 110, 253, 0.05);
            /* Menyesuaikan warna subtle */
            border-left: 4px solid var(--first-color, #0d6efd);
            color: var(--text-color);
            border-radius: 0 8px 8px 0;
        }

        @media screen and (max-width: 767px) {
            .section__height {
                padding-bottom: 6rem;
            }

            .overtime-card:hover {
                transform: none;
            }
        }
    </style>
@endsection

@extends('user.layouts.base')
@section('title', 'Dashboard Overtime')

@section('content')
    <section class="container px-3 section section__height" id="overtime">

        <div class="card overtime-card shadow-sm mx-auto my-4" style="max-width: 500px;">
            <div class="card-body p-4 p-md-5 text-center">

                <div class="overtime-icon-box shadow-sm">
                    <i class='bx bx-time-five animate__animated animate__pulse animate__infinite'></i>
                </div>

                <h4 class="fw-bold mb-1" style="color: var(--title-color);">Form Overtime</h4>
                <p class="text-muted small mb-4">Milik Karyawan: <span
                        class="fw-semibold text-dark">{{ $employee->nama_karyawan }}</span></p>

                {{-- Pesan Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="text-start mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Utama --}}
                <form action="{{ route('user.tampil_overtime') }}" method="post" target="_blank"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="text-start mb-4">
                        <label for="bulan" class="form-label small fw-bold text-uppercase tracking-wide"
                            style="color: var(--text-color); opacity: 0.8;">
                            Pilih Periode Bulan
                        </label>
                        <div class="input-group shadow-sm-button">
                            <span class="input-group-text input-group-icon" id="basic-addon1">
                                <i class='bx bx-calendar' style="font-size: 1.2rem;"></i>
                            </span>
                            <select class="form-select form-select-lg overtime-select" style="font-size: 0.95rem;"
                                name="bulan" id="bulan" required>
                                <option value="" selected disabled>-- Pilih Bulan Rekap --</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tombol Aksi Eksklusif --}}
                    <div class="row g-2 pt-2">
                        <div class="col-8">
                            <button
                                class="btn btn-lg w-100 fw-semibold shadow-sm text-white d-flex align-items-center justify-content-center gap-2"
                                style="background-color: var(--first-color); border: none; font-size: 0.95rem;"
                                type="submit">
                                <i class='bx bx-search-alt-2'></i> Lihat Data
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-lg btn-light w-100 fw-semibold text-muted"
                                style="font-size: 0.95rem; border: 1px solid #dee2e6;" type="reset">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Catatan Periode Informatif --}}
                <div class="info-note text-start p-3 mt-4 small">
                    <div class="d-flex gap-2">
                        <i class='bx bx-info-circle mt-1' style="color: var(--first-color); font-size: 1.1rem;"></i>
                        <div>
                            <span class="fw-bold d-block mb-1" style="color: var(--title-color);">Informasi Periode</span>
                            Rekapitulasi lembur dihitung dari **tanggal 16** sampai dengan **tanggal 15** pada setiap
                            bulannya.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        // Opsional: Otomatis mendeteksi fokus input group border color transition
        document.addEventListener("DOMContentLoaded", function() {
            const selectEl = document.getElementById('bulan');
            if (selectEl) {
                selectEl.addEventListener('change', function() {
                    // Validasi interaksi user biasa
                });
            }
        });
    </script>
@endsection

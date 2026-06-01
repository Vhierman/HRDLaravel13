@extends('user.layouts.base')
@section('title', 'Dashboard User')

@section('css')
    <style>
        .profile-card {
            background: var(--container-color);
            border-radius: 1rem;
            margin-bottom: 2rem;
            border: none;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1),
                box-shadow 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            will-change: transform, box-shadow;
        }

        .profile-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .profile-avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .profile-card:hover .profile-avatar {
            transform: scale(1.03);
        }

        .info-label {
            font-size: var(--tiny-font-size);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: var(--title-color);
            font-size: var(--normal-font-size);
        }

        .custom-badge {
            background-color: var(--first-color);
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
        }

        .custom-badge-alt {
            background-color: var(--body-color);
            color: var(--first-color-alt);
            border: 1px solid var(--first-color);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .custom-badge-alt:hover {
            background-color: var(--first-color);
            color: #fff;
        }

        .info-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: var(--body-color);
            color: var(--first-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: transform 0.3s ease;
        }

        .profile-card:hover .info-icon-box {
            transform: scale(1.1);
        }

        @media screen and (max-width: 767px) {
            .section__height {
                padding-bottom: 7rem !important;
            }

            .profile-card:hover {
                transform: none;
            }
        }
    </style>
@endsection

@section('content')
    <section class="custom-container section section__height" id="home">

        <div class="card profile-card shadow-sm mb-4 mx-3 mx-md-0">
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-md-3 text-center">
                        <div class="profile-avatar-wrapper">
                            <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}"
                                class="rounded-circle profile-avatar" alt="Foto {{ $employee->nama_karyawan }}"
                                loading="lazy">
                        </div>
                    </div>

                    <div class="col-12 col-md-9 text-center text-md-start">
                        <span class="badge mb-2 custom-badge">{{ $employee->positions->jabatan }}</span>
                        <h2 class="fw-bold mb-1" style="color: var(--title-color);">{{ $employee->nama_karyawan }}</h2>
                        <p class="text-muted mb-3" style="font-size: 0.95rem;">
                            <i class='bx bx-buildings me-1' style="color: var(--first-color);"></i>
                            {{ $employee->companies->nama_perusahaan }}
                        </p>

                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                            <span class="custom-badge-alt"><i class='bx bx-map me-1'></i>{{ $employee->areas->area }}</span>
                            <span class="custom-badge-alt"><i
                                    class='bx bx-git-branch me-1'></i>{{ $employee->divisions->penempatan }}</span>
                            <span class="custom-badge-alt"><i class='bx bx-calendar me-1'></i>{{ $UmurLengkap }}
                                Tahun</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-3 px-md-0">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card profile-card shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold m-0" style="color: var(--title-color);">
                                <i class='bx bx-briefcase me-2' style="color: var(--first-color);"></i>Informasi Pekerjaan
                            </h5>
                        </div>
                        <div class="card-body px-4 py-3">
                            <hr class="mt-0 mb-3 text-muted opacity-25">

                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-id-card'></i>
                                </div>
                                <div>
                                    <div class="info-label">Nomor Induk Karyawan (NIK)</div>
                                    <div class="info-value">{{ $employee->nik_karyawan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-id-card'></i>
                                </div>
                                <div>
                                    <div class="info-label">Nomor NPWP</div>
                                    <div class="info-value">{{ $employee->nomor_npwp ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-id-card'></i>
                                </div>
                                <div>
                                    <div class="info-label">Nomor BPJS Kesehatan</div>
                                    <div class="info-value">{{ $employee->nomor_bpjskesehatan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-id-card'></i>
                                </div>
                                <div>
                                    <div class="info-label">Nomor BPJS Ketenagakerjaan</div>
                                    <div class="info-value">{{ $employee->nomor_bpjsketenagakerjaan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-user-voice'></i>
                                </div>
                                <div>
                                    <div class="info-label">Status Nikah</div>
                                    <div>
                                        <span
                                            class="badge bg-success-subtle text-success px-2 py-1">{{ $employee->status_nikah ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card profile-card shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold m-0" style="color: var(--title-color);">
                                <i class='bx bx-user-pin me-2' style="color: var(--first-color);"></i>Hubungi & Kontak
                            </h5>
                        </div>
                        <div class="card-body px-4 py-3">
                            <hr class="mt-0 mb-3 text-muted opacity-25">

                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-envelope'></i>
                                </div>
                                <div class="w-100 overflow-hidden">
                                    <div class="info-label">Email</div>
                                    <div class="info-value text-break">{{ $employee->email_karyawan }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-phone'></i>
                                </div>
                                <div>
                                    <div class="info-label">Nomor Handphone</div>
                                    <div class="info-value">{{ $employee->nomor_handphone ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-calendar-check'></i>
                                </div>
                                <div>
                                    <div class="info-label">Tanggal Mulai Kerja</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoFormat('D MMMM Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-time-five'></i>
                                </div>
                                <div>
                                    <div class="info-label">Masa Kerja</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->diffForHumans(null, true) }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="info-icon-box me-3">
                                    <i class='bx bx-user-voice'></i>
                                </div>
                                <div>
                                    <div class="info-label">Status Karyawan</div>
                                    <div>
                                        <span
                                            class="badge bg-success-subtle text-success px-2 py-1">{{ $employee->status_kerja ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('js')
    <script>
        // Tempatkan script interaktif kustom di sini jika dibutuhkan nantinya
    </script>
@endsection

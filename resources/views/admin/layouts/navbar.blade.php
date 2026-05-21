<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4">
        <div class="btn-toggle">
            <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
        </div>


        <div class="search-bar flex-grow-1">
            <div class="position-relative">
                {{-- <input class="form-control rounded-5 px-5 search-control d-lg-block d-none" type="text"
                    placeholder="Search"> --}}
                {{-- <span
                    class="material-icons-outlined position-absolute d-lg-block d-none ms-3 translate-middle-y start-0 top-50">search</span> --}}
                <span
                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 search-close">close</span>
                <div class="search-popup p-3">
                    <div class="card rounded-4 overflow-hidden">
                        <div class="card-header d-lg-none">
                            <div class="position-relative">
                                <input class="form-control rounded-5 px-5 mobile-search-control" type="text"
                                    placeholder="Search">
                                <span
                                    class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50">search</span>
                                <span
                                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 mobile-search-close">close</span>
                            </div>
                        </div>
                        <div class="card-body search-content">

                            <div class="search-list d-flex flex-column gap-2">
                            </div>
                        </div>
                        <div class="card-footer text-center bg-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <ul class="navbar-nav gap-1 nav-right-links align-items-center">
            <li class="nav-item dropdown">
                <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
                    <div class="notify-list">
                    </div>
                </div>
            </li>

            <li class="nav-item d-md-flex d-none">
                <a class="nav-link position-relative" data-bs-toggle="offcanvas" href="#offcanvasCart"><i
                        class="material-icons-outlined">apps</i>
                    {{-- <span class="badge-notify">8</span> --}}
                </a>
            </li>
            <li class="nav-item dropdown">
                <a href="javascrpt:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                    <img src="{{ asset('storage/assets/foto/karyawan/' . $foto_karyawan) }}"
                        class="rounded-circle p-1 border" width="45" height="45" alt="">
                </a>
                <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
                    <a class="dropdown-item  gap-2 py-2" href="javascript:;">
                        <div class="text-center">
                            <img src="{{ asset('storage/assets/foto/karyawan/' . $foto_karyawan) }}"
                                class="rounded-circle p-1 shadow mb-3" width="90" height="90" alt="">
                            <h6 class="user-name mb-0 fw-bold">Hello, {{ $nama }}</h6>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    {{-- <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined">person_outline</i>Profile</a> --}}
                    {{-- <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined">local_bar</i>Setting</a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined">dashboard</i>Dashboard</a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined">account_balance</i>Earning</a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined">cloud_download</i>Downloads</a> --}}
                    {{-- <hr class="dropdown-divider"> --}}
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.logout') }}"><i
                            class="material-icons-outlined">power_settings_new</i>Logout</a>
                </div>
            </li>
        </ul>
    </nav>
</header>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart">
    <div class="offcanvas-header border-bottom h-70">
        <h5 class="mb-0" id="offcanvasRightLabel">Notifikasi Dokumen</h5>
        <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
            <i class="material-icons-outlined">close</i>
        </a>
    </div>
    <div class="offcanvas-body p-0">
        <div class="order-list">
            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/perijinan.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Perijinan Expired</h5>
                    <p class="mb-0 order-price">{{ $perijinan_expired }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('legal.notifPerijinanHabis') }}"class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/perijinan.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Perijinan Akan Habis</h5>
                    <p class="mb-0 order-price">{{ $perijinan_akanHabis }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('legal.notifPerijinanAkanHabis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/kontrak_kerja.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Berakhir Kontrak Kerja</h5>
                    <p class="mb-0 order-price">{{ $expired_akhir_kerja }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('kontrak_kerja.notif_kontrak_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/kontrak_kerja.jpg') }}"
                        class="img-fluid rounded-3" width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Akan Habis Kontrak Kerja</h5>
                    <p class="mb-0 order-price">{{ $akanHabis_akhir_kerja }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('kontrak_kerja.notif_kontrak_akan_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/overtimes.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Overtime Belum Di Approve</h5>
                    <p class="mb-0 order-price">{{ $belum_approve_overtime }}</p>
                </div>
                <div class="d-flex">
                    <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/certification.jpg') }}"
                        class="img-fluid rounded-3" width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Sertifikasi BNSP Expired</h5>
                    <p class="mb-0 order-price">{{ $sertifikat_bnsp_expired }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('certification_bnsp.notif_bnsp_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/certification.jpg') }}"
                        class="img-fluid rounded-3" width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Sertifikasi BNSP Akan Habis</h5>
                    <p class="mb-0 order-price">{{ $sertifikat_bnsp__akanHabis }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('certification_bnsp.notif_bnsp_akan_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/certification.jpg') }}"
                        class="img-fluid rounded-3" width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Sertifikasi Kementrian Expired</h5>
                    <p class="mb-0 order-price">{{ $sertifikat_kementrian_expired }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('certification_ministry.notif_ministry_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/certification.jpg') }}"
                        class="img-fluid rounded-3" width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Sertifikasi Kementrian Akan Habis</h5>
                    <p class="mb-0 order-price">{{ $sertifikat_kementrian_akanHabis }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('certification_ministry.notif_ministry_akan_habis') }}"
                        class="order-delete"><span class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/motor.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Pajak Motor Expired</h5>
                    <p class="mb-0 order-price">{{ $inventaris_motor_expired }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('inventory_motorcycle.notif_motor_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/motor.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Pajak Motor Akan Habis</h5>
                    <p class="mb-0 order-price">{{ $inventaris_motor_akanHabis }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('inventory_motorcycle.notif_motor_akan_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/mobil.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Pajak Mobil Expired</h5>
                    <p class="mb-0 order-price">{{ $inventaris_mobil_expired }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('inventory_car.notif_mobil_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>

            <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="order-img">
                    <img src="{{ asset('storage/assets/logo_navbar/mobil.jpg') }}" class="img-fluid rounded-3"
                        width="75" alt="">
                </div>
                <div class="order-info flex-grow-1">
                    <h5 class="mb-1 order-title">Pajak Mobil Akan Habis</h5>
                    <p class="mb-0 order-price">{{ $inventaris_mobil_akanHabis }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('inventory_car.notif_mobil_akan_habis') }}" class="order-delete"><span
                            class="material-icons-outlined">visibility</span></a>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer h-70 p-3 border-top">
        {{-- <div class="d-grid">
            <button type="button" class="btn btn-grd btn-grd-primary" data-bs-dismiss="offcanvas">View
                Products</button>
        </div> --}}
    </div>
</div>

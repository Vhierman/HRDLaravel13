<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="logo-icon">
            <img src="{{ asset('template_admin/assets/images/logo/LogoPrima.png') }}" class="logo-img" alt="">
        </div>
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0">HRIS_V3</h5>
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav">
        <!--navigation-->
        <ul class="metismenu" id="sidenav">

            {{-- Dashboard --}}
            <li>
                <a href="#">
                    <div class="parent-icon"><i class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            {{-- Dashboard --}}

            {{-- Master --}}
            <li>
                <a class="has-arrow" href="#">
                    <div class="parent-icon"><i class="material-icons-outlined">apps</i>
                    </div>
                    <div class="menu-title">Master</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>User</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Perusahaan</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Area</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Penempatan</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Jabatan</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Golongan</a>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Jam Kerja</a>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Minimal Upah</a>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Maksimal Upah BPJS
                            Kesehatan</a>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Maksimal Upah BPJS
                            Ketenagakerjaan</a>
                    </li>
                </ul>
            </li>
            {{-- Master --}}

            {{-- Karyawan --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">people_outline</i>
                    </div>
                    <div class="menu-title">Karyawan</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">emoji_people</i>Karyawan Aktif</a>
                    </li>
                    <li><a href="#"><i class="material-icons-outlined">follow_the_signs</i>Karyawan Keluar</a>
                    </li>
                </ul>
            </li>
            {{-- Karyawan --}}

            {{-- Absensi --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">access_alarms</i>
                    </div>
                    <div class="menu-title">Absensi</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Absensi Karyawan</a></li>
                </ul>
            </li>
            {{-- Absensi --}}

            {{-- Inventaris --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">inventory_2</i>
                    </div>
                    <div class="menu-title">Inventaris</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">two_wheeler</i>Inventaris Motor</a></li>
                    <li><a href="#"><i class="material-icons-outlined">directions_car</i>Inventaris Mobil</a></li>
                </ul>
            </li>
            {{-- Inventaris --}}

            {{-- Training --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">data_exploration</i>
                    </div>
                    <div class="menu-title">Training</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Training Internal</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Training Eksternal</a>
                    </li>
                </ul>
            </li>
            {{-- Training --}}

            {{-- Sertifikasi --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">book</i>
                    </div>
                    <div class="menu-title">Sertifikasi</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>BNSP</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Kementrian</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Lainnya</a></li>
                </ul>
            </li>
            {{-- Sertifikasi --}}

            {{-- Kaizen --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">workspace_premium</i>
                    </div>
                    <div class="menu-title">Kaizen</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>QCC Internal</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>QCC Eksternal</a></li>
                </ul>
            </li>
            {{-- Kaizen --}}

            {{-- Proses --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">ads_click</i>
                    </div>
                    <div class="menu-title">Proses</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Overtime</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>PKWT Kontrak</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>PKWT Harian</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Salary</a></li>
                </ul>
            </li>
            {{-- Proses --}}

            {{-- Surat --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">contact_mail</i>
                    </div>
                    <div class="menu-title">Surat</div>
                </a>
                <ul>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>PKWT Kontrak</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>PKWT Harian</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Penilaian Karyawan</a>
                    </li>
                </ul>
            </li>
            {{-- Surat --}}

            {{-- Laporan --}}
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class="material-icons-outlined">assignment_turned_in</i>
                    </div>
                    <div class="menu-title">Laporan</div>
                </a>
                <ul>
                    <li><a class="has-arrow" href="javascript:;"><i
                                class="material-icons-outlined">arrow_right</i>Absensi Department</a>
                        <ul>
                            <li><a href="auth-basic-login.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>PDC Daihatsu</a></li>
                            <li><a href="auth-basic-register.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>Produksi</a></li>
                            <li><a href="auth-basic-forgot-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>PPC</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>IC</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>IT</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>Document Control</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>HRD-GA</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>Marketing</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>Purchasing</a></li>
                            <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                        class="material-icons-outlined">arrow_right</i>Engineering</a></li>
                        </ul>
                    </li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Absensi Karyawan</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Perijinan</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Masuk</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Keluar</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Kontrak</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Tetap</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Harian</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Karyawan Outsourcing</a>
                    </li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Inventaris Motor</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Inventaris Mobil</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Training Internal</a></li>
                    <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Training Eksternal</a>
                    </li>
                </ul>
            </li>
            {{-- Laporan --}}

            {{-- Privacy Policy, Terms of Service, Documentation, and Support --}}
            <li>
                <a href="javascrpt:;">
                    <div class="parent-icon"><i class="material-icons-outlined">description</i>
                    </div>
                    <div class="menu-title">Privacy Policy</div>
                </a>
            </li>
            {{-- Privacy Policy, Terms of Service, Documentation, and Support --}}


        </ul>
        <!--end navigation-->
    </div>
</aside>

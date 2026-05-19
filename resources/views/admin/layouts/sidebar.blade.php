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
                <a href="{{ route('admin.dashboard') }}">
                    <div class="parent-icon"><i class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            {{-- Dashboard --}}

            {{-- Master --}}
            @if (Auth::user()->roles == 'admin')
                <li>
                    <a class="has-arrow" href="#">
                        <div class="parent-icon"><i class="material-icons-outlined">apps</i>
                        </div>
                        <div class="menu-title">Master</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('user.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>User</a>
                        </li>
                        <li><a href="{{ route('company.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Perusahaan</a></li>
                        <li><a href="{{ route('area.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Area</a></li>
                        <li><a href="{{ route('division.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Penempatan</a></li>
                        <li><a href="{{ route('position.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Jabatan</a></li>
                        <li><a href="{{ route('golongan.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Golongan</a></li>
                        <li><a href="{{ route('working_hour.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Jam Kerja</a></li>
                        <li><a href="{{ route('minimal_salary.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Minimal Upah</a></li>
                        <li><a href="{{ route('maksimal_upah_bpjskesehatan.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Maksimal Upah BPJS
                                Kesehatan</a></li>
                        <li><a href="{{ route('maksimal_upah_bpjstk.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Maksimal Upah BPJS
                                Ketenagakerjaan</a>
                        </li>
                        <li><a href="{{ route('bonus.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Bonus</a>
                        </li>
                    </ul>
                </li>
            @endif
            {{-- Master --}}

            {{-- Karyawan --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">people_outline</i>
                    </div>
                    <div class="menu-title">Karyawan</div>
                </a>
                <ul>
                    <li><a href="{{ route('employee.index') }}"><i
                                class="material-icons-outlined">emoji_people</i>Karyawan Aktif</a>
                    </li>
                    <li><a href="{{ route('employee_out.index') }}"><i
                                class="material-icons-outlined">follow_the_signs</i>Karyawan Keluar</a>
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
                    <li><a href="{{ route('attendance.index') }}"><i
                                class="material-icons-outlined">arrow_right</i>Absensi Karyawan</a></li>
                </ul>
            </li>
            {{-- Absensi --}}

            {{-- Inventaris --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd' || Auth::user()->roles == 'accounting')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">inventory_2</i>
                        </div>
                        <div class="menu-title">Inventaris</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('inventory_motorcycle.index') }}"><i
                                    class="material-icons-outlined">two_wheeler</i>Inventaris Motor</a></li>
                        <li><a href="{{ route('inventory_car.index') }}"><i
                                    class="material-icons-outlined">directions_car</i>Inventaris Mobil</a></li>
                    </ul>
                </li>
            @endif
            {{-- Inventaris --}}


            {{-- Training --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd' || Auth::user()->roles == 'leader')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">work_history</i>
                        </div>
                        <div class="menu-title">Training</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('training_internal.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Training Internal</a></li>
                        <li><a href="{{ route('training_eksternal.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Training Eksternal</a>
                        </li>
                    </ul>
                </li>
            @endif
            {{-- Training --}}

            {{-- Sertifikasi --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd' || Auth::user()->roles == 'accounting')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">workspace_premium</i>
                        </div>
                        <div class="menu-title">Sertifikasi</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('certification_bnsp.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>BNSP</a></li>
                        <li><a href="{{ route('certification_ministry.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Kementrian</a></li>
                        <li><a href="{{ route('certification_other.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Lainnya</a></li>
                    </ul>
                </li>
            @endif
            {{-- Sertifikasi --}}

            {{-- Overtimes --}}
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined">alarm_add</i>
                    </div>
                    <div class="menu-title">Overtimes</div>
                </a>
                <ul>
                    <li><a href="{{ route('overtime.index') }}"><i
                                class="material-icons-outlined">arrow_right</i>Overtime Karyawan</a></li>
                </ul>
            </li>
            {{-- Overtimes --}}

            {{-- Kontrak Kerja --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">ads_click</i>
                        </div>
                        <div class="menu-title">Kontrak Kerja</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('kontrak_kerja.form_kontrak_pkwt') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Karyawan PKWT</a></li>
                        <li><a href="{{ route('kontrak_kerja.form_kontrak_harian') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Karyawan Harian</a></li>
                        <li><a href="{{ route('kontrak_kerja.form_penilaian') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Penilaian Karyawan</a></li>
                    </ul>
                </li>
            @endif
            {{-- Kontrak Kerja --}}

            {{-- Perijinan --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd' || Auth::user()->roles == 'accounting')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">assured_workload</i>
                        </div>
                        <div class="menu-title">Perijinan</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('legal.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Perijinan Perusahaan</a>
                        </li>
                    </ul>
                </li>
            @endif
            {{-- Perijinan --}}

            {{-- Salary --}}
            @if (Auth::user()->roles == 'admin')
                <li>
                    <a href="#" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">currency_exchange</i>
                        </div>
                        <div class="menu-title">Gaji</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('salary.index') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Gaji
                                Karyawan</a>
                        </li>
                    </ul>
                </li>
            @endif
            {{-- Surat --}}

            {{-- Laporan --}}
            @if (Auth::user()->roles == 'admin' || Auth::user()->roles == 'hrd' || Auth::user()->roles == 'accounting')
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="material-icons-outlined">assignment_turned_in</i>
                        </div>
                        <div class="menu-title">Laporan</div>
                    </a>
                    <ul>
                        <li><a href="{{ route('report.rekap_absensi') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Rekap Absensi</a></li>
                        <li><a href="{{ route('report.absensi_karyawan') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Absensi Karyawan</a></li>
                        <li><a href="{{ route('report.karyawan_masuk') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Karyawan Masuk</a></li>
                        <li><a href="{{ route('report.karyawan_keluar') }}"><i
                                    class="material-icons-outlined">arrow_right</i>Karyawan Keluar</a></li>
                        <li><a href="#"><i class="material-icons-outlined">arrow_right</i>TurnOver Karyawan</a>
                        </li>
                        <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Overtime</a></li>
                    </ul>
                </li>
            @endif
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
            {{-- Logout --}}
            <li>
                <a href="{{ route('admin.logout') }}">
                    <div class="parent-icon"><i class="material-icons-outlined">logout</i>
                    </div>
                    <div class="menu-title">Logout</div>
                </a>
            </li>
            {{-- Logout --}}


        </ul>
        <!--end navigation-->
    </div>
</aside>

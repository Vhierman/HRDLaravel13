<!--=============== NAVBAR ===============-->
<header class="header" id="header">
    <nav class="nav container">
        <a href="#" class="nav__logo">Hello {{ $employee->nama_karyawan }}</a>

        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="{{ route('user.dashboard') }}"
                        class="nav__link {{ request()->routeIs('user.dashboard') ? 'active-link' : '' }}">
                        <i class='bx bx-home-alt nav__icon'></i>
                        <span class="nav__name">Home</span>
                    </a>
                </li>

                <li class="nav__item">
                    <a href="{{ route('user.overtime') }}"
                        class="nav__link {{ request()->routeIs('user.overtime') ? 'active-link' : '' }}">
                        <i class='bx bx-time nav__icon'></i>
                        <span class="nav__name">Overtime</span>
                    </a>
                </li>

                <li class="nav__item">
                    <a href="{{ route('user.skill') }}"
                        class="nav__link {{ request()->routeIs('user.skill') ? 'active-link' : '' }}">
                        <i class='bx bx-book-alt nav__icon'></i>
                        <span class="nav__name">Skills</span>
                    </a>
                </li>

                <li class="nav__item">
                    <a href="{{ route('user.cuti') }}"
                        class="nav__link {{ request()->routeIs('user.cuti') ? 'active-link' : '' }}">
                        <i class='bx bx-briefcase-alt nav__icon'></i>
                        <span class="nav__name">Cuti</span>
                    </a>
                </li>

                <li class="nav__item">
                    <a href="{{ route('user.logout') }}" class="nav__link">
                        <i class='bx bx-log-out nav__icon'></i>
                        <span class="nav__name">Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}" alt="Foto Karyawan"
            class="nav__img">
    </nav>
</header>
<!--=============== NAVBAR ===============-->

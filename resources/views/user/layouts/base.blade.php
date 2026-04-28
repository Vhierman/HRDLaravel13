@include('user.layouts.navbar')
@include('user.layouts.sidebar')
<h1>INI BASE</h1>
@yield('content')
<a href="{{ route('user.logout') }}" rel="noopener noreferrer">LOGOUT</a>
@include('user.layouts.footer')

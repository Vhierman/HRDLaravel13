<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; // Tambahkan ini
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        return view('user.auth');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Membuat kunci unik berdasarkan email dan IP Address
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Cek apakah pengguna terlalu banyak melakukan percobaan login
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik."
            ])->withInput();
        }

        $credentials = $request->only('email', 'password');
        // Memastikan hanya user dengan role 'karyawan' yang bisa login lewat sini
        $credentials['roles'] = 'karyawan'; 

        if (Auth::attempt($credentials)) {
            // Bersihkan catatan rate limit jika berhasil login
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            return redirect()->route('user.dashboard');
        }

        // Catat kegagalan login jika salah password/email
        RateLimiter::hit($throttleKey, 60); // mengunci selama 60 detik jika limit habis

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.auth');
    }

    public function authenticate(Request $request)
    {
        // //Validasi Data Dari Form Login
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // $credentials = $request->only('email','password');
        // // Tambahkan Kondisi Untuk Login Hanya Bisa Dengan Role Tertentu
        // $credentials['roles'] = ['admin','hrd','accounting','leader'];
        // //Jika Benar
        // if (Auth::attempt($credentials)) {
        //     $request->session()->regenerate();
        //     return redirect()->route('admin.dashboard');
        // }
        // //Jika Salah
        // return back()->withErrors([
        //     'error' => 'Your Credential Are Wrong'
        // ])->withInput();

        // 2. Proteksi Brute Force (Rate Limiting)
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'error' => "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik."
            ])->withInput();
        }

        // 3. Ambil data email & password saja
        $credentials = $request->only('email', 'password');

        // Menggunakan Auth::validate() terlebih dahulu untuk mengecek kecocokan email & password tanpa meloginkan user langsung
        if (Auth::validate($credentials)) {
            
            // Mengambil data user berdasarkan email yang diinput
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            // 4. Cek apakah Role User diizinkan masuk ke panel Admin
            $allowedRoles = ['admin', 'hrd', 'accounting', 'leader'];
            
            if (in_array($user->roles, $allowedRoles)) {
                
                // Jika lolos semua pengecekan, login-kan user secara resmi
                Auth::login($user);
                
                // Bersihkan record rate limiter
                RateLimiter::clear($throttleKey);

                // Regenerasi session untuk keamanan session hijacking
                $request->session()->regenerate();
                
                return redirect()->route('admin.dashboard');
            }
        }

        // jika gagal (salah password atau role tidak sesuai), naikkan hit rate limiter
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'error' => 'Kombinasi email, password salah atau Anda tidak memiliki hak akses.'
        ])->withInput();
            
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

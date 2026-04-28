<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\UserAuthenticate;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\User\LoginController as UserLoginController;
use App\Http\Controllers\User\DashboardUserController;

// Halaman Utama
Route::view('/','index');

//Login Admin Area
Route::get('admin/login',[LoginController::class,'index'])->name('login');
Route::post('admin/login',[LoginController::class,'authenticate'])->name('login.auth');

//Halaman Admin Area
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::view('/', 'admin.dashboard_admin')->name('admin.dashboard');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// Login User Area
Route::get('/login',[UserLoginController::class,'index'])->name('user.login');
Route::post('/login',[UserLoginController::class,'authenticate'])->name('user.login.auth');

// Halaman User Area
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/', [DashboardUserController::class, 'index'])->name('user.dashboard');
    Route::get('/logout', [UserLoginController::class, 'logout'])->name('user.logout');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTeamMembership;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\User\LoginController as UserLoginController;
use App\Http\Controllers\User\DashboardUserController;


// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('admin/login',[LoginController::class,'index'])->name('login');
Route::post('admin/login',[LoginController::class,'authenticate'])->name('login.auth');

// Route::prefix('admin')->group(function () {
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::view('/', 'admin.dashboard_admin')->name('admin.dashboard');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::view('/','index');
Route::get('/login',[UserLoginController::class,'index'])->name('user.login');
Route::post('/login',[UserLoginController::class,'auth'])->name('user.login.auth');

Route::prefix('user')->group(function () {
    Route::get('/', [DashboardUserController::class, 'index'])->name('user.dashboard');
});

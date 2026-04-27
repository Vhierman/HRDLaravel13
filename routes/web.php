<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTeamMembership;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\LoginController;

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

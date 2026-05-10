<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\UserAuthenticate;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\User\LoginController as UserLoginController;
use App\Http\Controllers\User\DashboardUserController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\GolonganController;
use App\Http\Controllers\Admin\WorkingHourController;
use App\Http\Controllers\Admin\MinimalSalaryController;
use App\Http\Controllers\Admin\MaksimalUpahBpjsKesehatanController;
use App\Http\Controllers\Admin\MaksimalUpahBpjsKetenagakerjaanController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeOutController;
use App\Http\Controllers\Admin\HistoryContractController;
use App\Http\Controllers\Admin\HistoryPositionController;
use App\Http\Controllers\Admin\HistoryFamilyController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\InventoryMotorcycleController;

// Halaman Utama
Route::view('/','index');

// Route Admin Area
//Login Admin Area
Route::get('admin/login',[LoginController::class,'index'])->name('admin.login');
Route::post('admin/login',[LoginController::class,'authenticate'])->name('login.auth');

//Halaman Admin Area
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardAdminController::class, 'index'])->name('admin.dashboard');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::resource('user', UserController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('area', AreaController::class);
    Route::resource('division', DivisionController::class);
    Route::resource('position', PositionController::class);
    Route::resource('golongan', GolonganController::class);
    Route::resource('working_hour', WorkingHourController::class);
    Route::resource('minimal_salary', MinimalSalaryController::class);
    Route::resource('maksimal_upah_bpjskesehatan', MaksimalUpahBpjsKesehatanController::class);
    Route::resource('maksimal_upah_bpjstk', MaksimalUpahBpjsKetenagakerjaanController::class);
    Route::get('employee/aktif_kerja/{nik_karyawan}', [EmployeeController::class, 'aktif_kerja'])->name('cetak.aktif_kerja');
    Route::get('employee/exportExcel', [EmployeeController::class, 'exportExcel'])->name('exportExcel');
    Route::resource('employee', EmployeeController::class);
    Route::get('history_contract/pkwt/{nik_karyawan}', [HistoryContractController::class, 'pkwt'])->name('cetak.pkwt');
    Route::resource('history_contract', HistoryContractController::class);
    Route::resource('history_position', HistoryPositionController::class);
    Route::resource('history_family', HistoryFamilyController::class);
    Route::get('employee_out/EmployeeOutExportExcel', [EmployeeOutController::class, 'EmployeeOutExportExcel'])->name('EmployeeOutExportExcel');
    Route::resource('employee_out', EmployeeOutController::class);
    Route::post('attendance/export_excell_absensi', [AttendanceController::class, 'export_excell_absensi'])->name('attendance.export_excell_absensi');
    Route::get('attendance/form_tampil', [AttendanceController::class, 'form_tampil'])->name('attendance.form_tampil');
    Route::get('attendance/form_edit', [AttendanceController::class, 'form_edit'])->name('attendance.form_edit');
    Route::get('attendance/form_hapus', [AttendanceController::class, 'form_hapus'])->name('attendance.form_hapus');
    Route::post('attendance/tampil_form_edit', [AttendanceController::class, 'tampil_form_edit'])->name('attendance.tampil_form_edit');
    Route::post('attendance/tampil_form_hapus', [AttendanceController::class, 'tampil_form_hapus'])->name('attendance.tampil_form_hapus');
    Route::post('attendance/tampil_absen', [AttendanceController::class, 'tampil_absen'])->name('attendance.tampil_absen');
    Route::resource('attendance', AttendanceController::class);
    Route::get('inventory_motorcycle/exportExcel', [InventoryMotorcycleController::class, 'exportExcel'])->name('inventory_motorcycle.exportExcel');
    Route::resource('inventory_motorcycle', InventoryMotorcycleController::class);
});
// Route Admin Area



// Route User Area
// Login User Area
Route::get('/login',[UserLoginController::class,'index'])->name('user.login');
Route::post('/login',[UserLoginController::class,'authenticate'])->name('user.login.auth');

// Halaman User Area
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/', [DashboardUserController::class, 'index'])->name('user.dashboard');
    Route::get('/logout', [UserLoginController::class, 'logout'])->name('user.logout');
});
// Route User Area

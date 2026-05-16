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
use App\Http\Controllers\Admin\InventoryCarController;
use App\Http\Controllers\Admin\TrainingInternalController;
use App\Http\Controllers\Admin\TrainingEksternalController;
use App\Http\Controllers\Admin\CertificationBnspController;
use App\Http\Controllers\Admin\CertificationMinistryController;
use App\Http\Controllers\Admin\CertificationOtherController;
use App\Http\Controllers\Admin\OvertimeController;

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
    //Master
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
    //Employee
    Route::get('employee/aktif_kerja/{nik_karyawan}', [EmployeeController::class, 'aktif_kerja'])->name('cetak.aktif_kerja');
    Route::get('employee/exportExcel', [EmployeeController::class, 'exportExcel'])->name('exportExcel');
    Route::resource('employee', EmployeeController::class);
    //History Employees
    Route::get('history_contract/pkwt/{nik_karyawan}', [HistoryContractController::class, 'pkwt'])->name('cetak.pkwt');
    Route::resource('history_contract', HistoryContractController::class);
    Route::resource('history_position', HistoryPositionController::class);
    Route::resource('history_family', HistoryFamilyController::class);
    //Employees Out
    Route::get('employee_out/EmployeeOutExportExcel', [EmployeeOutController::class, 'EmployeeOutExportExcel'])->name('EmployeeOutExportExcel');
    Route::resource('employee_out', EmployeeOutController::class);
    // Attendance
    Route::post('attendance/export_excell_absensi', [AttendanceController::class, 'export_excell_absensi'])->name('attendance.export_excell_absensi');
    Route::post('attendance/export_excell_non_absensi', [AttendanceController::class, 'export_excell_non_absensi'])->name('attendance.export_excell_non_absensi');
    Route::post('attendance/tampil_non_absen', [AttendanceController::class, 'tampil_non_absen'])->name('attendance.tampil_non_absen');
    Route::get('attendance/form_non_absen', [AttendanceController::class, 'form_non_absen'])->name('attendance.form_non_absen');
    Route::get('attendance/form_tampil', [AttendanceController::class, 'form_tampil'])->name('attendance.form_tampil');
    Route::get('attendance/form_edit', [AttendanceController::class, 'form_edit'])->name('attendance.form_edit');
    Route::get('attendance/form_hapus', [AttendanceController::class, 'form_hapus'])->name('attendance.form_hapus');
    Route::post('attendance/tampil_form_edit', [AttendanceController::class, 'tampil_form_edit'])->name('attendance.tampil_form_edit');
    Route::post('attendance/tampil_form_hapus', [AttendanceController::class, 'tampil_form_hapus'])->name('attendance.tampil_form_hapus');
    Route::post('attendance/tampil_absen', [AttendanceController::class, 'tampil_absen'])->name('attendance.tampil_absen');
    Route::resource('attendance', AttendanceController::class);
    // Inventory
    Route::get('inventory_motorcycle/exportExcel', [InventoryMotorcycleController::class, 'exportExcel'])->name('inventory_motorcycle.exportExcel');
    Route::resource('inventory_motorcycle', InventoryMotorcycleController::class);
    Route::get('inventory_car/exportExcel', [InventoryCarController::class, 'exportExcel'])->name('inventory_car.exportExcel');
    Route::resource('inventory_car', InventoryCarController::class);
    //Training Eksternal
    Route::post('training_eksternal/hapus_tanggal', [TrainingEksternalController::class, 'hapus_tanggal'])->name('training_eksternal.hapus_tanggal');
    Route::post('training_eksternal/tampil_hapus_tanggal', [TrainingEksternalController::class, 'tampil_hapus_tanggal'])->name('training_eksternal.tampil_hapus_tanggal');
    Route::get('training_eksternal/form_hapus_tanggal', [TrainingEksternalController::class, 'form_hapus_tanggal'])->name('training_eksternal.form_hapus_tanggal');
    Route::post('training_eksternal/update_tanggal', [TrainingEksternalController::class, 'update_tanggal'])->name('training_eksternal.update_tanggal');
    Route::post('training_eksternal/edit_tanggal', [TrainingEksternalController::class, 'edit_tanggal'])->name('training_eksternal.edit_tanggal');
    Route::get('training_eksternal/form_edit_tanggal', [TrainingEksternalController::class, 'form_edit_tanggal'])->name('training_eksternal.form_edit_tanggal');
    Route::post('training_eksternal/excell_view_materi', [TrainingEksternalController::class, 'excell_view_materi'])->name('training_eksternal.excell_view_materi');
    Route::post('training_eksternal/tampil_view_materi', [TrainingEksternalController::class, 'tampil_view_materi'])->name('training_eksternal.tampil_view_materi');
    Route::get('training_eksternal/view_materi', [TrainingEksternalController::class, 'view_materi'])->name('training_eksternal.view_materi');
    Route::post('training_eksternal/excell_view_nama', [TrainingEksternalController::class, 'excell_view_nama'])->name('training_eksternal.excell_view_nama');
    Route::post('training_eksternal/tampil_view_nama', [TrainingEksternalController::class, 'tampil_view_nama'])->name('training_eksternal.tampil_view_nama');
    Route::get('training_eksternal/view_nama', [TrainingEksternalController::class, 'view_nama'])->name('training_eksternal.view_nama');
    Route::post('training_eksternal/excell_view_penempatan', [TrainingEksternalController::class, 'excell_view_penempatan'])->name('training_eksternal.excell_view_penempatan');
    Route::post('training_eksternal/tampil_view_penempatan', [TrainingEksternalController::class, 'tampil_view_penempatan'])->name('training_eksternal.tampil_view_penempatan');
    Route::get('training_eksternal/view_penempatan', [TrainingEksternalController::class, 'view_penempatan'])->name('training_eksternal.view_penempatan');
    Route::post('training_eksternal/excell_training_eksternal', [TrainingEksternalController::class, 'excell_training_eksternal'])->name('training_eksternal.excell_training_eksternal');
    Route::post('training_eksternal/tampil_view_tanggal', [TrainingEksternalController::class, 'tampil_view_tanggal'])->name('training_eksternal.tampil_view_tanggal');
    Route::get('training_eksternal/view_tanggal', [TrainingEksternalController::class, 'view_tanggal'])->name('training_eksternal.view_tanggal');
    Route::resource('training_eksternal', TrainingEksternalController::class);
    //Training Internal
    Route::post('training_internal/hapus_tanggal', [TrainingInternalController::class, 'hapus_tanggal'])->name('training_internal.hapus_tanggal');
    Route::post('training_internal/tampil_hapus_tanggal', [TrainingInternalController::class, 'tampil_hapus_tanggal'])->name('training_internal.tampil_hapus_tanggal');
    Route::get('training_internal/form_hapus_tanggal', [TrainingInternalController::class, 'form_hapus_tanggal'])->name('training_internal.form_hapus_tanggal');
    Route::post('training_internal/update_tanggal', [TrainingInternalController::class, 'update_tanggal'])->name('training_internal.update_tanggal');
    Route::post('training_internal/edit_tanggal', [TrainingInternalController::class, 'edit_tanggal'])->name('training_internal.edit_tanggal');
    Route::get('training_internal/form_edit_tanggal', [TrainingInternalController::class, 'form_edit_tanggal'])->name('training_internal.form_edit_tanggal');
    Route::post('training_internal/excell_view_materi', [TrainingInternalController::class, 'excell_view_materi'])->name('training_internal.excell_view_materi');
    Route::post('training_internal/tampil_view_materi', [TrainingInternalController::class, 'tampil_view_materi'])->name('training_internal.tampil_view_materi');
    Route::get('training_internal/view_materi', [TrainingInternalController::class, 'view_materi'])->name('training_internal.view_materi');
    Route::post('training_internal/excell_view_nama', [TrainingInternalController::class, 'excell_view_nama'])->name('training_internal.excell_view_nama');
    Route::post('training_internal/tampil_view_nama', [TrainingInternalController::class, 'tampil_view_nama'])->name('training_internal.tampil_view_nama');
    Route::get('training_internal/view_nama', [TrainingInternalController::class, 'view_nama'])->name('training_internal.view_nama');
    Route::post('training_internal/excell_view_penempatan', [TrainingInternalController::class, 'excell_view_penempatan'])->name('training_internal.excell_view_penempatan');
    Route::post('training_internal/tampil_view_penempatan', [TrainingInternalController::class, 'tampil_view_penempatan'])->name('training_internal.tampil_view_penempatan');
    Route::get('training_internal/view_penempatan', [TrainingInternalController::class, 'view_penempatan'])->name('training_internal.view_penempatan');
    Route::post('training_internal/excell_training_internal', [TrainingInternalController::class, 'excell_training_internal'])->name('training_internal.excell_training_internal');
    Route::post('training_internal/tampil_view_tanggal', [TrainingInternalController::class, 'tampil_view_tanggal'])->name('training_internal.tampil_view_tanggal');
    Route::get('training_internal/view_tanggal', [TrainingInternalController::class, 'view_tanggal'])->name('training_internal.view_tanggal');
    Route::post('training_internal/excell_belum_training_internal', [TrainingInternalController::class, 'excell_belum_training_internal'])->name('training_internal.excell_belum_training_internal');
    Route::post('training_internal/tampil_view_belum_training', [TrainingInternalController::class, 'tampil_view_belum_training'])->name('training_internal.tampil_view_belum_training');
    Route::get('training_internal/form_belum_training', [TrainingInternalController::class, 'form_belum_training'])->name('training_internal.form_belum_training');
    Route::resource('training_internal', TrainingInternalController::class);
    // Certification
    Route::get('certification_bnsp/exportExcel', [CertificationBnspController::class, 'exportExcel'])->name('certification_bnsp.exportExcel');
    Route::resource('certification_bnsp', CertificationBnspController::class);
    Route::get('certification_ministry/exportExcel', [CertificationMinistryController::class, 'exportExcel'])->name('certification_ministry.exportExcel');
    Route::resource('certification_ministry', CertificationMinistryController::class);
    Route::get('certification_other/exportExcel', [CertificationOtherController::class, 'exportExcel'])->name('certification_other.exportExcel');
    Route::resource('certification_other', CertificationOtherController::class);
    });
    //Overtimes
    Route::post('overtime/exportExcell_rekap_overtime', [OvertimeController::class, 'exportExcell_rekap_overtime'])->name('overtime.exportExcell_rekap_overtime');
    Route::post('overtime/exportPDF_rekap_overtime', [OvertimeController::class, 'exportPDF_rekap_overtime'])->name('overtime.exportPDF_rekap_overtime');
    Route::post('overtime/cetak_rekap_overtime', [OvertimeController::class, 'cetak_rekap_overtime'])->name('overtime.cetak_rekap_overtime');
    Route::get('overtime/form_cetak_rekap_overtime', [OvertimeController::class, 'form_cetak_rekap_overtime'])->name('overtime.form_cetak_rekap_overtime');
    Route::post('overtime/cetak_slip_overtime', [OvertimeController::class, 'cetak_slip_overtime'])->name('overtime.cetak_slip_overtime');
    Route::get('overtime/form_cetak_slip_overtime', [OvertimeController::class, 'form_cetak_slip_overtime'])->name('overtime.form_cetak_slip_overtime');
    Route::post('overtime/proses_edit_approve_overtime', [OvertimeController::class, 'proses_edit_approve_overtime'])->name('overtime.proses_edit_approve_overtime');
    Route::post('overtime/proses_cancel_approve_overtime', [OvertimeController::class, 'proses_cancel_approve_overtime'])->name('overtime.proses_cancel_approve_overtime');
    Route::post('overtime/tampil_cancel_approve_overtime', [OvertimeController::class, 'tampil_cancel_approve_overtime'])->name('overtime.tampil_cancel_approve_overtime');
    Route::get('overtime/form_cancel_approve_overtime', [OvertimeController::class, 'form_cancel_approve_overtime'])->name('overtime.form_cancel_approve_overtime');
    Route::post('overtime/proses_approve_overtime', [OvertimeController::class, 'proses_approve_overtime'])->name('overtime.proses_approve_overtime');
    Route::post('overtime/tampil_approve_overtime', [OvertimeController::class, 'tampil_approve_overtime'])->name('overtime.tampil_approve_overtime');
    Route::get('overtime/form_approve_overtime', [OvertimeController::class, 'form_approve_overtime'])->name('overtime.form_approve_overtime');
    Route::post('overtime/tampil_hapus_overtime', [OvertimeController::class, 'tampil_hapus_overtime'])->name('overtime.tampil_hapus_overtime');
    Route::get('overtime/form_hapus_overtime', [OvertimeController::class, 'form_hapus_overtime'])->name('overtime.form_hapus_overtime');
    Route::post('overtime/tampil_edit_overtime', [OvertimeController::class, 'tampil_edit_overtime'])->name('overtime.tampil_edit_overtime');
    Route::get('overtime/form_edit_overtime', [OvertimeController::class, 'form_edit_overtime'])->name('overtime.form_edit_overtime');
    Route::post('overtime/export_excell_overtime', [OvertimeController::class, 'export_excell_overtime'])->name('overtime.export_excell_overtime');
    Route::post('overtime/tampil_overtime', [OvertimeController::class, 'tampil_overtime'])->name('overtime.tampil_overtime');
    Route::get('overtime/lihat_overtime', [OvertimeController::class, 'lihat_overtime'])->name('overtime.lihat_overtime');
    Route::resource('overtime', OvertimeController::class);
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

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Admin\Employees;
use App\Models\Admin\Legals;
use App\Models\Admin\Overtimes;
use App\Models\Admin\CertificationBnsps;
use App\Models\Admin\CertificationMinistries;
use App\Models\Admin\InventoryMotorcycles;
use App\Models\Admin\InventoryCars;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        View::composer('admin.layouts.navbar', function ($view) {

            $nama   = auth()->user()->name;
            $nik    = auth()->user()->nik;
            $roles  = auth()->user()->roles;
            $today = Carbon::today();
            $employee = Employees::where('nik_karyawan', $nik)->first();
            
            $foto_karyawan = $employee?->foto_karyawan;

            $perijinan_expired = Legals::whereDate('tanggal_habis', '<', $today)
                        ->count();
            $perijinan_akanHabis = Legals::whereBetween('tanggal_habis', [
                            $today,$today->copy()->addDays(30)
                        ])->count();
            $total_Perijinan = Legals::count();

            $expired_akhir_kerja = Employees::whereDate('tanggal_akhir_kerja', '<', $today)->where('status_kerja','Harian')
                        ->count();
            $akanHabis_akhir_kerja = Employees::whereBetween('tanggal_akhir_kerja', [
                            $today,$today->copy()->addDays(30)
                        ])->where('status_kerja','Harian')->count();

            $belum_approve_overtime = Overtimes::whereNull('acc_hrd')->count();

            $sertifikat_bnsp_expired = CertificationBnsps::whereDate('sampai_tanggal_bnsp', '<', $today)
                        ->count();
            $sertifikat_bnsp__akanHabis = CertificationBnsps::whereBetween('sampai_tanggal_bnsp', [
                            $today,$today->copy()->addDays(30)
                        ])->count();

            $sertifikat_kementrian_expired = CertificationMinistries::whereDate('sampai_tanggal_kementrian', '<', $today)
                        ->count();
            $sertifikat_kementrian_akanHabis = CertificationMinistries::whereBetween('sampai_tanggal_kementrian', [
                            $today,$today->copy()->addDays(30)
                        ])->count();

            $inventaris_motor_expired = InventoryMotorcycles::whereDate('tanggal_akhir_pajak_motor', '<', $today)
                        ->count();
            $inventaris_motor_akanHabis = InventoryMotorcycles::whereBetween('tanggal_akhir_pajak_motor', [
                            $today,$today->copy()->addDays(30)
                        ])->count();
            $inventaris_mobil_expired = InventoryCars::whereDate('tanggal_akhir_pajak_mobil', '<', $today)
                        ->count();
            $inventaris_mobil_akanHabis = InventoryCars::whereBetween('tanggal_akhir_pajak_mobil', [
                            $today,$today->copy()->addDays(30)
                        ])->count();


        $view->with([
                        'nama'                              => $nama,
                        'today'                             => $today,
                        'foto_karyawan'                     => $foto_karyawan,
                        'perijinan_expired'                 => $perijinan_expired,
                        'perijinan_akanHabis'               => $perijinan_akanHabis,
                        'total_Perijinan'                   => $total_Perijinan,
                        'expired_akhir_kerja'               => $expired_akhir_kerja,
                        'akanHabis_akhir_kerja'             => $akanHabis_akhir_kerja,
                        'belum_approve_overtime'            => $belum_approve_overtime,
                        'sertifikat_bnsp_expired'           => $sertifikat_bnsp_expired,
                        'sertifikat_bnsp__akanHabis'        => $sertifikat_bnsp__akanHabis,
                        'sertifikat_kementrian_expired'     => $sertifikat_kementrian_expired,
                        'sertifikat_kementrian_akanHabis'   => $sertifikat_kementrian_akanHabis,
                        'inventaris_motor_expired'          => $inventaris_motor_expired,
                        'inventaris_motor_akanHabis'        => $inventaris_motor_akanHabis, 
                        'inventaris_mobil_expired'          => $inventaris_mobil_expired, 
                        'inventaris_mobil_akanHabis'        => $inventaris_mobil_akanHabis, 
                    ]);

        });
    }
}

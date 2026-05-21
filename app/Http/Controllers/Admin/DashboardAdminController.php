<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Areas;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'leader' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        toast('Hello ' . auth()->user()->name, 'success');

        //Detail Jumlah Karyawan
        $jumlah_karyawan_all            = Employees::all()->count();
        $jumlah_karyawan_bsd            = Employees::where('areas_id',1)->count();
        $jumlah_karyawan_pdc_daihatsu   = Employees::whereIn('areas_id',[3,4,5])->count();
        $jumlah_karyawan_greenville     = Employees::where('areas_id',2)->count();
        
        //Detail Jumlah Penempatan
        $employeeCounts = Employees::select(
                        'divisions_id',
                        'status_kerja',
                        DB::raw('count(*) as total')
                        )
                        ->groupBy('divisions_id', 'status_kerja')
                        ->get();
        $dataPenempatan = [];
        foreach ($employeeCounts as $item) {
            $dataPenempatan[$item->divisions_id][$item->status_kerja] = $item->total;
        }

        //Detail Status Kerja
        $item_kontrak = Employees::where('status_kerja','PKWT')
                        ->count();
        $item_tetap = Employees::where('status_kerja', 'PKWTT')
                        ->count();
        $item_harian = Employees::where('status_kerja', 'Harian')
                        ->count();
        $item_outsourcing = Employees::where('status_kerja', 'Outsourcing')
                        ->count();
        
        // Chart Status Menikah
        $item_single = Employees::where('status_nikah', 'Single')
                        ->count();
        $item_menikah = Employees::where('status_nikah', 'Menikah')
                        ->count();
        $item_janda = Employees::where('status_nikah', 'Janda')
                        ->count();
        $item_duda = Employees::where('status_nikah', 'Duda')
                        ->count();
        
        // Chart Jenis Kelamin
        $item_pria = Employees::where('jenis_kelamin', 'Pria')
                        ->count();
        $item_wanita = Employees::where('jenis_kelamin', 'Wanita')
                        ->count();
        
        // Chart Agama Produksi
        $item_islam = Employees::where('agama', 'Islam')
                        ->count();
        $item_kristenprotestan = Employees::where('agama', 'Kristen Protestan')
                        ->count();
        $item_kristenkatholik = Employees::where('agama', 'Kristen Katholik')
                        ->count();
        $item_hindu = Employees::where('agama', 'Hindu')
                    ->count();
        $item_budha = Employees::where('agama', 'Budha')
                    ->count();

        return view('admin.dashboard_admin',
            [
            'jumlah_karyawan_all'           => $jumlah_karyawan_all,
            'jumlah_karyawan_bsd'           => $jumlah_karyawan_bsd,
            'jumlah_karyawan_pdc_daihatsu'  => $jumlah_karyawan_pdc_daihatsu,
            'jumlah_karyawan_greenville'    => $jumlah_karyawan_greenville,
            'dataPenempatan'                => $dataPenempatan,
            'item_kontrak'                  => $item_kontrak,
            'item_tetap'                    => $item_tetap,
            'item_harian'                   => $item_harian,
            'item_outsourcing'              => $item_outsourcing,
            'item_single'                   => $item_single,
            'item_menikah'                  => $item_menikah,
            'item_janda'                    => $item_janda,
            'item_duda'                     => $item_duda,
            'item_pria'                     => $item_pria,
            'item_wanita'                   => $item_wanita,
            'item_islam'                    => $item_islam,
            'item_kristenprotestan'         => $item_kristenprotestan,
            'item_kristenkatholik'          => $item_kristenkatholik,
            'item_hindu'                    => $item_hindu,
            'item_budha'                    => $item_budha
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }
}

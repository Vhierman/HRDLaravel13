<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Http\Requests\Admin\EmployeeUpdateRequest;
use App\Models\Admin\Employees;
use App\Models\Admin\Companies;
use App\Models\Admin\WorkingHours;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use App\Models\Admin\Areas;
use App\Models\Admin\MinimalSalaries;
use App\Models\Admin\HistoryContracts;
use App\Models\Admin\HistoryPositions;
use App\Models\Admin\HistoryFamilies;
use App\Models\Admin\HistorySalaries;
use App\Models\Admin\MaksimalUpahBpjsKesehatans;
use App\Models\Admin\MaksimalUpahBpjsKetenagakerjaans;
use App\Models\Admin\HistoryTrainingInternals;
use App\Models\Admin\HistoryTrainingEksternals;
use App\Models\Admin\InventoryCars;
use App\Models\Admin\InventoryMotorcycles;
use Alert;
use Codedge\Fpdf\Fpdf\Fpdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)->value('divisions_id');
        $divisionMap = [
                        19 => [19,20,21],
                        11 => [11],
                        10 => [10],
                        14 => [14],
                        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
                        ];
        $divisionIds = $divisionMap[$divisi] ?? null;
        
        $query = Employees::with([
                'areas',
                'golongans',
                'divisions',
                'positions'
                ]);
        if ($divisionIds) 
        {
            $query->whereIn('divisions_id', $divisionIds);
        }
        $employees = $query->get();

        return view('admin.pages.employee.index',[
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $companies      = Companies::all();
        $working_hours  = WorkingHours::all();
        $golongans      = Golongans::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $areas          = Areas::all();
        return view ('admin.pages.employee.create',[
                    'companies'     => $companies,
                    'working_hours' => $working_hours,
                    'divisions'     => $divisions,
                    'positions'     => $positions,
                    'golongans'     => $golongans,
                    'areas'         => $areas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        DB::beginTransaction();
        try 
        {
            $id_area        = $request->input('areas_id');
            $minimal_upah   = MinimalSalaries::where('areas_id',$id_area)->first();
            $data           = $request->except(['_token', '_method']);
            $fileFields     = ['foto_karyawan' => 'assets/foto/karyawan'];
            foreach ($fileFields as $field => $path) 
            {
                if ($request->hasFile($field)) 
                {
                    $file = $request->file($field);
                    $fileName = Str::random(10) . $file->getClientOriginalName();
                    $file->storeAs($path, $fileName, 'public');
                    $data[$field] = $fileName;
                } 
                else 
                {
                    $data[$field] = null;
                }
            }

            $employee = Employees::create($data);

            $awal_kontrak   = date_create($request->input('tanggal_mulai_kerja'));
            $akhir_kontrak  = date_create($request->input('tanggal_akhir_kerja'));
            $interval       = date_diff($awal_kontrak, $akhir_kontrak);
            $totalBulan     = ($interval->y * 12) + $interval->m;
            $totalBulan += 1;

            if ($totalBulan == 12) {
                $masa_kontrak = "1 Tahun";
            } elseif ($totalBulan > 12) {
                $tahun = floor($totalBulan / 12);
                $bulan = $totalBulan % 12;

                if ($bulan == 0) {
                    $masa_kontrak = $tahun . " Tahun";
                } else {
                    $masa_kontrak = $tahun . " Tahun " . $bulan . " Bulan";
                }
            } else {
                $masa_kontrak = $totalBulan . " Bulan";
            }

            $employee->history_contracts()->create([
                'employees_id'          => $employee->id,
                'nik_karyawan'          => $employee->nik_karyawan,
                'tanggal_awal_kontrak'  => $request->tanggal_mulai_kerja,
                'tanggal_akhir_kontrak' => $request->tanggal_akhir_kerja,
                'status_kontrak_kerja'  => $request->status_kerja,
                'masa_kontrak'          => $masa_kontrak,
                'jumlah_kontrak'        => 1,
                'input_oleh'            => $request->input_oleh,
            ]);
            $employee->history_positions()->create([
                'employees_id'          => $employee->id,
                'nik_karyawan'          => $employee->nik_karyawan,
                'companies_id_history'  => $request->input('companies_id'),
                'areas_id_history'      => $request->input('areas_id'),
                'divisions_id_history'  => $request->input('divisions_id'),
                'positions_id_history'  => $request->input('positions_id'),
                'tanggal_mutasi'        => $request->input('tanggal_mulai_kerja'),
                'input_oleh'            => $request->input('input_oleh')
            ]);

            // Input History Salaries
            // Rumus Gaji
            $gaji_pokok         = $minimal_upah->minimal_upah;
            $uang_makan         = 0;
            $uang_transport     = 0;
            $tunjangan_tugas    = 0;
            $tunjangan_pulsa    = 0;
            $tunjangan_jabatan  = 0;

            $jht                = 1;
            $jp                 = 1;
            $jkk                = 1;
            $jkm                = 1;
            $jkn                = 1;

        //Ikut Semua Kepesertaan BPJS Ketenagakerjaan Dan Kesehatan
        if ($jht != 0 && $jp != 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {       
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id',1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {

                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jp_perusahaan         = $jumlah_upah*2/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
                $potongan_jp_karyawan           = $jumlah_upah*1/100;
            }
            elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan*2/100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan*1/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan*4/100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan*2/100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan*1/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan,0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan,0);
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan,0);
            $hasil_potongan_jp_perusahaan       = round($potongan_jp_perusahaan,0);
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan,0);
            $hasil_potongan_jp_karyawan         = round($potongan_jp_karyawan,0);

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Tidak Ikut Semua Kepesertaan BPJS Ketenagakerjaan Dan Kesehatan
        elseif ($jht == 0 && $jp == 0 && $jkk == 0 && $jkm == 0 && $jkn == 0) {
            
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = 0;
            $hasil_potongan_jkk_perusahaan      = 0;
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Ikut Kepesertaan BPJS Ketenagakerjaan Dan Tidak Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht != 0 && $jp != 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id',1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jp_perusahaan         = $jumlah_upah*2/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
                $potongan_jp_karyawan           = $jumlah_upah*1/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan*2/100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan*1/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan,0);
            $hasil_potongan_jp_perusahaan       = round($potongan_jp_perusahaan,0);
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan,0);
            $hasil_potongan_jp_karyawan         = round($potongan_jp_karyawan,0);

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Ikut Kepesertaan BPJS Kesehatan Dan Tidak Ikut Kepesertaan BPJS Ketenagakerjaan 
        elseif ($jht == 0 && $jp == 0 && $jkk == 0 && $jkm == 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id',1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan*4/100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan*1/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan,0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan,0);
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = 0;
            $hasil_potongan_jkk_perusahaan      = 0;
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Tidak Ikut JHT Dan JP, Hanya Ikut JKK Dan JKM Dan Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht == 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id',1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
            }
            elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan*4/100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan*1/100;

                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan,0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan,0);
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Tidak Ikut JHT, JP, dan BPJS Kesehatan, Hanya Ikut JKK Dan JKM
        elseif ($jht == 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {       
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {    
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        }

        //Tidak Ikut JP, Hanya Ikut JHT, JKK Dan JKM Dan Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht != 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id',1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
            }
            elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah*4/100;
                $potongan_bpjsks_karyawan       = $jumlah_upah*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan*4/100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan*1/100;
    
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan,0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan,0);
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan,0);
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan,0);
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 

        //Tidak Ikut JP, dan BPJS Kesehatan, Hanya Ikut JHT, JKK , Dan JKM
        elseif ($jht != 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {       
            //End Rumus
            $jumlah_upah                        = $minimal_upah->minimal_upah;
            $upah_lembur_perjam                 = $jumlah_upah/173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id',1)->first();
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
            }
            elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah*3.7/100;
                $potongan_jkm_perusahaan        = $jumlah_upah*0.3/100;
                $potongan_jkk_perusahaan        = $jumlah_upah*0.24/100;
                $potongan_jht_karyawan          = $jumlah_upah*2/100;
            }
            else{
                dd('Salah');
            }
            
            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan,0);
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan,0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan,0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan,0);
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan+$hasil_potongan_jp_perusahaan+$hasil_potongan_jkm_perusahaan+$hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan+$hasil_potongan_jp_karyawan;
            $take_home_pay                      = $jumlah_upah-$jumlah_bpjstk_karyawan-$hasil_potongan_bpjsks_karyawan;
            //End Rumus
        } 
        
        //Kondisi Salah
        else {
            dd('Kondisi Salah');
        }
        // Rumus Gaji


        // Input History Salaries
        $employee->history_salaries()->create([
                'employees_id'                  => $employee->id,
                'nik_karyawan'                  => $employee->nik_karyawan,
                'gaji_pokok'                    => $gaji_pokok,
                'uang_makan'                    => $uang_makan,
                'uang_transport'                => $uang_transport,
                'tunjangan_tugas'               => $tunjangan_tugas,
                'tunjangan_pulsa'               => $tunjangan_pulsa,
                'tunjangan_jabatan'             => $tunjangan_jabatan,
                'jumlah_upah'                   => $minimal_upah->minimal_upah,
                'upah_lembur_perjam'            => $hasil_upah_lembur_perjam,
                'potongan_bpjsks_perusahaan'    => $hasil_potongan_bpjsks_perusahaan,
                'potongan_jht_perusahaan'       => $hasil_potongan_jht_perusahaan,
                'potongan_jp_perusahaan'        => $hasil_potongan_jp_perusahaan,
                'potongan_jkm_perusahaan'       => $hasil_potongan_jkm_perusahaan,
                'potongan_jkk_perusahaan'       => $hasil_potongan_jkk_perusahaan,
                'jumlah_bpjstk_perusahaan'      => $jumlah_bpjstk_perusahaan,
                'potongan_bpjsks_karyawan'      => $hasil_potongan_bpjsks_karyawan,
                'potongan_jht_karyawan'         => $hasil_potongan_jht_karyawan,
                'potongan_jp_karyawan'          => $hasil_potongan_jp_karyawan,
                'jumlah_bpjstk_karyawan'        => $jumlah_bpjstk_karyawan,
                'take_home_pay'                 => $take_home_pay,
                'input_oleh'                    => $request->input('input_oleh')
            ]);
        
            DB::commit();
            Alert::success('Success Input Data Karyawan','Oleh '.auth()->user()->name);
            return redirect()->route('employee.index');
        }
            catch (\Exception $e) 
        {
            DB::rollBack();
            Alert::error('Error', $e->getMessage());
            return redirect()
            ->back()
            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $employee               = Employees::findOrFail($id);
        $golongans              = Golongans::all();
        $companies              = Companies::all();
        $divisions              = Divisions::all();
        $positions              = Positions::all();
        $working_hours          = WorkingHours::all();
        $areas                  = Areas::all();
        $today                  = Carbon::today();
        $tanggal_lahir          = Carbon::parse($employee->tanggal_lahir);
        $tanggal_mulai_kerja    = Carbon::parse($employee->tanggal_mulai_kerja);
        $UmurLengkap            = $tanggal_lahir->diff($today)->format('%y Tahun, %m Bulan');
        $MasaKerja              = $tanggal_mulai_kerja->diff($today)->format('%y Tahun, %m Bulan');
        $history_contracts      = HistoryContracts::where('employees_id', $employee->id)->get();
        $history_families       = HistoryFamilies::where('employees_id', $employee->id)->get();
        $history_family         = HistoryFamilies::with(['employees'])->where('employees_id', $employee->id)->first();
        $history_positions      = HistoryPositions::with([
                                    'employees',
                                    'divisions',
                                    'positions',
                                    'companies',
                                    'areas'
                                    ])->where('employees_id', $employee->id)->get();
        return view ('admin.pages.employee.show',[
            'employee'                  => $employee,
            'golongans'                 => $golongans,
            'companies'                 => $companies,
            'divisions'                 => $divisions,
            'positions'                 => $positions,
            'working_hours'             => $working_hours,
            'UmurLengkap'               => $UmurLengkap,
            'MasaKerja'                 => $MasaKerja,
            'areas'                     => $areas,
            'history_contracts'         => $history_contracts,
            'history_positions'         => $history_positions,
            'history_families'          => $history_families,
            'history_family'            => $history_family
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $employee       = Employees::findOrFail($id);
        $golongans      = Golongans::all();
        $companies      = Companies::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $working_hours  = WorkingHours::all();
        $areas          = Areas::all();
        return view ('admin.pages.employee.edit',[
            'employee'      => $employee,
            'golongans'     => $golongans,
            'companies'     => $companies,
            'divisions'     => $divisions,
            'positions'     => $positions,
            'working_hours' => $working_hours,
            'areas'         => $areas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeUpdateRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data       = $request->except(['_token', '_method']);
        $employee   = Employees::findOrFail($id);
        $fileFields = [
            'foto_karyawan' => 'assets/foto/karyawan'
        ];
        foreach ($fileFields as $field => $path) {
            if ($request->hasFile($field)) {
                // Ambil file
                $file = $request->file($field);
                
                // Buat nama unik
                $fileName = Str::random(10) . $file->getClientOriginalName();
                
                // Simpan file baru
                $file->storeAs($path, $fileName, 'public');
                
                // Update array data untuk database
                $data[$field] = $fileName;

                // Hapus foto lama jika memang ada file lama di database
                if ($employee->$field) {
                    Storage::disk('public')->delete($path . '/' . $employee->$field);
                }
            } else {
                // Jika tidak ada file baru yang diunggah, 
                // hapus field ini dari array $data agar tidak menimpa data lama dengan null
                unset($data[$field]);
            }
        }
        $employee->update($data);
        Alert::success('Success Update Data Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $authName = auth()->user()->name;
        DB::transaction(function () use ($id, $authName) 
        {
            $employee = Employees::with([
            'history_contracts', 'history_salaries', 'history_families', 'history_positions', 
            'training_eksternals', 'training_internals', 'overtimes', 'rekap_salaries', 
            'inventory_motorcycles', 'inventory_cars'
            ])->findOrFail($id);

            $allRelations = [
                'history_contracts', 
                'history_salaries', 
                'history_families', 
                'history_positions', 
                'training_eksternals', 
                'training_internals',
                'inventory_motorcycles', 
                'overtimes', 
                'rekap_salaries', 
                'inventory_cars'
            ];

            foreach ($allRelations as $relation) {
                if ($employee->$relation) {
                    foreach ($employee->$relation as $item) {
                        $item->update(['hapus_oleh' => $authName]);
                        $item->delete();
                    }
                }
            }

            $employee->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $fileFields = [
                'foto_karyawan' => 'assets/foto/karyawan'
            ];

            foreach ($fileFields as $field => $path) {
                if ($employee->$field) {
                    Storage::disk('public')->delete($path . '/' . $employee->$field);
                }
            }
            $employee->delete();
        });
        Alert::success('Success Hapus Data Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.index');
    }

    public function aktif_kerja($id)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        $item           = Employees::findOrFail($id);
        $companies      = Companies::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $workinghours   = WorkingHours::all();
        $areas          = Areas::all();

        //Create Nomor Dokumen
        $nik_karyawan   = $item->nik_karyawan;
        $nomor          = substr($nik_karyawan, 6,6);
        $mytime         = Carbon::now();
        $bulan          = substr($mytime, 5, -12);
        $tahun          = substr($mytime, 0,4);
        if ($bulan == 1) {
            $romawi = 'I';
        }
        elseif ($bulan == 2) {
            $romawi = 'II';
        } 
        elseif ($bulan == 3) {
            $romawi = 'III';
        } 
        elseif ($bulan == 4) {
            $romawi = 'IV';
        } 
        elseif ($bulan == 5) {
            $romawi = 'V';
        } 
        elseif ($bulan == 6) {
            $romawi = 'VI';
        } 
        elseif ($bulan == 7) {
            $romawi = 'VII';
        } 
        elseif ($bulan == 8) {
            $romawi = 'VIII';
        } 
        elseif ($bulan == 9) {
            $romawi = 'IX';
        } 
        elseif ($bulan == 10) {
            $romawi = 'X';
        } 
        elseif ($bulan == 11) {
            $romawi = 'XI';
        } 
        elseif ($bulan == 12) {
            $romawi = 'XII';
        } 
        else {
            $romawi = 'SALAH';
        }
        //Create Nomor Dokumen

        $this->fpdf = new FPDF('P', 'mm', 'A4');
        $this->fpdf->setTopMargin(2);
        $this->fpdf->setLeftMargin(2);
        $this->fpdf->SetAutoPageBreak(false);
        $this->fpdf->AddPage();
        $this->fpdf->Cell(205, 293, '', 0, 0, 'C');
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Ln(30);
        $this->fpdf->SetFont('Arial', 'BU', '18');
        $this->fpdf->Cell(3);
        $this->fpdf->Cell(200, 10, 'SURAT KETERANGAN', 0, 0, 'C'); 
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', 'B', '14');
        $this->fpdf->Cell(3);
        $this->fpdf->Cell(200, 10, 'No.' . $nomor . '/HRD/PK/'. $romawi . '/'.$tahun.'', 0, 0, 'C');
        $this->fpdf->Ln(30);
        $this->fpdf->SetFont('Arial', '', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(100, 10, 'Kami Yang Bertanda Tangan Dibawah Ini :', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Achmad Firmansyah', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Jabatan', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Manager (HRD-GA)', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Menerangkan Bahwa', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, '  ', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->nama_karyawan . '', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'NIK ', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->nik_karyawan. '', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Jabatan / Penempatan ', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->positions->jabatan . ' / ' . $item->divisions->penempatan . '', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Tanggal Mulai Kerja', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . \Carbon\Carbon::parse($item->tanggal_mulai_kerja)->isoformat('D MMMM Y') . '', 0, 0, 'L');
        $this->fpdf->Ln(9);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Adalah benar yang bersangkutan masih bekerja dan aktif menjadi karyawan kami,', 0, 0, 'L');
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'dengan jabatan dan masa kerja di atas.', 0, 0, 'L');
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Demikianlah surat keterangan ini kami buat untuk digunakan dengan seperlunya.', 0, 0, 'L');
        $this->fpdf->Ln(10);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Tangerang Selatan, ' . \Carbon\Carbon::now()->isoformat('D MMMM Y') . '', 0, 0, 'L');
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Hormat kami,', 0, 0, 'L');
        $this->fpdf->Ln(38);
        $this->fpdf->SetFont('Arial', 'BU', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Achmad Firmansyah', 0, 0, 'L');
        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('Arial', 'B', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Manager (HRD-GA)', 0, 0, 'L');
        $this->fpdf->Output();
        exit;
    }

    public function exportExcel()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Perusahaan');
        $sheet->setCellValue('C1', 'Area');
        $sheet->setCellValue('D1', 'Golongan');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Penempatan');
        $sheet->setCellValue('G1', 'NIK');
        $sheet->setCellValue('H1', 'Nama');
        $sheet->setCellValue('I1', 'Email');
        $sheet->setCellValue('J1', 'NPWP');
        $sheet->setCellValue('K1', 'Tempat Lahir');
        $sheet->setCellValue('L1', 'Tanggal Lahir');
        $sheet->setCellValue('M1', 'Agama');
        $sheet->setCellValue('N1', 'Jenis Kelamin');
        $sheet->setCellValue('O1', 'Nomor Handphone');
        $sheet->setCellValue('P1', 'Pendidikan Terakhir');
        $sheet->setCellValue('Q1', 'Golongan Darah');
        $sheet->setCellValue('R1', 'Nomor BPJS Kesehatan');
        $sheet->setCellValue('S1', 'Nomor BPJS Ketenagakerjaan');
        $sheet->setCellValue('T1', 'Nomor Kartu Keluarga');
        $sheet->setCellValue('U1', 'Status Nikah');
        $sheet->setCellValue('V1', 'Nama Ayah');
        $sheet->setCellValue('W1', 'Nama Ibu');
        $sheet->setCellValue('X1', 'Nama Bank');
        $sheet->setCellValue('Y1', 'Nomor Rekening');
        $sheet->setCellValue('Z1', 'Status Kerja');
        $sheet->setCellValue('AA1', 'Tanggal Mulai Kerja');
        $sheet->setCellValue('AB1', 'Tanggal Akhir Kerja');
        $sheet->setCellValue('AC1', 'Alamat');
        $sheet->setCellValue('AD1', 'RT');
        $sheet->setCellValue('AE1', 'RW');
        $sheet->setCellValue('AF1', 'Kelurahan');
        $sheet->setCellValue('AG1', 'Kecamatan');
        $sheet->setCellValue('AH1', 'Kabupaten/Kota');
        $sheet->setCellValue('AI1', 'Provinsi');
        $sheet->setCellValue('AJ1', 'Kode POS');
        // Header

        //Style
        $sheet->getStyle('A1:AJ1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '4CAF50'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);
        //Style


        $employees  = Employees::with(['golongans','areas','divisions','positions','companies'])->get();

        $row = 2;
        $no = 1;
        foreach ($employees as $employee) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $employee->companies->nama_perusahaan);
                $sheet->setCellValue('C'.$row, $employee->areas->area);
                $sheet->setCellValue('D'.$row, $employee->golongans->golongan);
                $sheet->setCellValue('E'.$row, $employee->positions->jabatan);
                $sheet->setCellValue('F'.$row, $employee->divisions->penempatan);
                $sheet->setCellValue('G'.$row, $employee->nik_karyawan);
                $sheet->setCellValue('H'.$row, $employee->nama_karyawan);
                $sheet->setCellValue('I'.$row, $employee->email_karyawan);
                $sheet->setCellValue('J'.$row, "'".$employee->nomor_npwp);
                $sheet->setCellValue('K'.$row, $employee->tempat_lahir);
                $sheet->setCellValue('L'.$row, $employee->tanggal_lahir);
                $sheet->setCellValue('M'.$row, $employee->agama);
                $sheet->setCellValue('N'.$row, $employee->jenis_kelamin);
                $sheet->setCellValue('O'.$row, "'".$employee->nomor_handphone);
                $sheet->setCellValue('P'.$row, $employee->pendidikan_terakhir);
                $sheet->setCellValue('Q'.$row, $employee->golongan_darah);
                $sheet->setCellValue('R'.$row, "'".$employee->nomor_bpjskesehatan);
                $sheet->setCellValue('S'.$row, "'".$employee->nomor_bpjsketenagakerjaan);
                $sheet->setCellValue('T'.$row, "'".$employee->nomor_kartu_keluarga);
                $sheet->setCellValue('U'.$row, $employee->status_nikah);
                $sheet->setCellValue('V'.$row, $employee->nama_ayah);
                $sheet->setCellValue('W'.$row, $employee->nama_ibu);
                $sheet->setCellValue('X'.$row, $employee->nama_bank);
                $sheet->setCellValue('Y'.$row, "'".$employee->nomor_rekening);
                $sheet->setCellValue('Z'.$row, $employee->status_kerja);
                $sheet->setCellValue('AA'.$row, $employee->tanggal_mulai_kerja);
                $sheet->setCellValue('AB'.$row, $employee->tanggal_akhir_kerja);
                $sheet->setCellValue('AC'.$row, $employee->alamat);
                $sheet->setCellValue('AD'.$row, "'".$employee->rt);
                $sheet->setCellValue('AE'.$row, "'".$employee->rw);
                $sheet->setCellValue('AF'.$row, $employee->kelurahan);
                $sheet->setCellValue('AG'.$row, $employee->kecamatan);
                $sheet->setCellValue('AH'.$row, $employee->kota);
                $sheet->setCellValue('AI'.$row, $employee->provinsi);
                $sheet->setCellValue('AJ'.$row, "'".$employee->kode_pos);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:AJ{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:AJ{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G2:G{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J2:J{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("M2:M{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("N2:N{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("O2:O{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("P2:P{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("Q2:Q{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("R2:R{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("S2:S{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("T2:T{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("U2:U{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("X2:X{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("Y2:Y{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("Z2:Z{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AD2:AD{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AE2:AE{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AJ2:AJ{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Auto width
        $highestColumn = $sheet->getHighestColumn(); 
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'DatabaseSeluruhKaryawan.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\RekonSalaryUpdateRequest;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use App\Models\Admin\Areas;
use App\Models\Admin\HistorySalaries;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\MaksimalUpahBpjsKesehatans;
use App\Models\Admin\MaksimalUpahBpjsKetenagakerjaans;
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

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.salary.index');
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

        $item_salary     = HistorySalaries::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('id',$id)->first();
        
        return view('admin.pages.salary.edit_rekon_salary',[
                    'item_salary'  => $item_salary
                ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RekonSalaryUpdateRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data               = $request->except('_token');
        $gaji_pokok         = $request->input('gaji_pokok');
        $uang_makan         = $request->input('uang_makan');
        $uang_transport     = $request->input('uang_transport');
        $tunjangan_tugas    = $request->input('tunjangan_tugas');
        $tunjangan_pulsa    = $request->input('tunjangan_pulsa');
        $tunjangan_jabatan  = $request->input('tunjangan_jabatan');
        $jht                = $request->input('jht');
        $jp                 = $request->input('jp');
        $jkk                = $request->input('jkk');
        $jkm                = $request->input('jkm');
        $jkn                = $request->input('jkn');

        //Ikut Semua Kepesertaan BPJS Ketenagakerjaan Dan Kesehatan
        if ($jht != 0 && $jp != 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id', 1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {

                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jp_perusahaan         = $jumlah_upah * 2 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
                $potongan_jp_karyawan           = $jumlah_upah * 1 / 100;
            } elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan * 2 / 100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan * 1 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan * 4 / 100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan * 2 / 100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan * 1 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan, 0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan, 0);
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan, 0);
            $hasil_potongan_jp_perusahaan       = round($potongan_jp_perusahaan, 0);
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan, 0);
            $hasil_potongan_jp_karyawan         = round($potongan_jp_karyawan, 0);

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Tidak Ikut Semua Kepesertaan BPJS Ketenagakerjaan Dan Kesehatan
        elseif ($jht == 0 && $jp == 0 && $jkk == 0 && $jkm == 0 && $jkn == 0) {

            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = 0;
            $hasil_potongan_jkk_perusahaan      = 0;
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Ikut Kepesertaan BPJS Ketenagakerjaan Dan Tidak Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht != 0 && $jp != 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id', 1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jp_perusahaan         = $jumlah_upah * 2 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
                $potongan_jp_karyawan           = $jumlah_upah * 1 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
                $potongan_jp_perusahaan         = $maksimal_upah_bpjs_ketenagakerjaan * 2 / 100;
                $potongan_jp_karyawan           = $maksimal_upah_bpjs_ketenagakerjaan * 1 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan, 0);
            $hasil_potongan_jp_perusahaan       = round($potongan_jp_perusahaan, 0);
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan, 0);
            $hasil_potongan_jp_karyawan         = round($potongan_jp_karyawan, 0);

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Ikut Kepesertaan BPJS Kesehatan Dan Tidak Ikut Kepesertaan BPJS Ketenagakerjaan 
        elseif ($jht == 0 && $jp == 0 && $jkk == 0 && $jkm == 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id', 1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan * 4 / 100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan * 1 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan, 0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan, 0);
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = 0;
            $hasil_potongan_jkk_perusahaan      = 0;
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Tidak Ikut JHT Dan JP, Hanya Ikut JKK Dan JKM Dan Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht == 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id', 1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
            } elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan * 4 / 100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan * 1 / 100;

                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan, 0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan, 0);
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Tidak Ikut JHT, JP, dan BPJS Kesehatan, Hanya Ikut JKK Dan JKM
        elseif ($jht == 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = 0;
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = 0;
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Tidak Ikut JP, Hanya Ikut JHT, JKK Dan JKM Dan Ikut Kepesertaan BPJS Kesehatan
        elseif ($jht != 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn != 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjskesehatans                 = MaksimalUpahBpjsKesehatans::where('id', 1)->first();
            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_kesehatan       = $itemBpjskesehatans->maksimal_upah_bpjskesehatan;
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
            } elseif ($jumlah_upah <= $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $jumlah_upah * 4 / 100;
                $potongan_bpjsks_karyawan       = $jumlah_upah * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_kesehatan && $jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_bpjsks_perusahaan     = $maksimal_upah_bpjs_kesehatan * 4 / 100;
                $potongan_bpjsks_karyawan       = $maksimal_upah_bpjs_kesehatan * 1 / 100;

                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = round($potongan_bpjsks_perusahaan, 0);
            $hasil_potongan_bpjsks_karyawan     = round($potongan_bpjsks_karyawan, 0);
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan, 0);
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan, 0);
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Tidak Ikut JP, dan BPJS Kesehatan, Hanya Ikut JHT, JKK , Dan JKM
        elseif ($jht != 0 && $jp == 0 && $jkk != 0 && $jkm != 0 && $jkn == 0) {
            //End Rumus
            $jumlah_upah                        = $gaji_pokok + $uang_makan + $uang_transport ;
            $upah_lembur_perjam                 = $jumlah_upah / 173;
            $hasil_upah_lembur_perjam           = round($upah_lembur_perjam);

            $itemBpjsketenagakerjaans           = MaksimalUpahBpjsKetenagakerjaans::where('id', 1)->first();
            $maksimal_upah_bpjs_ketenagakerjaan = $itemBpjsketenagakerjaans->maksimal_upah_bpjsketenagakerjaan;

            if ($jumlah_upah <= $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
            } elseif ($jumlah_upah > $maksimal_upah_bpjs_ketenagakerjaan) {
                $potongan_jht_perusahaan        = $jumlah_upah * 3.7 / 100;
                $potongan_jkm_perusahaan        = $jumlah_upah * 0.3 / 100;
                $potongan_jkk_perusahaan        = $jumlah_upah * 0.24 / 100;
                $potongan_jht_karyawan          = $jumlah_upah * 2 / 100;
            } else {
                dd('Salah');
            }

            $hasil_potongan_bpjsks_perusahaan   = 0;
            $hasil_potongan_bpjsks_karyawan     = 0;
            $hasil_potongan_jht_perusahaan      = round($potongan_jht_perusahaan, 0);
            $hasil_potongan_jp_perusahaan       = 0;
            $hasil_potongan_jkm_perusahaan      = round($potongan_jkm_perusahaan, 0);
            $hasil_potongan_jkk_perusahaan      = round($potongan_jkk_perusahaan, 0);
            $hasil_potongan_jht_karyawan        = round($potongan_jht_karyawan, 0);
            $hasil_potongan_jp_karyawan         = 0;

            $jumlah_bpjstk_perusahaan           = $hasil_potongan_jht_perusahaan + $hasil_potongan_jp_perusahaan + $hasil_potongan_jkm_perusahaan + $hasil_potongan_jkk_perusahaan;
            $jumlah_bpjstk_karyawan             = $hasil_potongan_jht_karyawan + $hasil_potongan_jp_karyawan;
            $take_home_pay                      = round($jumlah_upah + $tunjangan_tugas + $tunjangan_pulsa + $tunjangan_jabatan - $jumlah_bpjstk_karyawan - $hasil_potongan_bpjsks_karyawan, -1);
            //End Rumus
        }

        //Kondisi Salah
        else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('salary.index');
        }

        $salary = HistorySalaries::where('id', $id)->first();
        $salary->update([
            'gaji_pokok'                    => $gaji_pokok,
            'uang_makan'                    => $uang_makan,
            'uang_transport'                => $uang_transport,
            'tunjangan_tugas'               => $tunjangan_tugas,
            'tunjangan_pulsa'               => $tunjangan_pulsa,
            'tunjangan_jabatan'             => $tunjangan_jabatan,
            'jumlah_upah'                   => $jumlah_upah,
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
            'edit_oleh'                     => Auth::user()->name
        ]);

        Alert::success('Success Edit Data Gaji Karyawan', 'Oleh ' . auth()->user()->name);
        return redirect()->route('salary.index');
    
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

    public function rekon_salary()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.salary.rekon_salary');
    }

    public function proses_rekon_salary (TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_awal       = $request->input('tanggal_awal');
        $tanggal_akhir      = $request->input('tanggal_akhir');

        $item_salaries     = HistorySalaries::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->get();

        $salary = RekapSalaries::where('periode_awal', $tanggal_awal)->first();

        if ($salary != NULL) {
            Alert::error('Data Tidak Ditemukan Atau Sudah Di Rekon');
            return redirect()->route('salary.index');
        } else {
            return view('admin.pages.salary.tampil_rekon_salary',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_salaries' => $item_salaries
            ]);
        }   

    }

    public function export_excell_rekon()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Golongan');
        $sheet->setCellValue('E1', 'Area');
        $sheet->setCellValue('F1', 'Jabatan');
        $sheet->setCellValue('G1', 'Penempatan');
        $sheet->setCellValue('H1', 'Nomor Rekening');
        $sheet->setCellValue('I1', 'Gaji Pokok');
        $sheet->setCellValue('J1', 'Uang Makan');
        $sheet->setCellValue('K1', 'Uang Transport');
        $sheet->setCellValue('L1', 'Tunjangan Tugas');
        $sheet->setCellValue('M1', 'Tunjangan Pulsa');
        $sheet->setCellValue('N1', 'Tunjangan Jabatan');
        $sheet->setCellValue('O1', 'Jumlah Upah');
        $sheet->setCellValue('P1', 'Upah Lembur Perjam');
        $sheet->setCellValue('Q1', 'Potongan BPJS Kesehatan Perusahaan');
        $sheet->setCellValue('R1', 'Potongan BPJS Kesehatan Karyawan');
        $sheet->setCellValue('S1', 'Potongan JHT Perusahaan');
        $sheet->setCellValue('T1', 'Potongan JHT Karyawan');
        $sheet->setCellValue('U1', 'Potongan JP Perusahaan');
        $sheet->setCellValue('V1', 'Potongan JP Karyawan');
        $sheet->setCellValue('W1', 'Potongan JKM');
        $sheet->setCellValue('X1', 'Potongan JKK');
        $sheet->setCellValue('Y1', 'Jumlah BPJSTK Perusahaan');
        $sheet->setCellValue('Z1', 'Jumlah BPJSTK Karyawan');
        $sheet->setCellValue('AA1', 'Take Home Pay');
    
        // Header

        //Style
        $sheet->getStyle('A1:AA1')->applyFromArray([
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

        $item_history_salaries = HistorySalaries::with([
                                'employees',
                                'employees.divisions',
                                'employees.positions',
                                'employees.golongans',
                                'employees.areas'
                                ])->get()->sortBy('employees.nama_karyawan');

        $row = 2;
        $no = 1;
        foreach ($item_history_salaries as $item_history_salary) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_history_salary->nik_karyawan);
                $sheet->setCellValue('C'.$row, "'".$item_history_salary->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, "'".$item_history_salary->employees->golongans->golongan);
                $sheet->setCellValue('E'.$row, "'".$item_history_salary->employees->areas->area);
                $sheet->setCellValue('F'.$row, "'".$item_history_salary->employees->positions->jabatan);
                $sheet->setCellValue('G'.$row, "'".$item_history_salary->employees->divisions->penempatan);
                $sheet->setCellValue('H'.$row, "'".$item_history_salary->employees->nomor_rekening);
                $sheet->setCellValue('I'.$row, "'".$item_history_salary->gaji_pokok);
                $sheet->setCellValue('J'.$row, "'".$item_history_salary->uang_makan);
                $sheet->setCellValue('K'.$row, "'".$item_history_salary->uang_transport);
                $sheet->setCellValue('L'.$row, "'".$item_history_salary->tunjangan_tugas);
                $sheet->setCellValue('M'.$row, "'".$item_history_salary->tunjangan_pulsa);
                $sheet->setCellValue('N'.$row, "'".$item_history_salary->tunjangan_jabatan);
                $sheet->setCellValue('O'.$row, "'".$item_history_salary->jumlah_upah);
                $sheet->setCellValue('P'.$row, "'".$item_history_salary->upah_lembur_perjam);
                $sheet->setCellValue('Q'.$row, "'".$item_history_salary->potongan_bpjsks_perusahaan);
                $sheet->setCellValue('R'.$row, "'".$item_history_salary->potongan_bpjsks_karyawan);
                $sheet->setCellValue('S'.$row, "'".$item_history_salary->potongan_jht_perusahaan);
                $sheet->setCellValue('T'.$row, "'".$item_history_salary->potongan_jht_karyawan);
                $sheet->setCellValue('U'.$row, "'".$item_history_salary->potongan_jp_perusahaan);
                $sheet->setCellValue('V'.$row, "'".$item_history_salary->potongan_jp_karyawan);
                $sheet->setCellValue('W'.$row, "'".$item_history_salary->potongan_jkk_perusahaan);
                $sheet->setCellValue('X'.$row, "'".$item_history_salary->potongan_jkm_perusahaan);
                $sheet->setCellValue('Y'.$row, "'".$item_history_salary->jumlah_bpjstk_perusahaan);
                $sheet->setCellValue('Z'.$row, "'".$item_history_salary->jumlah_bpjstk_karyawan);
                $sheet->setCellValue('AA'.$row, "'".$item_history_salary->take_home_pay);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:AA{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:AA{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:AA{$lastRow}")
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

        $filename = 'RekonsiliasiGaji.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function hasil_rekon_salary(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $items = HistorySalaries::all();

        DB::transaction(function () use ($items, $tanggal_awal, $tanggal_akhir) {
        foreach ($items as $item) {

            RekapSalaries::create([
                'periode_awal'                      => $tanggal_awal,
                'periode_akhir'                     => $tanggal_akhir,
                'employees_id'                      => $item->employees_id,
                'nik_karyawan'                      => $item->nik_karyawan,
                'gaji_pokok'                        => $item->gaji_pokok,
                'uang_makan'                        => $item->uang_makan,
                'uang_transport'                    => $item->uang_transport,
                'tunjangan_tugas'                   => $item->tunjangan_tugas,
                'tunjangan_pulsa'                   => $item->tunjangan_pulsa,
                'tunjangan_jabatan'                 => $item->tunjangan_jabatan,
                'jumlah_upah'                       => $item->jumlah_upah,
                'upah_lembur_perjam'                => $item->upah_lembur_perjam,
                'potongan_bpjsks_perusahaan'        => $item->potongan_bpjsks_perusahaan,
                'potongan_jht_perusahaan'           => $item->potongan_jht_perusahaan,
                'potongan_jp_perusahaan'            => $item->potongan_jp_perusahaan,
                'potongan_jkm_perusahaan'           => $item->potongan_jkm_perusahaan,
                'potongan_jkk_perusahaan'           => $item->potongan_jkk_perusahaan,
                'jumlah_bpjstk_perusahaan'          => $item->jumlah_bpjstk_perusahaan,
                'potongan_bpjsks_karyawan'          => $item->potongan_bpjsks_karyawan,
                'potongan_jht_karyawan'             => $item->potongan_jht_karyawan,
                'potongan_jp_karyawan'              => $item->potongan_jp_karyawan,
                'jumlah_bpjstk_karyawan'            => $item->jumlah_bpjstk_karyawan,
                'take_home_pay'                     => $item->take_home_pay,
                'input_oleh'                        => Auth::user()->name
            ]);
        }
        });

        Alert::success('Success Rekonsiliasi Data Gaji', 'Oleh ' . auth()->user()->name);
        return redirect()->route('salary.index');

    }

    public function rekap_salary()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.salary.rekap_salary');
    }

    public function proses_rekap_salary(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_awal       = $request->input('tanggal_awal');
        $tanggal_akhir      = $request->input('tanggal_akhir');

        $item_salaries     = RekapSalaries::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('periode_awal', $tanggal_awal)->where('periode_akhir', $tanggal_akhir)->get();


        if ($item_salaries->isEmpty()) {
            Alert::error('Data Tidak Ditemukan Atau Belum Di Rekon');
            return redirect()->route('salary.index');
        } else {
            return view('admin.pages.salary.tampil_rekap_salary',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_salaries' => $item_salaries
            ]);
        }   
    }

    public function cancel_rekap_gaji(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $awal               = $request->input('tanggal_awal');
        $akhir              = $request->input('tanggal_akhir');
        
        DB::transaction(function () use ($awal, $akhir)  {

            $rekapsalaries = RekapSalaries::where('periode_awal', $awal)->where('periode_akhir', $akhir);
            $rekapsalaries->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $rekapsalaries->delete();
        });

        Alert::success('Success Proses Cancel Rekap Gaji', 'Oleh ' . auth()->user()->name);
        return redirect()->route('salary.index');
    }

    public function export_excell_rekap(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Golongan');
        $sheet->setCellValue('E1', 'Area');
        $sheet->setCellValue('F1', 'Jabatan');
        $sheet->setCellValue('G1', 'Penempatan');
        $sheet->setCellValue('H1', 'Nomor Rekening');
        $sheet->setCellValue('I1', 'Gaji Pokok');
        $sheet->setCellValue('J1', 'Uang Makan');
        $sheet->setCellValue('K1', 'Uang Transport');
        $sheet->setCellValue('L1', 'Tunjangan Tugas');
        $sheet->setCellValue('M1', 'Tunjangan Pulsa');
        $sheet->setCellValue('N1', 'Tunjangan Jabatan');
        $sheet->setCellValue('O1', 'Jumlah Upah');
        $sheet->setCellValue('P1', 'Upah Lembur Perjam');
        $sheet->setCellValue('Q1', 'Potongan BPJS Kesehatan Perusahaan');
        $sheet->setCellValue('R1', 'Potongan BPJS Kesehatan Karyawan');
        $sheet->setCellValue('S1', 'Potongan JHT Perusahaan');
        $sheet->setCellValue('T1', 'Potongan JHT Karyawan');
        $sheet->setCellValue('U1', 'Potongan JP Perusahaan');
        $sheet->setCellValue('V1', 'Potongan JP Karyawan');
        $sheet->setCellValue('W1', 'Potongan JKM');
        $sheet->setCellValue('X1', 'Potongan JKK');
        $sheet->setCellValue('Y1', 'Jumlah BPJSTK Perusahaan');
        $sheet->setCellValue('Z1', 'Jumlah BPJSTK Karyawan');
        $sheet->setCellValue('AA1', 'Take Home Pay');
    
        // Header

        //Style
        $sheet->getStyle('A1:AA1')->applyFromArray([
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

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $item_rekap_salaries = RekapSalaries::with([
                                'employees',
                                'employees.divisions',
                                'employees.positions',
                                'employees.golongans',
                                'employees.areas'
                                ])->where('periode_awal',$tanggal_awal)->where('periode_akhir',$tanggal_akhir)->get()->sortBy('employees.nama_karyawan');

        $row = 2;
        $no = 1;
        foreach ($item_rekap_salaries as $item_rekap_salary) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_rekap_salary->nik_karyawan);
                $sheet->setCellValue('C'.$row, "'".$item_rekap_salary->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, "'".$item_rekap_salary->employees->golongans->golongan);
                $sheet->setCellValue('E'.$row, "'".$item_rekap_salary->employees->areas->area);
                $sheet->setCellValue('F'.$row, "'".$item_rekap_salary->employees->positions->jabatan);
                $sheet->setCellValue('G'.$row, "'".$item_rekap_salary->employees->divisions->penempatan);
                $sheet->setCellValue('H'.$row, "'".$item_rekap_salary->employees->nomor_rekening);
                $sheet->setCellValue('I'.$row, "'".$item_rekap_salary->gaji_pokok);
                $sheet->setCellValue('J'.$row, "'".$item_rekap_salary->uang_makan);
                $sheet->setCellValue('K'.$row, "'".$item_rekap_salary->uang_transport);
                $sheet->setCellValue('L'.$row, "'".$item_rekap_salary->tunjangan_tugas);
                $sheet->setCellValue('M'.$row, "'".$item_rekap_salary->tunjangan_pulsa);
                $sheet->setCellValue('N'.$row, "'".$item_rekap_salary->tunjangan_jabatan);
                $sheet->setCellValue('O'.$row, "'".$item_rekap_salary->jumlah_upah);
                $sheet->setCellValue('P'.$row, "'".$item_rekap_salary->upah_lembur_perjam);
                $sheet->setCellValue('Q'.$row, "'".$item_rekap_salary->potongan_bpjsks_perusahaan);
                $sheet->setCellValue('R'.$row, "'".$item_rekap_salary->potongan_bpjsks_karyawan);
                $sheet->setCellValue('S'.$row, "'".$item_rekap_salary->potongan_jht_perusahaan);
                $sheet->setCellValue('T'.$row, "'".$item_rekap_salary->potongan_jht_karyawan);
                $sheet->setCellValue('U'.$row, "'".$item_rekap_salary->potongan_jp_perusahaan);
                $sheet->setCellValue('V'.$row, "'".$item_rekap_salary->potongan_jp_karyawan);
                $sheet->setCellValue('W'.$row, "'".$item_rekap_salary->potongan_jkk_perusahaan);
                $sheet->setCellValue('X'.$row, "'".$item_rekap_salary->potongan_jkm_perusahaan);
                $sheet->setCellValue('Y'.$row, "'".$item_rekap_salary->jumlah_bpjstk_perusahaan);
                $sheet->setCellValue('Z'.$row, "'".$item_rekap_salary->jumlah_bpjstk_karyawan);
                $sheet->setCellValue('AA'.$row, "'".$item_rekap_salary->take_home_pay);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:AA{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:AA{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:AA{$lastRow}")
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

        $filename = 'RekapGaji.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function cetak_slip(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $id             = $request->input('id');
        $employees_id   = $request->input('employees_id');
        $nik_karyawan   = $request->input('nik_karyawan');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $judul = Employees::with([
            'areas',
            'divisions',
            'positions'
        ])->where('id', $employees_id)->first();

        $hasilslip = RekapSalaries::where('periode_awal', $tanggal_awal)
            ->where('periode_akhir', $tanggal_akhir)
            ->where('employees_id', $employees_id)
            ->first();

        $this->fpdf = new FPDF('L', 'cm', array(21, 14));
        $this->fpdf->setTopMargin(0.1);
        $this->fpdf->setLeftMargin(0.6);
        $this->fpdf->AddPage();

        $this->fpdf->Ln(0.1);

        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(10, 1, "PT Prima Komponen Indonesia", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(10, 1, "Jl.Kawasan Industri Taman Tekno, Blok F2. No.10-11, F1J, F1 A2", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(10, 1, "Setu, Setu, Tangerang Selatan, 15314.", 0, 0, 'L');

        $this->fpdf->SetFont('Arial', 'B', '10');
        $this->fpdf->Ln(0.3);
        $this->fpdf->Cell(20, 1, "Bukti Tanda Terima Slip Gaji", 0, 0, 'C');
        $this->fpdf->SetFont('Arial', '', '10');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(20, 1, "Periode " . \Carbon\Carbon::parse($tanggal_akhir)->isoformat('MMMM Y') . "", 0, 0, 'C');
        $this->fpdf->Ln(0.3);
        $this->fpdf->Cell(22, 1, "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------", 0, 0, 'C');
        $this->fpdf->Ln(0.3);
        $this->fpdf->SetFont('Arial', 'B', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(3, 1, "Nama ", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(5, 1, $judul->nama_karyawan, 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', 'B', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(3, 1, "Tanggal Mulai Kerja ", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(5, 1, \Carbon\Carbon::parse($judul->tanggal_mulai_kerja)->isoformat('D MMMM Y') . '', 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', 'B', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(3, 1, "Jabatan ", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(5, 1, $judul->positions->jabatan . " / " . $judul->divisions->penempatan . "", 0, 0, 'L');

        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Ln(0.3);
        $this->fpdf->Cell(22, 1, "--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------", 0, 0, 'C');
        $this->fpdf->Ln(0.3);

        $this->fpdf->SetFont('Arial', 'BI', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Gaji Pokok ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->gaji_pokok), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Uang Makan ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->uang_makan), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Uang Transport ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->uang_transport), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Tunjangan Tugas ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->tunjangan_tugas), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Tunjangan Pulsa ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->tunjangan_pulsa), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Tunjangan Jabatan ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->tunjangan_jabatan), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.5);
        $this->fpdf->SetFont('Arial', 'BI', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(3, 1, "Potongan ", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Iuran BPJS Ketenagakerjaan(JHT) 2%", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->potongan_jht_karyawan), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Iuran BPJS Ketenagakerjaan(JP) 1%", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->potongan_jp_karyawan), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "Iuran BPJS Kesehatan 1%", 0, 0, 'L');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.7, 1, number_format($hasilslip->potongan_bpjsks_karyawan), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'I', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.3);
        $this->fpdf->Cell(22, 1, "--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------", 0, 0, 'C');
        $this->fpdf->Ln(0.4);

        $this->fpdf->SetFont('Arial', 'BI', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(5.4, 1, "JUMLAH UPAH YANG DITERIMA ", 0, 0, 'L');
        $this->fpdf->SetFont('Arial', 'B', '9');
        $this->fpdf->Cell(0.6, 1, " : ", 0, 0, 'L');
        $this->fpdf->Cell(0.5, 1, "Rp.", 0, 0, 'L');
        $this->fpdf->Cell(1.8, 1, number_format($hasilslip->take_home_pay), 0, 0, 'R');
        $this->fpdf->SetFont('Arial', 'BI', '9');
        $this->fpdf->Cell(1.5, 1, "Perbulan", 0, 0, 'L');

        $this->fpdf->Ln(0.5);
        $this->fpdf->SetFont('Arial', 'BI', '8');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(1.8, 1, "Tangerang Selatan, " . \Carbon\Carbon::parse($tanggal_akhir)->isoformat('MMMM Y') . "", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(1.8, 1, "Mengetahui", 0, 0, 'L');
        $this->fpdf->Cell(11.5);
        $this->fpdf->Cell(1.8, 1, "Menerima", 0, 0, 'C');

        $this->fpdf->Ln(1.6);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(1.8, 1, "Achmad Firmansyah", 0, 0, 'L');
        $this->fpdf->Cell(11.5);
        $this->fpdf->Cell(1.8, 1, $judul->nama_karyawan, 0, 0, 'C');

        $this->fpdf->Ln(0.3);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(1.8, 1, "( Manager HRD - GA )", 0, 0, 'L');
        $this->fpdf->Cell(11.5);
        $this->fpdf->Cell(1.8, 1, "( " . $judul->positions->jabatan . " " .  $judul->divisions->penempatan . " )", 0, 0, 'C');

        $this->fpdf->Output();
        exit;
    }
    
}

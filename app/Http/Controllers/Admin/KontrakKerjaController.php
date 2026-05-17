<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ProsesTanggalKontrakKerjaRequest;
use App\Http\Requests\Admin\NamaTanggalAwalAkhirRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Models\Admin\Employees;
use App\Models\Admin\HistoryContracts;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use DB;
use Alert;
use Carbon\Carbon;

class KontrakKerjaController extends Controller
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

    public function form_kontrak_harian()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['areas','golongans','divisions','positions'])->where('status_kerja','Harian')->get();
        return view('admin.pages.kontrak_kerja.harian.index',['employees'=> $employees]);
    }

    public function form_proses_tanggal_harian()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.kontrak_kerja.harian.form_berdasarkan_tanggal');
    }

    public function proses_tanggal_harian(ProsesTanggalKontrakKerjaRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data                       = $request->except('_token');
        $tanggal_akhir_kontrak      = $request->input('tanggal_akhir_kontrak');
        $awal_kontrak               = date_create($request->input('tanggal_awal_perpanjang'));
        $akhir_kontrak              = date_create($request->input('tanggal_akhir_perpanjang'));
        $interval                   = date_diff($awal_kontrak, $akhir_kontrak);
        $totalBulan                 = ($interval->y * 12) + $interval->m;
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

        $employees      = Employees::with(['areas','golongans','divisions','positions'])->where('tanggal_akhir_kerja', $tanggal_akhir_kontrak)->where('status_kerja','Harian')->get();

        if ($employees->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return view('admin.pages.kontrak_kerja.harian.index',['employees'=> $employees]);
        }

        DB::transaction(function () use ($request, $masa_kontrak, $employees) 
        {
            foreach ($employees as $employee) 
            {
                HistoryContracts::create([
                    'employees_id'          => $employee->id,
                    'nik_karyawan'          => $employee->nik_karyawan,
                    'tanggal_awal_kontrak'  => $request->input('tanggal_awal_perpanjang'),
                    'tanggal_akhir_kontrak' => $request->input('tanggal_akhir_perpanjang'),
                    'status_kontrak_kerja'  => 'Harian',
                    'masa_kontrak'          => $masa_kontrak,
                    'jumlah_kontrak'        => 1,
                    'input_oleh'            => auth()->user()->name ]);

                    $employee->update([
                    'tanggal_akhir_kerja' => $request->input('tanggal_akhir_perpanjang'),
                    'edit_oleh' => auth()->user()->name]);
            }
        });

        Alert::success('Success Proses PKWT Harian', 'Oleh ' . auth()->user()->name);
        return view('admin.pages.kontrak_kerja.harian.index',['employees'=> $employees]);
    }

    public function form_proses_nama_harian()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['divisions','positions',])->where('status_kerja','Harian')->get();
        return view('admin.pages.kontrak_kerja.harian.form_berdasarkan_nama',['employees'=> $employees]);
    }

    public function proses_nama_harian(NamaTanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $awal_kontrak   = date_create($request->input('tanggal_awal'));
        $akhir_kontrak  = date_create($request->input('tanggal_akhir'));
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

        $employees = Employees::whereIn('id', $request->employees_id)->get();

        if ($employees->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return view('admin.pages.kontrak_kerja.harian.index',['employees'=> $employees]);
        }

        DB::transaction(function () use ($request, $masa_kontrak, $employees) 
        {
            foreach ($employees as $employee)
            {
                HistoryContracts::create([
                'employees_id'          => $employee->id,
                'nik_karyawan'          => $employee->nik_karyawan,
                'tanggal_awal_kontrak'  => $request->tanggal_awal,
                'tanggal_akhir_kontrak' => $request->tanggal_akhir,
                'status_kontrak_kerja'  => 'Harian',
                'masa_kontrak'          => $masa_kontrak,
                'jumlah_kontrak'        => 1,
                'input_oleh'            => auth()->user()->name
                ]);

                $employee->update([
                    'tanggal_akhir_kerja' => $request->tanggal_akhir,
                    'edit_oleh' => auth()->user()->name
                ]);
            }
        });
        Alert::success('Success Proses PKWT Harian', 'Oleh ' . auth()->user()->name);
        return view('admin.pages.kontrak_kerja.harian.index',['employees'=> $employees]);
    }

    public function form_cetak_tanggal_harian()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.kontrak_kerja.harian.form_cetak_tanggal');
    }

    public function proses_cetak_tanggal_harian(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $tanggal_salary     = substr($tanggal_akhir, 8, 3);
        $bulan_salary       = substr($tanggal_akhir, 5, 2);
        $tahun_salary       = substr($tanggal_akhir, 0,4);
        $tahun_bulan_salary = $tahun_salary.'-'.$bulan_salary;

        // dd($tahun_bulan_salary);

        $pkwtharians    = HistoryContracts::with([
                        'employees'
                        ])->where('status_kontrak_kerja', 'Harian')
                        ->whereBetween('tanggal_awal_kontrak', [$tanggal_awal, $tanggal_akhir])
                        ->orderBy('tanggal_awal_kontrak', 'ASC')->get();
    
        if ($pkwtharians->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('kontrak_kerja.form_cetak_tanggal_harian');
        }

        $this->fpdf = new FPDF('P', 'mm', 'A4');
        $this->fpdf->setTopMargin(10);
        $this->fpdf->setLeftMargin(4);
        $this->fpdf->SetAutoPageBreak(true);
        $this->fpdf->AddPage();

        foreach ($pkwtharians as $pkwtharian) {
                    

                $items = RekapSalaries::with([
                            'employees',
                            'employees.areas',
                            'employees.golongans',
                            'employees.positions',
                            'employees.divisions',
                            ])
                            ->where('employees_id', $pkwtharian->employees_id)
                            ->where('periode_akhir','like', '%'.$tahun_bulan_salary.'%')->get();

            foreach ($items as $item) {

                    $nomor              = substr($pkwtharian->nik_karyawan, 6,6);
                    $mytime             = Carbon::now();
                    $bulan              = substr($mytime, 5, -12);
                    $tahun              = substr($mytime, 0,4);

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

                    $upah_perhari = $item->jumlah_upah/20;
                    $upah_perbulan = $item->jumlah_upah;

                    $this->fpdf->SetFont('Arial', 'BU', '12');
                    $this->fpdf->Cell(190, 10, 'SURAT PERJANJIAN KERJA HARIAN LEPAS', 0, 0, 'C');
                    $this->fpdf->Ln(5);
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(190, 10, 'No : ' . $nomor . '/ PK / HRD / ' . $romawi . ' / ' . $tahun . '.', 0, 0, 'C');
            
                    $this->fpdf->Ln(10);
        
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Yang bertanda tangan di bawah ini :', 0, 0, 'L');
            
                    $this->fpdf->Ln(9);
        
                    $this->fpdf->Cell(10);
                    $this->fpdf->Cell(10, 7, '1. ', 0, 0, 'L');
                    $this->fpdf->Cell(50, 7, 'Nama', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' Achmad Firmansyah', 0, 0, 'L');
            
                    $this->fpdf->Ln();
        
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Jabatan', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' Manager HRD-GA PT Prima Komponen Indonesia', 0, 0, 'L');
            
                    $this->fpdf->Ln(9);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Dalam hal  ini  bertindak atas nama Manager HRD-GA PT Prima Komponen  Indonesia  yang', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'berkedudukan di Kawasan Industri Pergudangan Taman Tekno Blok F2 No.10-11, Kelurahan', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(120, 5, 'Setu, Kecamatan Setu, Tangerang Selatan. Dan selanjutnya disebut', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(50, 5, ' PIHAK  PERTAMA (I).', 0, 0, 'L');
            
                    $this->fpdf->Ln(8);
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(10);
                    $this->fpdf->Cell(10, 7, '2. ', 0, 0, 'L');
                    $this->fpdf->Cell(50, 7, 'No.KTP/SIM', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');

                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->nik_karyawan, 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Nama', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->nama_karyawan, 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Tempat,Tanggal Lahir', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->tempat_lahir.', '.\Carbon\Carbon::parse($pkwtharian->employees->tanggal_lahir)->isoformat('D MMMM Y'), 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Pendidikan Terakhir', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->pendidikan_terakhir, 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Jenis Kelamin', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->jenis_kelamin, 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Agama', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->agama, 0, 0, 'L');
            
                    $this->fpdf->Ln();
            
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 7, 'Alamat', 0, 0, 'L');
                    $this->fpdf->Cell(5, 7, ' : ', 0, 0, 'C');
                    $this->fpdf->Cell(115, 7, ' '.$pkwtharian->employees->alamat.', '.$pkwtharian->employees->rt.'/'.$pkwtharian->employees->rw.', Kelurahan.'.$pkwtharian->employees->kelurahan, 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(75);
                    $this->fpdf->Cell(115, 7, ' Kecamatan.'.$pkwtharian->employees->kecamatan.', Kota.'.$pkwtharian->employees->kota.', '.$pkwtharian->employees->provinsi, 0, 0, 'L');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(144, 5, 'Dalam  hal  ini  bertindak  untuk dan atas nama dari pribadi dan selanjutnya disebut', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(27, 5, '  PIHAK', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(30, 5, 'KEDUA (II).', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
            
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 1', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PERNYATAAN - PERNYATAAN', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(8);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(96, 5, ' telah   menyatakan   persetujuannya  untuk   menerima', 0, 0, 'L');
                    
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, ' PIHAK  KEDUA', 0, 0, 'L');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'selaku pekerja harian lepas.', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(8);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(28, 6, 'PIHAK KEDUA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(96, 5, 'menyatakan kesediannya selaku pekerja harian lepas yang tunduk pada tata,', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(115, 5, 'tertib, peraturan, dan sistem kerja yang berlaku pada perusahaan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(28, 5, 'PIHAK PERTAMA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 2', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'RUANG LINGKUP PEKERJAAN', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(10);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(57, 5, 'Pekerjaan yang harus dilakukan', 0, 0, 'L');
                    
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(28, 5, 'PIHAK KEDUA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(58, 5, 'selaku pekerja harian lepas pada', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(28, 5, 'PIHAK', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(21, 5, 'PERTAMA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(16, 5, ' adalah ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'BU', '11');
                    $this->fpdf->Cell(60, 5, ''.$item->employees->divisions->penempatan.' / '.$item->employees->positions->jabatan, 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(8);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(28, 5, 'PIHAK KEDUA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(96, 5, 'tidak diperkenankan mengerjakan pekerjaan lain selain yang disebutkan pada,', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(116, 5, 'ayat 1 tersebut di atas, kecuali atas persetujuan dan perintah dari', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(36, 5, ' PIHAK  PERTAMA ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(10, 5, ' atau ', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(15, 5, 'atasan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(36, 5, 'PIHAK KEDUA.', 0, 0, 'L');
            
                    // $this->fpdf->Ln(100);
                    // $this->fpdf->Ln(50);
                    // $this->fpdf->Cell(20);
                    // $this->fpdf->Cell(170, 5, '', 0, 0, 'C');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 3', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'MASA BERLAKU PERJANJIAN KERJA', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(10);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 6, 'Perjanjian kerja ini berlaku untuk jangka waktu 20 hari (dua puluh hari) kerja, terhitung sejak tanggal', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(135, 6, 'penandatanganan surat perjanjian kerja ini dan akan berakhir pada tanggal : ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'BU', '11');
                    $this->fpdf->Cell(30, 6, ' '.\Carbon\Carbon::parse($pkwtharian->tanggal_akhir_kontrak)->isoformat('D MMMM Y').'.', 0, 0, 'L');

                    // //TTD
                    // $this->fpdf->Ln();
                    // $this->fpdf->Cell(10);
                    // $this->fpdf->Image('../public/storage/assets/ttdAchmadFirmansyah.png' , 20,270,30);
                    // $this->fpdf->Ln(5);
                    // //TTD
            
                    $this->fpdf->Ln(100);
        
                    $this->fpdf->Ln(50);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, '', 0, 0, 'C');

                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(117, 5, 'Setelah berakhirnya jangka waktu tersebut. Hubungan kerja antara', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA.', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(15, 5, 'dengan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK KEDUA', 0, 0, 'C');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(115, 5, 'menjadi putus dengan sendirinya tanpa perlu pemberitahuan dari', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(10, 5, 'pada', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK KEDUA.', 0, 0, 'L');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 4', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'CARA KERJA', 0, 0, 'C');
                    
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(95, 5, 'atau wakil perusahaan PT Prima Komponen Indonesia akan memberikan', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(70, 5, 'pengarahan perihal cara kerja sebelum', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, 'PIHAK KEDUA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(65, 5, 'memulai pekerjaannya.', 0, 0, 'L');
            
                    $this->fpdf->Ln(6);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 5', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'JAM KERJA', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Berdasarkan peraturan ketenagakerjaan yang berlaku, jam kerja efektif perusahaan ditetapkan', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, '8 (delapan) jam perhari, 40 (empat puluh) jam perminggu, dengan jumlah hari kerja 5 (lima) hari', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'dalam seminggu.', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(10);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Jam masuk adalah jam 08:00 (delapan) pagi dan jam pulang adalah jam (17:00) (lima sore).', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 3', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '1.  Waktu istirahat pada hari Senin hingga Kamis ditetapkan selama 1 (satu) jam, yaitu', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '     pada pukul 12:00 (dua belas siang) hingga pukul 13:00 (satu siang).', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '2.  Waktu istirahat pada hari Jumat ditetapkan selama 1,5 (satu koma lima) jam, yaitu', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '     pada pukul 11:30 (sebelas tiga puluh siang) hingga pukul 13:00 (satu siang).', 0, 0, 'L');
            
                    $this->fpdf->Ln(10);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 6', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'UPAH DAN PEMBAYARAN', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');

                    if($pkwtharian->employees->golongans_id == 2)
                    {
                        $upah = $upah_perbulan;
                        $hari = "Bulan";
                    }
                    else
                    {
                        $upah = $upah_perhari;
                        $hari = "Hari";
                    }
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(57, 5, 'akan memberikan upah sebesar ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(25, 5, 'Rp.'.number_format($upah), 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(60, 5, 'rupiah setiap '.$hari.' kehadiran', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(15, 5, 'kepada', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, 'PIHAK KEDUA', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Pembayaran  upah  akan  dibayarkan setiap tanggal 25 setiap bulannya. ', 0, 0, 'L');
            
                    $this->fpdf->Ln(6);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 7', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'LEMBUR', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, 'PIHAK KEDUA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(140, 5, 'diharuskan masuk kerja lembur jika tersedia pekerjaan yang harus segera', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(140, 5, 'diselesaikan atau bersifat mendesak (URGENT).', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(77, 6, 'Sebagai imbalan kerja lembur sesuai ayat 1,', 0, 0, 'L');
                    
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(33, 5, 'PIHAK PERTAMA ', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(28, 5, 'akan membayar', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, ' PIHAK KEDUA', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(16, 5, 'sebesar', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(23, 5, 'Rp.'.number_format($item->upah_lembur_perjam), 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(60, 5, 'rupiah setiap jam lembur.', 0, 0, 'L');

                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(40, 5, 'rupiah setiap jam lembur.', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(10);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 3', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(150, 5, 'Pembayaran upah lembur akan di satukan dengan pembayaran upah yang akan diterima', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, 'PIHAK KEDUA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(105, 5, 'sesuai Pasal 6 ayat 2 perjanjian ini.', 0, 0, 'L');
            
                    $this->fpdf->Ln(6);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 8', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'BERAKHIRNYA PERJANJIAN', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(6);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(80, 5, 'Setiap saat hubungan kerja dapat diakhiri jika', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(30, 5, 'PIHAK KEDUA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(50, 5, 'melanggar tata tertib, peraturan,', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(85, 5, 'dan sistem kerja yang berlaku pada perusahaan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA.', 0, 0, 'L');
                    
                    //TTD
                    // $this->fpdf->Ln();
                    // $this->fpdf->Cell(20);
                    // $this->fpdf->Image('../public/storage/assets/ttdAchmadFirmansyah.png' , 20,270,30);
                    // $this->fpdf->Ln(5);
                    //TTD
                    $this->fpdf->Ln(100);
            
                    $this->fpdf->Ln(50);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, '', 0, 0, 'C');

                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(80, 5, 'Pelanggaran yang dimaksud pada ayat 1 tersebut diatas, adalah :', 0, 0, 'L');
            
                    $this->fpdf->Ln(7);
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '1.   Tidak masuk kerja selama 1 (satu) hari kerja tanpa keterangan tertulis atau alasan sah', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      yang dapat dibenarkan oleh atasan atau pihak perusahaan.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '2.   Melakukan tindak penipuan, pencurian, penggelapan, atau tindak-tindak melawan', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      hukum lainnya.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '3.   Menyalahgunakan wewenang dan jabatan untuk kepentingan pribadi.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(125, 5, '4.   Melakukan perusakan dengan sengaja yang menimbulkan kerugian ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA.', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(125, 5, '5.   Melakukan hal-hal lain karena kecerobohannya yang mengakibatkan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      mengalami kerugian.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '6.   Melakukan perjudian di tempat kerja.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '7.   Mabuk-mabukan atau mengkonsumsi narkotika dan obat-obatan terlarang di lingkungan', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      kerja perusahaan.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '8.   Melakukan keributan atau keonaran yang mengganggu suasana kerja di lingkungan kerja', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      perusahaan.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '9.   Melakukan perkelahian atau penganiayaan terhadap pekerja lain.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(29);
                    $this->fpdf->Cell(160, 5,'10.  Menghasut para pekerja lain untuk melakukan mogok kerja.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(29);
                    $this->fpdf->Cell(160, 5,'11.  Merokok ditempat kerja atau membawa rokok dan korek api dalam lingkungan kerja.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(29);
                    $this->fpdf->Cell(160, 5,'12.  Masuk jam kerja tidak tepat waktu selama 2 (dua) kali.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(29);
                    $this->fpdf->Cell(160, 5,'13.  Tidak menggunakan alat keselamatan kerja yang sudah ditetapkan.', 0, 0, 'L');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(29);
                    $this->fpdf->Cell(160, 5,'14.  Tidak menggunakan alat keselamatan dalam berkendara baik berangkat maupun pulang', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(160, 5, '      kerja.', 0, 0, 'L');
            
                    $this->fpdf->Ln(7);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 9', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'KEADAAN DARURAT (FORCE MAJEUR)', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln(7);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Perjanjian kerja ini batal dengan sendirinya jika karena keadaan atau situasi yang memaksa,', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Seperti : Bencana Alam, Pemberontakan, Perang, Huru-hara, Kerusuhan, Peraturan Pemerintah', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'atau apapun.', 0, 0, 'L');
            
                    $this->fpdf->Ln(7);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 10', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PENYELESAIAN PERSELISIHAN', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(7);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 1', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Apabila terjadi perselisihan antara kedua belah pihak, akan diselesaikan secara musyawarah ', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'untuk mencapai mufakat.', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln(7);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Ayat 2', 0, 0, 'C');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Apabila dengan cara ayat 1 pasal ini tidak tercapai kata sepakat, maka kedua belah pihak sepakat', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'untuk menyelesaikan permasalahan tersebut dilakukan melalui prosedur hukum.', 0, 0, 'L');
            
                    $this->fpdf->Ln(7);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PASAL 11', 0, 0, 'C');
            
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'PENUTUP', 0, 0, 'C');
            
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Demikianlah perjanjian ini dibuat, disetujui, dan ditandatangani dalam rangkap dua, asli, dan ', 0, 0, 'L');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'tembusan bermaterai cukup dan berkekuatan hukum yang sama. Satu dipegang oleh', 0, 0, 'L');
            
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Ln();
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(35, 5, 'PIHAK PERTAMA', 0, 0, 'L');
                    
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(35, 5, 'dan lainnya untuk', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(35, 5, 'PIHAK KEDUA.', 0, 0, 'L');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->SetFont('Arial', '', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(170, 5, 'Tangerang Selatan, '.\Carbon\Carbon::parse($pkwtharian->tanggal_awal_kontrak)->isoformat('D MMMM Y'), 0, 0, 'C');
            
                    $this->fpdf->Ln(8);
                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 5, 'PIHAK PERTAMA', 0, 0, 'C');
                    $this->fpdf->Cell(70, 5, '', 0, 0, 'C');
                    $this->fpdf->Cell(50, 5, 'PIHAK KEDUA', 0, 0, 'C');
            
                    $this->fpdf->Ln(30);
                    //TTD
                    // $this->fpdf->Cell(20);
                    // $this->fpdf->Image('../public/storage/assets/ttdAchmadFirmansyah.png' , 25,240,50);
                    // $this->fpdf->Ln(5);
                    //TTD

                    $this->fpdf->SetFont('Arial', 'B', '11');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(50, 5, '( Achmad Firmansyah )', 0, 0, 'C');
                    $this->fpdf->Cell(70, 5, '', 0, 0, 'C');
                    $this->fpdf->Cell(50, 5, '( '.$pkwtharian->employees->nama_karyawan.' )', 0, 0, 'C');
                    $this->fpdf->Ln(100);
                }
            }
        
        $this->fpdf->Output();
        exit;
    }

    public function form_kontrak_pkwt()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['areas','golongans','divisions','positions'])->where('status_kerja','PKWT')->get();
        return view('admin.pages.kontrak_kerja.pkwt.index',['employees'=> $employees]);
    }
    
    public function form_proses_tanggal_pkwt()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.kontrak_kerja.pkwt.form_berdasarkan_tanggal');
    }

    public function proses_tanggal_pkwt(ProsesTanggalKontrakKerjaRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data                       = $request->except('_token');
        $tanggal_akhir_kontrak      = $request->input('tanggal_akhir_kontrak');
        $awal_kontrak               = date_create($request->input('tanggal_awal_perpanjang'));
        $akhir_kontrak              = date_create($request->input('tanggal_akhir_perpanjang'));
        $interval                   = date_diff($awal_kontrak, $akhir_kontrak);
        $totalBulan                 = ($interval->y * 12) + $interval->m;
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

        $employees      = Employees::with(['areas','golongans','divisions','positions'])->where('tanggal_akhir_kerja', $tanggal_akhir_kontrak)->where('status_kerja','PKWT')->get();

        // dd($employees);

        if ($employees->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return view('admin.pages.kontrak_kerja.pkwt.index',['employees'=> $employees]);
        }

        DB::transaction(function () use ($request, $masa_kontrak, $employees) 
        {
            foreach ($employees as $employee) 
            {
                HistoryContracts::create([
                    'employees_id'          => $employee->id,
                    'nik_karyawan'          => $employee->nik_karyawan,
                    'tanggal_awal_kontrak'  => $request->input('tanggal_awal_perpanjang'),
                    'tanggal_akhir_kontrak' => $request->input('tanggal_akhir_perpanjang'),
                    'status_kontrak_kerja'  => 'PKWT',
                    'masa_kontrak'          => $masa_kontrak,
                    'jumlah_kontrak'        => 1,
                    'input_oleh'            => auth()->user()->name ]);

                    $employee->update([
                    'tanggal_akhir_kerja' => $request->input('tanggal_akhir_perpanjang'),
                    'edit_oleh' => auth()->user()->name]);
            }
        });

        Alert::success('Success Proses PKWT', 'Oleh ' . auth()->user()->name);
        return view('admin.pages.kontrak_kerja.pkwt.index',['employees'=> $employees]);
    }

    public function form_proses_nama_pkwt()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['divisions','positions',])->where('status_kerja','PKWT')->get();
        return view('admin.pages.kontrak_kerja.pkwt.form_berdasarkan_nama',['employees'=> $employees]);
    }

    public function proses_nama_pkwt(NamaTanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $awal_kontrak   = date_create($request->input('tanggal_awal'));
        $akhir_kontrak  = date_create($request->input('tanggal_akhir'));
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

        $employees = Employees::whereIn('id', $request->employees_id)->get();

        if ($employees->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return view('admin.pages.kontrak_kerja.pkwt.index',['employees'=> $employees]);
        }

        DB::transaction(function () use ($request, $masa_kontrak, $employees) 
        {
            foreach ($employees as $employee)
            {
                HistoryContracts::create([
                'employees_id'          => $employee->id,
                'nik_karyawan'          => $employee->nik_karyawan,
                'tanggal_awal_kontrak'  => $request->tanggal_awal,
                'tanggal_akhir_kontrak' => $request->tanggal_akhir,
                'status_kontrak_kerja'  => 'PKWT',
                'masa_kontrak'          => $masa_kontrak,
                'jumlah_kontrak'        => 1,
                'input_oleh'            => auth()->user()->name
                ]);

                $employee->update([
                    'tanggal_akhir_kerja' => $request->tanggal_akhir,
                    'edit_oleh' => auth()->user()->name
                ]);
            }
        });
        Alert::success('Success Proses PKWT', 'Oleh ' . auth()->user()->name);
        return view('admin.pages.kontrak_kerja.pkwt.index',['employees'=> $employees]);
    }

    public function form_cetak_tanggal_pkwt()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.kontrak_kerja.pkwt.form_cetak_tanggal');
    }

    public function proses_cetak_tanggal_pkwt(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $tanggal_salary     = substr($tanggal_akhir, 8, 3);
        $bulan_salary       = substr($tanggal_akhir, 5, 2);
        $tahun_salary       = substr($tanggal_akhir, 0,4);
        $tahun_bulan_salary = $tahun_salary.'-'.$bulan_salary;

        $pkwtkontraks    = HistoryContracts::with([
                        'employees'
                        ])->where('status_kontrak_kerja', 'PKWT')
                        ->whereBetween('tanggal_awal_kontrak', [$tanggal_awal, $tanggal_akhir])
                        ->orderBy('tanggal_awal_kontrak', 'ASC')->get();
    
        if ($pkwtkontraks->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('kontrak_kerja.form_cetak_tanggal_pkwt');
        }

        $this->fpdf = new FPDF('P', 'mm', 'A4');
        $this->fpdf->SetAutoPageBreak(true);
        $this->fpdf->AddPage();

        

        foreach ($pkwtkontraks as $pkwtkontrak) {
                    

                $items = RekapSalaries::with([
                            'employees',
                            'employees.areas',
                            'employees.golongans',
                            'employees.positions',
                            'employees.divisions',
                            ])
                            ->where('employees_id', $pkwtkontrak->employees_id)
                            ->where('periode_akhir','like', '%'.$tahun_bulan_salary.'%')->get();
                // dd($items);

            foreach ($items as $item) {

                    $nomor              = substr($pkwtkontrak->nik_karyawan, 6,6);
                    $mytime             = Carbon::now();
                    $bulan              = substr($mytime, 5, -12);
                    $tahun              = substr($mytime, 0,4);

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

                    $indentNomor = 40; 
                    $indentTeks = 30;  
                    $lebarTeks = 165;

                    $this->fpdf->SetFont('Arial', 'BU', '10');
                    $this->fpdf->Cell(190, 10, 'PERJANJIAN KERJA WAKTU TERTENTU', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetFont('Arial', 'B', '10');
                    $this->fpdf->Cell(60);
                    $this->fpdf->Cell(70, 10, 'No.' . $nomor . '/ HRD / PK / ' . $romawi . ' / ' . $tahun . '', 0, 0, 'C');

                    $this->fpdf->Ln(10);

                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(60, 7, 'Yang bertanda tangan dibawah ini :', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(30, 5, 'Nama', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(30, 5, ': Achmad Firmansyah', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(30, 5, 'Jabatan', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(30, 5, ': Manager (HRD-GA)', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(30, 5, 'Alamat', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(140, 5, ': Kawasan Industri Taman tekno, Blok F2. No.10-11 / F. No.1.J', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(50);
                    $this->fpdf->Cell(140, 5, '  Kelurahan Setu, Kecamatan Setu, Tangerang Selatan, 15314.', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Ln(2);
                    $this->fpdf->SetX($indentNomor);
                    $text1 = "Dalam hal ini bertindak untuk dan atas nama PT Prima Komponen Indonesia yang selanjutnya disebut pihak PERTAMA :";
                    $this->fpdf->SetX($indentTeks); 
                    $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');

                    $this->fpdf->Ln(1);

                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(30, 5, 'Nama', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(50, 5, ': ' . $pkwtkontrak->employees->nama_karyawan . '', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(30, 5, 'Tempat & Tgl Lahir', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(50, 5, ': ' . $pkwtkontrak->employees->tempat_lahir . ',' . \Carbon\Carbon::parse($pkwtkontrak->employees->tanggal_lahir)->isoformat('D MMMM Y') . '', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(30, 5, 'Alamat', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(140, 5, ': ' . $pkwtkontrak->employees->alamat . ', RT.' . $pkwtkontrak->employees->rt . ' / ' . $pkwtkontrak->employees->rw . ', Kelurahan ' . $pkwtkontrak->employees->kelurahan . '', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(50);
                    $this->fpdf->Cell(140, 5, '  Kecamatan ' . $pkwtkontrak->employees->kecamatan . ', Kota ' . $pkwtkontrak->employees->kota . ', Provinsi ' . $pkwtkontrak->employees->provinsi . ',' . $pkwtkontrak->employees->kode_pos . '', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Ln(7);
                    
                    $this->fpdf->SetX($indentNomor);
                    $text1 = "Dalam hal ini bertindak untuk dan atas nama dirinya sendiri dan selanjutnya disebut PIHAK KEDUA. Pada hari ".\Carbon\Carbon::parse($pkwtkontrak->tanggal_awal_kontrak)->isoformat('dddd, D MMMM Y')." bertempat di gedung PT Prima Komponen Indonesia, kedua belah pihak dengan ini sepakat untuk mengadakan perjanjian / ikatan kerja dalam jangka waktu tertentu, yaitu melalui kontrak kerja yang hubungan kerjanya berpegang pada syarat - syarat dan ketentuan sebagai berikut : ";
                    $this->fpdf->SetX($indentTeks); 
                    $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');

                    //Pasal 1
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 1', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'STATUS KARYAWAN DARI PIHAK KEDUA', 0, 0, 'C');
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetX($indentNomor);
                    $text1 = "PIHAK PERTAMA memberi tugas kepada PIHAK KEDUA, dan PIHAK KEDUA menyetujui dan menerima status sebagai karyawan kontrak berjangka di PT Prima Komponen Indonesia.";
                    $this->fpdf->SetX($indentTeks); 
                    $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');
                    //Pasal 1

                    //Pasal 2
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 2', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'JANGKA WAKTU KONTRAK KERJA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(150, 5, 'PIHAK KEDUA bersedia bekerja sebagai karyawan kontrak pada PIHAK PERTAMA untuk jangka waktu ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(20, 5,$pkwtkontrak->masa_kontrak, 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(85, 5,'terhitung sejak perjanjian kerja ini ditandatangani yaitu dari ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(28, 5, \Carbon\Carbon::parse($pkwtkontrak->tanggal_awal_kontrak)->isoformat('D MMMM Y'), 0, 0, 'C');
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(25, 5, ' sampai dengan ', 0, 0, 'C');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(28, 5,\Carbon\Carbon::parse($pkwtkontrak->tanggal_akhir_kontrak)->isoformat('D MMMM Y'), 0, 0, 'C');
                    //Pasal 2

                    //Pasal 3
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 3', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'TUGAS TUGAS POKOK PIHAK KEDUA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(170, 5, 'PIHAK KEDUA menerima tugas dari PIHAK PERTAMA sebagai berikut : ', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(50, 5, 'Nama Jabatan / Penempatan ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(80, 5, ': ' . $pkwtkontrak->employees->divisions->penempatan . ' / ' . $pkwtkontrak->employees->positions->jabatan . '', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(50, 5, 'Perusahaan ', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(80, 5, ': PT Prima Komponen Indonesia', 0, 0, 'L');
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetX($indentNomor);
                    $text1 = "PIHAK KEDUA menyatakan tidak keberatan melakukan tugas lain dari tugas pokoknya, apabila PIHAK PERTAMA memerlukannya.";
                    $this->fpdf->SetX($indentTeks); 
                    $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');
                    //Pasal 3

                    //Pasal 4
                    $indentNomorSub = 30; 
                    $indentTeksSub = 35;  
                    $lebarTeksSub = 157;

                    $this->fpdf->Ln(1);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 4', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'HARI KERJA DAN JAM KERJA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'a.', 0, 0, 'L');
                    $textSub = "Guna kelancaran penuaian tugas tersebut pada pasal 3 diatas, PIHAK KEDUA harus sudah berada di kantor atau ditempat lain yang ditentukan oleh PIHAK PERTAMA selama hari kerja an jam kerja yang berlaku di PT Prima Komponen Indonesia.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'b.', 0, 0, 'L');
                    $textSub = "PIHAK KEDUA menyetujui untuk bekerja menurut ketentuan hari kerja dan jam kerja pada PIHAK PERTAMA sesuai dengan ketentuan yang berlaku.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'c.', 0, 0, 'L');
                    $textSub = "PIHAK KEDUA juga menyatakan bersedia untuk bekerja diluar hari tau jam kerja tersebut bilamana PIHAK PERTAMA memerlukannya.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    //Pasal 4

                    // Pasal 5
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 5', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'PENDAPATAN YANG DITERIMA DARI PIHAK KEDUA DARI PIHAK PERTAMA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(170, 5, 'Sesuai dengan kesepakatan antara kedua belah pihak, dalam perjanjian kerja ini, PIHAK KEDUA menyetujui untuk', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(170, 5, 'menerima imbalan jasa pendapatan / upah dari PIHAK PERTAMA sebagai berikut :', 0, 0, 'L');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'a.', 0, 0, 'L');
                    $textSub = "Honorium / perhari sebesar sebagai berikut :";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(65, 5, 'Gaji Perbulan Yang Diterima', 0, 0, 'L');
                    $this->fpdf->Cell(65, 5, ': Rp.'.number_format($item->jumlah_upah), 0, 0, 'L');
                    $this->fpdf->Ln(10);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'b.', 0, 0, 'L');
                    $textSub = "Pihak KEDUA termasuk level karyawan non staff";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'c.', 0, 0, 'L');
                    $textSub = "Sistem pengupahan yang berlaku untuk PIHAK KEDUA adalah sistem No Work No Pay sesuai dengan ketentuan yang berlaku di PT Prima Komponen Indonesia.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    // Pasal 5

                    // Pasal 6
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 6', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'PAJAK PENDAPATAN', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(170, 5, 'PIHAK PERTAMA menanggung Pajak Pendapatan PIHAK KEDUA pada Pasal 5 Di atas.', 0, 0, 'L');
                    // Pasal 6

                    // Pasal 7
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 7', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'KEWAJIBAN PIHAK KEDUA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'a', 0, 0, 'L');
                    $textSub = "PIHAK KEDUA wajib melaksanakan tugas dengan sebaik-baiknya dan dengan penuh Tanggung Jawab PIHAK KEDUA bersedia dan wajib mentaati segala peraturan perusahaan PT Prima Komponen Indonesia dan menjaga semua rahasia perusahaan.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    // Pasal 7

                    // Pasal 8
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 8', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'PEMUTUSAN HUBUNGAN KERJA', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'a', 0, 0, 'L');
                    $textSub = "Hubungan kerja antar PIHAK PERTAMA dengan PIHAK KEDUA menjadi putus dengan sendirinya tanpa perlu pemberitahuan dari PIHAK PERTAMA pada PIHAK KEDUA. Apabila perjanjian kerja yang telah disepakati ini habis waktunya yaitu tanggal".\Carbon\Carbon::parse($item->tanggal_akhir_kontrak)->isoformat(' D MMMM Y').".";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'b', 0, 0, 'L');
                    $textSub = "Pemutusan hubungan kerja atas permintaan PIHAK KEDUA harus disampaikan paling sedikit satu bulan setengah sebelum tanggal pengun duran diri PIHAK KEDUA pada PIHAK PERTAMA.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'c', 0, 0, 'L');
                    $textSub = "Pemutusan hubungan kerja oleh PIHAK PERTAMA terhadap PIHAK KEDUA dapat segera dilakukan jika PIHAK KEDUA melakukan pelanggaran sesuai ketentua Tata Tertib yang diatur pada Peraturan Perusahaan.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    // Pasal 8

                    // Pasal 9
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'Pasal 9', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(170, 5, 'LAIN - LAIN', 0, 0, 'C');
                    $this->fpdf->Ln(5);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'a', 0, 0, 'L');
                    $textSub = "Perjanjian kerjasama ini dibuat dan ditandatangani oleh PIHAK PERTAMA dan PIHAK KEDUA dalam keadaan sadar,kontrak, sesuai dengan kesepakatan bersama PIHAK PERTAMA dan PIHAK KEDUA.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'b', 0, 0, 'L');
                    $textSub = "Perjanjian kerja ini dibuat dalam rangkap 2 ( Dua ) dan ditandatangani oleh PIHAK PERTAMA dan PIHAK KEDUA.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    $this->fpdf->SetX($indentNomorSub);
                    $this->fpdf->Cell(150, 5, 'c', 0, 0, 'L');
                    $textSub = "Jika terdapat perselisihan dalam perjanjian kerja ini. Maka kedua belah pihak sepakat untuk menyelesaikan secara musyawarah dan mufakat.";
                    $this->fpdf->SetX($indentTeksSub); 
                    $this->fpdf->MultiCell($lebarTeksSub, 5, $textSub, 0, 'J');
                    // Pasal 9

                    //End
                    $this->fpdf->Ln(2);
                    $this->fpdf->Cell(60);
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Cell(70, 5, 'Tangerang Selatan, ' . \Carbon\Carbon::parse($item->tanggal_awal_kontrak)->isoformat('D MMMM Y'), 0, 0, 'C');
                    $this->fpdf->Ln(10);
                    $this->fpdf->Cell(20);
                    $this->fpdf->SetFont('Arial', '', '9');
                    $this->fpdf->Cell(70, 5, 'Memahami dan menyetujui', 0, 0, 'C');
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(70, 5, 'Perjanjian Kerja ini', 0, 0, 'C');
                    $this->fpdf->Ln(4);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(70, 5, 'PIHAK KEDUA', 0, 0, 'C');
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(70, 5, 'PIHAK PERTAMA', 0, 0, 'C');
                    $this->fpdf->SetFont('Arial', 'B', '9');
                    $this->fpdf->Ln(40);
                    $this->fpdf->Cell(20);
                    $this->fpdf->Cell(70, 5, '( ' . $item->employees->nama_karyawan . ' )', 0, 0, 'C');
                    $this->fpdf->Cell(30);
                    $this->fpdf->Cell(70, 5, '( Achmad Firmansyah )', 0, 0, 'C');
                }
            }
        
        $this->fpdf->Output();
        exit;
    }
}

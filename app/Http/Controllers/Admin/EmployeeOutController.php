<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\EmployeeOutRequest;
use App\Models\Admin\EmployeesOuts;
use App\Models\Admin\Employees;
use App\Models\Admin\HistoryContracts;
use App\Models\Admin\HistoryPositions;
use App\Models\Admin\HistoryFamilies;
use App\Models\Admin\HistorySalaries;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\Overtimes;
use App\Models\Admin\InventoryCars;
use App\Models\Admin\InventoryMotorcyc;
use Alert;
use Codedge\Fpdf\Fpdf\Fpdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeOutController extends Controller
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

        $employees_outs = EmployeesOuts::with([
                            'employees',
                            'areas',
                            'golongans',
                            'divisions',
                            'positions'
                            ])->get();

        return view('admin.pages.employee_out.index',[
            'employees_outs' => $employees_outs
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

        $employees      = Employees::all();
        return view ('admin.pages.employee_out.create',[
            'employees'     => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeOutRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        DB::beginTransaction();
        try {
             $employee = Employees::findOrFail($request->input('employee_id'));

        $item_employee           = Employees::where('id', $request->input('employee_id'))->first();

        EmployeesOuts::create([
            'employees_id'                              => $request->input('employee_id'),
            'companies_id'                              => $item_employee->companies_id,
            'golongans_id'                              => $item_employee->golongans_id,
            'areas_id'                                  => $item_employee->areas_id,
            'divisions_id'                              => $item_employee->divisions_id,
            'positions_id'                              => $item_employee->positions_id,
            'nik_karyawan_keluar'                       => $item_employee->nik_karyawan,
            'nama_karyawan_keluar'                      => $item_employee->nama_karyawan,
            'nomor_npwp_karyawan_keluar'                => $item_employee->nomor_npwp,
            'email_karyawan_keluar'                     => $item_employee->email_karyawan,
            'nomor_handphone_karyawan_keluar'           => $item_employee->nomor_handphone,
            'tempat_lahir_karyawan_keluar'              => $item_employee->tempat_lahir,
            'tanggal_lahir_karyawan_keluar'             => $item_employee->tanggal_lahir,
            'nomor_bpjsketenagakerjaan_karyawan_keluar' => $item_employee->nomor_bpjsketenagakerjaan,
            'nomor_bpjskesehatan_karyawan_keluar'       => $item_employee->nomor_bpjskesehatan,
            'nomor_rekening_karyawan_keluar'            => $item_employee->nomor_rekening,
            'pendidikan_terakhir_karyawan_keluar'       => $item_employee->pendidikan_terakhir,
            'jenis_kelamin_karyawan_keluar'             => $item_employee->jenis_kelamin,
            'agama_karyawan_keluar'                     => $item_employee->agama,
            'alamat_karyawan_keluar'                    => $item_employee->alamat,
            'rt_karyawan_keluar'                        => $item_employee->rt,
            'rw_karyawan_keluar'                        => $item_employee->rw,
            'kelurahan_karyawan_keluar'                 => $item_employee->kelurahan,
            'kecamatan_karyawan_keluar'                 => $item_employee->kecamatan,
            'kota_karyawan_keluar'                      => $item_employee->kota,
            'provinsi_karyawan_keluar'                  => $item_employee->provinsi,
            'kode_pos_karyawan_keluar'                  => $item_employee->kode_pos,
            'nomor_absen_karyawan_keluar'               => $item_employee->nomor_absen,
            'golongan_darah_karyawan_keluar'            => $item_employee->golongan_darah,
            'nomor_kartu_keluarga_karyawan_keluar'      => $item_employee->nomor_kartu_keluarga,
            'status_nikah_karyawan_keluar'              => $item_employee->status_nikah,
            'nama_ayah_karyawan_keluar'                 => $item_employee->nama_ayah,
            'nama_ibu_karyawan_keluar'                  => $item_employee->nama_ibu,
            'tanggal_masuk_karyawan_keluar'             => $item_employee->tanggal_mulai_kerja,
            'tanggal_keluar_karyawan_keluar'            => $request->input('tanggal_keluar_karyawan_keluar'),
            'status_kerja_karyawan_keluar'              => $item_employee->status_kerja,
            'keterangan_keluar'                         => $request->input('keterangan_keluar'),
            'input_oleh'                                => auth()->user()->name
        ]);

        $id                     = $item_employee->id;
        $employee               = Employees::where('id', $id)->first();
        $contracts              = HistoryContracts::where('employees_id', $id)->get();
        $positions              = HistoryPositions::where('employees_id', $id)->get();
        $families               = HistoryFamilies::where('employees_id', $id)->get();
        $salaries               = HistorySalaries::where('employees_id', $id)->get();
        $rekap_salaries         = RekapSalaries::where('employees_id', $id)->get();
        $overtimes              = Overtimes::where('employees_id', $id)->get();

        $employee->delete();
        
        if ($contracts <> null) {
        foreach ($contracts as $contract ) {
            $contract->delete();
        }
        } else {}

        if ($positions <> null) {
        foreach ($positions as $position ) {
            $position->delete();
        }
        } else {}

        if ($families <> null) {
        foreach ($families as $family ) {
            $family->delete();
        }
        } else {}

        if ($salaries <> null) {
        foreach ($salaries as $salary ) {
            $salary->delete();
        }
        } else {}

        if ($rekap_salaries <> null) {
        foreach ($rekap_salaries as $rekap_salary ) {
            $rekap_salary->delete();
        }
        } else {}

        if ($overtimes <> null) {
        foreach ($overtimes as $overtime ) {
            $overtime->delete();
        }
        } else {}

        DB::commit();

        Alert::success(
            'Success Input Data Karyawan Keluar',
            'Oleh ' . auth()->user()->name
        );

        return redirect()->route('employee_out.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Salah','Oleh '.auth()->user()->name);
            return redirect()->route('employee_out.index');
        }

        // Alert::success('Success Input Data Karyawan Keluar','Oleh '.auth()->user()->name);
        // return redirect()->route('employee_out.index');
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

        $item_employee_out      = EmployeesOuts::findOrFail($id);
        $nikkaryawan            = $item_employee_out->nik_karyawan_keluar;

        $item_employee_outs = EmployeesOuts::with([
                            'employees',
                            'companies',
                            'golongans',
                            'areas',
                            'divisions',
                            'positions'
                            ])->where('employees_id', $item_employee_out->employees_id)->first();

        //Create Nomor DOkumen
        $nomor          = substr($item_employee_out->nik_karyawan_keluar, 6,6);
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
        //Create Nomor DOkumen

        $this->fpdf = new FPDF('P', 'mm', 'A4');
        $this->fpdf->AddPage();

        $indentNomor = 40; 
        $indentTeks = 20;  
        $lebarTeks = 175;

        $this->fpdf->Ln(15);
        $this->fpdf->SetFont('Arial', 'BU', '18');
        $this->fpdf->Cell(-5);
        $this->fpdf->Cell(200, 10, 'SURAT PENGALAMAN KERJA', 0, 0, 'C');

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', 'B', '14');
        $this->fpdf->Cell(-5);
        $this->fpdf->Cell(200, 10, 'No.' . $nomor . '/HRD/PK/' . $romawi . '/' . $tahun . '.', 0, 0, 'C');
        $this->fpdf->Ln(30);

        $this->fpdf->SetFont('Arial', '', '12');
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(100, 10, 'Kami Yang Bertanda Tangan Dibawah Ini :', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Achmad Firmansyah', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Jabatan', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Manager ( HRD - GA )', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Menerangkan Bahwa', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item_employee_out->nama_karyawan_keluar . '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Jabatan ', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item_employee_outs->positions->jabatan . ' / ' . $item_employee_outs->divisions->penempatan . '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(10);
        $this->fpdf->Cell(50, 10, 'Tanggal Mulai Kerja', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . \Carbon\Carbon::parse($item_employee_out->tanggal_masuk_karyawan_keluar)->isoformat('D MMMM Y') . ' s/d ' . \Carbon\Carbon::parse($item_employee_out->tanggal_keluar_karyawan_keluar)->isoformat('D MMMM Y') . '', 0, 0, 'L');
        $this->fpdf->Ln(15);



        $this->fpdf->SetX($indentNomor);
        $text1 = "Adalah benar pernah menjadi karyawan di PT Prima Komponen Indonesia dengan jabatan dan masa kerja di atas,sehubungan dengan ". $item_employee_out->keterangan_keluar ." dari yang bersangkutan,maka hubungan perusahaan dengan yang bersangkutan dinyatakan terputus.";
        $this->fpdf->SetX($indentTeks); 
        $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');

        $this->fpdf->Ln(2);

        $this->fpdf->SetX($indentNomor);
        $text1 = "Selama  bekerja  yang  bersangkutan  telah menunjukan loyalitas dan dedikasi yang tinggi untuk itu atas nama pimpinan perusahaan mengucapkan terima kasih.";
        $this->fpdf->SetX($indentTeks); 
        $this->fpdf->MultiCell($lebarTeks, 5, $text1, 0, 'J');

        $this->fpdf->Ln(2);
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(180, 10, 'Demikianlah surat keterangan ini kami buat untuk digunakan dengan seperlunya.', 0, 0, 'L');

        $this->fpdf->Ln(15);
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(180, 10, 'Tangerang Selatan, ' . \Carbon\Carbon::parse($item_employee_out->tanggal_keluar_karyawan_keluar)->isoformat('D MMMM Y') . ' ', 0, 0, 'L');

        $this->fpdf->Ln(5);
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(180, 10, 'Hormat kami,', 0, 0, 'L');

        $this->fpdf->Ln(35);

        $this->fpdf->SetFont('Arial', 'BU', '12');
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(180, 10, 'Achmad Firmansyah', 0, 0, 'L');

        $this->fpdf->Ln(5);

        $this->fpdf->SetFont('Arial', 'B', '12');
        $this->fpdf->Cell(10);
        $this->fpdf->Cell(180, 10, 'Manager ( HRD - GA )', 0, 0, 'L');

        $this->fpdf->Output();

        exit;
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

        $item_employee_out  = EmployeesOuts::findOrFail($id);

        return view ('admin.pages.employee_out.edit',[
                    'item_employee_out'  => $item_employee_out
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeOutRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data_employee_out   = $request->all();
        $item_employee_out   = EmployeesOuts::findOrFail($id);
        $item_employee_out->update($data_employee_out);
        Alert::info('Success Edit Data Karyawan Keluar','Oleh '.auth()->user()->name);
        return redirect()->route('employee_out.index');
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

        DB::transaction(function () use ($id) {
            $employee_out = EmployeesOuts::findOrFail($id);
            $employee_out->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $employee_out->delete();
        });
        Alert::error('Menghapus Data Karyawan Keluar','Oleh '.auth()->user()->name);
        return redirect()->route('employee_out.index');
    }
}

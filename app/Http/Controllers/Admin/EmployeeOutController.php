<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\EmployeeOutRequest;
use App\Http\Requests\Admin\EmployeeOutUpdateRequest;
use App\Models\Admin\EmployeesOuts;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use App\Models\Admin\HistoryContracts;
use App\Models\Admin\HistoryPositions;
use App\Models\Admin\HistoryFamilies;
use App\Models\Admin\HistorySalaries;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\Overtimes;
use App\Models\Admin\CertificationBnsps;
use App\Models\Admin\CertificationMinistries;
use App\Models\Admin\CertificationOthers;
use App\Models\Admin\TrainingInternals;
use App\Models\Admin\TrainingEksternals;
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

class EmployeeOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'leader'&& auth()->user()->roles != 'accounting') {
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

        $data   = $request->except('_token');
        DB::beginTransaction();
        try {
            $employee      = Employees::findOrFail($request->input('employee_id'));
            $item_employee  = Employees::where('id', $request->input('employee_id'))->first();
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

        $id                         = $item_employee->id;
        $employee                   = Employees::where('id', $id)->first();
        $contracts                  = HistoryContracts::where('employees_id', $id)->get();
        $positions                  = HistoryPositions::where('employees_id', $id)->get();
        $families                   = HistoryFamilies::where('employees_id', $id)->get();
        $salaries                   = HistorySalaries::where('employees_id', $id)->get();
        $rekap_salaries             = RekapSalaries::where('employees_id', $id)->get();
        $overtimes                  = Overtimes::where('employees_id', $id)->get();
        $certification_bnsps        = CertificationBnsps::where('employees_id', $id)->get();
        $certification_ministries   = CertificationMinistries::where('employees_id', $id)->get();
        $certification_others       = CertificationOthers::where('employees_id', $id)->get();
        $trainining_internals       = TrainingInternals::where('employees_id', $id)->get();
        $trainining_eksternals      = TrainingEksternals::where('employees_id', $id)->get();
        $inventory_cars             = InventoryCars::where('employees_id', $id)->get();
        $inventory_motorcycles      = InventoryMotorcycles::where('employees_id', $id)->get();

        $employee->delete();
        
        HistoryContracts::where('employees_id', $employee->id)->delete();
        HistoryPositions::where('employees_id', $employee->id)->delete();
        HistoryFamilies::where('employees_id', $employee->id)->delete();
        HistorySalaries::where('employees_id', $employee->id)->delete();
        RekapSalaries::where('employees_id', $employee->id)->delete();
        Overtimes::where('employees_id', $employee->id)->delete();
        

        if ($certification_bnsps <> null) {
        foreach ($certification_bnsps as $certification_bnsp ) {
            $certification_bnsp->delete();
        }
        } else {}

        if ($certification_ministries <> null) {
        foreach ($certification_ministries as $certification_ministry ) {
            $certification_ministry->delete();
        }
        } else {}

        if ($certification_others <> null) {
        foreach ($certification_others as $certification_other ) {
            $certification_other->delete();
        }
        } else {}

        if ($trainining_internals <> null) {
        foreach ($trainining_internals as $trainining_internal ) {
            $trainining_internal->delete();
        }
        } else {}

        if ($trainining_eksternals <> null) {
        foreach ($trainining_eksternals as $trainining_eksternal ) {
            $trainining_eksternal->delete();
        }
        } else {}

        if ($inventory_cars <> null) {
        foreach ($inventory_cars as $inventory_car ) {
            $inventory_car->delete();
        }
        } else {}

        if ($inventory_motorcycles <> null) {
        foreach ($inventory_motorcycles as $inventory_motorcycle ) {
            $inventory_motorcycle->delete();
        }
        } else {}

        //Menghapus Foto
        $fileFields = [
                'foto_karyawan' => 'assets/foto/karyawan'
                ];

                // Proses penghapusan file fisik dari storage
                foreach ($fileFields as $field => $path) {
                    if ($employee->$field) {
                        Storage::disk('public')->delete($path . '/' . $employee->$field);
                    }
                }
        //Menghapus Foto

        DB::commit();

        Alert::success('Success Input Data Karyawan Keluar','Oleh ' . auth()->user()->name);

        return redirect()->route('employee_out.index');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            Alert::error('Salah','Oleh '.auth()->user()->name);
            return redirect()->route('employee_out.index');
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
    public function update(EmployeeOutUpdateRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data_employee_out   = $request->except('_token');
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

    public function EmployeeOutExportExcel()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'leader'&& auth()->user()->roles != 'accounting') {
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
        $sheet->setCellValue('X1', 'Nomor Rekening');
        $sheet->setCellValue('Y1', 'Status Kerja');
        $sheet->setCellValue('Z1', 'Tanggal Mulai Kerja');
        $sheet->setCellValue('AA1', 'Tanggal Akhir Kerja');
        $sheet->setCellValue('AB1', 'Alamat');
        $sheet->setCellValue('AC1', 'RT');
        $sheet->setCellValue('AD1', 'RW');
        $sheet->setCellValue('AE1', 'Kelurahan');
        $sheet->setCellValue('AF1', 'Kecamatan');
        $sheet->setCellValue('AG1', 'Kabupaten/Kota');
        $sheet->setCellValue('AH1', 'Provinsi');
        $sheet->setCellValue('AI1', 'Kode POS');
        // Header

        //Style
        $sheet->getStyle('A1:AI1')->applyFromArray([
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

        $employees_outs  = EmployeesOuts::with(['golongans','areas','divisions','positions','companies'])->get();

        $row = 2;
        $no = 1;
        foreach ($employees_outs as $employees_out) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $employees_out->companies->nama_perusahaan);
                $sheet->setCellValue('C'.$row, $employees_out->areas->area);
                $sheet->setCellValue('D'.$row, $employees_out->golongans->golongan);
                $sheet->setCellValue('E'.$row, $employees_out->positions->jabatan);
                $sheet->setCellValue('F'.$row, $employees_out->divisions->penempatan);
                $sheet->setCellValue('G'.$row, $employees_out->nik_karyawan_keluar);
                $sheet->setCellValue('H'.$row, $employees_out->nama_karyawan_keluar);
                $sheet->setCellValue('I'.$row, $employees_out->email_karyawan_keluar);
                $sheet->setCellValue('J'.$row, "'".$employees_out->nomor_npwp_karyawan_keluar);
                $sheet->setCellValue('K'.$row, $employees_out->tempat_lahir_karyawan_keluar);
                $sheet->setCellValue('L'.$row, $employees_out->tanggal_lahir_karyawan_keluar);
                $sheet->setCellValue('M'.$row, $employees_out->agama_karyawan_keluar);
                $sheet->setCellValue('N'.$row, $employees_out->jenis_kelamin_karyawan_keluar);
                $sheet->setCellValue('O'.$row, "'".$employees_out->nomor_handphone_karyawan_keluar);
                $sheet->setCellValue('P'.$row, $employees_out->pendidikan_terakhir_karyawan_keluar);
                $sheet->setCellValue('Q'.$row, $employees_out->golongan_darah_karyawan_keluar);
                $sheet->setCellValue('R'.$row, "'".$employees_out->nomor_bpjskesehatan_karyawan_keluar);
                $sheet->setCellValue('S'.$row, "'".$employees_out->nomor_bpjsketenagakerjaan_karyawan_keluar);
                $sheet->setCellValue('T'.$row, "'".$employees_out->nomor_kartu_keluarga_karyawan_keluar);
                $sheet->setCellValue('U'.$row, $employees_out->status_nikah_karyawan_keluar);
                $sheet->setCellValue('V'.$row, $employees_out->nama_ayah_karyawan_keluar);
                $sheet->setCellValue('W'.$row, $employees_out->nama_ibu_karyawan_keluar);
                $sheet->setCellValue('X'.$row, "'".$employees_out->nomor_rekening_karyawan_keluar);
                $sheet->setCellValue('Y'.$row, $employees_out->status_kerja_karyawan_keluar);
                $sheet->setCellValue('Z'.$row, $employees_out->tanggal_masuk_karyawan_keluar);
                $sheet->setCellValue('AA'.$row, $employees_out->tanggal_keluar_karyawan_keluar);
                $sheet->setCellValue('AB'.$row, $employees_out->alamat_karyawan_keluar);
                $sheet->setCellValue('AC'.$row, "'".$employees_out->rt_karyawan_keluar);
                $sheet->setCellValue('AD'.$row, "'".$employees_out->rw_karyawan_keluar);
                $sheet->setCellValue('AE'.$row, $employees_out->kelurahan_karyawan_keluar);
                $sheet->setCellValue('AF'.$row, $employees_out->kecamatan_karyawan_keluar);
                $sheet->setCellValue('AG'.$row, $employees_out->kota_karyawan_keluar);
                $sheet->setCellValue('AH'.$row, $employees_out->provinsi_karyawan_keluar);
                $sheet->setCellValue('AI'.$row, "'".$employees_out->kode_pos_karyawan_keluar);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:AI{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:AI{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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
                $sheet->getStyle("AC2:AC{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AD2:AD{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AE2:AE{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("AI2:AI{$lastRow}")
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

        $filename = 'DatabaseKaryawanKeluar.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    
}

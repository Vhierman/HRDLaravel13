<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CertificationMinistryRequest;
use App\Models\Admin\CertificationMinistries;
use App\Models\Admin\Employees;
use App\Models\Admin\Golongans;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use Alert;
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

class CertificationMinistryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $certification_ministries = CertificationMinistries::with(['employees'])->get();
        return view('admin.pages.certification_ministry.index',[
            'certification_ministries' => $certification_ministries
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

        $employees      = Employees::with(['divisions'])->get();
        return view('admin.pages.certification_ministry.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificationMinistryRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();
        CertificationMinistries::create([
            'employees_id'                          => $request->input('employees_id'),
            'nik_karyawan'                          => $employee->nik_karyawan,
            'jumlah_sertifikat_kementrian'          => 1,
            'nomor_sertifikat_kementrian'           => $request->input('nomor_sertifikat_kementrian'),
            'jenis_sertifikat_kementrian'           => $request->input('jenis_sertifikat_kementrian'),
            'masa_berlaku_sertifikat_kementrian'    => $request->input('masa_berlaku_sertifikat_kementrian'),
            'tanggal_terbit_kementrian'             => $request->input('tanggal_terbit_kementrian'),
            'sampai_tanggal_kementrian'             => $request->input('sampai_tanggal_kementrian'),
            'lsp_kementrian'                        => $request->input('lsp_kementrian'),
            'input_oleh'                            => Auth::user()->name
            ]);
        Alert::success('Success Input Data Sertifikasi Kementrian','Oleh '.auth()->user()->name);
        return redirect()->route('certification_ministry.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
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

        $employees                  = Employees::with(['divisions'])->get();
        $certification_ministry     = CertificationMinistries::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.certification_ministry.edit',[
        'certification_ministry'         => $certification_ministry,
        'employees'                     => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificationMinistryRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                       = $request->except('_token');
        $certification_ministry     = CertificationMinistries::findOrFail($id);
        $employee                   = Employees::where('id',$request->input('employees_id'))->first();
        $certification_ministry->update([
            'employees_id'                          => $request->input('employees_id'),
            'nik_karyawan'                          => $employee->nik_karyawan,
            'jumlah_sertifikat_kementrian'          => 1,
            'nomor_sertifikat_kementrian'           => $request->input('nomor_sertifikat_kementrian'),
            'jenis_sertifikat_kementrian'           => $request->input('jenis_sertifikat_kementrian'),
            'masa_berlaku_sertifikat_kementrian'    => $request->input('masa_berlaku_sertifikat_kementrian'),
            'tanggal_terbit_kementrian'             => $request->input('tanggal_terbit_kementrian'),
            'sampai_tanggal_kementrian'             => $request->input('sampai_tanggal_kementrian'),
            'lsp_kementrian'                        => $request->input('lsp_kementrian'),
            'edit_oleh'                             => Auth::user()->name
            ]);
        Alert::success('Success Update Data Sertifikasi Kementrian','Oleh '.auth()->user()->name);
        return redirect()->route('certification_ministry.index');
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

        DB::transaction(function () use ($id) {
            $certification_ministry = CertificationMinistries::findOrFail($id);
            $certification_ministry->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $certification_ministry->delete();
        });
        Alert::error('Menghapus Data Sertifikasi Kementrian','Oleh '.auth()->user()->name);
        return redirect()->route('certification_ministry.index');
    }

    public function exportExcel()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Penempatan');
        $sheet->setCellValue('F1', 'Jenis Sertifikasi');
        $sheet->setCellValue('G1', 'Kementrian');
        $sheet->setCellValue('H1', 'Nomor Sertifikat');
        $sheet->setCellValue('I1', 'Masa Berlaku Sertifikat');
        $sheet->setCellValue('J1', 'Tanggal Terbit Sertifikat');
        $sheet->setCellValue('K1', 'Tanggal Berakhir Sertifikat');
        // Header

        //Style
        $sheet->getStyle('A1:K1')->applyFromArray([
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

        $certification_ministries = CertificationMinistries::with([
                                'employees',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($certification_ministries as $certification_ministry) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$certification_ministry->nik_karyawan);
                $sheet->setCellValue('C'.$row, $certification_ministry->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $certification_ministry->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $certification_ministry->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $certification_ministry->jenis_sertifikat_kementrian);
                $sheet->setCellValue('G'.$row, $certification_ministry->lsp_kementrian);
                $sheet->setCellValue('H'.$row, $certification_ministry->nomor_sertifikat_kementrian);
                $sheet->setCellValue('I'.$row, $certification_ministry->masa_berlaku_sertifikat_kementrian);
                $sheet->setCellValue('J'.$row, "'".$certification_ministry->tanggal_terbit_kementrian);
                $sheet->setCellValue('K'.$row, "'".$certification_ministry->sampai_tanggal_kementrian);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:K{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:K{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F2:F{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G2:G{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:H{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I2:I{$lastRow}")
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

        $filename = 'DataSertifikasiKementrian.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function notif_ministry_habis()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $today = Carbon::today();

        $certification_ministries = CertificationMinistries::with(['employees'])->whereDate('sampai_tanggal_kementrian', '<', $today)
                        ->get();
        
        return view('admin.pages.certification_ministry.index',[
            'certification_ministries' => $certification_ministries
        ]);
    }

    public function notif_ministry_akan_habis()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $today = Carbon::today();

        $certification_ministries = CertificationMinistries::with(['employees'])->whereBetween('sampai_tanggal_kementrian', [
                            $today,$today->copy()->addDays(30)
                        ])->get();
        
        return view('admin.pages.certification_ministry.index',[
            'certification_ministries' => $certification_ministries
        ]);
    }
}

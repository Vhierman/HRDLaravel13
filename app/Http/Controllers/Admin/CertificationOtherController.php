<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CertificationOtherRequest;
use App\Models\Admin\CertificationOthers;
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

class CertificationOtherController extends Controller
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

        $certification_others = CertificationOthers::with(['employees'])->get();
        return view('admin.pages.certification_other.index',[
            'certification_others' => $certification_others
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

        $employees      = Employees::with(['divisions'])->get();
        return view('admin.pages.certification_other.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificationOtherRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();
        CertificationOthers::create([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'jumlah_sertifikat_lain'    => 1,
            'nomor_sertifikat_lain'     => $request->input('nomor_sertifikat_lain'),
            'jenis_sertifikat_lain'     => $request->input('jenis_sertifikat_lain'),
            'tanggal_terbit_lain'       => $request->input('tanggal_terbit_lain'),
            'input_oleh'                => Auth::user()->name
            ]);
        Alert::success('Success Input Data Sertifikat','Oleh '.auth()->user()->name);
        return redirect()->route('certification_other.index');
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

        $employees              = Employees::with(['divisions'])->get();
        $certification_other     = CertificationOthers::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.certification_other.edit',[
        'certification_other'   => $certification_other,
        'employees'             => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificationOtherRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data                   = $request->except('_token');
        $certification_other    = CertificationOthers::findOrFail($id);
        $employee               = Employees::where('id',$request->input('employees_id'))->first();
        $certification_other->update([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'jumlah_sertifikat_lain'    => 1,
            'nomor_sertifikat_lain'     => $request->input('nomor_sertifikat_lain'),
            'jenis_sertifikat_lain'     => $request->input('jenis_sertifikat_lain'),
            'tanggal_terbit_lain'       => $request->input('tanggal_terbit_lain'),
            'edit_oleh'                 => Auth::user()->name
            ]);
        Alert::success('Success Update Data Sertifikat','Oleh '.auth()->user()->name);
        return redirect()->route('certification_other.index');
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
            $certification_other = CertificationOthers::findOrFail($id);
            $certification_other->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $certification_other->delete();
        });
        Alert::error('Menghapus Data Sertifikat','Oleh '.auth()->user()->name);
        return redirect()->route('certification_other.index');
    }

    public function exportExcel()
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
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Penempatan');
        $sheet->setCellValue('F1', 'Jenis Sertifikasi');
        $sheet->setCellValue('G1', 'Nomor Sertifikat');
        $sheet->setCellValue('H1', 'Tanggal Terbit Sertifikat');
        // Header

        //Style
        $sheet->getStyle('A1:H1')->applyFromArray([
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

        $certification_others = CertificationOthers::with([
                                'employees',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($certification_others as $certification_other) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$certification_other->nik_karyawan);
                $sheet->setCellValue('C'.$row, $certification_other->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $certification_other->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $certification_other->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $certification_other->jenis_sertifikat_lain);
                $sheet->setCellValue('G'.$row, $certification_other->nomor_sertifikat_lain);
                $sheet->setCellValue('H'.$row, "'".$certification_other->tanggal_terbit_lain);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:H{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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
                
        // Auto width
        $highestColumn = $sheet->getHighestColumn(); 
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);

        $filename = 'DataSertifikasiLain.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}

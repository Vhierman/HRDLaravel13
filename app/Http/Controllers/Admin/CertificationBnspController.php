<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CertificationBnspRequest;
use App\Models\Admin\CertificationBnsps;
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

class CertificationBnspController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' &&  auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $certification_bnsps = CertificationBnsps::with(['employees'])->get();
        return view('admin.pages.certification_bnsp.index',[
            'certification_bnsps' => $certification_bnsps
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' ) {
            abort(403);
        }

        $employees      = Employees::with(['divisions'])->get();
        return view('admin.pages.certification_bnsp.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificationBnspRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();
        CertificationBnsps::create([
            'employees_id'                  => $request->input('employees_id'),
            'nik_karyawan'                  => $employee->nik_karyawan,
            'jumlah_sertifikat_bnsp'        => 1,
            'nomor_sertifikat_bnsp'         => $request->input('nomor_sertifikat_bnsp'),
            'jenis_sertifikat_bnsp'         => $request->input('jenis_sertifikat_bnsp'),
            'masa_berlaku_sertifikat_bnsp'  => $request->input('masa_berlaku_sertifikat_bnsp'),
            'tanggal_terbit_bnsp'           => $request->input('tanggal_terbit_bnsp'),
            'sampai_tanggal_bnsp'           => $request->input('sampai_tanggal_bnsp'),
            'lsp_bnsp'                      => $request->input('lsp_bnsp'),
            'input_oleh'                    => Auth::user()->name
            ]);
        Alert::success('Success Input Data Sertifikasi BNSP','Oleh '.auth()->user()->name);
        return redirect()->route('certification_bnsp.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' &&  auth()->user()->roles != 'accounting') {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' ) {
            abort(403);
        }

        $employees              = Employees::with(['divisions'])->get();
        $certification_bnsp     = CertificationBnsps::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.certification_bnsp.edit',[
        'certification_bnsp'         => $certification_bnsp,
        'employees'             => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificationBnspRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' ) {
            abort(403);
        }

        $data                   = $request->except('_token');
        $certification_bnsp     = CertificationBnsps::findOrFail($id);
        $employee               = Employees::where('id',$request->input('employees_id'))->first();
        $certification_bnsp->update([
            'employees_id'                  => $request->input('employees_id'),
            'nik_karyawan'                  => $employee->nik_karyawan,
            'jumlah_sertifikat_bnsp'        => 1,
            'nomor_sertifikat_bnsp'         => $request->input('nomor_sertifikat_bnsp'),
            'jenis_sertifikat_bnsp'         => $request->input('jenis_sertifikat_bnsp'),
            'masa_berlaku_sertifikat_bnsp'  => $request->input('masa_berlaku_sertifikat_bnsp'),
            'tanggal_terbit_bnsp'           => $request->input('tanggal_terbit_bnsp'),
            'sampai_tanggal_bnsp'           => $request->input('sampai_tanggal_bnsp'),
            'lsp_bnsp'                      => $request->input('lsp_bnsp'),
            'edit_oleh'                     => Auth::user()->name
            ]);
        Alert::success('Success Update Data Sertifikasi BNSP','Oleh '.auth()->user()->name);
        return redirect()->route('certification_bnsp.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' ) {
            abort(403);
        }

        DB::transaction(function () use ($id) {
            $certification_bnsp = CertificationBnsps::findOrFail($id);
            $certification_bnsp->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $certification_bnsp->delete();
        });
        Alert::error('Menghapus Data Sertifikasi BNSP','Oleh '.auth()->user()->name);
        return redirect()->route('certification_bnsp.index');
    }

    public function exportExcel()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' ) {
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
        $sheet->setCellValue('G1', 'LSP BNSP');
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

        $certification_bnsps = CertificationBnsps::with([
                                'employees',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($certification_bnsps as $certification_bnsp) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$certification_bnsp->nik_karyawan);
                $sheet->setCellValue('C'.$row, $certification_bnsp->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $certification_bnsp->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $certification_bnsp->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $certification_bnsp->jenis_sertifikat_bnsp);
                $sheet->setCellValue('G'.$row, $certification_bnsp->lsp_bnsp);
                $sheet->setCellValue('H'.$row, $certification_bnsp->nomor_sertifikat_bnsp);
                $sheet->setCellValue('I'.$row, $certification_bnsp->masa_berlaku_sertifikat_bnsp);
                $sheet->setCellValue('J'.$row, "'".$certification_bnsp->tanggal_terbit_bnsp);
                $sheet->setCellValue('K'.$row, "'".$certification_bnsp->sampai_tanggal_bnsp);
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

        $filename = 'DataSertifikasiBNSP.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}

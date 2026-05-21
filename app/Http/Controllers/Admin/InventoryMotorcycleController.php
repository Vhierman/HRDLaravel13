<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\InventoryMotorcycleRequest;
use App\Models\Admin\InventoryMotorcycles;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
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

class InventoryMotorcycleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['admin', 'hrd',  'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $inventory_motorcycles = InventoryMotorcycles::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->get();

        return view('admin.pages.inventory_motorcycle.index',[
            'inventory_motorcycles' => $inventory_motorcycles
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
        return view('admin.pages.inventory_motorcycle.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryMotorcycleRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();
        InventoryMotorcycles::create([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'merk_motor'                => $request->input('merk_motor'),
            'type_motor'                => $request->input('type_motor'),
            'nomor_polisi'              => $request->input('nomor_polisi'),
            'warna_motor'               => $request->input('warna_motor'),
            'nomor_rangka_motor'        => $request->input('nomor_rangka_motor'),
            'nomor_mesin_motor'         => $request->input('nomor_mesin_motor'),
            'tanggal_akhir_pajak_motor' => $request->input('tanggal_akhir_pajak_motor'),
            'tanggal_akhir_plat_motor'  => $request->input('tanggal_akhir_plat_motor'),
            'input_oleh'                => Auth::user()->name
            ]);
        Alert::success('Success Input Data Inventaris Motor','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_motorcycle.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd',  'accounting'];
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

        $employees              = Employees::with(['divisions'])->get();
        $inventory_motorcycle   = InventoryMotorcycles::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.inventory_motorcycle.edit',[
        'inventory_motorcycle'  => $inventory_motorcycle,
        'employees'             => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryMotorcycleRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                   = $request->except('_token');
        $inventory_motorcycle   = InventoryMotorcycles::findOrFail($id);
        $employee               = Employees::where('id',$request->input('employees_id'))->first();
        $inventory_motorcycle->update([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'merk_motor'                => $request->input('merk_motor'),
            'type_motor'                => $request->input('type_motor'),
            'nomor_polisi'              => $request->input('nomor_polisi'),
            'warna_motor'               => $request->input('warna_motor'),
            'nomor_rangka_motor'        => $request->input('nomor_rangka_motor'),
            'nomor_mesin_motor'         => $request->input('nomor_mesin_motor'),
            'tanggal_akhir_pajak_motor' => $request->input('tanggal_akhir_pajak_motor'),
            'tanggal_akhir_plat_motor'  => $request->input('tanggal_akhir_plat_motor'),
            'edit_oleh'                 => Auth::user()->name
            ]);
        Alert::success('Success Update Data Inventaris Motor','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_motorcycle.index');
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
            $inventory_motorcycle = InventoryMotorcycles::findOrFail($id);
            $inventory_motorcycle->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $inventory_motorcycle->delete();
        });
        Alert::error('Menghapus Data Inventaris Motor','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_motorcycle.index');
    }

    public function exportExcel()
    {
        $allowedRoles = ['admin', 'hrd',  'accounting'];
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
        $sheet->setCellValue('F1', 'Nomor Polisi');
        $sheet->setCellValue('G1', 'Warna Motor');
        $sheet->setCellValue('H1', 'Merk Motor');
        $sheet->setCellValue('I1', 'Type Motor');
        $sheet->setCellValue('J1', 'Nomor Rangka');
        $sheet->setCellValue('K1', 'Nomor Mesin');
        $sheet->setCellValue('L1', 'Tanggal Akhir Pajak');
        $sheet->setCellValue('M1', 'Tanggal Akhir Plat');
        // Header

        //Style
        $sheet->getStyle('A1:M1')->applyFromArray([
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

        $inventory_motorcycles = InventoryMotorcycles::with([
                                'employees',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($inventory_motorcycles as $inventory_motorcycle) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$inventory_motorcycle->nik_karyawan);
                $sheet->setCellValue('C'.$row, $inventory_motorcycle->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $inventory_motorcycle->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $inventory_motorcycle->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $inventory_motorcycle->nomor_polisi);
                $sheet->setCellValue('G'.$row, $inventory_motorcycle->warna_motor);
                $sheet->setCellValue('H'.$row, $inventory_motorcycle->merk_motor);
                $sheet->setCellValue('I'.$row, $inventory_motorcycle->type_motor);
                $sheet->setCellValue('J'.$row, "'".$inventory_motorcycle->nomor_rangka_motor);
                $sheet->setCellValue('K'.$row, "'".$inventory_motorcycle->nomor_mesin_motor);
                $sheet->setCellValue('L'.$row, "'".$inventory_motorcycle->tanggal_akhir_pajak_motor);
                $sheet->setCellValue('M'.$row, "'".$inventory_motorcycle->tanggal_akhir_plat_motor);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:M{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:M{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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

        $filename = 'DataInventarisMotor.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function notif_motor_habis()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $today = Carbon::today();
        $inventory_motorcycles = InventoryMotorcycles::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereDate('tanggal_akhir_pajak_motor', '<', $today)
                            ->get();

        return view('admin.pages.inventory_motorcycle.index',[
            'inventory_motorcycles' => $inventory_motorcycles
        ]);
    }

    public function notif_motor_akan_habis()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $today = Carbon::today();
        $inventory_motorcycles = InventoryMotorcycles::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereBetween('tanggal_akhir_pajak_motor', [
                            $today,$today->copy()->addDays(30)
                            ])->get();

        return view('admin.pages.inventory_motorcycle.index',[
            'inventory_motorcycles' => $inventory_motorcycles
        ]);
    }
}

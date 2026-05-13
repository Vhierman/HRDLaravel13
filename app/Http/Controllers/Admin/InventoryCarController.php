<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\InventoryCarRequest;
use App\Models\Admin\InventoryCars;
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

class InventoryCarController extends Controller
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

        $inventory_cars = InventoryCars::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->get();

        return view('admin.pages.inventory_car.index',[
            'inventory_cars' => $inventory_cars
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
        return view('admin.pages.inventory_car.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryCarRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();
        InventoryCars::create([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'merk_mobil'                    => $request->input('merk_mobil'),
            'type_mobil'                => $request->input('type_mobil'),
            'nomor_polisi'              => $request->input('nomor_polisi'),
            'warna_mobil'               => $request->input('warna_mobil'),
            'nomor_rangka_mobil'        => $request->input('nomor_rangka_mobil'),
            'nomor_mesin_mobil'         => $request->input('nomor_mesin_mobil'),
            'tanggal_akhir_pajak_mobil' => $request->input('tanggal_akhir_pajak_mobil'),
            'tanggal_akhir_plat_mobil'  => $request->input('tanggal_akhir_plat_mobil'),
            'input_oleh'                => Auth::user()->name
            ]);

        Alert::success('Success Input Data Inventaris Mobil','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_car.index');
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
        $inventory_car          = InventoryCars::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.inventory_car.edit',[
        'inventory_car'         => $inventory_car,
        'employees'             => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryCarRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data                   = $request->except('_token');
        $inventory_car          = InventoryCars::findOrFail($id);
        $employee               = Employees::where('id',$request->input('employees_id'))->first();
        $inventory_car->update([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'merk_mobil'                => $request->input('merk_mobil'),
            'type_mobil'                => $request->input('type_mobil'),
            'nomor_polisi'              => $request->input('nomor_polisi'),
            'warna_mobil'               => $request->input('warna_mobil'),
            'nomor_rangka_mobil'        => $request->input('nomor_rangka_mobil'),
            'nomor_mesin_mobil'         => $request->input('nomor_mesin_mobil'),
            'tanggal_akhir_pajak_mobil' => $request->input('tanggal_akhir_pajak_mobil'),
            'tanggal_akhir_plat_mobil'  => $request->input('tanggal_akhir_plat_mobil'),
            'edit_oleh'                 => Auth::user()->name
            ]);
        Alert::success('Success Update Data Inventaris Mobil','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_car.index');
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
            $inventory_car = InventoryCars::findOrFail($id);
            $inventory_car->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $inventory_car->delete();
        });
        Alert::error('Menghapus Data Inventaris Mobil','Oleh '.auth()->user()->name);
        return redirect()->route('inventory_car.index');
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
        $sheet->setCellValue('F1', 'Nomor Polisi');
        $sheet->setCellValue('G1', 'Warna Mobil');
        $sheet->setCellValue('H1', 'Merk Mobil');
        $sheet->setCellValue('I1', 'Type Mobil');
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

        $inventory_cars = InventoryCars::with([
                                'employees',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($inventory_cars as $inventory_car) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$inventory_car->nik_karyawan);
                $sheet->setCellValue('C'.$row, $inventory_car->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $inventory_car->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $inventory_car->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $inventory_car->nomor_polisi);
                $sheet->setCellValue('G'.$row, $inventory_car->warna_mobil);
                $sheet->setCellValue('H'.$row, $inventory_car->merk_mobil);
                $sheet->setCellValue('I'.$row, $inventory_car->type_mobil);
                $sheet->setCellValue('J'.$row, "'".$inventory_car->nomor_rangka_mobil);
                $sheet->setCellValue('K'.$row, "'".$inventory_car->nomor_mesin_mobil);
                $sheet->setCellValue('L'.$row, "'".$inventory_car->tanggal_akhir_pajak_mobil);
                $sheet->setCellValue('M'.$row, "'".$inventory_car->tanggal_akhir_plat_mobil);
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

        $filename = 'DataInventarisMobil.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}

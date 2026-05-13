<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TrainingEksternalRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\NamaKaryawanRequest;
use App\Http\Requests\Admin\PenempatanRequest;
use App\Http\Requests\Admin\MateriTrainingEksternalRequest;
use App\Http\Requests\Admin\TanggalTrainingEksternalRequest;
use App\Models\Admin\TrainingEksternals;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TrainingEksternalController extends Controller
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

        return view('admin.pages.training_eksternal.index');
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
        return view('admin.pages.training_eksternal.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingEksternalRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $tanggal_awal   = $request->input('tanggal_awal_training_eksternal');
        $tanggal_akhir  = $request->input('tanggal_akhir_training_eksternal');
        $hari_awal      = \Carbon\Carbon::parse($tanggal_awal)->isoformat('dddd');
        $hari_akhir     = \Carbon\Carbon::parse($tanggal_akhir)->isoformat('dddd');
        foreach ($request->employees_id as $employeeId) {
            $employee = Employees::find($employeeId);
            $insert = [
                'employees_id'                                  => $employee->id,
                'nik_karyawan'                                  => $employee->nik_karyawan,
                'hari_awal_training_eksternal'                  => $hari_awal,
                'hari_akhir_training_eksternal'                 => $hari_akhir,
                'tanggal_awal_training_eksternal'               => $request->tanggal_awal_training_eksternal,
                'tanggal_akhir_training_eksternal'              => $request->tanggal_akhir_training_eksternal,
                'institusi_penyelenggara_training_eksternal'    => $request->institusi_penyelenggara_training_eksternal,
                'perihal_training_eksternal'                    => $request->perihal_training_eksternal,
                'jam_training_eksternal'                        => $request->jam_training_eksternal,
                'lokasi_training_eksternal'                     => $request->lokasi_training_eksternal,
                'alamat_training_eksternal'                     => $request->alamat_training_eksternal,
                'input_oleh'                                    => auth()->user()->name
            ];
            TrainingEksternals::create($insert);
        }
        Alert::success('Success Input Data Training Eksternal','Oleh '.auth()->user()->name);
        return redirect()->route('training_eksternal.index');
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

    public function view_tanggal()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_eksternal.form_view_tanggal');
    }

    public function tampil_view_tanggal(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.areas',
                            'employees.golongans',
                            'employees.positions',
                            'employees.divisions',
                            ])->whereBetween('tanggal_awal_training_eksternal', [$tanggal_awal, $tanggal_akhir])->get();

        // dd($item_training_eksternals);

        if (!$item_training_eksternals->isEmpty()) {
            return view('admin.pages.training_eksternal.view_tanggal',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_training_eksternals' => $item_training_eksternals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.view_tanggal');
        }    
    }

    public function excell_training_eksternal(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Area');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Penempatan');
        $sheet->setCellValue('G1', 'Institusi');
        $sheet->setCellValue('H1', 'Training');
        $sheet->setCellValue('I1', 'Tanggal Awal');
        $sheet->setCellValue('J1', 'Tanggal Akhir');
        $sheet->setCellValue('K1', 'Jam');
        $sheet->setCellValue('L1', 'Lokasi');
        $sheet->setCellValue('M1', 'Alamat');
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

        $tanggal_awal   = Carbon::parse($request->tanggal_awal)->format('Y-m-d');
        $tanggal_akhir  = Carbon::parse($request->tanggal_akhir)->format('Y-m-d');

        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.areas',
                            'employees.positions',
                            'employees.divisions'
                            ])
                            ->when($request->tanggal_awal && $request->tanggal_akhir, function ($query) use ($request) 
                            {
                                $query->whereBetween('tanggal_awal_training_eksternal', [
                                $request->tanggal_awal,
                                $request->tanggal_akhir
                                ]);
                            })->get();
        
        $row = 2;
        $no = 1;
        foreach ($item_training_eksternals as $item_training_eksternal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_eksternal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_eksternal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_eksternal->employees->areas->area);
                $sheet->setCellValue('E'.$row, $item_training_eksternal->employees->positions->jabatan);
                $sheet->setCellValue('F'.$row, $item_training_eksternal->employees->divisions->penempatan);
                $sheet->setCellValue('G'.$row, $item_training_eksternal->institusi_penyelenggara_training_eksternal);
                $sheet->setCellValue('H'.$row, $item_training_eksternal->perihal_training_eksternal);
                $sheet->setCellValue('I'.$row, "'".$item_training_eksternal->tanggal_awal_training_eksternal);
                $sheet->setCellValue('J'.$row, "'".$item_training_eksternal->tanggal_akhir_training_eksternal);
                $sheet->setCellValue('K'.$row, "'".$item_training_eksternal->jam_training_eksternal);
                $sheet->setCellValue('L'.$row, $item_training_eksternal->lokasi_training_eksternal);
                $sheet->setCellValue('M'.$row, $item_training_eksternal->alamat_training_eksternal);
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
                $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I2:I{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J2:J{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K2:K{$lastRow}")
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

        $filename = 'TrainingEksternalBerdasarkanTanggal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function view_nama()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['divisions'])->get();
        return view('admin.pages.training_eksternal.form_view_nama',[
            'employees' => $employees
        ]);
    }

    public function tampil_view_nama(NamaKaryawanRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $employees_id   = $request->input('employees_id');
        $employee       = Employees::where('id',$employees_id)->first();
        $nama_karyawan  = $employee->nama_karyawan;
        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            ])->where('employees_id', $employees_id)->get();
        if (!$item_training_eksternals->isEmpty()) {
            return view('admin.pages.training_eksternal.view_nama',[
                'employees_id'              => $employees_id,
                'nama_karyawan'             => $nama_karyawan,
                'item_training_eksternals'  => $item_training_eksternals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.view_nama');
        }  
    }

    public function excell_view_nama(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Hari');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Jam');
        $sheet->setCellValue('E1', 'Lokasi');
        $sheet->setCellValue('F1', 'Institusi Penyelenggara');
        $sheet->setCellValue('G1', 'Training');
        $sheet->setCellValue('H1', 'Alamat');
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
        
        $item_training_eksternals = TrainingEksternals::with('employees')
                                ->where('employees_id', $request->input('employees_id'))
                                ->get();
        
        $nama_karyawan = $request->input('nama_karyawan');
        
        $row = 2;
        $no = 1;
        foreach ($item_training_eksternals as $item_training_eksternal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $item_training_eksternal->hari_awal_training_eksternal);
                $sheet->setCellValue('C'.$row, "'".$item_training_eksternal->tanggal_awal_training_eksternal);
                $sheet->setCellValue('D'.$row, "'".$item_training_eksternal->jam_training_eksternal);
                $sheet->setCellValue('E'.$row, $item_training_eksternal->lokasi_training_eksternal);
                $sheet->setCellValue('F'.$row, $item_training_eksternal->institusi_penyelenggara_training_eksternal);
                $sheet->setCellValue('G'.$row, $item_training_eksternal->perihal_training_eksternal);
                $sheet->setCellValue('H'.$row, $item_training_eksternal->alamat_training_eksternal);
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
                $sheet->getStyle("C2:C{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E2:E{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F2:F{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G2:G{$lastRow}")
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


        $filename = 'TrainingEksternal'.$nama_karyawan.'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function view_penempatan()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $divisions      = Divisions::all();
        return view('admin.pages.training_eksternal.form_view_penempatan',[
            'divisions' => $divisions
        ]);
    }

    public function tampil_view_penempatan(PenempatanRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $divisions_id   = $request->input('divisions_id');
        $divisions      = Divisions::where('id',$divisions_id)->first();
        $penempatan     = $divisions->penempatan;
        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereHas('employees', function ($query) use ($divisions_id) {
                            $query->where('divisions_id', $divisions_id);
                            })->get();

        if (!$item_training_eksternals->isEmpty()) {
            return view('admin.pages.training_eksternal.view_penempatan',[
                'divisions_id'              => $divisions_id,
                'penempatan'                => $penempatan,
                'item_training_eksternals'   => $item_training_eksternals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.view_penempatan');
        }  
    }

    public function excell_view_penempatan(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Penempatan');
        $sheet->setCellValue('F1', 'Tanggal Awal');
        $sheet->setCellValue('G1', 'Tanggal Akhir');
        $sheet->setCellValue('H1', 'Jam');
        $sheet->setCellValue('I1', 'Lokasi');
        $sheet->setCellValue('J1', 'Institusi');
        $sheet->setCellValue('K1', 'Training');
        $sheet->setCellValue('L1', 'Alamat');
        // Header

        //Style
        $sheet->getStyle('A1:L1')->applyFromArray([
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

        $divisions_id   = $request->divisions_id;
        $divisions      = Divisions::where('id',$divisions_id)->first();
        $penempatan     = $divisions->penempatan;

        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereHas('employees', function ($query) use ($divisions_id) {
                            $query->where('divisions_id', $divisions_id);
                            })->get();

        $row = 2;
        $no = 1;
        foreach ($item_training_eksternals as $item_training_eksternal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_eksternal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_eksternal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_eksternal->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $item_training_eksternal->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, "'".$item_training_eksternal->tanggal_awal_training_eksternal);
                $sheet->setCellValue('G'.$row, "'".$item_training_eksternal->tanggal_akhir_training_eksternal);
                $sheet->setCellValue('H'.$row, "'".$item_training_eksternal->jam_training_eksternal);
                $sheet->setCellValue('I'.$row, $item_training_eksternal->lokasi_training_eksternal);
                $sheet->setCellValue('J'.$row, $item_training_eksternal->institusi_penyelenggara_training_eksternal);
                $sheet->setCellValue('K'.$row, $item_training_eksternal->perihal_training_eksternal);
                $sheet->setCellValue('L'.$row, $item_training_eksternal->alamat_training_eksternal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:L{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:L{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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
                $sheet->getStyle("J2:J{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K2:K{$lastRow}")
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

        $filename = 'TrainingEksternal'.$penempatan.'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function view_materi()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $item_training_eksternals = TrainingEksternals::with([
                                'employees',
                                'employees.divisions',
                                'employees.positions',
                                ])->select('perihal_training_eksternal')->groupBy('perihal_training_eksternal')->get();
        return view('admin.pages.training_eksternal.form_view_materi',[
            'item_training_eksternals' => $item_training_eksternals
        ]);
    }

    public function tampil_view_materi(MateriTrainingEksternalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data                        = $request->except('_token');
        $perihal_training_eksternal   = $request->input('perihal_training_eksternal');
        $training_eksternal          = TrainingEksternals::where('perihal_training_eksternal',$perihal_training_eksternal)->first();
        $item_training_eksternals    = TrainingEksternals::with([
                                        'employees',
                                        'employees.divisions',
                                        'employees.positions',
                                        ])->where('perihal_training_eksternal', $perihal_training_eksternal)->get();
                            
        if (!$item_training_eksternals->isEmpty()) {
            return view('admin.pages.training_eksternal.view_materi',[
                'perihal_training_eksternal'    => $perihal_training_eksternal,
                'item_training_eksternals'      => $item_training_eksternals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.view_materi');
        }  
    }

    public function excell_view_materi(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Penempatan');
        $sheet->setCellValue('F1', 'Penyelenggara');
        $sheet->setCellValue('G1', 'Training');
        $sheet->setCellValue('H1', 'Tanggal Awal');
        $sheet->setCellValue('I1', 'Tanggal Akhir');
        $sheet->setCellValue('J1', 'Jam');
        $sheet->setCellValue('K1', 'Lokasi');
        $sheet->setCellValue('L1', 'Alamat');
        // Header

        //Style
        $sheet->getStyle('A1:L1')->applyFromArray([
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


        $perihal_training_eksternal   = $request->perihal_training_eksternal;
        
        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->where('perihal_training_eksternal', $perihal_training_eksternal)->get();

        $row = 2;
        $no = 1;
        foreach ($item_training_eksternals as $item_training_eksternal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_eksternal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_eksternal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_eksternal->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $item_training_eksternal->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $item_training_eksternal->institusi_penyelenggara_training_eksternal);
                $sheet->setCellValue('G'.$row, $item_training_eksternal->perihal_training_eksternal);
                $sheet->setCellValue('H'.$row, "'".$item_training_eksternal->tanggal_awal_training_eksternal);
                $sheet->setCellValue('I'.$row, "'".$item_training_eksternal->tanggal_akhir_training_eksternal);
                $sheet->setCellValue('J'.$row, "'".$item_training_eksternal->jam_training_eksternal);
                $sheet->setCellValue('K'.$row, $item_training_eksternal->lokasi_training_eksternal);
                $sheet->setCellValue('L'.$row, $item_training_eksternal->alamat_training_eksternal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:L{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:L{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F2:F{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:H{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I2:I{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J2:J{$lastRow}")
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

        $filename = 'TrainingEksternal'.$perihal_training_eksternal.'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function form_edit_tanggal()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_eksternal.form_edit_tanggal');
    }

    public function edit_tanggal(TanggalTrainingEksternalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $tanggal_awal_training_eksternal  = $request->input('tanggal_awal_training_eksternal');
        $employees      = Employees::with(['divisions'])->get();
        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->where('tanggal_awal_training_eksternal', $tanggal_awal_training_eksternal)->get();
        if (!$item_training_eksternals->isEmpty()) {
            $selectedEmployees = $item_training_eksternals
                            ->pluck('employees_id')
                            ->toArray();
            return view('admin.pages.training_eksternal.view_edit_tanggal', [
                'employees' => $employees,
                'item_training_eksternals' => $item_training_eksternals,
                'selectedEmployees' => $selectedEmployees
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.form_edit_tanggal');
        }  
    }

    public function update_tanggal(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        
        $data   = $request->except('_token');
        try {
        DB::transaction(function () use ($request) {
            
        $tanggal_awal   = $request->input('tanggal_awal_training_eksternal');
        $tanggal_akhir  = $request->input('tanggal_akhir_training_eksternal');
        $tanggal_awal_lama_training_eksternal  = $request->input('tanggal_awal_lama_training_eksternal');

        $hari_awal  = \Carbon\Carbon::parse($tanggal_awal)->isoformat('dddd');
        $hari_akhir = \Carbon\Carbon::parse($tanggal_akhir)->isoformat('dddd');

        TrainingEksternals::where('tanggal_awal_training_eksternal', $tanggal_awal_lama_training_eksternal)->delete();

        foreach ($request->employees_id as $employeeId) {
            $employee = Employees::find($employeeId);

            TrainingEksternals::create([
            'employees_id'                                  => $employee->id,
            'nik_karyawan'                                  => $employee->nik_karyawan,
            'hari_awal_training_eksternal'                  => $hari_awal,
            'hari_akhir_training_eksternal'                 => $hari_akhir,
            'tanggal_awal_training_eksternal'               => $request->tanggal_awal_training_eksternal,
            'tanggal_akhir_training_eksternal'              => $request->tanggal_akhir_training_eksternal,
            'institusi_penyelenggara_training_eksternal'    => $request->institusi_penyelenggara_training_eksternal,
            'perihal_training_eksternal'                    => $request->perihal_training_eksternal,
            'jam_training_eksternal'                        => $request->jam_training_eksternal,
            'lokasi_training_eksternal'                     => $request->lokasi_training_eksternal,
            'alamat_training_eksternal'                     => $request->alamat_training_eksternal,
            'edit_oleh'                                     => auth()->user()->name
        ]);
        }
        });
        Alert::success('Success Edit Data Training Eksternal','Oleh '.auth()->user()->name);
        return redirect()->route('training_eksternal.index');
        } catch (\Exception $e) {
            // Jika ada error (misal: ID karyawan tidak ketemu, atau masalah koneksi DB)
        // Database akan otomatis Rollback (pembatalan delete/create)
        Log::error("Gagal update training eksternal: " . $e->getMessage());
        
        return redirect()->back()
            ->withErrors(['error' => 'Terjadi kesalahan sistem saat menyimpan data.'])
            ->withInput();
        }    
    }

    public function form_hapus_tanggal()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_eksternal.form_hapus_tanggal');
    }

    public function tampil_hapus_tanggal(TanggalTrainingEksternalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $tanggal_awal_training_eksternal    = $request->input('tanggal_awal_training_eksternal');
        $tanggal_akhir_training_eksternal   = $request->input('tanggal_akhir_training_eksternal');

        $item_training_eksternals = TrainingEksternals::with([
                            'employees',
                            'employees.areas',
                            'employees.positions',
                            'employees.divisions',
                            ])->where('tanggal_awal_training_eksternal',$tanggal_awal_training_eksternal)->get();

        if (!$item_training_eksternals->isEmpty()) {
            return view('admin.pages.training_eksternal.view_hapus_tanggal',[
                'tanggal_awal_training_eksternal'   => $tanggal_awal_training_eksternal,
                'tanggal_akhir_training_eksternal'  => $tanggal_akhir_training_eksternal,
                'item_training_eksternals'          => $item_training_eksternals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_eksternal.form_hapus_tanggal');
        }  
    }

    public function hapus_tanggal(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $tanggal_awal_training_eksternal = $request->tanggal_awal_training_eksternal;

        DB::transaction(function () use ($tanggal_awal_training_eksternal) {
        $training = TrainingEksternals::where('tanggal_awal_training_eksternal',$tanggal_awal_training_eksternal);
        $training->update([
                'hapus_oleh' => auth()->user()->name
                ]);

        $training->delete();
        });
        Alert::error('Menghapus Data Training Eksternal','Oleh '.auth()->user()->name);
        return redirect()->route('training_eksternal.index');
    }
}

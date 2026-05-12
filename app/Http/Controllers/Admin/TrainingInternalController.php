<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TrainingInternalRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\NamaKaryawanRequest;
use App\Http\Requests\Admin\PenempatanRequest;
use App\Http\Requests\Admin\MateriTrainingInternalRequest;
use App\Http\Requests\Admin\TanggalTrainingRequest;
use App\Models\Admin\TrainingInternals;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
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

class TrainingInternalController extends Controller
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

        return view('admin.pages.training_internal.index');
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
        return view('admin.pages.training_internal.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingInternalRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal    = $request->input('tanggal_training_internal');
        $hari       = \Carbon\Carbon::parse($tanggal)->isoformat('dddd');
        foreach ($request->employees_id as $employeeId) {
            $employee = Employees::find($employeeId);
            $insert = [
                'employees_id'              => $employee->id,
                'nik_karyawan'              => $employee->nik_karyawan,
                'hari_training_internal'    => $hari,
                'tanggal_training_internal' => $request->tanggal_training_internal,
                'jam_training_internal'     => $request->jam_training_internal,
                'lokasi_training_internal'  => $request->lokasi_training_internal,
                'materi_training_internal'  => $request->materi_training_internal,
                'trainer_training_internal' => $request->trainer_training_internal,
                'input_oleh'                => auth()->user()->name
            ];
            TrainingInternals::create($insert);
        }
        Alert::success('Success Input Data Training Internal','Oleh '.auth()->user()->name);
        return redirect()->route('training_internal.index');
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
    public function update(TrainingInternalRequest $request, string $id)
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
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_internal.form_view_tanggal');
    }

    public function tampil_view_tanggal(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.areas',
                            'employees.golongans',
                            'employees.positions',
                            'employees.divisions',
                            ])->whereBetween('tanggal_training_internal', [$tanggal_awal, $tanggal_akhir])->get();

        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_tanggal',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_training_internals' => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.view_tanggal');
        }       
    }

    public function excell_training_internal(Request $request)
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
        $sheet->setCellValue('D1', 'Area');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Penempatan');
        $sheet->setCellValue('G1', 'Tanggal');
        $sheet->setCellValue('H1', 'Lokasi');
        $sheet->setCellValue('I1', 'Materi');
        $sheet->setCellValue('J1', 'Trainer');
        // Header

        //Style
        $sheet->getStyle('A1:J1')->applyFromArray([
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

        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.areas',
                            'employees.positions',
                            'employees.divisions'
                            ])
                            ->when($request->tanggal_awal && $request->tanggal_akhir, function ($query) use ($request) 
                            {
                                $query->whereBetween('tanggal_training_internal', [
                                $request->tanggal_awal,
                                $request->tanggal_akhir
                                ]);
                            })->get();
        
        $row = 2;
        $no = 1;
        foreach ($item_training_internals as $item_training_internal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_internal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_internal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_internal->employees->areas->area);
                $sheet->setCellValue('E'.$row, $item_training_internal->employees->positions->jabatan);
                $sheet->setCellValue('F'.$row, $item_training_internal->employees->divisions->penempatan);
                $sheet->setCellValue('G'.$row, "'".$item_training_internal->tanggal_training_internal);
                $sheet->setCellValue('H'.$row, $item_training_internal->lokasi_training_internal);
                $sheet->setCellValue('I'.$row, $item_training_internal->materi_training_internal);
                $sheet->setCellValue('J'.$row, $item_training_internal->trainer_training_internal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:J{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$lastRow}")
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

        $filename = 'TrainingInternalBerdasarkanTanggal.xlsx';

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
        $employees      = Employees::all();
        return view('admin.pages.training_internal.form_view_nama',[
            'employees' => $employees
        ]);
    }

    public function tampil_view_nama(NamaKaryawanRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees_id   = $request->input('employees_id');
        $employee       = Employees::where('id',$employees_id)->first();
        $nama_karyawan  = $employee->nama_karyawan;
        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            ])->where('employees_id', $employees_id)->get();
        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_nama',[
                'employees_id'              => $employees_id,
                'nama_karyawan'             => $nama_karyawan,
                'item_training_internals'   => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.view_nama');
        }     
    }

    public function excell_view_nama(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Hari');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Jam');
        $sheet->setCellValue('E1', 'Lokasi');
        $sheet->setCellValue('F1', 'Materi');
        $sheet->setCellValue('G1', 'Trainer');
        // Header

        //Style
        $sheet->getStyle('A1:G1')->applyFromArray([
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
        
        $item_training_internals = TrainingInternals::with('employees')
                                ->where('employees_id', $request->input('employees_id'))
                                ->get();
        
        $nama_karyawan = $request->input('nama_karyawan');
        
        // dd($request->input('employees_id'));

        $row = 2;
        $no = 1;
        foreach ($item_training_internals as $item_training_internal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $item_training_internal->hari_training_internal);
                $sheet->setCellValue('C'.$row, "'".$item_training_internal->tanggal_training_internal);
                $sheet->setCellValue('D'.$row, $item_training_internal->jam_training_internal);
                $sheet->setCellValue('E'.$row, $item_training_internal->lokasi_training_internal);
                $sheet->setCellValue('F'.$row, $item_training_internal->materi_training_internal);
                $sheet->setCellValue('G'.$row, $item_training_internal->trainer_training_internal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:G{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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


        $filename = 'TrainingInternal'.$nama_karyawan.'.xlsx';

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
        return view('admin.pages.training_internal.form_view_penempatan',[
            'divisions' => $divisions
        ]);
    }

    public function tampil_view_penempatan(PenempatanRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $divisions_id   = $request->input('divisions_id');
        $divisions      = Divisions::where('id',$divisions_id)->first();
        $penempatan     = $divisions->penempatan;

        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereHas('employees', function ($query) use ($divisions_id) {
                            $query->where('divisions_id', $divisions_id);
                            })->get();

        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_penempatan',[
                'divisions_id'              => $divisions_id,
                'penempatan'                => $penempatan,
                'item_training_internals'   => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.view_penempatan');
        }  
    }

    public function excell_view_penempatan(Request $request)
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
        $sheet->setCellValue('F1', 'Tanggal');
        $sheet->setCellValue('G1', 'Lokasi');
        $sheet->setCellValue('H1', 'Materi');
        $sheet->setCellValue('I1', 'Trainer');
        // Header

        //Style
        $sheet->getStyle('A1:I1')->applyFromArray([
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

        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->whereHas('employees', function ($query) use ($divisions_id) {
                            $query->where('divisions_id', $divisions_id);
                            })->get();

        $row = 2;
        $no = 1;
        foreach ($item_training_internals as $item_training_internal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_internal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_internal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_internal->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $item_training_internal->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $item_training_internal->tanggal_training_internal);
                $sheet->setCellValue('G'.$row, "'".$item_training_internal->lokasi_training_internal);
                $sheet->setCellValue('H'.$row, $item_training_internal->materi_training_internal);
                $sheet->setCellValue('I'.$row, $item_training_internal->trainer_training_internal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:I{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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

        $filename = 'TrainingInternal'.$penempatan.'.xlsx';

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

        $item_training_internals = TrainingInternals::with([
                                'employees',
                                'employees.divisions',
                                'employees.positions',
                                ])->select('materi_training_internal')->groupBy('materi_training_internal')->get();

        return view('admin.pages.training_internal.form_view_materi',[
            'item_training_internals' => $item_training_internals
        ]);
    }

    public function tampil_view_materi(MateriTrainingInternalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $materi_training_internal   = $request->input('materi_training_internal');
        $training_internal          = TrainingInternals::where('materi_training_internal',$materi_training_internal)->first();
        $item_training_internals    = TrainingInternals::with([
                                        'employees',
                                        'employees.divisions',
                                        'employees.positions',
                                        ])->where('materi_training_internal', $materi_training_internal)->get();

        // dd($item_training_internals);
                            
        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_materi',[
                'materi_training_internal'  => $materi_training_internal,
                'item_training_internals'   => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.view_materi');
        }   
    }

    public function excell_view_materi(Request $request)
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
        $sheet->setCellValue('F1', 'Tanggal');
        $sheet->setCellValue('G1', 'Lokasi');
        $sheet->setCellValue('H1', 'Materi');
        $sheet->setCellValue('I1', 'Trainer');
        // Header

        //Style
        $sheet->getStyle('A1:I1')->applyFromArray([
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


        $materi_training_internal   = $request->materi_training_internal;
        
        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->where('materi_training_internal', $materi_training_internal)->get();

        $row = 2;
        $no = 1;
        foreach ($item_training_internals as $item_training_internal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_internal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_internal->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_internal->employees->positions->jabatan);
                $sheet->setCellValue('E'.$row, $item_training_internal->employees->divisions->penempatan);
                $sheet->setCellValue('F'.$row, $item_training_internal->tanggal_training_internal);
                $sheet->setCellValue('G'.$row, "'".$item_training_internal->lokasi_training_internal);
                $sheet->setCellValue('H'.$row, $item_training_internal->materi_training_internal);
                $sheet->setCellValue('I'.$row, $item_training_internal->trainer_training_internal);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:I{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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

        $filename = 'TrainingInternal'.$materi_training_internal.'.xlsx';

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

        return view('admin.pages.training_internal.form_edit_tanggal');
    }

    public function edit_tanggal(TanggalTrainingRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_training_internal  = $request->input('tanggal_training_internal');

        $employees = Employees::all();
        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            ])->where('tanggal_training_internal', $tanggal_training_internal)->get();
        
        if (!$item_training_internals->isEmpty()) {
            $selectedEmployees = $item_training_internals
                            ->pluck('employees_id')
                            ->toArray();
            return view('admin.pages.training_internal.view_edit_tanggal', [
                'employees' => $employees,
                'item_training_internals' => $item_training_internals,
                'selectedEmployees' => $selectedEmployees
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.form_edit_tanggal');
        }  
    }

    public function update_tanggal(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        
        $tanggal    = $request->input('tanggal_training_internal');
        $tanggal_lama    = $request->input('tanggal_lama_training_internal');
        $hari       = \Carbon\Carbon::parse($tanggal)->isoformat('dddd');

        TrainingInternals::where('tanggal_training_internal', $tanggal_lama)->delete();

        foreach ($request->employees_id as $employeeId) {
            $employee = Employees::find($employeeId);

            TrainingInternals::create([
            'employees_id'              => $employee->id,
            'nik_karyawan'              => $employee->nik_karyawan,
            'hari_training_internal'    => $hari,
            'tanggal_training_internal' => $request->tanggal_training_internal,
            'jam_training_internal'     => $request->jam_training_internal,
            'lokasi_training_internal'  => $request->lokasi_training_internal,
            'materi_training_internal'  => $request->materi_training_internal,
            'trainer_training_internal' => $request->trainer_training_internal,
            'edit_oleh'                 => auth()->user()->name
        ]);
        }
        Alert::success('Success Edit Data Training Internal','Oleh '.auth()->user()->name);
        return redirect()->route('training_internal.index');
    }

    public function form_hapus_tanggal()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_internal.form_hapus_tanggal');
    }

    public function tampil_hapus_tanggal(TanggalTrainingRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_training_internal  = $request->input('tanggal_training_internal');
        $item_training_internals = TrainingInternals::with([
                            'employees',
                            'employees.areas',
                            'employees.positions',
                            'employees.divisions',
                            ])->where('tanggal_training_internal',$tanggal_training_internal)->get();

        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_hapus_tanggal',[
                'tanggal_training_internal' => $tanggal_training_internal,
                'item_training_internals'   => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.form_hapus_tanggal');
        }  
    }

    public function hapus_tanggal(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_training_internal = $request->tanggal_training_internal;
        DB::transaction(function () use ($tanggal_training_internal) {
        $training = TrainingInternals::where('tanggal_training_internal',$tanggal_training_internal);
        $training->update([
                'hapus_oleh' => auth()->user()->name
                ]);

        $training->delete();
        });
        Alert::error('Menghapus Data Training Internal','Oleh '.auth()->user()->name);
        return redirect()->route('training_internal.index');
    }

    public function form_belum_training()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.training_internal.form_view_belum_training');
    }

    public function tampil_view_belum_training(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $item_training_internals = Employees::with([
                                'areas',
                                'golongans',
                                'positions',
                                'divisions'
                            ])->whereDoesntHave('training_internals', function ($query) use ($tanggal_awal, $tanggal_akhir) {
                                $query->whereBetween('tanggal_training_internal', [$tanggal_awal, $tanggal_akhir]);
                            })->get();

        if (!$item_training_internals->isEmpty()) {
            return view('admin.pages.training_internal.view_belum_training',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_training_internals' => $item_training_internals
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('training_internal.form_belum_training');
        }   
    }

    public function excell_belum_training_internal(Request $request)
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
        // Header

        //Style
        $sheet->getStyle('A1:E1')->applyFromArray([
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

        $item_training_internals = Employees::with([
                                'areas',
                                'golongans',
                                'positions',
                                'divisions'
                            ])->whereDoesntHave('training_internals', function ($query) use ($tanggal_awal, $tanggal_akhir) {
                                $query->whereBetween('tanggal_training_internal', [$tanggal_awal, $tanggal_akhir]);
                            })->get();

        $row = 2;
        $no = 1;
        foreach ($item_training_internals as $item_training_internal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_training_internal->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_training_internal->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_training_internal->positions->jabatan);
                $sheet->setCellValue('E'.$row, $item_training_internal->divisions->penempatan);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:E{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:E{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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
        
        // Auto width
        $highestColumn = $sheet->getHighestColumn(); 
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);

        $filename = 'TrainingInternal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

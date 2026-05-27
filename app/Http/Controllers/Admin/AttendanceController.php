<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AttendanceRequest;
use App\Http\Requests\Admin\AttendanceViewRequest;
use App\Http\Requests\Admin\AttendanceUpdateRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Models\Admin\Attendances;
use App\Models\Admin\Employees;
use App\Models\Admin\Areas;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
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

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $currentYear = now()->year;

        // 2. Ambil data absensi tahun ini (1 Query)
        $attendances = Attendances::whereYear('tanggal_absen', $currentYear)->get();

        // 3. Bangun query selectRaw untuk hitung total bulanan sekaligus (1 Query untuk statistik)
        $categories = ['Sakit', 'Ijin', 'Alpa', 'Cuti Tahunan', 'OFF'];
        $selectQueries = [];
        
        foreach ($categories as $category) {
            $key = strtolower(str_replace(' ', '_', $category)); 
            for ($month = 1; $month <= 12; $month++) {
                $selectQueries[] = "SUM(CASE WHEN MONTH(tanggal_absen) = {$month} AND keterangan_absen = '{$category}' THEN 1 ELSE 0 END) as item_{$key}_{$month}";
            }
        }

        // Eksekusi query statistik ke database 
        $statistics = Attendances::whereYear('tanggal_absen', $currentYear)
            ->selectRaw(implode(', ', $selectQueries))
            ->first()
            ->toArray();

        // 4. Format data ke dalam bentuk yang dipahami Highcharts
        $chartData = [];
        foreach ($categories as $category) {
            $key = strtolower(str_replace(' ', '_', $category));
            
            $monthlyCounts = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyCounts[] = (int) ($statistics["item_{$key}_{$month}"] ?? 0);
            }
            $chartData[] = [
                'name' => $category,
                'data' => $monthlyCounts
            ];
        }

        return view('admin.pages.attendance.index',[
            'chartData'   => $chartData,
            'statistics'  => $statistics
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    
        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        if (!empty($divisionIds)) 
        {
            $employees = Employees::with([
                'divisions'
            ])->whereIn('divisions_id',$divisionIds)->get();
        }
        else{
            $employees = Employees::with([
                'divisions'
            ])->get();
        }

        return view('admin.pages.attendance.create',[
            'employees' => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttendanceRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_attendance    = $request->except('_token');
        foreach ($request->employees_id as $employeeId) 
        {
            $employee = Employees::find($employeeId);
            $insert = [
                'employees_id'              => $employee->id,
                'nik_karyawan'              => $employee->nik_karyawan,
                'tanggal_absen'             => $request->input('tanggal_absen'),
                'lama_absen'                => 1,
                'keterangan_absen'          => $request->input('keterangan_absen'),
                'keterangan_cuti_khusus'    => $request->input('keterangan_cuti_khusus'),
                'input_oleh'                => Auth::user()->name
                ];
                Attendances::create($insert);
        }
        Alert::success('Success Input Data Absensi','Oleh '.auth()->user()->name);
        return redirect()->route('attendance.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
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
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                   = $request->except('_token');
        $tanggal_absen          = $request->input('tanggal_absen');
        $keterangan_absen       = $request->input('keterangan_absen');
        $keterangan_cuti_khusus = $request->input('keterangan_cuti_khusus');
        $attendance             = Attendances::where('id', $id)->first();
        $attendance->update([
            'tanggal_absen'             => $request->input('tanggal_absen'),
            'keterangan_absen'          => $request->input('keterangan_absen'),
            'keterangan_cuti_khusus'    => $request->input('keterangan_cuti_khusus'),
            'edit_oleh'                 => auth()->user()->name
        ]);
        Alert::success('Success Edit Data Absensi','Oleh '.auth()->user()->name);
        return redirect()->route('attendance.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        DB::transaction(function () use ($id) {
            $attendance = Attendances::findOrFail($id);
            $attendance->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $attendance->delete();
        });
        Alert::error('Menghapus Data Absensi','Oleh '.auth()->user()->name);
        return redirect()->route('attendance.index');
    }

    public function form_edit()
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)->value('divisions_id');
        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        if (!empty($divisionIds)) 
        {
            $employees = Employees::with([
                'divisions'
            ])->whereIn('divisions_id',$divisionIds)->get();
        }
        else{
            $employees = Employees::with([
                'divisions'
            ])->get();
        }

        return view('admin.pages.attendance.edit',[
            'employees' => $employees
        ]);
    }

    public function tampil_form_edit(AttendanceUpdateRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $tanggal_absen      = $request->input('tanggal_absen');
        $item_attendance = Attendances::with([
                            'employees'
                            ])
                            ->where('employees_id', $employees_id)
                            ->where('tanggal_absen', $tanggal_absen)
                            ->first();

        if ($item_attendance == null) {
            Alert::error('Data yang anda cari tidak ada');
            return redirect()->route('attendance.index');
        } else {
        return view('admin.pages.attendance.tampil_edit',[
            'item_attendance' => $item_attendance
        ]);
        }
    }

    public function form_hapus()
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        if (!empty($divisionIds)) 
        {
            $employees = Employees::with([
                'divisions'
            ])->whereIn('divisions_id',$divisionIds)->get();
        }
        else{
            $employees = Employees::with([
                'divisions'
            ])->get();
        }
        return view('admin.pages.attendance.hapus',[
            'employees' => $employees
        ]);
    }

    public function tampil_form_hapus(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $tanggal_absen      = $request->input('tanggal_absen');
        $item_attendance = Attendances::with([
                            'employees'
                            ])
                            ->where('employees_id', $employees_id)
                            ->where('tanggal_absen', $tanggal_absen)
                            ->first();

        if ($item_attendance == null) {
            Alert::error('Data yang anda cari tidak ada');
            return redirect()->route('attendance.index');
        } else {
        return view('admin.pages.attendance.tampil_hapus',[
            'item_attendance' => $item_attendance
        ]);
        }
    }

    public function form_tampil()
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.attendance.tampil_absen');
    }

    public function tampil_absen(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data           = $request->except('_token');
        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];

        $query = Attendances::with([
            'employees',
            'employees.divisions',
            'employees.positions',
            'employees.golongans'
        ])
        ->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir])
        ->whereHas('employees');

        if ($divisi && array_key_exists($divisi, $divisionMap)) {
            $divisionIds = $divisionMap[$divisi];
            
            $query->whereHas('employees', function ($q) use ($divisionIds) {
                $q->whereIn('divisions_id', $divisionIds);
            });
        }
        $item_attendances = $query->get();

        if ($item_attendances->isNotEmpty()) {
            return view('admin.pages.attendance.show',[
                'tanggal_awal'  => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'item_attendances' => $item_attendances
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('attendance.form_tampil');
        }        
    }

    public function export_excell_absensi(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Golongan');
        $sheet->setCellValue('E1', 'Area');
        $sheet->setCellValue('F1', 'Jabatan');
        $sheet->setCellValue('G1', 'Penempatan');
        $sheet->setCellValue('H1', 'Tanggal Absen');
        $sheet->setCellValue('I1', 'Jenis Absen');
        $sheet->setCellValue('J1', 'Keterangan');
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

        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        $query = Attendances::with([
        'employees',
        'employees.divisions',
        'employees.positions',
        'employees.golongans' ])
        ->whereBetween('tanggal_absen', [
            $tanggal_awal,
            $tanggal_akhir ]);

        if (!empty($divisionIds)) 
        {
            $query->whereHas('employees', function ($q) use ($divisionIds) 
            {
                $q->whereIn('divisions_id', $divisionIds);
            });
        }
        $item_attendances = $query->get();

        $row = 2;
        $no = 1;
        foreach ($item_attendances as $item_attendance) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_attendance->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_attendance->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_attendance->employees->golongans->golongan);
                $sheet->setCellValue('E'.$row, $item_attendance->employees->areas->area);
                $sheet->setCellValue('F'.$row, $item_attendance->employees->positions->jabatan);
                $sheet->setCellValue('G'.$row, $item_attendance->employees->divisions->penempatan);
                $sheet->setCellValue('H'.$row, "'".$item_attendance->tanggal_absen);
                $sheet->setCellValue('I'.$row, $item_attendance->keterangan_absen);
                $sheet->setCellValue('J'.$row, $item_attendance->keterangan_cuti_khusus);
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

        $filename = 'DatabaseAbsensiKaryawan.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function form_non_absen()
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.attendance.tampil_non_absen');
    }

    public function tampil_non_absen(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        $data = $request->except('_token');
        $tanggal_awal  = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        if (!empty($divisionIds)) {
        $item_employees = Employees::with([
            'divisions',
            'positions',
            'golongans'
        ])->whereIn('divisions_id',$divisionIds)->whereDoesntHave('attendances', function ($query) use ($tanggal_awal, $tanggal_akhir) 
        {
            $query->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir]);
        })->get();
        }
        else{
            $item_employees = Employees::with([
            'divisions',
            'positions',
            'golongans'
            ])->whereDoesntHave('attendances', function ($query) use ($tanggal_awal, $tanggal_akhir) 
            {
            $query->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir]);
            })->get();
        }

        if ($item_employees->isNotEmpty()) {
            return view('admin.pages.attendance.show_non_absen', [
                'tanggal_awal'   => $tanggal_awal,
                'tanggal_akhir'  => $tanggal_akhir,
                'item_employees' => $item_employees
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('attendance.form_non_absen');
        }
    }

    public function export_excell_non_absensi(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader','accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK Karyawan');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Golongan');
        $sheet->setCellValue('E1', 'Area');
        $sheet->setCellValue('F1', 'Jabatan');
        $sheet->setCellValue('G1', 'Penempatan');
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

        $tanggal_awal   = Carbon::parse($request->tanggal_awal)->format('Y-m-d');
        $tanggal_akhir  = Carbon::parse($request->tanggal_akhir)->format('Y-m-d');
        $nik            = auth()->user()->nik;
        $divisi         = Employees::where('nik_karyawan', $nik)
                                    ->value('divisions_id');
        $divisionMap = [
        19 => [19,20,21],
        11 => [11],
        10 => [10],
        14 => [14],
        5  => [5,6,9,11,12,13,14,15,16,19,20,21],
        ];
        $divisionIds = $divisionMap[$divisi] ?? [];

        if (!empty($divisionIds)) {
        $item_employees = Employees::with([
            'divisions',
            'positions',
            'golongans'
        ])->whereIn('divisions_id',$divisionIds)->whereDoesntHave('attendances', function ($query) use ($tanggal_awal, $tanggal_akhir) 
        {
            $query->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir]);
        })->get();
        }
        else{
            $item_employees = Employees::with([
            'divisions',
            'positions',
            'golongans'
            ])->whereDoesntHave('attendances', function ($query) use ($tanggal_awal, $tanggal_akhir) 
            {
            $query->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir]);
            })->get();
        }

        $row = 2;
        $no = 1;
        foreach ($item_employees as $item_employee) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_employee->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_employee->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_employee->golongans->golongan);
                $sheet->setCellValue('E'.$row, $item_employee->areas->area);
                $sheet->setCellValue('F'.$row, $item_employee->positions->jabatan);
                $sheet->setCellValue('G'.$row, $item_employee->divisions->penempatan);
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

        $filename = 'DatabaseKaryawanNonAbsen.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}

<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OvertimeRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\NamaKaryawanTanggalRequest;
use App\Http\Requests\Admin\OvertimeUpdateRequest;
use App\Http\Requests\Admin\NamaTanggalAwalAkhirRequest;
use App\Http\Requests\Admin\StatusPenempatanTanggalAwalAkhirRequest;
use App\Http\Requests\Admin\UpahLemburPerjamRequest;
use App\Models\Admin\Overtimes;
use App\Models\Admin\Employees;
use App\Models\Admin\Companies;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use App\Models\Admin\Areas;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\HistorySalaries;
use Codedge\Fpdf\Fpdf\Fpdf;
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

class OvertimeController extends Controller
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

        $tahun = Carbon::now()->year;

        $groups = [
            'Produksi'  => [11],
            'PDC'       => [19, 20, 21, 22],
            'Warehouse' => [13, 14],
            'Delivery'  => [12, 15, 18]
            // 'Quality'   => [8]
            // 'PPC'       => [10]
            // 'Office'    => [1, 2, 3, 4, 5, 6, 7, 9]
        ];

        // Flatten all division IDs into a single array for our query filter
        $allDivisionIds = array_merge(...array_values($groups));

        // 4. Run ONE single query to fetch aggregated monthly totals for all divisions
        $overtimeData = Overtimes::query()
                        ->join('employees', 'employees.id', '=', 'overtimes.employees_id')
                        ->join('divisions', 'divisions.id', '=', 'employees.divisions_id')
                        ->selectRaw('divisions.id as division_id,
                                    MONTH(overtimes.tanggal_lembur) as bulan,
                                    SUM(overtimes.jam_lembur) as total_jam')
                        ->whereIn('divisions.id', $allDivisionIds)
                        ->whereNotNull('overtimes.acc_hrd')
                        ->whereYear('overtimes.tanggal_lembur', $tahun)
                        ->groupBy('divisions.id',
                            DB::raw('MONTH(overtimes.tanggal_lembur)')
                        )->get();

        // 5. Initialize a structured array for your view template (Months 1-12)
        $result = [];
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $indexedData = [];

        foreach ($overtimeData as $row) {
            $indexedData[$row->division_id][$row->bulan] = $row->total_jam;
        }

        foreach ($groups as $groupName => $ids) {

            foreach ($monthNames as $monthNum => $monthName) {

                $total = $overtimeData
                    ->filter(function ($row) use ($ids, $monthNum) {
                        return in_array($row->division_id, $ids)
                            && (int)$row->bulan === $monthNum;
                    })
                    ->sum('total_jam');

                $result[$groupName][$monthName] = (float) $total;
            }
        }

        return view('admin.pages.overtime.index',
        [
            'tahun'     => $tahun,
            'result'    => $result
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
                'divisions','golongans'
            ])->whereIn('divisions_id',$divisionIds)->whereIn('golongans_id',[2,3])->get();
        }
        else{
            $employees = Employees::with([
                'divisions'
            ])->get();
        }

        
        return view('admin.pages.overtime.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OvertimeRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        //Request Form
        $data               = $request->except('_token');
        $tanggal_lembur     = $request->input('tanggal_lembur');
        $jenis_lembur       = $request->input('jenis_lembur');
        $keterangan_lembur  = $request->input('keterangan_lembur');
        $jam_masuk          = $request->input('jam_masuk');
        $jam_istirahat      = $request->input('jam_istirahat');
        $jam_pulang         = $request->input('jam_pulang');
        $uang_makan_lembur  = $request->input('uang_makan_lembur');
        $jam_lembur         = $jam_pulang - $jam_istirahat - $jam_masuk;

        //Rumus Lembur
        if ($jenis_lembur == "Libur") {

            $jam_pertama = 0;

            if ($jam_lembur < 8) {
                $jam_kedua = $jam_lembur;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 8) {
                $jam_kedua = 8;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 8) {

                $jam_kedua = 8;

                if ($jam_lembur - $jam_kedua > 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = $jam_lembur - $jam_kedua - $jam_ketiga;
                } elseif ($jam_lembur - $jam_kedua == 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = 0;
                } else {
                    $jam_ketiga = $jam_lembur - $jam_kedua;
                    $jam_keempat = 0;
                }
            }
        } elseif ($jenis_lembur == "Biasa") {
            // $jam_pertama = 0;

            if ($jam_lembur < 1) {
                $jam_pertama = $jam_lembur;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 1) {
                $jam_pertama = 1;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 1) {

                $jam_pertama = 1;

                if ($jam_lembur < 9) {
                    $jam_kedua = $jam_lembur - $jam_pertama;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur == 9) {
                    $jam_kedua = 8;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur > 9) {

                    $jam_kedua = 8;

                    if ($jam_lembur - $jam_kedua - $jam_pertama == 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = 0;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama > 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = $jam_lembur - $jam_ketiga - $jam_kedua - $jam_pertama;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama < 1) {

                        $jam_ketiga = $jam_lembur - $jam_kedua - $jam_pertama;
                        $jam_keempat = 0;
                    }
                }
            }
        } else {
            return redirect()->route('overtime.create');
        }

        $jumlah_jam_pertama     = $jam_pertama * 1.5;
        $jumlah_jam_kedua       = $jam_kedua * 2;
        $jumlah_jam_ketiga      = $jam_ketiga * 3;
        $jumlah_jam_keempat     = $jam_keempat * 4;
        //Rumus Lembur
        
        DB::transaction(function () use (
            $request,
            $tanggal_lembur,
            $jenis_lembur,
            $keterangan_lembur,
            $jam_masuk,
            $jam_istirahat,
            $jam_pulang,
            $jam_lembur,
            $jam_pertama,
            $jam_kedua,
            $jam_ketiga,
            $jam_keempat,
            $jumlah_jam_pertama,
            $jumlah_jam_kedua,
            $jumlah_jam_ketiga,
            $jumlah_jam_keempat,
            $uang_makan_lembur,
        ) {


        foreach ($request->employees_id as $employeeId) {

            $employee = Employees::find($employeeId);
            $insert = [
                'employees_id'          => $employee->id,
                'nik_karyawan'          => $employee->nik_karyawan,
                'jam_masuk'             => $jam_masuk,
                'jam_istirahat'         => $jam_istirahat,
                'jam_pulang'            => $jam_pulang,
                'keterangan_lembur'     => $keterangan_lembur,
                'tanggal_lembur'        => $tanggal_lembur,
                'jenis_lembur'          => $jenis_lembur,
                'jam_lembur'            => $jam_lembur,
                'jam_pertama'           => $jam_pertama,
                'jumlah_jam_pertama'    => $jumlah_jam_pertama,
                'jam_kedua'             => $jam_kedua,
                'jumlah_jam_kedua'      => $jumlah_jam_kedua,
                'jam_ketiga'            => $jam_ketiga,
                'jumlah_jam_ketiga'     => $jumlah_jam_ketiga,
                'jam_keempat'           => $jam_keempat,
                'jumlah_jam_keempat'    => $jumlah_jam_keempat,
                'uang_makan_lembur'     => $uang_makan_lembur,
                'input_oleh'            => auth()->user()->name
            ];
            Overtimes::create($insert);
        }
        });
        Alert::success('Success Input Data Overtime','Oleh '.auth()->user()->name);
        return redirect()->route('overtime.index');
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

        $item_overtime      = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('id', $id)->first();

        return view('admin.pages.overtime.tampil_edit_approval_overtime',['item_overtime' => $item_overtime]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OvertimeUpdateRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_token         = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $tanggal_lembur     = $request->input('tanggal_lembur');
        $jenis_lembur       = $request->input('jenis_lembur');
        $keterangan_lembur  = $request->input('keterangan_lembur');
        $jam_masuk          = $request->input('jam_masuk');
        $jam_istirahat      = $request->input('jam_istirahat');
        $jam_pulang         = $request->input('jam_pulang');
        $uang_makan_lembur  = $request->input('uang_makan_lembur');
        $jam_lembur         = $jam_pulang - $jam_istirahat - $jam_masuk;

        //Rumus Lembur
        if ($jenis_lembur == "Libur") {

            $jam_pertama = 0;

            if ($jam_lembur < 8) {
                $jam_kedua = $jam_lembur;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 8) {
                $jam_kedua = 8;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 8) {

                $jam_kedua = 8;

                if ($jam_lembur - $jam_kedua > 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = $jam_lembur - $jam_kedua - $jam_ketiga;
                } elseif ($jam_lembur - $jam_kedua == 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = 0;
                } else {
                    $jam_ketiga = $jam_lembur - $jam_kedua;
                    $jam_keempat = 0;
                }
            }
        } elseif ($jenis_lembur == "Biasa") {
            // $jam_pertama = 0;

            if ($jam_lembur < 1) {
                $jam_pertama = $jam_lembur;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 1) {
                $jam_pertama = 1;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 1) {

                $jam_pertama = 1;

                if ($jam_lembur < 9) {
                    $jam_kedua = $jam_lembur - $jam_pertama;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur == 9) {
                    $jam_kedua = 8;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur > 9) {

                    $jam_kedua = 8;

                    if ($jam_lembur - $jam_kedua - $jam_pertama == 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = 0;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama > 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = $jam_lembur - $jam_ketiga - $jam_kedua - $jam_pertama;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama < 1) {

                        $jam_ketiga = $jam_lembur - $jam_kedua - $jam_pertama;
                        $jam_keempat = 0;
                    }
                }
            }
        } else {
            return redirect()->route('overtimes.index');
        }

        $jumlah_jam_pertama         = $jam_pertama * 1.5;
        $jumlah_jam_kedua           = $jam_kedua * 2;
        $jumlah_jam_ketiga          = $jam_ketiga * 3;
        $jumlah_jam_keempat         = $jam_keempat * 4;
        //Rumus Lembur

        $overtimes                  = Overtimes::where('id', $id)->first();
        if ($overtimes != null) {
        $overtimes->update([
            'jam_masuk'             => $jam_masuk,
            'jam_istirahat'         => $jam_istirahat,
            'jam_pulang'            => $jam_pulang,
            'keterangan_lembur'     => $keterangan_lembur,
            'tanggal_lembur'        => $tanggal_lembur,
            'jenis_lembur'          => $jenis_lembur,
            'jam_lembur'            => $jam_lembur,
            'jam_pertama'           => $jam_pertama,
            'jumlah_jam_pertama'    => $jumlah_jam_pertama,
            'jam_kedua'             => $jam_kedua,
            'jumlah_jam_kedua'      => $jumlah_jam_kedua,
            'jam_ketiga'            => $jam_ketiga,
            'jumlah_jam_ketiga'     => $jumlah_jam_ketiga,
            'jam_keempat'           => $jam_keempat,
            'jumlah_jam_keempat'    => $jumlah_jam_keempat,
            'uang_makan_lembur'     => $uang_makan_lembur,
            'edit_oleh'             => auth()->user()->name
        ]);
            Alert::success('Success Edit Data Overtime', 'Oleh ' . auth()->user()->name);
            return redirect()->route('overtime.index');

        } else {
            // Jika data tidak ditemukan sejak awal
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        DB::transaction(function () use ($id) {
            $overtime = Overtimes::findOrFail($id);
            $overtime->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $overtime->delete();
        });
        Alert::error('Menghapus Data Overtime','Oleh '.auth()->user()->name);
        return redirect()->route('overtime.index');
        Alert::error('Menghapus Data Overtime', 'Oleh ' . auth()->user()->name);
        return redirect()->route('overtime.index');

    }

    public function lihat_overtime()
    {
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.overtime.lihat_overtime');
    }

    public function tampil_overtime(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
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

        $divisionIds = ($divisi && array_key_exists($divisi, $divisionMap)) ? $divisionMap[$divisi] : null;

        $query = Overtimes::with([
            'employees',
            'employees.divisions',
            'employees.positions',
            'employees.golongans'
        ])
        ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
        ->whereHas('employees', function ($q) use ($divisionIds) {
            if ($divisionIds) {
                $q->whereIn('divisions_id', $divisionIds);
            }
        });

        $item_overtimes = $query->get();

        $query_belum_rekap = Overtimes::whereNull('acc_hrd') 
        ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
        ->whereHas('employees', function ($q) use ($divisionIds) {
            if ($divisionIds) {
                $q->whereIn('divisions_id', $divisionIds);
            }
        });
        $jumlah_belum_direkap = $query_belum_rekap->count();

        $query_sudah_rekap = Overtimes::whereNotNull('acc_hrd')
        ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
        ->whereHas('employees', function ($q) use ($divisionIds) {
            if ($divisionIds) {
                $q->whereIn('divisions_id', $divisionIds);
            }
        });

        $jumlah_sudah_direkap = $query_sudah_rekap->count();

        if (!$item_overtimes->isEmpty()) {
            return view('admin.pages.overtime.tampil_overtime',[
                'tanggal_awal'          => $tanggal_awal,
                'tanggal_akhir'         => $tanggal_akhir,
                'item_overtimes'        => $item_overtimes,
                'jumlah_belum_direkap'  => $jumlah_belum_direkap,
                'jumlah_sudah_direkap'  => $jumlah_sudah_direkap
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.lihat_overtime');
        }   
    }

    public function export_excell_overtime(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader', 'accounting'];
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
        $sheet->setCellValue('H1', 'Tanggal Lembur');
        $sheet->setCellValue('I1', 'Jenis Lembur');
        $sheet->setCellValue('J1', 'Keterangan Lembur');
        $sheet->setCellValue('K1', 'Jam Masuk');
        $sheet->setCellValue('L1', 'Jam Istirahat');
        $sheet->setCellValue('M1', 'Jam Pulang');
        $sheet->setCellValue('N1', 'Jam Lembur');
        $sheet->setCellValue('O1', 'Uang Makan Lembur');
        $sheet->setCellValue('P1', 'Input Oleh');
        $sheet->setCellValue('Q1', 'Tanggal Input');
        $sheet->setCellValue('R1', 'Status');
        // Header

        //Style
        $sheet->getStyle('A1:R1')->applyFromArray([
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

        $divisionIds = ($divisi && array_key_exists($divisi, $divisionMap)) ? $divisionMap[$divisi] : null;

        $query = Overtimes::with([
            'employees',
            'employees.divisions',
            'employees.positions',
            'employees.golongans'
        ])
        ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
        ->whereHas('employees', function ($q) use ($divisionIds) {
            if ($divisionIds) {
                $q->whereIn('divisions_id', $divisionIds);
            }
        });

        $item_overtimes = $query->get();
        
        $row = 2;
        $no = 1;
        foreach ($item_overtimes as $item_overtime) {

                if ($item_overtime->acc_hrd == null) {
                    $status = "Belum Direkap";
                }
                else{
                    $status = "Sudah Direkap";
                }

                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_overtime->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_overtime->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_overtime->employees->golongans->golongan);
                $sheet->setCellValue('E'.$row, $item_overtime->employees->areas->area);
                $sheet->setCellValue('F'.$row, $item_overtime->employees->positions->jabatan);
                $sheet->setCellValue('G'.$row, $item_overtime->employees->divisions->penempatan);
                $sheet->setCellValue('H'.$row, "'".$item_overtime->tanggal_lembur);
                $sheet->setCellValue('I'.$row, $item_overtime->jenis_lembur);
                $sheet->setCellValue('J'.$row, $item_overtime->keterangan_lembur);
                $sheet->setCellValue('K'.$row, "'".$item_overtime->jam_masuk);
                $sheet->setCellValue('L'.$row, "'".$item_overtime->jam_istirahat);
                $sheet->setCellValue('M'.$row, "'".$item_overtime->jam_pulang);
                $sheet->setCellValue('N'.$row, "'".$item_overtime->jam_lembur);
                $sheet->setCellValue('O'.$row, "'".$item_overtime->uang_makan_lembur);
                $sheet->setCellValue('P'.$row, $item_overtime->input_oleh);
                $sheet->setCellValue('Q'.$row, "'".$item_overtime->created_at);
                $sheet->setCellValue('R'.$row, "'".$status);
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:R{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:R{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
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
                $sheet->getStyle("H2:H{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I2:I{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K2:R{$lastRow}")
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

        $filename = 'DataOvertime.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function form_edit_overtime()
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
                'divisions','golongans'
            ])->whereIn('divisions_id',$divisionIds)->whereIn('golongans_id',[2,3])->get();
        }
        else{
            $employees = Employees::with([
                'divisions','golongans'
            ])->get();
        }

        return view('admin.pages.overtime.edit_overtime',[
            'employees' => $employees
        ]);
    }

    public function tampil_edit_overtime(NamaKaryawanTanggalRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_lembur     = $request->input('tanggal');
        $employees_id       = $request->input('employees_id');
        $item_overtime      = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('tanggal_lembur',$tanggal_lembur)->where('employees_id',$employees_id)->first();

        if ($item_overtime != NULL) {
            return view('admin.pages.overtime.tampil_edit_overtime',[
                'tanggal_lembur'    => $tanggal_lembur,
                'item_overtime'     => $item_overtime
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }   
    }

    public function form_hapus_overtime()
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
                'divisions','golongans'
            ])->whereIn('divisions_id',$divisionIds)->whereIn('golongans_id',[2,3])->get();
        }
        else{
            $employees = Employees::with([
                'divisions','golongans'
            ])->get();
        }

        return view('admin.pages.overtime.hapus_overtime',[
            'employees' => $employees
        ]);
    }
    
    public function tampil_hapus_overtime(NamaKaryawanTanggalRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'leader'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $tanggal_lembur     = $request->input('tanggal');
        $item_overtime      = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('tanggal_lembur',$tanggal_lembur)->where('employees_id',$employees_id)->first();

        if ($item_overtime != NULL) {
            return view('admin.pages.overtime.tampil_hapus_overtime',[
                'tanggal_lembur'    => $tanggal_lembur,
                'item_overtime'     => $item_overtime
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }  
    }

    public function form_approve_overtime()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.overtime.approve_overtime');
    }

    public function tampil_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_awal       = $request->input('tanggal_awal');
        $tanggal_akhir      = $request->input('tanggal_akhir');

        $item_overtimes     = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
                            ->where('acc_hrd',NULL)->get();
        if (!$item_overtimes->isEmpty()) {
            return view('admin.pages.overtime.tampil_approve_overtime',[
                'tanggal_awal'      => $tanggal_awal,
                'tanggal_akhir'     => $tanggal_akhir,
                'item_overtimes'    => $item_overtimes
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }   
    }

    public function proses_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_token     = $request->except('_token');
        $awal           = $request->input('tanggal_awal');
        $akhir          = $request->input('tanggal_akhir');

        // TimeStamp
        $waktu_acc_hrd      = Carbon::now()->toDateTimeString();
        // TimeStamp

        $overtimes  = Overtimes::whereBetween('tanggal_lembur', [$awal, $akhir])->update([
                                    'acc_hrd'       => auth()->user()->name,
                                    'waktu_acc_hrd' => $waktu_acc_hrd ]);

        if ($overtimes > 0) 
        {
        Alert::success('Success Approve Data Lembur', 'Oleh ' . auth()->user()->name);
        return redirect()->route('overtime.index');
        }
        else{
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }
    }

    public function form_cancel_approve_overtime()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.overtime.cancel_approve_overtime');
    }

    public function tampil_cancel_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_awal       = $request->input('tanggal_awal');
        $tanggal_akhir      = $request->input('tanggal_akhir');

        $item_overtimes     = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
                            ->whereNotNull('acc_hrd')->get();

        if (!$item_overtimes->isEmpty()) {
            return view('admin.pages.overtime.tampil_cancel_approve_overtime',[
                'tanggal_awal'      => $tanggal_awal,
                'tanggal_akhir'     => $tanggal_akhir,
                'item_overtimes'    => $item_overtimes
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }   
    }

    public function proses_cancel_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_token         = $request->except('_token');
        $awal   = $request->input('tanggal_awal');
        $akhir  = $request->input('tanggal_akhir');

        // TimeStamp
        $waktu_acc_hrd      = Carbon::now()->toDateTimeString();
        // TimeStamp

        $overtimes  = Overtimes::whereBetween('tanggal_lembur', [$awal, $akhir])->update([
                                    'acc_hrd'       => NULL,
                                    'waktu_acc_hrd' => NULL ]);

        if ($overtimes > 0) 
        {
        Alert::success('Success Cancel Approve Data Lembur', 'Oleh ' . auth()->user()->name);
        return redirect()->route('overtime.index');
        }
        else{
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.index');
        }
    }

    public function proses_edit_approve_overtime(OvertimeUpdateRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_token         = $request->except('_token');
        $id                 = $request->input('id');
        $employees_id       = $request->input('employees_id');
        $tanggal_lembur     = $request->input('tanggal_lembur');
        $jenis_lembur       = $request->input('jenis_lembur');
        $keterangan_lembur  = $request->input('keterangan_lembur');
        $jam_masuk          = $request->input('jam_masuk');
        $jam_istirahat      = $request->input('jam_istirahat');
        $jam_pulang         = $request->input('jam_pulang');
        $uang_makan_lembur  = $request->input('uang_makan_lembur');
        $jam_lembur         = $jam_pulang - $jam_istirahat - $jam_masuk;

        //Rumus Lembur
        if ($jenis_lembur == "Libur") {

            $jam_pertama = 0;

            if ($jam_lembur < 8) {
                $jam_kedua = $jam_lembur;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 8) {
                $jam_kedua = 8;
                $jam_ketiga = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 8) {

                $jam_kedua = 8;

                if ($jam_lembur - $jam_kedua > 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = $jam_lembur - $jam_kedua - $jam_ketiga;
                } elseif ($jam_lembur - $jam_kedua == 1) {
                    $jam_ketiga = 1;
                    $jam_keempat = 0;
                } else {
                    $jam_ketiga = $jam_lembur - $jam_kedua;
                    $jam_keempat = 0;
                }
            }
        } elseif ($jenis_lembur == "Biasa") {
            // $jam_pertama = 0;

            if ($jam_lembur < 1) {
                $jam_pertama = $jam_lembur;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur == 1) {
                $jam_pertama = 1;
                $jam_kedua   = 0;
                $jam_ketiga  = 0;
                $jam_keempat = 0;
            } elseif ($jam_lembur > 1) {

                $jam_pertama = 1;

                if ($jam_lembur < 9) {
                    $jam_kedua = $jam_lembur - $jam_pertama;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur == 9) {
                    $jam_kedua = 8;
                    $jam_ketiga = 0;
                    $jam_keempat = 0;
                } elseif ($jam_lembur > 9) {

                    $jam_kedua = 8;

                    if ($jam_lembur - $jam_kedua - $jam_pertama == 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = 0;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama > 1) {

                        $jam_ketiga = 1;
                        $jam_keempat = $jam_lembur - $jam_ketiga - $jam_kedua - $jam_pertama;
                    } elseif ($jam_lembur - $jam_kedua - $jam_pertama < 1) {

                        $jam_ketiga = $jam_lembur - $jam_kedua - $jam_pertama;
                        $jam_keempat = 0;
                    }
                }
            }
        } else {
            return redirect()->route('overtimes.index');
        }

        $jumlah_jam_pertama     = $jam_pertama * 1.5;
        $jumlah_jam_kedua       = $jam_kedua * 2;
        $jumlah_jam_ketiga      = $jam_ketiga * 3;
        $jumlah_jam_keempat     = $jam_keempat * 4;
        //Rumus Lembur

        $overtimes              = Overtimes::where('id', $id)->first();

        $overtimes->update([
            'jam_masuk'             => $jam_masuk,
            'jam_istirahat'         => $jam_istirahat,
            'jam_pulang'            => $jam_pulang,
            'keterangan_lembur'     => $keterangan_lembur,
            'tanggal_lembur'        => $tanggal_lembur,
            'jenis_lembur'          => $jenis_lembur,
            'jam_lembur'            => $jam_lembur,
            'jam_pertama'           => $jam_pertama,
            'jumlah_jam_pertama'    => $jumlah_jam_pertama,
            'jam_kedua'             => $jam_kedua,
            'jumlah_jam_kedua'      => $jumlah_jam_kedua,
            'jam_ketiga'            => $jam_ketiga,
            'jumlah_jam_ketiga'     => $jumlah_jam_ketiga,
            'jam_keempat'           => $jam_keempat,
            'jumlah_jam_keempat'    => $jumlah_jam_keempat,
            'uang_makan_lembur'     => $uang_makan_lembur,
            'edit_oleh'             => auth()->user()->name
        ]);

        Alert::success('Success Edit Data Overtime', 'Oleh ' . auth()->user()->name);
        return redirect()->route('overtime.index');
    }

    public function form_cetak_slip_overtime()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.overtime.slip_overtime',[
            'employees' => $employees
        ]);
    }

    public function cetak_slip_overtime(NamaTanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $awal               = $request->input('tanggal_awal');
        $akhir              = $request->input('tanggal_akhir');
        $bulanawal          = Carbon::parse($awal)->isoformat('M');
        $bulanakhir         = Carbon::parse($akhir)->isoformat('M');
        $tahunawal          = Carbon::parse($awal)->isoformat('YYYY');
        $tahunakhir         = Carbon::parse($akhir)->isoformat('YYYY');

        $itemEmployee       = Employees::with([
                                            'areas',
                                            'golongans',
                                            'positions',
                                            'divisions',
                                            'overtimes' => function ($query) use ($awal, $akhir) {
                                            $query->whereNotNull('acc_hrd')
                                                    ->whereBetween('tanggal_lembur', [$awal, $akhir]);
                                            },
                                            'rekap_salaries' => function ($query) use ($bulanawal, $tahunawal) {
                                            $query->whereMonth('periode_akhir', $bulanawal)
                                                    ->whereYear('periode_akhir', $tahunawal);
                                            }
                                            ])->find($employees_id);
        $items =     Overtimes::with([
                        'employees',
                    ])
                        ->where('acc_hrd', '<>', NULL)
                        ->where('employees_id', $employees_id)
                        ->where('deleted_at', NULL)
                        ->whereBetween('tanggal_lembur', [$awal, $akhir])
                        ->orderBy('tanggal_lembur')
                        ->get();
        
        if (!$itemEmployee || $items->isEmpty()) {
            Alert::error('Data Belum Tersedia Atau Belum Direkap Oleh HRD');
            return redirect()->route('overtime.index');
        } else 
        {
        $this->fpdf = new FPDF('P', 'cm', array(21, 28));
        $this->fpdf->setTopMargin(0.2);
        $this->fpdf->setLeftMargin(0.6);
        $this->fpdf->AddPage();
        $this->fpdf->SetAutoPageBreak(true);

        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(10, 1, "PT PRIMA KOMPONEN INDONESIA", 0, 0, 'L');
        $this->fpdf->Ln(0.4);
        $this->fpdf->SetFont('Arial', '', '9');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(10, 1, $itemEmployee->areas->area . " - " . $itemEmployee->divisions->penempatan . "", 0, 0, 'L');

        $this->fpdf->SetFont('Arial', 'B', '10');
        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(20, 1, "Bukti Tanda Terima Slip Lembur", 0, 0, 'C');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(20, 1, "Periode " . \Carbon\Carbon::parse($awal)->isoformat('D MMMM Y') . " s/d " . \Carbon\Carbon::parse($akhir)->isoformat('D MMMM Y') . "", 0, 0, 'C');

        $this->fpdf->Ln(0.6);

        $this->fpdf->SetFont('Arial', '', '8');
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(7, 0.5, "Nama     : " . $itemEmployee->nama_karyawan . "", 0, 0, 'L');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(0.1);
        $this->fpdf->Cell(7, 0.5, "Bagian   : " . $itemEmployee->positions->jabatan . " / " . $itemEmployee->divisions->penempatan . "", 0, 0, 'L');

        $this->fpdf->Ln(0.5);

        $this->fpdf->Cell(0.1);
        $this->fpdf->SetFont('Arial', '', '8');
        $this->fpdf->SetFillColor(255, 255, 255); // Warna sel tabel header
        $this->fpdf->Cell(1, 0.8, 'No', 1, 0, 'C', 1);
        $this->fpdf->Cell(2, 0.8, 'Hari', 1, 0, 'C', 1);
        $this->fpdf->Cell(2, 0.8, 'Tanggal', 1, 0, 'C', 1);

        $this->fpdf->Cell(4.5, 0.4, 'Jam Lembur ( Dlm Jam )', 1, 0, 'C', 1);
        $this->fpdf->Cell(1.5, 0.8, '', 1, 0, 'C', 1);

        $this->fpdf->Cell(4, 0.4, 'Perhitungan Jam Lembur', 1, 0, 'C', 1);
        $this->fpdf->Cell(2.2, 0.8, '', 1, 0, 'C', 1);
        $this->fpdf->Cell(2.2, 0.8, '', 1, 0, 'C', 1);

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(5.1);
        $this->fpdf->Cell(1.5, 0.4, 'Masuk', 1, 0, 'C', 1);
        $this->fpdf->Cell(1.5, 0.4, 'Istirahat', 1, 0, 'C', 1);
        $this->fpdf->Cell(1.5, 0.4, 'Pulang', 1, 0, 'C', 1);

        $this->fpdf->Cell(1.5);
        $this->fpdf->Cell(1, 0.4, '1,5', 1, 0, 'C', 1);
        $this->fpdf->Cell(1, 0.4, '2', 1, 0, 'C', 1);
        $this->fpdf->Cell(1, 0.4, '3', 1, 0, 'C', 1);
        $this->fpdf->Cell(1, 0.4, '4', 1, 0, 'C', 1);

        $this->fpdf->Ln(-0.4);
        $this->fpdf->Cell(9.6);
        $this->fpdf->Cell(1.5, 0.4, 'Jam', 0, 0, 'C');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(9.6);
        $this->fpdf->Cell(1.5, 0.4, 'Lembur', 0, 0, 'C');


        $this->fpdf->Ln(-0.4);
        $this->fpdf->Cell(15.4);
        $this->fpdf->Cell(1.5, 0.4, 'Uang Makan', 0, 0, 'C');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(15.4);
        $this->fpdf->Cell(1.5, 0.4, 'perhari ( Rp )', 0, 0, 'C');

        $this->fpdf->Ln(-0.4);
        $this->fpdf->Cell(17.6);
        $this->fpdf->Cell(1.5, 0.4, 'U. Transport', 0, 0, 'C');

        $this->fpdf->Ln(0.4);
        $this->fpdf->Cell(17.6);
        $this->fpdf->Cell(1.5, 0.4, 'perhari ( Rp )', 0, 0, 'C');

        $no = 1;
        $jumlahjampertama = 0;
        $jumlahjamkedua = 0;
        $jumlahjamketiga = 0;
        $jumlahjamkeempat = 0;
        $jumlahuangmakanlembur = 0;
        $total = 0;

        foreach ($items as $item) {

            $harilembur         = \Carbon\Carbon::parse($item->tanggal_lembur)->isoformat('dddd');
            $tanggallembur      = \Carbon\Carbon::parse($item->tanggal_lembur)->isoformat('DD-MM-Y');
            $tahunlembur        = \Carbon\Carbon::parse($awal)->isoformat('YYYY');

            $this->fpdf->Ln(0.4);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(1, 0.4, $no, 1, 0, 'C');
            $this->fpdf->Cell(2, 0.4, $harilembur, 1, 0, 'C');
            $this->fpdf->Cell(2, 0.4, $tanggallembur, 1, 0, 'C');

            $this->fpdf->Cell(1.5, 0.4, $item->jam_masuk, 1, 0, 'C');
            $this->fpdf->Cell(1.5, 0.4, $item->jam_istirahat, 1, 0, 'C');
            $this->fpdf->Cell(1.5, 0.4, $item->jam_pulang, 1, 0, 'C');
            $this->fpdf->Cell(1.5, 0.4, $item->jam_lembur, 1, 0, 'C');

            $this->fpdf->Cell(1, 0.4, $item->jam_pertama, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $item->jam_kedua, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $item->jam_ketiga, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $item->jam_keempat, 1, 0, 'C');

            $this->fpdf->Cell(2.2, 0.4, number_format($item->uang_makan_lembur), 1, 0, 'C');
            $this->fpdf->Cell(2.2, 0.4, ' - ', 1, 0, 'C');

            $no++;
            $jumlahjampertama += $item->jumlah_jam_pertama;
            $jumlahjamkedua += $item->jumlah_jam_kedua;
            $jumlahjamketiga += $item->jumlah_jam_ketiga;
            $jumlahjamkeempat += $item->jumlah_jam_keempat;
            $jumlahuangmakanlembur += $item->uang_makan_lembur;
        }

        $jumlahjamlembur        = $jumlahjampertama + $jumlahjamkedua + $jumlahjamketiga + $jumlahjamkeempat;
        $jumlahuanglembur       = $jumlahjamlembur *  $itemEmployee->rekap_salaries->first()->upah_lembur_perjam ?? 0;
        $jumlahuangditerima     = $jumlahuanglembur + $jumlahuangmakanlembur;

            $this->fpdf->Ln(0.4);
            $this->fpdf->Cell(9.4);
            $this->fpdf->Cell(1.7, 0.4, 'Jumlah Jam', 0, 0, 'L');

            $this->fpdf->Cell(1, 0.4, $jumlahjampertama, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $jumlahjamkedua, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $jumlahjamketiga, 1, 0, 'C');
            $this->fpdf->Cell(1, 0.4, $jumlahjamkeempat, 1, 0, 'C');
            $this->fpdf->Cell(2.2, 0.4, $jumlahuangmakanlembur, 1, 0, 'C');
            $this->fpdf->Cell(2.2, 0.4, " - ", 1, 0, 'C');


            $this->fpdf->Ln(0.2);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Jumlah Jam Lembur', 0, 0, 'L');

            $this->fpdf->Cell(1.5);
            $this->fpdf->Cell(3, 0.2, $jumlahjamlembur, 0, 0, 'C');

            $this->fpdf->Ln(0.3);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Upah Lembur Perjam', 0, 0, 'L');
            $this->fpdf->Cell(1.5, 0.2, 'Rp.', 0, 0, 'R');
            $this->fpdf->Cell(3, 0.2, number_format($itemEmployee->rekap_salaries->first()->upah_lembur_perjam ?? 0), 0, 0, 'R');

            $this->fpdf->SetFont('Arial', 'B', '7');
            $this->fpdf->Cell(1.5);
            $this->fpdf->Cell(5, 0.2, 'Note : 0.5 Dlm angka = 30 menit dlm jam ( Jam Istirahat Lembur )', 0, 0, 'L');

            $this->fpdf->Ln(0.3);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(9.5, 0, '', 1, 0, 'L', 1);

            $this->fpdf->SetFont('Arial', '', '8');
            $this->fpdf->Ln(0.1);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Jumlah Uang Lembur', 0, 0, 'L');
            $this->fpdf->Cell(1.5, 0.2, 'Rp.', 0, 0, 'R');
            $this->fpdf->Cell(3, 0.2, number_format($jumlahuanglembur), 0, 0, 'R');

            $this->fpdf->Ln(0.3);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Jumlah Uang Makan Lembur', 0, 0, 'L');
            $this->fpdf->Cell(1.5, 0.2, 'Rp.', 0, 0, 'R');
            $this->fpdf->Cell(3, 0.2, number_format($jumlahuangmakanlembur), 0, 0, 'R');


            $this->fpdf->Ln(0.3);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Jumlah Uang Transport Lembur', 0, 0, 'L');
            $this->fpdf->Cell(1.5, 0.2, 'Rp.', 0, 0, 'R');
            $this->fpdf->Cell(3, 0.2, " - ", 0, 0, 'R');


            $this->fpdf->Ln(0.3);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(9.5, 0, '', 1, 0, 'L', 1);

            $this->fpdf->Ln(0.1);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Jumlah Uang Yang Diterima', 0, 0, 'L');
            $this->fpdf->Cell(1.5, 0.2, 'Rp.', 0, 0, 'R');
            $this->fpdf->Cell(3, 0.2, number_format($jumlahuangditerima), 0, 0, 'R');


            $this->fpdf->Ln(1);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, 'Mengetahui', 0, 0, 'L');

            $this->fpdf->Cell(6, 0.2, 'Tangerang Selatan, ............................,' . $tahunlembur, 0, 0, 'L');

            $this->fpdf->Cell(3);
            $this->fpdf->Cell(5.4, 0.2, 'Yang Menerima', 0, 0, 'L');


            $this->fpdf->Ln(2);
            $this->fpdf->Cell(0.1);
            $this->fpdf->Cell(5, 0.2, '(Achmad Firmansyah)', 0, 0, 'L');


            $this->fpdf->Cell(9);
            $this->fpdf->Cell(5.4, 0.2, '(' . $itemEmployee->nama_karyawan . ')', 0, 0, 'L');


            $this->fpdf->Ln(60);

            $this->fpdf->Output();
            exit;
            
        }
    }

    public function form_cetak_rekap_overtime()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.overtime.rekap_overtime');
    }

    public function cetak_rekap_overtime(StatusPenempatanTanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data           = $request->except('_token');
        $status_kerja   = $request->input('status_kerja');
        $penempatan     = $request->input('penempatan');
        $awal           = $request->input('tanggal_awal');
        $akhir          = $request->input('tanggal_akhir');

        $divisi = match ($penempatan) {
            'Produksi'      => [11],
            'Office'        => [1, 2, 3, 4, 7, 8, 9],
            'PPC'           => [5, 6, 12, 13, 14, 15, 16],
            'Quality'       => [10],
            'PDC Daihatsu'  => [19, 20, 21],
            default         => abort(403),
        };

        $item_overtimes = Overtimes::join('employees', 'employees.id', '=', 'overtimes.employees_id')
                                    ->select(
                                        'overtimes.employees_id',
                                        'overtimes.nik_karyawan',
                                        'employees.nama_karyawan',
                                        'employees.status_kerja'
                                    )
                                    ->selectRaw('
                                        SUM(overtimes.jumlah_jam_pertama) as jumlah_jam_pertama,
                                        SUM(overtimes.jumlah_jam_kedua) as jumlah_jam_kedua,
                                        SUM(overtimes.jumlah_jam_ketiga) as jumlah_jam_ketiga,
                                        SUM(overtimes.jumlah_jam_keempat) as jumlah_jam_keempat,
                                        SUM(overtimes.uang_makan_lembur) as uang_makan_lembur
                                    ')
                                    ->whereIn('employees.divisions_id', $divisi)
                                    ->where('employees.status_kerja', $status_kerja)
                                    ->whereBetween('overtimes.tanggal_lembur', [$awal, $akhir])
                                    ->whereNotNull('overtimes.acc_hrd')
                                    // Catatan: Hapus manual whereNull('deleted_at') jika model sudah pakai trait SoftDeletes bawaan Laravel
                                    ->groupBy(
                                        'overtimes.employees_id',
                                        'overtimes.nik_karyawan',
                                        'employees.nama_karyawan',
                                        'employees.status_kerja'
                                    )
                                    ->orderBy('employees.nama_karyawan')
                                    ->get();

        if ($item_overtimes->isEmpty()) {
            Alert::error('Data Overtime Tidak Ditemukan Atau Belum Di Rekap HRD');
            return redirect()->route('overtime.index');
        }

        return view('admin.pages.overtime.tampil_rekap_overtime', [
            'status_kerja'      => $status_kerja,
            'penempatan'        => $penempatan,
            'awal'              => $awal,
            'akhir'             => $akhir,
            'divisi'            => $divisi,
            'item_overtimes'    => $item_overtimes
        ]);
    }

    public function exportPDF_rekap_overtime(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $penempatan   = $request->input('penempatan');
        $status_kerja = $request->input('status_kerja');
        $awal         = $request->input('awal');
        $akhir        = $request->input('akhir');

        $tahunlembur  = \Carbon\Carbon::parse($awal)->isoformat('YYYY');
        
        // Gunakan bulan dan tahun dari akhir periode untuk penentuan upah rekap salary
        $bulanakhir   = \Carbon\Carbon::parse($akhir)->isoformat('MM');
        $tahunakhir   = \Carbon\Carbon::parse($akhir)->isoformat('YYYY');

        $divisi = match ($penempatan) {
            'Produksi'     => [11],
            'Office'       => [1, 2, 3, 4, 7, 8, 9],
            'PPC'          => [5, 6, 12, 13, 14, 15, 16],
            'Quality'      => [10],
            'PDC Daihatsu' => [19, 20, 21],
            default        => abort(403),
        };

        // 1. QUERY BERBASIS EMPLOYEES (Lebih aman untuk grouping per karyawan & Eager Loading)
        $employees = Employees::with([
                        'divisions',
                        'rekap_salaries' => function ($query) use ($bulanakhir, $tahunakhir) {
                            // Mengunci upah berdasarkan periode akhir penarikan/bulan berjalan
                            $query->whereMonth('periode_akhir', $bulanakhir)
                                ->whereYear('periode_akhir', $tahunakhir);
                        }
                    ])
                    ->withSum(['overtimes as total_jam_pertama' => function($q) use ($awal, $akhir) {
                        $q->whereBetween('tanggal_lembur', [$awal, $akhir])->whereNotNull('acc_hrd');
                    }], 'jumlah_jam_pertama')
                    ->withSum(['overtimes as total_jam_kedua' => function($q) use ($awal, $akhir) {
                        $q->whereBetween('tanggal_lembur', [$awal, $akhir])->whereNotNull('acc_hrd');
                    }], 'jumlah_jam_kedua')
                    ->withSum(['overtimes as total_jam_ketiga' => function($q) use ($awal, $akhir) {
                        $q->whereBetween('tanggal_lembur', [$awal, $akhir])->whereNotNull('acc_hrd');
                    }], 'jumlah_jam_ketiga')
                    ->withSum(['overtimes as total_jam_keempat' => function($q) use ($awal, $akhir) {
                        $q->whereBetween('tanggal_lembur', [$awal, $akhir])->whereNotNull('acc_hrd');
                    }], 'jumlah_jam_keempat')
                    ->withSum(['overtimes as total_uang_makan' => function($q) use ($awal, $akhir) {
                        $q->whereBetween('tanggal_lembur', [$awal, $akhir])->whereNotNull('acc_hrd');
                    }], 'uang_makan_lembur')
                    ->whereIn('divisions_id', $divisi)
                    ->where('status_kerja', $status_kerja)
                    ->orderBy('nama_karyawan')
                    ->get();

        // Filter karyawan yang benar-benar memiliki jam lembur di periode tersebut
        $item_overtimes = $employees->filter(function($employee) {
            return ($employee->total_jam_pertama + $employee->total_jam_kedua + $employee->total_jam_ketiga + $employee->total_jam_keempat) > 0;
        });

        if ($item_overtimes->isEmpty()) {
            return redirect()->back()->with('error', 'Data rekap lembur tidak ditemukan.');
        }

        // 2. INISIALISASI FPDF
        $this->fpdf = new FPDF('P', 'cm', [21, 28]);
        $this->fpdf->setTopMargin(0.5);
        $this->fpdf->setLeftMargin(0.4); // Sedikit dilonggarkan agar tidak terlalu mepet potong printer
        $this->fpdf->SetAutoPageBreak(true, 1.5);
        $this->fpdf->AddPage();

        // Render Header Dokumen
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(10, 0.5, "PT PRIMA KOMPONEN INDONESIA", 0, 1, 'L');
        
        $this->fpdf->SetFont('Arial', 'B', '10');
        $this->fpdf->Cell(20, 0.6, "Daftar Rekap Lembur Karyawan", 0, 1, 'C');
        $this->fpdf->Cell(20, 0.6, "Periode " . \Carbon\Carbon::parse($awal)->isoformat('D MMMM Y') . " s/d " . \Carbon\Carbon::parse($akhir)->isoformat('D MMMM Y'), 0, 1, 'C');
        $this->fpdf->Ln(0.4);

        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(7, 0.5, "Penempatan: " . $penempatan, 0, 1, 'L');

        // Render Table Header
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(0.6, 0.9, 'No', 1, 0, 'C');
        $this->fpdf->Cell(3.5, 0.9, 'Nama Karyawan', 1, 0, 'C');
        $this->fpdf->Cell(3.0, 0.9, 'Penempatan', 1, 0, 'C');
        $this->fpdf->Cell(1.5, 0.9, 'No Rek', 1, 0, 'C');
        $this->fpdf->Cell(1.5, 0.9, 'Tot Jam', 1, 0, 'C');
        $this->fpdf->Cell(1.5, 0.9, 'Upah/Jam', 1, 0, 'C');
        $this->fpdf->Cell(3.0, 0.9, 'Jumlah Uang Lembur', 1, 0, 'C');
        $this->fpdf->Cell(1.5, 0.9, 'Uang Mkn', 1, 0, 'C');
        $this->fpdf->Cell(2.0, 0.9, 'Gross', 1, 0, 'C');
        $this->fpdf->Cell(2.0, 0.9, 'Diterima', 1, 1, 'C');

        // 3. LOOP DATA
        $no = 1;
        $totaljumlahuanglembur   = 0;
        $totaluangmakanlembur    = 0;
        $totaljumlahuangditerima = 0;
        $totalhasiluangditerima  = 0;

        foreach ($item_overtimes as $employee) {
            $jumlahjam = $employee->total_jam_pertama + $employee->total_jam_kedua + $employee->total_jam_ketiga + $employee->total_jam_keempat;
            $uangmakanlembur = $employee->total_uang_makan ?? 0;

            // Penentuan lokasi penempatan
            $lokasiPenempatan = match ($employee->divisions?->id) {
                19      => "Sunter",
                20      => "Cibitung",
                21      => "Karawang Timur",
                default => $employee->divisions?->penempatan ?? '-',
            };

            $rekapSalary      = $employee->rekap_salaries->first();
            $upahlemburperjam = $rekapSalary?->upah_lembur_perjam ?? 0;
            
            $jumlahuanglembur   = $upahlemburperjam * $jumlahjam;
            $jumlahuangditerima = $jumlahuanglembur + $uangmakanlembur;

            // Logika Pembulatan Ratusan Terdekat
            $pembulatan2Digit = ceil($jumlahuangditerima) % 100;
            $total_jumlahuangditerima = match (true) {
                $pembulatan2Digit > 50 && $pembulatan2Digit < 100 => round($jumlahuangditerima, -2),
                $pembulatan2Digit < 50 && $pembulatan2Digit > 0   => round($jumlahuangditerima, -2) + 100,
                default                                           => round($jumlahuangditerima, -2),
            };

            // Cetak Baris Tabel
            $this->fpdf->SetFont('Arial', '', '7');
            $this->fpdf->Cell(0.6, 0.5, $no, 1, 0, 'C');
            $this->fpdf->Cell(3.5, 0.5, $employee->nama_karyawan, 1, 0, 'L');
            $this->fpdf->Cell(3.0, 0.5, $lokasiPenempatan, 1, 0, 'L');
            $this->fpdf->Cell(1.5, 0.5, $employee->nomor_rekening ?? '-', 1, 0, 'C');
            $this->fpdf->Cell(1.5, 0.5, $jumlahjam, 1, 0, 'C');
            $this->fpdf->Cell(1.5, 0.5, number_format($upahlemburperjam), 1, 0, 'R');
            $this->fpdf->Cell(3.0, 0.5, number_format($jumlahuanglembur), 1, 0, 'R');
            $this->fpdf->Cell(1.5, 0.5, number_format($uangmakanlembur), 1, 0, 'R');
            $this->fpdf->Cell(2.0, 0.5, number_format($jumlahuangditerima), 1, 0, 'R');
            $this->fpdf->Cell(2.0, 0.5, number_format($total_jumlahuangditerima), 1, 1, 'R');

            $no++;
            $totaljumlahuanglembur   += $jumlahuanglembur;
            $totaluangmakanlembur    += $uangmakanlembur;
            $totaljumlahuangditerima += $jumlahuangditerima;
            $totalhasiluangditerima  += $total_jumlahuangditerima;
        }

        // Baris Total Grand Total
        $this->fpdf->SetFont('Arial', 'B', '7');
        $this->fpdf->Cell(11.6, 0.5, 'TOTAL', 1, 0, 'R');
        $this->fpdf->Cell(3.0, 0.5, number_format($totaljumlahuanglembur), 1, 0, 'R');
        $this->fpdf->Cell(1.5, 0.5, number_format($totaluangmakanlembur), 1, 0, 'R');
        $this->fpdf->Cell(2.0, 0.5, number_format($totaljumlahuangditerima), 1, 0, 'R');
        $this->fpdf->Cell(2.0, 0.5, number_format($totalhasiluangditerima), 1, 1, 'R');

        // 4. BAGIAN TANDA TANGAN (FOOTER)
        $this->fpdf->Ln(0.5);
        $this->fpdf->SetFont('Arial', '', '8');
        $this->fpdf->Cell(5, 0.4, 'Mengetahui,', 0, 0, 'L');
        $this->fpdf->Cell(10, 0.4, 'Tangerang, ........................................, ' . $tahunlembur, 0, 0, 'L');
        $this->fpdf->Cell(5, 0.4, 'Diperiksa,', 0, 1, 'L');

        $this->fpdf->Ln(1.2); 
        
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Cell(5, 0.4, 'Veronica', 0, 0, 'L');
        $this->fpdf->Cell(10, 0.4, '', 0, 0, 'L');
        $this->fpdf->Cell(5, 0.4, 'Achmad Firmansyah', 0, 1, 'L');

        $this->fpdf->SetFont('Arial', '', '7');
        $this->fpdf->Cell(5, 0.3, '(Wakil Direktur Accounting, Finance, IT)', 0, 0, 'L');
        $this->fpdf->Cell(10, 0.3, '', 0, 0, 'L');
        $this->fpdf->Cell(5, 0.3, '(Manager HRD-GA)', 0, 1, 'L');

        $this->fpdf->Output('I', 'Rekap_Lembur_' . $penempatan . '.pdf');
        exit;
    }

    public function exportExcell_rekap_overtime(Request $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $penempatan   = $request->input('penempatan');
        $status_kerja = $request->input('status_kerja');
        $awal         = $request->input('awal');
        $akhir        = $request->input('akhir');

        // Menggunakan Carbon dengan format standard angka (01-12)
        $bulanawal  = \Carbon\Carbon::parse($awal)->format('m');
        $bulanakhir = \Carbon\Carbon::parse($akhir)->format('m');
        $tahunawal  = \Carbon\Carbon::parse($awal)->format('Y');
        $tahunakhir = \Carbon\Carbon::parse($akhir)->format('Y');

        $divisi = match ($penempatan) {
            'Produksi'      => [11],
            'Office'        => [1, 2, 3, 4, 7, 8, 9],
            'PPC'           => [5, 6, 12, 13, 14, 15, 16],
            'Quality'       => [10],
            'PDC Daihatsu'  => [19, 20, 21],
            default         => abort(404, 'Penempatan tidak valid'),
        };

        // Ambil data dasar overtimes dengan query yang sama persis seperti di view
        $item_overtimes = Overtimes::join('employees', 'employees.id', '=', 'overtimes.employees_id')
                            ->with([
                                'employees.golongans',
                                'employees.areas',
                                'employees.divisions',
                                'employees.positions',
                                // Samakan logic whereMonth & whereYear dengan yang ada di view Anda
                                'employees.rekap_salaries' => function ($query) use ($bulanawal, $bulanakhir, $tahunawal, $tahunakhir) {
                                    $query->whereMonth('periode_awal', $bulanawal)
                                        ->whereMonth('periode_akhir', $bulanakhir)
                                        ->whereYear('periode_awal', $tahunawal)
                                        ->whereYear('periode_akhir', $tahunakhir);
                                }
                            ])
                            ->select(
                                'overtimes.employees_id',
                                'overtimes.nik_karyawan'
                            )
                            ->selectRaw('
                                SUM(overtimes.jumlah_jam_pertama) as jam_1,
                                SUM(overtimes.jumlah_jam_kedua) as jam_2,
                                SUM(overtimes.jumlah_jam_ketiga) as jam_3,
                                SUM(overtimes.jumlah_jam_keempat) as jam_4,
                                SUM(overtimes.uang_makan_lembur) as total_uang_makan
                            ')
                            ->whereIn('employees.divisions_id', $divisi)
                            ->where('employees.status_kerja', $status_kerja)
                            ->whereBetween('overtimes.tanggal_lembur', [$awal, $akhir])
                            ->whereNotNull('overtimes.acc_hrd')
                            ->groupBy('overtimes.employees_id', 'overtimes.nik_karyawan')
                            ->orderBy('employees.nama_karyawan')
                            ->get();
        
        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'A1' => 'No', 'B1' => 'NIK Karyawan', 'C1' => 'Nama Karyawan', 'D1' => 'Golongan',
            'E1' => 'Area', 'F1' => 'Jabatan', 'G1' => 'Penempatan', 'H1' => 'Nomor Rekening',
            'I1' => 'Jam Lembur', 'J1' => 'Upah Lembur Perjam', 'K1' => 'Jumlah Uang Lembur',
            'L1' => 'Uang Makan Lembur', 'M1' => 'Jumlah Uang Diterima', 'N1' => 'Hasil Uang Lembur'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $row = 2;
        $no = 1;

        foreach ($item_overtimes as $item) 
        {
            // Pastikan memanggil relasi ke employees dengan benar
            $employee = $item->employees;
            if (!$employee) continue; 

            $jumlahjam = $item->jam_1 + $item->jam_2 + $item->jam_3 + $item->jam_4;
            $uangmakanlembur = $item->total_uang_makan;

            $penempatan_nama = match ($employee->divisions?->id) {
                19 => "Sunter",
                20 => "Cibitung",
                21 => "Karawang Timur",
                default => $employee->divisions?->penempatan ?? '-',
            };

            // Ambal data rekap salary pertama yang sudah di-filter di atas
            $rekapSalary = $employee->rekap_salaries->first();
            $upahlemburperjam = $rekapSalary?->upah_lembur_perjam ?? 0;
            
            $jumlahuanglembur = $upahlemburperjam * $jumlahjam;
            $jumlahuangditerima = $jumlahuanglembur + $uangmakanlembur;

            $pembulatan = ceil($jumlahuangditerima);
            $dua_angka_terakhir = $pembulatan % 100;

            if ($dua_angka_terakhir > 50 && $dua_angka_terakhir < 100) {
                $total_jumlahuangditerima = round($pembulatan, -2);
            } elseif ($dua_angka_terakhir < 50 && $dua_angka_terakhir > 0) {
                $total_jumlahuangditerima = round($pembulatan, -2) + 100;
            } else {
                $total_jumlahuangditerima = round($pembulatan, -2);
            }

            // Isi Cell Data
            $sheet->getRowDimension($row)->setRowHeight(25);
            $sheet->setCellValue('A'.$row, $no);
            $sheet->setCellValue('B'.$row, "'".$employee->nik_karyawan);
            $sheet->setCellValue('C'.$row, $employee->nama_karyawan); // dihilangkan kutip satu di depan nama jika tidak diperlukan teks murni
            $sheet->setCellValue('D'.$row, $employee->golongans?->golongan ?? '-');
            $sheet->setCellValue('E'.$row, $employee->areas?->area ?? '-');
            $sheet->setCellValue('F'.$row, $employee->positions?->jabatan ?? '-');
            $sheet->setCellValue('G'.$row, $penempatan_nama);
            $sheet->setCellValue('H'.$row, "'".$employee->nomor_rekening);
            $sheet->setCellValue('I'.$row, $jumlahjam);
            $sheet->setCellValue('J'.$row, $upahlemburperjam);
            $sheet->setCellValue('K'.$row, $jumlahuanglembur);
            $sheet->setCellValue('L'.$row, $uangmakanlembur);
            $sheet->setCellValue('M'.$row, $jumlahuangditerima);
            $sheet->setCellValue('N'.$row, $total_jumlahuangditerima);
            
            $row++;
            $no++;
        }

        // Border & Alignment Styling
        $lastRow = $row - 1;
        if($lastRow >= 2) {
            $sheet->getStyle("A1:N{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
            $sheet->getStyle("A1:N{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:N{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Auto width untuk seluruh kolom yang terisi
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Selesaikan proses download Excel (tambahkan return penutup kode Excel Anda di bawah ini)
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Overtime_'.time().'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function notif_overtime_belum_rekap()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $today = Carbon::today();
        $item_overtimes     = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->whereNull('acc_hrd')->get();

    
        return view('admin.pages.overtime.tampil_notif_overtime',[
            'item_overtimes'        => $item_overtimes
        ]);
         
    }

    public function upah_lembur_perjam()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $item_overtimes     = HistorySalaries::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->whereHas('employees', function($query) {
                                $query->whereIn('golongans_id', [2, 3]);
                            })
                            ->get();
    
        return view('admin.pages.overtime.tampil_upah_lembur_perjam',[
            'item_overtimes'        => $item_overtimes
        ]);
    }

    public function proses_lembur_perjam($id)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $historySalary      = HistorySalaries::findOrFail($id);
        $item               = HistorySalaries::with(['employees'])->where('employees_id',$id)->first();

        // dd($item->employees->nama_karyawan);
       
        return view('admin.pages.overtime.tampil_edit_lembur_perjam',[
                    'item'          => $item
        ]);
    }

    public function update_upah_lembur_perjam(UpahLemburPerjamRequest $request, string $id)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data               = $request->except('_token');
        $item               = HistorySalaries::findOrFail($id);
        $item->update([
            'upah_lembur_perjam'    => $request->upah_lembur_perjam,
            'edit_oleh'             => auth()->user()->name
        ]);

        Alert::info('Success Edit Upah Lembur Perjam','Oleh '.auth()->user()->name);
        return redirect()->route('overtime.index');
    }
}

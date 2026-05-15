<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OvertimeRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\NamaKaryawanTanggalRequest;
use App\Http\Requests\Admin\OvertimeUpdateRequest;
use App\Models\Admin\Overtimes;
use App\Models\Admin\Employees;
use App\Models\Admin\Companies;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use App\Models\Admin\Areas;
use App\Models\Admin\RekapSalaries;
use App\Models\Admin\HistorySalaries;
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.overtime.index');
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

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.overtime.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OvertimeRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        Alert::success('Success Input Data Overtime','Oleh '.auth()->user()->name);
        return redirect()->route('overtime.index');
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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

        if ($overtimes > 0) 
        {
            Alert::success('Success Edit Data Overtime', 'Oleh ' . auth()->user()->name);
            return redirect()->route('overtime.index');
        }
        else{
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.overtime.lihat_overtime');
    }

    public function tampil_overtime(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
                            ])->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])->get();
        if (!$item_overtimes->isEmpty()) {
            return view('admin.pages.overtime.tampil_overtime',[
                'tanggal_awal'      => $tanggal_awal,
                'tanggal_akhir'     => $tanggal_akhir,
                'item_overtimes'    => $item_overtimes
            ]);
        } else {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('overtime.lihat_overtime');
        }   
    }

    public function export_excell_overtime(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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

        $tanggal_awal   = Carbon::parse($request->tanggal_awal)->format('Y-m-d');
        $tanggal_akhir  = Carbon::parse($request->tanggal_akhir)->format('Y-m-d');

        $item_overtimes = Overtimes::with([
                                            'employees',
                                            'employees.areas',
                                            'employees.divisions',
                                            'employees.positions',
                                            'employees.golongans'
                                            ])
                                            ->when($request->tanggal_awal && $request->tanggal_akhir, function ($query) use ($request) 
                                            {
                                                $query->whereBetween('tanggal_lembur', [
                                                $request->tanggal_awal,
                                                $request->tanggal_akhir
                                                ]);
                                            })->get();
        
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.overtime.edit_overtime',[
            'employees' => $employees
        ]);
    }

    public function tampil_edit_overtime(NamaKaryawanTanggalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_lembur     = $request->input('tanggal');
        $item_overtime      = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('tanggal_lembur',$tanggal_lembur)->first();

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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.overtime.hapus_overtime',[
            'employees' => $employees
        ]);
    }
    
    public function tampil_hapus_overtime(NamaKaryawanTanggalRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data               = $request->except('_token');
        $tanggal_lembur     = $request->input('tanggal');
        $item_overtime      = Overtimes::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            'employees.areas'
                            ])->where('tanggal_lembur',$tanggal_lembur)->first();

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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.overtime.approve_overtime');
    }

    public function tampil_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data_token         = $request->except('_token');
        $awal   = $request->input('tanggal_awal');
        $akhir  = $request->input('tanggal_akhir');

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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.overtime.cancel_approve_overtime');
    }

    public function tampil_cancel_approve_overtime(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
}

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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $attendances = Attendances::whereYear(
        'tanggal_absen',now()->year)->get();
        
        //Sakit
        $item_sakit_januari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 1)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_februari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 2)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_maret = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 3)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_april = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 4)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_mei = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 5)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_juni = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 6)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_juli = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 7)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_agustus = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 8)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_september = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 9)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_oktober = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 10)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_november = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 11)->where('keterangan_absen',"Sakit")->count();
        $item_sakit_desember = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 12)->where('keterangan_absen',"Sakit")->count();
        //Sakit

        //Ijin
        $item_ijin_januari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 1)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_februari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 2)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_maret = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 3)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_april = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 4)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_mei = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 5)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_juni = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 6)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_juli = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 7)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_agustus = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 8)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_september = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 9)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_oktober = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 10)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_november = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 11)->where('keterangan_absen',"Ijin")->count();
        $item_ijin_desember = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 12)->where('keterangan_absen',"Ijin")->count();
        //Ijin

        //Alpa
        $item_alpa_januari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 1)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_februari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 2)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_maret = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 3)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_april = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 4)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_mei = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 5)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_juni = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 6)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_juli = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 7)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_agustus = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 8)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_september = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 9)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_oktober = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 10)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_november = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 11)->where('keterangan_absen',"Alpa")->count();
        $item_alpa_desember = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 12)->where('keterangan_absen',"Alpa")->count();
        //Alpa

        //Cuti Tahunan
        $item_cuti_tahunan_januari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 1)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_februari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 2)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_maret = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 3)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_april = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 4)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_mei = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 5)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_juni = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 6)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_juli = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 7)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_agustus = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 8)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_september = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 9)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_oktober = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 10)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_november = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 11)->where('keterangan_absen',"Cuti Tahunan")->count();
        $item_cuti_tahunan_desember = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 12)->where('keterangan_absen',"Cuti Tahunan")->count();
        //Cuti Tahunan

        //OFF
        $item_off_januari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 1)->where('keterangan_absen',"OFF")->count();
        $item_off_februari = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 2)->where('keterangan_absen',"OFF")->count();
        $item_off_maret = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 3)->where('keterangan_absen',"OFF")->count();
        $item_off_april = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 4)->where('keterangan_absen',"OFF")->count();
        $item_off_mei = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 5)->where('keterangan_absen',"OFF")->count();
        $item_off_juni = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 6)->where('keterangan_absen',"OFF")->count();
        $item_off_juli = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 7)->where('keterangan_absen',"OFF")->count();
        $item_off_agustus = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 8)->where('keterangan_absen',"OFF")->count();
        $item_off_september = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 9)->where('keterangan_absen',"OFF")->count();
        $item_off_oktober = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 10)->where('keterangan_absen',"OFF")->count();
        $item_off_november = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 11)->where('keterangan_absen',"OFF")->count();
        $item_off_desember = Attendances::whereYear('tanggal_absen',now()->year)->whereMonth('tanggal_absen', 12)->where('keterangan_absen',"OFF")->count();
        //OFF

        return view('admin.pages.attendance.index',[
            'attendances' => $attendances,
            'item_sakit_januari' => $item_sakit_januari,
            'item_sakit_februari' => $item_sakit_februari,
            'item_sakit_maret' => $item_sakit_maret,
            'item_sakit_april' => $item_sakit_april,
            'item_sakit_mei' => $item_sakit_mei,
            'item_sakit_juni' => $item_sakit_juni,
            'item_sakit_juli' => $item_sakit_juli,
            'item_sakit_agustus' => $item_sakit_agustus,
            'item_sakit_september' => $item_sakit_september,
            'item_sakit_oktober' => $item_sakit_oktober,
            'item_sakit_november' => $item_sakit_november,
            'item_sakit_desember' => $item_sakit_desember,
            'item_ijin_januari' => $item_ijin_januari,
            'item_ijin_februari' => $item_ijin_februari,
            'item_ijin_maret' => $item_ijin_maret,
            'item_ijin_april' => $item_ijin_april,
            'item_ijin_mei' => $item_ijin_mei,
            'item_ijin_juni' => $item_ijin_juni,
            'item_ijin_juli' => $item_ijin_juli,
            'item_ijin_agustus' => $item_ijin_agustus,
            'item_ijin_september' => $item_ijin_september,
            'item_ijin_oktober' => $item_ijin_oktober,
            'item_ijin_november' => $item_ijin_november,
            'item_ijin_desember' => $item_ijin_desember,
            'item_alpa_januari' => $item_alpa_januari,
            'item_alpa_februari' => $item_alpa_februari,
            'item_alpa_maret' => $item_alpa_maret,
            'item_alpa_april' => $item_alpa_april,
            'item_alpa_mei' => $item_alpa_mei,
            'item_alpa_juni' => $item_alpa_juni,
            'item_alpa_juli' => $item_alpa_juli,
            'item_alpa_agustus' => $item_alpa_agustus,
            'item_alpa_september' => $item_alpa_september,
            'item_alpa_oktober' => $item_alpa_oktober,
            'item_alpa_november' => $item_alpa_november,
            'item_alpa_desember' => $item_alpa_desember,
            'item_cuti_tahunan_januari' => $item_cuti_tahunan_januari,
            'item_cuti_tahunan_februari' => $item_cuti_tahunan_februari,
            'item_cuti_tahunan_maret' => $item_cuti_tahunan_maret,
            'item_cuti_tahunan_april' => $item_cuti_tahunan_april,
            'item_cuti_tahunan_mei' => $item_cuti_tahunan_mei,
            'item_cuti_tahunan_juni' => $item_cuti_tahunan_juni,
            'item_cuti_tahunan_juli' => $item_cuti_tahunan_juli,
            'item_cuti_tahunan_agustus' => $item_cuti_tahunan_agustus,
            'item_cuti_tahunan_september' => $item_cuti_tahunan_september,
            'item_cuti_tahunan_oktober' => $item_cuti_tahunan_oktober,
            'item_cuti_tahunan_november' => $item_cuti_tahunan_november,
            'item_cuti_tahunan_desember' => $item_cuti_tahunan_desember,
            'item_off_januari' => $item_off_januari,
            'item_off_februari' => $item_off_februari,
            'item_off_maret' => $item_off_maret,
            'item_off_april' => $item_off_april,
            'item_off_mei' => $item_off_mei,
            'item_off_juni' => $item_off_juni,
            'item_off_juli' => $item_off_juli,
            'item_off_agustus' => $item_off_agustus,
            'item_off_september' => $item_off_september,
            'item_off_oktober' => $item_off_oktober,
            'item_off_november' => $item_off_november,
            'item_off_desember' => $item_off_desember
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
        
        $employees      = Employees::all();
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data_attendance    = $request->except('_token');
        $employee           = Employees::where('id',$request->input('employees_id'))->first();
        Attendances::create([
            'employees_id'              => $request->input('employees_id'),
            'nik_karyawan'              => $employee->nik_karyawan,
            'tanggal_absen'             => $request->input('tanggal_absen'),
            'lama_absen'                => 1,
            'keterangan_absen'          => $request->input('keterangan_absen'),
            'keterangan_cuti_khusus'    => $request->input('keterangan_cuti_khusus'),
            'input_oleh'                => Auth::user()->name
            ]);
        Alert::success('Success Input Data Absensi','Oleh '.auth()->user()->name);
        return redirect()->route('attendance.index');
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
    public function update(Request $request, $id)
    {
        
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::all();
        return view('admin.pages.attendance.edit',[
            'employees' => $employees
        ]);
    }

    public function tampil_form_edit(AttendanceUpdateRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        $employees      = Employees::all();
        return view('admin.pages.attendance.hapus',[
            'employees' => $employees
        ]);
    }

    public function tampil_form_hapus(Request $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
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
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        return view('admin.pages.attendance.tampil_absen');
    }

    public function tampil_absen(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        $data = $request->except('_token');
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $item_attendances = Attendances::with([
                            'employees',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans',
                            ])->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir])->get();
        if (!$item_attendances->isEmpty()) {
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

        $item_attendances = Attendances::with([
                            'employees',
                            'employees.areas',
                            'employees.divisions',
                            'employees.positions',
                            'employees.golongans'
                            ])
                            ->when($request->tanggal_awal && $request->tanggal_akhir, function ($query) use ($request) 
                            {
                                $query->whereBetween('tanggal_absen', [
                                $request->tanggal_awal,
                                $request->tanggal_akhir
                                ]);
                            })->get();
        
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
}

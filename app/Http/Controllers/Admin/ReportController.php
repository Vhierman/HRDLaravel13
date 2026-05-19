<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\NamaTanggalAwalAkhirRequest;
use App\Models\Admin\Attendances;
use App\Models\Admin\Employees;
use App\Models\Admin\EmployeesOuts;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
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

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
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

    public function rekap_absensi()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_absen');
    }

    public function tampil_rekap_absensi(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $divisions = [
        'Marketing'             => 2,
        'Purchasing'            => 3,
        'Engineering'           => 4,
        'PPC'                   => 5,
        'Inventory Control'     => 6,
        'IT'                    => 7,
        'HRD'                   => 8,
        'Document Control'      => 9,
        'Quality'               => 10,
        'Produksi'              => 11,
        'Delivery'              => 12,
        'Delivery Produksi'     => 13,
        'Gudang RM'             => 14,
        'Gudang FG'             => 15,
        'Gudang Blok E'          => 16,
        'Daihatsu Sunter'       => 19,
        'Daihatsu Cibitung'     => 20,
        'Daihatsu Karawang'     => 21
        ];

        $data = Attendances::join('employees', 'attendances.employees_id', '=', 'employees.id')
                                ->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir])
                                ->selectRaw('employees.divisions_id, keterangan_absen, COUNT(*) as total')
                                ->groupBy('employees.divisions_id', 'keterangan_absen')
                                ->get();

        $hasil_absen = [];
                foreach ($divisions as $namaDivisi => $divisionId) {
                    $hasil_absen[$namaDivisi] = [
                        'cuti_tahunan' => 0,
                        'sakit' => 0,
                        'ijin' => 0,
                        'alpa' => 0,
                    ];
                }
                foreach ($data as $item) {
                    $namaDivisi = array_search($item->divisions_id, $divisions);
                    if ($namaDivisi) {
                        if ($item->keterangan_absen == 'Cuti Tahunan') {
                            $hasil_absen[$namaDivisi]['cuti_tahunan'] = $item->total;
                        } elseif ($item->keterangan_absen == 'Sakit') {
                            $hasil_absen[$namaDivisi]['sakit'] = $item->total;
                        } elseif ($item->keterangan_absen == 'Ijin') {
                            $hasil_absen[$namaDivisi]['ijin'] = $item->total;
                        } elseif ($item->keterangan_absen == 'Alpa') {
                            $hasil_absen[$namaDivisi]['alpa'] = $item->total;
                        }
                    }
                }
        
        //$hasil_absen['produksi']['cuti_tahunan']   Contoh pemangggilan di View
        return view('admin.pages.report.tampil_rekap_absen',[
                'tanggal_awal'      => $tanggal_awal,
                'tanggal_akhir'     => $tanggal_akhir,
                'hasil_absen'       => $hasil_absen
            ]);
    }

    public function absensi_karyawan()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.report.form_rekap_absen_karyawan',[
            'employees' => $employees
        ]);
    }

    public function tampil_absensi_karyawan(NamaTanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }
        
        $data               = $request->except('_token');
        $employees_id       = $request->input('employees_id');
        $awal               = $request->input('tanggal_awal');
        $akhir              = $request->input('tanggal_akhir');

         $item = Employees::with([
                'divisions',
                'positions',
                'golongans',
                'areas'
                ])->find($employees_id);

        $rekap_absen_employees     = Attendances::with([
                                    'employees'
                                    ])->whereBetween('tanggal_absen', [$awal, $akhir])
                                    ->where('employees_id',$employees_id)->get();

        if ($rekap_absen_employees->isEmpty()) {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('report.absensi_karyawan');
        }
        else{
            $summary = Attendances::whereBetween('tanggal_absen', [$awal, $akhir])
                                    ->where('employees_id', $employees_id)
                                    ->selectRaw('keterangan_absen, COUNT(*) as total')
                                    ->groupBy('keterangan_absen')
                                    ->pluck('total', 'keterangan_absen');

            $cutitahunan = $summary['Cuti Tahunan'] ?? 0;
            $sakit = $summary['Sakit'] ?? 0;
            $ijin = $summary['Ijin'] ?? 0;
            $alpa = $summary['Alpa'] ?? 0;

            $this->fpdf = new FPDF('P', 'mm', 'A4');
            $this->fpdf->AddPage();

            $this->fpdf->Ln(10);
            $this->fpdf->SetFont('Arial', 'B', '18');
            $this->fpdf->Cell(190, 5, 'DATA ABSENSI', 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(190, 5, $item->nama_karyawan, 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(190, 5, \Carbon\Carbon::parse($awal)->isoformat('D MMMM Y') . ' s/d ' . \Carbon\Carbon::parse($akhir)->isoformat('D MMMM Y') . '', 0, 1, 'C');

            $this->fpdf->Ln(10);
            $this->fpdf->SetFont('Arial', 'B', '11');
            $this->fpdf->Cell(25, 10, 'Sakit', 0, 0, 'L');
            $this->fpdf->Cell(5, 10, ' : ', 0, 0, 'C');
            $this->fpdf->Cell(60, 10, $sakit . ' Hari', 0, 0, 'L');

            $this->fpdf->Cell(25, 10, 'Cuti Tahunan', 0, 0, 'L');
            $this->fpdf->Cell(5, 10, ' : ', 0, 0, 'C');
            $this->fpdf->Cell(15, 10, $cutitahunan . ' Hari', 0, 0, 'L');
            $this->fpdf->Ln();

            $this->fpdf->Cell(25, 10, 'Ijin', 0, 0, 'L');
            $this->fpdf->Cell(5, 10, ' : ', 0, 0, 'C');
            $this->fpdf->Cell(60, 10, $ijin . ' Hari', 0, 0, 'L');

            $this->fpdf->Cell(25, 10, 'Alpa', 0, 0, 'L');
            $this->fpdf->Cell(5, 10, ' : ', 0, 0, 'C');
            $this->fpdf->Cell(15, 10, $alpa . ' Hari', 0, 0, 'L');
            $this->fpdf->Ln();

            $this->fpdf->Cell(1);
            $this->fpdf->SetFont('Arial', 'B', '12');
            $this->fpdf->SetFillColor(192, 192, 192); // Warna sel tabel header
            $this->fpdf->Cell(10, 10, 'No', 1, 0, 'C', 1);
            $this->fpdf->Cell(60, 10, 'Tanggal Absen', 1, 0, 'C', 1);
            $this->fpdf->Cell(60, 10, 'Jenis Absen', 1, 0, 'C', 1);
            $this->fpdf->Cell(60, 10, 'Keterangan', 1, 0, 'C', 1);

            $no = 1;

            foreach ($rekap_absen_employees as $rekap_absen_employee) {
                $this->fpdf->Ln();
                $this->fpdf->Cell(1);
                $this->fpdf->SetFont('Arial', '', '11');
                $this->fpdf->Cell(10, 8, $no, 1, 0, 'C');
                $this->fpdf->Cell(60, 8, \Carbon\Carbon::parse($rekap_absen_employee->tanggal_absen)->isoformat(' D MMMM Y'), 1, 0, 'C');
                $this->fpdf->Cell(60, 8, $rekap_absen_employee->keterangan_absen, 1, 0, 'C');
                $this->fpdf->Cell(60, 8, $rekap_absen_employee->keterangan_cuti_khusus, 1, 0, 'C');
                $no++;
            }

            $this->fpdf->Output();
            exit;

        }
    }

    public function karyawan_masuk()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_karyawan_masuk');
    }

    public function tampil_karyawan_masuk(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $awal   = $request->input('tanggal_awal');
        $akhir  = $request->input('tanggal_akhir');

        $employees = Employees::with([
                    'divisions',
                    'positions'
        ])->whereBetween('tanggal_mulai_kerja', [$awal, $akhir])->get();

        if ($employees->isEmpty()) 
        {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('report.karyawan_masuk');
        }
        else
        {
            $this->fpdf = new FPDF('L', 'mm', 'A4');
            $this->fpdf->AddPage();

            $this->fpdf->Ln(10);
            $this->fpdf->SetFont('Arial', 'B', '18');
            $this->fpdf->Cell(280, 5, 'DATA KARYAWAN MASUK', 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(280, 5, 'PERIODE', 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(280, 5, \Carbon\Carbon::parse($awal)->isoformat(' D MMMM Y') . ' s/d ' . \Carbon\Carbon::parse($akhir)->isoformat(' D MMMM Y') . '', 0, 1, 'C');

            $this->fpdf->Ln(10);

            $this->fpdf->Cell(1);
            $this->fpdf->SetFont('Arial', 'B', '12');
            $this->fpdf->SetFillColor(192, 192, 192); // Warna sel tabel header
            $this->fpdf->Cell(10, 10, 'No', 1, 0, 'C', 1);
            $this->fpdf->Cell(90, 10, 'Nama Karyawan', 1, 0, 'C', 1);
            $this->fpdf->Cell(50, 10, 'Mulai Kerja', 1, 0, 'C', 1);
            $this->fpdf->Cell(50, 10, 'No Rekening', 1, 0, 'C', 1);
            $this->fpdf->Cell(80, 10, 'Penempatan', 1, 0, 'C', 1);

            $no = 1;

            foreach ($employees as $employee) {
                $this->fpdf->Ln();
                $this->fpdf->Cell(1);
                $this->fpdf->SetFont('Arial', '', '11');
                $this->fpdf->Cell(10, 8, $no, 1, 0, 'C');
                $this->fpdf->Cell(90, 8, $employee->nama_karyawan, 1, 0, 'L');
                $this->fpdf->Cell(50, 8, \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoformat(' D MMMM Y'), 1, 0, 'C');
                $this->fpdf->Cell(50, 8, $employee->nomor_rekening, 1, 0, 'C');
                $this->fpdf->Cell(80, 8, $employee->divisions->penempatan, 1, 0, 'L');
                $no++;
            }

            $this->fpdf->Output();
            exit;
        }
    }

    public function karyawan_keluar()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_karyawan_keluar');
    }

    public function tampil_karyawan_keluar(TanggalAwalAkhirRequest $request)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $awal   = $request->input('tanggal_awal');
        $akhir  = $request->input('tanggal_akhir');

        $employeesOuts = EmployeesOuts::with([
                    'divisions',
                    'positions'
        ])->whereBetween('tanggal_keluar_karyawan_keluar', [$awal, $akhir])->get();

        if ($employeesOuts->isEmpty()) 
        {
            Alert::error('Data Tidak Ditemukan');
            return redirect()->route('report.karyawan_keluar');
        }
        else
        {
            $this->fpdf = new FPDF('L', 'mm', 'A4');
            $this->fpdf->AddPage();

            $this->fpdf->Ln(10);
            $this->fpdf->SetFont('Arial', 'B', '18');
            $this->fpdf->Cell(280, 5, 'DATA KARYAWAN KELUAR', 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(280, 5, 'PERIODE', 0, 1, 'C');
            $this->fpdf->Ln(5);

            $this->fpdf->Cell(280, 5, \Carbon\Carbon::parse($awal)->isoformat(' D MMMM Y') . ' s/d ' . \Carbon\Carbon::parse($akhir)->isoformat(' D MMMM Y') . '', 0, 1, 'C');

            $this->fpdf->Ln(10);

            $this->fpdf->Cell(1);
            $this->fpdf->SetFont('Arial', 'B', '12');
            $this->fpdf->SetFillColor(192, 192, 192); // Warna sel tabel header
            $this->fpdf->Cell(10, 10, 'No', 1, 0, 'C', 1);
            $this->fpdf->Cell(90, 10, 'Nama Karyawan', 1, 0, 'C', 1);
            $this->fpdf->Cell(50, 10, 'Mulai Kerja', 1, 0, 'C', 1);
            $this->fpdf->Cell(50, 10, 'No Rekening', 1, 0, 'C', 1);
            $this->fpdf->Cell(80, 10, 'Penempatan', 1, 0, 'C', 1);

            $no = 1;

            foreach ($employeesOuts as $employeesOut) {
                $this->fpdf->Ln();
                $this->fpdf->Cell(1);
                $this->fpdf->SetFont('Arial', '', '11');
                $this->fpdf->Cell(10, 8, $no, 1, 0, 'C');
                $this->fpdf->Cell(90, 8, $employeesOut->nama_karyawan_keluar, 1, 0, 'L');
                $this->fpdf->Cell(50, 8, \Carbon\Carbon::parse($employeesOut->tanggal_masuk_karyawan_keluar)->isoformat(' D MMMM Y'), 1, 0, 'C');
                $this->fpdf->Cell(50, 8, $employeesOut->nomor_rekening_karyawan_keluar, 1, 0, 'C');
                $this->fpdf->Cell(80, 8, $employeesOut->divisions->penempatan, 1, 0, 'L');
                $no++;
            }
            $this->fpdf->Output();
            exit;
        }
    }
}

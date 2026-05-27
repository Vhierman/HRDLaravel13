<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Http\Requests\Admin\TahunRequest;
use App\Http\Requests\Admin\NamaTanggalAwalAkhirRequest;
use App\Models\Admin\Attendances;
use App\Models\Admin\Employees;
use App\Models\Admin\EmployeesOuts;
use App\Models\Admin\Overtimes;
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
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
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
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    public function rekap_absensi()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_absen');
    }

    public function tampil_rekap_absensi(TanggalAwalAkhirRequest $request)
    {
        // 1. Validasi Akses
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $tanggal_awal  = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        // Perbaikan typo spasi tersembunyi
        $statusTidakMasuk = ['Sakit', 'Ijin', 'Alpa', 'Cuti Tahunan', 'Cuti Khusus', 'Cuti Panjang', 'OFF'];

        // ==========================================
        // KIERI 1: AMBIL DATA TREN HARIAN (LINE CHART)
        // ==========================================
        $dataAbsensi = DB::table('attendances')
            ->selectRaw('tanggal_absen, keterangan_absen, COUNT(employees_id) as jumlah')
            ->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir])
            ->whereIn('keterangan_absen', $statusTidakMasuk)
            ->groupBy('tanggal_absen', 'keterangan_absen')
            ->orderBy('tanggal_absen', 'asc')
            ->get();

        // Olah Sumbu X (Tanggal)
        $categoriesTanggal = [];
        $start = \Carbon\Carbon::parse($tanggal_awal);
        $end   = \Carbon\Carbon::parse($tanggal_akhir);
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $categoriesTanggal[] = $date->format('Y-m-d');
        }

        // Pemetaan struktur data Series Line Chart
        $seriesData = [];
        foreach ($statusTidakMasuk as $status) {
            $seriesData[$status] = array_fill_keys($categoriesTanggal, 0);
        }

        foreach ($dataAbsensi as $row) {
            if (isset($seriesData[$row->keterangan_absen][$row->tanggal_absen])) {
                $seriesData[$row->keterangan_absen][$row->tanggal_absen] = (int) $row->jumlah;
            }
        }

        $colors = [
            'Sakit'        => '#ffc107',
            'Ijin'         => '#0d6efd',
            'Alpa'         => '#dc3545',
            'Cuti Tahunan' => '#198754',
            'Cuti Khusus'  => '#6f42c1',
        ];

        $finalSeries = [];
        foreach ($seriesData as $status => $datesValue) {
            $finalSeries[] = [
                'name'  => $status,
                'data'  => array_values($datesValue),
                'color' => $colors[$status] ?? '#6c757d'
            ];
        }

        $formattedDates = array_map(function($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('d M');
        }, $categoriesTanggal);

        // ==========================================
        // OPTIMASI ELEGAN: PROSES PIE & KPI DARI HASIL KIERI 1
        // (Tanpa tembak kueri COUNT() berulang kali ke database)
        // ==========================================
        $pieSummary = [];
        $totalKasusSakitGlobal        = 0;
        $totalKasusCutiTahunanGlobal  = 0;
        $totalKasusIjinGlobal         = 0;
        $totalKasusAlpaGlobal         = 0;
        $totalTidakMasukGlobalPie     = 0;

        foreach ($dataAbsensi as $row) {
            $totalTidakMasukGlobalPie += $row->jumlah;
            
            // Akumulasi Angka untuk KPI Cards
            if ($row->keterangan_absen === 'Sakit') $totalKasusSakitGlobal += $row->jumlah;
            if ($row->keterangan_absen === 'Cuti Tahunan') $totalKasusCutiTahunanGlobal += $row->jumlah;
            if ($row->keterangan_absen === 'Ijin') $totalKasusIjinGlobal += $row->jumlah;
            if ($row->keterangan_absen === 'Alpa') $totalKasusAlpaGlobal += $row->jumlah;

            // Akumulasi Angka Grouping untuk Pie Chart
            if (!isset($pieSummary[$row->keterangan_absen])) {
                $pieSummary[$row->keterangan_absen] = 0;
            }
            $pieSummary[$row->keterangan_absen] += $row->jumlah;
        }

        $pieData = [];
        foreach ($pieSummary as $status => $jumlah) {
            $pieData[] = [
                'name'  => $status,
                'y'     => (int) $jumlah,
                'color' => $colors[$status] ?? '#6c757d'
            ];
        }

        // ==========================================
        // KIERI 2 & 3: AMBIL DATA DIVISI SECARA DINAMIS
        // ==========================================
        // Mengambil master penempatan aktif langsung dari database
        $divisions = DB::table('divisions')->pluck('id', 'penempatan')->toArray();

        $dataDivisi = DB::table('attendances')
            ->join('employees', 'attendances.employees_id', '=', 'employees.id')
            ->whereBetween('tanggal_absen', [$tanggal_awal, $tanggal_akhir])
            ->selectRaw('employees.divisions_id, keterangan_absen, COUNT(*) as total')
            ->groupBy('employees.divisions_id', 'keterangan_absen')
            ->get();

        // Bangun template hasil absensi kosong
        $hasil_absen = [];
        foreach ($divisions as $namaDivisi => $divisionId) {
            $hasil_absen[$namaDivisi] = [
                'cuti_tahunan' => 0,
                'sakit'        => 0,
                'ijin'         => 0,
                'alpa'         => 0,
            ];
        }

        // Isi template dengan record kuantitas dari Kueri Data Divisi
        foreach ($dataDivisi as $item) {
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

        // 4. Return View dengan Variabel Bersih
        return view('admin.pages.report.tampil_rekap_absen', [
            'tanggal_awal'                => $tanggal_awal,
            'tanggal_akhir'               => $tanggal_akhir,
            'totalKasusSakitGlobal'       => $totalKasusSakitGlobal,
            'totalKasusCutiTahunanGlobal' => $totalKasusCutiTahunanGlobal,
            'totalKasusIjinGlobal'        => $totalKasusIjinGlobal,
            'totalKasusAlpaGlobal'        => $totalKasusAlpaGlobal,
            'categoriesTanggal'           => json_encode($formattedDates),
            'seriesData'                  => json_encode($finalSeries, JSON_NUMERIC_CHECK),
            'totalTidakMasukGlobalPie'    => $totalTidakMasukGlobalPie,
            'pieData'                     => json_encode($pieData, JSON_NUMERIC_CHECK),
            'hasil_absen'                 => $hasil_absen
        ]);
    }

    public function absensi_karyawan()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $employees      = Employees::with(['divisions','golongans',])->whereIn('golongans_id',[2,3])->get();
        return view('admin.pages.report.form_rekap_absen_karyawan',[
            'employees' => $employees
        ]);
    }

    public function tampil_absensi_karyawan(NamaTanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
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
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_karyawan_masuk');
    }

    public function tampil_karyawan_masuk(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
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
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.report.form_rekap_karyawan_keluar');
    }

    public function tampil_karyawan_keluar(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
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

    public function turnover()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.report.form_turnover');
    }

    public function tampil_turnover(TanggalAwalAkhirRequest $request)
    {
        // 1. Validasi Akses Pengguna
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $tanggal_awal  = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $tahunForm     = \Carbon\Carbon::parse($tanggal_awal)->year;

        // ==========================================
        // 2. DASHBOARD ATAS (TOTAL GLOBAL)
        // ==========================================
        $jumlah_karyawan_all   = Employees::count();
        $total_karyawan_masuk  = Employees::whereBetween('tanggal_mulai_kerja', [$tanggal_awal, $tanggal_akhir])->count();
        $total_karyawan_keluar = EmployeesOuts::whereBetween('tanggal_keluar_karyawan_keluar', [$tanggal_awal, $tanggal_akhir])->count();

        // Fungsi pembantu agar tidak menulis ulang kueri subquery pengecekan karyawan keluar
        $queryKaryawanAktif = function ($query, $tanggal) {
            return $query->select(DB::raw(1))
                ->from('employees_outs')
                ->whereColumn('employees_outs.nik_karyawan_keluar', 'employees.nik_karyawan')
                ->whereDate('employees_outs.tanggal_keluar_karyawan_keluar', '<=', $tanggal);
        };

        $jumlahAwal = DB::table('employees')
            ->whereDate('tanggal_mulai_kerja', '<', $tanggal_awal)
            ->whereNotExists(function ($q) use ($queryKaryawanAktif, $tanggal_awal) {
                $queryKaryawanAktif($q, \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d'));
            })->count();

        $jumlahAkhir = DB::table('employees')
            ->whereDate('tanggal_mulai_kerja', '<=', $tanggal_akhir)
            ->whereNotExists(function ($q) use ($queryKaryawanAktif, $tanggal_akhir) {
                $queryKaryawanAktif($q, $tanggal_akhir);
            })->count();

        $rataRataKaryawan = ($jumlahAwal + $jumlahAkhir) / 2;
        $turnover         = $rataRataKaryawan > 0 ? ($total_karyawan_keluar / $rataRataKaryawan) * 100 : 0;

        // ==========================================
        // 3. GRAFIK BULANAN (MASUK, KELUAR, & TRADING RATE)
        // ==========================================
        $masuk = Employees::selectRaw('MONTH(tanggal_mulai_kerja) as bulan, COUNT(*) as total')
            ->whereBetween('tanggal_mulai_kerja', [$tanggal_awal, $tanggal_akhir])
            ->groupByRaw('MONTH(tanggal_mulai_kerja)')->pluck('total', 'bulan');
                
        $keluar = EmployeesOuts::selectRaw('MONTH(tanggal_keluar_karyawan_keluar) as bulan, COUNT(*) as total')
            ->whereBetween('tanggal_keluar_karyawan_keluar', [$tanggal_awal, $tanggal_akhir])
            ->groupByRaw('MONTH(tanggal_keluar_karyawan_keluar)')->pluck('total', 'bulan');

        $dataMasuk = []; $dataKeluar = []; $dataTurnoverBulanan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $dataMasuk[]  = (int) ($masuk[$bulan] ?? 0);
            $dataKeluar[] = (int) ($keluar[$bulan] ?? 0);

            $startOfBulan = \Carbon\Carbon::createFromDate($tahunForm, $bulan, 1)->startOfMonth()->format('Y-m-d');
            $endOfBulan   = \Carbon\Carbon::createFromDate($tahunForm, $bulan, 1)->endOfMonth()->format('Y-m-d');

            $karyawanAwalBulan = DB::table('employees')->whereDate('tanggal_mulai_kerja', '<', $startOfBulan)
                ->whereNotExists(function ($q) use ($queryKaryawanAktif, $startOfBulan) {
                    $queryKaryawanAktif($q, \Carbon\Carbon::parse($startOfBulan)->subDay()->format('Y-m-d'));
                })->count();

            $karyawanAkhirBulan = DB::table('employees')->whereDate('tanggal_mulai_kerja', '<=', $endOfBulan)
                ->whereNotExists(function ($q) use ($queryKaryawanAktif, $endOfBulan) {
                    $queryKaryawanAktif($q, $endOfBulan);
                })->count();

            $karyawanKeluarBulan = $dataKeluar[$bulan - 1];
            $rataRataBulan       = ($karyawanAwalBulan + $karyawanAkhirBulan) / 2;
            $dataTurnoverBulanan[] = $rataRataBulan > 0 ? round(($karyawanKeluarBulan / $rataRataBulan) * 100, 2) : 0;
        }

        // ==========================================
        // 4. ANALISIS TURNOVER PER PENEMPATAN / DIVISI (FIXED BUG)
        // ==========================================
        $divisions = DB::table('divisions')->get();
        $kumpulanTurnover = [];

        foreach ($divisions as $div) {
            $divAwal = DB::table('employees')->where('divisions_id', $div->id)->whereDate('tanggal_mulai_kerja', '<', $tanggal_awal)
                ->whereNotExists(function ($q) use ($queryKaryawanAktif, $tanggal_awal) {
                    $queryKaryawanAktif($q, \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d'));
                })->count();

            $divAkhir = DB::table('employees')->where('divisions_id', $div->id)->whereDate('tanggal_mulai_kerja', '<=', $tanggal_akhir)
                ->whereNotExists(function ($q) use ($queryKaryawanAktif, $tanggal_akhir) {
                    $queryKaryawanAktif($q, $tanggal_akhir);
                })->count();

            $divKeluar = DB::table('employees_outs')->where('divisions_id', $div->id)
                ->whereBetween('tanggal_keluar_karyawan_keluar', [$tanggal_awal, $tanggal_akhir])->count();

            $divRataRata = ($divAwal + $divAkhir) / 2;
            $divTurnoverRate = $divRataRata > 0 ? round(($divKeluar / $divRataRata) * 100, 2) : 0;

            if ($divTurnoverRate > 0) {
                // FIX: Data dimasukkan ke dalam variabel array penampung pengurutan
                $kumpulanTurnover[$div->penempatan] = $divTurnoverRate;
            }
        }

        $categoriesDivisi = []; $ratesTurnoverDivisi = [];
        if (!empty($kumpulanTurnover)) {
            arsort($kumpulanTurnover);
            $categoriesDivisi    = array_keys($kumpulanTurnover);
            $ratesTurnoverDivisi = array_values($kumpulanTurnover);
        }

        // ==========================================
        // 5. ANALISIS ALASAN KELUAR (PIE & BAR CHART)
        // ==========================================
        $groupAlasan = DB::table('employees_outs')
            ->selectRaw('keterangan_keluar, alasan_keluar, COUNT(*) as total')
            ->whereBetween('tanggal_keluar_karyawan_keluar', [$tanggal_awal, $tanggal_akhir])
            ->groupBy('keterangan_keluar', 'alasan_keluar')->get();

        $pieData = []; $categoriesAlasan = []; $ratesAlasan = [];

        foreach ($groupAlasan as $item) {
            $persentase = $total_karyawan_keluar > 0 ? round(($item->total / $total_karyawan_keluar) * 100, 2) : 0;
            
            if ($persentase > 0) {
                $pieData[] = ['name' => $item->keterangan_keluar, 'y' => $persentase, 'v' => $item->total];
                
                if ($item->alasan_keluar) {
                    $categoriesAlasan[] = $item->alasan_keluar;
                    $ratesAlasan[]      = $persentase;
                }
            }
        }

        // ==========================================
        // 6. KIRIM DATA KE VIEW
        // ==========================================
        return view('admin.pages.report.tampil_turnover', [
            'tanggal_awal'          => $tanggal_awal,
            'tanggal_akhir'         => $tanggal_akhir,
            'dataMasuk'             => $dataMasuk,
            'dataKeluar'            => $dataKeluar,
            'total_karyawan_masuk'  => $total_karyawan_masuk,
            'total_karyawan_keluar' => $total_karyawan_keluar,
            'jumlah_karyawan_all'   => $jumlah_karyawan_all,
            'jumlahAwal'            => $jumlahAwal,
            'jumlahAkhir'           => $jumlahAkhir,
            'jumlahKeluar'          => $total_karyawan_keluar,
            'rataRataKaryawan'      => $rataRataKaryawan,
            'turnover'              => round($turnover, 2) . '%',
            'categoriesDivisi'      => json_encode($categoriesDivisi),
            'ratesTurnoverDivisi'   => json_encode($ratesTurnoverDivisi, JSON_NUMERIC_CHECK),
            'dataTurnoverBulanan'   => json_encode($dataTurnoverBulanan, JSON_NUMERIC_CHECK),
            'pieData'               => json_encode($pieData, JSON_NUMERIC_CHECK),
            'categoriesAlasan'      => json_encode($categoriesAlasan),
            'ratesAlasan'           => json_encode($ratesAlasan, JSON_NUMERIC_CHECK),
        ]);
    }

    public function overtime()
    {
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.report.form_overtime');
    }

    public function tampil_overtime(TanggalAwalAkhirRequest $request)
    {
        // 1. Validasi Hak Akses
        $allowedRoles = ['admin', 'hrd', 'accounting'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $tanggal_awal  = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        // 2. METRIK GLOBAL (KPI CARDS) - Dioptimalkan menjadi satu baris kueri agregasi
        $metrikGlobal = DB::table('overtimes')
            ->selectRaw('SUM(jam_lembur) as total_jam, COUNT(DISTINCT employees_id) as total_karyawan, AVG(jam_lembur) as avg_jam')
            ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
            ->first();

        $total_jam_overtime     = round((float) ($metrikGlobal->total_jam ?? 0), 1);
        $totalKaryawanGlobal    = $metrikGlobal->total_karyawan ?? 0;
        $ringkasanGlobal        = round((float) ($metrikGlobal->avg_jam ?? 0), 2);

        // 3. TREN RATA-RATA HARIAN (LINE/AREA CHART)
        $dataRataRata = DB::table('overtimes')
            ->selectRaw('tanggal_lembur, AVG(jam_lembur) as avg_jam_lembur')
            ->whereBetween('tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
            ->groupBy('tanggal_lembur')
            ->orderBy('tanggal_lembur', 'asc')
            ->get();

        $categoriesTanggal = [];
        $seriesRataRata    = [];

        foreach ($dataRataRata as $row) {
            $categoriesTanggal[] = \Carbon\Carbon::parse($row->tanggal_lembur)->translatedFormat('d M');
            $seriesRataRata[]    = round((float) $row->avg_jam_lembur, 2);
        }

        // 4. ANALISIS PER DIVISI (DISTRIBUSI JAM & PORSI KARYAWAN) 
        // OPTIMASI: Menggabungkan dua query JOIN yang berat menjadi satu kali eksekusi database
        $dataDivisiGlobal = DB::table('overtimes')
            ->join('employees', 'overtimes.employees_id', '=', 'employees.id')
            ->join('divisions', 'employees.divisions_id', '=', 'divisions.id')
            ->selectRaw('divisions.penempatan, SUM(overtimes.jam_lembur) as total_jam_lembur, COUNT(DISTINCT overtimes.employees_id) as jumlah_karyawan')
            ->whereBetween('overtimes.tanggal_lembur', [$tanggal_awal, $tanggal_akhir])
            ->groupBy('divisions.penempatan')
            ->orderBy('total_jam_lembur', 'desc') // Diurutkan dari beban lembur tertinggi
            ->get();

        $categoriesDivisi = [];
        $seriesJamLembur  = [];
        $pieDataKaryawan  = [];

        foreach ($dataDivisiGlobal as $row) {
            // Untuk Grafik Batang Distribusi Jam Kerja
            $categoriesDivisi[] = $row->penempatan;
            $seriesJamLembur[]  = round((float) $row->total_jam_lembur, 1);

            // Untuk Pie Chart Proporsi Kepala/Karyawan
            $pieDataKaryawan[] = [
                'name' => $row->penempatan,
                'y'    => (int) $row->jumlah_karyawan
            ];
        }

        // 5. LEMPAR DATA KE VIEW BLADE
        return view('admin.pages.report.tampil_overtime', [
            'tanggal_awal'           => $tanggal_awal,
            'tanggal_akhir'          => $tanggal_akhir,
            'total_jam_overtime'     => $total_jam_overtime,    // Dipakai untuk KPI total jam
            'totalJamGlobal'         => $total_jam_overtime,    // Menjaga kompatibilitas variabel di view Anda
            'jumlah_karyawan_lembur' => $totalKaryawanGlobal,   // Dipakai untuk KPI total karyawan unik
            'totalKaryawanGlobal'    => $totalKaryawanGlobal,   // Menjaga kompatibilitas variabel di view Anda
            'rataRataGlobal'         => $ringkasanGlobal,
            'categoriesTanggal'      => json_encode($categoriesTanggal),
            'seriesRataRata'         => json_encode($seriesRataRata, JSON_NUMERIC_CHECK),
            'categoriesDivisi'       => json_encode($categoriesDivisi),
            'seriesJamLembur'        => json_encode($seriesJamLembur, JSON_NUMERIC_CHECK),
            'pieDataKaryawan'        => json_encode($pieDataKaryawan, JSON_NUMERIC_CHECK),
        ]);
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SafetyRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Models\Admin\Safetys;
use App\Models\Admin\Employees;
use Alert;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

class SafetyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $safetys = Safetys::with([
                            'employees',
                            'employees.areas',
                            'employees.positions',
                            'employees.divisions',
                            ])->get();

        return view('admin.pages.safety.index',[
            'safetys' => $safetys
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
        return view('admin.pages.safety.create',['employees'=> $employees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SafetyRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data       = $request->except('_token');
        $employee   = Employees::where('id',$request->input('employees_id'))->first();

        Safetys::create([
            'employees_id'         => $request->input('employees_id'),
            'nik_karyawan'          => $employee->nik_karyawan,
            'tanggal_kecelakaan'    => $request->input('tanggal_kecelakaan'),
            'lokasi_kecelakaan'     => $request->input('lokasi_kecelakaan'),
            'jenis_kecelakaan'      => $request->input('jenis_kecelakaan'),
            'kategori_kecelakaan'   => $request->input('kategori_kecelakaan'),
            'hari_hilang'           => $request->input('hari_hilang'),
            'status'                => $request->input('status'),
            'input_oleh'            => Auth::user()->name
            ]);

        Alert::success('Success Input Data Kecelakaan Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('safety.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
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

        $employees      = Employees::with(['divisions'])->get();
        $safety         = Safetys::with(['employees'])->where('id', $id)->first();
        return view('admin.pages.safety.edit',[
        'safety'    => $safety,
        'employees' => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SafetyRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                       = $request->except('_token');
        $safety                     = Safetys::findOrFail($id);
        $employee                   = Employees::where('id',$request->input('employees_id'))->first();
        $safety->update([
            'employees_id'          => $request->input('employees_id'),
            'nik_karyawan'          => $employee->nik_karyawan,
            'tanggal_kecelakaan'    => $request->input('tanggal_kecelakaan'),
            'lokasi_kecelakaan'     => $request->input('lokasi_kecelakaan'),
            'jenis_kecelakaan'      => $request->input('jenis_kecelakaan'),
            'kategori_kecelakaan'   => $request->input('kategori_kecelakaan'),
            'hari_hilang'           => $request->input('hari_hilang'),
            'status'                => $request->input('status'),
            'edit_oleh'             => Auth::user()->name
            ]);
        Alert::success('Success Update Data Kecelakaan Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('safety.index');
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
            $safety = Safetys::findOrFail($id);
            $safety->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $safety->delete();
        });
        Alert::error('Menghapus Data Kecelakaan Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('safety.index');
    }

    public function exportExcel()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Kecelakaan');
        $sheet->setCellValue('C1', 'NIK Karyawan');
        $sheet->setCellValue('D1', 'Nama Karyawan');
        $sheet->setCellValue('E1', 'Area');
        $sheet->setCellValue('F1', 'Jabatan');
        $sheet->setCellValue('G1', 'Penempatan');
        $sheet->setCellValue('H1', 'Lokasi Kecelakaan');
        $sheet->setCellValue('I1', 'Jenis Kecelakaan');
        $sheet->setCellValue('J1', 'Kategori Kecelakaan');
        $sheet->setCellValue('K1', 'Hari Hilang');
        $sheet->setCellValue('L1', 'Status');
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

        $safetys = Safetys::with([
                                'employees',
                                'employees.areas',
                                'employees.positions',
                                'employees.divisions'
                                ])->get();
        
        $row = 2;
        $no = 1;
        foreach ($safetys as $safety) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$safety->tanggal_kecelakaan);
                $sheet->setCellValue('C'.$row, "'".$safety->nik_karyawan);
                $sheet->setCellValue('D'.$row, $safety->employees->nama_karyawan);
                $sheet->setCellValue('E'.$row, $safety->employees->areas->area);
                $sheet->setCellValue('F'.$row, $safety->employees->positions->jabatan);
                $sheet->setCellValue('G'.$row, $safety->employees->divisions->penempatan);
                $sheet->setCellValue('H'.$row, $safety->lokasi_kecelakaan);
                $sheet->setCellValue('I'.$row, $safety->jenis_kecelakaan);
                $sheet->setCellValue('J'.$row, $safety->kategori_kecelakaan);
                $sheet->setCellValue('K'.$row, "'".$safety->hari_hilang);
                $sheet->setCellValue('L'.$row, $safety->status);

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
                $sheet->getStyle("C2:C{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J2:J{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K2:K{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("L2:L{$lastRow}")
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

        $filename = 'DataKecelakaanKerja.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function form_statistik_kecelakaan()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.safety.form_statistik_kecelakaan');

    }
    
    public function tampil_statistik_kecelakaan(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $tanggal_awal       = $request->input('tanggal_awal');
        $tanggal_akhir      = $request->input('tanggal_akhir');

        $rekap_fatality     = Safetys::where('kategori_kecelakaan', 'Fatality')->whereBetween('tanggal_kecelakaan',[$tanggal_awal,$tanggal_akhir])->count();
        $rekap_lwd          = Safetys::where('kategori_kecelakaan', 'LWD')->whereBetween('tanggal_kecelakaan',[$tanggal_awal,$tanggal_akhir])->count();
        $rekap_non_lwd      = Safetys::where('kategori_kecelakaan', 'Non LWD')->whereBetween('tanggal_kecelakaan',[$tanggal_awal,$tanggal_akhir])->count();
        $rekap_traffic      = Safetys::where('kategori_kecelakaan', 'Traffic Accident')->whereBetween('tanggal_kecelakaan',[$tanggal_awal,$tanggal_akhir])->count();

        // Chart StatistikHarian
        // 2. Ambil data agregat berdasarkan tanggal_kecelakaan dan kategori_kecelakaan (Hanya 1 Query)
        $raw_data = DB::table('safetys')
                    ->selectRaw('tanggal_kecelakaan, kategori_kecelakaan, COUNT(*) as jumlah')
                    ->whereBetween('tanggal_kecelakaan', [$tanggal_awal, $tanggal_akhir])
                    ->groupBy('tanggal_kecelakaan', 'kategori_kecelakaan')
                    ->get()
                    ->groupBy('tanggal_kecelakaan'); // Dikelompokkan ulang menjadi key berformat tanggal

        // 3. Generate semua daftar tanggal dari awal hingga akhir menggunakan CarbonPeriod
        $period = CarbonPeriod::create($tanggal_awal, $tanggal_akhir);

        // Siapkan array kosong penampung data Highcharts
        $categories_pertama     = []; // Penampung Label Sumbu X (Tanggal)
        $dataFatality   = []; // Data Array untuk Garis Fatality
        $dataLWD        = []; // Data Array untuk Garis LWD
        $dataNonLWD     = []; // Data Array untuk Garis Non LWD
        $dataTraffic    = []; // Data Array untuk Garis Traffic Accident

        // 4. Lakukan perulangan harian untuk memetakan data ke dalam array masing-masing
        foreach ($period as $date) {
            $formatted_date = $date->format('Y-m-d');
            
            // Simpan label tanggal (Format: "24 May") ke array categories
            $categories_pertama[] = $date->format('d M');

            // Ambil data kecelakaan pada tanggal terkait jika ada di database
            $day_data = $raw_data->get($formatted_date);

            // Kondisi: Jika data ada, cari jumlah berdasarkan kategori enumnnya. Jika tidak ada, set ke 0
            $dataFatality[] = $day_data ? (int) $day_data->where('kategori_kecelakaan', 'Fatality')->first()?->jumlah ?? 0 : 0;
            $dataLWD[]      = $day_data ? (int) $day_data->where('kategori_kecelakaan', 'LWD')->first()?->jumlah ?? 0 : 0;
            $dataNonLWD[]   = $day_data ? (int) $day_data->where('kategori_kecelakaan', 'Non LWD')->first()?->jumlah ?? 0 : 0;
            $dataTraffic[]  = $day_data ? (int) $day_data->where('kategori_kecelakaan', 'Traffic Accident')->first()?->jumlah ?? 0 : 0;
        }
        // Chart StatistikHarian

        // Chart Kecelakaan Berdasarkan Jenis Kecelakaan
        // 1. Ambil data agregat dari database
        $data_kecelakaan = Safetys::selectRaw('jenis_kecelakaan, COUNT(*) as total')
            ->groupBy('jenis_kecelakaan')
            ->get();
        // 2. Format data menjadi array yang dikenali oleh Pie Chart Highcharts
        // Format tujuan: [ ['name' => 'Fatality', 'y' => 10], ['name' => 'LWD', 'y' => 5] ]
        $chartData = $data_kecelakaan->map(function ($item) {
            return [
                'name' => $item->jenis_kecelakaan ?? 'Tidak Diketahui',
                'y'    => (int) $item->total // Highcharts mewajibkan tipe data integer/float untuk nilai Y
            ];
        });
        // Chart Kecelakaan Berdasarkan Jenis Kecelakaan

        // Chart Kecelakaan Berdasarkan Penempatan
        // 1. Ambil data agregat jumlah kecelakaan berdasarkan penempatan divisi
        $data_divisi = DB::table('safetys')
            ->join('employees', 'safetys.employees_id', '=', 'employees.id')
            ->join('divisions', 'employees.divisions_id', '=', 'divisions.id')
            ->select('divisions.penempatan', DB::raw('COUNT(safetys.id) as total_kecelakaan'))
            ->groupBy('divisions.penempatan')
            ->get();

        // 2. Format menjadi struktur data Pie Chart Highcharts [{ name: '...', y: ... }]
        $chartDataDivisi = $data_divisi->map(function ($item) {
            return [
                'name' => $item->penempatan ?? 'Tanpa Penempatan',
                'y'    => (int) $item->total_kecelakaan
            ];
        });
        // Chart Kecelakaan Berdasarkan Penempatan

        // Chart Kecelakaan Berdasarkan Lokasi Kecelakaan
        // 1. Ambil data jumlah kecelakaan dikelompokkan berdasarkan lokasi
        $data_lokasi = DB::table('safetys')
            ->select('lokasi_kecelakaan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('lokasi_kecelakaan') // Memastikan lokasi tidak kosong
            ->groupBy('lokasi_kecelakaan')
            ->orderBy('total', 'desc') // Mengurutkan dari lokasi paling rawan/banyak kasus
            ->get();

        // 2. Pisahkan data menjadi 2 array terpisah untuk kebutuhan Bar Chart
        $categoriesKeempat = [];
        $chartDataLokasiKecelakaan = [];

        foreach ($data_lokasi as $item) {
            $categoriesKeempat[] = $item->lokasi_kecelakaan;
            $chartDataLokasiKecelakaan[] = (int) $item->total; // Pastikan dikonversi ke integer
        }
        // Chart Kecelakaan Berdasarkan Lokasi Kecelakaan

        // Chart Hari Hilang Berdasarkan Penempatan
        // 1. Ambil data gabungan: hitung kasus (COUNT) dan total hari hilang (SUM) per penempatan
        $data_statistik = DB::table('safetys')
            ->join('employees', 'safetys.employees_id', '=', 'employees.id')
            ->join('divisions', 'employees.divisions_id', '=', 'divisions.id')
            ->select(
                'divisions.penempatan',
                DB::raw('COUNT(safetys.id) as total_kasus'),
                DB::raw('SUM(safetys.hari_hilang) as total_hari_hilang')
            )
            ->groupBy('divisions.penempatan')
            ->orderBy('total_kasus', 'desc') // Urutkan berdasarkan divisi paling banyak kasus
            ->get();

        // 2. Siapkan array penampung untuk format Highcharts
        $categories_hari_hilang = [];
        $dataKasusHariHilang = [];
        $dataHariHilang = [];

        foreach ($data_statistik as $item) {
            $categories_hari_hilang[]     = $item->penempatan ?? 'Tanpa Penempatan';
            $dataKasusHariHilang[]      = (int) $item->total_kasus;
            // Jika ada divisi yang tidak memiliki hari hilang (0), pastikan tetap terisi angka 0 bukan null
            $dataHariHilang[] = (int) ($item->total_hari_hilang ?? 0);
        }
        // Chart Hari Hilang Berdasarkan Penempatan

        return view('admin.pages.safety.tampil_statistik_kecelakaan',[
            'tanggal_awal'                  => $tanggal_awal,
            'tanggal_akhir'                 => $tanggal_akhir,
            'rekap_fatality'                => $rekap_fatality,
            'rekap_lwd'                     => $rekap_lwd,
            'rekap_non_lwd'                 => $rekap_non_lwd,
            'rekap_traffic'                 => $rekap_traffic,
            'categories_pertama'            => json_encode($categories_pertama),
            'fatality'                      => json_encode($dataFatality),
            'lwd'                           => json_encode($dataLWD),
            'non_lwd'                       => json_encode($dataNonLWD),
            'traffic'                       => json_encode($dataTraffic),
            'chartData'                     => json_encode($chartData),
            'chartDataDivisi'               => json_encode($chartDataDivisi),
            'categoriesKeempat'             => json_encode($categoriesKeempat),
            'chartDataLokasiKecelakaan'     => json_encode($chartDataLokasiKecelakaan),
            'categories_hari_hilang'        => json_encode($categories_hari_hilang),
            'dataKasusHariHilang'           => json_encode($dataKasusHariHilang),
            'dataHariHilang'                => json_encode($dataHariHilang),
        ]);

    }
}

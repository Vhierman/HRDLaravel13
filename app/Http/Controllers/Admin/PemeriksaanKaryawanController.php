<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\FaskesRequest;
use App\Http\Requests\Admin\PemeriksaanKaryawanRequest;
use App\Http\Requests\Admin\TanggalAwalAkhirRequest;
use App\Models\Admin\Faskes;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\PemeriksaanKaryawan;
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
use Carbon\CarbonPeriod;

class PemeriksaanKaryawanController extends Controller
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

        $currentYear = now()->year;

        // 1. Ambil data agregasi dasar (Gunakan count efektif)
        $total_karyawan = Employees::count();
        $total_faskes = Faskes::count();
        
        $karyawan_mcu_tahun_ini = PemeriksaanKaryawan::whereYear('tanggal_pemeriksaan', $currentYear)
            ->distinct('employees_id')
            ->count('employees_id');
            
        $karyawan_belum_mcu_tahun_ini = $total_karyawan - $karyawan_mcu_tahun_ini;

        // 2. Trend MCU per Bulan (Gunakan Map & Collection untuk mengisi bulan kosong)
        $trend_mcu = PemeriksaanKaryawan::selectRaw("MONTH(tanggal_pemeriksaan) as bulan, COUNT(*) as total")
            ->whereYear('tanggal_pemeriksaan', $currentYear)
            ->groupByRaw('MONTH(tanggal_pemeriksaan)')
            ->pluck('total', 'bulan'); // Langsung menghasilkan array [bulan => total]

        $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Mengisi otomatis bulan yang tidak ada datanya dengan angka 0
        $seriesData = collect(range(1, 12))->map(fn($bulan) => (int) ($trend_mcu[$bulan] ?? 0))->all();

        // 3. Data Pie: Status Kelayakan (Sederhanakan format langsung untuk Highcharts)
        $pieData = PemeriksaanKaryawan::selectRaw("status_kelayakan as name, COUNT(*) as y")
            ->whereYear('tanggal_pemeriksaan', $currentYear) // Ditambahkan filter tahun agar konsisten
            ->groupBy('status_kelayakan')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name ?? 'Unknown',
                'y' => (int) $item->y
            ])->all();

        // 4. Data Pie: Jenis Pemeriksaan
        $pieDataJenisPemeriksaan = PemeriksaanKaryawan::selectRaw("jenis_pemeriksaan as name, COUNT(*) as y")
            ->whereYear('tanggal_pemeriksaan', $currentYear) // Ditambahkan filter tahun agar konsisten
            ->groupBy('jenis_pemeriksaan')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'y' => (int) $item->y
            ])->all();

        return view('admin.pages.pemeriksaan_karyawan.index', compact(
            'currentYear',
            'total_karyawan',
            'karyawan_mcu_tahun_ini',
            'karyawan_belum_mcu_tahun_ini',
            'categories',
            'seriesData',
            'pieData',
            'pieDataJenisPemeriksaan',
            'total_faskes'

        ));
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
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

        $pemeriksaan_karyawan   = PemeriksaanKaryawan::findOrFail($id);
        $faskess                = Faskes::all();
        $employees              = Employees::with(['divisions'])->get();
        
        return view('admin.pages.pemeriksaan_karyawan.edit',[
        'pemeriksaan_karyawan'  => $pemeriksaan_karyawan,
        'faskess'               => $faskess,
        'employees'             => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                   = $request->except('_token');
        $pemeriksaan_karyawan   = PemeriksaanKaryawan::findOrFail($id);
        $employee               = Employees::where('id',$request->input('employees_id'))->first();
        $pemeriksaan_karyawan->update([
            'employees_id'                      => $request->input('employees_id'),
            'nik_karyawan'                      => $pemeriksaan_karyawan->nik_karyawan,
            'faskes_id '                        => $request->input('nomor_sertifikat_faskes_id bnsp'),
            'nomor_mcu'                         => $request->input('nomor_mcu'),
            'tanggal_pemeriksaan'               => $request->input('tanggal_pemeriksaan'),
            'dokter_pemeriksa'                  => $request->input('dokter_pemeriksa'),
            'berat_badan'                       => $request->input('berat_badan'),
            'tinggi_badan'                      => $request->input('tinggi_badan'),
            'tekanan_darah'                     => $request->input('tekanan_darah'),
            'gula_darah'                        => $request->input('gula_darah'),
            'ekg'                               => $request->input('ekg'),
            'jenis_pemeriksaan'                 => $request->input('jenis_pemeriksaan'),
            'status_kelayakan'                  => $request->input('status_kelayakan'),
            'catatan_dokter'                    => $request->input('catatan_dokter'),
            'tanggal_pemeriksaan_berikutnya'    => $request->input('tanggal_pemeriksaan_berikutnya'),
            'edit_oleh'                         => Auth::user()->name
            ]);

        Alert::success('Success Update Data Pemeriksaan Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.index');
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
            $pemeriksaan_karyawan = PemeriksaanKaryawan::findOrFail($id);
            $pemeriksaan_karyawan->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $pemeriksaan_karyawan->delete();
        });
        Alert::error('Menghapus Data Pemeriksaan Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.index');
    }

    public function data_faskes()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $faskess = Faskes::all();
        return view('admin.pages.pemeriksaan_karyawan.data_faskes',
        [
            'faskess' => $faskess
        ]);
    }

    public function form_edit_faskes($id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $faskes = Faskes::find($id);
        return view('admin.pages.pemeriksaan_karyawan.form_edit_faskes',
        [
            'faskes' => $faskes
        ]);
    }
    public function hapus_faskes($id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        DB::transaction(function () use ($id) {
            $faskes = Faskes::findOrFail($id);
            $faskes->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $faskes->delete();
        });
        Alert::success('Success Hapus Data Faskes','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.data_faskes');
    
    }
    public function form_tambah_faskes()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        return view('admin.pages.pemeriksaan_karyawan.form_tambah_faskes');
    }

    public function tambah_faskes(FaskesRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        Faskes::create([
            'nama_faskes'   => $request->input('nama_faskes'),
            'alamat'        => $request->input('alamat'),
            'rt'            => $request->input('rt'),
            'rw'            => $request->input('rw'),
            'kelurahan'     => $request->input('kelurahan'),
            'kecamatan'     => $request->input('kecamatan'),
            'kota'          => $request->input('kota'),
            'provinsi'      => $request->input('provinsi'),
            'kode_pos'      => $request->input('kode_pos'),
            'input_oleh'    => Auth::user()->name
            ]);
        Alert::success('Success Input Data Faskes','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.index');
    }

    public function update_faskes(FaskesRequest $request,string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data = $request->except('_token');
        Faskes::where('id',$id)->update([
            'nama_faskes'   => $request->input('nama_faskes'),
            'alamat'        => $request->input('alamat'),
            'rt'            => $request->input('rt'),
            'rw'            => $request->input('rw'),
            'kelurahan'     => $request->input('kelurahan'),
            'kecamatan'     => $request->input('kecamatan'),
            'kota'          => $request->input('kota'),
            'provinsi'      => $request->input('provinsi'),
            'kode_pos'      => $request->input('kode_pos'),
            'edit_oleh'     => Auth::user()->name
        ]);
        Alert::success('Success Update Data Faskes','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.data_faskes');
    }

    public function form_tambah_data_pemeriksaan()
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        $employees  = Employees::all();
        $faskess     = Faskes::all();
        return view('admin.pages.pemeriksaan_karyawan.form_tambah_data_pemeriksaan',
        [
            'employees' => $employees,
            'faskess' => $faskess
        ]);
    }

    public function tambah_data_pemeriksaan(PemeriksaanKaryawanRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        $employee = Employees::find($request->input('employees_id'));
        PemeriksaanKaryawan::create([
            'employees_id'                     => $request->input('employees_id'),
            'faskes_id'                        => $request->input('faskes_id'),
            'nomor_mcu'                         => $request->input('nomor_mcu'),
            'nik_karyawan'                      => $employee->nik_karyawan,
            'tanggal_pemeriksaan'               => $request->input('tanggal_pemeriksaan'),
            'dokter_pemeriksa'                  => $request->input('dokter_pemeriksa'),
            'berat_badan'                       => $request->input('berat_badan'),
            'tinggi_badan'                      => $request->input('tinggi_badan'),
            'tekanan_darah'                     => $request->input('tekanan_darah'),
            'gula_darah'                        => $request->input('gula_darah'),
            'ekg'                               => $request->input('ekg'),
            'jenis_pemeriksaan'                 => $request->input('jenis_pemeriksaan'),
            'status_kelayakan'                  => $request->input('status_kelayakan'),
            'catatan_dokter'                    => $request->input('catatan_dokter'),
            'tanggal_pemeriksaan_berikutnya'    => $request->input('tanggal_pemeriksaan_berikutnya'),
            'input_oleh'                        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Pemeriksaan Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('pemeriksaan_karyawan.index');
    }

    public function form_lihat_data_pemeriksaan()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.pemeriksaan_karyawan.form_lihat_data_pemeriksaan');
    }
    
    public function tampil_data_pemeriksaan(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                   = $request->except('_token');
        $tanggal_awal           = $request->input('tanggal_awal');
        $tanggal_akhir          = $request->input('tanggal_akhir');
        $itemPemeriksaans       = PemeriksaanKaryawan::with('employees','faskes')
                                    ->whereBetween('tanggal_pemeriksaan',[$tanggal_awal,$tanggal_akhir])->get();

        return view('admin.pages.pemeriksaan_karyawan.tampil_data_pemeriksaan',[
                'tanggal_awal'      => $tanggal_awal,
                'tanggal_akhir'     => $tanggal_akhir,
                'itemPemeriksaans'  => $itemPemeriksaans
            ]);
    }

    public function export_excell(Request $request)
    {
        $allowedRoles = ['admin', 'hrd'];
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
        $sheet->setCellValue('D1', 'Area');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Penempatan');
        $sheet->setCellValue('G1', 'Tanggal Pemeriksaan');
        $sheet->setCellValue('H1', 'Nama Faskes');
        $sheet->setCellValue('I1', 'Nomor MCU');
        $sheet->setCellValue('J1', 'Dokter Pemeriksa');
        $sheet->setCellValue('K1', 'Berat Badan');
        $sheet->setCellValue('L1', 'Tinggi Badan');
        $sheet->setCellValue('M1', 'Tekanan Darah');
        $sheet->setCellValue('N1', 'Gula Darah');
        $sheet->setCellValue('O1', 'EKG');
        $sheet->setCellValue('P1', 'Jenis Pemeriksaan');
        $sheet->setCellValue('Q1', 'Status Kelayakan');
        $sheet->setCellValue('R1', 'Catatan Dokter');
        $sheet->setCellValue('S1', 'Tanggal Pemeriksaan Berikutnya');
        // Header

        //Style
        $sheet->getStyle('A1:S1')->applyFromArray([
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

        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');

        $item_pemeriksaans = PemeriksaanKaryawan::with([
                                    'employees',
                                    'faskes',
                                    'employees.areas',
                                    'employees.divisions',
                                    'employees.positions' ])
                                    ->whereBetween('tanggal_pemeriksaan', [
                                        $tanggal_awal,
                                        $tanggal_akhir ])->get();

        $row = 2;
        $no = 1;
        foreach ($item_pemeriksaans as $item_pemeriksaan) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$item_pemeriksaan->nik_karyawan);
                $sheet->setCellValue('C'.$row, $item_pemeriksaan->employees->nama_karyawan);
                $sheet->setCellValue('D'.$row, $item_pemeriksaan->employees->areas->area);
                $sheet->setCellValue('E'.$row, $item_pemeriksaan->employees->positions->jabatan);
                $sheet->setCellValue('F'.$row, $item_pemeriksaan->employees->divisions->penempatan);
                $sheet->setCellValue('G'.$row, "'".$item_pemeriksaan->tanggal_pemeriksaan);
                $sheet->setCellValue('H'.$row, $item_pemeriksaan->faskes->nama_faskes);
                $sheet->setCellValue('I'.$row, "'".$item_pemeriksaan->nomor_mcu);
                $sheet->setCellValue('J'.$row, "'".$item_pemeriksaan->dokter_pemeriksa); 
                $sheet->setCellValue('K'.$row, "'".$item_pemeriksaan->berat_badan); 
                $sheet->setCellValue('L'.$row, "'".$item_pemeriksaan->tinggi_badan); 
                $sheet->setCellValue('M'.$row, "'".$item_pemeriksaan->tekanan_darah); 
                $sheet->setCellValue('N'.$row, "'".$item_pemeriksaan->gula_darah); 
                $sheet->setCellValue('O'.$row, "'".$item_pemeriksaan->ekg); 
                $sheet->setCellValue('P'.$row, $item_pemeriksaan->jenis_pemeriksaan); 
                $sheet->setCellValue('Q'.$row, $item_pemeriksaan->status_kelayakan); 
                $sheet->setCellValue('R'.$row, $item_pemeriksaan->catatan_dokter); 
                $sheet->setCellValue('S'.$row, "'".$item_pemeriksaan->tanggal_pemeriksaan_berikutnya); 
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:S{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:S{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G2:G{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I2:I{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K2:Q{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("S2:S{$lastRow}")
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

        $filename = 'DataPemeriksaanKaryawan.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function form_lihat_statistik_pemeriksaan()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        return view('admin.pages.pemeriksaan_karyawan.form_lihat_statistik_pemeriksaan');
    }

    public function tampil_data_statistik_pemeriksaan(TanggalAwalAkhirRequest $request)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data                           = $request->except('_token');
        $tanggal_awal                   = $request->input('tanggal_awal');
        $tanggal_akhir                  = $request->input('tanggal_akhir');
        $total_karyawan                 = Employees::count();
        $total_faskes = Faskes::count();

        $karyawan_mcu_tahun_ini         = PemeriksaanKaryawan::whereBetween('tanggal_pemeriksaan', [$tanggal_awal,$tanggal_akhir])
                                                        ->distinct('employees_id')
                                                        ->count('employees_id');
        $karyawan_belum_mcu_tahun_ini   = $total_karyawan - $karyawan_mcu_tahun_ini;

        //Bulan
        // 2. Ambil data agregasi jumlah pemeriksaan per bulan dari database
        // Menggunakan format YYYY-MM untuk mengelompokkan data secara akurat
        $dataPemeriksaan = PemeriksaanKaryawan::selectRaw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') as bulan, COUNT(*) as total")
            ->whereBetween('tanggal_pemeriksaan', [$tanggal_awal, $tanggal_akhir])
            ->groupByRaw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')")
            ->pluck('total', 'bulan'); // Hasil: ['2026-01' => 50, '2026-02' => 35]

        // 3. Buat periode bulanan lengkap berdasarkan input tanggal awal & akhir
        $start = Carbon::parse($tanggal_awal)->startOfMonth();
        $end = Carbon::parse($tanggal_akhir)->startOfMonth();
        
        // Buat interval per 1 bulan
        $period = CarbonPeriod::create($start, '1 month', $end);

        $categoriesBulan = [];
        $seriesDataBulan = [];
        $grandTotalBulan = 0;

        foreach ($period as $date) {

            $keyBulan = $date->format('Y-m');

            $categoriesBulan[] = $date->translatedFormat('M Y');

            $totalBulanIni = (int) ($dataPemeriksaan[$keyBulan] ?? 0);

            $seriesDataBulan[] = $totalBulanIni;

            $grandTotalBulan += $totalBulanIni;
        }
        //Bulan

        // 3. Data Pie: Status Kelayakan (Sederhanakan format langsung untuk Highcharts)
        $pieData = PemeriksaanKaryawan::selectRaw("status_kelayakan as name, COUNT(*) as y")
            ->whereBetween('tanggal_pemeriksaan', [$tanggal_awal,$tanggal_akhir]) // Ditambahkan filter tahun agar konsisten
            ->groupBy('status_kelayakan')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name ?? 'Unknown',
                'y' => (int) $item->y
            ])->all();

        // 4. Data Pie: Jenis Pemeriksaan
        $pieDataJenisPemeriksaan = PemeriksaanKaryawan::selectRaw("jenis_pemeriksaan as name, COUNT(*) as y")
            ->whereBetween('tanggal_pemeriksaan', [$tanggal_awal,$tanggal_akhir]) // Ditambahkan filter tahun agar konsisten
            ->groupBy('jenis_pemeriksaan')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'y' => (int) $item->y
            ])->all();


        //Penempatan
        // 2. Query agregasi data berdasarkan penempatan (divisions.penempatan)
        $dataPenempatan = PemeriksaanKaryawan::join('employees', 'health_employees.employees_id', '=', 'employees.id')
            ->join('divisions', 'employees.divisions_id', '=', 'divisions.id')
            ->selectRaw('divisions.penempatan as nama_divisi, COUNT(health_employees.id) as total')
            ->whereBetween('health_employees.tanggal_pemeriksaan', [$tanggal_awal, $tanggal_akhir])
            ->groupBy('divisions.id', 'divisions.penempatan')
            ->get();

        // 3. Hitung Grand Total untuk keperluan kalkulasi persentase manual jika dibutuhkan di backend
        $grandTotalPenempatan = $dataPenempatan->sum('total');

        // 4. Format data agar siap dibaca oleh Highcharts
        $categoriesPenempatan = [];
        $seriesDataPenempatan = [];

        foreach ($dataPenempatan as $item) {
            $categoriesPenempatan[] = $item->nama_divisi;
            
            // Hitung persentase
            $persentase = $grandTotalPenempatan > 0 ? round(($item->total / $grandTotalPenempatan) * 100, 2) : 0;

            $seriesDataPenempatan[] = [
                'y' => (int) $item->total,
                'percentage_custom' => $persentase // Kita sisipkan data persentase ke dalam koordinat Y
            ];
        }
        //Penempatan


        //Tanggal
        // 2. Ambil data agregasi jumlah pemeriksaan per tanggal dari database
        $dataPemeriksaan = PemeriksaanKaryawan::selectRaw("DATE(tanggal_pemeriksaan) as tanggal, COUNT(*) as total")
            ->whereBetween('tanggal_pemeriksaan', [$tanggal_awal, $tanggal_akhir])
            ->groupByRaw("DATE(tanggal_pemeriksaan)")
            ->pluck('total', 'tanggal'); // Menghasilkan array asosiatif: ['YYYY-MM-DD' => total]

        // 3. Buat deretan tanggal lengkap (mencegah tanggal kosong hilang di grafik)
        $period = CarbonPeriod::create($tanggal_awal, $tanggal_akhir);
        
        $categoriesTanggal = [];
        $seriesDataTanggal = [];
        $grandTotalTanggal = 0;

        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            
            // Format label untuk X-Axis (Contoh: 01 Jun)
            $categoriesTanggal[] = $date->translatedFormat('d M'); 
            
            // Jika tanggal tersebut tidak ada di DB, isi dengan 0
            $totalHariIni = (int) ($dataPemeriksaan[$formattedDate] ?? 0);
            $seriesDataTanggal[] = $totalHariIni;
            
            // Hitung total kumulatif untuk info ringkasan
            $grandTotalTanggal += $totalHariIni;
        }
        //Tanggal

        return view('admin.pages.pemeriksaan_karyawan.tampil_data_statistik_pemeriksaan',[
                'tanggal_awal'                  => $tanggal_awal,
                'tanggal_akhir'                 => $tanggal_akhir,
                'total_karyawan'                => $total_karyawan,
                'karyawan_mcu_tahun_ini'        => $karyawan_mcu_tahun_ini,
                'karyawan_belum_mcu_tahun_ini'  => $karyawan_belum_mcu_tahun_ini,

                'categoriesBulan'                    => $categoriesBulan,
                'seriesDataBulan'                    => $seriesDataBulan,
                'grandTotalBulan'                    => $grandTotalBulan,

                'pieData'                       => $pieData,
                'pieDataJenisPemeriksaan'       => $pieDataJenisPemeriksaan,
                'seriesDataPenempatan'       => $seriesDataPenempatan,
                'categoriesPenempatan'       => $categoriesPenempatan,
                'grandTotalPenempatan'       => $grandTotalPenempatan,
                'categoriesTanggal'       => $categoriesTanggal,
                'seriesDataTanggal'       => $seriesDataTanggal,
                'grandTotalTanggal'       => $grandTotalTanggal,
                'total_faskes'       => $total_faskes,
            ]);
    }
}

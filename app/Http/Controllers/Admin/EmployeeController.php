<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Http\Requests\Admin\EmployeeUpdateRequest;
use App\Models\Admin\Employees;
use App\Models\Admin\Companies;
use App\Models\Admin\WorkingHours;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Golongans;
use App\Models\Admin\Areas;
use App\Models\Admin\MinimalSalaries;
use App\Models\Admin\HistoryContracts;
use App\Models\Admin\HistoryPositions;
use Alert;
use Codedge\Fpdf\Fpdf\Fpdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
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

        $employees = Employees::with([
                    'areas',
                    'golongans',
                    'divisions',
                    'positions'
                    ])->get();

        return view('admin.pages.employee.index',[
            'employees' => $employees
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

        $companies      = Companies::all();
        $working_hours  = WorkingHours::all();
        $golongans      = Golongans::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $areas          = Areas::all();

        return view ('admin.pages.employee.create',[
            'companies'     => $companies,
            'working_hours' => $working_hours,
            'divisions'     => $divisions,
            'positions'     => $positions,
            'golongans'     => $golongans,
            'areas'         => $areas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        //Cari Minimal Upah Dari Area
        $id_area        = $request->input('areas_id');
        $minimal_upah   = MinimalSalaries::where('areas_id',$id_area)->get();
        //Cari Minimal Upah Dari Area
        
        $data       = $request->except(['_token', '_method']);

        $fileFields = [
            'foto_karyawan' => 'assets/foto/karyawan',
            'foto_ktp'      => 'assets/foto/ktp',
            'foto_npwp'     => 'assets/foto/npwp',
            'foto_kk'       => 'assets/foto/kk',
        ];

        foreach ($fileFields as $field => $path) {
            if ($request->hasFile($field)) {
                // Ambil objek filenya, bukan status boolean-nya
                $file = $request->file($field);
                
                // Buat nama unik
                $fileName = Str::random(10) . $file->getClientOriginalName();
                
                // Simpan file
                $file->storeAs($path, $fileName, 'public');
                
                // Masukkan nama file ke array data untuk disimpan ke database
                $data[$field] = $fileName;
            } else {
                // Jika tidak ada file diunggah (saat create), pastikan nilainya null atau sesuai kebutuhan
                $data[$field] = null;
            }
        }

        Employees::create($data);
        
        Alert::success('Success Input Data Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.index');

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

        $employee               = Employees::findOrFail($id);
        $golongans              = Golongans::all();
        $companies              = Companies::all();
        $divisions              = Divisions::all();
        $positions              = Positions::all();
        $working_hours          = WorkingHours::all();
        $areas                  = Areas::all();

        $today                  = Carbon::today();
        $tanggal_lahir          = Carbon::parse($employee->tanggal_lahir);
        $tanggal_mulai_kerja    = Carbon::parse($employee->tanggal_mulai_kerja);

        $UmurLengkap            = $tanggal_lahir->diff($today)->format('%y Tahun, %m Bulan');
        $MasaKerja              = $tanggal_mulai_kerja->diff($today)->format('%y Tahun, %m Bulan');

        $history_contracts      = HistoryContracts::with(['employees'])->get();

        $history_positions = HistoryPositions::with([
                            'employees',
                            'divisions',
                            'positions',
                            'companies',
                            'areas'
                            ])->where('employees_id', $employee->id)->get();

        return view ('admin.pages.employee.show',[
            'employee'                  => $employee,
            'golongans'                 => $golongans,
            'companies'                 => $companies,
            'divisions'                 => $divisions,
            'positions'                 => $positions,
            'working_hours'             => $working_hours,
            'UmurLengkap'               => $UmurLengkap,
            'MasaKerja'                 => $MasaKerja,
            'areas'                     => $areas,
            'history_contracts'         => $history_contracts,
            'history_positions'         => $history_positions
        ]);
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

        $employee       = Employees::findOrFail($id);
        $golongans      = Golongans::all();
        $companies      = Companies::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $working_hours  = WorkingHours::all();
        $areas          = Areas::all();

        return view ('admin.pages.employee.edit',[
            'employee'      => $employee,
            'golongans'     => $golongans,
            'companies'     => $companies,
            'divisions'     => $divisions,
            'positions'     => $positions,
            'working_hours' => $working_hours,
            'areas'         => $areas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeUpdateRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except(['_token', '_method']);
        $employee   = Employees::findOrFail($id);
        $fileFields = [
            'foto_karyawan' => 'assets/foto/karyawan',
            'foto_ktp'      => 'assets/foto/ktp',
            'foto_npwp'     => 'assets/foto/npwp',
            'foto_kk'       => 'assets/foto/kk',
        ];

        foreach ($fileFields as $field => $path) {
            if ($request->hasFile($field)) {
                // Ambil file
                $file = $request->file($field);
                
                // Buat nama unik
                $fileName = Str::random(10) . $file->getClientOriginalName();
                
                // Simpan file baru
                $file->storeAs($path, $fileName, 'public');
                
                // Update array data untuk database
                $data[$field] = $fileName;

                // Hapus foto lama jika memang ada file lama di database
                if ($employee->$field) {
                    Storage::disk('public')->delete($path . '/' . $employee->$field);
                }
            } else {
                // Jika tidak ada file baru yang diunggah, 
                // hapus field ini dari array $data agar tidak menimpa data lama dengan null
                unset($data[$field]);
            }
        }

        $employee->update($data);

        Alert::success('Success Update Data Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.index');
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
            $employee = Employees::findOrFail($id);

            $employee->update([
                'hapus_oleh' => auth()->user()->name
            ]);

            $fileFields = [
                'foto_karyawan' => 'assets/foto/karyawan',
                'foto_ktp'      => 'assets/foto/ktp',
                'foto_npwp'     => 'assets/foto/npwp',
                'foto_kk'       => 'assets/foto/kk',
            ];

            // Proses penghapusan file fisik dari storage
            foreach ($fileFields as $field => $path) {
                if ($employee->$field) {
                    Storage::disk('public')->delete($path . '/' . $employee->$field);
                }
            }

            $employee->delete();
        });

        // Notifikasi dan Redirect
        Alert::success('Success Hapus Data Karyawan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.index');
    }

    public function aktif_kerja($id)
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        
        $item           = Employees::findOrFail($id);
        $companies      = Companies::all();
        $divisions      = Divisions::all();
        $positions      = Positions::all();
        $workinghours   = WorkingHours::all();
        $areas          = Areas::all();

        //Create Nomor Dokumen
        $nik_karyawan   = $item->nik_karyawan;
        $nomor          = substr($nik_karyawan, 6,6);
        $mytime         = Carbon::now();
        $bulan          = substr($mytime, 5, -12);
        $tahun          = substr($mytime, 0,4);
        if ($bulan == 1) {
            $romawi = 'I';
        }
        elseif ($bulan == 2) {
            $romawi = 'II';
        } 
        elseif ($bulan == 3) {
            $romawi = 'III';
        } 
        elseif ($bulan == 4) {
            $romawi = 'IV';
        } 
        elseif ($bulan == 5) {
            $romawi = 'V';
        } 
        elseif ($bulan == 6) {
            $romawi = 'VI';
        } 
        elseif ($bulan == 7) {
            $romawi = 'VII';
        } 
        elseif ($bulan == 8) {
            $romawi = 'VIII';
        } 
        elseif ($bulan == 9) {
            $romawi = 'IX';
        } 
        elseif ($bulan == 10) {
            $romawi = 'X';
        } 
        elseif ($bulan == 11) {
            $romawi = 'XI';
        } 
        elseif ($bulan == 12) {
            $romawi = 'XII';
        } 
        else {
            $romawi = 'SALAH';
        }
        //Create Nomor Dokumen

        $this->fpdf = new FPDF('P', 'mm', 'A4');
        $this->fpdf->setTopMargin(2);
        $this->fpdf->setLeftMargin(2);
        $this->fpdf->SetAutoPageBreak(false);
        $this->fpdf->AddPage();

        $this->fpdf->Cell(205, 293, '', 0, 0, 'C');
        $this->fpdf->SetFont('Arial', 'B', '8');
        $this->fpdf->Ln(30);
        $this->fpdf->SetFont('Arial', 'BU', '18');
        $this->fpdf->Cell(3);
        $this->fpdf->Cell(200, 10, 'SURAT KETERANGAN', 0, 0, 'C'); 
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', 'B', '14');
        $this->fpdf->Cell(3);
        $this->fpdf->Cell(200, 10, 'No.' . $nomor . '/HRD/PK/'. $romawi . '/'.$tahun.'', 0, 0, 'C');
        $this->fpdf->Ln(30);

        $this->fpdf->SetFont('Arial', '', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(100, 10, 'Kami Yang Bertanda Tangan Dibawah Ini :', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Achmad Firmansyah', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Jabatan', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : Manager (HRD-GA)', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Menerangkan Bahwa', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, '  ', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Nama', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->nama_karyawan . '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'NIK ', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->nik_karyawan. '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Jabatan / Penempatan ', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . $item->positions->jabatan . ' / ' . $item->divisions->penempatan . '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(50, 10, 'Tanggal Mulai Kerja', 0, 0, 'L');
        $this->fpdf->Cell(100, 10, ' : ' . \Carbon\Carbon::parse($item->tanggal_mulai_kerja)->isoformat('D MMMM Y') . '', 0, 0, 'L');
        $this->fpdf->Ln(9);

        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Adalah benar yang bersangkutan masih bekerja dan aktif menjadi karyawan kami,', 0, 0, 'L');
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'dengan jabatan dan masa kerja di atas.', 0, 0, 'L');

        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Demikianlah surat keterangan ini kami buat untuk digunakan dengan seperlunya.', 0, 0, 'L');

        $this->fpdf->Ln(10);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Tangerang Selatan, ' . \Carbon\Carbon::now()->isoformat('D MMMM Y') . '', 0, 0, 'L');

        $this->fpdf->Ln(5);
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Hormat kami,', 0, 0, 'L');

        $this->fpdf->Ln(38);

        $this->fpdf->SetFont('Arial', 'BU', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Achmad Firmansyah', 0, 0, 'L');

        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('Arial', 'B', '12');
        $this->fpdf->Cell(15);
        $this->fpdf->Cell(180, 10, 'Manager (HRD-GA)', 0, 0, 'L');

        $this->fpdf->Output();

        exit;
    }
}

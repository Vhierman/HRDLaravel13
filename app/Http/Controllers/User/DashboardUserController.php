<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\BulanRequest;
use App\Models\Admin\Employees;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use App\Models\Admin\Overtimes;
use Codedge\Fpdf\Fpdf\Fpdf;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $allowedRoles = ['karyawan'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        toast('Hello ' . auth()->user()->name, 'success');

        $employee = Employees::with([
                            'divisions',
                            'companies',
                            'positions',
                            'areas',
                            ])->where('nik_karyawan',auth()->user()->nik)->first();

        $today                  = Carbon::today();
        $tanggal_mulai_kerja    = Carbon::parse($employee->tanggal_mulai_kerja);
        $tanggal_lahir    = Carbon::parse($employee->tanggal_lahir);
        $MasaKerja              = $tanggal_mulai_kerja->diff($today)->format('%y');
        $UmurLengkap            = $tanggal_lahir->diff($today)->format('%y');

        


        return view('user.dashboard_user',[
            'employee'=>$employee,
            'MasaKerja'=>$MasaKerja,
            'UmurLengkap'=>$UmurLengkap
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $allowedRoles = ['karyawan'];
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
        $allowedRoles = ['karyawan'];
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
        $allowedRoles = ['karyawan'];
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
        $allowedRoles = ['karyawan'];
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
        $allowedRoles = ['karyawan'];
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
        $allowedRoles = ['karyawan'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    }

    public function overtime(BulanRequest $request)
    {
        $allowedRoles = ['karyawan'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $bulan = $request->bulan;
        $tahun = date('Y');

        // tanggal akhir = 15 bulan yang dipilih
        $tanggalAkhir = Carbon::create($tahun, $bulan, 15);

        // tanggal awal = 16 bulan sebelumnya
        $tanggalAwal = Carbon::create($tahun, $bulan, 16)
                        ->subMonth();

        $itemEmployee       = Employees::with([
                                                'areas',
                                                'golongans',
                                                'positions',
                                                'divisions',
                                                'overtimes' => function ($query) use ($tanggalAwal, $tanggalAkhir) {
                                                $query->whereNotNull('acc_hrd')
                                                        ->whereBetween('tanggal_lembur', [$tanggalAwal, $tanggalAkhir]);
                                                },
                                                'rekap_salaries' => function ($query) use ($bulan, $tahun) {
                                                $query->whereMonth('periode_akhir', $bulan)
                                                        ->whereYear('periode_akhir', $tahun);
                                                }
                                                ])->where('nik_karyawan',auth()->user()->nik)->first();
        $items =     Overtimes::with([
                        'employees',
                    ])
                        ->where('acc_hrd', '<>', NULL)
                        ->where('nik_karyawan', auth()->user()->nik)
                        ->where('deleted_at', NULL)
                        ->whereBetween('tanggal_lembur', [$tanggalAwal, $tanggalAkhir])
                        ->orderBy('tanggal_lembur')
                        ->get();

        if (!$itemEmployee || $items->isEmpty()) 
        {
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'Data Belum Tersedia Atau Belum Direkap Oleh HRD');
        }
        else
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
            $this->fpdf->Cell(20, 1, "Periode " . \Carbon\Carbon::parse($tanggalAwal)->isoformat('D MMMM Y') . " s/d " . \Carbon\Carbon::parse($tanggalAkhir)->isoformat('D MMMM Y') . "", 0, 0, 'C');

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
                $tahunlembur        = \Carbon\Carbon::parse($tanggalAwal)->isoformat('YYYY');

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
            $jumlahuanglembur       = $jumlahjamlembur *  $itemEmployee->rekap_salaries->first()?->upah_lembur_perjam ?? 0;
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
}

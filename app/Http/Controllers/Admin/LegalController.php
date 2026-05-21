<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\LegalRequest;
use App\Models\Admin\Legals;
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

class LegalController extends Controller
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

        $legals = Legals::all();

        return view('admin.pages.legal.index',[
            'legals' => $legals
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

        return view('admin.pages.legal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LegalRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except('_token');
        Legals::create([
            'nama_perijinan'    => $request->input('nama_perijinan'),
            'nomor_perijinan'   => $request->input('nomor_perijinan'),
            'instansi_penerbit' => $request->input('instansi_penerbit'),
            'tanggal_berlaku'   => $request->input('tanggal_berlaku'),
            'tanggal_habis'     => $request->input('tanggal_habis'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Perijinan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
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

        $legal = Legals::findOrFail($id);
        return view('admin.pages.legal.edit',[
        'legal' => $legal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LegalRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $legal  = Legals::findOrFail($id);
        $legal->update([
            'nama_perijinan'    => $request->input('nama_perijinan'),
            'nomor_perijinan'   => $request->input('nomor_perijinan'),
            'instansi_penerbit' => $request->input('instansi_penerbit'),
            'tanggal_berlaku'   => $request->input('tanggal_berlaku'),
            'tanggal_habis'     => $request->input('tanggal_habis'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Perijinan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
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
            $legal = Legals::findOrFail($id);
            $legal->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $legal->delete();
        });
        Alert::error('Menghapus Data Perijinan Perusahaan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
    }

    public function exportExcel()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Perijinan');
        $sheet->setCellValue('C1', 'Nomor Perijinan');
        $sheet->setCellValue('D1', 'Instansi Penerbit');
        $sheet->setCellValue('E1', 'Tanggal Dikeluarkan');
        $sheet->setCellValue('F1', 'Tanggal Habis');
        // Header

        //Style
        $sheet->getStyle('A1:F1')->applyFromArray([
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

        $legals = Legals::all();
        
        $row = 2;
        $no = 1;
        foreach ($legals as $legal) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, "'".$legal->nama_perijinan);
                $sheet->setCellValue('C'.$row,  "'".$legal->nomor_perijinan);
                $sheet->setCellValue('D'.$row, $legal->instansi_penerbit);
                $sheet->setCellValue('E'.$row,  "'".$legal->tanggal_berlaku);
                $sheet->setCellValue('F'.$row,  "'".$legal->tanggal_habis);                
                $row++;
                $no++;
        }

         // Border seluruh data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $sheet->getStyle("A1:F{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
                $sheet->getStyle("A2:F{$lastRow}")
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

        $filename = 'DataPerijinanPerusahaan.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function notifPerijinanHabis()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $today = Carbon::today();
        $legals = Legals::whereDate('tanggal_habis', '<', $today)->get();
        return view('admin.pages.legal.index',[
            'legals' => $legals
        ]);
    }
    
    public function notifPerijinanAkanHabis()
    {
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd' && auth()->user()->roles != 'accounting') {
            abort(403);
        }

        $today = Carbon::today();
        $legals = Legals::whereBetween('tanggal_habis', [
                            $today,$today->copy()->addDays(30)
                        ])->get();
        return view('admin.pages.legal.index',[
            'legals' => $legals
        ]);
    }
}

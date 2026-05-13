<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\HistoryPositionRequest;
use App\Models\Admin\HistoryPositions;
use App\Models\Admin\Employees;
use App\Models\Admin\Companies;
use App\Models\Admin\Divisions;
use App\Models\Admin\Positions;
use App\Models\Admin\Areas;
use App\Models\Admin\Golongans;
use App\Models\Admin\WorkingHours;
use DB;
use Alert;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;

class HistoryPositionController extends Controller
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HistoryPositionRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data           = $request->except('_token');
        $employees      = null;
        DB::transaction(function () use ($request, &$employees) {
            HistoryPositions::create([
                'employees_id'          => $request->input('employees_id'),
                'nik_karyawan'          => $request->input('nik_karyawan'),
                'companies_id_history'  => $request->input('companies_id'),
                'areas_id_history'      => $request->input('areas_id'),
                'divisions_id_history'  => $request->input('divisions_id'),
                'positions_id_history'  => $request->input('positions_id'),
                'tanggal_mutasi'        => $request->input('tanggal_mutasi'),
                'input_oleh'            => auth()->user()->name
            ]);

            $employees                  = Employees::where('nik_karyawan', $request->input('nik_karyawan'))->first();
            $employees->update([
                'companies_id'          => $request->input('companies_id'),
                'areas_id'              => $request->input('areas_id'),
                'divisions_id'          => $request->input('divisions_id'),
                'positions_id'          => $request->input('positions_id'),
                'edit_oleh'             => auth()->user()->name
            ]);
        });
        
        Alert::success('Success Input Data History Jabatan','Oleh '.auth()->user()->name);
        return redirect()->route('employee.show',$employees->id);
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
    public function update(HistoryPositionRequest $request, string $id)
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

        $history_positions = HistoryPositions::findOrFail($id);
        $employeeId = $history_positions->employees_id; 

        if (!$employeeId) {
            return redirect()->back()->with('error', 'Gagal menemukan ID Karyawan.');
        }
        DB::transaction(function () use ($history_positions) {
            $history_positions->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $history_positions->delete();
        });
        Alert::error('Menghapus Data History Jabatan', 'Oleh ' . auth()->user()->name);
        return redirect()->route('employee.show', ['employee' => $employeeId]);
    }

}

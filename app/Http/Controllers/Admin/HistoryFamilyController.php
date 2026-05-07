<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\HistoryFamilies;
use App\Models\Admin\Employees;
use App\Http\Requests\Admin\HistoryFamilyRequest;
use Alert;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistoryFamilyController extends Controller
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
    public function store(HistoryFamilyRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $employees      = Employees::where('nik_karyawan', $request->input('nik_karyawan'))->first();

        HistoryFamilies::create([
            'employees_id'                                          => $request->input('employees_id'),
            'nik_karyawan'                                          => $request->input('nik_karyawan'),
            'hubungan_keluarga'                                     => $request->input('hubungan_keluarga'),
            'nik_history_keluarga'                                  => $request->input('nik_history_keluarga'),
            'nomor_bpjs_kesehatan_history_keluarga'                 => $request->input('nomor_bpjs_kesehatan_history_keluarga'),
            'nama_history_keluarga'                                 => $request->input('nama_history_keluarga'),
            'jenis_kelamin_history_keluarga'                        => $request->input('jenis_kelamin_history_keluarga'),
            'tempat_lahir_history_keluarga'                         => $request->input('tempat_lahir_history_keluarga'),
            'tanggal_lahir_history_keluarga'                        => $request->input('tanggal_lahir_history_keluarga'),
            'golongan_darah_history_keluarga'                       => $request->input('golongan_darah_history_keluarga'),
            'input_oleh'                                            => auth()->user()->name
        ]);

        Alert::success('Success Input Data History Keluarga','Oleh '.auth()->user()->name);
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
    public function update(HistoryFamilyRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $history_family = HistoryFamilies::findOrFail($id);
        $nik_karyawan   = $history_family->nik_karyawan;
        $employee       = Employees::where('nik_karyawan', $nik_karyawan)->first();
        $data           = $request->all();
        $history_family->update([
            'employees_id'                          => $request->input('employees_id'),
            'nik_karyawan'                          => $request->input('nik_karyawan'),
            'hubungan_keluarga'                     => $request->input('hubungan_keluarga'),
            'nik_history_keluarga'                  => $request->input('nik_history_keluarga'),
            'nomor_bpjs_kesehatan_history_keluarga' => $request->input('nomor_bpjs_kesehatan_history_keluarga'),
            'nama_history_keluarga'                 => $request->input('nama_history_keluarga'),
            'jenis_kelamin_history_keluarga'        => $request->input('jenis_kelamin_history_keluarga'),
            'tempat_lahir_history_keluarga'         => $request->input('tempat_lahir_history_keluarga'),
            'tanggal_lahir_history_keluarga'        => $request->input('tanggal_lahir_history_keluarga'),
            'golongan_darah_history_keluarga'       => $request->input('golongan_darah_history_keluarga'),
            'edit_oleh'                             => Auth::user()->name
            ]);
        Alert::info('Success Edit Data History Keluarga','Oleh '.auth()->user()->name);
        return redirect()->route('employee.show',$employee->id);
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

        $history_family     = HistoryFamilies::findOrFail($id);
        $employeeId         = $history_family->employees_id; 

        if (!$employeeId) {
            return redirect()->back()->with('error', 'Gagal menemukan ID Karyawan.');
        }

        DB::transaction(function () use ($history_family) {
            $history_family->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $history_family->delete();
        });

        Alert::error('Menghapus Data History Keluarga', 'Oleh ' . auth()->user()->name);
        return redirect()->route('employee.show', ['employee' => $employeeId]);
    }
}

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
use Alert;
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

        $employees = Employees::all();
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

        //Foto Karyawan
        $foto_karyawan = $request->hasFile('foto_karyawan');
        $originalfoto_karyawan = Str::random(10).$foto_karyawan->getClientOriginalName();
        $foto_karyawan->storeAs('assets/foto/karyawan', $originalfoto_karyawan, 'public');
        $data['foto_karyawan'] = $originalfoto_karyawan;
        //Foto KTP
        $foto_ktp = $request->hasFile('foto_ktp');
        $originalfoto_ktp = Str::random(10).$foto_ktp->getClientOriginalName();
        $foto_ktp->storeAs('assets/foto/ktp', $originalfoto_ktp, 'public');
        $data['foto_ktp'] = $originalfoto_ktp;
        //Foto NPWP
        $foto_npwp = $request->hasFile('foto_npwp');
        $originalfoto_npwp = Str::random(10).$foto_npwp->getClientOriginalName();
        $foto_npwp->storeAs('assets/foto/npwp', $originalfoto_npwp, 'public');
        $data['foto_npwp'] = $originalfoto_npwp;
        //Foto KK
        $foto_kk = $request->hasFile('foto_kk');
        $originalfoto_kk = Str::random(10).$foto_kk->getClientOriginalName();
        $foto_kk->storeAs('assets/foto/kk', $originalfoto_kk, 'public');
        $data['foto_kk'] = $originalfoto_kk;

        Employees::create($data);
        // dd($data);

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
}

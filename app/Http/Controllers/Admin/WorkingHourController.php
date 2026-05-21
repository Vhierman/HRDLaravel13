<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\WorkingHourRequest;
use App\Models\Admin\WorkingHours;
use Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkingHourController extends Controller
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

        $working_hours = WorkingHours::all();
        return view('admin.pages.working_hour.index',[
            'working_hours' => $working_hours
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

        return view('admin.pages.working_hour.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkingHourRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        WorkingHours::create([
            'jam_masuk'     => $request->input('jam_masuk'),
            'jam_pulang'    => $request->input('jam_pulang'),
            'input_oleh'    => Auth::user()->name
            ]);
        Alert::success('Success Input Data Jam Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('working_hour.index');
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

        $working_hour = WorkingHours::findOrFail($id);
        return view('admin.pages.working_hour.edit',[
        'working_hour' => $working_hour
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkingHourRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        $working_hour = WorkingHours::findOrFail($id);
        $working_hour->update([
            'jam_masuk'     => $request->input('jam_masuk'),
            'jam_pulang'    => $request->input('jam_pulang'),
            'edit_oleh'    => Auth::user()->name
            ]);
        Alert::success('Success Update Data Jam Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('working_hour.index');
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
            $working_hour = WorkingHours::findOrFail($id);
            $working_hour->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $working_hour->delete();
        });
        Alert::error('Menghapus Data Jam Kerja','Oleh '.auth()->user()->name);
        return redirect()->route('working_hour.index');
    }
}

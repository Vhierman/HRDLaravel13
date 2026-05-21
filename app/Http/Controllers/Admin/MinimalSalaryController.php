<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\MinimalSalaryRequest;
use App\Models\Admin\MinimalSalaries;
use App\Models\Admin\Areas;
use Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MinimalSalaryController extends Controller
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

        $minimal_salaries = MinimalSalaries::with([
                'areas'
                ])->get();

        return view('admin.pages.minimal_salary.index',[
            'minimal_salaries' => $minimal_salaries
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

        $areas      = Areas::all();
        return view('admin.pages.minimal_salary.create',[
            'areas'         => $areas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MinimalSalaryRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        MinimalSalaries::create([
            'minimal_upah'      => $request->input('minimal_upah'),
            'areas_id'          => $request->input('areas_id'),
            'input_oleh'        => Auth::user()->name
            ]);

        Alert::success('Success Input Data Minimal Upah','Oleh '.auth()->user()->name);
        return redirect()->route('minimal_salary.index');
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

        $areas          = Areas::all();
        $minimal_salary = MinimalSalaries::findOrFail($id);
        
        return view('admin.pages.minimal_salary.edit',[
        'minimal_salary' => $minimal_salary,
        'areas'          => $areas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MinimalSalaryRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        $minimal_salary = MinimalSalaries::findOrFail($id);
        $minimal_salary->update([
            'minimal_upah'      => $request->input('minimal_upah'),
            'areas_id'          => $request->input('areas_id'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Minimal Upah','Oleh '.auth()->user()->name);
        return redirect()->route('minimal_salary.index');
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
            $minimal_salary = MinimalSalaries::findOrFail($id);
            $minimal_salary->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $minimal_salary->delete();
        });
        Alert::error('Menghapus Data Minimal Upah','Oleh '.auth()->user()->name);
        return redirect()->route('minimal_salary.index');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AreaRequest;
use App\Models\Admin\Areas;
use Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AreaController extends Controller
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

        $areas = Areas::all();
        return view('admin.pages.area.index',[
            'areas' => $areas
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
        
        return view('admin.pages.area.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AreaRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        Areas::create([
            'area'              => $request->input('area'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Area','Oleh '.auth()->user()->name);
        return redirect()->route('area.index');
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
    public function edit($id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
    
        $area = Areas::findOrFail($id);
        return view('admin.pages.area.edit',[
        'area' => $area
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AreaRequest $request, $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data_token = $request->except('_token');
        $area = Areas::findOrFail($id);
        $area->update([
            'area'              => $request->input('area'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::warning('Success Update Data Area','Oleh '.auth()->user()->name);
        return redirect()->route('area.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        DB::transaction(function () use ($id) {
            $area = Areas::findOrFail($id);
            $area->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $area->delete();
        });
        Alert::error('Menghapus Data Area','Oleh '.auth()->user()->name);
        return redirect()->route('area.index');
    }
}

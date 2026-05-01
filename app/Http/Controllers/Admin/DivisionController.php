<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DivisionRequest;
use App\Models\Admin\Divisions;
use Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
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

        $divisions = Divisions::all();
        return view('admin.pages.division.index',[
            'divisions' => $divisions
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
        return view('admin.pages.division.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DivisionRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data = $request->all();
        Divisions::create([
            'penempatan'        => $request->input('penempatan'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Penempatan','Oleh '.auth()->user()->name);
        return redirect()->route('division.index');
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

        $division = Divisions::findOrFail($id);
        return view('admin.pages.division.edit',[
        'division' => $division
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DivisionRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $division = Divisions::findOrFail($id);
        $division->update([
            'penempatan'        => $request->input('penempatan'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Penempatan','Oleh '.auth()->user()->name);
        return redirect()->route('division.index');
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
            $division = Divisions::findOrFail($id);
            $division->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $division->delete();
        });

        Alert::error('Menghapus Data Penempatan','Oleh '.auth()->user()->name);
        return redirect()->route('division.index');
    }
}

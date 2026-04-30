<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PositionRequest;
use App\Models\Admin\Positions;
use Alert;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
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

        $positions = Positions::all();
        return view('admin.pages.position.index',[
            'positions' => $positions
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
        return view('admin.pages.position.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        $data = $request->all();
        Positions::create([
            'jabatan'           => $request->input('jabatan'),
            'input_oleh'        => Auth::user()->name
            ]);

        Alert::success('Success Input Data Jabatan','Oleh '.auth()->user()->name);
        return redirect()->route('position.index');
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

        $position = Positions::findOrFail($id);
        return view('admin.pages.position.edit',[
        'position' => $position
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $position = Positions::findOrFail($id);
        $position->update([
            'jabatan'           => $request->input('jabatan'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Jabatan','Oleh '.auth()->user()->name);
        return redirect()->route('position.index');
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

        $position  = Positions::findOrFail($id);        
        $position->update([
            'hapus_oleh'    => auth()->user()->name
        ]);
        $position->delete();
        Alert::error('Menghapus Data Jabatan','Oleh '.auth()->user()->name);
        return redirect()->route('position.index');
    }
}

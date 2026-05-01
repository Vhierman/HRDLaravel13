<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\GolonganRequest;
use App\Models\Admin\Golongans;
use Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GolonganController extends Controller
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

        $golongans = Golongans::all();
        return view('admin.pages.golongan.index',[
            'golongans' => $golongans
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

        return view('admin.pages.golongan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GolonganRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data = $request->all();
        Golongans::create([
            'golongan'          => $request->input('golongan'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Golongan','Oleh '.auth()->user()->name);
        return redirect()->route('golongan.index');
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

        $golongan = Golongans::findOrFail($id);
        return view('admin.pages.golongan.edit',[
        'golongan' => $golongan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GolonganRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $golongan = Golongans::findOrFail($id);
        $golongan->update([
            'golongan'          => $request->input('golongan'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Golongan','Oleh '.auth()->user()->name);
        return redirect()->route('golongan.index');
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
            $golongan = Golongans::findOrFail($id);
            $golongan->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $golongan->delete();
        });
        Alert::error('Menghapus Data Golongan','Oleh '.auth()->user()->name);
        return redirect()->route('golongan.index');
    }
}

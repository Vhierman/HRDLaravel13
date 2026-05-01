<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\MaksimalUpahBpjsKetenagakerjaanRequest;
use App\Models\Admin\MaksimalUpahBpjsKetenagakerjaans;
use Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaksimalUpahBpjsKetenagakerjaanController extends Controller
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

        $maksimal_upah_bpjsketenagakerjaans = MaksimalUpahBpjsKetenagakerjaans::all();
        return view('admin.pages.maksimal_upah_bpjsketenagakerjaan.index',[
            'maksimal_upah_bpjsketenagakerjaans' => $maksimal_upah_bpjsketenagakerjaans
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

        return view('admin.pages.maksimal_upah_bpjsketenagakerjaan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaksimalUpahBpjsKetenagakerjaanRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data = $request->all();
        MaksimalUpahBpjsKetenagakerjaans::create([
            'maksimal_upah_bpjsketenagakerjaan'       => $request->input('maksimal_upah_bpjsketenagakerjaan'),
            'input_oleh'                        => Auth::user()->name
            ]);

        Alert::success('Success Input Data Maksimal Upah BPJS Ketenagakerjaan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjstk.index');
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

        $maksimal_upah_bpjsketenagakerjaan = MaksimalUpahBpjsKetenagakerjaans::findOrFail($id);
        return view('admin.pages.maksimal_upah_bpjsketenagakerjaan.edit',[
        'maksimal_upah_bpjsketenagakerjaan' => $maksimal_upah_bpjsketenagakerjaan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaksimalUpahBpjsKetenagakerjaanRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $maksimal_upah_bpjsketenagakerjaan = MaksimalUpahBpjsKetenagakerjaans::findOrFail($id);
        $maksimal_upah_bpjsketenagakerjaan->update([
            'maksimal_upah_bpjsketenagakerjaan'         => $request->input('maksimal_upah_bpjsketenagakerjaan'),
            'edit_oleh'                                 => Auth::user()->name
            ]);
        Alert::success('Success Update Data Maksimal Upah BPJS Ketenagakerjaan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjstk.index');
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
            $maksimal_upah_bpjsketenagakerjaan = MaksimalUpahBpjsKetenagakerjaans::findOrFail($id);
            $maksimal_upah_bpjsketenagakerjaan->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $maksimal_upah_bpjsketenagakerjaan->delete();
        });
        Alert::error('Menghapus Data Maksimal Upah BPJS Ketenagakerjaan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjstk.index');
    }
}

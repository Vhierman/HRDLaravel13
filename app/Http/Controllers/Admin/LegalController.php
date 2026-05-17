<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\LegalRequest;
use App\Models\Admin\Legals;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LegalController extends Controller
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

        $legals = Legals::all();

        return view('admin.pages.legal.index',[
            'legals' => $legals
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

        return view('admin.pages.legal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LegalRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data       = $request->except('_token');
        Legals::create([
            'nama_perijinan'    => $request->input('nama_perijinan'),
            'nomor_perijinan'   => $request->input('nomor_perijinan'),
            'instansi_penerbit' => $request->input('instansi_penerbit'),
            'tanggal_berlaku'   => $request->input('tanggal_berlaku'),
            'tanggal_habis'     => $request->input('tanggal_habis'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Perijinan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
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

        $legal = Legals::findOrFail($id);
        return view('admin.pages.legal.edit',[
        'legal' => $legal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LegalRequest $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        $legal  = Legals::findOrFail($id);
        $legal->update([
            'nama_perijinan'    => $request->input('nama_perijinan'),
            'nomor_perijinan'   => $request->input('nomor_perijinan'),
            'instansi_penerbit' => $request->input('instansi_penerbit'),
            'tanggal_berlaku'   => $request->input('tanggal_berlaku'),
            'tanggal_habis'     => $request->input('tanggal_habis'),
            'edit_oleh'         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Perijinan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
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
            $legal = Legals::findOrFail($id);
            $legal->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $legal->delete();
        });
        Alert::error('Menghapus Data Perijinan Perusahaan','Oleh '.auth()->user()->name);
        return redirect()->route('legal.index');
    }
}

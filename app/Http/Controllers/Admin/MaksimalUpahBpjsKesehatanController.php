<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\MaksimalUpahBpjsKesehatanRequest;
use App\Models\Admin\MaksimalUpahBpjsKesehatans;
use Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaksimalUpahBpjsKesehatanController extends Controller
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

        $maksimal_upah_bpjskesehatans = MaksimalUpahBpjsKesehatans::all();
        return view('admin.pages.maksimal_upah_bpjskesehatan.index',[
            'maksimal_upah_bpjskesehatans' => $maksimal_upah_bpjskesehatans
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

        return view('admin.pages.maksimal_upah_bpjskesehatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaksimalUpahBpjsKesehatanRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        MaksimalUpahBpjsKesehatans::create([
            'maksimal_upah_bpjskesehatan'       => $request->input('maksimal_upah_bpjskesehatan'),
            'input_oleh'                        => Auth::user()->name
            ]);

        Alert::success('Success Input Data Maksimal Upah BPJS Kesehatan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjskesehatan.index');
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

        $maksimal_upah_bpjskesehatan = MaksimalUpahBpjsKesehatans::findOrFail($id);
        return view('admin.pages.maksimal_upah_bpjskesehatan.edit',[
        'maksimal_upah_bpjskesehatan' => $maksimal_upah_bpjskesehatan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaksimalUpahBpjsKesehatanRequest $request, string $id)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        $maksimal_upah_bpjskesehatan = MaksimalUpahBpjsKesehatans::findOrFail($id);
        $maksimal_upah_bpjskesehatan->update([
            'maksimal_upah_bpjskesehatan'       => $request->input('maksimal_upah_bpjskesehatan'),
            'edit_oleh'                         => Auth::user()->name
            ]);
        Alert::success('Success Update Data Maksimal Upah BPJS Kesehatan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjskesehatan.index');
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
            $maksimal_upah_bpjskesehatan = MaksimalUpahBpjsKesehatans::findOrFail($id);
            $maksimal_upah_bpjskesehatan->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $maksimal_upah_bpjskesehatan->delete();
        });
        Alert::error('Menghapus Data Maksimal Upah BPJS Kesehatan','Oleh '.auth()->user()->name);
        return redirect()->route('maksimal_upah_bpjskesehatan.index');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CompanyRequest;
use App\Models\Admin\Companies;
use Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
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

        $companies = Companies::all();
        return view('admin.pages.company.index',[
            'companies' => $companies
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
        return view('admin.pages.company.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        $data   = $request->except('_token');
        Companies::create([
            'nama_perusahaan'   => $request->input('nama_perusahaan'),
            'input_oleh'        => Auth::user()->name
            ]);
        Alert::success('Success Input Data Perusahaan','Oleh '.auth()->user()->name);
        return redirect()->route('company.index');
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
    public function edit($id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        $company = Companies::findOrFail($id);
        return view('admin.pages.company.edit',[
        'company' => $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request,$id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
        $data   = $request->except('_token');
        $company = Companies::findOrFail($id);
        $company->update([
            'nama_perusahaan'   => $request->input('nama_perusahaan'),
            'edit_oleh'         => Auth::user()->name
            ]);

        Alert::success('Success Update Data Perusahaan','Oleh '.auth()->user()->name);
        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }

        DB::transaction(function () use ($id) {
            $company = Companies::findOrFail($id);
            $company->update([
                'hapus_oleh' => auth()->user()->name
            ]);
            $company->delete();
        });

        Alert::error('Menghapus Data Perusahaan','Oleh '.auth()->user()->name);
        return redirect()->route('company.index');
    }
}

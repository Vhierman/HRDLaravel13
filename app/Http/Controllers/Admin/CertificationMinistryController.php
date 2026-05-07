<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CertificationMinistryController extends Controller
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        if (auth()->user()->roles != 'admin' && auth()->user()->roles != 'hrd') {
            abort(403);
        }
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
    }
}

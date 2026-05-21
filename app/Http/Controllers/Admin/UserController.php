<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        //Tampilkan Semua Data Dari Model User Kecuali User Yang Sedang Login
        $users = User::where('id', '!=', Auth::user()->id)->get();
        //Tampilkan View User
        return view('admin.pages.user.index',[
            'users' => $users
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

        return view('admin.pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        //
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        $data   = $request->except('_token');
        User::create([
        'name'         => $request->name,
        'nik'          => $request->nik,
        'email'        => $request->email,
        'password'     => Hash::make($request->password),
        'roles'        => $request->roles,
        'input_oleh'   => auth()->user()->name,
        ]);

        Alert::success('Success Input Data User','Oleh '.auth()->user()->name);
        return redirect()->route('user.index');
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
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        //Ambil Semua Data Dari Model User Berdasarkan ID
        $user = User::findOrFail($id);

        //Tampilkan View Edit User
        return view('admin.pages.user.edit',[
        'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }

        //Ambil Semua Data Dari Form
        $data   = $request->except('_token');

        //Ambil Semua Data Dari Model User Berdasarkan ID
        $user = User::findOrFail($id);

        // Jika Password Diisi
        if ($request->filled('password')) {
            $user->update([
            'name'          => $request->input('name'),
            'nik'           => $request->input('nik'),
            'email'         => $request->input('email'),
            'password'      => bcrypt($request->input('password')),
            'roles'         => $request->input('roles'),
            'edit_oleh'     => Auth::user()->name
            ]);
        }
        // Jika Password Tidak Diisi
        else{
            $user->update([
            'name'          => $request->input('name'),
            'nik'           => $request->input('nik'),
            'email'         => $request->input('email'),
            'roles'         => $request->input('roles'),
            'edit_oleh'     => Auth::user()->name
        ]);
        }
        
        Alert::success('Success Edit Data User', 'Oleh ' . auth()->user()->name);
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $allowedRoles = ['admin', 'hrd'];
        if (!in_array(auth()->user()->roles, $allowedRoles)) {
            abort(403);
        }
        
        User::find($id)->delete();
        Alert::error('Menghapus Data User','Oleh '.auth()->user()->name);
        return redirect()->route('user.index')->with('success', 'User deleted');
    }
}

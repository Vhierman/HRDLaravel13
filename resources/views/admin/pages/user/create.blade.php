@extends('admin.layouts.base')
@section('title', 'Dashboard Admin');

@section('content')
    {{-- <h1>User</h1> --}}

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Master</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">

        {{-- Pesan Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Pesan Error --}}

        <div class="card-body p-4">
            <h5 class="mb-4">Form Tamnbah User</h5>
            <form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Nama</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" onkeyup="huruf(this);" name="name"
                            old="{{ old('name') }}" id="input35" placeholder="Name" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input36" class="col-sm-3 col-form-label">NIK</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" onkeyup="angka(this);" name="nik"
                            old="{{ old('nik') }}" id="input36" placeholder="NIK" maxlength="16" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input36" class="col-sm-3 col-form-label">Email Address</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" name="email" old="{{ old('email') }}" id="input36"
                            placeholder="Email" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input36" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="password" id="input36" placeholder="Password" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input39" class="col-sm-3 col-form-label">Roles</label>
                    <div class="col-sm-9">

                        <select id="input39" class="form-select" name="roles">
                            <option value="">Pilih Roles</option>
                            <option value="admin">
                                Admin</option>
                            <option value="karyawan">
                                Karyawan</option>
                            <option value="hrd">
                                HRD</option>
                            <option value="accounting">
                                Accounting
                            </option>
                            <option value="leader">
                                Leader
                            </option>
                            <option value="supervisor">
                                Supervisor
                            </option>
                            <option value="manager">
                                Manager
                            </option>
                        </select>

                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-9">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('user.index') }}" class="btn btn-danger px-4 raised d-flex gap-2">
                                        <i class="material-icons-outlined">cancel</i>Cancel
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>
@endsection

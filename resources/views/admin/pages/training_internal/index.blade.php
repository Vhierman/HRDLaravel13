@extends('admin.layouts.base')
@section('title', 'Data Training');
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Training</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Training Internal</li>
                </ol>
            </nav>
        </div>
    </div>
    @php
        $userRole = Auth::user()->roles;
        $canManage = in_array($userRole, ['admin', 'hrd']);
        $canView = in_array($userRole, ['leader']);
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-4">
                    <a href="{{ route('training_internal.view_tanggal') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Berdasarkan Tanggal</a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('training_internal.view_nama') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Berdasarkan Nama</a>
                </div>
                @if ($canManage)
                    <div class="col-md-4">
                        <a href="{{ route('training_internal.view_penempatan') }}"
                            class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Berdasarkan Penempatan</a>
                    </div>
                @endif

                <div class="col-md-4">
                    <a href="{{ route('training_internal.view_materi') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Berdasarkan Materi Training</a>
                </div>
                @if ($canManage)
                    <div class="col-md-4">
                        <a href="{{ route('training_internal.create') }}"
                            class="btn btn-grd btn-grd-success px-5 btn-lg py-3 w-100">Tambah Data</a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('training_internal.form_edit_tanggal') }}"
                            class="btn btn-grd btn-grd-warning px-5 btn-lg py-3 w-100">Edit
                            Data</a>
                    </div>
                @endif
                @if ($canManage)
                    <div class="col-md-6">
                        <a href="{{ route('training_internal.form_belum_training') }}"
                            class="btn btn-grd btn-primary px-5 btn-lg py-3 w-100">Lihat Data Karyawan Belum Pernah
                            Training</a>
                    </div>
                @endif
                @if ($canView)
                    <div class="col-md-12">
                        <a href="{{ route('training_internal.form_belum_training') }}"
                            class="btn btn-grd btn-primary px-5 btn-lg py-3 w-100">Lihat Data Karyawan Belum Pernah
                            Training</a>
                    </div>
                @endif
                @if ($canManage)
                    <div class="col-md-6">
                        <a href="{{ route('training_internal.form_hapus_tanggal') }}"
                            class="btn btn-grd btn-grd-danger px-5 btn-lg py-3 w-100">Hapus Data Training Internal</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

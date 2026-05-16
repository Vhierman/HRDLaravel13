@extends('admin.layouts.base')
@section('title', 'Data Training');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtimes</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Overtime Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-3">
                    <a href="{{ route('overtime.lihat_overtime') }}" class="btn btn-primary px-5 btn-lg py-3 w-100">Lihat
                        Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.create') }}" class="btn btn-success px-5 btn-lg py-3 w-100">Tambah Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_edit_overtime') }}" class="btn btn-warning px-5 btn-lg py-3 w-100">Edit
                        Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_hapus_overtime') }}"
                        class="btn btn-danger px-5 btn-lg py-3 w-100">Hapus Data</a>
                </div>
            </div>
            <div class="row justify-content-center g-3 text-center mt-2">
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_approve_overtime') }}"
                        class="btn btn-primary px-5 btn-lg py-3 w-100">Rekap Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_cetak_slip_overtime') }}"
                        class="btn btn-success px-5 btn-lg py-3 w-100">Cetak Slip</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_cetak_rekap_overtime') }}"
                        class="btn btn-warning px-5 btn-lg py-3 w-100">Cetak Rekap</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('overtime.form_cancel_approve_overtime') }}"
                        class="btn btn-danger px-5 btn-lg py-3 w-100">Cancel Rekap</a>
                </div>
            </div>
        </div>
    </div>
@endsection

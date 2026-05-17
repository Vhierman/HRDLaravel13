@extends('admin.layouts.base')
@section('title', 'Data Gaji');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Gaji</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Gaji Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-6">
                    <a href="{{ route('salary.rekon_salary') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Proses Rekon Gaji Karyawan</a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('salary.rekap_salary') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Tampil Rekap Gaji Karyawan</a>
                </div>
            </div>
        </div>
    </div>
@endsection

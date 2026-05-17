@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Kontrak Kerja');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Kontrak Kerja</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Karyawan Harian</li>
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
            <h5 class="mb-4">Proses Kontrak Kerja Karyawan Harian Berdasarkan Nama</h5>
            <form action="{{ route('kontrak_kerja.proses_nama_harian') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-12">
                        <label class="form-label fs-6">Tanggal Berakhir Kontrak Kerja</label>
                        <select name="employees_id[]" class="form-select" id="multiple-select-custom-field"
                            data-placeholder="Pilih Nama Karyawan" multiple>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employees_id', [])) ? 'selected' : '' }}>
                                    {{ $employee->nik_karyawan }} - {{ $employee->nama_karyawan }} -
                                    {{ $employee->divisions->penempatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Awal Perpanjang Kontrak</label>
                        <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Akhir Perpanjang Kontrak</label>
                        <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2">Perpanjang
                                        Kontrak</button>
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
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('template_admin/assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        $('#karyawan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

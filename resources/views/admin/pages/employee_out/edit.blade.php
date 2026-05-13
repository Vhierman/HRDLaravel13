@section('css')
    <link href="{{ asset('template_admin/assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Karyawan Keluar');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Data Karyawan Keluar</li>
                </ol>
            </nav>
        </div>
    </div>

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

    <div id="stepper1" class="bs-stepper">
        <div class="card">

            <div class="card-body p-4">
                <h5 class="mb-4">Form Edit Karyawan Keluar</h5>
                <form action="{{ route('employee_out.update', $item_employee_out->id) }}" method="post"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">NIK Karyawan</label>
                        <div class="col-sm-9">
                            <input type="text" name="nik_karyawan_keluar"
                                value="{{ $item_employee_out->nik_karyawan_keluar }}" class="form-control"
                                placeholder="Masukan NIK Karyawan Keluar" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Nama Karyawan</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_karyawan_keluar"
                                value="{{ $item_employee_out->nama_karyawan_keluar }}" class="form-control"
                                placeholder="Masukan Nama Karyawan Keluar" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Keterangan Keluar</label>
                        <div class="col-sm-9">
                            <select id="input39" class="form-select" name="keterangan_keluar">
                                <option value="">Pilih Keterangan Keluar</option>
                                <option value="Berakhir Kontrak Kerja"
                                    @if ($item_employee_out->keterangan_keluar == 'Berakhir Kontrak Kerja') {{ 'selected="selected"' }} @endif>
                                    Berakhir Kontrak Kerja</option>
                                <option value="Pengunduran Diri"
                                    @if ($item_employee_out->keterangan_keluar == 'Pengunduran Diri') {{ 'selected="selected"' }} @endif>
                                    Pengunduran Diri</option>
                                <option value="Pemutusan Hubungan Kerja"
                                    @if ($item_employee_out->keterangan_keluar == 'Pemutusan Hubungan Kerja') {{ 'selected="selected"' }} @endif>
                                    Pemutusan Hubungan Kerja</option>
                                <option value="Memasuki Usia Pensiun"
                                    @if ($item_employee_out->keterangan_keluar == 'Memasuki Usia Pensiun') {{ 'selected="selected"' }} @endif>
                                    Memasuki Usia Pensiun
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Tanggal Keluar</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_keluar_karyawan_keluar"
                                value="{{ $item_employee_out->tanggal_keluar_karyawan_keluar }}" class="form-control"
                                placeholder="dd/mm/yyyy">
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
                                        <a href="{{ route('employee_out.index') }}"
                                            class="btn btn-danger px-4 raised d-flex gap-2">
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
    </div>

@endsection

@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>

    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/main.js') }}"></script>
@endsection

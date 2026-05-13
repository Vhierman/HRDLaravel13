@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection
@extends('admin.layouts.base')
@section('title', 'Tambah Karyawan Keluar');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Tambah Karyawan Keluar</li>
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
                <h5 class="mb-4">Form Tambah Karyawan Keluar</h5>
                <form action="{{ route('employee_out.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Nama</label>
                        <div class="col-sm-9">
                            <select name="employee_id" class="form-select" id="nama_karyawan_keluar-select"
                                data-placeholder="Pilih Karyawan">
                                <option value="">Pilih Karyawan</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Keterangan Keluar</label>
                        <div class="col-sm-9">
                            <select id="input39" class="form-select" name="keterangan_keluar">
                                <option value="">Pilih Keterangan Keluar</option>
                                <option value="Berakhir Kontrak Kerja"
                                    {{ old('keterangan_keluar') == 'Berakhir Kontrak Kerja' ? 'selected' : '' }}>
                                    Berakhir Kontrak Kerja</option>
                                <option value="Pengunduran Diri"
                                    {{ old('keterangan_keluar') == 'Pengunduran Diri' ? 'selected' : '' }}>
                                    Pengunduran Diri</option>
                                <option value="Pemutusan Hubungan Kerja"
                                    {{ old('keterangan_keluar') == 'Pemutusan Hubungan Kerja' ? 'selected' : '' }}>
                                    Pemutusan Hubungan Kerja</option>
                                <option value="Memasuki Usia Pensiun"
                                    {{ old('keterangan_keluar') == 'Memasuki Usia Pensiun' ? 'selected' : '' }}>
                                    Memasuki Usia Pensiun
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Tanggal Keluar</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_keluar_karyawan_keluar"
                                value="{{ old('tanggal_keluar_karyawan_keluar') }}" class="form-control"
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
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('template_admin/assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        $('#nama_karyawan_keluar-select').select2({
            theme: 'bootstrap-5'
        });
    </script>
@endsection

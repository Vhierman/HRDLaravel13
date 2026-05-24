@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Tambah Data Overtime');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtime</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Tambah Overtime</li>
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
            <h5 class="mb-4">Form Tambah Overtime</h5>
            <form action="{{ route('overtime.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Lembur</label>
                        <input type="date" name="tanggal_lembur" value="{{ old('tanggal_lembur') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Keterangan Lembur</label>
                        <input type="text" name="keterangan_lembur" value="{{ old('keterangan_lembur') }}"
                            class="form-control" placeholder="Masukan Keterangan Lembur">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Masuk</label>
                        <input type="number" step="0.01"name="jam_masuk" value="{{ old('jam_masuk') }}"
                            class="form-control" placeholder="Masukan Jam Masuk">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Istirahat</label>
                        <input type="number" step="0.01" name="jam_istirahat" value="{{ old('jam_istirahat') }}"
                            class="form-control" placeholder="Masukan Jam Istirahat">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Pulang</label>
                        <input type="number" step="0.01" name="jam_pulang" value="{{ old('jam_pulang') }}"
                            class="form-control" placeholder="Masukan Jam Pulang">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jenis Lembur</label>
                        <select name="jenis_lembur" class="form-select">
                            <option value="">Pilih Jenis Lembur</option>
                            <option value="Biasa" @if (old('jenis_lembur') == 'Biasa') {{ 'selected' }} @endif>Biasa
                            </option>
                            <option value="Libur" @if (old('jenis_lembur') == 'Libur') {{ 'selected' }} @endif>Libur
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Uang Makan Lembur</label>
                        <select name="uang_makan_lembur" class="form-select">
                            <option value="">Dapat Uang Makan Lembur ?</option>
                            <option value="17000" @if (old('uang_makan_lembur') == 'Dapat Uang Makan') {{ 'selected' }} @endif>Dapat Uang
                                Makan
                            </option>
                            <option value="0" @if (old('uang_makan_lembur') == 'Tidak Dapat Uang Makan') {{ 'selected' }} @endif>Tidak Dapat
                                Uang Makan
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-12">
                        <label for="karyawan-select" class="form-label fs-6">Nama Karyawan</label>
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
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('overtime.index') }}"
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

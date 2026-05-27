@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Tambah Data Absensi');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Absensi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data Absensi</li>
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
            <h5 class="mb-4 fs-4">Form Tambah Absensi</h5>
            <form action="{{ route('attendance.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Tanggal Absen</label>
                        <input type="date" name="tanggal_absen" value="{{ old('tanggal_absen') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label for="karyawan-select" class="form-label fs-6">Keterangan Absen</label>
                        <select class="form-select" name="keterangan_absen" id="keterangan_absen-select"
                            data-placeholder="Pilih Keterangan Absen">
                            <option value="">Pilih Keterangan Absen</option>
                            <option value="Sakit" {{ old('keterangan_absen') == 'Sakit' ? 'selected' : '' }}>
                                Sakit</option>
                            <option value="Ijin" {{ old('keterangan_absen') == 'Ijin' ? 'selected' : '' }}>
                                Ijin</option>
                            <option value="Alpa" {{ old('keterangan_absen') == 'Alpa' ? 'selected' : '' }}>
                                Alpa</option>
                            <option value="Cuti Tahunan" {{ old('keterangan_absen') == 'Cuti Tahunan' ? 'selected' : '' }}>
                                Cuti Tahunan
                            </option>
                            <option value="Cuti Khusus" {{ old('keterangan_absen') == 'Cuti Khusus' ? 'selected' : '' }}>
                                Cuti Khusus
                            </option>
                            <option value="Cuti Panjang" {{ old('keterangan_absen') == 'Cuti Panjang' ? 'selected' : '' }}>
                                Cuti Panjang
                            </option>
                            <option value="OFF" {{ old('keterangan_absen') == 'OFF' ? 'selected' : '' }}>
                                OFF
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label for="karyawan-select" class="form-label fs-6">Keterangan</label>
                        <input type="text" name="keterangan_cuti_khusus" value="{{ old('keterangan_cuti_khusus') }}"
                            class="form-control" placeholder="Masukan Keterangan">
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
                <br>
                <div class="row g-3">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('attendance.index') }}"
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
        $('#keterangan_absen-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

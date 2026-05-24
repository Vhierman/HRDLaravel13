@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Tambah Data Kecelakaan Kerja');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Safety</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data Kecelakaan Kerja</li>
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
            <h5 class="mb-4">Form Tambah Data Kecelakaan Kerja</h5>
            <form action="{{ route('safety.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Kejadian</label>
                        <input type="date" name="tanggal_kecelakaan" value="{{ old('tanggal_kecelakaan') }}"
                            class="form-control" placeholder="Masukan Tanggal Kecelakaan">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Nama Karyawan</label>
                        <select name="employees_id" class="form-select" id="karyawan-select"
                            data-placeholder="Pilih Karyawan">
                            <option value="">Pilih Karyawan</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employees_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->nik_karyawan }} - {{ $employee->nama_karyawan }} -
                                    {{ $employee->divisions->penempatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Lokasi Kecelakaan</label>
                        <input type="text" name="lokasi_kecelakaan" value="{{ old('lokasi_kecelakaan') }}"
                            class="form-control" placeholder="Masukan Lokasi Kecelakaan">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jenis Kecelakaan</label>
                        <input type="text" name="jenis_kecelakaan" value="{{ old('jenis_kecelakaan') }}"
                            class="form-control" placeholder="Masukan Jenis Kecelakaan">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Kategori Kecelakaan</label>
                        <select id="input39" class="form-select" name="kategori_kecelakaan"
                            id="kategori_kecelakaan-select" data-placeholder="Pilih Kategori Kecelakaan">
                            <option value="">Pilih Kategori Kecelakaan</option>
                            <option value="Fatality" {{ old('kategori_kecelakaan') == 'Fatality' ? 'selected' : '' }}>
                                Fatality</option>
                            <option value="LWD" {{ old('kategori_kecelakaan') == 'LWD' ? 'selected' : '' }}>
                                LWD</option>
                            <option value="Non LWD" {{ old('kategori_kecelakaan') == 'Non LWD' ? 'selected' : '' }}>
                                Non LWD</option>
                            <option value="Traffic Accident"
                                {{ old('kategori_kecelakaan') == 'Traffic Accident' ? 'selected' : '' }}>
                                Traffic Accident
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jumlah Hari Hilang</label>
                        <input type="number" name="hari_hilang" value="{{ old('hari_hilang') }}" class="form-control"
                            placeholder="Masukan Jumlah Hari Hilang">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-12">
                        <label class="form-label fs-6">Status</label>
                        <select id="input39" class="form-select" name="status" id="status-select"
                            data-placeholder="Pilih Kategori Kecelakaan">
                            <option value="">Pilih Kategori Kecelakaan</option>
                            <option value="Sembuh" {{ old('status') == 'Sembuh' ? 'selected' : '' }}>
                                Sembuh</option>
                            <option value="Gangguan Kesehatan"
                                {{ old('status') == 'Gangguan Kesehatan' ? 'selected' : '' }}>
                                Gangguan Kesehatan</option>
                            <option value="Cacat" {{ old('status') == 'Cacat' ? 'selected' : '' }}>
                                Cacat</option>
                            <option value="Meninggal" {{ old('status') == 'Meninggal' ? 'selected' : '' }}>
                                Meninggal
                            </option>
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
                                    <a href="{{ route('safety.index') }}" class="btn btn-danger px-4 raised d-flex gap-2">
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
        $('#kategori_kecelakaan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        $('#status-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

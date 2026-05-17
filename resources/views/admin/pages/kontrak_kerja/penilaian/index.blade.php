@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Penilaian Karyawan');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Kontrak Kerja</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Penilaian Karyawan</li>
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
            <h5 class="mb-4">Penilaian Karyawan</h5>
            <form action="{{ route('kontrak_kerja.cetak_penilaian_karyawan') }}" target="_blank" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="status_kerja-select" class="form-label fs-6">Status Kerja</label>
                        <select class="form-select" name="status_kerja" id="status_kerja-select"
                            data-placeholder="Pilih Status Kerja">
                            <option value="">Pilih Status Kerja</option>
                            <option value="PKWT" {{ old('status_kerja') == 'PKWT' ? 'selected' : '' }}>
                                PKWT</option>
                            <option value="Harian" {{ old('status_kerja') == 'Harian' ? 'selected' : '' }}>
                                Harian</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="penempatan-select" class="form-label fs-6">Penempatan</label>
                        <select class="form-select" name="penempatan" id="penempatan-select"
                            data-placeholder="Pilih Penempatan">
                            <option value="">Pilih Penempatan</option>
                            <option value="PDC Daihatsu" {{ old('penempatan') == 'PDC Daihatsu' ? 'selected' : '' }}>
                                PDC Daihatsu</option>
                            <option value="Produksi" {{ old('penempatan') == 'Produksi' ? 'selected' : '' }}>
                                Produksi</option>
                            <option value="PPC" {{ old('penempatan') == 'PPC' ? 'selected' : '' }}>
                                PPC</option>
                            <option value="Quality" {{ old('penempatan') == 'Quality' ? 'selected' : '' }}>
                                Quality</option>
                            <option value="Office" {{ old('penempatan') == 'Office' ? 'selected' : '' }}>
                                Office</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Mulai Dari</label>
                        <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Sampai Tanggal</label>
                        <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">print</i>Cetak Penilaian Karyawan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('kontrak_kerja.form_penilaian') }}"
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
        $('#status_kerja-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        $('#penempatan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

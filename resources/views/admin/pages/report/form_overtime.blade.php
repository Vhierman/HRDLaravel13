@extends('admin.layouts.base')
@section('title', 'Karyawan Keluar');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Laporan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Overtime Karyawan</li>
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
            <h5 class="mb-4">Cari Data Overtime Karyawan</h5>
            <form action="{{ route('report.tampil_overtime') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Dari Tanggal</label>
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
                                            class="material-icons-outlined">visibility</i>Lihat Data</button>
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
        $('#tahun-select').select2({
            theme: 'bootstrap-5'
        });
    </script>
@endsection

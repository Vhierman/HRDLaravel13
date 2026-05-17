@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@extends('admin.layouts.base')
@section('title', 'Detail Karyawan')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Detail Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">

        <!-- PROFILE SECTION -->

        <div class="col-lg-4 col-12">
            <div class="card employee-card shadow-lg">

                <div class="position-relative">
                    <img src="{{ asset('storage/assets/profile_karyawan/PrimaFuture.png') }}" class="img-fluid rounded"
                        alt="">
                    <div class="position-absolute top-100 start-50 translate-middle">
                        <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}" width="150"
                            height="150" class="rounded-circle raised p-1 bg-white" alt="">
                    </div>
                </div>

                <!-- Profile Text -->
                <div class="text-center p-4 mt-5" style="margin-top:-70px;">

                    <h3 class="mt-3 fw-bold text-black">
                        {{ $employee->nama_karyawan }}
                    </h3>

                    <h4 class="mt-3 fw-bold text-black">
                        {{ $employee->positions->jabatan }} /
                        {{ $employee->divisions->penempatan }}
                    </h4>

                    <hr class="text-secondary">

                    <!-- Quick Stats -->
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h5 class="text-primary">Nama Perusahaan</h5>
                            <p class="mb-0">{{ $employee->companies->nama_perusahaan }}</p>
                        </div>
                        <div class="col-6">
                            <h5 class="text-danger">Area</h5>
                            <p class="mb-0">{{ $employee->areas->area }}</p>
                        </div>
                    </div>

                    <hr class="text-secondary">

                    <!-- Documents -->
                    <div class="text-center">
                        <div class="row row-cols-auto g-3 justify-content-center">
                            <div class="col-4">
                                <h5 class="text-info">Masa Kerja</h5>
                                <p class="mb-0">{{ $MasaKerja }}</p>
                            </div>

                            <div class="col-4">
                                <h5 class="text-success">Umur</h5>
                                <p class="mb-0">{{ $UmurLengkap }}</p>
                            </div>

                            <div class="col-4">
                                <h5 class="text-warning">Status Kerja</h5>
                                <p class="mb-0">{{ $employee->status_kerja }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END PROFILE SECTION -->

        <!-- DETAIL SECTION -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#primary-pills-karyawan" role="tab"
                                aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class="bi bi-person me-1 fs-6"></i>
                                    </div>
                                    <div class="tab-title">Karyawan</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#primary-pills-alamat" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class="bi bi-house-door me-1 fs-6"></i>
                                    </div>
                                    <div class="tab-title">Alamat</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#primary-pills-bpjs" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bi bi-bag-plus me-1 fs-6'></i>
                                    </div>
                                    <div class="tab-title">BPJS</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#primary-pills-history" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bi bi-tags me-1 fs-6'></i>
                                    </div>
                                    <div class="tab-title">History</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        {{-- Tab Karyawan --}}
                        <div class="tab-pane fade show active" id="primary-pills-karyawan" role="tabpanel">
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Tanggal Mulai Kerja</h6>
                                            <p class="mb-0">
                                                {{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoformat('D MMMM Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Tanggal Akhir Kerja</h6>
                                            <p class="mb-0">
                                                {{ \Carbon\Carbon::parse($employee->tanggal_akhir_kerja)->isoformat('D MMMM Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nomor Rekening</h6>
                                            <p class="mb-0">
                                                {{ $employee->nomor_rekening }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nama Bank</h6>
                                            <p class="mb-0">
                                                {{ $employee->nama_bank }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">NIK KTP</h6>
                                            <p class="mb-0">{{ $employee->nik_karyawan }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nomor NPWP</h6>
                                            <p class="mb-0">{{ $employee->nomor_npwp }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Tempat Tanggal Lahir</h6>
                                            <p class="mb-0">{{ $employee->tempat_lahir }} -
                                                {{ \Carbon\Carbon::parse($employee->tanggal_lahir)->isoformat('D MMMM Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Email Karyawan</h6>
                                            <p class="mb-0">{{ $employee->email_karyawan }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Agama</h6>
                                            <p class="mb-0">{{ $employee->agama }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Jenis Kelamin</h6>
                                            <p class="mb-0">{{ $employee->jenis_kelamin }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Pendidikan Terakhir</h6>
                                            <p class="mb-0">{{ $employee->pendidikan_terakhir }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Golongan Darah</h6>
                                            <p class="mb-0">{{ $employee->golongan_darah }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nomor Handphone</h6>
                                            <p class="mb-0">{{ $employee->nomor_handphone }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nomor KK</h6>
                                            <p class="mb-0">{{ $employee->nomor_kartu_keluarga }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Status Menikah</h6>
                                            <p class="mb-0">{{ $employee->status_nikah }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nomor Absen</h6>
                                            <p class="mb-0">{{ $employee->nomor_absen }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nama Ayah</h6>
                                            <p class="mb-0">{{ $employee->nama_ayah }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Nama Ibu</h6>
                                            <p class="mb-0">{{ $employee->nama_ibu }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab Karyawan --}}

                        {{-- Tab Alamat --}}
                        <div class="tab-pane fade  " id="primary-pills-alamat" role="tabpanel">
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Alamat, RT/RW</h6>
                                            <p class="mb-0">
                                                {{ $employee->alamat }}, {{ $employee->rt }}/{{ $employee->alamat }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Kelurahan</h6>
                                            <p class="mb-0">
                                                {{ $employee->kelurahan }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Kecamatan</h6>
                                            <p class="mb-0">
                                                {{ $employee->kecamatan }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Kabupaten/Kota</h6>
                                            <p class="mb-0">
                                                {{ $employee->kota }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Provinsi</h6>
                                            <p class="mb-0">
                                                {{ $employee->provinsi }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">Kode POS</h6>
                                            <p class="mb-0">
                                                {{ $employee->kode_pos }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- End Tab Alamat --}}

                        {{-- Tab BPJS --}}
                        <div class="tab-pane fade  " id="primary-pills-bpjs" role="tabpanel">
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-person-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">BPJS Kesehatan</h6>
                                            <p class="mb-0">
                                                {{ $employee->nomor_bpjskesehatan }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                        <div class="detail-icon fs-5">
                                            <i class="bi bi-house-vcard"></i>
                                        </div>
                                        <div class="detail-info">
                                            <h6 class="fw-bold mb-1">BPJS Ketenagakerjaan</h6>
                                            <p class="mb-0">
                                                {{ $employee->nomor_bpjsketenagakerjaan }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        {{-- End Tab BPJS --}}

                        {{-- Tab History --}}
                        <div class="tab-pane fade" id="primary-pills-history" role="tabpanel">
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                {{-- History Kontrak --}}
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded w-100">
                                        <div class="detail-info w-100">
                                            <button type="button"
                                                class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#ScrollableModalHistoryKontrak">
                                                <i class="material-icons-outlined">search</i>History Kontrak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{-- History Jabatan --}}
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded w-100">
                                        <div class="detail-info w-100">
                                            <button type="button"
                                                class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#ScrollableModalHistoryJabatan">
                                                <i class="material-icons-outlined">search</i>History Jabatan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Modal Table History Kontrak --}}
                            <div class="modal fade" id="ScrollableModalHistoryKontrak">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom-0 bg-grd-primary py-2">
                                            <h5 class="modal-title">Form History Kontrak
                                            </h5>
                                            <a href="#" class="primary-menu-close" data-bs-dismiss="modal">
                                                <i class="material-icons-outlined">close</i>
                                            </a>
                                        </div>
                                        <div class="modal-body">
                                            <div class="order-summary">
                                                <div class="card mb-0">
                                                    <div class="card-body">

                                                        @if ($employee->status_kerja != 'PKWTT')
                                                            {{-- Head --}}
                                                            <div class="card border bg-transparent shadow-none mb-3">
                                                                <div class="card-body">
                                                                    <p class="fs-5">
                                                                        {{ $employee->nama_karyawan }}
                                                                    </p>
                                                                    <button type="button"
                                                                        class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#ScrollableModalTambahHistoryKontrak">
                                                                        <i class="material-icons-outlined">add</i>Tambah
                                                                        Data History Kontrak
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            {{-- End Head --}}
                                                        @endif

                                                        {{-- Body --}}
                                                        <div class="card border bg-transparent shadow-none">
                                                            <div class="card-body ">
                                                                <div class="table-responsive">
                                                                    <table id="example2"
                                                                        class="table table-striped table-bordered w-100"
                                                                        style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>No</th>
                                                                                <th>Awal Kontrak</th>
                                                                                <th>Akhir Kontrak</th>
                                                                                <th>Status Kerja</th>
                                                                                <th>Masa Kontrak</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                            <tr class="search-row">
                                                                                <th></th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Awal Kontrak..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Akhir Kontrak..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Status Kerja..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Masa Kontrak..." />
                                                                                </th>

                                                                                <th></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $no = 1;
                                                                            @endphp
                                                                            @foreach ($history_contracts as $history_contract)
                                                                                <tr>
                                                                                    <td>{{ $no++ }}</td>
                                                                                    <td>{{ \Carbon\Carbon::parse($history_contract->tanggal_awal_kontrak)->isoformat('DD-MM-Y') }}
                                                                                    </td>
                                                                                    <td>{{ \Carbon\Carbon::parse($history_contract->tanggal_akhir_kontrak)->isoformat('DD-MM-Y') }}
                                                                                    </td>
                                                                                    <td>{{ $history_contract->status_kontrak_kerja }}
                                                                                    </td>
                                                                                    <td>{{ $history_contract->masa_kontrak }}
                                                                                    </td>

                                                                                    <td>
                                                                                        <div
                                                                                            class="row row-cols-auto g-3 justify-content-center">
                                                                                            <div class="col">
                                                                                                <a href="{{ route('cetak.pkwt', $history_contract->id) }}"
                                                                                                    class="btn btn-primary raised d-flex gap-2"
                                                                                                    target="_blank">
                                                                                                    <i
                                                                                                        class="material-icons-outlined">print</i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <div class="col">
                                                                                                <form
                                                                                                    action="{{ route('history_contract.destroy', $history_contract->id) }}"
                                                                                                    method="POST">
                                                                                                    @csrf
                                                                                                    @method('delete')
                                                                                                    <button
                                                                                                        class="btn btn-danger raised d-flex gap-2">
                                                                                                        <i
                                                                                                            class="material-icons-outlined">delete</i>
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- End Body --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Modal Body --}}
                                    </div>
                                    {{-- Modal Content --}}
                                </div>
                                {{-- Modal Dialog --}}
                            </div>
                            {{-- End Modal Table History Kontrak --}}
                            {{-- Modal Add History Kontrak --}}
                            <div class="modal fade" id="ScrollableModalTambahHistoryKontrak">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                                            <h5 class="modal-title">Form Tambah History Kontrak</h5>
                                            <a href="javascript:;" class="primary-menu-close" data-bs-dismiss="modal">
                                                <i class="material-icons-outlined">close</i>
                                            </a>
                                        </div>
                                        <div class="modal-body">
                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @endif

                                            <!-- Area Pesan Error Manual (Session) -->
                                            @if (session('error'))
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    {{ session('error') }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @endif
                                            <div class="form-body">
                                                <form action="{{ route('history_contract.store') }}"
                                                    id="inputHistoryContract" method="post"
                                                    enctype="multipart/form-data" class="row g-3">
                                                    @csrf
                                                    <input type="hidden" name="employees_id" class="form-control"
                                                        value="{{ $employee->id }}" id="inputHistoryContract" readonly
                                                        placeholder="Employees ID">
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryContract" class="form-label">NIK
                                                            Karyawan</label>
                                                        <input type="text" name="nik_karyawan" class="form-control"
                                                            value="{{ $employee->nik_karyawan }}"
                                                            id="inputHistoryContract" readonly placeholder="NIK">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryContract" class="form-label">Nama
                                                            Karyawan</label>
                                                        <input type="text" name="nama_karyawan"
                                                            value="{{ $employee->nama_karyawan }}" class="form-control"
                                                            id="inputHistoryContract" readonly
                                                            placeholder="Nama Karyawan">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryContract" class="form-label">Awal
                                                            Kontrak</label>
                                                        <input type="date" name="awal_kontrak" class="form-control"
                                                            id="inputHistoryContract" placeholder="dd/mm/yyyy">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryContract" class="form-label">Akhir
                                                            Kontrak</label>
                                                        <input type="date" name="akhir_kontrak" class="form-control"
                                                            id="inputHistoryContract" placeholder="dd/mm/yyyy">
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Status Kerja</label>
                                                        <select id="inputHistoryContract" class="form-select"
                                                            name="status_kerja">
                                                            <option value="">Pilih Status Kerja</option>
                                                            <option value="PKWTT"
                                                                {{ old('status_kerja') == 'PKWTT' ? 'selected' : '' }}>
                                                                PKWTT</option>
                                                            <option value="PKWT"
                                                                {{ old('status_kerja') == 'PKWT' ? 'selected' : '' }}>
                                                                PKWT</option>
                                                            <option value="Harian"
                                                                {{ old('status_kerja') == 'Harian' ? 'selected' : '' }}>
                                                                Harian</option>
                                                            <option value="Outsourcing"
                                                                {{ old('status_kerja') == 'Outsourcing' ? 'selected' : '' }}>
                                                                Outsourcing
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                                            <button type="submit"
                                                                class="btn btn-grd-danger px-4">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Modal Add History Kontrak --}}
                            {{-- History Kontrak --}}


                            {{-- Modal Table History Jabatan --}}
                            <div class="modal fade" id="ScrollableModalHistoryJabatan">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom-0 bg-grd-primary py-2">
                                            <h5 class="modal-title">Form History Jabatan
                                            </h5>
                                            <a href="#" class="primary-menu-close" data-bs-dismiss="modal">
                                                <i class="material-icons-outlined">close</i>
                                            </a>
                                        </div>
                                        <div class="modal-body">
                                            <div class="order-summary">
                                                <div class="card mb-0">
                                                    <div class="card-body">


                                                        {{-- Head --}}
                                                        <div class="card border bg-transparent shadow-none mb-3">
                                                            <div class="card-body">
                                                                <p class="fs-5">
                                                                    {{ $employee->nama_karyawan }}
                                                                </p>
                                                                <button type="button"
                                                                    class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#ScrollableModalTambahHistoryJabatan">
                                                                    <i class="material-icons-outlined">add</i>Tambah
                                                                    Data History Jabatan
                                                                </button>
                                                            </div>
                                                        </div>
                                                        {{-- End Head --}}


                                                        {{-- Body --}}
                                                        <div class="card border bg-transparent shadow-none">
                                                            <div class="card-body ">
                                                                <div class="table-responsive">
                                                                    <table id="example2"
                                                                        class="table table-striped table-bordered w-100"
                                                                        style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>No</th>
                                                                                <th>Perusahaan</th>
                                                                                <th>Area</th>
                                                                                <th>Penempatan</th>
                                                                                <th>Jabatan</th>
                                                                                <th>Tanggal Mutasi</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                            <tr class="search-row">
                                                                                <th></th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Perusahaan..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Area..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Penempatan..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Jabatan..." />
                                                                                </th>
                                                                                <th><input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        placeholder="Cari Tanggal Mutasi..." />
                                                                                </th>
                                                                                <th></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $no = 1;
                                                                            @endphp
                                                                            @foreach ($history_positions as $history_position)
                                                                                <tr>
                                                                                    <td>{{ $no++ }}</td>
                                                                                    <td>{{ $history_position->companies->nama_perusahaan }}
                                                                                    </td>
                                                                                    <td>{{ $history_position->areas->area }}
                                                                                    </td>
                                                                                    <td>{{ $history_position->divisions->penempatan }}
                                                                                    </td>
                                                                                    <td>{{ $history_position->positions->jabatan }}
                                                                                    </td>
                                                                                    <td>{{ \Carbon\Carbon::parse($history_position->tanggal_mutasi)->isoformat('DD-MM-Y') }}
                                                                                    </td>

                                                                                    <td>
                                                                                        <div
                                                                                            class="row row-cols-auto g-3 justify-content-center">
                                                                                            <div class="col">
                                                                                                <form
                                                                                                    action="{{ route('history_position.destroy', $history_position->id) }}"
                                                                                                    method="POST">
                                                                                                    @csrf
                                                                                                    @method('delete')
                                                                                                    <button
                                                                                                        class="btn btn-danger raised d-flex gap-2">
                                                                                                        <i
                                                                                                            class="material-icons-outlined">delete</i>
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- End Body --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Modal Body --}}
                                    </div>
                                    {{-- Modal Content --}}
                                </div>
                                {{-- Modal Dialog --}}
                            </div>
                            {{-- End Modal Table History Jabatan --}}
                            {{-- Modal Add History Jabatan --}}
                            <div class="modal fade" id="ScrollableModalTambahHistoryJabatan">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                                            <h5 class="modal-title">Form Tambah History Jabatan</h5>
                                            <a href="javascript:;" class="primary-menu-close" data-bs-dismiss="modal">
                                                <i class="material-icons-outlined">close</i>
                                            </a>
                                        </div>
                                        <div class="modal-body">
                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @endif

                                            <!-- Area Pesan Error Manual (Session) -->
                                            @if (session('error'))
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    {{ session('error') }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @endif
                                            <div class="form-body">
                                                <form action="{{ route('history_position.store') }}"
                                                    id="inputHistoryPosition" method="post"
                                                    enctype="multipart/form-data" class="row g-3">
                                                    @csrf
                                                    <input type="hidden" name="employees_id" class="form-control"
                                                        value="{{ $employee->id }}" id="inputHistoryPosition" readonly
                                                        placeholder="Employees ID">
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" class="form-label">NIK
                                                            Karyawan</label>
                                                        <input type="text" name="nik_karyawan" class="form-control"
                                                            value="{{ $employee->nik_karyawan }}"
                                                            id="inputHistoryPosition" readonly placeholder="NIK">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" class="form-label">Nama
                                                            Karyawan</label>
                                                        <input type="text" name="nama_karyawan"
                                                            value="{{ $employee->nama_karyawan }}" class="form-control"
                                                            id="inputHistoryPosition" readonly
                                                            placeholder="Nama Karyawan">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" class="form-label">Nama
                                                            Perusahaan</label>
                                                        <select name="companies_id" id="inputHistoryPosition"
                                                            class="form-select">
                                                            <option value="">Pilih Perusahaan</option>
                                                            @foreach ($companies as $company)
                                                                <option value="{{ $company->id }}"
                                                                    {{ old('companies_id') == $company->id ? 'selected' : '' }}>
                                                                    {{ $company->nama_perusahaan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" id="inputHistoryPosition"
                                                            class="form-label">Nama
                                                            Area</label>
                                                        <select name="areas_id" id="inputHistoryPosition"
                                                            class="form-select">
                                                            <option value="">Pilih Area</option>
                                                            @foreach ($areas as $area)
                                                                <option value="{{ $area->id }}"
                                                                    {{ old('areas_id') == $area->id ? 'selected' : '' }}>
                                                                    {{ $area->area }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" id="inputHistoryPosition"
                                                            class="form-label">Nama
                                                            Penempatan</label>
                                                        <select name="divisions_id" id="inputHistoryPosition"
                                                            class="form-select">
                                                            <option value="">Pilih Penempatan</option>
                                                            @foreach ($divisions as $division)
                                                                <option value="{{ $division->id }}"
                                                                    {{ old('divisions_id') == $division->id ? 'selected' : '' }}>
                                                                    {{ $division->penempatan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputHistoryPosition" class="form-label">Nama
                                                            Jabatan</label>
                                                        <select name="positions_id" id="inputHistoryPosition"
                                                            class="form-select">
                                                            <option value="">Pilih Jabatan</option>
                                                            @foreach ($positions as $position)
                                                                <option value="{{ $position->id }}"
                                                                    {{ old('positions_id') == $position->id ? 'selected' : '' }}>
                                                                    {{ $position->jabatan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label for="inputHistoryPosition" class="form-label">Tanggal
                                                            Mutasi</label>
                                                        <input type="date" name="tanggal_mutasi"
                                                            value="{{ old('tanggal_mutasi') }}" class="form-control"
                                                            id="inputHistoryPosition" placeholder="dd/mm/yyyy">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                                            <button type="submit"
                                                                class="btn btn-grd-danger px-4">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Modal Add History Jabatan --}}
                            {{-- History Jabatan --}}
                            <div class="row g-2 row-cols-1 row-cols-lg-2 my-0 mx-2">
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded w-100">
                                        <div class="detail-info w-100">
                                            <button type="button"
                                                class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#ScrollableModalHistoryKeluarga">
                                                <i class="material-icons-outlined">search</i>History Keluarga
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal Table History Keluarga --}}
                                <div class="modal fade" id="ScrollableModalHistoryKeluarga">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom-0 bg-grd-primary py-2">
                                                <h5 class="modal-title">Form History Keluarga
                                                </h5>
                                                <a href="#" class="primary-menu-close" data-bs-dismiss="modal">
                                                    <i class="material-icons-outlined">close</i>
                                                </a>
                                            </div>
                                            <div class="modal-body">
                                                <div class="order-summary">
                                                    <div class="card mb-0">
                                                        <div class="card-body">
                                                            {{-- Head --}}
                                                            <div class="card border bg-transparent shadow-none mb-3">
                                                                <div class="card-body">
                                                                    <p class="fs-5">
                                                                        {{ $employee->nama_karyawan }}
                                                                    </p>
                                                                    <button type="button"
                                                                        class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#ScrollableModalTambahHistoryKeluarga">
                                                                        <i class="material-icons-outlined">add</i>Tambah
                                                                        Data History Keluarga
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            {{-- End Head --}}

                                                            {{-- Body --}}
                                                            <div class="card border bg-transparent shadow-none">
                                                                <div class="card-body ">
                                                                    <div class="table-responsive">
                                                                        <table id="example2"
                                                                            class="table table-striped table-bordered w-100"
                                                                            style="width:100%">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>No</th>
                                                                                    <th>Status</th>
                                                                                    <th>NIK</th>
                                                                                    <th>Nomor BPJS</th>
                                                                                    <th>Nama</th>
                                                                                    <th>Jenis Kelamin</th>
                                                                                    <th>Tempat Tanggal Lahir</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                                <tr class="search-row">
                                                                                    <th></th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari Status..." />
                                                                                    </th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari NIK..." />
                                                                                    </th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari No BPJS..." />
                                                                                    </th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari Nama..." />
                                                                                    </th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari Jenis Kelamin..." />
                                                                                    </th>
                                                                                    <th><input type="text"
                                                                                            class="form-control form-control-sm"
                                                                                            placeholder="Cari Tempat Tanggal Lahir..." />
                                                                                    </th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @php
                                                                                    $no = 1;
                                                                                @endphp
                                                                                @foreach ($history_families as $history_family)
                                                                                    <tr>
                                                                                        <td>{{ $no++ }}</td>
                                                                                        <td>{{ $history_family->hubungan_keluarga }}
                                                                                        </td>
                                                                                        <td>{{ $history_family->nik_history_keluarga }}
                                                                                        </td>
                                                                                        <td>{{ $history_family->nomor_bpjs_kesehatan_history_keluarga }}
                                                                                        </td>
                                                                                        <td>{{ $history_family->nama_history_keluarga }}
                                                                                        </td>
                                                                                        <td>{{ $history_family->jenis_kelamin_history_keluarga }}
                                                                                        </td>
                                                                                        <td>{{ $history_family->tempat_lahir_history_keluarga }},{{ \Carbon\Carbon::parse($history_family->tanggal_lahir_history_keluarga)->isoformat('DD-MM-Y') }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <div
                                                                                                class="row row-cols-auto g-3 justify-content-center">
                                                                                                <div class="col">
                                                                                                    <button type="button"
                                                                                                        class="btn btn-success raised d-flex gap-2"
                                                                                                        data-bs-toggle="modal"
                                                                                                        data-bs-target="#ScrollableModalEditHistoryKeluarga">
                                                                                                        <i
                                                                                                            class="material-icons-outlined">edit</i>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <div class="col">
                                                                                                    <form
                                                                                                        action="{{ route('history_family.destroy', $history_family->id) }}"
                                                                                                        method="POST">
                                                                                                        @csrf
                                                                                                        @method('delete')
                                                                                                        <button
                                                                                                            class="btn btn-danger raised d-flex gap-2">
                                                                                                            <i
                                                                                                                class="material-icons-outlined">delete</i>
                                                                                                        </button>
                                                                                                    </form>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- End Body --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Modal Body --}}
                                        </div>
                                        {{-- Modal Content --}}
                                    </div>
                                    {{-- Modal Dialog --}}
                                </div>
                                {{-- End Modal Table History Keluarga --}}
                                {{-- Modal Add History Keluarga --}}
                                <div class="modal fade" id="ScrollableModalTambahHistoryKeluarga">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                                                <h5 class="modal-title">Form Tambah History Keluarga</h5>
                                                <a href="javascript:;" class="primary-menu-close"
                                                    data-bs-dismiss="modal">
                                                    <i class="material-icons-outlined">close</i>
                                                </a>
                                            </div>
                                            <div class="modal-body">
                                                @if ($errors->any())
                                                    <div class="alert alert-danger alert-dismissible fade show"
                                                        role="alert">
                                                        <ul class="mb-0">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                            aria-label="Close"></button>
                                                    </div>
                                                @endif

                                                <!-- Area Pesan Error Manual (Session) -->
                                                @if (session('error'))
                                                    <div class="alert alert-danger alert-dismissible fade show"
                                                        role="alert">
                                                        {{ session('error') }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                            aria-label="Close"></button>
                                                    </div>
                                                @endif
                                                <div class="form-body">
                                                    <form action="{{ route('history_family.store') }}"
                                                        id="inputHistoryKeluarga" method="post"
                                                        enctype="multipart/form-data" class="row g-3">
                                                        @csrf
                                                        <input type="hidden" name="employees_id" class="form-control"
                                                            value="{{ $employee->id }}" id="inputHistoryKeluarga"
                                                            readonly placeholder="Employees ID">
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">NIK
                                                                Karyawan</label>
                                                            <input type="text" name="nik_karyawan"
                                                                class="form-control"
                                                                value="{{ $employee->nik_karyawan }}"
                                                                id="inputHistoryKeluarga" readonly
                                                                placeholder="First Name">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">Nama
                                                                Karyawan</label>
                                                            <input type="text" name="nama_karyawan"
                                                                value="{{ $employee->nama_karyawan }}"
                                                                class="form-control" id="inputHistoryKeluarga" readonly
                                                                placeholder="Nama Karyawan">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Hubungan Keluarga</label>
                                                            <select id="inputHistoryKeluarga" class="form-select"
                                                                name="hubungan_keluarga">
                                                                <option value="">Pilih Hubungan Keluarga</option>
                                                                <option value="Istri"
                                                                    {{ old('hubungan_keluarga') == 'PKWTT' ? 'selected' : '' }}>
                                                                    Istri</option>
                                                                <option value="Suami"
                                                                    {{ old('hubungan_keluarga') == 'PKWT' ? 'selected' : '' }}>
                                                                    Suami</option>
                                                                <option value="Anak"
                                                                    {{ old('hubungan_keluarga') == 'Harian' ? 'selected' : '' }}>
                                                                    Anak</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">NIK
                                                                KTP</label>
                                                            <input type="text" name="nik_history_keluarga"
                                                                maxlength="16" onkeyup="angka(this);"
                                                                class="form-control" id="inputHistoryKeluarga"
                                                                placeholder="Masukan NIK KTP">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">Nama
                                                                Lengkap</label>
                                                            <input type="text" name="nama_history_keluarga"
                                                                onkeyup="huruf(this);" class="form-control"
                                                                id="inputHistoryKeluarga"
                                                                placeholder="Masukan Nama Lengkap">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">No BPJS
                                                                Kesehatan</label>
                                                            <input type="text"
                                                                name="nomor_bpjs_kesehatan_history_keluarga"
                                                                onkeyup="angka(this);" maxlength="13"
                                                                class="form-control" id="inputHistoryKeluarga"
                                                                placeholder="Masukan Nomor BPJS Kesehatan">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Jenis Kelamin</label>
                                                            <select id="inputHistoryKeluarga" class="form-select"
                                                                name="jenis_kelamin_history_keluarga">
                                                                <option value="">Pilih Jenis Kelamin</option>
                                                                <option value="Pria"
                                                                    {{ old('jenis_kelamin_history_keluarga') == 'Pria' ? 'selected' : '' }}>
                                                                    Pria</option>
                                                                <option value="Wanita"
                                                                    {{ old('jenis_kelamin_history_keluarga') == 'Wanita' ? 'selected' : '' }}>
                                                                    Wanita</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">Tempat
                                                                Lahir Lahir</label>
                                                            <input type="text" name="tempat_lahir_history_keluarga"
                                                                class="form-control" id="inputHistoryKeluarga"
                                                                placeholder="Masukan Tempat Lahir">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="inputHistoryKeluarga" class="form-label">Tanggal
                                                                Lahir</label>
                                                            <input type="date" name="tanggal_lahir_history_keluarga"
                                                                class="form-control" id="inputHistoryKeluarga"
                                                                placeholder="dd/mm/yyyy">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Golongan Darah</label>
                                                            <select id="inputHistoryKeluarga" class="form-select"
                                                                name="golongan_darah_history_keluarga">
                                                                <option value="">Pilih Golongan Darah</option>
                                                                <option value="A"
                                                                    {{ old('golongan_darah_history_keluarga') == 'A' ? 'selected' : '' }}>
                                                                    A</option>
                                                                <option value="B"
                                                                    {{ old('golongan_darah_history_keluarga') == 'B' ? 'selected' : '' }}>
                                                                    B</option>
                                                                <option value="AB"
                                                                    {{ old('golongan_darah_history_keluarga') == 'AB' ? 'selected' : '' }}>
                                                                    AB</option>
                                                                <option value="O"
                                                                    {{ old('golongan_darah_history_keluarga') == 'O' ? 'selected' : '' }}>
                                                                    O</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="d-md-flex d-grid align-items-center gap-3">
                                                                <button type="submit"
                                                                    class="btn btn-grd-danger px-4">Submit</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Modal Add History Keluarga --}}
                                {{-- Modal Edit History Keluarga --}}
                                @if (isset($history_family) && $history_family != null)
                                    <div class="modal fade" id="ScrollableModalEditHistoryKeluarga">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-bottom-0 py-2 bg-grd-info">
                                                    <h5 class="modal-title">Form Edit History Keluarga</h5>
                                                    <a href="javascript:;" class="primary-menu-close"
                                                        data-bs-dismiss="modal">
                                                        <i class="material-icons-outlined">close</i>
                                                    </a>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($errors->any())
                                                        <div class="alert alert-danger alert-dismissible fade show"
                                                            role="alert">
                                                            <ul class="mb-0">
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="alert" aria-label="Close"></button>
                                                        </div>
                                                    @endif

                                                    <!-- Area Pesan Error Manual (Session) -->
                                                    @if (session('error'))
                                                        <div class="alert alert-danger alert-dismissible fade show"
                                                            role="alert">
                                                            {{ session('error') }}
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="alert" aria-label="Close"></button>
                                                        </div>
                                                    @endif
                                                    <div class="form-body">
                                                        <form
                                                            action="{{ route('history_family.update', $history_family->id) }}"
                                                            id="editHistoryKeluarga" method="post"
                                                            enctype="multipart/form-data" class="row g-3">
                                                            @method('PUT')
                                                            @csrf
                                                            <input type="hidden" name="employees_id"
                                                                class="form-control" value="{{ $employee->id }}"
                                                                id="editHistoryKeluarga" readonly
                                                                placeholder="Employees ID">
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">NIK
                                                                    Karyawan</label>
                                                                <input type="text" name="nik_karyawan"
                                                                    class="form-control"
                                                                    value="{{ $employee->nik_karyawan }}"
                                                                    id="editHistoryKeluarga" readonly
                                                                    placeholder="First Name">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">Nama
                                                                    Karyawan</label>
                                                                <input type="text" name="nama_karyawan"
                                                                    value="{{ $employee->nama_karyawan }}"
                                                                    class="form-control" id="editHistoryKeluarga" readonly
                                                                    placeholder="Nama Karyawan">
                                                            </div>


                                                            <div class="col-md-6">
                                                                <label class="form-label">Hubungan Keluarga</label>
                                                                <select id="editHistoryKeluarga" class="form-select"
                                                                    name="hubungan_keluarga">
                                                                    <option value="">Pilih Hubungan Keluarga</option>
                                                                    <option value="Suami"
                                                                        @if ($history_family->hubungan_keluarga == 'Suami') {{ 'selected="selected"' }} @endif>
                                                                        Suami</option>
                                                                    <option value="Istri"
                                                                        @if ($history_family->hubungan_keluarga == 'Istri') {{ 'selected="selected"' }} @endif>
                                                                        Istri</option>
                                                                    <option value="Anak"
                                                                        @if ($history_family->hubungan_keluarga == 'Anak') {{ 'selected="selected"' }} @endif>
                                                                        Anak</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">NIK
                                                                    KTP</label>
                                                                <input type="text" name="nik_history_keluarga"
                                                                    maxlength="16"
                                                                    value="{{ $history_family->nik_history_keluarga }}"
                                                                    onkeyup="angka(this);" class="form-control"
                                                                    id="editHistoryKeluarga"
                                                                    placeholder="Masukan NIK KTP">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">Nama
                                                                    Lengkap</label>
                                                                <input type="text" name="nama_history_keluarga"
                                                                    onkeyup="huruf(this);"
                                                                    value="{{ $history_family->nama_history_keluarga }}"
                                                                    class="form-control" id="editHistoryKeluarga"
                                                                    placeholder="Masukan Nama Lengkap">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">No
                                                                    BPJS
                                                                    Kesehatan</label>
                                                                <input type="text"
                                                                    name="nomor_bpjs_kesehatan_history_keluarga"
                                                                    onkeyup="angka(this);" maxlength="13"
                                                                    value="{{ $history_family->nomor_bpjs_kesehatan_history_keluarga }}"
                                                                    class="form-control" id="editHistoryKeluarga"
                                                                    placeholder="Masukan Nomor BPJS Kesehatan">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Jenis Kelamin</label>
                                                                <select id="editHistoryKeluarga" class="form-select"
                                                                    name="jenis_kelamin_history_keluarga">
                                                                    <option value="">Pilih Jenis Kelamin</option>
                                                                    <option value="Pria"
                                                                        @if ($history_family->jenis_kelamin_history_keluarga == 'Pria') {{ 'selected="selected"' }} @endif>
                                                                        Pria</option>
                                                                    <option value="Wanita"
                                                                        @if ($history_family->jenis_kelamin_history_keluarga == 'Wanita') {{ 'selected="selected"' }} @endif>
                                                                        Wanita</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga" class="form-label">Tempat
                                                                    Lahir Lahir</label>
                                                                <input type="text" name="tempat_lahir_history_keluarga"
                                                                    class="form-control"
                                                                    value="{{ $history_family->tempat_lahir_history_keluarga }}"
                                                                    id="editHistoryKeluarga"
                                                                    placeholder="Masukan Tempat Lahir">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="editHistoryKeluarga"
                                                                    class="form-label">Tanggal
                                                                    Lahir</label>
                                                                <input type="date"
                                                                    name="tanggal_lahir_history_keluarga"
                                                                    class="form-control"
                                                                    value="{{ $history_family->tanggal_lahir_history_keluarga }}"
                                                                    id="editHistoryKeluarga" placeholder="dd/mm/yyyy">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Golongan Darah</label>
                                                                <select id="editHistoryKeluarga" class="form-select"
                                                                    name="golongan_darah_history_keluarga">
                                                                    <option value="">Pilih Jenis Kelamin</option>
                                                                    <option value="A"
                                                                        @if ($history_family->golongan_darah_history_keluarga == 'A') {{ 'selected="selected"' }} @endif>
                                                                        A</option>
                                                                    <option value="B"
                                                                        @if ($history_family->golongan_darah_history_keluarga == 'B') {{ 'selected="selected"' }} @endif>
                                                                        B</option>
                                                                    <option value="AB"
                                                                        @if ($history_family->golongan_darah_history_keluarga == 'AB') {{ 'selected="selected"' }} @endif>
                                                                        AB</option>
                                                                    <option value="O"
                                                                        @if ($history_family->golongan_darah_history_keluarga == 'O') {{ 'selected="selected"' }} @endif>
                                                                        O</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="d-md-flex d-grid align-items-center gap-3">
                                                                    <button type="submit"
                                                                        class="btn btn-grd-danger px-4">Submit</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- End Modal Edit History Keluarga --}}
                                <div class="col">
                                    <div class="d-flex align-items-start gap-3 border p-3 rounded w-100">
                                        <div class="detail-info w-100">
                                            <a href="{{ route('cetak.aktif_kerja', $employee->id) }}"
                                                class="btn btn-primary px-4 raised d-flex gap-2 w-100 justify-content-center"
                                                target="_blank">
                                                <i class="material-icons-outlined">print</i>Cetak Surat Keterangan Aktif
                                                Kerja
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab History --}}


                    </div>
                </div>
            </div>
        </div>
        <!-- END DETAIL SECTION -->
    </div>

@endsection

{{-- Datatables --}}
@section('js')
    {{-- <script src="{{ asset('template_admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script> --}}
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            // 1. Inisialisasi DataTable
            var table = $('#example2').DataTable({
                lengthChange: false,
                orderCellsTop: true, // Penting: Agar sorting tetap di baris header pertama
                fixedHeader: true,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            // 2. Tambahkan Button Container
            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');

            // 3. Logika Pencarian Per Kolom
            // Kita ambil setiap input dari baris '.search-row'
            $('#example2 .search-row input').on('keyup change', function() {
                // Ambil index kolom dari elemen parent <th>
                var columnIndex = $(this).parent().index();

                if (table.column(columnIndex).search() !== this.value) {
                    table
                        .column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });
        });
    </script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>

@endsection
{{-- Datatables --}}

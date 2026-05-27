@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        /* Membuat input pencarian kolom lebih tipis dan rapi */
        .search-row th {
            padding: 8px 4px !important;
        }

        .search-row input {
            font-size: 13px;
            padding: 4px 8px;
        }

        /* Menjaga tombol aksi tetap rapi dalam satu baris */
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
@endsection
@extends('admin.layouts.base')
@section('title', 'Data Karyawan Keluar')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Karyawan Keluar</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $userRole = Auth::user()->roles;
        $canManage = in_array($userRole, ['admin', 'hrd']);
        $canDelete = $userRole === 'admin';
        $canView = in_array($userRole, ['admin', 'hrd', 'leader', 'accounting']);
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="d-flex flex-wrap gap-2 mb-4">
                @if ($canManage)
                    <a href="{{ route('employee_out.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="bi bi-person-plus"></i> Tambah Karyawan Keluar
                    </a>
                    <a href="{{ route('EmployeeOutExportExcel') }}" target="_blank"
                        class="btn btn-success d-flex align-items-center gap-1">
                        <i class="bi bi-cloud-arrow-down"></i> Download Database Karyawan Keluar
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="example2" class="table table-striped table-hover table-bordered align-middle w-100">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Penempatan</th>
                            <th>Akhir Kerja</th>
                            <th>Status Kerja</th>
                            <th>BPJS Kesehatan</th>
                            <th>BPJSTK</th>
                            <th width="10%" class="text-center">Action</th>

                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control" placeholder="Cari Nama..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari NIK..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari Penempatan..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari Akhir Kerja..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari Status Kerja..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari BPJS Kesehatan..." /></th>
                            <th><input type="text" class="form-control" placeholder="Cari BPJSTK..." /></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($employees_outs as $employee_out)
                            @php

                                switch ($employee_out->status_kerja_karyawan_keluar) {
                                    case 'PKWTT':
                                        $status_kerja_karyawan_keluar = 'Tetap';
                                        $badge_color = 'success';
                                        break;
                                    case 'Outsourcing':
                                        $status_kerja = 'Outsourcing';
                                        $badge_color = 'danger';
                                        break;
                                    case 'Harian':
                                        $status_kerja = 'Harian';
                                        $badge_color = 'info text-dark';
                                        break;
                                    default:
                                        $status_kerja = 'Kontrak';
                                        $badge_color = 'info text-dark';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $employee_out->nama_karyawan_keluar }}</td>
                                <td>{{ $employee_out->nik_karyawan_keluar }}</td>
                                <td>{{ $employee_out->divisions->penempatan }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee_out->tanggal_keluar_karyawan_keluar)->isoformat('DD-MM-Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badge_color }}">
                                        {{ $employee_out->status_kerja_karyawan_keluar }}
                                    </span>
                                </td>
                                <td>{{ $employee_out->nomor_bpjskesehatan_karyawan_keluar ?? '-' }}</td>
                                <td>{{ $employee_out->nomor_bpjsketenagakerjaan_karyawan_keluar ?? '-' }}</td>
                                <td>
                                    <!-- MERAPIKAN STRUKTUR UTAMA TOMBOL AKSI -->
                                    <div class="action-buttons">
                                        @if ($canManage)
                                            <a href="{{ route('employee_out.edit', $employee_out->id) }}"
                                                class="btn btn-sm btn-outline-success p-1 d-inline-flex align-items-center"
                                                title="Edit">
                                                <i class="material-icons-outlined fs-6">edit</i>
                                            </a>
                                            <a href="{{ route('employee_out.show', $employee_out->id) }}"
                                                class="btn btn-sm btn-outline-primary p-1 d-inline-flex align-items-center"
                                                title="Print" target="_blank">
                                                <i class="material-icons-outlined fs-6">print</i>
                                            </a>
                                            @if ($canDelete)
                                                <form action="{{ route('employee_out.destroy', $employee_out->id) }}"
                                                    method="POST" class="d-inline m-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger p-1 d-inline-flex align-items-center btn-delete"
                                                        title="Hapus">
                                                        <i class="material-icons-outlined fs-6">delete</i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if ($canView)
                                            <button type="button"
                                                class="btn btn-sm btn-outline-info p-1 d-inline-flex align-items-center btn-view-detail"
                                                title="Lihat" data-bs-toggle="modal"
                                                data-bs-target="#ScrollableModalKaryawanKeluar"
                                                data-perusahaan="{{ $employee_out->companies->nama_perusahaan }}"
                                                data-area="{{ $employee_out->areas->area }}"
                                                data-jabatan="{{ $employee_out->positions->jabatan }}"
                                                data-penempatan="{{ $employee_out->divisions->penempatan }}"
                                                data-nama="{{ $employee_out->nama_karyawan_keluar }}"
                                                data-nik="{{ $employee_out->nik_karyawan_keluar }}"
                                                data-npwp="{{ $employee_out->nomor_npwp_karyawan_keluar }}"
                                                data-handphone="{{ $employee_out->nomor_handphone_karyawan_keluar }}"
                                                data-email="{{ $employee_out->email_karyawan_keluar }}"
                                                data-tempat_lahir="{{ $employee_out->tempat_lahir_karyawan_keluar }}"
                                                data-tanggal_lahir="{{ \Carbon\Carbon::parse($employee_out->tanggal_lahir_karyawan_keluar)->isoformat('DD-MM-Y') }}"
                                                data-agama="{{ $employee_out->agama_karyawan_keluar }}"
                                                data-jenis_kelamin="{{ $employee_out->jenis_kelamin_karyawan_keluar }}"
                                                data-nomor_rekening="{{ $employee_out->nomor_rekening_karyawan_keluar }}"
                                                data-bpjs_kesehatan="{{ $employee_out->nomor_bpjskesehatan_karyawan_keluar ?? '-' }}"
                                                data-bpjs_ketenagakerjaan="{{ $employee_out->nomor_bpjsketenagakerjaan_karyawan_keluar ?? '-' }}"
                                                data-status_nikah="{{ $employee_out->status_nikah_karyawan_keluar }}"
                                                data-nama_ayah="{{ $employee_out->nama_ayah_karyawan_keluar }}"
                                                data-nama_ibu="{{ $employee_out->nama_ibu_karyawan_keluar }}"
                                                data-status_kerja="{{ $employee_out->status_kerja_karyawan_keluar }}"
                                                data-tanggal_masuk="{{ \Carbon\Carbon::parse($employee_out->tanggal_masuk_karyawan_keluar)->isoformat('DD-MM-Y') }}"
                                                data-tanggal_keluar="{{ \Carbon\Carbon::parse($employee_out->tanggal_keluar_karyawan_keluar)->isoformat('DD-MM-Y') }}"
                                                data-keterangan_keluar="{{ $employee_out->keterangan_keluar }}"
                                                data-nomor_kartu_keluarga="{{ $employee_out->nomor_kartu_keluarga_karyawan_keluar }}"
                                                data-alamat="{{ $employee_out->alamat_karyawan_keluar }}"
                                                data-rt="{{ $employee_out->rt_karyawan_keluar }}"
                                                data-rw="{{ $employee_out->rw_karyawan_keluar }}"
                                                data-kelurahan="{{ $employee_out->kelurahan_karyawan_keluar }}"
                                                data-kecamatan="{{ $employee_out->kecamatan_karyawan_keluar }}"
                                                data-kota="{{ $employee_out->kota_karyawan_keluar }}"
                                                data-alasan_keluar="{{ $employee_out->alasan_keluar }}">
                                                <i class="material-icons-outlined fs-6">visibility</i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Modal View Karyawan Keluar --}}
            <div class="modal fade" id="ScrollableModalKaryawanKeluar">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0 bg-grd-primary py-2">
                            <h5 class="modal-title">Detail Karyawan Keluar
                            </h5>
                            <a href="#" class="primary-menu-close" data-bs-dismiss="modal">
                                <i class="material-icons-outlined">close</i>
                            </a>
                        </div>
                        <div class="modal-body">
                            <div class="order-summary">
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <div class="row g-4 row-cols-1 row-cols-lg-4">
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-buildings"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nama Perusahaan</p>
                                                        <p class="mb-0" id="modal-perusahaan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-buildings"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Area</p>
                                                        <p class="mb-0" id="modal-area">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-buildings"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Jabatan</p>
                                                        <p class="mb-0" id="modal-jabatan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-buildings"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Penempatan</p>
                                                        <p class="mb-0" id="modal-penempatan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-circle"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nama Karyawan</p>
                                                        <p class="mb-0" id="modal-nama">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-card-text"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">NIK</p>
                                                        <p class="mb-0" id="modal-nik">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-card-text"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor NPWP</p>
                                                        <p class="mb-0" id="modal-npwp">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i
                                                            class="bi bi-telephone-forward-fill"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor Handphone</p>
                                                        <p class="mb-0" id="modal-handphone">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-envelope-at"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Email</p>
                                                        <p class="mb-0" id="modal-email">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-cake2"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Tempat Tanggal Lahir</p>
                                                        <p class="mb-0" id="modal-ttl">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-shield-fill"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Agama</p>
                                                        <p class="mb-0" id="modal-agama">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-gender-ambiguous"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Jenis Kelamin</p>
                                                        <p class="mb-0" id="modal-jenis_kelamin">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-postcard-fill"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor Rekening</p>
                                                        <p class="mb-0" id="modal-nomor_rekening">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-shield-plus"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor BPJS Kesehatan</p>
                                                        <p class="mb-0" id="modal-bpjs_kesehatan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-shield-plus"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor BPJSTK</p>
                                                        <p class="mb-0" id="modal-bpjs_ketenagakerjaan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-video2"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Status Menikah</p>
                                                        <p class="mb-0" id="modal-status_nikah">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-standing"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nama Ayah</p>
                                                        <p class="mb-0" id="modal-nama_ayah">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i
                                                            class="bi bi-person-standing-dress"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nama Ibu</p>
                                                        <p class="mb-0" id="modal-nama_ibu">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-workspace"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Status Kerja</p>
                                                        <p class="mb-0" id="modal-status_kerja">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-calendar2-week"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Tanggal Masuk</p>
                                                        <p class="mb-0" id="modal-tanggal_masuk">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-calendar2-week"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Tanggal Keluar</p>
                                                        <p class="mb-0" id="modal-tanggal_keluar">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-workspace"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Keterangan Keluar</p>
                                                        <p class="mb-0" id="modal-keterangan_keluar">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-person-workspace"></i>
                                                    </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Nomor KK</p>
                                                        <p class="mb-0" id="modal-nomor_kartu_keluarga">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Alamat</p>
                                                        <p class="mb-0" id="modal-alamat">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">RT/RW</p>
                                                        <p class="mb-0" id="modal-rtrw">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Kelurahan</p>
                                                        <p class="mb-0" id="modal-kelurahan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i></div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Kecamatan</p>
                                                        <p class="mb-0" id="modal-kecamatan">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i> </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Kabupaten/Kota</p>
                                                        <p class="mb-0" id="modal-kota">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-start gap-3 border p-3 rounded">
                                                    <div class="detail-icon fs-5"><i class="bi bi-geo-alt"></i> </div>
                                                    <div class="detail-info">
                                                        <p class="fw-bold mb-1">Alasan Keluar</p>
                                                        <p class="mb-0" id="modal-alasan_keluar">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    // Menggunakan objek bahasa lokal agar anti-blokir dan lebih cepat load-nya
                    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sSearch": "Cari:",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya",
                        "sLast": "Terakhir"
                    }
                }
            });

            // Logika Pencarian Per Kolom dengan optimasi debounce
            var delayTimer;
            $('#example2 .search-row input').on('keyup change', function() {
                var columnIndex = $(this).parent().index();
                var value = this.value;
                clearTimeout(delayTimer);
                delayTimer = setTimeout(function() {
                    if (table.column(columnIndex).search() !== value) {
                        table.column(columnIndex).search(value).draw();
                    }
                }, 300);
            });

            // SweetAlert2 Delegasi Event (Supaya tetap bekerja saat halaman DataTables berpindah)
            $('#example2').on('click', '.btn-delete', function(e) {
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data karyawan ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-view-detail', function() {
            var perusahaan = $(this).data('perusahaan');
            var area = $(this).data('area');
            var jabatan = $(this).data('jabatan');
            var penempatan = $(this).data('penempatan');
            var nama = $(this).data('nama');
            var nik = $(this).data('nik');
            var npwp = $(this).data('npwp');
            var handphone = $(this).data('handphone');
            var email = $(this).data('email');
            var tempat_lahir = $(this).data('tempat_lahir');
            var tanggal_lahir = $(this).data('tanggal_lahir');
            var ttl = tempat_lahir + ', ' + tanggal_lahir;
            var agama = $(this).data('agama');
            var jenis_kelamin = $(this).data('jenis_kelamin');
            var nomor_rekening = $(this).data('nomor_rekening');
            var bpjs_kesehatan = $(this).data('bpjs_kesehatan');
            var bpjs_ketenagakerjaan = $(this).data('bpjs_ketenagakerjaan');
            var status_nikah = $(this).data('status_nikah');
            var nama_ayah = $(this).data('nama_ayah');
            var nama_ibu = $(this).data('nama_ibu');
            var status_kerja = $(this).data('status_kerja');
            var tanggal_masuk = $(this).data('tanggal_masuk');
            var tanggal_keluar = $(this).data('tanggal_keluar');
            var keterangan_keluar = $(this).data('keterangan_keluar');
            var nomor_kartu_keluarga = $(this).data('nomor_kartu_keluarga');
            var alamat = $(this).data('alamat');
            var rt = $(this).data('rt');
            var rw = $(this).data('rw');
            var rtrw = rt + '/' + rw;
            var kelurahan = $(this).data('kelurahan');
            var kecamatan = $(this).data('kecamatan');
            var kota = $(this).data('kota');
            var alasan_keluar = $(this).data('alasan_keluar');

            $('#modal-perusahaan').text(perusahaan);
            $('#modal-area').text(area);
            $('#modal-jabatan').text(jabatan);
            $('#modal-penempatan').text(penempatan);
            $('#modal-nama').text(nama);
            $('#modal-nik').text(nik);
            $('#modal-npwp').text(npwp);
            $('#modal-handphone').text(handphone);
            $('#modal-email').text(email);
            $('#modal-ttl').text(ttl);
            $('#modal-agama').text(agama);
            $('#modal-jenis_kelamin').text(jenis_kelamin);
            $('#modal-nomor_rekening').text(nomor_rekening);
            $('#modal-bpjs_kesehatan').text(bpjs_kesehatan);
            $('#modal-bpjs_ketenagakerjaan').text(bpjs_ketenagakerjaan);
            $('#modal-status_nikah').text(status_nikah);
            $('#modal-nama_ayah').text(nama_ayah);
            $('#modal-nama_ibu').text(nama_ibu);
            $('#modal-status_kerja').text(status_kerja);
            $('#modal-tanggal_masuk').text(tanggal_masuk);
            $('#modal-tanggal_keluar').text(tanggal_keluar);
            $('#modal-keterangan_keluar').text(keterangan_keluar);
            $('#modal-nomor_kartu_keluarga').text(nomor_kartu_keluarga);
            $('#modal-alamat').text(alamat);
            $('#modal-rtrw').text(rtrw);
            $('#modal-kelurahan').text(kelurahan);
            $('#modal-kecamatan').text(kecamatan);
            $('#modal-kota').text(kota);
            $('#modal-alasan_keluar').text(alasan_keluar);
        });
    </script>
@endsection

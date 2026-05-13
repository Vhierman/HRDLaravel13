@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        .search-row input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
@endsection

@extends('admin.layouts.base')
@section('title', 'Data Sertifikasi Lain');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Sertifikasi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Lain</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <div class="btn-group position-static">
                        <a href="{{ route('certification_other.create') }}" class="btn-group position-static">
                            <button type="button" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Tambah Sertifikasi
                            </button>
                        </a>
                        <a href="{{ route('certification_other.exportExcel') }}" target="_blank"
                            class="btn-group position-static">
                            <button type="button" class="btn btn-success">
                                <i class="bi bi-cloud-arrow-down"></i> Download Data Sertifikasi
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK Karyawan</th>
                            <th>Nama Karyawan</th>
                            <th>Jenis Sertifikasi</th>
                            <th>Nomor Sertifikat</th>
                            <th>Tanggal Terbit</th>
                            <th>Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Area..." />
                            </th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari NIK Karyawan..."></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Nama Karyawan..."></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Jenis Sertifikat..."></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Nomor Sertifikat..."></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Tanggal Terbit..."></th>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($certification_others as $certification_other)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $certification_other->employees->nik_karyawan }}</td>
                                <td>{{ $certification_other->employees->nama_karyawan }}</td>
                                <td>{{ $certification_other->jenis_sertifikat_lain }}</td>
                                <td>{{ $certification_other->nomor_sertifikat_lain }}</td>
                                <td>{{ \Carbon\Carbon::parse($certification_other->tanggal_terbit_lain)->isoformat('DD-MM-Y') }}
                                </td>
                                <td>
                                    <div class="row row-cols-auto g-3">
                                        <div class="col">
                                            <a href="{{ route('certification_other.edit', $certification_other->id) }}"
                                                class="btn btn-success raised d-flex gap-2">
                                                <i class="material-icons-outlined">edit</i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <form
                                                action="{{ route('certification_other.destroy', $certification_other->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger raised d-flex gap-2">
                                                    <i class="material-icons-outlined">delete</i>
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
                fixedHeader: false,
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
@endsection
{{-- Datatables --}}

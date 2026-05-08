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
@section('title', 'Data Karyawan Keluar');

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
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <a href="{{ route('employee_out.create') }}" class="btn btn-primary px-5 raised">
                        Tambah Data Karyawan Keluar
                    </a>
                    <a href="{{ route('exportExcel') }}" target="_blank" class="btn btn-success px-5 raised">
                        Download Data Karyawan Keluar
                    </a>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Penempatan</th>
                            <th>Akhir Kerja</th>
                            <th>Status Kerja</th>
                            <th>BPJS Kesehatan</th>
                            <th>BPJSTK</th>
                            <th>Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Nama..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari NIK..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Penempatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Akhir Kerja..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Status Kerja..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari BPJS Kesehatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari BPJSTK..." />
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($employees_outs as $employee_out)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $employee_out->nama_karyawan_keluar }}</td>
                                <td>{{ $employee_out->nik_karyawan_keluar }}</td>
                                <td>{{ $employee_out->divisions->penempatan }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee_out->tanggal_keluar_karyawan_keluar)->isoformat('DD-MM-Y') }}
                                </td>
                                <td>{{ $employee_out->status_kerja_karyawan_keluar }}</td>
                                <td>{{ $employee_out->nomor_bpjskesehatan_karyawan_keluar }}</td>
                                <td>{{ $employee_out->nomor_bpjsketenagakerjaan_karyawan_keluar }}</td>
                                <td>
                                    <div class="row row-cols-auto g-3">
                                        <div class="col">
                                            <a href="{{ route('employee_out.edit', $employee_out->id) }}"
                                                class="btn btn-success raised d-flex gap-2">
                                                <i class="material-icons-outlined">edit</i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="{{ route('employee_out.show', $employee_out->id) }}"
                                                class="btn btn-primary raised d-flex gap-2">
                                                <i class="material-icons-outlined">print</i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <form action="{{ route('employee_out.destroy', $employee_out->id) }}"
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
@endsection
{{-- Datatables --}}

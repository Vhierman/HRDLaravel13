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
@section('title', 'Data Karyawan');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Karyawan Aktif</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <div class="btn-group position-static">
                        <a href="{{ route('employee.create') }}" class="btn-group position-static">
                            <button type="button" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Tambah Karyawan
                            </button>
                        </a>
                        <a href="{{ route('exportExcel') }}" target="_blank" class="btn-group position-static">
                            <button type="button" class="btn btn-success">
                                <i class="bi bi-cloud-arrow-down"></i> Download Database Karyawan
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
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Jabatan</th>
                            <th>Penempatan</th>
                            <th>Area</th>
                            <th>Golongan</th>
                            <th>Status</th>
                            <th>Awal Kerja</th>
                            <th>Akhir Kerja</th>
                            <th>Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Nama..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari NIK..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Penempatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Area..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Golongan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Status..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Awal Kerja..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Akhir Kerja..." />
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($employees as $employee)
                            @if ($employee->status_kerja == 'PKWTT')
                                @php
                                    $tanggal_akhir_kerja = $employee->status_kerja;
                                    $status_kerja = 'Tetap';
                                @endphp
                            @elseif($employee->status_kerja == 'PKWT')
                                @php
                                    $tanggal_akhir_kerja = \Carbon\Carbon::parse(
                                        $employee->tanggal_akhir_kerja,
                                    )->isoformat('DD-MM-Y');
                                    $status_kerja = 'Kontrak';
                                @endphp
                            @elseif($employee->status_kerja == 'Harian')
                                @php
                                    $tanggal_akhir_kerja = \Carbon\Carbon::parse(
                                        $employee->tanggal_akhir_kerja,
                                    )->isoformat('DD-MM-Y');
                                    $status_kerja = 'Harian';
                                @endphp
                            @elseif($employee->status_kerja == 'Outsourcing')
                                @php
                                    $tanggal_akhir_kerja = \Carbon\Carbon::parse(
                                        $employee->tanggal_akhir_kerja,
                                    )->isoformat('DD-MM-Y');
                                    $status_kerja = 'Outsourcing';
                                @endphp
                            @else
                                @php
                                    $tanggal_akhir_kerja = \Carbon\Carbon::parse(
                                        $employee->tanggal_akhir_kerja,
                                    )->isoformat('DD-MM-Y');

                                @endphp
                            @endif
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $employee->nama_karyawan }}</td>
                                <td>{{ $employee->nik_karyawan }}</td>
                                <td>{{ $employee->positions->jabatan }}</td>
                                <td>{{ $employee->divisions->penempatan }}</td>
                                <td>{{ $employee->areas->area }}</td>
                                <td>{{ $employee->golongans->golongan }}</td>
                                <td>{{ $status_kerja }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoformat('DD-MM-Y') }}</td>
                                <td>{{ $tanggal_akhir_kerja }}</td>
                                <td>
                                    <div class="row row-cols-auto g-3">
                                        <div class="col">
                                            <a href="{{ route('employee.edit', $employee->id) }}"
                                                class="btn btn-sm btn-success raised d-flex gap-2">
                                                <i class="material-icons-outlined">edit</i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="{{ route('employee.show', $employee->id) }}"
                                                class="btn btn-sm btn-primary raised d-flex gap-2">
                                                <i class="material-icons-outlined">visibility</i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <form action="{{ route('employee.destroy', $employee->id) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-sm btn-danger raised d-flex gap-2">
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

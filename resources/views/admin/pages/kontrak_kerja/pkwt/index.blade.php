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
@section('title', 'Data Kontrak Kerja');

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-2 text-center">
                <div class="col-md-6">
                    <a href="{{ route('kontrak_kerja.form_proses_tanggal_pkwt') }}"
                        class="btn btn-primary px-5 btn-sm py-2 w-100">Proses Kontrak Kerja Karyawan Kontrak
                        Berdasarkan Tanggal</a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('kontrak_kerja.form_proses_nama_pkwt') }}"
                        class="btn btn-primary px-5 btn-sm py-2 w-100">Proses Kontrak Kerja Karyawan Kontrak
                        Berdasarkan Nama</a>
                </div>
            </div>
            <div class="row justify-content-center g-2 text-center mt-0">
                <div class="col-md-12">
                    <a href="{{ route('kontrak_kerja.form_cetak_tanggal_pkwt') }}"
                        class="btn btn-success px-5 btn-sm py-2 w-100">Cetak Kontrak Kerja Karyawan Kontrak Berdasarkan
                        Tanggal</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK Karyawan</th>
                            <th>Nama Karyawan</th>
                            <th>Area</th>
                            <th>Jabatan</th>
                            <th>Penempatan</th>
                            <th>Akhir Kontrak</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari NIK Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Nama Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari Area..." /></th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Penempatan..." /></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Akhir Kontrak..." /></th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $employee->nik_karyawan }}</td>
                                <td>{{ $employee->nama_karyawan }}</td>
                                <td>{{ $employee->areas->area }}</td>
                                <td>{{ $employee->positions->jabatan }}</td>
                                <td>{{ $employee->divisions->penempatan }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee->tanggal_akhir_kerja)->isoformat('DD-MM-Y') }}
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

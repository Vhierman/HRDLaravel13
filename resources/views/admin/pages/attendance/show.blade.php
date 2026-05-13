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
@section('title', 'Data Absensi');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Absensi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Absensi Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <form action="{{ route('attendance.export_excell_absensi') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                        <input type="hidden" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                        <button type="submit" class="btn btn-success px-5 raised">
                            Download Rekap Absensi Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}
                        </button>
                    </form>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>NIK Karyawan</th>
                            <th>Golongan</th>
                            <th>Jabatan</th>
                            <th>Penempatan</th>
                            <th>Tanggal Absen</th>
                            <th>Jenis Absen</th>
                            <th>Keterangan</th>

                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Nama Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari NIK Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Golongan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Penempatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Tanggal Absen..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jenis Absen..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Keterangan..." />
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($item_attendances as $item_attendance)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item_attendance->employees->nama_karyawan }}</td>
                                <td>{{ $item_attendance->nik_karyawan }}</td>
                                <td>{{ $item_attendance->employees->golongans->golongan }}</td>
                                <td>{{ $item_attendance->employees->positions->jabatan }}</td>
                                <td>{{ $item_attendance->employees->divisions->penempatan }}</td>
                                <td>{{ \Carbon\Carbon::parse($item_attendance->tanggal_absen)->isoformat('DD-MM-Y') }}</td>
                                <td>{{ $item_attendance->keterangan_absen }}</td>
                                <td>{{ $item_attendance->keterangan_cuti_khusus }}</td>

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

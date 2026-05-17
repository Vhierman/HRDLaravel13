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
@section('title', 'Data Gaji');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Gaji</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Gaji Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <form action="{{ route('salary.cancel_rekap_gaji') }}" method="POST" class="d-inline" id="formCancel">
                        @csrf
                        <input type="hidden" name="tanggal_awal" id="tanggal_awal_cancel" class="form-control"
                            value="{{ $tanggal_awal }}">
                        <input type="hidden" name="tanggal_akhir" id="tanggal_akhir_cancel" class="form-control"
                            value="{{ $tanggal_akhir }}">

                        <button type="submit" class="btn btn-danger px-5 raised" id="formCancel">
                            Proses Cancel Rekap Gaji Periode
                            {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}
                        </button>
                    </form>
                    <form action="{{ route('salary.export_excell_rekap') }}" method="POST" class="d-inline"
                        id="formExport">
                        @csrf
                        <input type="hidden" name="tanggal_awal" id="tanggal_awal_export" class="form-control"
                            value="{{ $tanggal_awal }}">
                        <input type="hidden" name="tanggal_akhir" id="tanggal_akhir_export" class="form-control"
                            value="{{ $tanggal_akhir }}">
                        <button type="submit" class="btn btn-success px-5 raised" id="formExport">
                            Download Excell Data Rekap Gaji Periode
                            {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }}
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
                            <th>NIK Karyawan</th>
                            <th>Nama Karyawan</th>
                            <th>Golongan</th>
                            <th>Penempatan</th>
                            <th>Gaji Pokok</th>
                            <th>Uang Makan</th>
                            <th>Uang Transport</th>
                            <th>Tunjangan Tugas</th>
                            <th>Tunjangan Pulsa</th>
                            <th>Tunjangan Jabatan</th>
                            <th>Jumlah Upah</th>
                            <th>Upah Lembur Perjam</th>
                            <th>Take Home Pay</th>
                            <th>Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari NIK Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Nama Karyawan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Golongan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Penempatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Gaji Pokok..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Uang Makan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Uang Transport..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Tunjangan Tugas..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Tunjangan Pulsa..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Tunjangan Jabatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jumlah Upah..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Upah Lembur Perjam..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Take Home Pay..." />
                            </th>
                            <th>
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($item_salaries as $item_salary)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item_salary->employees->nama_karyawan }}</td>
                                <td>{{ $item_salary->nik_karyawan }}</td>
                                <td>{{ $item_salary->employees->golongans->golongan }}</td>
                                <td>{{ $item_salary->employees->divisions->penempatan }}</td>
                                <td>{{ number_format($item_salary->gaji_pokok) }}</td>
                                <td>{{ number_format($item_salary->uang_makan) }}</td>
                                <td>{{ number_format($item_salary->uang_transport) }}</td>
                                <td>{{ number_format($item_salary->tunjangan_tugas) }}</td>
                                <td>{{ number_format($item_salary->tunjangan_pulsa) }}</td>
                                <td>{{ number_format($item_salary->tunjangan_jabatan) }}</td>
                                <td>{{ number_format($item_salary->jumlah_upah) }}</td>
                                <td>{{ number_format($item_salary->upah_lembur_perjam) }}</td>
                                <td>{{ number_format($item_salary->take_home_pay) }}</td>
                                <td>
                                    <div class="row row-cols-auto g-3">
                                        <div class="col">
                                            <form action="{{ route('salary.cetak_slip') }}" id="cetak_slip" method="POST"
                                                target="_blank">
                                                @csrf

                                                <input type="hidden" class="form-control" name="id" readonly
                                                    value="{{ $item_salary }}">
                                                <input type="hidden" class="form-control" name="employees_id" readonly
                                                    value="{{ $item_salary->employees_id }}">
                                                <input type="hidden" class="form-control" name="nik_karyawan" readonly
                                                    value="{{ $item_salary->nik_karyawan }}">
                                                <input type="hidden" class="form-control" name="tanggal_awal" readonly
                                                    value="{{ $tanggal_awal }}">
                                                <input type="hidden" class="form-control" name="tanggal_akhir" readonly
                                                    value="{{ $tanggal_akhir }}">

                                                <button type="submit" class="btn btn-sm btn-info raised d-flex gap-2"
                                                    id="cetak_slip">
                                                    <i class="material-icons-outlined">print</i>
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

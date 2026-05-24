@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        /* Memastikan container responsive membungkus dengan sempurna */
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        /* Memaksa tabel memanfaatkan lebar maksimal */
        #example2 {
            width: 100% !important;
            margin: 0 !important;
        }

        /* Desain Header Utama Tabel */
        #example2 thead tr:first-child th {
            background-color: #f8f9fa;
            color: #343a40;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 8px !important;
            white-space: nowrap;
            /* Mencegah judul kolom patah baris berantakan */
        }

        /* Desain Baris Pencarian Kolom */
        .search-row th {
            background-color: #ffffff !important;
            padding: 6px 4px !important;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .search-row input {
            font-size: 12px;
            padding: 5px 8px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.2s ease-in-out;
            width: 100%;
            min-width: 100px;
            /* Batas minimal agar input tidak mengecil habis */
            box-sizing: border-box;
        }

        .search-row input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Merapikan Ukuran & Layout Tombol Aksi */
        .action-container {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }

        /* Mengunci ukuran tombol agar tetap simetris & responsive */
        .action-container .btn-action {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            flex-shrink: 0;
            /* Mencegah tombol gepeng saat layar menyempit */
        }

        .action-container .btn-action i {
            font-size: 16px !important;
            line-height: 1;
        }

        /* Merapikan isi cell */
        #example2 tbody td {
            vertical-align: middle;
            padding: 10px 8px !important;
            font-size: 14px;
            white-space: nowrap;
            /* Menjaga data teks seperti Nopol/Tanggal tetap satu baris */
        }

        /* Berikan kelonggaran khusus kolom nama agar teks panjang bisa turun jika space habis */
        #example2 tbody td:nth-child(2) {
            white-space: normal;
            min-width: 150px;
        }
    </style>
@endsection

@extends('admin.layouts.base')
@section('title', 'Data Overtimes');
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtimes</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Upah Lembur Perjam</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <br>
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
                            <th>Upah Lembur Perjam</th>
                            <th>Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari NIK Karyawan..." /></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Nama Karyawan..." /></th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari Area..." /></th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Penempatan..." /></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Upah Lembur Perjam..." /></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($item_overtimes as $item_overtime)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item_overtime->employees->nik_karyawan }}</td>
                                <td>{{ $item_overtime->employees->nama_karyawan }}</td>
                                <td>{{ $item_overtime->employees->areas->area }}</td>
                                <td>{{ $item_overtime->employees->positions->jabatan }}</td>
                                <td>{{ $item_overtime->employees->divisions->penempatan }}</td>
                                <td>{{ $item_overtime->upah_lembur_perjam }}</td>
                                <td class="text-center">
                                    <div class="action-container">
                                        <form
                                            action="{{ route('overtime.proses_lembur_perjam', $item_overtime->employees->id) }}"
                                            method="POST" style="display: inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success btn-action"
                                                title="Edit Data">
                                                <i class="material-icons-outlined">edit</i>
                                            </button>
                                        </form>
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
        });
    </script>
@endsection
{{-- Datatables --}}

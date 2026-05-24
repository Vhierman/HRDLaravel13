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
@section('title', 'Data Overtime');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtime</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Overtime Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col">
                    <form action="{{ route('overtime.proses_cancel_approve_overtime') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                        <input type="hidden" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                        <button type="submit" class="btn btn-primary px-5 raised w-100">
                            Cancel Approve Data Overtime Periode
                            {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col">
                    <a href="{{ route('overtime.index') }}" class="btn btn-danger px-5 raised w-100">Back</a>
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
                            <th>Jenis Lembur</th>
                            <th>Tanggal Lembur</th>
                            <th>Jam Masuk</th>
                            <th>Jam Istirahat</th>
                            <th>Jam Pulang</th>
                            <th>Jumlah Jam</th>
                            <th>Uang Makan</th>
                            <th>Keterangan Lembur</th>
                            <th>Status</th>
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
                                    placeholder="Cari Jenis Lembur..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Tanggal Lembur..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jam Masuk..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jam Istirahat..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jam Pulang..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jumlah Jam..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Uang Makan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Keterangan Lembur..." />
                            </th>

                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Status..." />
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($item_overtimes as $item_overtime)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item_overtime->employees->nama_karyawan }}</td>
                                <td>{{ $item_overtime->nik_karyawan }}</td>
                                <td>{{ $item_overtime->employees->golongans->golongan }}</td>
                                <td>{{ $item_overtime->employees->positions->jabatan }}</td>
                                <td>{{ $item_overtime->employees->divisions->penempatan }}</td>
                                <td>{{ $item_overtime->jenis_lembur }}</td>
                                <td>{{ \Carbon\Carbon::parse($item_overtime->tanggal_lembur)->isoformat('DD-MM-Y') }}</td>
                                <td>{{ $item_overtime->jam_masuk }}</td>
                                <td>{{ $item_overtime->jam_istirahat }}</td>
                                <td>{{ $item_overtime->jam_pulang }}</td>
                                <td>{{ $item_overtime->jam_lembur }}</td>
                                <td>{{ $item_overtime->uang_makan_lembur }}</td>
                                <td>{{ $item_overtime->keterangan_lembur }}</td>
                                @if ($item_overtime->acc_hrd == null)
                                    <td><span class="badge bg-danger">Belum Direkap</span></td>
                                @else
                                    <td><span class="badge bg-primary">Sudah Direkap</span></td>
                                @endif
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

            // SweetAlert2 Delegasi Event (Supaya tetap bekerja saat halaman DataTables berpindah)
            $('#example2').on('click', '.btn-delete', function(e) {
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data Sertifikasi Kementrian ini akan dihapus permanen!",
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
@endsection
{{-- Datatables --}}

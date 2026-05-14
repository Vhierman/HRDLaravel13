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

@php
    use App\Models\Admin\Employees;
@endphp
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
            <div class="row g-2">
                <div class="col">
                    <form action="{{ route('overtime.exportPDF_rekap_overtime') }}" target="_blank" method="POST"
                        class="d-inline" id="exportPDF" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="awal" class="form-control" value="{{ $awal }}"
                            id="inputawalexportPDF">
                        <input type="hidden" name="akhir" class="form-control" value="{{ $akhir }}"
                            id="inputakhirexportPDF">
                        <input type="hidden" name="penempatan" class="form-control" value="{{ $penempatan }}"
                            id="inputpenempatanexportPDF">
                        <input type="hidden" name="status_kerja" class="form-control" value="{{ $status_kerja }}"
                            id="inputstatuskerjaexportPDF">
                        <button type="submit" class="btn btn-danger px-5 raised w-100">
                            Download PDF Rekap Overtime Periode {{ \Carbon\Carbon::parse($awal)->isoformat('DD-MM-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($akhir)->isoformat('DD-MM-Y') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col">
                    <form action="{{ route('overtime.exportExcell_rekap_overtime') }}" target="_blank" method="POST"
                        class="d-inline" id="exportExcell" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="awal" class="form-control" value="{{ $awal }}"
                            id="inputawalexportExcell">
                        <input type="hidden" name="akhir" class="form-control" value="{{ $akhir }}"
                            id="inputakhirexportExcell">
                        <input type="hidden" name="penempatan" class="form-control" value="{{ $penempatan }}"
                            id="inputpenempatanexportExcell">
                        <input type="hidden" name="status_kerja" class="form-control" value="{{ $status_kerja }}"
                            id="inputstatuskerjaexportExcell">

                        <button type="submit" class="btn btn-success px-5 raised w-100">
                            Download Excell Rekap Overtime Periode {{ \Carbon\Carbon::parse($awal)->isoformat('DD-MM-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($akhir)->isoformat('DD-MM-Y') }}
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
                            <th>Jumlah Jam Lembur</th>
                            <th>Upah Lembur Perjam</th>
                            <th>Jumlah Uang Lembur</th>
                            <th>Uang Makan Lembur</th>
                            <th>Jumlah Uang Diterima</th>
                            <th>Jumlah Uang Diterima Pembulatan</th>
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
                                    placeholder="Cari Jumlah Jam Lembur..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Upah Lembur Perjam..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jumlah Uang Lembur..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Uang Makan Lembur..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Jumlah Uang Diterima..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Total Jumlah Uang Diterima..." />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($item_overtimes as $item_overtime)
                            @php
                                $jumlahjam =
                                    $item_overtime->jumlah_jam_pertama +
                                    $item_overtime->jumlah_jam_kedua +
                                    $item_overtime->jumlah_jam_ketiga +
                                    $item_overtime->jumlah_jam_keempat;
                                $uangmakanlembur = $item_overtime->uang_makan_lembur;
                            @endphp
                            @php
                                $bulanawal = \Carbon\Carbon::parse($awal)->isoformat('MM');
                                $bulanakhir = \Carbon\Carbon::parse($akhir)->isoformat('MM');
                                $tahunawal = \Carbon\Carbon::parse($awal)->isoformat('YYYY');
                                $tahunakhir = \Carbon\Carbon::parse($akhir)->isoformat('YYYY');

                                $collection = Employees::with([
                                    'positions',
                                    'history_salaries',
                                    'rekap_salaries' => function ($query) use (
                                        $bulanawal,
                                        $bulanakhir,
                                        $tahunawal,
                                        $tahunakhir,
                                    ) {
                                        $query
                                            ->whereMonth('periode_awal', $bulanawal)
                                            ->whereMonth('periode_akhir', $bulanakhir)
                                            ->whereYear('periode_awal', $tahunawal)
                                            ->whereYear('periode_akhir', $tahunakhir);
                                    },
                                    'overtimes' => function ($query) use ($awal, $akhir) {
                                        $query
                                            ->whereNotNull('acc_hrd')
                                            ->whereBetween('tanggal_lembur', [$awal, $akhir]);
                                    },
                                ])
                                    ->where('id', $item_overtime->employees_id)
                                    ->whereHas('overtimes', function ($query) use ($awal, $akhir) {
                                        $query
                                            ->whereNotNull('acc_hrd')
                                            ->whereBetween('tanggal_lembur', [$awal, $akhir]);
                                    })
                                    ->whereHas('rekap_salaries', function ($query) use (
                                        $bulanawal,
                                        $bulanakhir,
                                        $tahunawal,
                                        $tahunakhir,
                                    ) {
                                        $query
                                            ->whereMonth('periode_akhir', $bulanawal)
                                            ->whereYear('periode_akhir', $tahunawal);
                                    })
                                    ->first();
                            @endphp
                            @php
                                $namakaryawan = $collection->nama_karyawan;
                                $jabatan = $collection->positions->jabatan;
                                $nomorrekening = $collection->nomor_rekening;
                                $rekapSalary = $collection->rekap_salaries->first();
                                $upahlemburperjam = $rekapSalary?->upah_lembur_perjam ?? 0;
                                $jumlahuanglembur = $upahlemburperjam * $jumlahjam;
                                $jumlahuangditerima = $jumlahuanglembur + $uangmakanlembur;
                                $jumlahuangditerimapembulatan = ceil($jumlahuangditerima);
                                if (
                                    substr($jumlahuangditerimapembulatan, -2) > 50 &&
                                    substr($jumlahuangditerimapembulatan, -2) < 100
                                ) {
                                    $total_jumlahuangditerima = round($jumlahuangditerimapembulatan, -2);
                                } elseif (
                                    substr($jumlahuangditerimapembulatan, -2) < 50 &&
                                    substr($jumlahuangditerimapembulatan, -2) > 0
                                ) {
                                    $total_jumlahuangditerima = round($jumlahuangditerimapembulatan, -2) + 100;
                                } elseif (substr($jumlahuangditerimapembulatan, -2) <= 0) {
                                    $total_jumlahuangditerima = round($jumlahuangditerimapembulatan, -2);
                                } elseif (substr($jumlahuangditerimapembulatan, -2) == 50) {
                                    $total_jumlahuangditerima = round($jumlahuangditerimapembulatan, -2);
                                } else {
                                    $total_jumlahuangditerima = 0;
                                }
                            @endphp

                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item_overtime->employees->nama_karyawan }}</td>
                                <td>{{ $item_overtime->nik_karyawan }}</td>
                                <td>{{ $item_overtime->employees->golongans->golongan }}</td>
                                <td>{{ $item_overtime->employees->positions->jabatan }}</td>
                                <td>{{ $jumlahjam }}</td>
                                <td>{{ number_format($upahlemburperjam) }}</td>
                                <td>{{ number_format($jumlahuanglembur) }}</td>
                                <td>{{ number_format($uangmakanlembur) }}</td>
                                <td>{{ number_format($jumlahuangditerima) }}</td>
                                <td>{{ number_format($total_jumlahuangditerima) }}</td>
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

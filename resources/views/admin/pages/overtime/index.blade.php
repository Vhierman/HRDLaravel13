@extends('admin.layouts.base')
@section('title', 'Data Overtimes');
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtimes</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Overtime Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    @php
        $userRole = Auth::user()->roles;
        $canProcess = in_array($userRole, ['admin', 'hrd', 'leader']);
        $canRekap = in_array($userRole, ['admin', 'hrd']);
        $canExport = in_array($userRole, ['admin', 'hrd', 'accounting']);
        $canView = in_array($userRole, ['accounting']);
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                @if ($canView)
                    <div class="col-md-4">
                        <a href="{{ route('overtime.lihat_overtime') }}"
                            class="btn btn-primary px-5 btn-lg py-3 w-100">Lihat
                            Data</a>
                    </div>
                @endif
                @if ($canProcess)
                    <div class="col-md-3">
                        <a href="{{ route('overtime.lihat_overtime') }}"
                            class="btn btn-primary px-5 btn-lg py-3 w-100">Lihat
                            Data</a>
                    </div>
                @endif
                @if ($canProcess)
                    <div class="col-md-3">
                        <a href="{{ route('overtime.create') }}" class="btn btn-success px-5 btn-lg py-3 w-100">Tambah
                            Data</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_edit_overtime') }}"
                            class="btn btn-warning px-5 btn-lg py-3 w-100">Edit
                            Data</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_hapus_overtime') }}"
                            class="btn btn-danger px-5 btn-lg py-3 w-100">Hapus Data</a>
                    </div>
                @endif

                @if ($canRekap)
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_approve_overtime') }}"
                            class="btn btn-primary px-5 btn-lg py-3 w-100">Rekap Data</a>
                    </div>
                @endif



                @if ($canRekap)
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_cetak_slip_overtime') }}"
                            class="btn btn-success px-5 btn-lg py-3 w-100">Cetak Slip</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_cetak_rekap_overtime') }}"
                            class="btn btn-warning px-5 btn-lg py-3 w-100">Cetak Rekap</a>
                    </div>
                @endif

                @if ($canView)
                    <div class="col-md-4">
                        <a href="{{ route('overtime.form_cetak_slip_overtime') }}"
                            class="btn btn-success px-5 btn-lg py-3 w-100">Cetak Slip</a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('overtime.form_cetak_rekap_overtime') }}"
                            class="btn btn-warning px-5 btn-lg py-3 w-100">Cetak Rekap</a>
                    </div>
                @endif

                @if ($canRekap)
                    <div class="col-md-3">
                        <a href="{{ route('overtime.form_cancel_approve_overtime') }}"
                            class="btn btn-danger px-5 btn-lg py-3 w-100">Cancel Rekap</a>
                    </div>
                    <div class="col-md-12">
                        <a href="{{ route('overtime.upah_lembur_perjam') }}"
                            class="btn btn-grd btn-grd-branding px-5 btn-lg py-3 w-100">Upah Lembur Perjam</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <div id="chartOvertime"></div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        const overtimeData = @json($result);

        Highcharts.chart('chartOvertime', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Tren Overtime Karyawan Periode Tahun {{ $tahun }}'
            },

            xAxis: {
                categories: [
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ]
            },
            yAxis: {
                title: {
                    text: 'Satuan Jam'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            credits: {
                enabled: false
            },
            series: [

                {
                    name: 'Produksi',
                    data: Object.values(overtimeData.Produksi)
                },

                {
                    name: 'PDC',
                    data: Object.values(overtimeData.PDC)
                },

                {
                    name: 'Warehouse',
                    data: Object.values(overtimeData.Warehouse)
                },

                {
                    name: 'Delivery',
                    data: Object.values(overtimeData.Delivery)
                }

            ]
        });
    </script>
@endsection

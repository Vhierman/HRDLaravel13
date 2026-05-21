@extends('admin.layouts.base')

@section('title', 'Rekap Absensi')

@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Laporan</div>

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">
                        Rekap Absensi
                    </li>
                </ol>
            </nav>
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
                text: 'Overtime Karyawan Periode Tahun {{ $tahun }}'
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

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
            <div id="chartTurnover"></div>
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
        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
        const dataMasuk = @json($dataMasuk);
        const dataKeluar = @json($dataKeluar);
        Highcharts.chart('chartTurnover', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Turnover Karyawan Periode Tahun {{ $tahun }}'
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
                    text: 'Temperature (°C)'
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
            series: [{
                    name: 'Karyawan Masuk',
                    data: dataMasuk
                },
                {
                    name: 'Karyawan Keluar',
                    data: dataKeluar
                }
            ]
        });
    </script>
@endsection

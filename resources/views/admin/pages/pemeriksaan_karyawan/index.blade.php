@section('css')
    <style>
    </style>
@endsection

@extends('admin.layouts.base')
@section('title', 'Data Pemeriksaan Karyawan');

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-3">
                    <a href="{{ route('pemeriksaan_karyawan.data_faskes') }}" class="btn btn-primary px-5 raised">
                        Data Faskes
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pemeriksaan_karyawan.form_tambah_data_pemeriksaan') }}"
                        class="btn btn-primary px-5 raised">
                        Tambah Data Pemeriksaan
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pemeriksaan_karyawan.form_lihat_data_pemeriksaan') }}"
                        class="btn btn-primary px-5 raised">
                        Lihat Data Pemeriksaan
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pemeriksaan_karyawan.form_lihat_statistik_pemeriksaan') }}"
                        class="btn btn-primary px-5 raised">
                        Lihat Statistik Pemeriksaan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-xl-4">
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $total_karyawan }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Karyawan
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Periode Tahun {{ $currentYear }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $karyawan_mcu_tahun_ini }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Man Power (Sudah MCU)
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Periode Tahun {{ $currentYear }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $karyawan_belum_mcu_tahun_ini }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Man Power (Belum MCU)
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Periode Tahun {{ $currentYear }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $total_faskes }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Total Faskes
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Periode Tahun {{ $currentYear }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div id="statusKelayakanChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div id="trendMcuChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div id="jenisPemeriksaanChart"></div>
                </div>
            </div>
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
        document.addEventListener('DOMContentLoaded', function() {

            Highcharts.chart('trendMcuChart', {

                chart: {
                    type: 'line'
                },

                title: {
                    text: 'Jumlah Pemeriksaan MCU per Bulan'
                },



                xAxis: {
                    categories: @json($categories),
                    title: {
                        text: 'Bulan'
                    }
                },

                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: {
                        text: 'Jumlah MCU'
                    }
                },

                tooltip: {
                    shared: true,
                    valueSuffix: ' MCU'
                },

                legend: {
                    enabled: true
                },

                series: [{
                    name: 'MCU',
                    data: @json($seriesData)
                }],

                credits: {
                    enabled: false
                }

            });

        });
    </script>

    <script>
        Highcharts.chart('statusKelayakanChart', {

            chart: {
                type: 'pie'
            },

            title: {
                text: 'Persentase Status Kelayakan'
            },



            tooltip: {
                pointFormat: '<b>{point.y} Orang</b><br>' +
                    '{point.percentage:.2f}%'
            },

            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },

            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>' +
                            '{point.y} Orang<br>' +
                            '{point.percentage:.2f}%'
                    },
                    showInLegend: true
                }
            },


            series: [{
                name: 'Jumlah MCU',
                colorByPoint: true,
                data: @json($pieData)
            }],

            credits: {
                enabled: false
            }

        });
    </script>

    <script>
        Highcharts.chart('jenisPemeriksaanChart', {

            chart: {
                type: 'pie'
            },

            title: {
                text: 'Persentase Jenis Pemeriksaan'
            },

            tooltip: {
                pointFormat: '<b>{point.y} Pemeriksaan</b><br>' +
                    '{point.percentage:.2f}%'
            },

            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true,

                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>' +
                            '{point.y} Pemeriksaan<br>' +
                            '{point.percentage:.2f}%'
                    }
                }
            },

            series: [{
                name: 'Jumlah Pemeriksaan',
                colorByPoint: true,
                data: @json($pieDataJenisPemeriksaan)
            }],

            credits: {
                enabled: false
            }
        });
    </script>




@endsection

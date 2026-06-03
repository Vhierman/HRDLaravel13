@section('css')
    <style>
    </style>
@endsection

@extends('admin.layouts.base')
@section('title', 'Statistik Pemeriksaan Karyawan');

@section('content')
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
                    <p class="mb-0">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</p>
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
                    <p class="mb-0">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</p>
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
                    <p class="mb-0">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</p>
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
                    <p class="mb-0">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</p>
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
                    <div id="penempatanChart"></div>
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

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="trenBulananChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="trenHarianChart"></div>
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
            Highcharts.chart('trenBulananChart', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Jumlah Pemeriksaan MCU per Bulan'
                },

                xAxis: {
                    categories: @json($categoriesBulan),
                    gridLineWidth: 1, // Garis pandu vertikal tipis
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: {
                        text: 'Jumlah Karyawan Diperiksa (Orang)'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' Karyawan'
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true // Menampilkan angka total langsung di atas titik koordinat
                        },
                        enableMouseTracking: true
                    }
                },
                series: [{
                    name: 'Total Pasien MCU',
                    data: @json($seriesDataBulan),
                    color: '#198754', // Menggunakan warna hijau sukses (emerald-style) untuk tren bulanan
                    lineWidth: 3,
                    marker: {
                        radius: 6,
                        fillColor: '#ffffff',
                        lineWidth: 3,
                        lineColor: '#198754'
                    }
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
                text: 'Persentase Berdasarkan Status Kelayakan'
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
                text: 'Persentase Berdasarkan Jenis Pemeriksaan'
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriesData = @json($categoriesPenempatan);
            const seriesDataRaw = @json($seriesDataPenempatan);

            Highcharts.chart('penempatanChart', {
                chart: {
                    type: 'column' // Ubah jadi 'bar' jika ingin grafik horizontal
                },
                title: {
                    text: 'Persentase Berdasarkan Penempatan'
                },

                xAxis: {
                    categories: categoriesData,
                    crosshair: true,
                    title: {
                        text: 'Divisi / Penempatan'
                    }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: {
                        text: 'Jumlah Pemeriksaan (Orang)'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">Divisi {point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">Jumlah: </td>' +
                        '<td style="padding:0"><b>{point.y} Orang</b></td></tr>' +
                        '<tr><td style="color:{series.color};padding:0">Persentase: </td>' +
                        '<td style="padding:0"><b>{point.percentage_custom}%</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            // Menampilkan Jumlah dan Persentase tepat di atas Bar Chart
                            format: '{point.y} org ({point.percentage_custom}%)'
                        }
                    }
                },
                series: [{
                    name: 'Karyawan Terperiksa',
                    colorByPoint: true, // Membuat warna tiap bar berbeda otomatis
                    data: seriesDataRaw
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('trenHarianChart', {
                chart: {
                    type: 'line' // Menggunakan model Line Chart
                },
                title: {
                    text: 'Jumlah Pemeriksaan MCU per Hari'
                },

                xAxis: {
                    categories: @json($categoriesTanggal),
                    gridLineWidth: 1, // Memberikan garis vertikal halus di background agar mudah dibaca
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false,
                    title: {
                        text: 'Jumlah Karyawan (Orang)'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' Orang'
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true // Menampilkan angka jumlah di atas setiap titik plot/node
                        },
                        enableMouseTracking: true
                    }
                },
                series: [{
                    name: 'Jumlah Pemeriksaan',
                    data: @json($seriesDataTanggal),
                    color: '#0d6efd', // Warna garis utama biru
                    lineWidth: 3,
                    marker: {
                        radius: 5,
                        fillColor: '#ffffff',
                        lineWidth: 2,
                        lineColor: '#0d6efd' // Modifikasi titik plot lingkaran kosong di tengah
                    }
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection

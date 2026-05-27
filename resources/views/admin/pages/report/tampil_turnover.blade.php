@extends('admin.layouts.base')
@section('title', 'Rekap Turnover Karyawan')
@section('content')

    <div class="row row-cols-1 row-cols-xl-4">
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $turnover }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Turnover Rate (%)
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
                            <h2 class="mb-0">{{ $jumlahKeluar }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Karyawan Keluar
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
                            <h2 class="mb-0">{{ $rataRataKaryawan }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Rata Rata Karyawan
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
                            <h2 class="mb-0">{{ $total_karyawan_masuk }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Karyawan Masuk
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div id="chartTurnover"></div>
        </div>
    </div>

    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartTurnoverBulanan"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartTurnoverDivisi"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartAlasanKeluar"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartAlasanKeluarBar"></div>
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
        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
        const dataMasuk = @json($dataMasuk);
        const dataKeluar = @json($dataKeluar);
        Highcharts.chart('chartTurnover', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Karyawan Masuk Dan Keluar Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}'
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
                    text: 'Jumlah Karyawan'
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartTurnoverBulanan', {
                chart: {
                    type: 'line' // Menggunakan tipe grafik garis untuk melihat tren naik/turun
                },
                title: {
                    text: 'Tren Turnover Rate Karyawan Bulanan'
                },
                subtitle: {
                    text: 'Analisis Perputaran Periode Januari - Desember'
                },
                xAxis: {
                    // Sumbu X merepresentasikan urutan bulan dalam setahun
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                        'Nov', 'Des'
                    ],
                    title: {
                        text: 'Bulan'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Persentase Turnover (%)'
                    },
                    labels: {
                        format: '{value}%'
                    }
                },
                tooltip: {
                    shared: true,
                    crosshairs: true, // Menampilkan garis bantu tegak lurus saat kursor menunjuk titik data
                    valueSuffix: '%'
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true, // Menampilkan angka persentase tepat di atas titik koordinat garis
                            format: '{point.y:.2f}%'
                        },
                        enableMouseTracking: true
                    }
                },
                series: [{
                    name: 'Turnover Rate',
                    data: {!! $dataTurnoverBulanan !!}, // Array berisi 12 data angka dari controller
                    color: '#0d6efd', // Menggunakan warna biru atau sesuaikan tema dashboard Anda
                    marker: {
                        radius: 4,
                        symbol: 'circle'
                    }
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartTurnoverDivisi', {
                chart: {
                    type: 'column' // UBAH KE 'column' agar nama divisi berada di bawah (Sumbu X)
                },
                title: {
                    text: 'Turnover Rate Karyawan Berdasarkan Department'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d M Y') }}'
                },
                xAxis: {
                    categories: {!! $categoriesDivisi !!}, // Sumbu X sekarang otomatis berisi Nama Divisi
                    title: {
                        text: 'Nama Penempatan / Divisi' // Label untuk sumbu X
                    },
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Persentase Turnover (%)' // Sumbu Y berubah menjadi nilai persen
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.2f} %</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: { // Sesuaikan dari 'bar' menjadi 'column'
                        dataLabels: {
                            enabled: true, // Angka persentase muncul di atas balok grafik
                            format: '{point.y:.2f}%'
                        }
                    }
                },
                series: [{
                    name: 'Turnover Rate', // Ini adalah nama Legenda/Keterangan Warna Grafik
                    data: {!! $ratesTurnoverDivisi !!},
                    color: '#dc3545'
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartAlasanKeluar', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie' // Set jenis grafik menjadi Lingkaran / Pie
                },
                title: {
                    text: 'Persentase Keterangan Karyawan Keluar'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d M Y') }} (Total: {{ $total_karyawan_keluar }} Orang)'
                },
                tooltip: {
                    // Saat kursor diarahkan, akan memunculkan nama alasan, persentase, dan jumlah orang asli
                    pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b> ({point.v} orang)'
                },
                accessibility: {
                    point: {
                        valueDescriptionFormat: '{index}. {x} data, {y}%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            // Format label luar chart: Menampilkan Nama Alasan dan Persentasenya
                            format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                            style: {
                                fontSize: '12px'
                            }
                        },
                        showInLegend: true // Menampilkan legenda kotak warna di bagian bawah grafik
                    }
                },
                series: [{
                    name: 'Porsi',
                    colorByPoint: true,
                    data: {!! $pieData !!} // Inject json data dari controller
                }],
                credits: {
                    enabled: false // Menghapus watermark highcharts.com
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartAlasanKeluarBar', {
                chart: {
                    type: 'bar' // Menggunakan tipe 'bar' untuk batang horizontal samping
                },
                title: {
                    text: 'Persentase Alasan Karyawan Keluar'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d M Y') }} (Total: {{ $jumlahKeluar }} Orang)'
                },
                xAxis: {
                    categories: {!! $categoriesAlasan !!}, // Berisi nama alasan/keterangan keluar
                    title: {
                        text: null
                    },
                    gridLineWidth: 1
                },
                yAxis: {
                    min: 0,
                    max: 100, // Membatasi persentase maksimal hingga 100%
                    title: {
                        text: 'Porsi Persentase (%)',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify',
                        format: '{value}%'
                    }
                },
                tooltip: {
                    valueSuffix: ' %'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true, // Memunculkan nilai persentase di ujung batang
                            format: '{point.y:.2f}%'
                        },
                        colorByPoint: true // Mengaktifkan variasi warna otomatis tiap batang alasan agar menarik
                    }
                },
                series: [{
                    name: 'Persentase Kontribusi',
                    data: {!! $ratesAlasan !!}, // Array nilai angka persentase dari controller
                }],
                colors: ['#4f6bed', '#ffb03a', '#ff4f4f', '#2ec4b6',
                    '#a370f7'
                ], // Custom palet warna soft premium
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection

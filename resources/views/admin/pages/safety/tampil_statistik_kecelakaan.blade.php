@extends('admin.layouts.base')
@section('title', 'Data Safety');
@section('content')

    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4 fs-4">Data Statistik Kecelakaan Kerja Periode
                {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</h5>
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
                <div class="col">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div
                                    class="wh-48 d-flex bg-danger text-danger bg-opacity-10 align-items-center justify-content-center rounded-circle">
                                    <span class="material-icons-outlined">emergency</span>
                                </div>
                                <div class="">
                                    <div class="d-flex align-items-center align-self-end text-success mb-1">
                                        <p class="mb-0">Kecelakaan Berat</p>
                                    </div>
                                    <h4 class="mb-0">{{ $rekap_fatality }}</h4>
                                    <p class="mb-0">Fatality</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div
                                    class="wh-48 d-flex bg-primary text-primary bg-opacity-10 align-items-center justify-content-center rounded-circle">
                                    <span class="material-icons-outlined">person_off</span>
                                </div>
                                <div class="">
                                    <div class="d-flex align-items-center align-self-end text-success mb-1">
                                        <p class="mb-0">Lost Working Day</p>
                                    </div>
                                    <h4 class="mb-0">{{ $rekap_lwd }}</h4>
                                    <p class="mb-0">LWD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div
                                    class="wh-48 d-flex bg-info text-info bg-opacity-10 align-items-center justify-content-center rounded-circle">
                                    <span class="material-icons-outlined">emoji_people</span>
                                </div>
                                <div class="">
                                    <div class="d-flex align-items-center align-self-end text-success mb-1">
                                        <p class="mb-0">Non Lost Working Day</p>
                                    </div>
                                    <h4 class="mb-0">{{ $rekap_non_lwd }}</h4>
                                    <p class="mb-0">Non LWD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div
                                    class="wh-48 d-flex bg-success text-success bg-opacity-10 align-items-center justify-content-center rounded-circle">
                                    <span class="material-icons-outlined">motorcycle</span>
                                </div>
                                <div class="">
                                    <div class="d-flex align-items-center align-self-end text-success mb-1">
                                        <p class="mb-0">Traffic Accident </p>
                                    </div>
                                    <h4 class="mb-0">{{ $rekap_traffic }}</h4>
                                    <p class="mb-0">Traffic Accident</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>

    {{-- StatistikHarian --}}
    <div class="row mt-2">
        <div class="col-md-12">
            <div id="containerStatistikHarian"></div>
        </div>
    </div>
    {{-- StatistikHarian --}}

    <div class="row mt-2">
        {{-- Kecelakaan Berdasarkan Jenis Kecelakaan --}}
        <div class="col-md-6">
            <div id="containerKecelakaanBerdasarkanJenisKecelakaan"></div>
        </div>
        {{-- Kecelakaan Berdasarkan Jenis Kecelakaan --}}
        {{-- Kecelakaan Berdasarkan Penempatan --}}
        <div class="col-md-6">
            <div id="containerKecelakaanBerdasarkanPenempatan"></div>
        </div>
        {{-- Kecelakaan Berdasarkan Penempatan --}}
    </div>

    <div class="row mt-2">
        {{-- Kecelakaan Berdasarkan Lokasi Kecelakaan --}}
        <div class="col-md-6">
            <div id="containerKecelakaanBerdasarkanLokasiKecelakaan"></div>
        </div>
        {{-- Kecelakaan Berdasarkan Lokasi Kecelakaan --}}
        {{-- Hari Hilang Berdasarkan Penempatan --}}
        <div class="col-md-6">
            <div id="containerKecelakaanBerdasarkanHariHilang"></div>
        </div>
        {{-- Hari Hilang Berdasarkan Penempatan --}}
    </div>

@endsection

@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Chart --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    {{-- StatistikHarian --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Membaca kiriman variabel array berformat JSON dari Controller Laravel
            const xCategories = {!! $categories_pertama !!};
            const seriesFatality = {!! $fatality !!};
            const seriesLWD = {!! $lwd !!};
            const seriesNonLWD = {!! $non_lwd !!};
            const seriesTraffic = {!! $traffic !!};

            Highcharts.chart('containerStatistikHarian', {
                chart: {
                    type: 'line' // Menentukan grafik bertipe LINE CHART
                },
                title: {
                    text: 'Grafik Pemantauan Kasus Insiden K3 Harian',
                    align: 'left'
                },
                subtitle: {
                    text: 'Analisis data berdasarkan kolom kategori_kecelakaan',
                    align: 'left'
                },
                xAxis: {
                    categories: xCategories, // Memasang list array tanggal pada sumbu horizontal X
                    tickInterval: 1,
                    crosshair: true,
                    labels: {
                        rotate: -45, // Memiringkan label tanggal agar tidak saling bertabrakan
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    allowDecimals: false, // Memaksa angka sumbu Y berupa bilangan bulat (1, 2, 3...)
                    title: {
                        text: 'Jumlah Kasus Kecelakaan'
                    }
                },
                tooltip: {
                    shared: true, // Menampilkan detail semua kategori sekaligus dalam satu kotak info harian
                    useHTML: true
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true // Menampilkan angka total kasus tepat di atas titik simpul garis
                        },
                        enableMouseTracking: true
                    }
                },
                // Mapping data array ke masing-masing objek jalur garis (Line Series)
                series: [{
                        name: 'Fatality',
                        data: seriesFatality,
                        color: '#d63031', // Merah Solid
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Lost Working Day (LWD)',
                        data: seriesLWD,
                        color: '#e17055', // Oranye
                        marker: {
                            symbol: 'diamond'
                        }
                    },
                    {
                        name: 'Non Lost Working Day',
                        data: seriesNonLWD,
                        color: '#2ecc71', // Hijau
                        marker: {
                            symbol: 'square'
                        }
                    },
                    {
                        name: 'Traffic Accident',
                        data: seriesTraffic,
                        color: '#0984e3', // Biru
                        marker: {
                            symbol: 'triangle'
                        }
                    }
                ]
            });
        });
    </script>

    {{-- Kecelakaan Berdasarkan Jenis Kecelakaan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Mengambil data JSON dari Controller Laravel
            const dataSafety = {!! $chartData !!};

            Highcharts.chart('containerKecelakaanBerdasarkanJenisKecelakaan', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Data Kecelakaan Berdasarkan Jenis Kecelakaan',
                    align: 'left'
                },
                subtitle: {
                    text: 'Persentase Total Kasus Berdasarkan Jenis Kecelakaan',
                    align: 'left'
                },
                tooltip: {
                    // Menampilkan jumlah asli dan persentasenya saat kursor diarahkan ke chart
                    pointFormat: '{series.name}: <b>{point.y} Kasus</b> ({point.percentage:.1f}%)'
                },
                accessibility: {
                    point: {
                        valueDescriptionFormat: '{index}. {xDescription}, {value} kasus.'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            // Menampilkan label nama jenis kecelakaan dan persentase bulatnya di luar lingkaran
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        showInLegend: true // Menampilkan legenda kotak warna di bagian bawah chart
                    }
                },
                series: [{
                    name: 'Total Insiden',
                    colorByPoint: true,
                    data: dataSafety // Memasukkan array data dari controller di sini
                }]
            });
        });
    </script>

    {{-- Kecelakaan Berdasarkan Penempatan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Membaca data JSON dari Controller Laravel
            const dataPenempatan = {!! $chartDataDivisi !!};

            Highcharts.chart('containerKecelakaanBerdasarkanPenempatan', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Data Kecelakaan Berdasarkan Penempatan',
                    align: 'left',
                    style: {
                        fontWeight: 'bold'
                    }
                },
                subtitle: {
                    text: 'Persentase Total Kasus Berdasarkan Lokasi Penempatan Divisi',
                    align: 'left'
                },
                tooltip: {
                    // Menampilkan nama lokasi, jumlah kasus asli, dan persentase otomatisnya
                    pointFormat: '{series.name}: <b>{point.y} Kasus</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            // Menampilkan Label nama penempatan dan persentasenya di grafik
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            distance: 20
                        },
                        showInLegend: true // Menampilkan legenda kotak warna di bagian bawah
                    }
                },
                series: [{
                    name: 'Jumlah Insiden',
                    colorByPoint: true,
                    data: dataPenempatan // Data array hasil mapping dari Laravel
                }]
            });
        });
    </script>

    {{-- Kecelakaan Berdasarkan Lokasi Kecelakaan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Mengambil data JSON dari Controller Laravel
            const lokasiCategories = {!! $categoriesKeempat !!};
            const totalKasusData = {!! $chartDataLokasiKecelakaan !!};

            Highcharts.chart('containerKecelakaanBerdasarkanLokasiKecelakaan', {
                chart: {
                    type: 'column' // Gunakan 'bar' jika ingin batang horizontal (menyamping)
                },
                title: {
                    text: 'Data Kecelakaan Kerja Berdasarkan Lokasi Kejadian',
                    align: 'left'
                },
                subtitle: {
                    text: 'Data akumulasi total kasus kecelakaan di setiap area kerja',
                    align: 'left'
                },
                xAxis: {
                    categories: lokasiCategories, // Array nama-nama lokasi dimasukkan disini
                    crosshair: true,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Kasus (Insiden)'
                    },
                    allowDecimals: false // Mengunci sumbu Y agar tidak memunculkan angka desimal (.5)
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y} Kasus</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true, // Menampilkan angka total langsung di atas batang grafik
                            format: '{point.y}'
                        }
                    }
                },
                series: [{
                    name: 'Total Kasus',
                    data: totalKasusData, // Array angka jumlah kasus dimasukkan disini
                    color: '#e74c3c' // Memberikan warna merah solid (identik dengan alert/safety)
                }]
            });
        });
    </script>

    {{-- Hari Hilang Berdasarkan Penempatan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const divisiCategories = {!! $categories_hari_hilang !!};
            const kasusSeries = {!! $dataKasusHariHilang !!};
            const hariHilangSeries = {!! $dataHariHilang !!};

            Highcharts.chart('containerKecelakaanBerdasarkanHariHilang', {
                chart: {
                    type: 'line' // <--- Mengubah default global chart menjadi LINE
                },
                title: {
                    text: 'Jumlah Hari Kerja Hilang Berdasarkan Penempatan',
                    align: 'left'
                },
                subtitle: {
                    text: 'Perbandingan Jumlah Insiden vs Total Hari Kerja Hilang Berdasarkan Penempatan',
                    align: 'left'
                },
                xAxis: [{
                    categories: divisiCategories,
                    crosshair: true
                }],
                yAxis: [{ // Sumbu Y Pertama (Kiri) - Kasus
                        allowDecimals: false,
                        title: {
                            text: 'Jumlah Kasus (Insiden)',
                            style: {
                                color: '#3498db'
                            }
                        },
                        labels: {
                            style: {
                                color: '#3498db'
                            }
                        }
                    },
                    { // Sumbu Y Kedua (Kanan) - Hari Hilang
                        title: {
                            text: 'Total Hari Kerja Hilang (Hari)',
                            style: {
                                color: '#e74c3c'
                            }
                        },
                        labels: {
                            style: {
                                color: '#e74c3c'
                            }
                        },
                        opposite: true
                    }
                ],
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    line: { // <--- Mengatur opsi visualisasi khusus untuk grafik garis
                        dataLabels: {
                            enabled: true // Menampilkan angka point di atas setiap titik garis
                        },
                        enableMouseTracking: true
                    }
                },
                series: [{
                        name: 'Jumlah Kasus',
                        type: 'line', // <--- Dipastikan tipe data berbentuk line
                        yAxis: 0,
                        data: kasusSeries,
                        color: '#3498db',
                        marker: {
                            symbol: 'circle' // Simbol titik bulat untuk Jumlah Kasus
                        }
                    },
                    {
                        name: 'Total Hari Hilang',
                        type: 'line', // <--- Dipastikan tipe data berbentuk line
                        yAxis: 1,
                        data: hariHilangSeries,
                        color: '#e74c3c',
                        marker: {
                            symbol: 'diamond' // Simbol titik belah ketupat untuk Hari Hilang agar mudah dibedakan
                        }
                    }
                ]
            });
        });
    </script>
@endsection

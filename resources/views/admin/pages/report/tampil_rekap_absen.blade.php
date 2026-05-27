@extends('admin.layouts.base')
@section('title', 'Rekap Absensi');
@section('content')

    <div class="row row-cols-1 row-cols-xl-4">
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $totalKasusCutiTahunanGlobal }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Kasus Cuti Tahunan
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
                            <h2 class="mb-0">{{ $totalKasusSakitGlobal }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Kasus Sakit
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
                            <h2 class="mb-0">{{ $totalKasusIjinGlobal }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Kasus Ijin
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
                            <h2 class="mb-0">{{ $totalKasusAlpaGlobal }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Kasus Alpa
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
            <h5 class="mb-4">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</h5>
            <div class="row row-cols-12 row-cols-xl-12">
                <div class="col-12 col-xl-12">
                    <figure class="highcharts-figure">
                        <div id="chartTrenAbsensi"></div>
                    </figure>
                </div>
            </div>
        </div>
    </div>


    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="mb-4">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</h5>
                    <div class="row row-cols-12 row-cols-xl-12">
                        <div class="col-12 col-xl-12">
                            <figure class="highcharts-figure">
                                <div id="chartPieAbsensi"></div>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="mb-4">Periode {{ \Carbon\Carbon::parse($tanggal_awal)->isoformat('DD-MM-Y') }} s/d
                        {{ \Carbon\Carbon::parse($tanggal_akhir)->isoformat('DD-MM-Y') }}</h5>
                    <div class="row row-cols-12 row-cols-xl-12">
                        <div class="col-12 col-xl-12">
                            <figure class="highcharts-figure">
                                <div id="container"></div>
                            </figure>
                        </div>
                    </div>
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

    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>

    <script>
        var hasilAbsen = @json($hasil_absen);
        const categories = Object.keys(hasilAbsen);
        const cutiData = categories.map(divisi => hasilAbsen[divisi].cuti_tahunan);
        const sakitData = categories.map(divisi => hasilAbsen[divisi].sakit);
        const ijinData = categories.map(divisi => hasilAbsen[divisi].ijin);
        const alpaData = categories.map(divisi => hasilAbsen[divisi].alpa);

        Highcharts.chart('container', {
            chart: {
                type: 'bar',
                height: 1400
            },
            title: {
                text: 'Rekap Absensi Karyawan'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categories,
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Absensi',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: 'MP'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: [{
                    name: 'Cuti Tahunan',
                    data: cutiData
                },
                {
                    name: 'Sakit',
                    data: sakitData
                },
                {
                    name: 'Ijin',
                    data: ijinData
                },
                {
                    name: 'Alpa',
                    data: alpaData
                }
            ]
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartTrenAbsensi', {
                chart: {
                    type: 'line' // Menggunakan format grafik garis
                },
                title: {
                    text: 'Tren Ketidakhadiran Karyawan Harian'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d M Y') }}'
                },
                xAxis: {
                    categories: {!! $categoriesTanggal !!}, // Array tanggal (Sumbu X)
                    crosshair: true
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Karyawan (Orang)'
                    },
                    min: 0,
                    allowDecimals: false // Mencegah munculnya angka desimal pada jumlah orang
                },
                tooltip: {
                    shared: true, // Menampilkan seluruh status absen dalam satu box info saat kursor diarahkan
                    useHTML: true,
                    headerFormat: '<span style="font-size:12px; font-weight:bold;">Tanggal: {point.key}</span><br/><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0; padding-left:10px;"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>'
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true // Menampilkan angka jumlah tepat di atas titik koordinat garis
                        },
                        enableMouseTracking: true
                    }
                },
                series: {!! $seriesData !!}, // Data multi-series (Sakit, Ijin, Alpa, Cuti) dari controller
                credits: {
                    enabled: false // Menghilangkan watermark highcharts.com
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartPieAbsensi', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Proporsi Alasan Ketidakhadiran Karyawan'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d M Y') }}'
                },
                tooltip: {
                    // Format Tooltip: Keterangan Absen: Nilai Riil Kasus (Persentase 1 Angka Desimal)
                    pointFormat: '{series.name}: <b>{point.y} Kali</b> ({point.percentage:.1f}%)'
                },
                accessibility: {
                    point: {
                        valueDescriptionFormat: '{index}. {x} value {y}.'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            // Menampilkan label nama status beserta persentase kontribusinya langsung di luar lingkaran
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                textOutline: 'none',
                                fontSize: '13px'
                            }
                        },
                        showInLegend: true // Memunculkan legend kotak warna penjelasan di bawah chart
                    }
                },
                series: [{
                    name: 'Volume Absen',
                    colorByPoint: true,
                    data: {!! $pieData !!} // Menyematkan array objek JSON dari controller
                }],
                credits: {
                    enabled: false // Menghapus watermark link highcharts
                }
            });
        });
    </script>
@endsection

@extends('admin.layouts.base')

@section('title', 'Rekap Absensi')

@section('content')

    <div class="row row-cols-1 row-cols-xl-3">
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ number_format($format_rupiah_total, 0, ',', '.') }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Overtime (Rp)
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
                            <h2 class="mb-0">{{ number_format($total_jam_overtime, 0, ',', '.') }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Jam Lembur (Jam)
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
                            <h2 class="mb-0">{{ $jumlah_karyawan_lembur }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Karyawan Lembur
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
            <div id="chartRataRataLembur"></div>
        </div>
    </div>




    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartLemburDivisi"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <div id="chartKaryawanLemburPie"></div>
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
            Highcharts.chart('chartRataRataLembur', {
                chart: {
                    type: 'line' // Menggunakan grafik garis untuk melihat tren durasi waktu
                },
                title: {
                    text: 'Tren Jam Overtime Rata-Rata Harian'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d M Y') }}'
                },
                xAxis: {
                    categories: {!! $categoriesTanggal !!}, // Tanggal lembur pada Sumbu X
                    crosshair: true
                },
                yAxis: {
                    title: {
                        text: 'Rata-rata Durasi (Jam)'
                    },
                    min: 0,
                    labels: {
                        format: '{value} Jam'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' Jam' // Menambahkan satuan 'Jam' di belakang angka tooltip saat kursor diarahkan
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true, // Menampilkan angka rata-rata jam tepat di atas titik koordinat grafik
                            format: '{point.y:.2f} Jam'
                        },
                        enableMouseTracking: true
                    }
                },
                series: [{
                    name: 'Rerata Jam Lembur',
                    data: {!! $seriesRataRata !!}, // Nilai rata-rata jam lembur pada Sumbu Y
                    color: '#ffc107', // Warna kuning jingga khas metrik warning/lembur
                    marker: {
                        radius: 4,
                        symbol: 'circle'
                    }
                }],
                credits: {
                    enabled: false // Menghapus tulisan watermark highcharts.com
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartLemburDivisi', {
                chart: {
                    type: 'column' // DIUBAH KESINI: Mengubah batang menjadi vertikal (Column Chart)
                },
                title: {
                    text: 'Total Jam Overtime Per Department'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d M Y') }}'
                },
                xAxis: {
                    categories: {!! $categoriesDivisi !!}, // SEKARANG BERADA DI SUMBU X (BAGIAN BAWAH)
                    crosshair: true,
                    title: {
                        text: 'Penempatan / Divisi' // Label penjelas sumbu X
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Total Durasi Lembur (Jam)'
                    },
                    labels: {
                        format: '{value} Jam'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y} Jam Kerja</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: { // Diubah dari 'bar' menjadi 'column'
                        dataLabels: {
                            enabled: true, // Memunculkan angka di atas balok grafik
                            format: '{point.y} Jam'
                        },
                        colorByPoint: true // Mewarnai setiap divisi dengan warna yang berbeda
                    }
                },
                series: [{
                    name: 'Total Jam Lembur',
                    data: {!! $seriesJamLembur !!}
                }],
                colors: ['#dc3545', '#fd7e14', '#ffc107', '#198754', '#0d6efd', '#6f42c1'],
                credits: {
                    enabled: false
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('chartKaryawanLemburPie', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Proporsi Jumlah Karyawan Lembur Per Department'
                },
                subtitle: {
                    text: 'Periode: {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d M Y') }}'
                },
                tooltip: {
                    // Menampilkan jumlah orang riil dan persentasenya terhadap total partisipan lembur
                    pointFormat: '{series.name}: <b>{point.y} Orang</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            // Menampilkan Nama Penempatan beserta Jumlah Orang riilnya
                            format: '<b>{point.name}</b>: {point.y} Orang ({point.percentage:.1f}%)',
                            style: {
                                textOutline: 'none' // Menghilangkan bayangan teks agar lebih clean
                            }
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Jumlah Personil',
                    colorByPoint: true,
                    data: {!! $pieDataKaryawan !!} // Mengambil data objek array JSON dari controller
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection

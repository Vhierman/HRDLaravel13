@extends('admin.layouts.base')
@section('title', 'Data Absensi');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Absensi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Absensi Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">

        @if (in_array(Auth::user()->roles, ['admin', 'hrd', 'leader']))
            <div class="card-body">
                <div class="row justify-content-center g-3 text-center">
                    <div class="col-md-3">
                        <a href="{{ route('attendance.form_tampil') }}"
                            class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Data Absensi</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('attendance.create') }}"
                            class="btn btn-grd btn-grd-success px-5 btn-lg py-3 w-100">Tambah Data Absensi</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('attendance.form_edit') }}"
                            class="btn btn-grd btn-grd-info px-5 btn-lg py-3 w-100">Edit Data Absensi</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('attendance.form_hapus') }}"
                            class="btn btn-grd btn-grd-danger px-5 btn-lg py-3 w-100">Hapus Data Absensi</a>
                    </div>
                </div>
                <div class="row justify-content-center g-3 text-center mt-2">
                    <div class="col-md-12">
                        <a href="{{ route('attendance.form_non_absen') }}" class="btn btn-info px-5 btn-lg py-3 w-100">Lihat
                            Data Karyawan Yang Full Hadir</a>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array(Auth::user()->roles, ['accounting']))
            <div class="card-body">
                <div class="row justify-content-center g-3 text-center">
                    <div class="col-md-12">
                        <a href="{{ route('attendance.form_tampil') }}"
                            class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Data Absensi</a>
                    </div>
                </div>
                <div class="row justify-content-center g-3 text-center mt-2">
                    <div class="col-md-12">
                        <a href="{{ route('attendance.form_non_absen') }}" class="btn btn-info px-5 btn-lg py-3 w-100">Lihat
                            Data Karyawan Yang Full Hadir</a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if (in_array(Auth::user()->roles, ['admin', 'hrd', 'accounting']))
        {{-- Chart --}}
        <div class="col-12 col-xl-12">
            <div class="card rounded-4">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Tren Absensi Karyawan Tahun {{ \Carbon\Carbon::now()->year }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartAbsensi"></div>
                </div>
            </div>
        </div>
        {{-- Chart --}}
    @endif
@endsection

@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Parsing data dari Controller Laravel ke JavaScript secara aman
            const seriesData = @json($chartData);

            // 2. Inisialisasi Highcharts Line Chart
            Highcharts.chart('chartAbsensi', {
                chart: {
                    type: 'line', // Menentukan format Line Chart
                    style: {
                        fontFamily: 'system-ui, -apple-system, sans-serif'
                    }
                },
                title: {
                    text: null // Atau gunakan '' jika tidak ingin menampilkan judul
                },
                // Menentukan Sumbu X (Bulan Januari - Desember)
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                        'Nov', 'Des'
                    ],
                    crosshair: true
                },
                // Menentukan Sumbu Y
                yAxis: {
                    title: {
                        text: 'Jumlah Man Power'
                    },
                    allowDecimals: false, // Menghindari angka desimal (misal: 1.5 orang)
                    min: 0
                },
                // Pengaturan ketika pointer mouse mendekati garis (Hover)
                tooltip: {
                    headerFormat: '<span style="font-size:8px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                // Pengaturan visual untuk tipe grafik line
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true // Set ke true jika ingin memunculkan angka langsung di atas titik grafik
                        },
                        enableMouseTracking: true
                    }
                },
                // Memasukkan data dari Laravel. Formatnya langsung match!
                series: seriesData,

                // Pengaturan warna kustom (Opsional - sesuaikan dengan tema HRIS-mu)
                colors: ['#457B9D', '#E9C46A', '#E63946', '#2A9D8F', '#6C757D'],

                // Mengatur posisi legenda/keterangan warna kategori
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                // Menyembunyikan credit tulisan "Highcharts.com" di sudut kanan bawah chart
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection

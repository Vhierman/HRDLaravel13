@section('css')
@endsection

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
                            Data
                            Karyawan Yang Tidak Pernah Absen</a>
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
                            Data
                            Karyawan Yang Tidak Pernah Absen</a>
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
                        <h5 class="mb-0">Rekap Absensi Karyawan Tahun {{ \Carbon\Carbon::now()->year }}</h5>
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
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apex-custom-chart.js') }}"></script>
    <script>
        // 1. Ambil data yang sudah diformat otomatis dari Controller
        const seriesData = {!! json_encode($chartData) !!};

        var options = {
            // 2. Cukup panggil variabelnya di sini. ApexCharts langsung paham strukturnya!
            series: seriesData,

            legend: {
                fontSize: '16px'
            },
            chart: {
                foreColor: "#9ba7b2",
                height: 470,
                type: 'bar',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false,
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    gradientToColors: ['#00c6fb', '#ffcc33', '#b31217', '#00CDAC', '#333399'],
                    shadeIntensity: 1,
                    type: 'vertical',
                    stops: [0, 100, 100, 100]
                },
            },
            colors: ['#005bea', "#ffb347", "#e52d27", "#02AAB0", "#ff00cc"],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    borderRadiusApplication: 'around',
                    borderRadiusWhenStacked: 'last',
                    columnWidth: '50%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 4,
                colors: ["transparent"]
            },
            grid: {
                show: true,
                borderColor: 'rgba(0, 0, 0, 0.15)',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: "dark",
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
                labels: {
                    style: {
                        fontSize: '16px'
                    }
                }
            }
        };

        // Pastikan di HTML kamu sudah ada tag <div id="chartAbsensi"></div>
        var chart = new ApexCharts(document.querySelector("#chartAbsensi"), options);
        chart.render();
    </script>
@endsection

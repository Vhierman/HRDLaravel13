@section('css')
@endsection

@extends('admin.layouts.base')
@section('title', 'Data Absensi');

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Master</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Data</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center g-3 text-center">
                <div class="col-md-3">
                    <a href="{{ route('attendance.form_tampil') }}"
                        class="btn btn-grd btn-grd-deep-blue px-5 btn-lg py-3 w-100">Lihat Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('attendance.create') }}"
                        class="btn btn-grd btn-grd-success px-5 btn-lg py-3 w-100">Tambah Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('attendance.form_edit') }}"
                        class="btn btn-grd btn-grd-info px-5 btn-lg py-3 w-100">Edit Data</a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('attendance.form_hapus') }}"
                        class="btn btn-grd btn-grd-danger px-5 btn-lg py-3 w-100">Hapus Data</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="col-12 col-xl-12">
        <div class="card rounded-4">
            <div class="card-header py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Rekap Absensi Tahun {{ \Carbon\Carbon::now()->year }}</h5>
                </div>
            </div>
            <div class="card-body">
                <div id="chartAbsensi"></div>
            </div>
        </div>
    </div>
    {{-- Chart --}}
@endsection


@section('js')
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apex-custom-chart.js') }}"></script>
    <script>
        var options = {
            series: [{
                name: "Cuti",
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 63, 60, 66]
            }, {
                name: "Sakit",
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 91, 114, 94]
            }, {
                name: "Ijin",
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41, 52, 53, 41]
            }, {
                name: "Alpa",
                data: [70, 85, 66, 45, 36, 64, 98, 45, 52, 98, 45, 52]
            }],
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
                    show: !1,
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    gradientToColors: ['#00c6fb', '#ffcc33', '#00CDAC', '#b31217'],
                    shadeIntensity: 1,
                    type: 'vertical',
                    // opacityFrom: 1.0,
                    // opacityTo: 0.1,
                    stops: [0, 100, 100, 100]
                },
            },
            colors: ['#005bea', "#ffb347", "#02AAB0", "#e52d27"],
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
                show: !0,
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

        var chart = new ApexCharts(document.querySelector("#chartAbsensi"), options);
        chart.render();
    </script>
@endsection

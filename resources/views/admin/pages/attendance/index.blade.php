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
        </div>
    </div>

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
@endsection

@section('js')
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/apexchart/apex-custom-chart.js') }}"></script>
    <script>
        var item_sakit_januari = {{ json_encode($item_sakit_januari) }};
        var item_sakit_februari = {{ json_encode($item_sakit_februari) }};
        var item_sakit_maret = {{ json_encode($item_sakit_maret) }};
        var item_sakit_april = {{ json_encode($item_sakit_april) }};
        var item_sakit_mei = {{ json_encode($item_sakit_mei) }};
        var item_sakit_juni = {{ json_encode($item_sakit_juni) }};
        var item_sakit_juli = {{ json_encode($item_sakit_juli) }};
        var item_sakit_agustus = {{ json_encode($item_sakit_agustus) }};
        var item_sakit_september = {{ json_encode($item_sakit_september) }};
        var item_sakit_oktober = {{ json_encode($item_sakit_oktober) }};
        var item_sakit_november = {{ json_encode($item_sakit_november) }};
        var item_sakit_desember = {{ json_encode($item_sakit_desember) }};
        var item_ijin_januari = {{ json_encode($item_ijin_januari) }};
        var item_ijin_februari = {{ json_encode($item_ijin_februari) }};
        var item_ijin_maret = {{ json_encode($item_ijin_maret) }};
        var item_ijin_april = {{ json_encode($item_ijin_april) }};
        var item_ijin_mei = {{ json_encode($item_ijin_mei) }};
        var item_ijin_juni = {{ json_encode($item_ijin_juni) }};
        var item_ijin_juli = {{ json_encode($item_ijin_juli) }};
        var item_ijin_agustus = {{ json_encode($item_ijin_agustus) }};
        var item_ijin_september = {{ json_encode($item_ijin_september) }};
        var item_ijin_oktober = {{ json_encode($item_ijin_oktober) }};
        var item_ijin_november = {{ json_encode($item_ijin_november) }};
        var item_ijin_desember = {{ json_encode($item_ijin_desember) }};
        var item_alpa_januari = {{ json_encode($item_alpa_januari) }};
        var item_alpa_februari = {{ json_encode($item_alpa_februari) }};
        var item_alpa_maret = {{ json_encode($item_alpa_maret) }};
        var item_alpa_april = {{ json_encode($item_alpa_april) }};
        var item_alpa_mei = {{ json_encode($item_alpa_mei) }};
        var item_alpa_juni = {{ json_encode($item_alpa_juni) }};
        var item_alpa_juli = {{ json_encode($item_alpa_juli) }};
        var item_alpa_agustus = {{ json_encode($item_alpa_agustus) }};
        var item_alpa_september = {{ json_encode($item_alpa_september) }};
        var item_alpa_oktober = {{ json_encode($item_alpa_oktober) }};
        var item_alpa_november = {{ json_encode($item_alpa_november) }};
        var item_alpa_desember = {{ json_encode($item_alpa_desember) }};
        var item_cuti_tahunan_januari = {{ json_encode($item_cuti_tahunan_januari) }};
        var item_cuti_tahunan_februari = {{ json_encode($item_cuti_tahunan_februari) }};
        var item_cuti_tahunan_maret = {{ json_encode($item_cuti_tahunan_maret) }};
        var item_cuti_tahunan_april = {{ json_encode($item_cuti_tahunan_april) }};
        var item_cuti_tahunan_mei = {{ json_encode($item_cuti_tahunan_mei) }};
        var item_cuti_tahunan_juni = {{ json_encode($item_cuti_tahunan_juni) }};
        var item_cuti_tahunan_juli = {{ json_encode($item_cuti_tahunan_juli) }};
        var item_cuti_tahunan_agustus = {{ json_encode($item_cuti_tahunan_agustus) }};
        var item_cuti_tahunan_september = {{ json_encode($item_cuti_tahunan_september) }};
        var item_cuti_tahunan_oktober = {{ json_encode($item_cuti_tahunan_oktober) }};
        var item_cuti_tahunan_november = {{ json_encode($item_cuti_tahunan_november) }};
        var item_cuti_tahunan_desember = {{ json_encode($item_cuti_tahunan_desember) }};
        var item_off_januari = {{ json_encode($item_off_januari) }};
        var item_off_februari = {{ json_encode($item_off_februari) }};
        var item_off_maret = {{ json_encode($item_off_maret) }};
        var item_off_april = {{ json_encode($item_off_april) }};
        var item_off_mei = {{ json_encode($item_off_mei) }};
        var item_off_juni = {{ json_encode($item_off_juni) }};
        var item_off_juli = {{ json_encode($item_off_juli) }};
        var item_off_agustus = {{ json_encode($item_off_agustus) }};
        var item_off_september = {{ json_encode($item_off_september) }};
        var item_off_oktober = {{ json_encode($item_off_oktober) }};
        var item_off_november = {{ json_encode($item_off_november) }};
        var item_off_desember = {{ json_encode($item_off_desember) }};

        var options = {
            series: [{
                name: "Cuti",
                data: [item_cuti_tahunan_januari, item_cuti_tahunan_februari, item_cuti_tahunan_maret,
                    item_cuti_tahunan_april,
                    item_cuti_tahunan_mei, item_cuti_tahunan_juni, item_cuti_tahunan_juli,
                    item_cuti_tahunan_agustus,
                    item_cuti_tahunan_september, item_cuti_tahunan_oktober, item_cuti_tahunan_november,
                    item_cuti_tahunan_desember
                ]
            }, {
                name: "Sakit",
                data: [item_sakit_januari, item_sakit_februari, item_sakit_maret, item_sakit_april,
                    item_sakit_mei, item_sakit_juni, item_sakit_juli, item_sakit_agustus,
                    item_sakit_september, item_sakit_oktober, item_sakit_november, item_sakit_desember
                ]
            }, {
                name: "Ijin",
                data: [item_ijin_januari, item_ijin_februari, item_ijin_maret, item_ijin_april,
                    item_ijin_mei, item_ijin_juni, item_ijin_juli, item_ijin_agustus,
                    item_ijin_september, item_ijin_oktober, item_ijin_november, item_ijin_desember
                ]
            }, {
                name: "Alpa",
                data: [item_alpa_januari, item_alpa_februari, item_alpa_maret, item_alpa_april,
                    item_alpa_mei, item_alpa_juni, item_alpa_juli, item_alpa_agustus,
                    item_alpa_september, item_alpa_oktober, item_alpa_november, item_alpa_desember
                ]
            }, {
                name: "OFF",
                data: [item_off_januari, item_off_februari, item_off_maret, item_off_april,
                    item_off_mei, item_off_juni, item_off_juli, item_off_agustus,
                    item_off_september, item_off_oktober, item_off_november, item_off_desember
                ]
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
                    gradientToColors: ['#00c6fb', '#ffcc33', '#00CDAC', '#b31217', '#E2E2E2'],
                    shadeIntensity: 1,
                    type: 'vertical',
                    // opacityFrom: 1.0,
                    // opacityTo: 0.1,
                    stops: [0, 100, 100, 100]
                },
            },
            colors: ['#005bea', "#ffb347", "#02AAB0", "#e52d27", "#C9D6FF"],
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

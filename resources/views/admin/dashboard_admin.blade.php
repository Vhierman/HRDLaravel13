@extends('admin.layouts.base')
@section('title', 'Dashboard Admin');

@section('content')

    <div class="row row-cols-1 row-cols-xl-4">
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $jumlah_karyawan_all }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Jumlah Seluruh Karyawan
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Seluruh Area Perusahaan</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $jumlah_karyawan_bsd }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Tangerang Selatan
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Penempatan BSD</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $jumlah_karyawan_pdc_daihatsu }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                Sunter,Cibitung,Karawang
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Penempatan PDC Daihatsu</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0">{{ $jumlah_karyawan_greenville }}</h2>
                        </div>
                        <div class="">
                            <p
                                class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-success text-success bg-opacity-10">
                                PK66 & BL
                            </p>
                        </div>
                    </div>
                    <p class="mb-0">Penempatan Greenville</p>
                </div>
            </div>
        </div>
    </div><!--end row-->

    <div class="row row-cols-12 row-cols-xl-12">
        <div class="col-12 col-xl-12">
            <div id="containerpenempatandetail"></div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <div id="containerkontrak"></div>
        </div>
        <div class="col-md-6">
            <div id="containerstatusnikah"></div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <div id="containerjeniskelamin"></div>
        </div>
        <div class="col-md-6">
            <div id="containeragama"></div>
        </div>
    </div>

@endsection

@section('js')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    {{-- Chart Penempatan Detail --}}
    <script>
        var pkwtt_bl = {{ json_encode($dataPenempatan[18]['PKWTT'] ?? 0) }};
        var pkwt_bl = {{ json_encode($dataPenempatan[18]['PKWT'] ?? 0) }};
        var harian_bl = {{ json_encode($dataPenempatan[18]['Harian'] ?? 0) }};
        var outsourcing_bl = {{ json_encode($dataPenempatan[18]['Outsourcing'] ?? 0) }};
        var pkwtt_ppc = {{ json_encode($dataPenempatan[5]['PKWTT'] ?? 0) }};
        var pkwt_ppc = {{ json_encode($dataPenempatan[5]['PKWT'] ?? 0) }};
        var harian_ppc = {{ json_encode($dataPenempatan[5]['Harian'] ?? 0) }};
        var outsourcing_ppc = {{ json_encode($dataPenempatan[5]['Outsourcing'] ?? 0) }};
        var pkwtt_produksi = {{ json_encode($dataPenempatan[11]['PKWTT'] ?? 0) }};
        var pkwt_produksi = {{ json_encode($dataPenempatan[11]['PKWT'] ?? 0) }};
        var harian_produksi = {{ json_encode($dataPenempatan[11]['Harian'] ?? 0) }};
        var outsourcing_produksi = {{ json_encode($dataPenempatan[11]['Outsourcing'] ?? 0) }};
        var pkwtt_rm = {{ json_encode($dataPenempatan[14]['PKWTT'] ?? 0) }};
        var pkwt_rm = {{ json_encode($dataPenempatan[14]['PKWT'] ?? 0) }};
        var harian_rm = {{ json_encode($dataPenempatan[14]['Harian'] ?? 0) }};
        var outsourcing_rm = {{ json_encode($dataPenempatan[14]['Outsourcing'] ?? 0) }};
        var pkwtt_fg = {{ json_encode($dataPenempatan[15]['PKWTT'] ?? 0) }};
        var pkwt_fg = {{ json_encode($dataPenempatan[15]['PKWT'] ?? 0) }};
        var harian_fg = {{ json_encode($dataPenempatan[15]['Harian'] ?? 0) }};
        var outsourcing_fg = {{ json_encode($dataPenempatan[15]['Outsourcing'] ?? 0) }};
        var pkwtt_del_prod = {{ json_encode($dataPenempatan[13]['PKWTT'] ?? 0) }};
        var pkwt_del_prod = {{ json_encode($dataPenempatan[13]['PKWT'] ?? 0) }};
        var harian_del_prod = {{ json_encode($dataPenempatan[13]['Harian'] ?? 0) }};
        var outsourcing_del_prod = {{ json_encode($dataPenempatan[13]['Outsourcing'] ?? 0) }};
        var pkwtt_del = {{ json_encode($dataPenempatan[12]['PKWTT'] ?? 0) }};
        var pkwt_del = {{ json_encode($dataPenempatan[12]['PKWT'] ?? 0) }};
        var harian_del = {{ json_encode($dataPenempatan[12]['Harian'] ?? 0) }};
        var outsourcing_del = {{ json_encode($dataPenempatan[12]['Outsourcing'] ?? 0) }};
        var pkwtt_blok_e = {{ json_encode($dataPenempatan[16]['PKWTT'] ?? 0) }};
        var pkwt_blok_e = {{ json_encode($dataPenempatan[16]['PKWT'] ?? 0) }};
        var harian_blok_e = {{ json_encode($dataPenempatan[16]['Harian'] ?? 0) }};
        var outsourcing_blok_e = {{ json_encode($dataPenempatan[16]['Outsourcing'] ?? 0) }};
        var pkwtt_sunter = {{ json_encode($dataPenempatan[19]['PKWTT'] ?? 0) }};
        var pkwt_sunter = {{ json_encode($dataPenempatan[19]['PKWT'] ?? 0) }};
        var harian_sunter = {{ json_encode($dataPenempatan[19]['Harian'] ?? 0) }};
        var outsourcing_sunter = {{ json_encode($dataPenempatan[19]['Outsourcing'] ?? 0) }};
        var pkwtt_cibitung = {{ json_encode($dataPenempatan[20]['PKWTT'] ?? 0) }};
        var pkwt_cibitung = {{ json_encode($dataPenempatan[20]['PKWT'] ?? 0) }};
        var harian_cibitung = {{ json_encode($dataPenempatan[20]['Harian'] ?? 0) }};
        var outsourcing_cibitung = {{ json_encode($dataPenempatan[20]['Outsourcing'] ?? 0) }};
        var pkwtt_karawang = {{ json_encode($dataPenempatan[21]['PKWTT'] ?? 0) }};
        var pkwt_karawang = {{ json_encode($dataPenempatan[21]['PKWT'] ?? 0) }};
        var harian_karawang = {{ json_encode($dataPenempatan[21]['Harian'] ?? 0) }};
        var outsourcing_karawang = {{ json_encode($dataPenempatan[21]['Outsourcing'] ?? 0) }};
        var pkwtt_marketing = {{ json_encode($dataPenempatan[2]['PKWTT'] ?? 0) }};
        var pkwt_marketing = {{ json_encode($dataPenempatan[2]['PKWT'] ?? 0) }};
        var harian_marketing = {{ json_encode($dataPenempatan[2]['Harian'] ?? 0) }};
        var outsourcing_marketing = {{ json_encode($dataPenempatan[2]['Outsourcing'] ?? 0) }};
        var pkwtt_accounting = {{ json_encode($dataPenempatan[1]['PKWTT'] ?? 0) }};
        var pkwt_accounting = {{ json_encode($dataPenempatan[1]['PKWT'] ?? 0) }};
        var harian_accounting = {{ json_encode($dataPenempatan[1]['Harian'] ?? 0) }};
        var outsourcing_accounting = {{ json_encode($dataPenempatan[1]['Outsourcing'] ?? 0) }};
        var pkwtt_ic = {{ json_encode($dataPenempatan[6]['PKWTT'] ?? 0) }};
        var pkwt_ic = {{ json_encode($dataPenempatan[6]['PKWT'] ?? 0) }};
        var harian_ic = {{ json_encode($dataPenempatan[6]['Harian'] ?? 0) }};
        var outsourcing_ic = {{ json_encode($dataPenempatan[6]['Outsourcing'] ?? 0) }};
        var pkwtt_it = {{ json_encode($dataPenempatan[7]['PKWTT'] ?? 0) }};
        var pkwt_it = {{ json_encode($dataPenempatan[7]['PKWT'] ?? 0) }};
        var harian_it = {{ json_encode($dataPenempatan[7]['Harian'] ?? 0) }};
        var outsourcing_it = {{ json_encode($dataPenempatan[7]['Outsourcing'] ?? 0) }};
        var pkwtt_engineering = {{ json_encode($dataPenempatan[4]['PKWTT'] ?? 0) }};
        var pkwt_engineering = {{ json_encode($dataPenempatan[4]['PKWT'] ?? 0) }};
        var harian_engineering = {{ json_encode($dataPenempatan[4]['Harian'] ?? 0) }};
        var outsourcing_engineering = {{ json_encode($dataPenempatan[4]['Outsourcing'] ?? 0) }};
        var pkwtt_purchasing = {{ json_encode($dataPenempatan[3]['PKWTT'] ?? 0) }};
        var pkwt_purchasing = {{ json_encode($dataPenempatan[3]['PKWT'] ?? 0) }};
        var harian_purchasing = {{ json_encode($dataPenempatan[3]['Harian'] ?? 0) }};
        var outsourcing_purchasing = {{ json_encode($dataPenempatan[3]['Outsourcing'] ?? 0) }};
        var pkwtt_quality = {{ json_encode($dataPenempatan[10]['PKWTT'] ?? 0) }};
        var pkwt_quality = {{ json_encode($dataPenempatan[10]['PKWT'] ?? 0) }};
        var harian_quality = {{ json_encode($dataPenempatan[10]['Harian'] ?? 0) }};
        var outsourcing_quality = {{ json_encode($dataPenempatan[10]['Outsourcing'] ?? 0) }};
        var pkwtt_dc = {{ json_encode($dataPenempatan[9]['PKWTT'] ?? 0) }};
        var pkwt_dc = {{ json_encode($dataPenempatan[9]['PKWT'] ?? 0) }};
        var harian_dc = {{ json_encode($dataPenempatan[9]['Harian'] ?? 0) }};
        var outsourcing_dc = {{ json_encode($dataPenempatan[9]['Outsourcing'] ?? 0) }};
        var pkwtt_hrd = {{ json_encode($dataPenempatan[8]['PKWTT'] ?? 0) }};
        var pkwt_hrd = {{ json_encode($dataPenempatan[8]['PKWT'] ?? 0) }};
        var harian_hrd = {{ json_encode($dataPenempatan[8]['Harian'] ?? 0) }};
        var outsourcing_hrd = {{ json_encode($dataPenempatan[8]['Outsourcing'] ?? 0) }};
        var pkwtt_security = {{ json_encode($dataPenempatan[17]['PKWTT'] ?? 0) }};
        var pkwt_security = {{ json_encode($dataPenempatan[17]['PKWT'] ?? 0) }};
        var harian_security = {{ json_encode($dataPenempatan[17]['Harian'] ?? 0) }};
        var outsourcing_security = {{ json_encode($dataPenempatan[17]['Outsourcing'] ?? 0) }};


        Highcharts.chart('containerpenempatandetail', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Detail Penempatan Karyawan'
            },
            xAxis: {
                categories: ['Blok BL', 'Accounting', 'IC', 'IT', 'HRD', 'Security', 'Doc Control', 'Marketing',
                    'Engineering',
                    'Quality',
                    'Purchasing', 'PPC', 'Produksi', 'Delivery Produksi', 'Gudang RM', 'Gudang FG', 'Delivery',
                    'Blok E', 'Daihatsu Sunter',
                    'Daihatsu Cibitung', 'Daihatsu Karawang Timur'
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Jumlah Karyawan'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'percent'
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                    name: 'Tetap',
                    data: [pkwtt_bl, pkwtt_accounting, pkwtt_ic, pkwtt_it, pkwtt_hrd, pkwtt_security, pkwtt_dc,
                        pkwtt_marketing,
                        pkwtt_engineering, pkwtt_quality, pkwtt_purchasing, pkwtt_ppc, pkwtt_produksi,
                        pkwtt_del_prod, pkwtt_rm, pkwtt_fg, pkwtt_del,
                        pkwtt_blok_e,
                        pkwtt_sunter, pkwtt_cibitung, pkwtt_karawang
                    ]
                }, {
                    name: 'Kontrak',
                    data: [pkwt_bl, pkwt_accounting, pkwt_ic, pkwt_it, pkwt_hrd, pkwt_security, pkwt_dc,
                        pkwt_marketing,
                        pkwt_engineering, pkwt_quality, pkwt_purchasing, pkwt_ppc, pkwt_produksi,
                        pkwt_del_prod, pkwt_rm, pkwt_fg, pkwt_del,
                        pkwt_blok_e,
                        pkwt_sunter, pkwt_cibitung, pkwt_karawang
                    ]
                },
                {
                    name: 'Harian',
                    data: [harian_bl, harian_accounting, harian_ic, harian_it, harian_hrd, harian_security,
                        harian_dc,
                        harian_marketing,
                        harian_engineering, harian_quality, harian_purchasing, harian_ppc, harian_produksi,
                        harian_del_prod, harian_rm, harian_fg, harian_del,
                        harian_blok_e,
                        harian_sunter, harian_cibitung, harian_karawang
                    ]
                }, {
                    name: 'Outsourcing',
                    data: [outsourcing_bl, outsourcing_accounting, outsourcing_ic, outsourcing_it,
                        outsourcing_hrd, outsourcing_security, outsourcing_dc,
                        outsourcing_marketing,
                        outsourcing_engineering, outsourcing_quality, outsourcing_purchasing,
                        outsourcing_ppc, outsourcing_produksi,
                        outsourcing_del_prod, outsourcing_rm, outsourcing_fg, outsourcing_del,
                        outsourcing_blok_e,
                        outsourcing_sunter, outsourcing_cibitung, outsourcing_karawang
                    ]
                }
            ]
        });
    </script>
    {{-- Chart Penempatan Detail --}}

    {{-- Chart Kontrak --}}
    <script>
        var kontrak = {{ json_encode($item_kontrak) }};
        var tetap = {{ json_encode($item_tetap) }};
        var harian = {{ json_encode($item_harian) }};
        var outsourcing = {{ json_encode($item_outsourcing) }};
        Highcharts.chart('containerkontrak', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: ''
            },
            tooltip: {
                valueSuffix: 'MP'
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
                        distance: 20
                    },
                    showInLegend: true,
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Tetap',
                    y: tetap,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Kontrak',
                    y: kontrak
                }, {
                    name: 'Harian',
                    y: harian
                }, {
                    name: 'Outsourcing',
                    y: outsourcing
                }]
            }]
        });
    </script>
    {{-- Chart KOntrak --}}

    {{-- Chart Status Menikah --}}
    <script>
        var single = {{ json_encode($item_single) }};
        var menikah = {{ json_encode($item_menikah) }};
        var janda = {{ json_encode($item_janda) }};
        var duda = {{ json_encode($item_duda) }};
        Highcharts.chart('containerstatusnikah', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: ''
            },
            tooltip: {
                valueSuffix: 'MP'
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
                        distance: 20
                    },
                    showInLegend: true,
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Single',
                    y: single,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Menikah',
                    y: menikah
                }, {
                    name: 'Janda',
                    y: janda
                }, {
                    name: 'Duda',
                    y: duda
                }]
            }]
        });
    </script>
    {{-- Chart Status Menikah --}}

    {{-- Chart Jenis Kelamin --}}
    <script>
        var pria = {{ json_encode($item_pria) }};
        var wanita = {{ json_encode($item_wanita) }};
        Highcharts.chart('containerjeniskelamin', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: ''
            },
            tooltip: {
                valueSuffix: 'MP'
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
                        distance: 20
                    },
                    showInLegend: true,
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Pria',
                    y: pria,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Wanita',
                    y: wanita
                }]
            }]
        });
    </script>
    {{-- Chart Jenis Kelamin --}}

    {{-- Chart Agama --}}
    <script>
        var islam = {{ json_encode($item_islam) }};
        var kristenprotestan = {{ json_encode($item_kristenprotestan) }};
        var kristenkatholik = {{ json_encode($item_kristenkatholik) }};
        var hindu = {{ json_encode($item_hindu) }};
        var budha = {{ json_encode($item_budha) }};
        Highcharts.chart('containeragama', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: ''
            },
            tooltip: {
                valueSuffix: 'MP'
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
                        distance: 20
                    },
                    showInLegend: true,
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Islam',
                    y: islam,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Kristen Protestan',
                    y: kristenprotestan
                }, {
                    name: 'Kristen Katholik',
                    y: kristenkatholik
                }, {
                    name: 'Hindu',
                    y: hindu
                }, {
                    name: 'Budha',
                    y: budha
                }]
            }]
        });
    </script>
    {{-- Chart Agama --}}
@endsection

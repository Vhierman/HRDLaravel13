@extends('admin.layouts.base')
@section('title', 'Rekap Absensi');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Laporan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Rekap Absensi</li>
                </ol>
            </nav>
        </div>
    </div>

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
@endsection

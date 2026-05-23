@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        .search-row input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
@endsection
@extends('admin.layouts.base')
@section('title', 'Data Inventaris Mobil');
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Inventaris</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Inventaris Mobil</li>
                </ol>
            </nav>
        </div>
    </div>
    @php
        $userRole = Auth::user()->roles;
        $canManage = in_array($userRole, ['admin', 'hrd']);
        $canView = in_array($userRole, ['admin', 'hrd', 'accounting']);
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <div class="btn-group position-static">
                        @if ($canManage)
                            <a href="{{ route('inventory_car.create') }}" class="btn-group position-static">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Tambah Inventaris Mobil
                                </button>
                            </a>
                        @endif
                        <a href="{{ route('inventory_car.exportExcel') }}" target="_blank"
                            class="btn-group position-static">
                            <button type="button" class="btn btn-success">
                                <i class="bi bi-cloud-arrow-down"></i> Download Data Inventaris Mobil
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Penempatan</th>
                            <th>mobil</th>
                            <th>Nomor Polisi</th>
                            <th>Akhir Pajak</th>
                            <th>Akhir Plat</th>
                            @if ($canManage)
                                <th>Action</th>
                            @endif
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Nama Karyawan..." /></th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Penempatan..." /></th>
                            <th><input type="text" class="form-control form-control-sm"placeholder="Cari mobil..." />
                            </th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Nomor Polisi..." /></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Akhir Pajak..." /></th>
                            <th><input type="text"
                                    class="form-control form-control-sm"placeholder="Cari Akhir Plat..." /></th>
                            @if ($canManage)
                                <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($inventory_cars as $inventory_car)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $inventory_car->employees->nama_karyawan }}</td>
                                <td>{{ $inventory_car->employees->positions->jabatan }}</td>
                                <td>{{ $inventory_car->employees->divisions->penempatan }}</td>
                                <td>{{ $inventory_car->merk_mobil }}/{{ $inventory_car->type_mobil }}</td>
                                <td>{{ $inventory_car->nomor_polisi }}</td>
                                <td>{{ \Carbon\Carbon::parse($inventory_car->tanggal_akhir_pajak_mobil)->isoformat('DD-MM-Y') }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($inventory_car->tanggal_akhir_plat_mobil)->isoformat('DD-MM-Y') }}
                                </td>
                                @if ($canManage)
                                    <td>
                                        <div class="row row-cols-auto g-3">
                                            <div class="col">
                                                <a href="{{ route('inventory_car.edit', $inventory_car->id) }}"
                                                    class="btn btn-success raised d-flex gap-2">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <form action="{{ route('inventory_car.destroy', $inventory_car->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger raised d-flex gap-2">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

{{-- Datatables --}}
@section('js')
    {{-- <script src="{{ asset('template_admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script> --}}
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // 1. Inisialisasi DataTable
            var table = $('#example2').DataTable({
                lengthChange: false,
                orderCellsTop: true, // Penting: Agar sorting tetap di baris header pertama
                fixedHeader: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            // 2. Tambahkan Button Container
            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');

            // 3. Logika Pencarian Per Kolom
            // Kita ambil setiap input dari baris '.search-row'
            $('#example2 .search-row input').on('keyup change', function() {
                // Ambil index kolom dari elemen parent <th>
                var columnIndex = $(this).parent().index();

                if (table.column(columnIndex).search() !== this.value) {
                    table
                        .column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });
        });
    </script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
{{-- Datatables --}}

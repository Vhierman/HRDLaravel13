@section('css')
    <link href="{{ asset('template_admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        /* Membuat input pencarian kolom lebih tipis dan rapi */
        .search-row th {
            padding: 8px 4px !important;
        }

        .search-row input {
            font-size: 13px;
            padding: 4px 8px;
        }

        /* Menjaga tombol aksi tetap rapi dalam satu baris */
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
@endsection

@extends('admin.layouts.base')
@section('title', 'Data Karyawan')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Karyawan Aktif</li>
                </ol>
            </nav>
        </div>
    </div>
    @php
        $userRole = Auth::user()->roles;
        $canManage = in_array($userRole, ['admin', 'hrd']);
        $canDelete = $userRole === 'admin';
        $canExport = in_array($userRole, ['admin', 'hrd', 'accounting']);
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if ($canManage)
                    <a href="{{ route('employee.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="bi bi-person-plus"></i> Tambah Karyawan
                    </a>
                @endif
                @if ($canExport)
                    <a href="{{ route('exportExcel') }}" target="_blank"
                        class="btn btn-success d-flex align-items-center gap-1">
                        <i class="bi bi-cloud-arrow-down"></i> Download Database Karyawan
                    </a>
                @endif
                @if ($canManage)
                    <a href="{{ route('kontrak_kerja.notif_kontrak_habis') }}"
                        class="btn btn-danger d-flex align-items-center gap-1"><i
                            class="material-icons-outlined">settings_accessibility</i>Berakhirnya Kontrak Kerja Harian<span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">{{ $expired_akhir_kerja }}<span
                                class="visually-hidden">unread messages</span></span>
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Jabatan</th>
                            <th>Penempatan</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>Awal Kerja</th>
                            <th>Akhir Kerja</th>
                            <th class="text-center">Action</th>
                        </tr>
                        <tr class="search-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Nama..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari NIK..." /></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Jabatan..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Penempatan..." /></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Area..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Cari Status..." />
                            </th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Awal Kerja..." /></th>
                            <th><input type="text" class="form-control form-control-sm"
                                    placeholder="Cari Akhir Kerja..." /></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($employees as $employee)
                            @php
                                $tanggal_akhir_kerja = $employee->tanggal_akhir_kerja
                                    ? \Carbon\Carbon::parse($employee->tanggal_akhir_kerja)->isoformat('DD-MM-Y')
                                    : '-';

                                switch ($employee->status_kerja) {
                                    case 'PKWTT':
                                        $status_kerja = 'Tetap';
                                        $tanggal_akhir_kerja = 'PKWTT';
                                        $badge_color = 'success';
                                        break;
                                    case 'Outsourcing':
                                        $status_kerja = 'Outsourcing';
                                        $badge_color = 'danger';
                                        break;
                                    case 'Harian':
                                        $status_kerja = 'Harian';
                                        $badge_color = 'info text-dark';
                                        break;
                                    default:
                                        $status_kerja = 'Kontrak';
                                        $badge_color = 'primary';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $employee->nama_karyawan }}</td>
                                <td>{{ $employee->nik_karyawan }}</td>
                                <td>{{ $employee->positions->jabatan ?? '-' }}</td>
                                <td>{{ $employee->divisions->penempatan ?? '-' }}</td>
                                <td>{{ $employee->areas->area ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badge_color }}">
                                        {{ $status_kerja }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoformat('DD-MM-Y') }}</td>
                                <td>{{ $tanggal_akhir_kerja }}</td>
                                <td>
                                    <div class="action-container">
                                        {{-- Tombol Edit untuk Admin dan HRD disatukan --}}
                                        @if ($canManage)
                                            <button type="button"
                                                onclick="window.location.href='{{ route('employee.edit', $employee->id) }}'"
                                                class="btn btn-sm btn-outline-success btn-action" title="Edit Data">
                                                <i class="material-icons-outlined">edit</i>
                                            </button>
                                        @endif

                                        {{-- Tombol Hapus khusus Admin --}}
                                        @if ($canDelete)
                                            <form action="{{ route('employee.destroy', $employee->id) }}" method="POST"
                                                class="d-inline delete-form" style="display: inline; margin: 0;">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                    title="Hapus Data">
                                                    <i class="material-icons-outlined">delete</i>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                            onclick="window.location.href='{{ route('employee.show', $employee->id) }}'"
                                            class="btn btn-sm btn-outline-primary btn-action" title="Edit Data">
                                            <i class="material-icons-outlined">visibility</i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    // Menggunakan objek bahasa lokal agar anti-blokir dan lebih cepat load-nya
                    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sSearch": "Cari:",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya",
                        "sLast": "Terakhir"
                    }
                }
            });

            // Logika Pencarian Per Kolom dengan optimasi debounce
            var delayTimer;
            $('#example2 .search-row input').on('keyup change', function() {
                var columnIndex = $(this).parent().index();
                var value = this.value;
                clearTimeout(delayTimer);
                delayTimer = setTimeout(function() {
                    if (table.column(columnIndex).search() !== value) {
                        table.column(columnIndex).search(value).draw();
                    }
                }, 300);
            });

            // SweetAlert2 Delegasi Event (Supaya tetap bekerja saat halaman DataTables berpindah)
            $('#example2').on('click', '.btn-delete', function(e) {
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data karyawan ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

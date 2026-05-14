@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Hapus Data Overtime');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Overtime</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Hapus Overtime</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        {{-- Pesan Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Pesan Error --}}

        <div class="card-body p-4">
            <h5 class="mb-4">Form Hapus Overtime {{ $item_overtime->employees->nama_karyawan }}</h5>
            <form action="{{ route('overtime.destroy', $item_overtime->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('delete')
                <input type="hidden" name="employees_id" value="{{ $item_overtime->employees_id }}" class="form-control"
                    placeholder="Employees ID" readonly>
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Lembur</label>
                        <input type="date" name="tanggal_lembur" value="{{ $item_overtime->tanggal_lembur }}"
                            class="form-control" placeholder="dd/mm/yyyy" readonly>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Keterangan Lembur</label>
                        <input type="text" name="keterangan_lembur" value="{{ $item_overtime->keterangan_lembur }}"
                            class="form-control" placeholder="Masukan Keterangan Lembur" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Masuk</label>
                        <input type="text" name="jam_masuk" value="{{ $item_overtime->jam_masuk }}" class="form-control"
                            placeholder="Masukan Jam Masuk" readonly>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Istirahat</label>
                        <input type="text" name="jam_istirahat" value="{{ $item_overtime->jam_istirahat }}"
                            class="form-control" placeholder="Masukan Jam Istirahat" readonly>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label fs-6">Jam Pulang</label>
                        <input type="text" name="jam_pulang" value="{{ $item_overtime->jam_pulang }}"
                            class="form-control" placeholder="Masukan Jam Pulang" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jenis Lembur</label>
                        <input type="text" name="jenis_lembur" value="{{ $item_overtime->jenis_lembur }}"
                            class="form-control" placeholder="Masukan Jenis Lembur" readonly>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Uang Makan Lembur</label>
                        <input type="text" name="uang_makan_lembur" value="{{ $item_overtime->uang_makan_lembur }}"
                            class="form-control" placeholder="Masukan Uang Makan Lembur" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-danger px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Hapus</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('overtime.index') }}"
                                        class="btn btn-primary px-4 raised d-flex gap-2">
                                        <i class="material-icons-outlined">cancel</i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('template_admin/assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        $('#karyawan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

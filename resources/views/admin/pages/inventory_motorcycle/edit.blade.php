@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Data Inventaris Motor');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Inventaris</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Inventaris Motor</li>
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
            <h5 class="mb-4">Form Edit Inventaris Motor</h5>
            <form action="{{ route('inventory_motorcycle.update', $inventory_motorcycle->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Nama Karyawan</label>
                        <select name="employees_id" class="form-select" id="karyawan-select"
                            data-placeholder="Pilih Karyawan">
                            <option value="{{ $inventory_motorcycle->employees_id }}">Pilih Area</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    @if ($inventory_motorcycle->employees_id == $employee->id) {{ 'selected="selected"' }} @endif>
                                    {{ $employee->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Nomor Polisi</label>
                        <input type="text" name="nomor_polisi" value="{{ $inventory_motorcycle->nomor_polisi }}"
                            class="form-control" placeholder="Masukan Nomor Polisi">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Merk Motor</label>
                        <input type="text" name="merk_motor" value="{{ $inventory_motorcycle->merk_motor }}"
                            class="form-control" placeholder="Masukan Merk Motor">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Type Motor</label>
                        <input type="text" name="type_motor" value="{{ $inventory_motorcycle->type_motor }}"
                            class="form-control" placeholder="Masukan Type Motor">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Warna Motor</label>
                        <input type="text" name="warna_motor" value="{{ $inventory_motorcycle->warna_motor }}"
                            class="form-control" placeholder="Masukan Warna Motor">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Nomor Rangka</label>
                        <input type="text" name="nomor_rangka_motor"
                            value="{{ $inventory_motorcycle->nomor_rangka_motor }}" class="form-control"
                            placeholder="Masukan Nomor Rangka">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Nomor Mesin</label>
                        <input type="text" name="nomor_mesin_motor"
                            value="{{ $inventory_motorcycle->nomor_mesin_motor }}" class="form-control"
                            placeholder="Masukan Nomor Mesin">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Akhir Pajak</label>
                        <input type="date" name="tanggal_akhir_pajak_motor"
                            value="{{ $inventory_motorcycle->tanggal_akhir_pajak_motor }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Akhir Plat</label>
                        <input type="date" name="tanggal_akhir_plat_motor"
                            value="{{ $inventory_motorcycle->tanggal_akhir_plat_motor }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                </div>

                <br>
                <div class="row g-3">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('inventory_motorcycle.index') }}"
                                        class="btn btn-danger px-4 raised d-flex gap-2">
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

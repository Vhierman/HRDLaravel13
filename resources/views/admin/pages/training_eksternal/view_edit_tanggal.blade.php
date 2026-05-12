@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Data Training Eksternal');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Training</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Data Training Eksternal</li>
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
            <h5 class="mb-4">Form Edit Training Eksternal</h5>
            <form action="{{ route('training_eksternal.update_tanggal') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tanggal_awal_lama_training_eksternal"
                    value="{{ $item_training_eksternals->first()->tanggal_awal_training_eksternal }}" class="form-control"
                    placeholder="dd/mm/yyyy">

                <div class="row justify-content-center g-3 mb-2">
                    <div class="col-md-12">
                        <label for="karyawan-select" class="form-label">Nama Karyawan</label>
                        <select name="employees_id[]" class="form-select" id="multiple-select-custom-field"
                            data-placeholder="Pilih Nama Karyawan" multiple>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employees_id', $selectedEmployees)) ? 'selected' : '' }}>
                                    {{ $employee->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Tanggal Awal Training</label>
                        <input type="date" name="tanggal_awal_training_eksternal"
                            value="{{ old('tanggal_awal_training_eksternal', $item_training_eksternals->first()->tanggal_awal_training_eksternal) }}"
                            class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Tanggal Akhir Training</label>
                        <input type="date" name="tanggal_akhir_training_eksternal"
                            value="{{ old('tanggal_akhir_training_eksternal', $item_training_eksternals->first()->tanggal_akhir_training_eksternal) }}"
                            class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Institusi Penyelenggara Training</label>
                        <input type="text" name="institusi_penyelenggara_training_eksternal"
                            value="{{ old('institusi_penyelenggara_training_eksternal', $item_training_eksternals->first()->institusi_penyelenggara_training_eksternal) }}"
                            class="form-control" placeholder="Institusi Penyelenggara Training">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Training</label>
                        <input type="text" name="perihal_training_eksternal"
                            value="{{ old('perihal_training_eksternal', $item_training_eksternals->first()->perihal_training_eksternal) }}"
                            class="form-control" placeholder="Training">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Jam Training</label>
                        <input type="time" name="jam_training_eksternal"
                            value="{{ old('jam_training_eksternal', $item_training_eksternals->first()->jam_training_eksternal) }}"
                            class="form-control" placeholder="00:00">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi_training_eksternal"
                            value="{{ old('lokasi_training_eksternal', $item_training_eksternals->first()->lokasi_training_eksternal) }}"
                            class="form-control" placeholder="Masukan Lokasi Training">
                    </div>
                </div>
                <div class="row justify-content-center g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat_training_eksternal"
                            value="{{ old('alamat_training_eksternal', $item_training_eksternals->first()->alamat_training_eksternal) }}"
                            class="form-control" placeholder="Masukan Alamat Lengkap Lokasi Training">
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
                                    <a href="{{ route('training_eksternal.index') }}"
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

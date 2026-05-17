@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Rekon Gaji');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Gaji</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Gaji Karyawan</li>
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
            <h5 class="mb-4">Form Edit Gaji Karyawan</h5>
            <form action="{{ route('salary.update', $item_salary->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">NIK Karyawan</label>
                        <input type="text" name="nik_karyawan" value="{{ $item_salary->employees->nik_karyawan }}"
                            class="form-control" placeholder="Masukan NIK Karyawan" readonly>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" value="{{ $item_salary->employees->nama_karyawan }}"
                            class="form-control" placeholder="Masukan Nama Karyawan" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ $item_salary->employees->positions->jabatan }}"
                            class="form-control" placeholder="Masukan Jabatan" readonly>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Penempatan</label>
                        <input type="text" name="penempatan" value="{{ $item_salary->employees->divisions->penempatan }}"
                            class="form-control" placeholder="Masukan Penempatan" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Gaji Pokok</label>
                        <input type="text" name="gaji_pokok" value="{{ $item_salary->gaji_pokok }}" class="form-control"
                            placeholder="Masukan Gaji Pokok" onkeyup="angka(this);">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Uang Makan</label>
                        <input type="text" name="uang_makan" value="{{ $item_salary->uang_makan }}" class="form-control"
                            placeholder="Masukan Uang Makan" onkeyup="angka(this);">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Uang Transport</label>
                        <input type="text" name="uang_transport" value="{{ $item_salary->uang_transport }}"
                            class="form-control" placeholder="Masukan Uang Transport" onkeyup="angka(this);">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tunjangan Tugas</label>
                        <input type="text" name="tunjangan_tugas" value="{{ $item_salary->tunjangan_tugas }}"
                            class="form-control" placeholder="Masukan Tunjangan Tugas" onkeyup="angka(this);">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tunjangan Pulsa</label>
                        <input type="text" name="tunjangan_pulsa" value="{{ $item_salary->tunjangan_pulsa }}"
                            class="form-control" placeholder="Masukan Tunjangan Pulsa" onkeyup="angka(this);">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tunjangan Jabatan</label>
                        <input type="text" name="tunjangan_jabatan" value="{{ $item_salary->tunjangan_jabatan }}"
                            class="form-control" placeholder="Masukan Tunjangan Jabatan" onkeyup="angka(this);">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <label class="form-label fs-6">Kepesertaan BPJS Kesehatan Dan Ketenagakerjaan</label>
                    <br>
                    <div class="d-flex align-items-center gap-3">

                        <div class="form-check form-check-success">
                            <input class="form-check-input" name="jkn" type="hidden" id="flexCheckSuccess"
                                value="0">
                            <input class="form-check-input" type="checkbox" name="jkn" value="1"
                                @if ($item_salary->potongan_bpjsks_perusahaan != 0) checked @endif id="flexCheckSuccess">
                            <label class="form-check-label" for="flexCheckSuccess">
                                BPJS Kesehatan
                            </label>
                        </div>
                        <div class="form-check form-check-danger">
                            <input class="form-check-input" name="jht" type="hidden" id="flexCheckDanger"
                                value="0">
                            <input class="form-check-input" type="checkbox" name="jht" value="1"
                                @if ($item_salary->potongan_jht_perusahaan != 0) checked @endif id="flexCheckDanger">
                            <label class="form-check-label" for="flexCheckDanger">
                                Jaminan Hari Tua
                            </label>
                        </div>
                        <div class="form-check form-check-warning">
                            <input class="form-check-input" name="jp" type="hidden" id="flexCheckWarning"
                                value="0">
                            <input class="form-check-input" type="checkbox" name="jp" value="1"
                                @if ($item_salary->potongan_jp_perusahaan != 0) checked @endif id="flexCheckWarning">
                            <label class="form-check-label" for="flexCheckWarning">
                                Jaminan Pensiun
                            </label>
                        </div>
                        <div class="form-check form-check-info">
                            <input class="form-check-input" name="jkm" type="hidden" id="flexCheckInfo"
                                value="0">
                            <input class="form-check-input" type="checkbox" name="jkm" value="1"
                                @if ($item_salary->potongan_jkm_perusahaan != 0) checked @endif id="flexCheckInfo">
                            <label class="form-check-label" for="flexCheckInfo">
                                Jaminan Kematian
                            </label>
                        </div>
                        <div class="form-check form-check-primary">
                            <input class="form-check-input" name="jkk" type="hidden" id="flexCheckPrimary"
                                value="0">
                            <input class="form-check-input" type="checkbox" name="jkk" value="1"
                                @if ($item_salary->potongan_jkk_perusahaan != 0) checked @endif id="flexCheckPrimary">
                            <label class="form-check-label" for="flexCheckPrimary">
                                Jaminan Kecelakaan Kerja
                            </label>
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
                                        <a href="{{ route('salary.index') }}"
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

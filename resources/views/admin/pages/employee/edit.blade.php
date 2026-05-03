@section('css')
    <link href="{{ asset('template_admin/assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Karyawan');

@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Data Karyawan</li>
                </ol>
            </nav>
        </div>
    </div>

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

    <div id="stepper1" class="bs-stepper">
        <div class="card">

            <div class="card-header">
                <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
                    <div class="step" data-target="#test-l-1">
                        <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                            <div class="bs-stepper-circle">1</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Divisi</h5>
                                <p class="mb-0 steper-sub-title">Deskripsi Pekerjaan</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-2">
                        <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <div class="bs-stepper-circle">2</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Karyawan</h5>
                                <p class="mb-0 steper-sub-title">Biodata Karyawan</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-3">
                        <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <div class="bs-stepper-circle">3</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Alamat</h5>
                                <p class="mb-0 steper-sub-title">Alamat Lengkap Karyawan</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-4">
                        <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
                            <div class="bs-stepper-circle">4</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">BPJS</h5>
                                <p class="mb-0 steper-sub-title">BPJS Kesehatan Dan Ketenagakerjaan</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-5">
                        <div class="step-trigger" role="tab" id="stepper1trigger5" aria-controls="test-l-5">
                            <div class="bs-stepper-circle">5</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Foto</h5>
                                <p class="mb-0 steper-sub-title">Foto Karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="bs-stepper-content">
                    <form action="{{ route('employee.update', $employee->id) }}" method="post"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        {{-- Tab Divisi --}}
                        <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                            <h5 class="mb-1">Divisi</h5>
                            <p class="mb-4">Deskripsi Pekerjaan</p>

                            <div class="row g-3">

                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Perusahaan</label>
                                    <select name="companies_id" class="form-select">
                                        <option value="{{ $employee->companies_id }}">Pilih Perusahaan</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                @if ($employee->companies_id == $company->id) {{ 'selected="selected"' }} @endif>
                                                {{ $company->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Area</label>
                                    <select name="areas_id" class="form-select">
                                        <option value="{{ $employee->areas_id }}">Pilih Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}"
                                                @if ($employee->areas_id == $area->id) {{ 'selected="selected"' }} @endif>
                                                {{ $area->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Golongan</label>
                                    <select name="golongans_id" class="form-select">
                                        <option value="{{ $employee->golongans_id }}">Pilih Golongan</option>
                                        @foreach ($golongans as $golongan)
                                            <option value="{{ $golongan->id }}"
                                                @if ($employee->golongans_id == $golongan->id) {{ 'selected="selected"' }} @endif>
                                                {{ $golongan->golongan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Penempatan</label>
                                    <select name="divisions_id" class="form-select">
                                        <option value="{{ $employee->divisions_id }}">Pilih Penempatan</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}"
                                                @if ($employee->divisions_id == $division->id) {{ 'selected="selected"' }} @endif>
                                                {{ $division->penempatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Jabatan</label>
                                    <select name="positions_id" class="form-select">
                                        <option value="{{ $employee->positions_id }}">Pilih Jabatan</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                @if ($employee->positions_id == $position->id) {{ 'selected="selected"' }} @endif>
                                                {{ $position->jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Jam kerja</label>
                                    <select name="working_hours_id" class="form-select">
                                        <option value="{{ $employee->working_hours_id }}">Pilih Jam Kerja</option>
                                        @foreach ($working_hours as $working_hour)
                                            <option value="{{ $working_hour->id }}"
                                                @if ($employee->working_hours_id == $working_hour->id) {{ 'selected="selected"' }} @endif>
                                                {{ $working_hour->jam_masuk . ' s/d ' . $working_hour->jam_pulang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Status Kerja</label>
                                    <select id="input39" class="form-select" name="status_kerja">
                                        <option value="">Pilih Status Kerja</option>
                                        <option value="PKWTT"
                                            @if ($employee->status_kerja == 'PKWTT') {{ 'selected="selected"' }} @endif>
                                            PKWTT</option>
                                        <option value="PKWT"
                                            @if ($employee->status_kerja == 'PKWT') {{ 'selected="selected"' }} @endif>
                                            PKWT</option>
                                        <option value="Harian"
                                            @if ($employee->status_kerja == 'Harian') {{ 'selected="selected"' }} @endif>
                                            Harian</option>
                                        <option value="Outsourcing"
                                            @if ($employee->status_kerja == 'Outsourcing') {{ 'selected="selected"' }} @endif>
                                            Outsourcing
                                        </option>
                                    </select>

                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Tanggal Mulai Kerja</label>
                                    <input type="date" name="tanggal_mulai_kerja"
                                        value="{{ $employee->tanggal_mulai_kerja }}" class="form-control"
                                        placeholder="dd/mm/yyyy">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Tanggal Akhir Kerja</label>
                                    <input type="date" name="tanggal_akhir_kerja"
                                        value="{{ $employee->tanggal_akhir_kerja }}" class="form-control"
                                        placeholder="dd/mm/yyyy">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" name="nomor_rekening" onkeyup="angka(this);"
                                        value="{{ $employee->nomor_rekening }}" class="form-control"
                                        placeholder="Masukan Nomor Rekening">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nama Bank</label>
                                    <select id="input39" class="form-select" name="nama_bank">
                                        <option value="">Pilih Bank</option>
                                        <option value="BCA"
                                            @if ($employee->nama_bank == 'BCA') {{ 'selected="selected"' }} @endif>
                                            BCA</option>
                                        <option value="Mandiri"
                                            @if ($employee->nama_bank == 'Mandiri') {{ 'selected="selected"' }} @endif>
                                            Mandiri</option>
                                        <option value="Permata"
                                            @if ($employee->nama_bank == 'Permata') {{ 'selected="selected"' }} @endif>
                                            Permata</option>
                                        <option value="BRI"
                                            @if ($employee->nama_bank == 'BRI') {{ 'selected="selected"' }} @endif>
                                            BRI
                                        </option>
                                        <option value="BNI"
                                            @if ($employee->nama_bank == 'BNI') {{ 'selected="selected"' }} @endif>
                                            BNI
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="col-12 col-lg-6">
                                <button type="button" class="btn btn-info" onclick="stepper1.next()">Next</button>
                            </div>
                        </div>
                        {{-- End Tab Divisi --}}

                        {{-- Tab Karyawan --}}
                        <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">

                            <h5 class="mb-1">Karyawan</h5>
                            <p class="mb-4">Biodata Karyawan</p>

                            <div class="row g-3">

                                <div class="col-12 col-lg-6">
                                    <label class="form-label">NIK KTP</label>
                                    <input type="text" onkeyup="angka(this);" maxlength="16" name="nik_karyawan"
                                        value="{{ $employee->nik_karyawan }}" class="form-control"
                                        placeholder="Masukan NIK Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" onkeyup="huruf(this);" name="nama_karyawan"
                                        value="{{ $employee->nama_karyawan }}" class="form-control"
                                        placeholder="Masukan Nama Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Email</label>
                                    <input type="text" name="email_karyawan" value="{{ $employee->email_karyawan }}"
                                        class="form-control" placeholder="Masukan Email Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor Absen</label>
                                    <input type="text" onkeyup="angka(this);" name="nomor_absen"
                                        value="{{ $employee->nomor_absen }}" maxlength="4" class="form-control"
                                        placeholder="Masukan Nomor Absen Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor NPWP</label>
                                    <input type="text" onkeyup="angka(this);" maxlength="16" name="nomor_npwp"
                                        value="{{ $employee->nomor_npwp }}" class="form-control"
                                        placeholder="Masukan Nomor NPWP Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor Handphone</label>
                                    <input type="text" onkeyup="angka(this);" maxlength="16" name="nomor_handphone"
                                        value="{{ $employee->nomor_handphone }}" class="form-control"
                                        placeholder="Masukan Nomor Handphone Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Tempat, Tanggal Lahir</label>
                                    <div class="input-group">
                                        <input type="text" value="{{ $employee->tempat_lahir }}"
                                            placeholder="Masukan Tempat Lahir" name="tempat_lahir" class="form-control">
                                        <input type="date" placeholder="dd/mm/yyyy"
                                            value="{{ $employee->tanggal_lahir }}" name="tanggal_lahir"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Agama</label>
                                    <select id="input39" class="form-select" name="agama">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam"
                                            @if ($employee->agama == 'Islam') {{ 'selected="selected"' }} @endif>
                                            Islam</option>
                                        <option value="Kristen Protestan"
                                            @if ($employee->agama == 'Kristen Protestan') {{ 'selected="selected"' }} @endif>
                                            Kristen Protestan</option>
                                        <option value="Kristen Katholik"
                                            @if ($employee->agama == 'Kristen Katholik') {{ 'selected="selected"' }} @endif>
                                            Kristen Katholik</option>
                                        <option value="Hindu"
                                            @if ($employee->agama == 'Hindu') {{ 'selected="selected"' }} @endif>
                                            Hindu
                                        </option>
                                        <option value="Budha"
                                            @if ($employee->agama == 'Budha') {{ 'selected="selected"' }} @endif>
                                            Budha
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select id="input39" class="form-select" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Pria"
                                            @if ($employee->jenis_kelamin == 'Pria') {{ 'selected="selected"' }} @endif>
                                            Pria</option>
                                        <option value="Wanita"
                                            @if ($employee->jenis_kelamin == 'Wanita') {{ 'selected="selected"' }} @endif>
                                            Wanita</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <select id="input39" class="form-select" name="pendidikan_terakhir">
                                        <option value="">Pilih Pendidikan Terakhir</option>
                                        <option value="SD"
                                            @if ($employee->pendidikan_terakhir == 'SD') {{ 'selected="selected"' }} @endif>
                                            SD</option>
                                        <option value="SMP"
                                            @if ($employee->pendidikan_terakhir == 'SMP') {{ 'selected="selected"' }} @endif>
                                            SMP</option>
                                        <option value="SMA/SMK"
                                            @if ($employee->pendidikan_terakhir == 'SMA/SMK') {{ 'selected="selected"' }} @endif>
                                            SMA/SMK</option>
                                        <option value="D3"
                                            @if ($employee->pendidikan_terakhir == 'D3') {{ 'selected="selected"' }} @endif>
                                            D3</option>
                                        <option value="S1"
                                            @if ($employee->pendidikan_terakhir == 'S1') {{ 'selected="selected"' }} @endif>
                                            S1</option>
                                        <option value="S2"
                                            @if ($employee->pendidikan_terakhir == 'S2') {{ 'selected="selected"' }} @endif>
                                            S2</option>
                                        <option value="S3"
                                            @if ($employee->pendidikan_terakhir == 'S3') {{ 'selected="selected"' }} @endif>
                                            S3</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Golongan Darah</label>
                                    <select id="input39" class="form-select" name="golongan_darah">
                                        <option value="">Pilih Golongan Darah</option>
                                        <option value="A"
                                            @if ($employee->golongan_darah == 'A') {{ 'selected="selected"' }} @endif>
                                            A</option>
                                        <option value="B"
                                            @if ($employee->golongan_darah == 'B') {{ 'selected="selected"' }} @endif>
                                            B</option>
                                        <option value="AB"
                                            @if ($employee->golongan_darah == 'AB') {{ 'selected="selected"' }} @endif>
                                            AB</option>
                                        <option value="O"
                                            @if ($employee->golongan_darah == 'O') {{ 'selected="selected"' }} @endif>
                                            O</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Status Menikah</label>
                                    <select id="input39" class="form-select" name="status_nikah">
                                        <option value="">Pilih Status Menikah</option>
                                        <option value="Single"
                                            @if ($employee->status_nikah == 'Single') {{ 'selected="selected"' }} @endif>
                                            Single</option>
                                        <option value="Menikah"
                                            @if ($employee->status_nikah == 'Menikah') {{ 'selected="selected"' }} @endif>
                                            Menikah</option>
                                        <option value="Janda"
                                            @if ($employee->status_nikah == 'Janda') {{ 'selected="selected"' }} @endif>
                                            Janda</option>
                                        <option value="Duda"
                                            @if ($employee->status_nikah == 'Duda') {{ 'selected="selected"' }} @endif>
                                            Duda</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nama Ayah Kandung</label>
                                    <input type="text" onkeyup="huruf(this);" name="nama_ayah"
                                        value="{{ $employee->nama_ayah }}" class="form-control"
                                        placeholder="Masukan Nama Ayah Kandung">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nama Ibu Kandung</label>
                                    <input type="text" onkeyup="huruf(this);" name="nama_ibu"
                                        value="{{ $employee->nama_ibu }}" class="form-control"
                                        placeholder="Masukan Nama Ibu Kandung">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor Kartu keluarga</label>
                                    <input type="text" onkeyup="angka(this);" maxlength="16"
                                        name="nomor_kartu_keluarga" value="{{ $employee->nomor_kartu_keluarga }}"
                                        class="form-control" placeholder="Masukan Nomor Kartu Keluarga">
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper1.previous()">Previous</button>
                                        <button type="button" class="btn btn-info"
                                            onclick="stepper1.next()">Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab Karyawan --}}

                        {{-- Tab Alamat --}}
                        <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Alamat</h5>
                            <p class="mb-4">Alamat Lengkap Karyawan</p>

                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <input type="text" name="alamat" value="{{ $employee->alamat }}"
                                        class="form-control" placeholder="Masukan Alamat Lengkap">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">RT / RW</label>
                                    <div class="input-group">
                                        <input type="text" value="{{ $employee->rt }}" maxlength="3"
                                            placeholder="RT" name="rt" class="form-control">
                                        <input type="text" placeholder="RW" value="{{ $employee->rw }}"
                                            name="rw" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" name="kelurahan" value="{{ $employee->kelurahan }}"
                                        class="form-control" placeholder="Masukan Nama Kelurahan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" name="kecamatan" value="{{ $employee->kecamatan }}"
                                        class="form-control" placeholder="Masukan Nama Kecamatan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Kabupaten / Kota</label>
                                    <input type="text" name="kota" value="{{ $employee->kota }}"
                                        class="form-control" placeholder="Masukan Nama Kabupaten / Kota">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" name="provinsi" value="{{ $employee->provinsi }}"
                                        class="form-control" placeholder="Masukan Nama Provinsi">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Kode POS</label>
                                    <input type="text" name="kode_pos" value="{{ $employee->kode_pos }}"
                                        class="form-control" placeholder="Masukan Kode POS">
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <button type="button" class="btn btn-primary"
                                                onclick="stepper1.previous()">Previous</button>
                                            <button type="button" class="btn btn-info"
                                                onclick="stepper1.next()">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab Alamat --}}

                        {{-- Tab BPJS --}}
                        <div id="test-l-4" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Your </h5>
                            <p class="mb-4">Inform companies about your education life</p>

                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor BPJS Kesehatan</label>
                                    <input type="text" onkeyup="angka(this);" maxlength="13"
                                        name="nomor_bpjskesehatan" value="{{ $employee->nomor_bpjskesehatan }}"
                                        class="form-control" placeholder="Masukan Nomor BPJS Kesehatan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Nomor BPJS Ketenagakerjaan</label>
                                    <input type="text" maxlength="11" name="nomor_bpjsketenagakerjaan"
                                        value="{{ $employee->nomor_bpjsketenagakerjaan }}" class="form-control"
                                        placeholder="Masukan Nomor BPJS Ketenagakerjaan">
                                </div>

                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <button type="button" class="btn btn-primary"
                                                    onclick="stepper1.previous()">Previous</button>
                                                <button type="button" class="btn btn-info"
                                                    onclick="stepper1.next()">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab BPJS --}}

                        {{-- Tab Foto --}}
                        <div id="test-l-5" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Foto</h5>
                            <p class="mb-4">Foto Karyawan</p>

                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Foto Karyawan</label>
                                    @if ($employee->foto_karyawan)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}"
                                                class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                    <input type="file" name="foto_karyawan" class="form-control"
                                        placeholder="Foto Karyawan">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Foto KTP</label>
                                    @if ($employee->foto_ktp)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/assets/foto/ktp/' . $employee->foto_ktp) }}"
                                                class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                    <input type="file" name="foto_ktp" class="form-control" placeholder="Foto KTP">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Foto NPWP</label>
                                    @if ($employee->foto_npwp)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/assets/foto/npwp/' . $employee->foto_npwp) }}"
                                                class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                    <input type="file" name="foto_npwp" class="form-control" placeholder="Foto NPWP">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label">Foto Kartu Keluarga</label>
                                    @if ($employee->foto_kk)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/assets/foto/kk/' . $employee->foto_kk) }}"
                                                class="img-thumbnail" width="100">
                                        </div>
                                    @endif
                                    <input type="file" name="foto_kk" class="form-control"
                                        placeholder="Foto Kartu Keluarga">
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper1.previous()">Previous</button>
                                        <button class="btn btn-success px-4" onclick="stepper2.next()">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Tab Foto --}}

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>

    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/main.js') }}"></script>
@endsection

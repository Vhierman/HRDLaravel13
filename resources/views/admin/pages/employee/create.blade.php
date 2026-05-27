    @section('css')
        {{-- Select2 --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
        {{-- FormWizard --}}
        <link href="{{ asset('template_admin/assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet">

    @endsection
    @extends('admin.layouts.base')
    @section('title', 'Tambah Karyawan');
    @section('content')

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Karyawan</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item active" aria-current="page">Tambah Karyawan</li>
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
                        <form action="{{ route('employee.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            {{-- Tab Divisi --}}
                            <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                                <h5 class="mb-1">Divisi</h5>
                                <p class="mb-4">Deskripsi Pekerjaan</p>

                                <div class="row g-3">

                                    <div class="col-12 col-lg-6">
                                        <label for="perusahaan-select" class="form-label fs-6">Perusahaan</label>
                                        <select name="companies_id" class="form-select" id="perusahaan-select"
                                            data-placeholder="Pilih Perusahaan">
                                            <option value="">Pilih Perusahaan</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ old('companies_id') == $company->id ? 'selected' : '' }}>
                                                    {{ $company->nama_perusahaan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="area-select" class="form-label fs-6">Area</label>
                                        <select name="areas_id" class="form-select" id="area-select"
                                            data-placeholder="Pilih Area">
                                            <option value="">Pilih Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ old('areas_id') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->area }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="golongan-select" class="form-label fs-6">Golongan</label>
                                        <select name="golongans_id" class="form-select" id="golongan-select"
                                            data-placeholder="Pilih Golongan">
                                            <option value="">Pilih Golongan</option>
                                            @foreach ($golongans as $golongan)
                                                <option value="{{ $golongan->id }}"
                                                    {{ old('golongans_id') == $golongan->id ? 'selected' : '' }}>
                                                    {{ $golongan->golongan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="penempatan-select" class="form-label fs-6">Penempatan</label>
                                        <select name="divisions_id" class="form-select" id="penempatan-select"
                                            data-placeholder="Pilih Penempatan">
                                            <option value="">Pilih Penempatan</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{ $division->id }}"
                                                    {{ old('divisions_id') == $division->id ? 'selected' : '' }}>
                                                    {{ $division->penempatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="jabatan-select" class="form-label fs-6">Jabatan</label>
                                        <select name="positions_id" class="form-select" id="jabatan-select"
                                            data-placeholder="Pilih Jabatan">
                                            <option value="">Pilih Jabatan</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}"
                                                    {{ old('positions_id') == $position->id ? 'selected' : '' }}>
                                                    {{ $position->jabatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="jam_kerja-select" class="form-label fs-6">Jam kerja</label>
                                        <select name="working_hours_id" class="form-select" id="jam_kerja-select"
                                            data-placeholder="Pilih Jam Kerja">
                                            <option value="">Pilih Jam Kerja</option>
                                            @foreach ($working_hours as $working_hour)
                                                <option value="{{ $working_hour->id }}"
                                                    {{ old('working_hours_id') == $working_hour->id ? 'selected' : '' }}>
                                                    {{ $working_hour->jam_masuk . ' s/d ' . $working_hour->jam_pulang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="status_kerja-select" class="form-label fs-6">Status Kerja</label>
                                        <select class="form-select" name="status_kerja" id="status_kerja-select"
                                            data-placeholder="Pilih Status Kerja">
                                            <option value="">Pilih Status Kerja</option>
                                            <option value="PKWTT" {{ old('status_kerja') == 'PKWTT' ? 'selected' : '' }}>
                                                PKWTT</option>
                                            <option value="PKWT" {{ old('status_kerja') == 'PKWT' ? 'selected' : '' }}>
                                                PKWT</option>
                                            <option value="Harian"
                                                {{ old('status_kerja') == 'Harian' ? 'selected' : '' }}>
                                                Harian</option>
                                            <option value="Outsourcing"
                                                {{ old('status_kerja') == 'Outsourcing' ? 'selected' : '' }}>
                                                Outsourcing
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Tanggal Mulai Kerja</label>
                                        <input type="date" name="tanggal_mulai_kerja"
                                            value="{{ old('tanggal_mulai_kerja') }}" class="form-control"
                                            placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Tanggal Akhir Kerja</label>
                                        <input type="date" name="tanggal_akhir_kerja"
                                            value="{{ old('tanggal_akhir_kerja') }}" class="form-control"
                                            placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor Rekening</label>
                                        <input type="text" name="nomor_rekening" onkeyup="angka(this);"
                                            value="{{ old('nomor_rekening') }}" class="form-control"
                                            placeholder="Masukan Nomor Rekening">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nama Bank</label>
                                        <select id="input39" class="form-select" name="nama_bank"
                                            id="nama_bank-select" data-placeholder="Pilih Nama Bank">
                                            <option value="">Pilih Nama Bank</option>
                                            <option value="BCA" {{ old('nama_bank') == 'BCA' ? 'selected' : '' }}>
                                                BCA</option>
                                            <option value="Mandiri" {{ old('nama_bank') == 'Mandiri' ? 'selected' : '' }}>
                                                Mandiri</option>
                                            <option value="Permata" {{ old('nama_bank') == 'Permata' ? 'selected' : '' }}>
                                                Permata</option>
                                            <option value="BRI" {{ old('nama_bank') == 'BRI' ? 'selected' : '' }}>
                                                BRI
                                            </option>
                                            <option value="BRI" {{ old('nama_bank') == 'BRI' ? 'selected' : '' }}>
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
                            <div id="test-l-2" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger2">

                                <h5 class="mb-1">Karyawan</h5>
                                <p class="mb-4">Biodata Karyawan</p>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">NIK KTP</label>
                                        <input type="text" onkeyup="angka(this);" maxlength="16" name="nik_karyawan"
                                            value="{{ old('nik_karyawan') }}" class="form-control"
                                            placeholder="Masukan NIK Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nama Lengkap</label>
                                        <input type="text" onkeyup="huruf(this);" name="nama_karyawan"
                                            value="{{ old('nama_karyawan') }}" class="form-control"
                                            placeholder="Masukan Nama Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Email</label>
                                        <input type="text" name="email_karyawan" value="{{ old('email_karyawan') }}"
                                            class="form-control" placeholder="Masukan Email Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor Absen</label>
                                        <input type="text" onkeyup="angka(this);" name="nomor_absen"
                                            value="{{ old('nomor_absen') }}" maxlength="4" class="form-control"
                                            placeholder="Masukan Nomor Absen Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor NPWP</label>
                                        <input type="text" onkeyup="angka(this);" maxlength="16" name="nomor_npwp"
                                            value="{{ old('nomor_npwp') }}" class="form-control"
                                            placeholder="Masukan Nomor NPWP Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor Handphone</label>
                                        <input type="text" onkeyup="angka(this);" maxlength="16"
                                            name="nomor_handphone" value="{{ old('nomor_handphone') }}"
                                            class="form-control" placeholder="Masukan Nomor Handphone Karyawan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Tempat, Tanggal Lahir</label>
                                        <div class="input-group">
                                            <input type="text" value="{{ old('tempat_lahir') }}"
                                                placeholder="Masukan Tempat Lahir" name="tempat_lahir"
                                                class="form-control">
                                            <input type="date" placeholder="dd/mm/yyyy"
                                                value="{{ old('tanggal_lahir') }}" name="tanggal_lahir"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Agama</label>
                                        <select id="input39" class="form-select" name="agama">
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>
                                                Islam</option>
                                            <option value="Kristen Protestan"
                                                {{ old('agama') == 'Kristen Protestan' ? 'selected' : '' }}>
                                                Kristen Protestan</option>
                                            <option value="Kristen Katholik"
                                                {{ old('agama') == 'Kristen Katholik' ? 'selected' : '' }}>
                                                Kristen Katholik</option>
                                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>
                                                Hindu
                                            </option>
                                            <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}>
                                                Budha
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Jenis Kelamin</label>
                                        <select id="input39" class="form-select" name="jenis_kelamin">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Pria" {{ old('jenis_kelamin') == 'Pria' ? 'selected' : '' }}>
                                                Pria</option>
                                            <option value="Wanita"
                                                {{ old('jenis_kelamin') == 'Wanita' ? 'selected' : '' }}>
                                                Wanita</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Pendidikan Terakhir</label>
                                        <select id="input39" class="form-select" name="pendidikan_terakhir">
                                            <option value="">Pilih Pendidikan Terakhir</option>
                                            <option value="SD"
                                                {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>
                                                SD</option>
                                            <option value="SMP"
                                                {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>
                                                SMP</option>
                                            <option value="SMA/SMK"
                                                {{ old('pendidikan_terakhir') == 'SMA/SMK' ? 'selected' : '' }}>
                                                SMA/SMK</option>
                                            <option value="S1"
                                                {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>
                                                S1</option>
                                            <option value="S2"
                                                {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>
                                                S2</option>
                                            <option value="S3"
                                                {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>
                                                S3</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Golongan Darah</label>
                                        <select id="input39" class="form-select" name="golongan_darah">
                                            <option value="">Pilih Golongan Darah</option>
                                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>
                                                A</option>
                                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>
                                                B</option>
                                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>
                                                AB</option>
                                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>
                                                O</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Status Menikah</label>
                                        <select id="input39" class="form-select" name="status_nikah">
                                            <option value="">Pilih Status Menikah</option>
                                            <option value="Single"
                                                {{ old('status_nikah') == 'Single' ? 'selected' : '' }}>
                                                Single</option>
                                            <option value="Menikah"
                                                {{ old('status_nikah') == 'Menikah' ? 'selected' : '' }}>
                                                Menikah</option>
                                            <option value="Janda" {{ old('status_nikah') == 'Janda' ? 'selected' : '' }}>
                                                Janda</option>
                                            <option value="Duda" {{ old('status_nikah') == 'Duda' ? 'selected' : '' }}>
                                                Duda</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nama Ayah Kandung</label>
                                        <input type="text" onkeyup="huruf(this);" name="nama_ayah"
                                            value="{{ old('nama_ayah') }}" class="form-control"
                                            placeholder="Masukan Nama Ayah Kandung">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nama Ibu Kandung</label>
                                        <input type="text" onkeyup="huruf(this);" name="nama_ibu"
                                            value="{{ old('nama_ibu') }}" class="form-control"
                                            placeholder="Masukan Nama Ibu Kandung">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor Kartu keluarga</label>
                                        <input type="text" onkeyup="angka(this);" maxlength="16"
                                            name="nomor_kartu_keluarga" value="{{ old('nomor_kartu_keluarga') }}"
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
                            <div id="test-l-3" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger3">
                                <h5 class="mb-1">Alamat</h5>
                                <p class="mb-4">Alamat Lengkap Karyawan</p>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Alamat Lengkap</label>
                                        <input type="text" name="alamat" value="{{ old('alamat') }}"
                                            class="form-control" placeholder="Masukan Alamat Lengkap">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">RT / RW</label>
                                        <div class="input-group">
                                            <input type="text" value="{{ old('rt') }}" maxlength="3"
                                                placeholder="RT" name="rt" class="form-control">
                                            <input type="text" placeholder="RW" value="{{ old('rw') }}"
                                                name="rw" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Kelurahan</label>
                                        <input type="text" name="kelurahan" value="{{ old('kelurahan') }}"
                                            class="form-control" placeholder="Masukan Nama Kelurahan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Kecamatan</label>
                                        <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                                            class="form-control" placeholder="Masukan Nama Kecamatan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Kabupaten / Kota</label>
                                        <input type="text" name="kota" value="{{ old('kota') }}"
                                            class="form-control" placeholder="Masukan Nama Kabupaten / Kota">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Provinsi</label>
                                        <input type="text" name="provinsi" value="{{ old('provinsi') }}"
                                            class="form-control" placeholder="Masukan Nama Provinsi">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Kode POS</label>
                                        <input type="text" name="kode_pos" value="{{ old('kode_pos') }}"
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
                            <div id="test-l-4" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger3">
                                <h5 class="mb-1">Your </h5>
                                <p class="mb-4">Inform companies about your education life</p>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor BPJS Kesehatan</label>
                                        <input type="text" onkeyup="angka(this);" maxlength="13"
                                            name="nomor_bpjskesehatan" value="{{ old('nomor_bpjskesehatan') }}"
                                            class="form-control" placeholder="Masukan Nomor BPJS Kesehatan">
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Nomor BPJS Ketenagakerjaan</label>
                                        <input type="text" maxlength="11" name="nomor_bpjsketenagakerjaan"
                                            value="{{ old('nomor_bpjsketenagakerjaan') }}" class="form-control"
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
                            <div id="test-l-5" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger3">
                                <h5 class="mb-1">Foto</h5>
                                <p class="mb-4">Foto Karyawan</p>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fs-6">Foto Karyawan</label>
                                        <input type="file" name="foto_karyawan" class="form-control"
                                            placeholder="Foto Karyawan">
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-3">
                                            <button type="button" class="btn btn-primary"
                                                onclick="stepper1.previous()">Previous</button>
                                            <button class="btn btn-success px-4" onclick="stepper2.next()">Simpan</button>
                                        </div>
                                    </div>
                                </div><!---end row-->

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

        {{-- Form Wizard --}}
        <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
        <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/main.js') }}"></script>
        {{-- Select2 --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('template_admin/assets/plugins/select2/js/select2-custom.js') }}"></script>
        <script>
            $('#penempatan-select').select2({
                theme: 'bootstrap-5'
            });
            $('#jabatan-select').select2({
                theme: 'bootstrap-5'
            });
            $('#area-select').select2({
                theme: 'bootstrap-5'
            });
            $('#perusahaan-select').select2({
                theme: 'bootstrap-5'
            });
            $('#golongan-select').select2({
                theme: 'bootstrap-5'
            });
            $('#jam_kerja-select').select2({
                theme: 'bootstrap-5'
            });
            $('#status_kerja-select').select2({
                theme: 'bootstrap-5'
            });
            $('#nama_bank-select').select2({
                theme: 'bootstrap-5'
            });
        </script>
    @endsection

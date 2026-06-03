@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection
@extends('admin.layouts.base')
@section('title', 'Tambah Faskes');

@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Cek Kesehatan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Pemeriksaan Karyawan</li>
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
            <h5 class="mb-4">Form Tambah Data Pemeriksaan Karyawan</h5>
            <form action="{{ route('pemeriksaan_karyawan.tambah_data_pemeriksaan') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Nama Karyawan</label>
                        <select name="employees_id" class="form-select" id="karyawan-select"
                            data-placeholder="Pilih Karyawan">
                            <option value="">Pilih Karyawan</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employees_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->nik_karyawan }} - {{ $employee->nama_karyawan }} -
                                    {{ $employee->divisions->penempatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="faskes-select" class="form-label fs-6">Nama Faskes</label>
                        <select name="faskes_id" class="form-select" id="faskes-select" data-placeholder="Pilih Faskes">
                            <option value="">Pilih Faskes</option>
                            @foreach ($faskess as $faskes)
                                <option value="{{ $faskes->id }}" {{ old('faskes') == $faskes->id ? 'selected' : '' }}>
                                    {{ $faskes->nama_faskes }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan') }}"
                            class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Nomor Pemeriksaan</label>
                        <input type="text" name="nomor_mcu" value="{{ old('nomor_mcu') }}" class="form-control"
                            placeholder="Masukan Nomor Pemeriksaan">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Petugas / Dokter Pemeriksa</label>
                        <input type="text" name="dokter_pemeriksa" value="{{ old('dokter_pemeriksa') }}"
                            class="form-control" placeholder="Masukan Nama Petugas / Dokter Pemeriksa">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Jenis Pemeriksaan</label>
                        <select class="form-select" name="jenis_pemeriksaan" id="jenis_pemeriksaan-select"
                            data-placeholder="Pilih Jenis Pemeriksaan">
                            <option value="">Pilih Jenis Pemeriksaan</option>
                            <option value="Awal" {{ old('jenis_pemeriksaan') == 'Awal' ? 'selected' : '' }}>
                                Awal</option>
                            <option value="Berkala" {{ old('jenis_pemeriksaan') == 'Berkala' ? 'selected' : '' }}>
                                Berkala</option>
                            <option value="Khusus" {{ old('jenis_pemeriksaan') == 'Khusus' ? 'selected' : '' }}>
                                Khusus</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" value="{{ old('berat_badan') }}" class="form-control"
                            placeholder="Masukan Berat Badan" maxlength="3" onkeyup="onKeyUpAngka(this)">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" class="form-control"
                            placeholder="Masukan Tinggi Badan" maxlength="3" onkeyup="onKeyUpAngka(this)">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tekanan Darah</label>
                        <input type="text" name="tekanan_darah" value="{{ old('tekanan_darah') }}" class="form-control"
                            placeholder="Masukan Tekanan Darah">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Gula Darah</label>
                        <input type="text" name="gula_darah" value="{{ old('gula_darah') }}" class="form-control"
                            placeholder="Masukan Gula Darah">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">EKG</label>
                        <input type="text" name="ekg" value="{{ old('ekg') }}" class="form-control"
                            placeholder="Masukan EKG">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Pemeriksaan Berikutnya</label>
                        <input type="date" name="tanggal_pemeriksaan_berikutnya"
                            value="{{ old('tanggal_pemeriksaan_berikutnya') }}" class="form-control"
                            placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Catatan Dokter</label>
                        <textarea name="catatan_dokter" value="{{ old('catatan_dokter') }}" class="form-control"
                            placeholder="Masukan Catatan Dokter"></textarea>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Status Kelayakan</label>
                        <select class="form-select" name="status_kelayakan" id="status_kelayakan-select"
                            data-placeholder="Pilih Status Kelayakan">
                            <option value="">Pilih Status Kelayakan</option>
                            <option value="Fit" {{ old('status_kelayakan') == 'Fit' ? 'selected' : '' }}>
                                Fit</option>
                            <option value="Fit With Note"
                                {{ old('status_kelayakan') == 'Fit With Note' ? 'selected' : '' }}>
                                Fit With Note</option>
                            <option value="Unfit" {{ old('status_kelayakan') == 'Unfit' ? 'selected' : '' }}>
                                Unfit</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('pemeriksaan_karyawan.index') }}"
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
    <script>
        $('#faskes-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
    <script>
        $('#jenis_pemeriksaan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
    <script>
        $('#status_kelayakan-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection

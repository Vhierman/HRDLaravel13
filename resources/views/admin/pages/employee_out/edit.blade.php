@section('css')
    <link href="{{ asset('template_admin/assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet">
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@extends('admin.layouts.base')
@section('title', 'Edit Karyawan Keluar');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Data Karyawan Keluar</li>
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

            <div class="card-body p-4">
                <h5 class="mb-4">Form Edit Karyawan Keluar</h5>
                <form action="{{ route('employee_out.update', $item_employee_out->id) }}" method="post"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">NIK Karyawan</label>
                        <div class="col-sm-9">
                            <input type="text" name="nik_karyawan_keluar"
                                value="{{ $item_employee_out->nik_karyawan_keluar }}" class="form-control"
                                placeholder="Masukan NIK Karyawan Keluar" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Nama Karyawan</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_karyawan_keluar"
                                value="{{ $item_employee_out->nama_karyawan_keluar }}" class="form-control"
                                placeholder="Masukan Nama Karyawan Keluar" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Tanggal Keluar</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_keluar_karyawan_keluar"
                                value="{{ $item_employee_out->tanggal_keluar_karyawan_keluar }}" class="form-control"
                                placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Keterangan Keluar</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="keterangan_keluar" id="keterangan_keluar-select"
                                data-placeholder="Pilih Keterangan Keluar">
                                <option value="">Pilih Keterangan Keluar</option>
                                <option value="Berakhir Kontrak Kerja"
                                    @if ($item_employee_out->keterangan_keluar == 'Berakhir Kontrak Kerja') {{ 'selected="selected"' }} @endif>
                                    Berakhir Kontrak Kerja</option>
                                <option value="Pengunduran Diri"
                                    @if ($item_employee_out->keterangan_keluar == 'Pengunduran Diri') {{ 'selected="selected"' }} @endif>
                                    Pengunduran Diri</option>
                                <option value="Pemutusan Hubungan Kerja"
                                    @if ($item_employee_out->keterangan_keluar == 'Pemutusan Hubungan Kerja') {{ 'selected="selected"' }} @endif>
                                    Pemutusan Hubungan Kerja</option>
                                <option value="Memasuki Usia Pensiun"
                                    @if ($item_employee_out->keterangan_keluar == 'Memasuki Usia Pensiun') {{ 'selected="selected"' }} @endif>
                                    Memasuki Usia Pensiun
                                </option>
                                <option value="Meninggal Dunia"
                                    @if ($item_employee_out->keterangan_keluar == 'Meninggal Dunia') {{ 'selected="selected"' }} @endif>
                                    Meninggal Dunia
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Alasan Keluar</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="alasan_keluar" id="alasan_keluar-select"
                                data-placeholder="Pilih Alasan Keluar">
                                <option value="">Pilih Alasan Keluar</option>
                                <option value="Peluang Karir Lebih Baik"
                                    @if ($item_employee_out->alasan_keluar == 'Peluang Karir Lebih Baik') {{ 'selected="selected"' }} @endif>
                                    Peluang Karir Lebih Baik</option>
                                <option value="Gaji / Benefit Tidak Sesuai"
                                    @if ($item_employee_out->alasan_keluar == 'Gaji / Benefit Tidak Sesuai') {{ 'selected="selected"' }} @endif>
                                    Gaji / Benefit Tidak Sesuai</option>
                                <option value="Lingkungan Kerja Tidak Nyaman"
                                    @if ($item_employee_out->alasan_keluar == 'Lingkungan Kerja Tidak Nyaman') {{ 'selected="selected"' }} @endif>
                                    Lingkungan Kerja Tidak Nyaman</option>
                                <option value="Work-Life Balance Buruk"
                                    @if ($item_employee_out->alasan_keluar == 'Work-Life Balance Buruk') {{ 'selected="selected"' }} @endif>
                                    Work-Life Balance Buruk
                                </option>
                                <option value="Absensi Tinggi"
                                    @if ($item_employee_out->alasan_keluar == 'Absensi Tinggi') {{ 'selected="selected"' }} @endif>
                                    Absensi Tinggi
                                </option>
                                <option value="Relokasi Tempat Tinggal"
                                    @if ($item_employee_out->alasan_keluar == 'Relokasi Tempat Tinggal') {{ 'selected="selected"' }} @endif>
                                    Relokasi Tempat Tinggal
                                </option>
                                <option value="Melanjutkan Pendidikan"
                                    @if ($item_employee_out->alasan_keluar == 'Melanjutkan Pendidikan') {{ 'selected="selected"' }} @endif>
                                    Melanjutkan Pendidikan
                                </option>
                                <option value="Kondisi Kesehatan"
                                    @if ($item_employee_out->alasan_keluar == 'Kondisi Kesehatan') {{ 'selected="selected"' }} @endif>
                                    Kondisi Kesehatan
                                </option>
                                <option value="Konflik Dengan Atasan"
                                    @if ($item_employee_out->alasan_keluar == 'Konflik Dengan Atasan') {{ 'selected="selected"' }} @endif>
                                    Konflik Dengan Atasan
                                </option>
                                <option value="Konflik Dengan Rekan Kerja"
                                    @if ($item_employee_out->alasan_keluar == 'Konflik Dengan Rekan Kerja') {{ 'selected="selected"' }} @endif>
                                    Konflik Dengan Rekan Kerja
                                </option>
                                <option value="Beban Kerja Terlalu Tinggi"
                                    @if ($item_employee_out->alasan_keluar == 'Beban Kerja Terlalu Tinggi') {{ 'selected="selected"' }} @endif>
                                    Beban Kerja Terlalu Tinggi
                                </option>
                                <option value="Kurangnya Kesempatan Promosi"
                                    @if ($item_employee_out->alasan_keluar == 'Kurangnya Kesempatan Promosi') {{ 'selected="selected"' }} @endif>
                                    Kurangnya Kesempatan Promosi
                                </option>
                                <option value="Ketidakcocokan Budaya Perusahaan"
                                    @if ($item_employee_out->alasan_keluar == 'Ketidakcocokan Budaya Perusahaan') {{ 'selected="selected"' }} @endif>
                                    Ketidakcocokan Budaya Perusahaan
                                </option>
                                <option value="Perubahan Jalur Karir"
                                    @if ($item_employee_out->alasan_keluar == 'Perubahan Jalur Karir') {{ 'selected="selected"' }} @endif>
                                    Perubahan Jalur Karir
                                </option>
                                <option value="PHK / Efisiensi Perusahaan"
                                    @if ($item_employee_out->alasan_keluar == 'PHK / Efisiensi Perusahaan') {{ 'selected="selected"' }} @endif>
                                    PHK / Efisiensi Perusahaan
                                </option>
                                <option value="Pensiun"
                                    @if ($item_employee_out->alasan_keluar == 'Pensiun') {{ 'selected="selected"' }} @endif>
                                    Pensiun
                                </option>
                                <option value="Alasan Keluarga"
                                    @if ($item_employee_out->alasan_keluar == 'Alasan Keluarga') {{ 'selected="selected"' }} @endif>
                                    Alasan Keluarga
                                </option>
                                <option value="Menikah / Mengurus Anak"
                                    @if ($item_employee_out->alasan_keluar == 'Menikah / Mengurus Anak') {{ 'selected="selected"' }} @endif>
                                    Menikah / Mengurus Anak
                                </option>
                                <option value="Perusahaan Tutup"
                                    @if ($item_employee_out->alasan_keluar == 'Perusahaan Tutup') {{ 'selected="selected"' }} @endif>
                                    Perusahaan Tutup
                                </option>
                                <option value="Pelanggaran Disiplin"
                                    @if ($item_employee_out->alasan_keluar == 'Pelanggaran Disiplin') {{ 'selected="selected"' }} @endif>
                                    Pelanggaran Disiplin
                                </option>
                                <option value="Tidak Lulus Masa Percobaan"
                                    @if ($item_employee_out->alasan_keluar == 'Tidak Lulus Masa Percobaan') {{ 'selected="selected"' }} @endif>
                                    Tidak Lulus Masa Percobaan
                                </option>
                                <option value="Resign Tanpa Keterangan"
                                    @if ($item_employee_out->alasan_keluar == 'Resign Tanpa Keterangan') {{ 'selected="selected"' }} @endif>
                                    Resign Tanpa Keterangan
                                </option>
                                <option value="Transportasi / Jarak Tempuh"
                                    @if ($item_employee_out->alasan_keluar == 'Transportasi / Jarak Tempuh') {{ 'selected="selected"' }} @endif>
                                    Transportasi / Jarak Tempuh
                                </option>
                                <option value="Jadwal Kerja Tidak Sesuai"
                                    @if ($item_employee_out->alasan_keluar == 'Jadwal Kerja Tidak Sesuai') {{ 'selected="selected"' }} @endif>
                                    Jadwal Kerja Tidak Sesuai
                                </option>
                                <option value="Tekanan Kerja / Stress Kerja"
                                    @if ($item_employee_out->alasan_keluar == 'Tekanan Kerja / Stress Kerja') {{ 'selected="selected"' }} @endif>
                                    Tekanan Kerja / Stress Kerja
                                </option>
                                <option value="Ketidakpuasan Terhadap Manajemen"
                                    @if ($item_employee_out->alasan_keluar == 'Ketidakpuasan Terhadap Manajemen') {{ 'selected="selected"' }} @endif>
                                    Ketidakpuasan Terhadap Manajemen
                                </option>
                                <option value="Ingin Membuka Usaha Sendiri"
                                    @if ($item_employee_out->alasan_keluar == 'Ingin Membuka Usaha Sendiri') {{ 'selected="selected"' }} @endif>
                                    Ingin Membuka Usaha Sendiri
                                </option>
                                <option value="Pindah Industri Pekerjaan"
                                    @if ($item_employee_out->alasan_keluar == 'Pindah Industri Pekerjaan') {{ 'selected="selected"' }} @endif>
                                    Pindah Industri Pekerjaan
                                </option>
                                <option value="Mendapatkan Penawaran Luar Negeri"
                                    @if ($item_employee_out->alasan_keluar == 'Mendapatkan Penawaran Luar Negeri') {{ 'selected="selected"' }} @endif>
                                    Mendapatkan Penawaran Luar Negeri
                                </option>
                                <option value="Lainnya"
                                    @if ($item_employee_out->alasan_keluar == 'Lainnya') {{ 'selected="selected"' }} @endif>
                                    Lainnya
                                </option>
                            </select>
                        </div>
                    </div>



                    <div class="row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <div class="d-md-flex d-grid align-items-center gap-3">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                                class="material-icons-outlined">save</i>Simpan</button>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('employee_out.index') }}"
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
    </div>

@endsection

@section('js')
    {{-- SweetAlert --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- On Key Up --}}
    <script src="{{ asset('template_admin/assets/plugins/onkeyup-angka-huruf/onkeyup_angka_huruf.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('template_admin/assets/plugins/bs-stepper/js/main.js') }}"></script>
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('template_admin/assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        $('#nama_karyawan_keluar-select').select2({
            theme: 'bootstrap-5'
        });
        $('#keterangan_keluar-select').select2({
            theme: 'bootstrap-5'
        });
        $('#alasan_keluar-select').select2({
            theme: 'bootstrap-5'
        });
    </script>
@endsection

@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection
@extends('admin.layouts.base')
@section('title', 'Tambah Karyawan Keluar');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Karyawan</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Tambah Karyawan Keluar</li>
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
                <h5 class="mb-4">Form Tambah Karyawan Keluar</h5>
                <form action="{{ route('employee_out.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Nama</label>
                        <div class="col-sm-9">
                            <select name="employees_id" class="form-select" id="nama_karyawan_keluar-select"
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
                    </div>

                    <div class="row mb-3">
                        <label for="input35" class="col-sm-3 col-form-label fs-6">Tanggal Keluar</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal_keluar_karyawan_keluar"
                                value="{{ old('tanggal_keluar_karyawan_keluar') }}" class="form-control"
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
                                    {{ old('keterangan_keluar') == 'Berakhir Kontrak Kerja' ? 'selected' : '' }}>
                                    Berakhir Kontrak Kerja</option>
                                <option value="Pengunduran Diri"
                                    {{ old('keterangan_keluar') == 'Pengunduran Diri' ? 'selected' : '' }}>
                                    Pengunduran Diri</option>
                                <option value="Pemutusan Hubungan Kerja"
                                    {{ old('keterangan_keluar') == 'Pemutusan Hubungan Kerja' ? 'selected' : '' }}>
                                    Pemutusan Hubungan Kerja</option>
                                <option value="Memasuki Usia Pensiun"
                                    {{ old('keterangan_keluar') == 'Memasuki Usia Pensiun' ? 'selected' : '' }}>
                                    Memasuki Usia Pensiun
                                </option>
                                <option value="Meninggal Dunia"
                                    {{ old('keterangan_keluar') == 'Meninggal Dunia' ? 'selected' : '' }}>
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
                                    {{ old('alasan_keluar') == 'Peluang Karir Lebih Baik' ? 'selected' : '' }}>
                                    Peluang Karir Lebih Baik</option>
                                <option value="Gaji / Benefit Tidak Sesuai"
                                    {{ old('alasan_keluar') == 'Gaji / Benefit Tidak Sesuai' ? 'selected' : '' }}>
                                    Gaji / Benefit Tidak Sesuai</option>
                                <option value="Lingkungan Kerja Tidak Nyaman"
                                    {{ old('alasan_keluar') == 'Lingkungan Kerja Tidak Nyaman' ? 'selected' : '' }}>
                                    Lingkungan Kerja Tidak Nyaman</option>
                                <option value="Work-Life Balance Buruk"
                                    {{ old('alasan_keluar') == 'Work-Life Balance Buruk' ? 'selected' : '' }}>
                                    Work-Life Balance Buruk
                                </option>
                                <option value="Absensi Tinggi"
                                    {{ old('alasan_keluar') == 'Absensi Tinggi' ? 'selected' : '' }}>
                                    Absensi Tinggi
                                </option>
                                <option value="Relokasi Tempat Tinggal"
                                    {{ old('alasan_keluar') == 'Relokasi Tempat Tinggal' ? 'selected' : '' }}>
                                    Relokasi Tempat Tinggal
                                </option>
                                <option value="Melanjutkan Pendidikan"
                                    {{ old('alasan_keluar') == 'Melanjutkan Pendidikan' ? 'selected' : '' }}>
                                    Melanjutkan Pendidikan
                                </option>
                                <option value="Kondisi Kesehatan"
                                    {{ old('alasan_keluar') == 'Kondisi Kesehatan' ? 'selected' : '' }}>
                                    Kondisi Kesehatan
                                </option>
                                <option value="Konflik Dengan Atasan"
                                    {{ old('alasan_keluar') == 'Konflik Dengan Atasan' ? 'selected' : '' }}>
                                    Konflik Dengan Atasan
                                </option>
                                <option value="Konflik Dengan Rekan Kerja"
                                    {{ old('alasan_keluar') == 'Konflik Dengan Rekan Kerja' ? 'selected' : '' }}>
                                    Konflik Dengan Rekan Kerja
                                </option>
                                <option value="Beban Kerja Terlalu Tinggi"
                                    {{ old('alasan_keluar') == 'Beban Kerja Terlalu Tinggi' ? 'selected' : '' }}>
                                    Beban Kerja Terlalu Tinggi
                                </option>
                                <option value="Kurangnya Kesempatan Promosi"
                                    {{ old('alasan_keluar') == 'Kurangnya Kesempatan Promosi' ? 'selected' : '' }}>
                                    Kurangnya Kesempatan Promosi
                                </option>
                                <option value="Ketidakcocokan Budaya Perusahaan"
                                    {{ old('alasan_keluar') == 'Ketidakcocokan Budaya Perusahaan' ? 'selected' : '' }}>
                                    Ketidakcocokan Budaya Perusahaan
                                </option>
                                <option value="Perubahan Jalur Karir"
                                    {{ old('alasan_keluar') == 'Perubahan Jalur Karir' ? 'selected' : '' }}>
                                    Perubahan Jalur Karir
                                </option>
                                <option value="PHK / Efisiensi Perusahaan"
                                    {{ old('alasan_keluar') == 'PHK / Efisiensi Perusahaan' ? 'selected' : '' }}>
                                    PHK / Efisiensi Perusahaan
                                </option>
                                <option value="Pensiun" {{ old('alasan_keluar') == 'Pensiun' ? 'selected' : '' }}>
                                    Pensiun
                                </option>
                                <option value="Alasan Keluarga"
                                    {{ old('alasan_keluar') == 'Alasan Keluarga' ? 'selected' : '' }}>
                                    Alasan Keluarga
                                </option>
                                <option value="Menikah / Mengurus Anak"
                                    {{ old('alasan_keluar') == 'Menikah / Mengurus Anak' ? 'selected' : '' }}>
                                    Menikah / Mengurus Anak
                                </option>
                                <option value="Perusahaan Tutup"
                                    {{ old('alasan_keluar') == 'Perusahaan Tutup' ? 'selected' : '' }}>
                                    Perusahaan Tutup
                                </option>
                                <option value="Pelanggaran Disiplin"
                                    {{ old('alasan_keluar') == 'Pelanggaran Disiplin' ? 'selected' : '' }}>
                                    Pelanggaran Disiplin
                                </option>
                                <option value="Tidak Lulus Masa Percobaan"
                                    {{ old('alasan_keluar') == 'Tidak Lulus Masa Percobaan' ? 'selected' : '' }}>
                                    Tidak Lulus Masa Percobaan
                                </option>
                                <option value="Resign Tanpa Keterangan"
                                    {{ old('alasan_keluar') == 'Resign Tanpa Keterangan' ? 'selected' : '' }}>
                                    Resign Tanpa Keterangan
                                </option>
                                <option value="Transportasi / Jarak Tempuh"
                                    {{ old('alasan_keluar') == 'Transportasi / Jarak Tempuh' ? 'selected' : '' }}>
                                    Transportasi / Jarak Tempuh
                                </option>
                                <option value="Jadwal Kerja Tidak Sesuai"
                                    {{ old('alasan_keluar') == 'Jadwal Kerja Tidak Sesuai' ? 'selected' : '' }}>
                                    Jadwal Kerja Tidak Sesuai
                                </option>
                                <option value="Tekanan Kerja / Stress Kerja"
                                    {{ old('alasan_keluar') == 'Tekanan Kerja / Stress Kerja' ? 'selected' : '' }}>
                                    Tekanan Kerja / Stress Kerja
                                </option>
                                <option value="Ketidakpuasan Terhadap Manajemen"
                                    {{ old('alasan_keluar') == 'Ketidakpuasan Terhadap Manajemen' ? 'selected' : '' }}>
                                    Ketidakpuasan Terhadap Manajemen
                                </option>
                                <option value="Ingin Membuka Usaha Sendiri"
                                    {{ old('alasan_keluar') == 'Ingin Membuka Usaha Sendiri' ? 'selected' : '' }}>
                                    Ingin Membuka Usaha Sendiri
                                </option>
                                <option value="Pindah Industri Pekerjaan"
                                    {{ old('alasan_keluar') == 'Pindah Industri Pekerjaan' ? 'selected' : '' }}>
                                    Pindah Industri Pekerjaan
                                </option>
                                <option value="Mendapatkan Penawaran Luar Negeri"
                                    {{ old('alasan_keluar') == 'Mendapatkan Penawaran Luar Negeri' ? 'selected' : '' }}>
                                    Mendapatkan Penawaran Luar Negeri
                                </option>
                                <option value="Lainnya" {{ old('alasan_keluar') == 'Lainnya' ? 'selected' : '' }}>
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

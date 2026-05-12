@extends('admin.layouts.base')
@section('title', 'Edit Data Absensi');
@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Absensi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Absensi</li>
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
            <h5 class="mb-4">Form Edit Absensi</h5>
            <form action="{{ route('attendance.update', $item_attendance->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">NIK Karyawan</label>
                        <input type="text" name="nik_karyawan" value="{{ $item_attendance->employees->nik_karyawan }}"
                            class="form-control" placeholder="Masukan NIK Karyawan" readonly>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" value="{{ $item_attendance->employees->nama_karyawan }}"
                            class="form-control" placeholder="Masukan Nama Karyawan" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Tanggal Absen</label>
                        <input type="date" name="tanggal_absen" value="{{ $item_attendance->tanggal_absen }}"
                            class="form-control" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Keterangan Absen</label>
                        <select id="input39" class="form-select" name="keterangan_absen">
                            <option value="">Pilih Status Kerja</option>
                            <option value="Sakit" @if ($item_attendance->keterangan_absen == 'Sakit') {{ 'selected="selected"' }} @endif>
                                Sakit</option>
                            <option value="Ijin" @if ($item_attendance->keterangan_absen == 'Ijin') {{ 'selected="selected"' }} @endif>
                                Ijin</option>
                            <option value="Alpa" @if ($item_attendance->keterangan_absen == 'Alpa') {{ 'selected="selected"' }} @endif>
                                Alpa</option>
                            <option value="Cuti Tahunan"
                                @if ($item_attendance->keterangan_absen == 'Cuti Tahunan') {{ 'selected="selected"' }} @endif>
                                Cuti Tahunan
                            </option>
                            <option value="Cuti Khusus"
                                @if ($item_attendance->keterangan_absen == 'Cuti Khusus') {{ 'selected="selected"' }} @endif>
                                Cuti Khusus
                            </option>
                            <option value="OFF" @if ($item_attendance->keterangan_absen == 'OFF') {{ 'selected="selected"' }} @endif>
                                OFF
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-12 col-lg-6">
                        <label for="karyawan-select" class="form-label fs-6">Keterangan</label>
                        <input type="text" name="keterangan_cuti_khusus"
                            value="{{ $item_attendance->keterangan_cuti_khusus }}" class="form-control"
                            placeholder="Masukan Keterangan">
                    </div>
                </div>
                <br>
                <div class="row g-3">
                    <div class="col-sm-12 col-lg-6">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('attendance.index') }}"
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

@endsection

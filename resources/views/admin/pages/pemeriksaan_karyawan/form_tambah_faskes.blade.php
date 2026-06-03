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
            <h5 class="mb-4">Form Tambah Faskes</h5>
            <form action="{{ route('pemeriksaan_karyawan.tambah_faskes') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Nama Faskes</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="nama_faskes" value="{{ old('nama_faskes') }}"
                            id="input35" placeholder="Nama Faskes" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Alamat Faskes</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="alamat" value="{{ old('alamat') }}" id="input35"
                            placeholder="Alamat Faskes" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input36" class="col-sm-3 col-form-label">RT</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" onkeyup="angka(this);" name="rt"
                            value="{{ old('rt') }}" id="input36" placeholder="RT" maxlength="3" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input36" class="col-sm-3 col-form-label">RW</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" onkeyup="angka(this);" name="rw"
                            value="{{ old('rw') }}" id="input36" placeholder="RW" maxlength="3" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Kelurahan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="kelurahan" value="{{ old('kelurahan') }}"
                            id="input35" placeholder="Kelurahan" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Kecamatan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="kecamatan" value="{{ old('kecamatan') }}"
                            id="input35" placeholder="Kecamatan" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Kabupaten/Kota</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="kota" value="{{ old('kota') }}" id="input35"
                            placeholder="Kabupaten/Kota" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Provinsi</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="provinsi" value="{{ old('provinsi') }}"
                            id="input35" placeholder="Provinsi" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label">Kode POS</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="5" onkeyup="angka(this);"
                            name="kode_pos" value="{{ old('kode_pos') }}" id="input35" placeholder="Kode POS" />
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
                                    <a href="{{ route('pemeriksaan_karyawan.data_faskes') }}"
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

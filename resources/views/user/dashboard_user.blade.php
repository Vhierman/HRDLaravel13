@extends('user.layouts.base')
@section('title', 'Dashboard User')
@section('content')

    <section class="layout-container section section__height" id="home">
        <h2 class="section__title">Home</h2>
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-3 text-center">
                        <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}"
                            class="rounded-circle shadow img-fluid" style="width: 180px; height: 180px; object-fit: cover;">
                    </div>

                    <div class="col-lg-9 text-center text-lg-start">
                        <h2 class="fw-bold mb-1">{{ $employee->nama_karyawan }}</h2>
                        <h5 class="text-primary mb-3">{{ $employee->positions->jabatan }}</h5>
                        <p class="text-muted">
                            Bergabung sejak
                            {{ \Carbon\Carbon::parse($employee->tanggal_mulai_kerja)->isoformat('D MMMM Y') }} di
                            {{ $employee->companies->nama_perusahaan }}
                        </p>
                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2">
                            <span class="badge bg-primary">{{ $employee->companies->nama_perusahaan }}</span>
                            <span class="badge bg-success">{{ $employee->areas->area }}</span>
                            <span class="badge bg-warning text-dark">{{ $employee->divisions->penempatan }}</span>
                            <span class="badge bg-danger text-break">{{ $employee->email_karyawan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="layout-container section section__height" id="overtime">
        <h2 class="section__title">Overtime</h2>
        <div class="card text-center mx-auto" style="max-width: 600px;">
            <div class="card-header">
                Form Lihat Overtime {{ $employee->nama_karyawan }}
            </div>
            <div class="card-body">
                {{-- Pesan Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="text-start mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.overtime') }}" method="post" target="_blank" enctype="multipart/form-data">
                    @csrf
                    <label for="bulan" class="form-label fw-semibold">Pilih Periode Overtime</label>
                    <select class="form-select mb-3" aria-label="Default select example" name="bulan" id="bulan">
                        <option value="" selected>Pilih Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">Lihat</button>
                        <button class="btn btn-danger" type="reset">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-body-secondary small">
                Periode Overtime Tanggal 16 Sampai Tanggal 15 Setiap Bulannya
            </div>
        </div>
    </section>

    <section class="layout-container section section__height" id="skills">
        <h2 class="section__title">Skills</h2>
    </section>

    <section class="layout-container section section__height" id="cuti">
        <h2 class="section__title">Cuti</h2>
    </section>

    {{-- <section class="layout-container section section__height" id="contactme">
        <h2 class="section__title">Contact Me</h2>
    </section> --}}

@endsection

@extends('user.layouts.base')
@section('title', 'Dashboard User')

@section('content')
    <section class="section-page" id="home">
        <div class="container">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-3 text-center">
                            <img src="{{ asset('storage/assets/foto/karyawan/' . $employee->foto_karyawan) }}"
                                class="rounded-circle shadow" width="180" height="180" style="object-fit: contain;">
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
                                <span class="badge bg-danger">{{ $employee->email_karyawan }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center">
                        <div class="card-body">
                            <h2 class="fw-bold text-primary mb-1">{{ $MasaKerja }}</h2>
                            <p class="text-muted small mb-0">Tahun (Pengalaman)</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center">
                        <div class="card-body">
                            <h2 class="fw-bold text-success mb-1">{{ $UmurLengkap }}</h2>
                            <p class="text-muted small mb-0">Tahun (Umur)</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center">
                        <div class="card-body">
                            <h2 class="fw-bold text-warning mb-1">{{ $employee->pendidikan_terakhir }}</h2>
                            <p class="text-muted small mb-0">Pendidikan Terakhir</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center">
                        <div class="card-body">
                            <h2 class="fw-bold text-danger mb-1">{{ $employee->status_kerja }}</h2>
                            <p class="text-muted small mb-0">Status Kerja</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-page" id="about">
        <div class="container text-center">
            <h2 class="display-4 fw-bold">Overtime Karyawan</h2>


            <div class="card text-center">
                <div class="card-header">
                    Form Lihat Overtime {{ $employee->nama_karyawan }}
                </div>
                <div class="card-body">
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
                    <form action="{{ route('user.overtime') }}" method="post" target="_blank"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="inputPassword5" class="form-label">Pilih Periode Overtime</label>
                        <select class="form-select" aria-label="Default select example" name="bulan">
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
                        <div class="d-grid gap-2 mt-2">
                            <button class="btn btn-primary" type="submit">Lihat</button>
                        </div>
                        <div class="d-grid gap-2 mt-2">
                            <button class="btn btn-danger" type="reset">Cancel</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-body-secondary">
                    Periode Overtime Tanggal 16 Sampai Tanggal 15 Setiap Bulannya
                </div>
            </div>
        </div>

    </section>

    <section class="section-page" id="skills">
        <div class="container text-center">
            <h2 class="display-4 fw-bold">Coming Soon</h2>
        </div>
    </section>

    <section class="section-page" id="portfolio">
        <div class="container text-center">
            <h2 class="display-4 fw-bold">Coming Soon</h2>
        </div>
    </section>

    <section class="section-page" id="contactme">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Coming Soon</h1>
        </div>
    </section>
@endsection

@section('js')
    <script>
        // Logika klik manual link navbar
        document.querySelectorAll('.nav__link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav__link').forEach(item => item.classList.remove(
                    'active-link'));
                this.classList.add('active-link');
            });
        });
    </script>
@endsection

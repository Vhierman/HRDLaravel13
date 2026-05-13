@extends('admin.layouts.base')
@section('title', 'Edit Maksimal Upah BPJS Ketenagakerjaan');

@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Master</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Maksimal Upah BPJS Ketenagakerjaan</li>
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
            <h5 class="mb-4">Form Edit Maksimal Upah BPJS Ketenagakerjaan</h5>
            <form action="{{ route('maksimal_upah_bpjstk.update', $maksimal_upah_bpjsketenagakerjaan->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row mb-3">
                    <label for="input35" class="col-sm-3 col-form-label fs-6">Maksimal Upah BPJS Ketenagakerjaan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" onkeyup="angka(this);"
                            name="maksimal_upah_bpjsketenagakerjaan"
                            value="{{ $maksimal_upah_bpjsketenagakerjaan->maksimal_upah_bpjsketenagakerjaan }}"
                            id="input35" placeholder="Maksimal Upah BPJS Ketenagakerjaan" />
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
                                    <a href="{{ route('maksimal_upah_bpjstk.index') }}"
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

@extends('admin.layouts.base')
@section('title', 'Edit Minimal Upah');

@section('content')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Master</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Edit Minimal Upah</li>
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
            <h5 class="mb-4">Form Edit Minimal Upah</h5>
            <form action="{{ route('minimal_salary.update', $minimal_salary->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Area</label>
                        <select name="areas_id" class="form-select">
                            <option value="{{ $minimal_salary->areas_id }}">Pilih Area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}"
                                    @if ($minimal_salary->areas_id == $area->id) {{ 'selected="selected"' }} @endif>
                                    {{ $area->area }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fs-6">Minimal Upah</label>
                        <input type="text" name="minimal_upah" onkeyup="angka(this);"
                            value="{{ $minimal_salary->minimal_upah }}" class="form-control"
                            placeholder="Masukan Minimal Upah">
                    </div>
                </div>
                <br>
                <div class="row g-3">
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary px-4 raised d-flex gap-2"><i
                                            class="material-icons-outlined">save</i>Simpan</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('minimal_salary.index') }}"
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

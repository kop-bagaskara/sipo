@extends('main.layouts.main')
@section('title')
    Permohonan Data Karyawan
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Permohonan Data Karyawan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Permohonan Data Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Permohonan Data Karyawan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Data Lembur</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('hr.overtime.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="request_date" class="form-label required">Tanggal Lembur</label>
                                        <input type="date"
                                            class="form-control @error('request_date') is-invalid @enderror"
                                            id="request_date" name="request_date"
                                            value="{{ old('request_date', date('Y-m-d')) }}" required>
                                        @error('request_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label required">Lokasi</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                                            id="location" name="location" value="{{ old('location') }}"
                                            placeholder="Masukkan lokasi lembur" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_name" class="form-label required">Nama Karyawan</label>
                                        <input type="text"
                                            class="form-control @error('employee_name') is-invalid @enderror"
                                            id="employee_name" name="employee_name"
                                            value="{{ old('employee_name', auth()->user()->name) }}"
                                            placeholder="Masukkan nama karyawan" required>
                                        @error('employee_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label required">Bagian</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror"
                                            id="department" name="department" value="{{ old('department') }}"
                                            placeholder="Masukkan bagian/divisi" required>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label required">Jam Mulai</label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                            id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label required">Jam Selesai</label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                            id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="job_description" class="form-label required">Keterangan
                                            Pekerjaan</label>
                                        <textarea class="form-control @error('job_description') is-invalid @enderror" id="job_description"
                                            name="job_description" rows="4" placeholder="Jelaskan pekerjaan yang akan dilakukan saat lembur" required>{{ old('job_description') }}</textarea>
                                        @error('job_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('hr.overtime.index') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> Simpan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Validasi jam selesai harus setelah jam mulai
                const startTimeInput = document.getElementById('start_time');
                const endTimeInput = document.getElementById('end_time');

                function validateTime() {
                    const startTime = startTimeInput.value;
                    const endTime = endTimeInput.value;

                    if (startTime && endTime) {
                        if (endTime <= startTime) {
                            endTimeInput.setCustomValidity('Jam selesai harus setelah jam mulai');
                        } else {
                            endTimeInput.setCustomValidity('');
                        }
                    }
                }

                startTimeInput.addEventListener('change', validateTime);
                endTimeInput.addEventListener('change', validateTime);
            });
        </script>
    @endsection

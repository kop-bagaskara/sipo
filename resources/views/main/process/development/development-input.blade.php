@extends('main.layouts.main')
@section('title')
    Development Input - Job Development
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .form-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-submit:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .file-upload-area.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-high {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }

        .priority-medium {
            background-color: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffcc02;
        }

        .priority-low {
            background-color: #e8f5e8;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Loading overlay to block UI while submitting */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .loading-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.55);
            padding: 18px 22px;
            border-radius: 8px;
            color: #fff;
            min-width: 180px;
        }

        .loading-spinner-large {
            width: 46px;
            height: 46px;
            border: 4px solid #ffffff;
            border-top-color: #764ba2;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }

        .unit-job-input {
            background-color: #e9ecef;
            font-weight: 600;
            color: #495057;
        }

        .unit-job-input:focus {
            background-color: #fff;
            color: #495057;
        }

        .unit-job-input.is-valid {
            border-color: #38a169;
            box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
            background-color: #f0fff4;
        }

        .unit-job-input.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
            background-color: #fff5f5;
        }

        .unit-job-dropdown {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .unit-job-dropdown.is-valid {
            border-color: #38a169;
            box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
        }

        .unit-job-dropdown.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 200px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .checkbox-item label {
            margin: 0;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
        }

        .change-percentage-input {
            max-width: 120px;
        }

        .material-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }

        .material-detail {
            margin-top: 10px;
            padding-left: 26px;
        }

        .material-detail input {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .material-detail input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection
@section('page-title')
    Development Input
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Development Input</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Development Input</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="form-header">
                            @if (isset($mode) && $mode === 'view')
                                <h4 style="text-align: center;color: white;"><i class="mdi mdi-eye"></i> Detail Job
                                    Development</h4>
                                <p class="mb-0">Lihat detail lengkap job development</p>
                            @elseif(isset($mode) && $mode === 'edit')
                                <h4 style="text-align: center;color: white;"><i class="mdi mdi-pencil"></i> Edit Job
                                    Development</h4>
                                <p class="mb-0">Edit job development yang sudah ada</p>
                            @else
                                <h4 style="text-align: center;color: white;"><i class="mdi mdi-plus-circle"></i> Form Input
                                    Job Development</h4>
                                <p class="mb-0">Job Development dapat diinput melalui form ini</p>
                            @endif
                        </div>

                        <form id="submitJobDevelopment" method="POST">
                            @csrf
                            @if(isset($mode) && $mode === 'edit')
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <input type="text" name="status_job" id="status_job" class="form-control" value="OPEN"
                                hidden>

                            <!-- Hidden fields untuk tanggal input dan job deadline -->
                            <input type="hidden" name="tanggal" value="{{ isset($job) ? $job->tanggal->format('Y-m-d') : date('Y-m-d') }}">
                            <input type="hidden" name="job_deadline" value="{{ isset($job) ? ($job->job_deadline ? $job->job_deadline->format('Y-m-d') : '') : '' }}" id="hiddenJobDeadline">
                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered mb-2">
                                        <tr>
                                            <td style="width:5%"><b>No. </b></td>
                                            <td style="width:20%"><b>Customer</b></td>
                                            <td>
                                                <input type="text" name="customer" class="form-control"
                                                    value="{{ isset($job) ? $job->customer : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>1. </b></td>
                                            <td style="width:20%"><b>Product</b></td>
                                            <td>
                                                <input type="text" name="product" class="form-control"
                                                    value="{{ isset($job) ? $job->product : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>2. </b></td>
                                            <td style="width:20%"><b>Kode Design</b></td>
                                            <td>
                                                <input type="text" name="kode_design" class="form-control"
                                                    value="{{ isset($job) ? $job->kode_design : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>3. </b></td>
                                            <td style="width:20%"><b>Dimension</b></td>
                                            <td>
                                                <input type="text" name="dimension" class="form-control"
                                                    value="{{ isset($job) ? $job->dimension : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>4. </b></td>
                                            <td style="width:20%"><b>Material</b></td>
                                            <td>
                                                <input type="text" name="material" class="form-control"
                                                    value="{{ isset($job) ? $job->material : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>5. </b></td>
                                            <td style="width:20%"><b>Total Color</b></td>
                                            <td>
                                                <input type="text" name="total_color" class="form-control"
                                                    value="{{ isset($job) ? $job->total_color : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%">1. </td>
                                                        <td style="width:40%"><input type="text" name="color[1]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[1]) ? $job->colors[1] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                        <td style="width:10%">6. </td>
                                                        <td style="width:40%"><input type="text" name="color[6]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[6]) ? $job->colors[6] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">2. </td>
                                                        <td style="width:40%"><input type="text" name="color[2]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[2]) ? $job->colors[2] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                        <td style="width:10%">7. </td>
                                                        <td style="width:40%"><input type="text" name="color[7]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[7]) ? $job->colors[7] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">3. </td>
                                                        <td style="width:40%"><input type="text" name="color[3]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[3]) ? $job->colors[3] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                        <td style="width:10%">8. </td>
                                                        <td style="width:40%"><input type="text" name="color[8]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[8]) ? $job->colors[8] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">4. </td>
                                                        <td style="width:40%"><input type="text" name="color[4]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[4]) ? $job->colors[4] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                        <td style="width:10%">9. </td>
                                                        <td style="width:40%"><input type="text" name="color[9]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[9]) ? $job->colors[9] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">5. </td>
                                                        <td style="width:40%"><input type="text" name="color[5]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[5]) ? $job->colors[5] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                        <td style="width:10%">10. </td>
                                                        <td style="width:40%"><input type="text" name="color[10]"
                                                                id="color" class="form-control"
                                                                value="{{ isset($job) && isset($job->colors[10]) ? $job->colors[10] : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>6. </b></td>
                                            <td style="width:20%"><b>Qty Order Estimation</b></td>
                                            <td>
                                                <input type="text" name="qty_order_estimation" class="form-control"
                                                    value="{{ isset($job) ? $job->qty_order_estimation : '' }}"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }} required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>7. </b></td>
                                            <td style="width:20%"><b>Job Type</b></td>
                                            <td>
                                                <select name="job_type" class="form-control"
                                                    {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }} required>
                                                    <option value="">-- Pilih Tipe Job --</option>
                                                    <option value="new"
                                                        {{ isset($job) && $job->job_type === 'new' ? 'selected' : '' }}>
                                                        Produk Baru</option>
                                                    <option value="repeat"
                                                        {{ isset($job) && $job->job_type === 'repeat' ? 'selected' : '' }}>
                                                        Produk Repeat</option>
                                                </select>
                                                <div class="error-message"></div>
                                                <hr>
                                                <div class="material-khusus-section"
                                                    style="display: {{ isset($job) && $job->job_type === 'new' ? 'block' : 'none' }};">


                                                    <b>Material Khusus</b>
                                                    <div class="alert alert-info alert-sm mb-2">
                                                        <small><i class="fa fa-info-circle"></i> Centang material khusus
                                                            yang diperlukan, lalu isi detail materialnya</small>
                                                    </div>

                                                    <!-- Kertas Khusus -->
                                                    <div class="material-item mb-3">
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="kertas_khusus" value="1"
                                                                id="kertas_khusus"
                                                                {{ isset($job) && $job->kertas_khusus ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="kertas_khusus">Kertas Khusus</label>
                                                        </div>
                                                        <div class="material-detail" id="kertas_khusus_detail"
                                                            style="display: {{ isset($job) && $job->kertas_khusus ? 'block' : 'none' }};">
                                                            <input type="text" name="kertas_khusus_detail"
                                                                class="form-control mt-2"
                                                                placeholder="Jenis kertas khusus apa yang diperlukan?"
                                                                value="{{ isset($job) ? $job->kertas_khusus_detail : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </div>
                                                    </div>

                                                    <!-- Tinta Khusus -->
                                                    <div class="material-item mb-3">
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="tinta_khusus" value="1"
                                                                id="tinta_khusus"
                                                                {{ isset($job) && $job->tinta_khusus ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="tinta_khusus">Tinta Khusus</label>
                                                        </div>
                                                        <div class="material-detail" id="tinta_khusus_detail"
                                                            style="display: {{ isset($job) && $job->tinta_khusus ? 'block' : 'none' }};">
                                                            <input type="text" name="tinta_khusus_detail"
                                                                class="form-control mt-2"
                                                                placeholder="Jenis tinta khusus apa yang diperlukan?"
                                                                value="{{ isset($job) ? $job->tinta_khusus_detail : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </div>
                                                    </div>

                                                    <!-- Foil Khusus -->
                                                    <div class="material-item mb-3">
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="foil_khusus" value="1"
                                                                id="foil_khusus"
                                                                {{ isset($job) && $job->foil_khusus ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="foil_khusus">Foil Khusus</label>
                                                        </div>
                                                        <div class="material-detail" id="foil_khusus_detail"
                                                            style="display: {{ isset($job) && $job->foil_khusus ? 'block' : 'none' }};">
                                                            <input type="text" name="foil_khusus_detail"
                                                                class="form-control mt-2"
                                                                placeholder="Jenis foil khusus apa yang diperlukan?"
                                                                value="{{ isset($job) && $job->foil_khusus_detail ? $job->foil_khusus_detail : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </div>
                                                    </div>

                                                    <!-- Pale Tooling Khusus -->
                                                    <div class="material-item mb-3">
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="pale_tooling_khusus"
                                                                value="1" id="pale_tooling_khusus"
                                                                {{ isset($job) && $job->pale_tooling_khusus ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="pale_tooling_khusus">Pale Tooling Khusus</label>
                                                        </div>
                                                        <div class="material-detail" id="pale_tooling_khusus_detail"
                                                            style="display: {{ isset($job) && $job->pale_tooling_khusus ? 'block' : 'none' }};">
                                                            <input type="text" name="pale_tooling_khusus_detail"
                                                                class="form-control mt-2"
                                                                placeholder="Jenis pale tooling khusus apa yang diperlukan?"
                                                                value="{{ isset($job) ? ($job->pale_tooling_khusus_detail ? $job->pale_tooling_khusus_detail : '') : '' }}"
                                                                {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        </div>
                                                    </div>

                                                    <small class="text-muted">Field ini hanya muncul untuk produk baru.
                                                        Centang material khusus yang diperlukan, lalu isi detail
                                                        materialnya.</small>
                                                </div>

                                                {{-- Field Perubahan --}}
                                                <div class="perubahan-section"
                                                    style="display: {{ isset($job) && $job->job_type === 'repeat' ? 'block' : 'none' }};">
                                                    <b>Persentase Perubahan</b>
                                                <div class="row">
                                                        <div class="col">
                                                        <input type="number" name="change_percentage"
                                                                class="form-control change-percentage-input"
                                                                placeholder="%" min="0" max="100"
                                                            value="{{ isset($job) ? $job->change_percentage : '' }}"
                                                            {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                        <small class="text-muted">Berapa persen perubahan dari produk
                                                            repeat</small>
                                                    </div>
                                                </div>
                                                    <br>
                                                    <b>Detail Perubahan</b>
                                                    <br>

                                                <div class="checkbox-group">
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="change_details[]"
                                                            value="ukuran_dimensi" id="change_ukuran"
                                                            {{ isset($job) && isset($job->change_details) && in_array('ukuran_dimensi', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="change_ukuran">Ukuran/Dimensi</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                            <input type="checkbox" name="change_details[]"
                                                                value="material" id="change_material"
                                                            {{ isset($job) && isset($job->change_details) && in_array('material', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="change_material">Material</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="change_details[]" value="warna"
                                                            id="change_warna"
                                                            {{ isset($job) && isset($job->change_details) && in_array('warna', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="change_warna">Warna</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                            <input type="checkbox" name="change_details[]"
                                                                value="finishing" id="change_finishing"
                                                            {{ isset($job) && isset($job->change_details) && in_array('finishing', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="change_finishing">Finishing</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="change_details[]"
                                                            value="struktur_packaging" id="change_struktur"
                                                            {{ isset($job) && isset($job->change_details) && in_array('struktur_packaging', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="change_finishing">Struktur Packaging</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                            <input type="checkbox" name="change_details[]"
                                                                value="lainnya" id="change_lainnya"
                                                            {{ isset($job) && isset($job->change_details) && in_array('lainnya', $job->change_details) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="change_lainnya">Lainnya</label>
                                                    </div>
                                                </div>
                                                <div class="error-message"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="proses_row">
                                            <td style="width:5%"><b>8. </b></td>
                                            <td style="width:20%"><b>Proses Produksi <span class="text-danger">*</span></b></td>
                                            <td>
                                                <div class="alert alert-info alert-sm mb-2">
                                                    <small><i class="fa fa-info-circle"></i> Pilih minimal satu proses produksi yang diperlukan</small>
                                                </div>
                                                <div class="checkbox-group">
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="CETAK" id="proses_cetak"
                                                            {{ isset($job) && isset($job->proses) && in_array('CETAK', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_cetak">CETAK</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="PLONG" id="proses_plong"
                                                            {{ isset($job) && isset($job->proses) && in_array('PLONG', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_plong">PLONG</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="POTONG" id="proses_potong"
                                                            {{ isset($job) && isset($job->proses) && in_array('POTONG', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_potong">POTONG</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="LAMINASI" id="proses_laminasi"
                                                            {{ isset($job) && isset($job->proses) && in_array('LAMINASI', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_laminasi">LAMINASI</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="EMBOSS" id="proses_emboss"
                                                            {{ isset($job) && isset($job->proses) && in_array('EMBOSS', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_emboss">EMBOSS</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="FOIL" id="proses_foil"
                                                            {{ isset($job) && isset($job->proses) && in_array('FOIL', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_foil">FOIL</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="UV" id="proses_uv"
                                                            {{ isset($job) && isset($job->proses) && in_array('UV', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_uv">UV</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="VARNISH" id="proses_varnish"
                                                            {{ isset($job) && isset($job->proses) && in_array('VARNISH', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_varnish">VARNISH</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="FOLDING" id="proses_folding"
                                                            {{ isset($job) && isset($job->proses) && in_array('FOLDING', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_folding">FOLDING</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="BINDING" id="proses_binding"
                                                            {{ isset($job) && isset($job->proses) && in_array('BINDING', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_binding">BINDING</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="PACKAGING" id="proses_packaging"
                                                            {{ isset($job) && isset($job->proses) && in_array('PACKAGING', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_packaging">PACKAGING</label>
                                                    </div>
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" name="proses[]" value="QUALITY_CHECK" id="proses_quality_check"
                                                            {{ isset($job) && isset($job->proses) && in_array('QUALITY_CHECK', $job->proses) ? 'checked' : '' }}
                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                        <label for="proses_quality_check">QUALITY CHECK</label>
                                                    </div>
                                                </div>
                                                <div class="error-message" id="proses_error"></div>
                                            </td>
                                        </tr>

                                        <tr id="job_order_row">
                                            <td style="width:5%"><b>9. </b></td>
                                            <td style="width:20%"><b>Job Order</b></td>
                                            <td>
                                                <div id="job-order-container">
                                                    @if (isset($job) && isset($job->job_order) && count($job->job_order) > 0)
                                                        @foreach ($job->job_order as $index => $jobOrderItem)
                                                            <div class="job-order-item mb-3"
                                                                data-job-id="{{ $index + 1 }}">
                                                                <div class="row align-items-end">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Jenis Pekerjaan</label>
                                                                        <select
                                                                            name="job_order[{{ $index + 1 }}][jenis_pekerjaan]"
                                                                            class="form-control select2-job-order"
                                                                            {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}
                                                                            required>
                                                                            <option value="" disabled
                                                                                {{ !isset($jobOrderItem['jenis_pekerjaan']) ? 'selected' : '' }}>
                                                                                -- Pilih Jenis Pekerjaan --</option>
                                                                            @foreach (\App\Models\JenisPekerjaanPrepress::all() as $jenis)
                                                                                <option value="{{ $jenis->nama_jenis }}"
                                                                                    data-kode="{{ $jenis->id }}"
                                                                                    data-waktu="{{ $jenis->waktu_estimasi }}"
                                                                                    {{ isset($jobOrderItem['jenis_pekerjaan']) && $jobOrderItem['jenis_pekerjaan'] == $jenis->nama_jenis ? 'selected' : '' }}>
                                                                                    {{ $jenis->nama_jenis }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label">Unit Job</label>
                                                                        <input type="text"
                                                                            name="job_order[{{ $index + 1 }}][unit_job]"
                                                                            class="form-control unit-job-input"
                                                                            value="{{ $jobOrderItem['unit_job'] ?? '' }}"
                                                                            {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        @if (isset($mode) && $mode !== 'view')
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm btn-remove-job"
                                                                                {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                                                <i class="fa fa-trash"></i> Hapus
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <!-- Job Order Item 1 -->
                                                        <div class="job-order-item mb-3" data-job-id="1">
                                                            <div class="row align-items-end">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Jenis Pekerjaan</label>
                                                                    <select name="job_order[1][jenis_pekerjaan]"
                                                                        class="form-control select2-job-order" required>
                                                                        <option value="" disabled selected>-- Pilih
                                                                            Jenis Pekerjaan --</option>
                                                                        @foreach (\App\Models\JenisPekerjaanPrepress::all() as $jenis)
                                                                            <option value="{{ $jenis->nama_jenis }}"
                                                                                data-kode="{{ $jenis->id }}"
                                                                                data-waktu="{{ $jenis->waktu_estimasi }}">
                                                                                {{ $jenis->nama_jenis }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Unit Job</label>
                                                                    <input type="text" name="job_order[1][unit_job]"
                                                                        class="form-control unit-job-input">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm btn-remove-job"
                                                                        style="display: none;">
                                                                        <i class="fa fa-trash"></i> Hapus
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Tombol Add Job Order -->
                                                <div class="text-center mt-2">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        id="btn-add-job-order">
                                                        <i class="fa fa-plus"></i> Tambah Job Order
                                                    </button>
                                                </div>

                                                <div class="error-message"></div>
                                            </td>
                                        </tr>

                                        <tr id="file_data_row">
                                            <td style="width:5%"><b>10. </b></td>
                                            <td style="width:20%"><b>File atau Data <span class="text-danger">*</span></b>
                                            </td>
                                            <td>
                                                <div class="alert alert-info alert-sm mb-2">
                                                    <small><i class="fa fa-info-circle"></i> Pilih minimal satu jenis file
                                                        data yang akan disediakan</small>
                                                </div>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_cetak" value="Contoh Cetak"
                                                                {{ isset($job) && isset($job->file_data) && in_array('Contoh Cetak', $job->file_data) ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="file_data_contoh_cetak">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_contoh_cetak">Contoh
                                                                Cetak</label></td>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_produk" value="Contoh Produk"
                                                                {{ isset($job) && isset($job->file_data) && in_array('Contoh Produk', $job->file_data) ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="file_data_contoh_produk">&nbsp;</label>
                                                        </td>
                                                        <td style="width:30%"><label for="file_data_contoh_produk">Contoh
                                                                Produk</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_softcopy" value="File Softcopy"
                                                                {{ isset($job) && isset($job->file_data) && in_array('File Softcopy', $job->file_data) ? 'checked' : '' }}
                                                                {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }}>
                                                            <label for="file_data_softcopy">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_softcopy">File
                                                                Softcopy</label></td>
                                                    </tr>
                                                </table>
                                                <div class="error-message" id="file_data_error"></div>
                                            </td>
                                        </tr>
                                        <tr id="prioritas_row">
                                            <td style="width:5%"><b>11. </b></td>
                                            <td style="width:20%"><b>Prioritas</b></td>
                                            <td>
                                                <select name="prioritas_job" id="prioritas_job" class="form-control"
                                                    {{ isset($mode) && $mode === 'view' ? 'disabled' : '' }} required>
                                                    <option value="" disabled selected>-- Pilih Prioritas --</option>
                                                    <option value="Urgent"
                                                        {{ isset($job) && $job->prioritas_job === 'Urgent' ? 'selected' : '' }}>
                                                        Urgent</option>
                                                    <option value="Normal"
                                                        {{ isset($job) && $job->prioritas_job === 'Normal' ? 'selected' : '' }}>
                                                        Normal</option>
                                                </select>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr id="lampiran_row">
                                            <td style="width:5%"><b>12. </b></td>
                                            <td style="width:20%"><b>Lampiran</b></td>
                                            <td>
                                                @if (isset($job) && isset($job->attachment_paths) && count($job->attachment_paths) > 0)
                                                    <!-- Existing Attachments -->
                                                    <div class="alert alert-info mb-3">
                                                        <h6><i class="fa fa-paperclip"></i> Lampiran yang sudah ada:</h6>
                                                        <div class="">
                                                            @foreach ($job->attachment_paths as $index => $attachment)
                                                                <div class="">
                                                                    <div
                                                                        class="d-flex align-items-center p-2 border rounded">
                                                                        <i class="fa fa-file mr-2"></i>
                                                                        <span
                                                                            class="flex-grow-1">{{ basename($attachment) }}</span>
                                                                        <div class="btn-group btn-group-sm">
                                                                            <a href="/sipo_krisan/public/{{ $attachment }}"
                                                                                target="_blank"
                                                                                class="btn btn-outline-info btn-sm">
                                                                                <i class="fa fa-download"></i>
                                                                            </a>
                                                                            @if (!isset($mode) || $mode !== 'view')
                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-sm"
                                                                                    onclick="deleteExistingAttachment({{ $index }}, '{{ basename($attachment) }}')">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- No attachments message -->
                                                    <div class="alert alert-warning mb-3">
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        <strong>Tidak ada lampiran</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @if (isset($mode) && $mode === 'view')
                                                                Belum ada file yang diupload untuk job ini.
                                                            @else
                                                                Silakan upload file yang diperlukan untuk job ini.
                                                            @endif
                                                        </small>
                                                    </div>
                                                @endif

                                                @if (!isset($mode) || $mode !== 'view')
                                                    <!-- File Upload Area -->
                                                    <div class="file-upload-area" id="fileUploadArea">
                                                        <i class="fa fa-cloud-upload-alt"
                                                            style="font-size: 48px; color: #6c757d; margin-bottom: 15px;"></i>
                                                        <h5>Drag & Drop file atau klik untuk memilih</h5>
                                                        <p class="text-muted">Format yang didukung: PDF, DOC, DOCX, JPG,
                                                            JPEG, PNG (Max: 2MB per file)</p>
                                                        <p class="text-muted"><strong>💡 Bisa upload multiple files
                                                                sekaligus! Pilih beberapa file atau drag & drop beberapa
                                                                file bersamaan.</strong></p>
                                                        <input type="file" name="attachments[]" id="attachments"
                                                            class="form-control" style="display: none;"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                                                        <button type="button" class="btn btn-outline-primary"
                                                            onclick="document.getElementById('attachments').click()">
                                                            <i class="fa fa-folder-open"></i> Pilih File(s)
                                                        </button>
                                                    </div>
                                                    <div id="selectedFiles" style="display: none; margin-top: 15px;">
                                                        <h6><i class="fa fa-files-o"></i> File yang dipilih (<span
                                                                id="fileCount">0</span>):</h6>
                                                        <div id="fileList"></div>
                                                        <div class="text-center mt-2">
                                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                                onclick="document.getElementById('attachments').click()">
                                                                <i class="fa fa-plus"></i> Tambah File Lagi
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr id="catatan_row">
                                            <td style="width:5%"><b>13. </b></td>
                                            <td style="width:20%"><b>Catatan</b></td>
                                            <td>
                                                <textarea name="catatan" id="catatan" class="form-control" rows="3"
                                                    {{ isset($mode) && $mode === 'view' ? 'readonly' : '' }}>{{ isset($job) ? $job->catatan : '' }}</textarea>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <div class="row">
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Issued by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span>{{ auth()->user()->name }}</span>
                                            <br>
                                            <span>{{ date('Y-m-d') }}</span>
                                        </div>
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Received by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span>-</span>
                                            <br>
                                            <span>-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (isset($mode) && $mode === 'view')
                                <div class="text-center my-4">
                                    <a href="{{ route('development.marketing-jobs.list') }}"
                                        class="btn btn-secondary me-2">
                                        <i class="mdi mdi-arrow-left"></i> Kembali ke List
                                    </a>
                                    @if ($job->status_job === 'OPEN' && $job->marketing_user_id == auth()->id())
                                        <a href="{{ route('development.marketing-input.edit', $job->id) }}"
                                            class="btn btn-warning">
                                            <i class="mdi mdi-pencil"></i> Edit Job
                                        </a>
                                    @endif
                                </div>
                            @elseif(isset($mode) && $mode === 'edit')
                                <div class="text-center my-4">
                                    <button type="submit" class="btn btn-warning me-2" id="submitButton">
                                        <i class="mdi mdi-content-save"></i> Update Job Development
                                    </button>
                                    <a href="{{ route('development.marketing-jobs.list') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Batal
                                    </a>
                                </div>
                            @else
                                <button type="submit" class="btn btn-info my-4 w-100" id="submitButton">Submit Job
                                    Development</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="loading-box">
                <div class="loading-spinner-large"></div>
                <div>Sedang menyimpan...</div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            // Setup CSRF token untuk semua AJAX request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fungsi untuk auto-calculate job deadline berdasarkan job type
            function calculateJobDeadline() {
                var selectedJobType = $('select[name="job_type"]').val();
                var today = new Date();
                var deadline = new Date(today);

                if (selectedJobType === 'new') {
                    // Produk Baru: 30 hari deadline
                    deadline.setDate(today.getDate() + 30);
                    console.log('Produk Baru: 30 hari deadline');
                } else if (selectedJobType === 'repeat') {
                    // Produk Repeat: 14 hari deadline
                    deadline.setDate(today.getDate() + 14);
                    console.log('Produk Repeat: 14 hari deadline');
                }

                // Format tanggal untuk input hidden
                var year = deadline.getFullYear();
                var month = String(deadline.getMonth() + 1).padStart(2, '0');
                var day = String(deadline.getDate()).padStart(2, '0');
                var formattedDate = year + '-' + month + '-' + day;

                // Update hidden field
                $('#hiddenJobDeadline').val(formattedDate);

                console.log('Job Type:', selectedJobType);
                console.log('Calculated Deadline:', formattedDate);
            }

            // Fungsi untuk mengontrol visibility field berdasarkan job type
            function toggleJobTypeFields() {
                var selectedJobType = $('select[name="job_type"]').val();
                console.log('Selected job type:', selectedJobType);

                // Sembunyikan semua field terlebih dahulu
                $('.material-khusus-section').hide();
                $('.perubahan-section').hide();

                if (selectedJobType === 'new') {
                    // Tampilkan material khusus untuk produk baru
                    $('.material-khusus-section').show();
                    $('.perubahan-section').hide();
                    console.log('Showing material khusus section');
                } else if (selectedJobType === 'repeat') {
                    // Tampilkan field perubahan untuk produk repeat
                    $('.material-khusus-section').hide();
                    $('.perubahan-section').show();
                    console.log('Showing perubahan section');
                } else {
                    // Jika tidak ada yang dipilih, sembunyikan semua
                    $('.material-khusus-section').hide();
                    $('.perubahan-section').hide();
                    console.log('Hiding all sections');
                }

                // Recalculate deadline when job type changes
                calculateJobDeadline();
            }

            // Event listener untuk perubahan job type
            $('select[name="job_type"]').on('change', function() {
                console.log('Job type changed to:', $(this).val());
                toggleJobTypeFields();
                calculateJobDeadline();
            });

            // Jalankan sekali saat halaman dimuat untuk mengatur kondisi awal
            $(document).ready(function() {
                toggleJobTypeFields();
                calculateJobDeadline();
            });

            // Check if we're in view or edit mode
            var isViewMode = {{ isset($mode) && $mode === 'view' ? 'true' : 'false' }};
            var isEditMode = {{ isset($mode) && $mode === 'edit' ? 'true' : 'false' }};
            var jobId = {{ isset($job) ? $job->id : 'null' }};

            // If in view mode, disable all form inputs
            if (isViewMode) {
                $('input, select, textarea').prop('disabled', true);
                $('.btn-remove-job').hide();
                $('#btn-add-job-order').hide();
            }

            // If in edit mode, change form action and add hidden job_id
            if (isEditMode && jobId) {
                // Form action tidak perlu diubah karena menggunakan AJAX
                $('#submitJobDevelopment').append('<input type="hidden" name="job_id" value="' + jobId + '">');

                // Show existing attachments if any
                @if (isset($job) && isset($job->attachment_paths) && count($job->attachment_paths) > 0)
                    var existingAttachments = {!! json_encode($job->attachment_paths) !!};
                    if (existingAttachments && existingAttachments.length > 0) {
                        var attachmentHtml = '<div class="alert alert-info"><strong>Attachments saat ini:</strong><br>';
                        existingAttachments.forEach(function(attachment) {
                            attachmentHtml += '<a href="/sipo_krisan/public/' + attachment +
                                '" target="_blank" class="btn btn-sm btn-outline-info mr-2 mb-2">' +
                                '<i class="mdi mdi-download"></i> ' + attachment.split('/').pop() + '</a>';
                        });
                        attachmentHtml += '</div>';
                        $('#fileUploadArea').before(attachmentHtml);
                    }
                @endif
            }


            // Function to delete existing attachment
            window.deleteExistingAttachment = function(index, filename) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus file "${filename}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add hidden input to mark this attachment for deletion
                        var deleteInput = $('<input>').attr({
                            type: 'hidden',
                            name: 'delete_attachments[]',
                            value: index
                        });
                        $('#submitJobDevelopment').append(deleteInput);

                        // Hide the attachment display
                        $(`.col-md-6:eq(${index})`).fadeOut();

                        Swal.fire({
                            title: 'Berhasil!',
                            text: `File "${filename}" akan dihapus saat form disubmit.`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            };

            // Helper function untuk menampilkan SweetAlert yang lebih user-friendly
            function showSweetAlert(type, title, message, details = null) {
                let icon = '';
                let confirmButtonColor = '';

                switch (type) {
                    case 'error':
                        icon = 'error';
                        confirmButtonColor = '#e53e3e';
                        break;
                    case 'warning':
                        icon = 'warning';
                        confirmButtonColor = '#f59e0b';
                        break;
                    case 'info':
                        icon = 'info';
                        confirmButtonColor = '#3b82f6';
                        break;
                    case 'success':
                        icon = 'success';
                        confirmButtonColor = '#38a169';
                        break;
                }

                let htmlContent = `<div class="text-left">${message}`;
                if (details) {
                    htmlContent += `<hr><div class="mt-3"><strong>Detail:</strong><br>${details}</div>`;
                }
                htmlContent += '</div>';

                Swal.fire({
                    icon: icon,
                    title: title,
                    html: htmlContent,
                    confirmButtonText: 'OK',
                    confirmButtonColor: confirmButtonColor,
                    showCloseButton: true,
                    customClass: {
                        popup: 'swal-wide',
                        title: 'swal-title',
                        content: 'swal-content'
                    }
                });
            }

            // Helper function untuk menampilkan field errors
            function showFieldErrors(errors) {
                // Reset semua field error
                $('.form-control').removeClass('is-invalid');
                $('.error-message').hide();

                // Tampilkan error untuk setiap field
                $.each(errors, function(field, messages) {
                    let fieldElement = $(`[name="${field}"]`);
                    if (fieldElement.length) {
                        fieldElement.addClass('is-invalid');

                        // Buat atau update error message
                        let errorDiv = fieldElement.next('.error-message');
                        if (errorDiv.length === 0) {
                            errorDiv = $('<div class="error-message"></div>');
                            fieldElement.after(errorDiv);
                        }

                        errorDiv.html(Array.isArray(messages) ? messages.join('<br>') : messages).show();
                    }
                });
            }

            // File upload functions
            var selectedFiles = [];

            function removeFile(index) {
                selectedFiles.splice(index, 1);
                updateFileList();

                // Update file input
                var dt = new DataTransfer();
                selectedFiles.forEach(function(file) {
                    dt.items.add(file);
                });
                $('#attachments')[0].files = dt.files;
            }

            function showSelectedFiles() {
                if (selectedFiles.length > 0) {
                    $('#selectedFiles').show();
                    $('#fileUploadArea').hide();
                } else {
                    $('#selectedFiles').hide();
                    $('#fileUploadArea').show();
                }
            }

            function updateFileList() {
                var fileListHtml = '';
                selectedFiles.forEach(function(file, index) {
                    var fileSize = (file.size / 1024 / 1024).toFixed(2);
                    fileListHtml += `
                        <div class="alert alert-info d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="fa fa-file"></i>
                                <strong>${file.name}</strong>
                                <small class="text-muted">(${fileSize} MB)</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                $('#fileList').html(fileListHtml);
                $('#fileCount').text(selectedFiles.length);
                showSelectedFiles();
            }

            // Material khusus checkbox handlers
            function handleMaterialCheckbox(checkboxId, detailId) {
                var checkbox = $('#' + checkboxId);
                var detail = $('#' + detailId);

                checkbox.on('change', function() {
                    if (this.checked) {
                        detail.show();
                        detail.find('input').prop('required', true);
                    } else {
                        detail.hide();
                        detail.find('input').prop('required', false).val('');
                    }
                });
            }



            $(document).ready(function() {
                var isSubmittingJob = false;
                var jobOrderCounter = 1;

                // Inisialisasi Select2 untuk job order
                initializeSelect2JobOrder();

                // Inisialisasi checkbox material khusus
                handleMaterialCheckbox('kertas_khusus', 'kertas_khusus_detail');
                handleMaterialCheckbox('tinta_khusus', 'tinta_khusus_detail');
                handleMaterialCheckbox('foil_khusus', 'foil_khusus_detail');
                handleMaterialCheckbox('pale_tooling_khusus', 'pale_tooling_khusus_detail');

                // Set initial visibility based on job type
                var initialJobType = $('select[name="job_type"]').val();
                console.log('Initial job type:', initialJobType);

                if (initialJobType === 'repeat') {
                    console.log('Setting initial state for repeat product');
                    $('#special_materials_row').hide();
                    $('#change_percentage_row').show();
                    $('#change_details_row').show();
                } else if (initialJobType === 'new') {
                    console.log('Setting initial state for new product');
                    $('#special_materials_row').show();
                    $('#change_percentage_row').hide();
                    $('#change_details_row').hide();
                } else {
                    console.log('No job type selected, showing default state');
                    // Default state: show special materials, hide change rows
                    $('#special_materials_row').show();
                    $('#change_percentage_row').hide();
                    $('#change_details_row').hide();
                }

                // Force initial visibility with !important if needed
                if (initialJobType === 'repeat') {
                    $('#change_percentage_row').css('display', 'table-row !important');
                    $('#change_details_row').css('display', 'table-row !important');
                } else {
                    $('#special_materials_row').css('display', 'table-row !important');
                }

                // Debug initial visibility
                console.log('Initial visibility state:');
                console.log('Special materials visible:', $('#special_materials_row').is(':visible'));
                console.log('Change percentage visible:', $('#change_percentage_row').is(':visible'));
                console.log('Change details visible:', $('#change_details_row').is(':visible'));

                // Check all row visibility for debugging
                console.log('=== ROW VISIBILITY DEBUG ===');
                console.log('Row 1 (Product):', $('tr:contains("1. Product")').is(':visible'));
                console.log('Row 2 (Kode Design):', $('tr:contains("2. Kode Design")').is(':visible'));
                console.log('Row 3 (Dimension):', $('tr:contains("3. Dimension")').is(':visible'));
                console.log('Row 4 (Material):', $('tr:contains("4. Material")').is(':visible'));
                console.log('Row 5 (Material Khusus):', $('#special_materials_row').is(':visible'));
                console.log('Row 6 (Total Color):', $('tr:contains("6. Total Color")').is(':visible'));
                console.log('Row 7 (Qty Order Estimation):', $('tr:contains("7. Qty Order Estimation")').is(
                    ':visible'));
                console.log('Row 8 (Job Type):', $('tr:contains("8. Job Type")').is(':visible'));
                console.log('Row 9 (Persentase Perubahan):', $('#change_percentage_row').is(':visible'));
                console.log('Row 10 (Detail Perubahan):', $('#change_details_row').is(':visible'));
                console.log('Row 11 (Job Order):', $('#job_order_row').is(':visible'));
                console.log('Row 12 (File atau Data):', $('#file_data_row').is(':visible'));
                console.log('Row 13 (Prioritas):', $('#prioritas_row').is(':visible'));
                console.log('Row 14 (Lampiran):', $('#lampiran_row').is(':visible'));
                console.log('Row 15 (Catatan):', $('#catatan_row').is(':visible'));
                console.log('=== END DEBUG ===');

                // Event handler untuk tombol add job order
                $(document).on('click', '#btn-add-job-order', function() {
                    addJobOrder();
                });

                // Event handler untuk tombol remove job order
                $(document).on('click', '.btn-remove-job', function() {
                    removeJobOrder($(this));
                });

                // Event handler untuk perubahan jenis pekerjaan
                $(document).on('change', '.select2-job-order', function() {
                    updateUnitJob($(this));
                });

                // File upload handling
                $('#attachments').on('change', function() {
                    var files = Array.from(this.files);
                    var validFiles = [];

                    files.forEach(function(file) {
                        // Validasi ukuran file (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            showSweetAlert('error', 'File Terlalu Besar',
                                `File "${file.name}" ukurannya lebih dari 2MB.`,
                                'Silakan pilih file yang lebih kecil.');
                            return;
                        }

                        // Validasi tipe file
                        var allowedTypes = ['application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg', 'image/jpg', 'image/png'
                        ];
                        if (!allowedTypes.includes(file.type)) {
                            showSweetAlert('error', 'Format File Tidak Didukung',
                                `File "${file.name}" formatnya tidak didukung.`,
                                'Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.');
                            return;
                        }

                        validFiles.push(file);
                    });

                    // Add valid files to selectedFiles array
                    validFiles.forEach(function(file) {
                        // Check if file already exists
                        var exists = selectedFiles.some(function(existingFile) {
                            return existingFile.name === file.name && existingFile.size === file
                                .size;
                        });

                        if (!exists) {
                            selectedFiles.push(file);
                        }
                    });

                    updateFileList();
                });

                // Drag and drop functionality
                $('#fileUploadArea').on('dragover', function(e) {
                    e.preventDefault();
                    $(this).addClass('dragover');
                });

                $('#fileUploadArea').on('dragleave', function(e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');
                });

                $('#fileUploadArea').on('drop', function(e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');

                    var files = Array.from(e.originalEvent.dataTransfer.files);
                    var validFiles = [];

                    files.forEach(function(file) {
                        // Validasi ukuran file (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            showSweetAlert('error', 'File Terlalu Besar',
                                `File "${file.name}" ukurannya lebih dari 2MB.`,
                                'Silakan pilih file yang lebih kecil.');
                            return;
                        }

                        // Validasi tipe file
                        var allowedTypes = ['application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg', 'image/jpg', 'image/png'
                        ];
                        if (!allowedTypes.includes(file.type)) {
                            showSweetAlert('error', 'Format File Tidak Didukung',
                                `File "${file.name}" formatnya tidak didukung.`,
                                'Gunakan PDF, DOC, DOCX, JPG, JPEG, atau PNG.');
                            return;
                        }

                        validFiles.push(file);
                    });

                    // Add valid files to selectedFiles array
                    validFiles.forEach(function(file) {
                        // Check if file already exists
                        var exists = selectedFiles.some(function(existingFile) {
                            return existingFile.name === file.name && existingFile.size === file
                                .size;
                        });

                        if (!exists) {
                            selectedFiles.push(file);
                        }
                    });

                    // Update file input
                    var dt = new DataTransfer();
                    selectedFiles.forEach(function(file) {
                        dt.items.add(file);
                    });
                    $('#attachments')[0].files = dt.files;

                    updateFileList();
                });

                // Click to upload
                $('#fileUploadArea').on('click', function() {
                    $('#attachments').click();
                });

                // Fungsi untuk inisialisasi Select2 untuk job order
                function initializeSelect2JobOrder() {
                    $('.select2-job-order').select2({
                        placeholder: "Pilih jenis pekerjaan...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('body')
                    });
                }

                // Fungsi untuk menambah job order baru
                function addJobOrder() {
                    jobOrderCounter++;

                    // Ambil options dari select pertama untuk digunakan di select baru
                    var firstSelect = $('.select2-job-order').first();
                    var options = firstSelect.html();

                    var newJobOrder = `
                        <div class="job-order-item mb-3" data-job-id="${jobOrderCounter}">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <select name="job_order[${jobOrderCounter}][jenis_pekerjaan]" class="form-control select2-job-order" required>
                                        <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                                        @foreach (\App\Models\JenisPekerjaanPrepress::all() as $jenis)
                                            <option value="{{ $jenis->nama_jenis }}" data-kode="{{ $jenis->id }}" data-waktu="{{ $jenis->waktu_estimasi }}">{{ $jenis->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unit Job</label>
                                    <input type="text" name="job_order[${jobOrderCounter}][unit_job]" class="form-control unit-job-input" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-job">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#job-order-container').append(newJobOrder);

                    // Inisialisasi Select2 untuk job order baru
                    $(`[name="job_order[${jobOrderCounter}][jenis_pekerjaan]"]`).select2({
                        placeholder: "Pilih jenis pekerjaan...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('body')
                    });

                    // Update tombol hapus untuk semua item
                    updateRemoveButtons();
                }

                // Fungsi untuk menghapus job order
                function removeJobOrder(button) {
                    button.closest('.job-order-item').remove();
                    updateRemoveButtons();
                }

                // Fungsi untuk update tombol hapus
                function updateRemoveButtons() {
                    var jobOrderItems = $('.job-order-item');

                    if (jobOrderItems.length === 1) {
                        // Jika hanya ada 1 item, sembunyikan tombol hapus
                        jobOrderItems.find('.btn-remove-job').hide();
                    } else {
                        // Jika ada lebih dari 1 item, tampilkan semua tombol hapus
                        jobOrderItems.find('.btn-remove-job').show();
                    }
                }

                // Fungsi untuk update unit job berdasarkan jenis pekerjaan yang dipilih
                function updateUnitJob(selectElement) {
                    var selectedOption = selectElement.find('option:selected');
                    var jenisPekerjaan = selectedOption.data('kode');
                    var jobOrderItem = selectElement.closest('.job-order-item');
                    var unitJobInput = jobOrderItem.find('.unit-job-input');
                    var jobOrderId = jobOrderItem.data('job-id');

                    console.log('Updating unit job for Job Order ID:', jobOrderId);
                    console.log('Selected jenis pekerjaan:', jenisPekerjaan);

                    // Reset unit job input dan hapus dropdown yang ada
                    resetUnitJobField(unitJobInput, jobOrderId);

                    if (!jenisPekerjaan) {
                        return;
                    }

                    // AJAX call untuk mengambil unit job
                    $.ajax({
                        url: "{{ route('development.job-order.get-unit-job') }}",
                        type: "POST",
                        data: {
                            jenis_pekerjaan: jenisPekerjaan,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('AJAX Response for Job Order ID:', jobOrderId, response);

                            if (response.success && response.unit_jobs && response.unit_jobs.length > 0) {
                                // Selalu buat dropdown untuk konsistensi UI
                                createUnitJobDropdown(unitJobInput, response.unit_jobs, jobOrderId);
                            } else {
                                unitJobInput.val('');
                                unitJobInput.removeClass('is-valid');
                                unitJobInput.addClass('is-invalid');
                                console.error('Unit job tidak ditemukan untuk Job Order ID:', jobOrderId,
                                    response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error mengambil unit job untuk Job Order ID:', jobOrderId, xhr);
                            unitJobInput.val('');
                            unitJobInput.removeClass('is-valid');
                            unitJobInput.addClass('is-invalid');

                            // Tampilkan error message
                            var errorMessage = 'Gagal mengambil unit job';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            showSweetAlert('error', 'Error Unit Job', errorMessage,
                                'Silakan coba lagi atau hubungi administrator.');
                        }
                    });
                }

                // Fungsi untuk reset unit job field dan hapus dropdown
                function resetUnitJobField(unitJobInput, jobOrderId) {
                    console.log('Resetting unit job field for Job Order ID:', jobOrderId);

                    // Reset input value
                    unitJobInput.val('');
                    unitJobInput.removeClass('is-invalid is-valid');

                    // Hapus dropdown yang ada (jika ada)
                    var existingDropdown = unitJobInput.next('.unit-job-dropdown');
                    if (existingDropdown.length > 0) {
                        console.log('Removing existing dropdown for Job Order ID:', jobOrderId);
                        existingDropdown.remove();
                    }

                    // Tampilkan kembali input field
                    unitJobInput.show();

                    console.log('Unit job field reset completed for Job Order ID:', jobOrderId);
                }

                // Fungsi untuk membuat dropdown unit job
                function createUnitJobDropdown(unitJobInput, unitJobs, jobOrderId) {
                    console.log('Creating dropdown for Job Order ID:', jobOrderId, 'with unit jobs:', unitJobs);

                    // Hapus dropdown yang sudah ada dengan lebih spesifik
                    var existingDropdown = unitJobInput.siblings('.unit-job-dropdown');
                    if (existingDropdown.length > 0) {
                        console.log('Removing existing dropdown for Job Order ID:', jobOrderId);
                        existingDropdown.remove();
                    }

                    // Buat dropdown baru dengan ID yang unik
                    var dropdownId = 'unit-job-dropdown-' + jobOrderId;
                    var dropdown = $('<select class="form-control unit-job-dropdown" id="' + dropdownId + '" name="' +
                        unitJobInput.attr('name') + '">');

                    // Jika hanya ada 1 unit job, set sebagai selected
                    if (unitJobs.length === 1) {
                        dropdown.append('<option value="' + unitJobs[0] + '" selected>' + unitJobs[0] + '</option>');
                        // Auto-set value ke hidden input
                        unitJobInput.val(unitJobs[0]);
                    } else {
                        dropdown.append('<option value="" disabled selected>-- Pilih Unit Job --</option>');
                        unitJobs.forEach(function(unitJob) {
                            dropdown.append('<option value="' + unitJob + '">' + unitJob + '</option>');
                        });
                    }

                    // Event handler untuk dropdown
                    dropdown.on('change', function() {
                        var selectedUnitJob = $(this).val();
                        console.log('Dropdown changed for Job Order ID:', jobOrderId, 'Selected:',
                            selectedUnitJob);

                        if (selectedUnitJob) {
                            unitJobInput.val(selectedUnitJob);
                            unitJobInput.removeClass('is-invalid');
                            unitJobInput.addClass('is-valid');
                            $(this).removeClass('is-invalid');
                            $(this).addClass('is-valid');
                        } else {
                            unitJobInput.val('');
                            unitJobInput.removeClass('is-valid');
                            unitJobInput.addClass('is-invalid');
                            $(this).removeClass('is-valid');
                            $(this).addClass('is-invalid');
                        }
                    });

                    // Jika hanya ada 1 unit job, trigger change event untuk set valid state
                    if (unitJobs.length === 1) {
                        dropdown.trigger('change');
                    }

                    // Ganti input dengan dropdown
                    unitJobInput.hide();
                    unitJobInput.after(dropdown);

                    console.log('Dropdown created successfully for Job Order ID:', jobOrderId);
                }

                // Update tombol hapus saat halaman dimuat
                updateRemoveButtons();



                var submitJobDevelopment = "{{ route('development.development-input.store') }}";
                var updateJobUrl = "{{ isset($job) ? route('development.update-job', $job->id) : '' }}";
                var isEditMode = {{ isset($mode) && $mode === 'edit' ? 'true' : 'false' }};

                $('#submitJobDevelopment').submit(function(e) {
                    e.preventDefault();
                    if (isSubmittingJob) {
                        return; // prevent double submit
                    }

                    // Reset field errors
                    $('.form-control').removeClass('is-invalid');
                    $('.error-message').hide();

                    // Validasi proses - pastikan minimal ada satu yang dipilih
                    var prosesCheckboxes = $('input[name="proses[]"]:checked');
                    if (prosesCheckboxes.length === 0) {
                        // Tampilkan error di bawah field proses
                        $('#proses_error').html('Proses produksi wajib dipilih minimal satu jenis').show();
                        // Scroll ke field proses
                        $('html, body').animate({
                            scrollTop: $('#proses_error').offset().top - 100
                        }, 500);
                        showSweetAlert('error', 'Proses Produksi Wajib Dipilih',
                            'Mohon pilih minimal satu proses produksi yang diperlukan.',
                            'Pilih salah satu atau lebih dari: CETAK, PLONG, POTONG, LAMINASI, EMBOSS, FOIL, UV, VARNISH, FOLDING, BINDING, PACKAGING, atau QUALITY CHECK.'
                            );
                        return;
                    } else {
                        // Hide error jika sudah valid
                        $('#proses_error').hide();
                    }

                    // Validasi file_data - pastikan minimal ada satu yang dipilih
                    var fileDataCheckboxes = $('input[name="file_data[]"]:checked');
                    if (fileDataCheckboxes.length === 0) {
                        // Tampilkan error di bawah field file_data
                        $('#file_data_error').html('File data wajib dipilih minimal satu jenis').show();
                        // Scroll ke field file_data
                        $('html, body').animate({
                            scrollTop: $('#file_data_error').offset().top - 100
                        }, 500);
                        showSweetAlert('error', 'File Data Wajib Dipilih',
                            'Mohon pilih minimal satu jenis file data yang akan disediakan.',
                            'Pilih salah satu atau lebih dari: Contoh Cetak, Contoh Produk, atau File Softcopy.'
                            );
                        return;
                    } else {
                        // Hide error jika sudah valid
                        $('#file_data_error').hide();
                    }

                    // Validasi job order - pastikan minimal ada satu yang diisi
                    var jobOrderItems = $('.job-order-item');
                    var validJobOrders = 0;

                    console.log('Validating job orders...');
                    jobOrderItems.each(function(index) {
                        var jobOrderId = $(this).data('job-id');
                        var jenisPekerjaan = $(this).find('select[name*="[jenis_pekerjaan]"]').val();
                        var unitJob = $(this).find('input[name*="[unit_job]"]').val();

                        console.log('Job Order', index + 1, 'ID:', jobOrderId, 'Jenis:', jenisPekerjaan,
                            'Unit:', unitJob);

                        if (jenisPekerjaan && unitJob) {
                            validJobOrders++;
                            console.log('Job Order', index + 1, 'is VALID');
                        } else {
                            console.log('Job Order', index + 1, 'is INVALID');
                        }
                    });

                    console.log('Total valid job orders:', validJobOrders);

                    if (validJobOrders === 0) {
                        showSweetAlert('error', 'Job Order Wajib Diisi',
                            'Mohon pilih minimal satu jenis pekerjaan dan pastikan unit job terisi.',
                            'Pilih jenis pekerjaan untuk mengisi unit job secara otomatis.');
                        return;
                    }

                    // Prepare form data with file upload
                    var formData = new FormData(this);

                    // Debug: Log form data yang akan dikirim
                    console.log('Form data being sent:');
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // show loading & guard
                    isSubmittingJob = true;
                    $('#submitButton').prop('disabled', true).text(isEditMode ? 'Mengupdate...' :
                        'Menyimpan...');
                    $('#loadingOverlay').fadeIn(120);

                    $.ajax({
                        url: isEditMode ? updateJobUrl : submitJobDevelopment,
                        data: formData,
                        type: "POST",
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response);
                            if (response.errors) {
                                showFieldErrors(response.errors);
                                showSweetAlert('error', 'Validasi Error',
                                    'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.',
                                    'Silakan perbaiki field yang ditandai dengan border merah.');
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: isEditMode ?
                                        'Job Development berhasil diupdate!' :
                                        'Job Development berhasil disubmit! Mohon ditunggu Job diproses oleh Team Development. Untuk monitoring, silahkan klik Dashboard Development!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "{{ route('development.rnd-workspace.index') }}";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                            let errorDetails = '';

                            if (xhr.responseJSON) {
                                // Handle validation errors
                                if (xhr.responseJSON.errors) {
                                    showFieldErrors(xhr.responseJSON.errors);
                                    showSweetAlert('error', 'Validasi Error',
                                        'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.',
                                        'Silakan perbaiki field yang ditandai dengan border merah.'
                                        );
                                    return;
                                }

                                errorMessage = xhr.responseJSON.message || errorMessage;
                            }

                            // Jika CSRF token mismatch
                            if (xhr.status === 419) {
                                errorMessage =
                                    'Session expired. Silakan refresh halaman dan coba lagi.';
                                errorDetails =
                                    'Ini terjadi karena session login sudah berakhir. Silakan login ulang.';
                            }

                            // Jika server error
                            if (xhr.status >= 500) {
                                errorMessage =
                                    'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                                errorDetails =
                                    `Error Code: ${xhr.status}<br>Silakan hubungi administrator jika masalah berlanjut.`;
                            }

                            showSweetAlert('error', 'Gagal Submit Job Development', errorMessage,
                                errorDetails);
                        },
                        complete: function() {
                            // always hide overlay and re-enable submit (if not redirected yet)
                            $('#loadingOverlay').fadeOut(100);
                            $('#submitButton').prop('disabled', false).text(isEditMode ?
                                'Update Job Development' : 'Submit Job Development');
                            isSubmittingJob = false;
                        }
                    });
                });
            });
        </script>
    @endsection

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('sipo_krisan/public/assets/images/ficon.png') }}">
    <title>SiPO - Krisanthium</title>


    <link href="{{ asset('sipo_krisan/public/news/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sipo_krisan/public/news/plugins/morrisjs/morris.css') }}" rel="stylesheet">
    <link href="{{ asset('sipo_krisan/public/news/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('sipo_krisan/public/news/css/colors/blue.css') }}" id="theme" rel="stylesheet">


    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
        .cust-col {
            white-space: nowrap;
        }

        /* Employee Information Styles */
        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
        }

        .info-item {
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
            margin-top: 0.25rem;
        }

        .card-header {
            border-bottom: none;
            /* border-radius: 8px 8px 0 0 !important; */
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .border-left-pink {
            border-left: 4px solid #e91e63 !important;
        }

        .border-left-purple {
            border-left: 4px solid #9c27b0 !important;
        }

        .border-left-orange {
            border-left: 4px solid #ff9800 !important;
        }

        .border-left-teal {
            border-left: 4px solid #009688 !important;
        }

        .border-left-indigo {
            border-left: 4px solid #3f51b5 !important;
        }

        .border-left-cyan {
            border-left: 4px solid #00bcd4 !important;
        }

        .border-left-dark {
            border-left: 4px solid #343a40 !important;
        }

        .text-pink {
            color: #e91e63 !important;
        }

        .text-purple {
            color: #9c27b0 !important;
        }

        .text-orange {
            color: #ff9800 !important;
        }

        .text-teal {
            color: #009688 !important;
        }

        .text-indigo {
            color: #3f51b5 !important;
        }

        .text-cyan {
            color: #00bcd4 !important;
        }

        .btn-outline-pink {
            color: #e91e63;
            border-color: #e91e63;
        }

        .btn-outline-pink:hover {
            color: #fff;
            background-color: #e91e63;
            border-color: #e91e63;
        }

        .btn-outline-purple {
            color: #9c27b0;
            border-color: #9c27b0;
        }

        .btn-outline-purple:hover {
            color: #fff;
            background-color: #9c27b0;
            border-color: #9c27b0;
        }

        .btn-outline-orange {
            color: #ff9800;
            border-color: #ff9800;
        }

        .btn-outline-orange:hover {
            color: #fff;
            background-color: #ff9800;
            border-color: #ff9800;
        }

        .btn-outline-teal {
            color: #009688;
            border-color: #009688;
        }

        .btn-outline-teal:hover {
            color: #fff;
            background-color: #009688;
            border-color: #009688;
        }

        .btn-outline-indigo {
            color: #3f51b5;
            border-color: #3f51b5;
        }

        .btn-outline-indigo:hover {
            color: #fff;
            background-color: #3f51b5;
            border-color: #3f51b5;
        }

        .btn-outline-cyan {
            color: #00bcd4;
            border-color: #00bcd4;
        }

        .btn-outline-cyan:hover {
            color: #fff;
            background-color: #00bcd4;
            border-color: #00bcd4;
        }

        /* Calendar Styles */
        .calendar-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .fc-event {
            border-radius: 4px !important;
            border: none !important;
            padding: 2px 4px !important;
            font-size: 12px !important;
        }

        .absence-event {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .shift-change-event {
            background-color: #4ecdc4 !important;
            color: white !important;
        }

        .overtime-event {
            background-color: #ffe66d !important;
            color: #333 !important;
        }

        .vehicle-event {
            background-color: #95e1d3 !important;
            color: #333 !important;
        }

        .asset-event {
            background-color: #a8e6cf !important;
            color: #333 !important;
        }

        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }

        .preloader .circular {
            width: 50px;
            height: 50px;
        }

        .preloader .path {
            stroke: #007bff;
            stroke-linecap: round;
            animation: dash 1.5s ease-in-out infinite;
        }

        .nav-tabs {
            display: flex;
            width: 100%;
            border-bottom: 1px solid #dee2e6;
        }

        .nav-tabs .nav-item {
            flex: 1;
            text-align: center;
        }

        .nav-tabs .nav-link {
            border-radius: 0;
            font-weight: 500;
            width: 100%;
            border: 1px solid #dee2e6;
            border-bottom: none;
            padding: 12px 8px;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .checkbox-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .tab-saved {
            background-color: #d4edda !important;
            border-color: #c3e6cb !important;
        }

        .tab-saved .nav-link {
            color: #155724 !important;
        }

        /* Styling untuk checkbox di tabel Kemampuan Bahasa */
        .table tbody td.text-center {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .table tbody td.text-center input[type="checkbox"] {
            margin: 0 5px 0 0 !important;
            width: 18px !important;
            height: 18px !important;
            cursor: pointer;
            vertical-align: middle;
            display: inline-block;
        }

        .table tbody td.text-center label {
            margin: 0 !important;
            cursor: pointer;
            display: inline !important;
            vertical-align: middle;
            font-weight: normal;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .nav-tabs {
                flex-direction: column;
                width: 100%;
            }

            .nav-tabs .nav-item {
                width: 100%;
                margin-bottom: 5px;
            }

            .nav-tabs .nav-link {
                padding: 10px 5px;
                font-size: 0.8rem;
                white-space: normal;
                text-align: center;
            }

            .tab-content {
                padding: 15px 10px;
            }

            .form-section {
                padding: 15px 10px;
            }

            .section-title {
                font-size: 1.1rem;
                padding-bottom: 8px;
                margin-bottom: 15px;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 8px 5px;
            }

            .table tbody td.text-center {
                font-size: 0.8rem;
            }

            .table tbody td.text-center input[type="checkbox"] {
                width: 16px !important;
                height: 16px !important;
                margin: 0 3px 0 0 !important;
            }

            .checkbox-group {
                flex-direction: column;
                gap: 10px;
            }

            .progress {
                height: 20px !important;
            }

            .progress-bar {
                font-size: 0.75rem;
            }

            #progressText,
            #testProgressText {
                font-size: 0.7rem;
            }

            .card-header h4 {
                font-size: 1.1rem;
            }

            .card-subtitle {
                font-size: 0.85rem;
            }

            .btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .btn-sm {
                padding: 5px 10px;
                font-size: 0.8rem;
            }

            .btn-lg {
                padding: 12px 20px;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                font-size: 0.75rem;
                padding: 8px 3px;
            }

            .tab-content {
                padding: 10px 5px;
            }

            .form-section {
                padding: 10px 5px;
            }

            .section-title {
                font-size: 1rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 6px 3px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 8px 10px;
            }



            .breadcrumb {
                font-size: 0.85rem;
            }
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 1, 150;
                stroke-dashoffset: 0;
            }

            50% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -35;
            }

            100% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -124;
            }
        }

        /* Breadcrumb Styles */
        .page-titles {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            /* padding: 15px 20px; */
            margin: 0;
        }

        .page-titles h3 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .page-titles .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .page-titles .breadcrumb-item {
            font-size: 14px;
        }

        .page-titles .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            color: #adb5bd;
            /* padding: 0 10px; */
            font-weight: normal;
            font-size: 12px;
        }

        .page-titles .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .page-titles .breadcrumb-item a:hover {
            color: #007bff;
            text-decoration: none;
        }

        .page-titles .breadcrumb-item.active {
            color: #495057;
            font-weight: 500;
        }

    </style>
</head>

<body class="fix-header fix-sidebar card-no-border logo-center">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                stroke-miterlimit="10" />
        </svg>
    </div>
    <div id="main-wrapper">
        {{-- <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.html">
                        <span class="text-white bebas-neue font-weight-bold">SiPO - Krisanthium</span>
                    </a>
                </div>

            </nav>
        </header> --}}
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">


        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor mb-0">Form Data Pelamar Kerja</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb mb-0">
                    {{-- <li class="breadcrumb-item">
                        <a href="javascript:void(0)">
                            <i class="mdi mdi-home"></i> HR
                        </a>
                    </li> --}}
                    {{-- <li class="breadcrumb-item">
                        <a href="javascript:void(0)">Dashboard Pelamar</a>
                    </li> --}}
                    <li class="breadcrumb-item active">PT. Krisanthium Offset Printing</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12" id="applicantForm">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">DATA PELAMAR KERJA</h4>
                        <p class="card-subtitle">Silakan isi form berikut dengan data yang lengkap dan benar</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('public.staff-applicant.store') }}" method="POST"
                            enctype="multipart/form-data" id="applicantForm">
                            @csrf
                            <input type="hidden" name="current_tab" id="currentTab"
                                value="{{ old('current_tab', $applicant ? 'data-diri' : 'posisi') }}">
                            <input type="hidden" name="saved_tabs" id="savedTabs" value="">
                            <input type="hidden" name="applicant_id" id="applicantId"
                                value="{{ old('applicant_id', $applicant ? $applicant->id : '') }}">
                            <input type="hidden" name="applicant_status" id="applicantStatus"
                                value="{{ old('applicant_status', $applicant ? $applicant->status : '') }}">

                            <!-- Navigation Tabs -->
                            <ul class="nav nav-tabs" id="applicantTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $applicant ? '' : 'active' }}" id="posisi-tab"
                                        data-toggle="tab" data-target="#posisi" type="button" role="tab">
                                        1. Posisi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $applicant ? 'active' : '' }}" id="data-diri-tab"
                                        data-toggle="tab" data-target="#data-diri" type="button" role="tab">
                                        2. Data Diri
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab"
                                        data-bs-target="#pendidikan" type="button" role="tab">
                                        3. Pendidikan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kursus-tab" data-bs-toggle="tab" data-bs-target="#kursus"
                                        type="button" role="tab">
                                        4. Kursus
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pengalaman-tab" data-bs-toggle="tab"
                                        data-bs-target="#pengalaman" type="button" role="tab">
                                        5. Pengalaman
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="keluarga-tab" data-bs-toggle="tab"
                                        data-bs-target="#keluarga" type="button" role="tab">
                                        6. Keluarga
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kemampuan-tab" data-bs-toggle="tab"
                                        data-bs-target="#kemampuan" type="button" role="tab">
                                        7. Kemampuan
                                    </button>
                                </li>
                            </ul>

                            <!-- Progress Bar -->
                            <div class="row mt-3 mb-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Progress Pengisian Form</small>
                                        <small class="text-muted" id="progressPercentage">0%</small>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" id="formProgressBar" style="width: 0%;"
                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span id="progressText" class="font-weight-bold">0 dari 7 tab
                                                tersimpan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="tab-content" id="applicantTabContent">
                                <!-- Tab 1: Posisi & Jabatan -->
                                <div class="tab-pane fade {{ $applicant ? '' : 'show active' }}" id="posisi"
                                    role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">1. Posisi / Jabatan</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="posisi_dilamar">Posisi / Jabatan yang dikehendaki <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('posisi_dilamar') is-invalid @enderror"
                                                        id="posisi_dilamar" name="posisi_dilamar"
                                                        value="{{ old('posisi_dilamar', $applicant ? $applicant->posisi_dilamar : '') }}"
                                                        required>
                                                    @error('posisi_dilamar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="gaji_terakhir">Gaji Terakhir <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('gaji_terakhir') is-invalid @enderror"
                                                        id="gaji_terakhir" name="gaji_terakhir"
                                                        value="{{ old('gaji_terakhir', $applicant ? $applicant->gaji_terakhir : '') }}"
                                                        required>
                                                    @error('gaji_terakhir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="gaji_diharapkan">Gaji Diharapkan <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('gaji_diharapkan') is-invalid @enderror"
                                                        id="gaji_diharapkan" name="gaji_diharapkan"
                                                        value="{{ old('gaji_diharapkan', $applicant ? $applicant->gaji_diharapkan : '') }}">
                                                    @error('gaji_diharapkan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="mulai_kerja">Mulai dapat bekerja Tanggal</label>
                                                    <input type="date"
                                                        class="form-control @error('mulai_kerja') is-invalid @enderror"
                                                        id="mulai_kerja" name="mulai_kerja"
                                                        value="{{ old('mulai_kerja', $applicant && $applicant->mulai_kerja ? $applicant->mulai_kerja->format('Y-m-d') : '') }}">
                                                    @error('mulai_kerja')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Data Diri -->
                                <div class="tab-pane fade {{ $applicant ? 'show active' : '' }}" id="data-diri"
                                    role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">2. Data Pelamar Kerja</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_lengkap">Nama Lengkap <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('nama_lengkap') is-invalid @enderror"
                                                        id="nama_lengkap" name="nama_lengkap"
                                                        value="{{ old('nama_lengkap', $applicant ? $applicant->nama_lengkap : '') }}"
                                                        required>
                                                    @error('nama_lengkap')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="alias">Alias <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('alias') is-invalid @enderror"
                                                        id="alias" name="alias"
                                                        value="{{ old('alias', $applicant ? $applicant->alias : '') }}"
                                                        required>
                                                    @error('alias')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="jenis_kelamin">Jenis Kelamin <span
                                                            class="text-danger">*</span></label>
                                                    <select
                                                        class="form-control @error('jenis_kelamin') is-invalid @enderror"
                                                        id="jenis_kelamin" name="jenis_kelamin" required>
                                                        <option value="">Pilih</option>
                                                        <option value="L"
                                                            {{ old('jenis_kelamin', $applicant ? $applicant->jenis_kelamin : '') == 'L' ? 'selected' : '' }}>
                                                            Laki-laki</option>
                                                        <option value="P"
                                                            {{ old('jenis_kelamin', $applicant ? $applicant->jenis_kelamin : '') == 'P' ? 'selected' : '' }}>
                                                            Perempuan</option>
                                                    </select>
                                                    @error('jenis_kelamin')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tempat_lahir">Tempat Lahir <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                        id="tempat_lahir" name="tempat_lahir"
                                                        value="{{ old('tempat_lahir', $applicant ? $applicant->tempat_lahir : '') }}"
                                                        required>
                                                    @error('tempat_lahir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tanggal_lahir">Tanggal Lahir <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date"
                                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                                        id="tanggal_lahir" name="tanggal_lahir"
                                                        value="{{ old('tanggal_lahir', $applicant && $applicant->tanggal_lahir ? $applicant->tanggal_lahir->format('Y-m-d') : '') }}"
                                                        required>
                                                    @error('tanggal_lahir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="agama">Agama <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control @error('agama') is-invalid @enderror"
                                                        id="agama" name="agama" required>
                                                        <option value="">Pilih Agama</option>
                                                        <option value="Islam"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Islam' ? 'selected' : '' }}>
                                                            Islam</option>
                                                        <option value="Kristen"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Kristen' ? 'selected' : '' }}>
                                                            Kristen</option>
                                                        <option value="Katolik"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Katolik' ? 'selected' : '' }}>
                                                            Katolik</option>
                                                        <option value="Hindu"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Hindu' ? 'selected' : '' }}>
                                                            Hindu</option>
                                                        <option value="Budha"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Budha' ? 'selected' : '' }}>
                                                            Budha</option>
                                                        <option value="Konghucu"
                                                            {{ old('agama', $applicant ? $applicant->agama : '') == 'Konghucu' ? 'selected' : '' }}>
                                                            Konghucu</option>
                                                    </select>
                                                    @error('agama')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kebangsaan">Kebangsaan <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('kebangsaan') is-invalid @enderror"
                                                        id="kebangsaan" name="kebangsaan"
                                                        value="{{ old('kebangsaan', $applicant ? $applicant->kebangsaan : 'Indonesia') }}"
                                                        required>
                                                    @error('kebangsaan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_ktp">No. KTP / SIM <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('no_ktp') is-invalid @enderror"
                                                        id="no_ktp" name="no_ktp"
                                                        value="{{ old('no_ktp', $applicant ? $applicant->no_ktp : '') }}"
                                                        required>
                                                    @error('no_ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Alamat KTP: <span class="text-danger">*</span></h6>
                                                <div class="form-group">
                                                    <textarea class="form-control @error('alamat_ktp') is-invalid @enderror" id="alamat_ktp" name="alamat_ktp"
                                                        rows="3" required>{{ old('alamat_ktp', $applicant ? $applicant->alamat_ktp : '') }}</textarea>
                                                    @error('alamat_ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="kode_pos_ktp">Kode Pos</label>
                                                    <input type="text"
                                                        class="form-control @error('kode_pos_ktp') is-invalid @enderror"
                                                        id="kode_pos_ktp" name="kode_pos_ktp"
                                                        value="{{ old('kode_pos_ktp', $applicant ? $applicant->kode_pos_ktp : '') }}">
                                                    @error('kode_pos_ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Alamat Domisili: <span class="text-danger">*</span></h6>
                                                <div class="form-group">
                                                    <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" id="alamat_domisili"
                                                        name="alamat_domisili" rows="3" required>{{ old('alamat_domisili', $applicant ? $applicant->alamat_domisili : '') }}</textarea>
                                                    @error('alamat_domisili')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="kode_pos_domisili">Kode Pos</label>
                                                    <input type="text"
                                                        class="form-control @error('kode_pos_domisili') is-invalid @enderror"
                                                        id="kode_pos_domisili" name="kode_pos_domisili"
                                                        value="{{ old('kode_pos_domisili', $applicant ? $applicant->kode_pos_domisili : '') }}">
                                                    @error('kode_pos_domisili')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="no_handphone">No. Handphone <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('no_handphone') is-invalid @enderror"
                                                        id="no_handphone" name="no_handphone"
                                                        value="{{ old('no_handphone', $applicant ? $applicant->no_handphone : '') }}"
                                                        required>
                                                    @error('no_handphone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="no_npwp">No. NPWP</label>
                                                    <input type="text"
                                                        class="form-control @error('no_npwp') is-invalid @enderror"
                                                        id="no_npwp" name="no_npwp"
                                                        value="{{ old('no_npwp', $applicant ? $applicant->no_npwp : '') }}">
                                                    @error('no_npwp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="email">Alamat Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email"
                                                        value="{{ old('email', $applicant ? $applicant->email : '') }}"
                                                        required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="bpjs_kesehatan">BPJS Kesehatan</label>
                                                    <input type="text"
                                                        class="form-control @error('bpjs_kesehatan') is-invalid @enderror"
                                                        id="bpjs_kesehatan" name="bpjs_kesehatan"
                                                        value="{{ old('bpjs_kesehatan', $applicant ? $applicant->bpjs_kesehatan : '') }}">
                                                    @error('bpjs_kesehatan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kontak_darurat">No. Kontak Darurat <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('kontak_darurat') is-invalid @enderror"
                                                        id="kontak_darurat" name="kontak_darurat"
                                                        value="{{ old('kontak_darurat', $applicant ? $applicant->kontak_darurat : '') }}"
                                                        required>
                                                    @error('kontak_darurat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hubungan_kontak_darurat">Hubungan <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('hubungan_kontak_darurat') is-invalid @enderror"
                                                        id="hubungan_kontak_darurat" name="hubungan_kontak_darurat"
                                                        value="{{ old('hubungan_kontak_darurat', $applicant ? $applicant->hubungan_kontak_darurat : '') }}"
                                                        required>
                                                    @error('hubungan_kontak_darurat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Pendidikan -->
                                <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">3. Data Pendidikan</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No.</th>
                                                        <th width="15%">Pendidikan</th>
                                                        <th width="12%">Tahun Masuk</th>
                                                        <th width="12%">Tahun Lulus</th>
                                                        <th width="20%">Nama Sekolah</th>
                                                        <th width="15%">Tempat</th>
                                                        <th width="21%">Jurusan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $pendidikanData =
                                                            $applicant && $applicant->pendidikan
                                                                ? $applicant->pendidikan
                                                                : [];
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $pendidikanItem = isset($pendidikanData[$i - 1])
                                                                ? $pendidikanData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td>
                                                                <select class="form-control"
                                                                    name="pendidikan[{{ $i }}][tingkat]">
                                                                    <option value="">Pilih</option>
                                                                    <option value="SD"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'SD' ? 'selected' : '' }}>
                                                                        SD</option>
                                                                    <option value="SMP"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'SMP' ? 'selected' : '' }}>
                                                                        SMP</option>
                                                                    <option value="SMA"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'SMA' ? 'selected' : '' }}>
                                                                        SMA</option>
                                                                    <option value="D1"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'D1' ? 'selected' : '' }}>
                                                                        D1</option>
                                                                    <option value="D2"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'D2' ? 'selected' : '' }}>
                                                                        D2</option>
                                                                    <option value="D3"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'D3' ? 'selected' : '' }}>
                                                                        D3</option>
                                                                    <option value="D4"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'D4' ? 'selected' : '' }}>
                                                                        D4</option>
                                                                    <option value="S1"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'S1' ? 'selected' : '' }}>
                                                                        S1</option>
                                                                    <option value="S2"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'S2' ? 'selected' : '' }}>
                                                                        S2</option>
                                                                    <option value="S3"
                                                                        {{ $pendidikanItem && $pendidikanItem['tingkat'] == 'S3' ? 'selected' : '' }}>
                                                                        S3</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="number" class="form-control"
                                                                    name="pendidikan[{{ $i }}][tahun_masuk]"
                                                                    min="1950" max="2030"
                                                                    value="{{ $pendidikanItem && isset($pendidikanItem['tahun_masuk']) ? $pendidikanItem['tahun_masuk'] : '' }}">
                                                            </td>
                                                            <td><input type="number" class="form-control"
                                                                    name="pendidikan[{{ $i }}][tahun_lulus]"
                                                                    min="1950" max="2030"
                                                                    value="{{ $pendidikanItem && isset($pendidikanItem['tahun_lulus']) ? $pendidikanItem['tahun_lulus'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pendidikan[{{ $i }}][nama_sekolah]"
                                                                    value="{{ $pendidikanItem && isset($pendidikanItem['nama_sekolah']) ? $pendidikanItem['nama_sekolah'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pendidikan[{{ $i }}][tempat]"
                                                                    value="{{ $pendidikanItem && isset($pendidikanItem['tempat']) ? $pendidikanItem['tempat'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pendidikan[{{ $i }}][jurusan]"
                                                                    value="{{ $pendidikanItem && isset($pendidikanItem['jurusan']) ? $pendidikanItem['jurusan'] : '' }}">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 4: Kursus & Keterampilan -->
                                <div class="tab-pane fade" id="kursus" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">4. Data Kursus / Keterampilan / Organisasi</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No.</th>
                                                        <th width="25%">Jenis Kursus / Keterampilan / Organisasi
                                                        </th>
                                                        <th width="12%">Tahun Masuk</th>
                                                        <th width="12%">Tahun Lulus</th>
                                                        <th width="20%">Nama Lembaga</th>
                                                        <th width="15%">Tempat</th>
                                                        <th width="11%">Jurusan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $kursusData =
                                                            $applicant && $applicant->kursus ? $applicant->kursus : [];
                                                    @endphp
                                                    @for ($i = 1; $i <= 7; $i++)
                                                        @php
                                                            $kursusItem = isset($kursusData[$i - 1])
                                                                ? $kursusData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td><input type="text" class="form-control"
                                                                    name="kursus[{{ $i }}][jenis]"
                                                                    value="{{ $kursusItem && isset($kursusItem['jenis']) ? $kursusItem['jenis'] : '' }}">
                                                            </td>
                                                            <td><input type="number" class="form-control"
                                                                    name="kursus[{{ $i }}][tahun_masuk]"
                                                                    min="1950" max="2030"
                                                                    value="{{ $kursusItem && isset($kursusItem['tahun_masuk']) ? $kursusItem['tahun_masuk'] : '' }}">
                                                            </td>
                                                            <td><input type="number" class="form-control"
                                                                    name="kursus[{{ $i }}][tahun_lulus]"
                                                                    min="1950" max="2030"
                                                                    value="{{ $kursusItem && isset($kursusItem['tahun_lulus']) ? $kursusItem['tahun_lulus'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="kursus[{{ $i }}][nama_lembaga]"
                                                                    value="{{ $kursusItem && isset($kursusItem['nama_lembaga']) ? $kursusItem['nama_lembaga'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="kursus[{{ $i }}][tempat]"
                                                                    value="{{ $kursusItem && isset($kursusItem['tempat']) ? $kursusItem['tempat'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="kursus[{{ $i }}][jurusan]"
                                                                    value="{{ $kursusItem && isset($kursusItem['jurusan']) ? $kursusItem['jurusan'] : '' }}">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 5: Pengalaman Kerja -->
                                <div class="tab-pane fade" id="pengalaman" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">5. Data Pengalaman Kerja</h5>
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="addPengalamanRow()">
                                                <i class="mdi mdi-plus"></i> Tambah Baris
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="pengalamanTable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No.</th>
                                                        <th width="25%">Nama Perusahaan</th>
                                                        <th width="12%">Tanggal Masuk</th>
                                                        <th width="12%">Tanggal Keluar</th>
                                                        <th width="15%">Bagian</th>
                                                        <th width="15%">Jabatan</th>
                                                        <th width="14%">Alasan</th>
                                                        <th width="6%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pengalamanTableBody">
                                                    @php
                                                        $pengalamanData =
                                                            $applicant && $applicant->pengalaman
                                                                ? $applicant->pengalaman
                                                                : [];
                                                        $pengalamanCount =
                                                            count($pengalamanData) > 0 ? count($pengalamanData) : 1;
                                                    @endphp
                                                    @for ($i = 1; $i <= $pengalamanCount; $i++)
                                                        @php
                                                            $pengalamanItem = isset($pengalamanData[$i - 1])
                                                                ? $pengalamanData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pengalaman[{{ $i }}][nama_perusahaan]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['nama_perusahaan']) ? $pengalamanItem['nama_perusahaan'] : '' }}"
                                                                    required>
                                                            </td>
                                                            <td><input type="date" class="form-control"
                                                                    name="pengalaman[{{ $i }}][tanggal_masuk]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['tanggal_masuk']) ? $pengalamanItem['tanggal_masuk'] : '' }}"
                                                                    required>
                                                            </td>
                                                            <td><input type="date" class="form-control"
                                                                    name="pengalaman[{{ $i }}][tanggal_keluar]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['tanggal_keluar']) ? $pengalamanItem['tanggal_keluar'] : '' }}"
                                                                    required>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pengalaman[{{ $i }}][bagian]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['bagian']) ? $pengalamanItem['bagian'] : '' }}"
                                                                    required>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pengalaman[{{ $i }}][jabatan]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['jabatan']) ? $pengalamanItem['jabatan'] : '' }}"
                                                                    required>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="pengalaman[{{ $i }}][alasan]"
                                                                    value="{{ $pengalamanItem && isset($pengalamanItem['alasan']) ? $pengalamanItem['alasan'] : '' }}">
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($i > 1 || count($pengalamanData) > 1)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="removePengalamanRow(this)">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 6: Data Keluarga -->
                                <div class="tab-pane fade" id="keluarga" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">6. Data Keluarga (Suami/Isteri & Anak)</h5>
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="addKeluargaAnakRow()">
                                                <i class="mdi mdi-plus"></i> Tambah Baris
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="keluargaAnakTable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">NO.</th>
                                                        <th width="20%">Nama</th>
                                                        <th width="15%">Tempat Lahir</th>
                                                        <th width="15%">Tanggal Lahir</th>
                                                        <th width="10%">L / P</th>
                                                        <th width="15%">Pendidikan</th>
                                                        <th width="14%">Pekerjaan</th>
                                                        <th width="6%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="keluargaAnakTableBody">
                                                    @php
                                                        $keluargaAnakData =
                                                            $applicant && $applicant->keluarga_anak
                                                                ? $applicant->keluarga_anak
                                                                : [];
                                                        $keluargaAnakCount =
                                                            count($keluargaAnakData) > 0 ? count($keluargaAnakData) : 1;
                                                    @endphp
                                                    @for ($i = 1; $i <= $keluargaAnakCount; $i++)
                                                        @php
                                                            $keluargaAnakItem = isset($keluargaAnakData[$i - 1])
                                                                ? $keluargaAnakData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][nama]"
                                                                    value="{{ $keluargaAnakItem && isset($keluargaAnakItem['nama']) ? $keluargaAnakItem['nama'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][tempat_lahir]"
                                                                    value="{{ $keluargaAnakItem && isset($keluargaAnakItem['tempat_lahir']) ? $keluargaAnakItem['tempat_lahir'] : '' }}">
                                                            </td>
                                                            <td><input type="date" class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][tanggal_lahir]"
                                                                    value="{{ $keluargaAnakItem && isset($keluargaAnakItem['tanggal_lahir']) ? $keluargaAnakItem['tanggal_lahir'] : '' }}">
                                                            </td>
                                                            <td>
                                                                <select class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][jenis_kelamin]">
                                                                    <option value="">-</option>
                                                                    <option value="L"
                                                                        {{ $keluargaAnakItem && isset($keluargaAnakItem['jenis_kelamin']) && $keluargaAnakItem['jenis_kelamin'] == 'L' ? 'selected' : '' }}>
                                                                        L</option>
                                                                    <option value="P"
                                                                        {{ $keluargaAnakItem && isset($keluargaAnakItem['jenis_kelamin']) && $keluargaAnakItem['jenis_kelamin'] == 'P' ? 'selected' : '' }}>
                                                                        P</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][pendidikan]"
                                                                    value="{{ $keluargaAnakItem && isset($keluargaAnakItem['pendidikan']) ? $keluargaAnakItem['pendidikan'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_anak[{{ $i }}][pekerjaan]"
                                                                    value="{{ $keluargaAnakItem && isset($keluargaAnakItem['pekerjaan']) ? $keluargaAnakItem['pekerjaan'] : '' }}">
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($i > 1 || count($keluargaAnakData) > 1)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="removeKeluargaAnakRow(this)">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>

                                        <h5 class="section-title mt-4">Data Keluarga (Orang tua & Saudara Kandung)</h5>
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="addKeluargaOrtuRow()">
                                                <i class="mdi mdi-plus"></i> Tambah Baris
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="keluargaOrtuTable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">NO.</th>
                                                        <th width="20%">Nama</th>
                                                        <th width="15%">Tempat Lahir</th>
                                                        <th width="15%">Tanggal Lahir</th>
                                                        <th width="10%">Status</th>
                                                        <th width="15%">Pendidikan</th>
                                                        <th width="14%">Pekerjaan</th>
                                                        <th width="6%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="keluargaOrtuTableBody">
                                                    @php
                                                        $keluargaOrtuData =
                                                            $applicant && $applicant->keluarga_ortu
                                                                ? $applicant->keluarga_ortu
                                                                : [];
                                                        $keluargaOrtuCount =
                                                            count($keluargaOrtuData) > 0 ? count($keluargaOrtuData) : 1;
                                                    @endphp
                                                    @for ($i = 1; $i <= $keluargaOrtuCount; $i++)
                                                        @php
                                                            $keluargaOrtuItem = isset($keluargaOrtuData[$i - 1])
                                                                ? $keluargaOrtuData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][nama]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['nama']) ? $keluargaOrtuItem['nama'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][tempat_lahir]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['tempat_lahir']) ? $keluargaOrtuItem['tempat_lahir'] : '' }}">
                                                            </td>
                                                            <td><input type="date" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][tanggal_lahir]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['tanggal_lahir']) ? $keluargaOrtuItem['tanggal_lahir'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][status]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['status']) ? $keluargaOrtuItem['status'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][pendidikan]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['pendidikan']) ? $keluargaOrtuItem['pendidikan'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="keluarga_ortu[{{ $i }}][pekerjaan]"
                                                                    value="{{ $keluargaOrtuItem && isset($keluargaOrtuItem['pekerjaan']) ? $keluargaOrtuItem['pekerjaan'] : '' }}">
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($i > 1 || count($keluargaOrtuData) > 1)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="removeKeluargaOrtuRow(this)">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 7: Kemampuan & Lainnya -->
                                <div class="tab-pane fade" id="kemampuan" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">7. Kemampuan Bahasa</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th width="20%">Bahasa</th>
                                                                <th width="20%">Pasif</th>
                                                                <th width="20%">Aktif</th>
                                                                <th width="20%">Baca</th>
                                                                <th width="20%">Tulis</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $bahasaData =
                                                                    $applicant && $applicant->bahasa
                                                                        ? $applicant->bahasa
                                                                        : [];
                                                            @endphp
                                                            <tr>
                                                                <td><strong>a. Indonesia</strong></td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_indonesia_pasif"
                                                                        name="bahasa[indonesia][pasif]" value="1"
                                                                        {{ isset($bahasaData['indonesia']['pasif']) && $bahasaData['indonesia']['pasif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_indonesia_pasif">Pasif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_indonesia_aktif"
                                                                        name="bahasa[indonesia][aktif]" value="1"
                                                                        {{ isset($bahasaData['indonesia']['aktif']) && $bahasaData['indonesia']['aktif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_indonesia_aktif">Aktif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_indonesia_baca"
                                                                        name="bahasa[indonesia][baca]" value="1"
                                                                        {{ isset($bahasaData['indonesia']['baca']) && $bahasaData['indonesia']['baca'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_indonesia_baca">Baca</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_indonesia_tulis"
                                                                        name="bahasa[indonesia][tulis]" value="1"
                                                                        {{ isset($bahasaData['indonesia']['tulis']) && $bahasaData['indonesia']['tulis'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_indonesia_tulis">Tulis</label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>b. Inggris</strong></td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_inggris_pasif"
                                                                        name="bahasa[inggris][pasif]" value="1"
                                                                        {{ isset($bahasaData['inggris']['pasif']) && $bahasaData['inggris']['pasif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_inggris_pasif">Pasif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_inggris_aktif"
                                                                        name="bahasa[inggris][aktif]" value="1"
                                                                        {{ isset($bahasaData['inggris']['aktif']) && $bahasaData['inggris']['aktif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_inggris_aktif">Aktif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_inggris_baca"
                                                                        name="bahasa[inggris][baca]" value="1"
                                                                        {{ isset($bahasaData['inggris']['baca']) && $bahasaData['inggris']['baca'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_inggris_baca">Baca</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_inggris_tulis"
                                                                        name="bahasa[inggris][tulis]" value="1"
                                                                        {{ isset($bahasaData['inggris']['tulis']) && $bahasaData['inggris']['tulis'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_inggris_tulis">Tulis</label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>c. Mandarin</strong></td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_mandarin_pasif"
                                                                        name="bahasa[mandarin][pasif]" value="1"
                                                                        {{ isset($bahasaData['mandarin']['pasif']) && $bahasaData['mandarin']['pasif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_mandarin_pasif">Pasif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_mandarin_aktif"
                                                                        name="bahasa[mandarin][aktif]" value="1"
                                                                        {{ isset($bahasaData['mandarin']['aktif']) && $bahasaData['mandarin']['aktif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_mandarin_aktif">Aktif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_mandarin_baca"
                                                                        name="bahasa[mandarin][baca]" value="1"
                                                                        {{ isset($bahasaData['mandarin']['baca']) && $bahasaData['mandarin']['baca'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_mandarin_baca">Baca</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_mandarin_tulis"
                                                                        name="bahasa[mandarin][tulis]" value="1"
                                                                        {{ isset($bahasaData['mandarin']['tulis']) && $bahasaData['mandarin']['tulis'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_mandarin_tulis">Tulis</label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>d. Lain-lain</strong></td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_lain_pasif"
                                                                        name="bahasa[lain][pasif]" value="1"
                                                                        {{ isset($bahasaData['lain']['pasif']) && $bahasaData['lain']['pasif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_lain_pasif">Pasif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_lain_aktif"
                                                                        name="bahasa[lain][aktif]" value="1"
                                                                        {{ isset($bahasaData['lain']['aktif']) && $bahasaData['lain']['aktif'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_lain_aktif">Aktif</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_lain_baca"
                                                                        name="bahasa[lain][baca]" value="1"
                                                                        {{ isset($bahasaData['lain']['baca']) && $bahasaData['lain']['baca'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_lain_baca">Baca</label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" id="bahasa_lain_tulis"
                                                                        name="bahasa[lain][tulis]" value="1"
                                                                        {{ isset($bahasaData['lain']['tulis']) && $bahasaData['lain']['tulis'] ? 'checked' : '' }}>
                                                                    <label for="bahasa_lain_tulis">Tulis</label>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="section-title mt-4">Data Pendukung Lain</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Anda memiliki Mobil?</label>
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <input type="radio" name="punya_mobil" value="ya"
                                                                id="mobil_ya"
                                                                {{ $applicant && $applicant->punya_mobil == 'ya' ? 'checked' : '' }}>
                                                            <label for="mobil_ya">Ya</label>
                                                        </div>
                                                        <div class="checkbox-item">
                                                            <input type="radio" name="punya_mobil" value="tidak"
                                                                id="mobil_tidak"
                                                                {{ $applicant && $applicant->punya_mobil == 'tidak' ? 'checked' : '' }}>
                                                            <label for="mobil_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Anda memiliki Sepeda Motor?</label>
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <input type="radio" name="punya_motor" value="ya"
                                                                id="motor_ya"
                                                                {{ $applicant && $applicant->punya_motor == 'ya' ? 'checked' : '' }}>
                                                            <label for="motor_ya">Ya</label>
                                                        </div>
                                                        <div class="checkbox-item">
                                                            <input type="radio" name="punya_motor" value="tidak"
                                                                id="motor_tidak"
                                                                {{ $applicant && $applicant->punya_motor == 'tidak' ? 'checked' : '' }}>
                                                            <label for="motor_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>SIM yang dimiliki?</label>
                                                    <div class="checkbox-group">
                                                        @php
                                                            $simData =
                                                                $applicant && $applicant->sim
                                                                    ? (is_array($applicant->sim)
                                                                        ? $applicant->sim
                                                                        : [$applicant->sim])
                                                                    : [];
                                                        @endphp
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="sim[]" value="C"
                                                                id="sim_c"
                                                                {{ in_array('C', $simData) ? 'checked' : '' }}>
                                                            <label for="sim_c">C</label>
                                                        </div>
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="sim[]" value="A"
                                                                id="sim_a"
                                                                {{ in_array('A', $simData) ? 'checked' : '' }}>
                                                            <label for="sim_a">A</label>
                                                        </div>
                                                        <div class="checkbox-item">
                                                            <input type="checkbox" name="sim[]" value="B1"
                                                                id="sim_b1"
                                                                {{ in_array('B1', $simData) ? 'checked' : '' }}>
                                                            <label for="sim_b1">B1/Umum</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="section-title mt-4">Apakah anda bersedia untuk:</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <label>a. Kerja lembur?</label>
                                                            <input type="radio" name="kerja_lembur" value="ya"
                                                                id="lembur_ya"
                                                                {{ $applicant && $applicant->kerja_lembur == 'ya' ? 'checked' : '' }}>
                                                            <label for="lembur_ya">Ya</label>
                                                            <input type="radio" name="kerja_lembur" value="tidak"
                                                                id="lembur_tidak"
                                                                {{ $applicant && $applicant->kerja_lembur == 'tidak' ? 'checked' : '' }}>
                                                            <label for="lembur_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <label>b. Kerja Shift?</label>
                                                            <input type="radio" name="kerja_shift" value="ya"
                                                                id="shift_ya"
                                                                {{ $applicant && $applicant->kerja_shift == 'ya' ? 'checked' : '' }}>
                                                            <label for="shift_ya">Ya</label>
                                                            <input type="radio" name="kerja_shift" value="tidak"
                                                                id="shift_tidak"
                                                                {{ $applicant && $applicant->kerja_shift == 'tidak' ? 'checked' : '' }}>
                                                            <label for="shift_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <label>c. Kerja luar kota?</label>
                                                            <input type="radio" name="kerja_luar_kota"
                                                                value="ya" id="luar_kota_ya"
                                                                {{ $applicant && $applicant->kerja_luar_kota == 'ya' ? 'checked' : '' }}>
                                                            <label for="luar_kota_ya">Ya</label>
                                                            <input type="radio" name="kerja_luar_kota"
                                                                value="tidak" id="luar_kota_tidak"
                                                                {{ $applicant && $applicant->kerja_luar_kota == 'tidak' ? 'checked' : '' }}>
                                                            <label for="luar_kota_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <label>d. Test Psiko?</label>
                                                            <input type="radio" name="test_psiko" value="ya"
                                                                id="psiko_ya"
                                                                {{ $applicant && $applicant->test_psiko == 'ya' ? 'checked' : '' }}>
                                                            <label for="psiko_ya">Ya</label>
                                                            <input type="radio" name="test_psiko" value="tidak"
                                                                id="psiko_tidak"
                                                                {{ $applicant && $applicant->test_psiko == 'tidak' ? 'checked' : '' }}>
                                                            <label for="psiko_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox-group">
                                                        <div class="checkbox-item">
                                                            <label>e. Test Kesehatan?</label>
                                                            <input type="radio" name="test_kesehatan"
                                                                value="ya" id="kesehatan_ya"
                                                                {{ $applicant && $applicant->test_kesehatan == 'ya' ? 'checked' : '' }}>
                                                            <label for="kesehatan_ya">Ya</label>
                                                            <input type="radio" name="test_kesehatan"
                                                                value="tidak" id="kesehatan_tidak"
                                                                {{ $applicant && $applicant->test_kesehatan == 'tidak' ? 'checked' : '' }}>
                                                            <label for="kesehatan_tidak">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hobby">Hobby</label>
                                                    <input type="text" class="form-control" id="hobby"
                                                        name="hobby"
                                                        value="{{ old('hobby', $applicant ? $applicant->hobby : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lain_lain">Lain-lain</label>
                                                    <input type="text" class="form-control" id="lain_lain"
                                                        name="lain_lain"
                                                        value="{{ old('lain_lain', $applicant ? $applicant->lain_lain : '') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="section-title mt-4">Referensi</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No.</th>
                                                        <th width="25%">Nama</th>
                                                        <th width="30%">Alamat</th>
                                                        <th width="20%">No. Telp</th>
                                                        <th width="20%">Jabatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $referensiData =
                                                            $applicant && $applicant->referensi
                                                                ? $applicant->referensi
                                                                : [];
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $referensiItem = isset($referensiData[$i - 1])
                                                                ? $referensiData[$i - 1]
                                                                : null;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $i }}</td>
                                                            <td><input type="text" class="form-control"
                                                                    name="referensi[{{ $i }}][nama]"
                                                                    value="{{ $referensiItem && isset($referensiItem['nama']) ? $referensiItem['nama'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="referensi[{{ $i }}][alamat]"
                                                                    value="{{ $referensiItem && isset($referensiItem['alamat']) ? $referensiItem['alamat'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="referensi[{{ $i }}][no_telp]"
                                                                    value="{{ $referensiItem && isset($referensiItem['no_telp']) ? $referensiItem['no_telp'] : '' }}">
                                                            </td>
                                                            <td><input type="text" class="form-control"
                                                                    name="referensi[{{ $i }}][jabatan]"
                                                                    value="{{ $referensiItem && isset($referensiItem['jabatan']) ? $referensiItem['jabatan'] : '' }}">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-group mt-4">
                                            <div class="alert alert-info">
                                                <strong>Deklarasi:</strong><br>
                                                Dengan ini saya menyatakan bahwa, keterangan yang saya berikan diatas
                                                adalah benar.
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_deklarasi">Surabaya, Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal_deklarasi"
                                                        name="tanggal_deklarasi"
                                                        value="{{ old('tanggal_deklarasi', $applicant && $applicant->tanggal_deklarasi ? $applicant->tanggal_deklarasi->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ttd_pelamar">Tanda Tangan Pelamar</label>
                                                    <input type="text" class="form-control" id="ttd_pelamar"
                                                        name="ttd_pelamar" placeholder="Nama Terang & Tanda Tangan"
                                                        value="{{ old('ttd_pelamar', $applicant ? $applicant->ttd_pelamar : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Actions - akan ditampilkan di setiap tab -->
                            <div class="row mt-4" id="tabActions" style="display: none;">
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button type="button" class="btn btn-primary" onclick="saveCurrentTab()">
                                            <i class="mdi mdi-check"></i> <span id="saveButtonText">Save & Next</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test List Card - Tampil setelah tanda tangan pelamar terisi -->
        <div class="row">
            <div class="col-12">
                <div id="testListCard" class="card"
                    style="display: {{ $applicant && $applicant->ttd_pelamar ? 'block' : 'none' }};">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-clipboard-text"></i> Daftar Test
                        </h4>
                        <p class="card-subtitle">Daftar test yang harus dikerjakan oleh pelamar</p>
                    </div>
                    <div class="card-body">
                        <!-- Test Progress Bar -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Progress Test</small>
                                    <small class="text-muted" id="testProgressPercentage">0%</small>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar" id="testProgressBar" style="width: 0%;" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <span id="testProgressText" class="font-weight-bold">0 dari 2 test
                                            selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="testListContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat daftar test...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('sipo_krisan/public/news/plugins/jquery/jquery.min.js') }}"></script>
            <script src="{{ asset('sipo_krisan/public/news/plugins/jquery/jquery.min.js') }}"></script>

            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- Toastr for notifications -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

            <!-- CSRF Token Setup for AJAX -->
            <script>
                // Setup CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Ensure jQuery is available globally
                window.$ = window.jQuery = jQuery;

                // Prevent Bootstrap 5 conflicts with Bootstrap 4
                if (typeof bootstrap !== 'undefined') {
                    // If Bootstrap 5 is loaded, store it separately
                    window.bootstrap5 = bootstrap;
                    // Remove global bootstrap to prevent conflicts
                    delete window.bootstrap;
                }

                // Toastr configuration
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
            </script>


            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

            <!-- SweetAlert2 JS -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

            <script>
                // Hide preloader when page is fully loaded
                window.addEventListener('load', function() {
                    const preloader = document.querySelector('.preloader');
                    if (preloader) {
                        preloader.style.opacity = '0';
                        setTimeout(function() {
                            preloader.style.display = 'none';
                        }, 500);
                    }
                });

                // Auto-fill employee data from URL parameter
                document.addEventListener('DOMContentLoaded', function() {
                    // Get employee ID from URL parameter
                    const urlParams = new URLSearchParams(window.location.search);
                    const employeeId = urlParams.get('id');

                    if (employeeId) {
                        // Fetch employee data and auto-fill form
                        fetchEmployeeData(employeeId);
                    }

                    // Add event listeners for date calculation
                    const startDateInput = document.getElementById('start-date');
                    const endDateInput = document.getElementById('end-date');
                    const durationDisplay = document.getElementById('duration-display');

                    if (startDateInput && endDateInput && durationDisplay) {
                        startDateInput.addEventListener('change', calculateDuration);
                        endDateInput.addEventListener('change', calculateDuration);
                    }
                });

                function fetchEmployeeData(employeeId) {
                    // Fetch employee data from server
                    fetch(`/sipo/hr/leave-request/employee-data/${employeeId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Auto-fill form fields
                                autoFillForm(data.employee);
                            } else {
                                console.error('Employee not found:', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching employee data:', error);
                        });
                }

                function autoFillForm(employee) {
                    // Auto-fill contact fields with employee data
                    const contactField = document.querySelector('input[name="contact_during_leave"]');
                    const emergencyField = document.querySelector('input[name="emergency_contact"]');

                    if (contactField && employee.no_hp) {
                        contactField.value = employee.no_hp;
                    }

                    if (emergencyField && employee.no_hp) {
                        emergencyField.value = employee.no_hp;
                    }

                    // Show employee info in a notification
                    if (employee.nama_lengkap) {
                        showEmployeeInfo(employee);
                    }
                }

                function showEmployeeInfo(employee) {
                    // Show employee info notification
                    const notification = document.createElement('div');
                    notification.className = 'alert alert-info alert-dismissible fade show';
                    notification.innerHTML = `
                <strong>Data Karyawan Terdeteksi:</strong> ${employee.nama_lengkap} (${employee.posisi || 'N/A'})
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

                    // Insert at the top of the form
                    const form = document.querySelector('form');
                    if (form) {
                        form.parentNode.insertBefore(notification, form);
                    }
                }

                function calculateDuration() {
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;
                    const durationDisplay = document.getElementById('duration-display');

                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        const end = new Date(endDate);

                        if (end >= start) {
                            const timeDiff = end.getTime() - start.getTime();
                            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end days
                            durationDisplay.value = `${daysDiff} hari`;
                        } else {
                            durationDisplay.value = 'Tanggal selesai harus setelah tanggal mulai';
                        }
                    } else {
                        durationDisplay.value = '';
                    }
                }

                function showLeaveForm(leaveType) {
                    const leaveTypeNames = {
                        'annual': 'Cuti Tahunan (C)',
                        'dinas': 'Izin Dinas (DIN)',
                        'special': 'Cuti Khusus (P1)',
                        'sick': 'Cuti Sakit (S/SKD)',
                        'maternity': 'Cuti Hamil (H2)',
                        'menstrual': 'Cuti Haid (H1)',
                        'info': 'Ijin Dengan Informasi (I)',
                        'temp_sick': 'Ijin Sementara Sakit (IS)',
                        'temp_personal': 'Ijin Sementara Pribadi (ISP)',
                        'late': 'Lambat Datang (LD)',
                        'early': 'Pulang Awal (PA)',
                        'absent': 'Mangkir (M)'
                    };

                    // Set leave type
                    document.getElementById('selected-leave-type').value = leaveType;
                    document.getElementById('leave-type-display').textContent = leaveTypeNames[leaveType];

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('leaveFormModal'));
                    modal.show();
                }

                function submitLeaveForm() {
                    // Validate form
                    const form = document.getElementById('leave-request-form');
                    if (form.checkValidity()) {
                        form.submit();
                    } else {
                        form.reportValidity();
                    }
                }
            </script>
            <script>
                $(document).ready(function() {
                    // Initialize Bootstrap tabs - support both Bootstrap 4 and 5
                    $('#applicantTabs button[data-toggle="tab"], #applicantTabs button[data-bs-toggle="tab"]').on('click', function(e) {
                        e.preventDefault()
                        var target = $(this).data('target') || $(this).data('bs-target')

                        // Try Bootstrap 4 first
                        if ($(this).data('target')) {
                            $(this).tab('show')
                        } else {
                            // Bootstrap 5
                            var tabElement = document.querySelector(target)
                            if (tabElement && typeof window.bootstrap5 !== 'undefined') {
                                var tab = new window.bootstrap5.Tab(tabElement)
                                tab.show()
                            } else {
                                $(this).tab('show')
                            }
                        }

                        updateCurrentTab(target)
                    })

                    // Show tab actions on first load
                    $('#tabActions').show()

                    // If applicant exists, start from data-diri tab
                    @if ($applicant)
                        updateCurrentTab('#data-diri')
                        // Show test list only if tanda tangan pelamar is filled
                        var applicantId = $('#applicantId').val()
                        var ttdPelamar = $('#ttd_pelamar').val()
                        if (applicantId && ttdPelamar && ttdPelamar.trim() !== '') {
                            loadTestList(applicantId)
                        }
                        // Mark saved tabs after a short delay to ensure DOM is ready
                        setTimeout(function() {
                            checkAndMarkSavedTabs()
                            updateProgressBar()
                        }, 500)
                    @else
                        updateCurrentTab('#posisi')
                        // Initial progress bar update for new form
                        updateProgressBar()
                    @endif

                    // SweetAlert untuk success message
                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end'
                        });
                    @endif

                    // SweetAlert untuk error message
                    @if (session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '{{ session('error') }}',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    @endif

                    // Form validation
                    $('#applicantForm').on('submit', function(e) {
                        var isValid = true;
                        var firstErrorTab = null;

                        // Check required fields in each tab
                        $('#applicantTabContent .tab-pane').each(function() {
                            var tabId = $(this).attr('id');
                            var requiredFields = $(this).find(
                                'input[required], select[required], textarea[required]');

                            requiredFields.each(function() {
                                if (!$(this).val()) {
                                    isValid = false;
                                    if (!firstErrorTab) {
                                        firstErrorTab = tabId;
                                    }
                                    $(this).addClass('is-invalid');
                                } else {
                                    $(this).removeClass('is-invalid');
                                }
                            });
                        });

                        if (!isValid) {
                            e.preventDefault();

                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Form Belum Lengkap!',
                                text: 'Silakan lengkapi semua field yang wajib diisi.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });

                            // Switch to first tab with error
                            if (firstErrorTab) {
                                var tabButton = $('button[data-target="#' + firstErrorTab + '"]');
                                tabButton.tab('show');
                            }
                        }
                    });
                });

                // Tab management functions
                function updateCurrentTab(tabId) {
                    $('#currentTab').val(tabId.replace('#', ''))
                    updateSaveButtonText()
                }

                function updateSaveButtonText() {
                    var currentTab = $('#currentTab').val()
                    var isLastTab = currentTab === 'kemampuan'
                    var ttdPelamar = $('#ttd_pelamar').val()
                    var hasTtd = ttdPelamar && ttdPelamar.trim() !== ''

                    if (hasTtd) {
                        // Jika sudah ada tanda tangan, ubah menjadi UPDATE
                        $('#saveButtonText').text(isLastTab ? 'UPDATE' : 'UPDATE')
                    } else {
                        // Jika belum ada tanda tangan, gunakan teks default
                        $('#saveButtonText').text(isLastTab ? 'Simpan & Selesai' : 'Save & Next')
                    }
                }

                function saveCurrentTab() {
                    var currentTab = $('#currentTab').val()
                    var isValid = validateCurrentTab(currentTab)

                    if (!isValid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Form Belum Lengkap!',
                            text: 'Silakan lengkapi semua field yang wajib diisi di tab ini.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        })
                        return
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    })

                    // Prepare form data
                    var formData = new FormData()
                    var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    console.log('CSRF Token:', csrfToken)

                    // Get applicant_id from hidden field or localStorage
                    var applicantId = $('#applicantId').val() || localStorage.getItem('applicant_id')
                    if (applicantId) {
                        $('#applicantId').val(applicantId)
                    }

                    formData.append('_token', csrfToken)
                    formData.append('current_tab', currentTab)
                    formData.append('saved_tabs', $('#savedTabs').val())
                    formData.append('applicant_id', applicantId || '')

                    // Collect data from current tab
                    collectTabData(currentTab, formData)

                    // Send AJAX request
                    var storeUrl = '{{ route('public.staff-applicant.store') }}';
                    console.log('Sending AJAX request to:', storeUrl);
                    console.log('Form data:', formData);

                    $.ajax({
                        url: storeUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Success response:', response);
                            Swal.close()

                            if (response.success) {
                                // Update saved tabs
                                var savedTabs = $('#savedTabs').val()
                                if (savedTabs) {
                                    savedTabs += ',' + currentTab
                                } else {
                                    savedTabs = currentTab
                                }
                                $('#savedTabs').val(savedTabs)

                                // Update applicant ID if this is first save
                                if (response.applicant_id) {
                                    $('#applicantId').val(response.applicant_id)
                                    // Save applicant_id to localStorage to persist across page refreshes
                                    localStorage.setItem('applicant_id', response.applicant_id)
                                    localStorage.setItem('applicant_saved_at', new Date().toISOString())

                                    // Update URL with applicant ID without reloading
                                    var currentUrl = window.location.href
                                    var newUrl = currentUrl.split('?')[0] // Remove existing query params
                                    if (newUrl.indexOf('#') > -1) {
                                        newUrl = newUrl.split('#')[0] // Remove existing hash
                                    }
                                    newUrl += '?id=' + response.applicant_id
                                    window.history.pushState({
                                        applicant_id: response.applicant_id
                                    }, '', newUrl)
                                    console.log('URL updated with applicant_id:', newUrl)
                                }

                                // Mark tab as saved
                                markTabAsSaved(currentTab)

                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data tab berhasil disimpan',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Move to next tab or finish
                                    if (currentTab === 'kemampuan') {
                                        // Last tab - reload page with applicant_id in URL
                                        var applicantId = response.applicant_id || $('#applicantId').val() ||
                                            localStorage.getItem('applicant_id')

                                        if (applicantId) {
                                            // Reload page with ID in URL
                                            window.location.href =
                                                '{{ route('public.staff-applicant.create') }}?id=' + applicantId
                                        } else {
                                            // Just reload the page
                                            window.location.reload()
                                        }
                                    } else {
                                        // If kemampuan tab is saved and tanda tangan is filled, show test list
                                        if (currentTab === 'kemampuan' && response.applicant_id) {
                                            var ttdPelamar = $('#ttd_pelamar').val()
                                            if (ttdPelamar && ttdPelamar.trim() !== '') {
                                                // Show test list without reloading
                                                loadTestList(response.applicant_id)
                                            }
                                        }

                                        // Move to next tab (jika bukan kemampuan, atau setelah show test list)
                                        if (currentTab !== 'kemampuan') {
                                            moveToNextTab(currentTab)
                                        }
                                    }
                                })
                            }
                        },
                        error: function(xhr) {
                            console.log('Error response:', xhr);
                            console.log('Error status:', xhr.status);
                            console.log('Error responseText:', xhr.responseText);

                            Swal.close()
                            var errorMessage = 'Terjadi kesalahan saat menyimpan data'

                            // Handle CSRF token expired (419)
                            if (xhr.status === 419) {
                                // Get saved applicant_id from localStorage
                                var savedApplicantId = localStorage.getItem('applicant_id')
                                if (savedApplicantId) {
                                    $('#applicantId').val(savedApplicantId)
                                }

                                // Reload CSRF token and retry
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: 'Token CSRF telah kadaluarsa. Memperbarui token dan mencoba lagi...',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                })

                                // Reload page to get new CSRF token, but preserve applicant_id
                                setTimeout(function() {
                                    // Store current tab before reload
                                    localStorage.setItem('current_tab_before_reload', currentTab)
                                    window.location.reload()
                                }, 1000)
                                return
                            }

                            // Handle 404 - Applicant not found (clear invalid applicant_id from localStorage)
                            if (xhr.status === 404 && xhr.responseJSON && xhr.responseJSON.message &&
                                xhr.responseJSON.message.includes('tidak ditemukan')) {
                                // Clear invalid applicant_id from localStorage and hidden field
                                localStorage.removeItem('applicant_id')
                                localStorage.removeItem('applicant_saved_at')
                                $('#applicantId').val('')
                                console.log('Cleared invalid applicant_id from localStorage')
                            }

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                var errorList = [];
                                for (var field in errors) {
                                    errorList.push(field + ': ' + errors[field][0]);
                                }
                                errorMessage = 'Validasi gagal:\n' + errorList.join('\n');
                            } else if (xhr.status === 422) {
                                errorMessage = 'Data tidak valid. Silakan periksa kembali input Anda.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonText: 'OK'
                            })
                        }
                    })
                }

                function validateCurrentTab(tabId) {
                    var requiredFields = $('#' + tabId + ' input[required], #' + tabId + ' select[required], #' + tabId +
                        ' textarea[required]')
                    var isValid = true

                    requiredFields.each(function() {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid')
                            isValid = false
                        } else {
                            $(this).removeClass('is-invalid')
                        }
                    })

                    return isValid
                }

                function collectTabData(tabId, formData) {
                    // Collect all form data from current tab
                    $('#' + tabId + ' input, #' + tabId + ' select, #' + tabId + ' textarea').each(function() {
                        if ($(this).attr('name')) {
                            if ($(this).attr('type') === 'file') {
                                if (this.files.length > 0) {
                                    formData.append($(this).attr('name'), this.files[0])
                                }
                            } else if ($(this).attr('type') === 'checkbox') {
                                if ($(this).is(':checked')) {
                                    formData.append($(this).attr('name'), $(this).val())
                                }
                            } else if ($(this).attr('type') === 'radio') {
                                if ($(this).is(':checked')) {
                                    formData.append($(this).attr('name'), $(this).val())
                                }
                            } else {
                                formData.append($(this).attr('name'), $(this).val())
                            }
                        }
                    })
                }

                function markTabAsSaved(tabId) {
                    // Remove existing check icon if any (support both Bootstrap 4 and 5)
                    var tabButton = $('button[data-target="#' + tabId + '"], button[data-bs-target="#' + tabId + '"]')
                    tabButton.find('.mdi-check').remove()

                    // Add visual indicator that tab is saved
                    tabButton.addClass('tab-saved')
                    tabButton.append(' <i class="mdi mdi-check text-success"></i>')

                    // Update progress bar
                    updateProgressBar()
                }

                function updateProgressBar() {
                    var tabs = ['posisi', 'data-diri', 'pendidikan', 'kursus', 'pengalaman', 'keluarga', 'kemampuan']
                    var totalTabs = tabs.length
                    var savedTabs = 0

                    // Count saved tabs (tabs with check icon or tab-saved class)
                    tabs.forEach(function(tabId) {
                        // Support both Bootstrap 4 and 5 syntax
                        var tabButton = $('button[data-target="#' + tabId + '"], button[data-bs-target="#' + tabId + '"]')
                        if (tabButton.hasClass('tab-saved') || tabButton.find('.mdi-check').length > 0) {
                            savedTabs++
                        }
                    })

                    // Calculate percentage
                    var percentage = Math.round((savedTabs / totalTabs) * 100)

                    // Update progress bar
                    $('#formProgressBar').css('width', percentage + '%')
                    $('#formProgressBar').attr('aria-valuenow', percentage)
                    $('#progressPercentage').text(percentage + '%')
                    $('#progressText').text(savedTabs + ' dari ' + totalTabs + ' tab tersimpan')

                    // Update progress bar color based on progress
                    $('#formProgressBar').removeClass('bg-danger bg-warning bg-info bg-success')
                    if (percentage < 30) {
                        $('#formProgressBar').addClass('bg-danger')
                    } else if (percentage < 60) {
                        $('#formProgressBar').addClass('bg-warning')
                    } else if (percentage < 100) {
                        $('#formProgressBar').addClass('bg-info')
                    } else {
                        $('#formProgressBar').addClass('bg-success')
                    }
                }

                function checkAndMarkSavedTabs() {
                    var applicantId = $('#applicantId').val()
                    if (!applicantId) return

                    // Check each tab for saved data
                    var tabs = ['posisi', 'data-diri', 'pendidikan', 'kursus', 'pengalaman', 'keluarga', 'kemampuan']

                    tabs.forEach(function(tabId) {
                        var hasData = false

                        switch (tabId) {
                            case 'posisi':
                                // Check if posisi tab has data
                                hasData = $('#posisi_dilamar').val() && $('#posisi_dilamar').val().trim() !== ''
                                break
                            case 'data-diri':
                                // Check if data-diri tab has required data
                                hasData = $('#nama_lengkap').val() && $('#nama_lengkap').val().trim() !== '' &&
                                    $('#jenis_kelamin').val() && $('#jenis_kelamin').val() !== '' &&
                                    $('#tanggal_lahir').val() && $('#tanggal_lahir').val() !== '' &&
                                    $('#no_handphone').val() && $('#no_handphone').val().trim() !== '' &&
                                    $('#email').val() && $('#email').val().trim() !== ''
                                break
                            case 'pendidikan':
                                // Check if pendidikan tab has any data
                                $('#pendidikan select[name*="[tingkat]"], #pendidikan input[name*="[nama_sekolah]"]').each(
                                    function() {
                                        if ($(this).val() && $(this).val().trim() !== '') {
                                            hasData = true
                                            return false // break loop
                                        }
                                    })
                                break
                            case 'kursus':
                                // Check if kursus tab has any data
                                $('#kursus input[name*="[jenis]"], #kursus input[name*="[nama_lembaga]"]').each(function() {
                                    if ($(this).val() && $(this).val().trim() !== '') {
                                        hasData = true
                                        return false // break loop
                                    }
                                })
                                break
                            case 'pengalaman':
                                // Check if pengalaman tab has any data
                                $('#pengalaman input[name*="[nama_perusahaan]"]').each(function() {
                                    if ($(this).val() && $(this).val().trim() !== '') {
                                        hasData = true
                                        return false // break loop
                                    }
                                })
                                break
                            case 'keluarga':
                                // Check if keluarga tab has any data
                                $('#keluarga input[name*="[nama]"]').each(function() {
                                    if ($(this).val() && $(this).val().trim() !== '') {
                                        hasData = true
                                        return false // break loop
                                    }
                                })
                                break
                            case 'kemampuan':
                                // Check if kemampuan tab has any data (checkboxes, radio, or text fields)
                                if ($('#kemampuan input[type="checkbox"]:checked').length > 0 ||
                                    $('#kemampuan input[type="radio"]:checked').length > 0 ||
                                    $('#kemampuan input[type="text"]').filter(function() {
                                        return $(this).val() && $(this).val().trim() !== ''
                                    }).length > 0) {
                                    hasData = true
                                }
                                break
                        }

                        if (hasData) {
                            markTabAsSaved(tabId)
                        }
                    })
                }

                function moveToNextTab(currentTab) {
                    var tabs = ['posisi', 'data-diri', 'pendidikan', 'kursus', 'pengalaman', 'keluarga', 'kemampuan']
                    var currentIndex = tabs.indexOf(currentTab)

                    if (currentIndex < tabs.length - 1) {
                        var nextTab = tabs[currentIndex + 1]
                        var nextTabId = '#' + nextTab

                        // Find the button for next tab (support both Bootstrap 4 and 5)
                        var nextButton = $('button[data-target="' + nextTabId + '"], button[data-bs-target="' + nextTabId + '"]')

                        if (nextButton.length > 0) {
                            // Try Bootstrap 4 first
                            if (nextButton.data('target')) {
                                nextButton.tab('show')
                            } else {
                                // Bootstrap 5 - use native Bootstrap 5 API
                                var tabElement = document.querySelector(nextTabId)
                                if (tabElement) {
                                    // Use Bootstrap 5 if available (stored as bootstrap5)
                                    if (typeof window.bootstrap5 !== 'undefined' && window.bootstrap5.Tab) {
                                        var tab = new window.bootstrap5.Tab(tabElement)
                                        tab.show()
                                    } else {
                                        // Fallback to jQuery Bootstrap 4
                                        nextButton.tab('show')
                                    }
                                } else {
                                    // Fallback
                                    nextButton.tab('show')
                                }
                            }

                            // Update active states
                            $('#applicantTabs button').removeClass('active')
                            $('#applicantTabContent .tab-pane').removeClass('show active')
                            nextButton.addClass('active')
                            $(nextTabId).addClass('show active')

                            updateCurrentTab(nextTabId)
                        }
                    }
                }


                function loadTestList(applicantId) {
                    if (!applicantId) {
                        applicantId = $('#applicantId').val()
                    }

                    if (!applicantId) {
                        return
                    }

                    // Check if tanda tangan pelamar is filled
                    var ttdPelamar = $('#ttd_pelamar').val()
                    if (!ttdPelamar || ttdPelamar.trim() === '') {
                        // Don't show test list if tanda tangan is not filled
                        return
                    }

                    // Show test list card
                    $('#testListCard').show()
                    $('#applicantForm').hide()

                    // Create simple test list directly
                    createSimpleTestList(applicantId)
                }

                function createSimpleTestList(applicantId) {
                    // Only 2 tests for staff level: test_1 (Matematika) and test_3 (Buta Warna)
                    var testTypes = [{
                            type: 'test_1',
                            name: 'Tes Matematika'
                        },
                        {
                            type: 'test_3',
                            name: 'Tes Buta Warna'
                        }
                    ]

                    // Fetch test results from server (with cache busting to ensure fresh data)
                    var testResultsUrl = '{{ url('sipo/hr/staff-applicants') }}/' + applicantId + '/test-results-json?' + new Date().getTime()
                    $.ajax({
                        url: testResultsUrl,
                        method: 'GET',
                        dataType: 'json',
                        cache: false, // Prevent browser caching
                        success: function(response) {
                            // Parse JSON response to get test results
                            var testResults = {}
                            if (response && response.success && response.testResults) {
                                response.testResults.forEach(function(result) {
                                    testResults[result.test_type] = result
                                })
                            }

                            // Build test list table
                            var baseUrl = '{{ url('sipo/hr/staff-applicants') }}/' + applicantId + '/test'
                            var html = '<div class="table-responsive">' +
                                '<table class="table table-bordered table-striped">' +
                                '<thead>' +
                                '<tr>' +
                                '<th style="width: 50px;">No</th>' +
                                '<th>Test</th>' +
                                '<th style="width: 120px;">Status</th>' +
                                '<th style="width: 150px;">Aksi</th>' +
                                '</tr>' +
                                '</thead>' +
                                '<tbody>'

                            var completedTests = 0
                            testTypes.forEach(function(test, index) {
                                var testUrl = baseUrl + '/' + test.type
                                var testResult = testResults[test.type]
                                var statusHtml = ''
                                var buttonHtml = ''

                                if (testResult) {
                                    completedTests++
                                    statusHtml =
                                        '<span class="badge badge-success"><i class="mdi mdi-check"></i> Selesai</span>'
                                    if (testResult.score !== null && testResult.max_score !== null) {
                                        var scorePercentage = Math.round((testResult.score / testResult
                                            .max_score) * 100)
                                        statusHtml += '<br><small class="text-muted">Skor: ' + testResult
                                            .score + '/' + testResult.max_score + ' (' + scorePercentage +
                                            '%)</small>'
                                    }
                                    buttonHtml = '<a href="' + testUrl + '" class="btn btn-info btn-sm">' +
                                        '<i class="mdi mdi-refresh"></i> Ulang Test' +
                                        '</a>'
                                } else {
                                    statusHtml =
                                        '<span class="badge badge-secondary"><i class="mdi mdi-clock-outline"></i> Belum Dikerjakan</span>'
                                    buttonHtml = '<a href="' + testUrl + '" class="btn btn-primary btn-sm">' +
                                        '<i class="mdi mdi-play"></i> Mulai Test' +
                                        '</a>'
                                }

                                html += '<tr>' +
                                    '<td class="text-center">' + (index + 1) + '</td>' +
                                    '<td><strong>' + test.name + '</strong></td>' +
                                    '<td>' + statusHtml + '</td>' +
                                    '<td>' + buttonHtml + '</td>' +
                                    '</tr>'
                            })

                            html += '</tbody></table></div>'

                            // Add "Selesai Proses Tes" button
                            var applicantStatus = response.applicant_status || $('#applicantStatus').val() || ''
                            var isAllTestsCompleted = completedTests === testTypes.length
                            // Only hide button if status is already final (accepted/rejected)
                            var isFinalStatus = applicantStatus === 'accepted' || applicantStatus === 'rejected'

                            // Debug logging
                            console.log('Test completion check:', {
                                completedTests: completedTests,
                                totalTests: testTypes.length,
                                isAllCompleted: isAllTestsCompleted,
                                applicantStatus: applicantStatus,
                                isFinalStatus: isFinalStatus
                            })

                            // Always show button if all tests completed, regardless of status
                            // Also show button if status is not final (even if not all tests completed)
                            // Only hide button if status is final AND not all tests completed
                            var shouldShowButton = isAllTestsCompleted || !isFinalStatus

                            if (shouldShowButton) {
                                var buttonClass = isAllTestsCompleted ? 'btn-success' : 'btn-secondary'
                                var buttonDisabled = isAllTestsCompleted ? '' : 'disabled'
                                var buttonOnclick = isAllTestsCompleted ? 'onclick="finishTestProcess()"' : ''
                                var messageText = isAllTestsCompleted ?
                                    'Semua test telah selesai dikerjakan. Klik tombol di atas untuk menyelesaikan proses interview test.' :
                                    'Selesaikan semua test terlebih dahulu untuk dapat menyelesaikan proses interview test.'

                                html += '<div class="text-center mt-4 mb-3">' +
                                    '<button type="button" class="btn btn-lg ' + buttonClass +
                                    '" id="finishTestProcessBtn" ' + buttonDisabled + ' ' + buttonOnclick + '>' +
                                    '<i class="mdi mdi-check-circle"></i> Selesai Proses Tes' +
                                    '</button>' +
                                    '<p class="text-muted mt-2"><small>' + messageText + '</small></p>' +
                                    '</div>'
                            } else {
                                console.log('Button not shown because:', {
                                    isAllTestsCompleted: isAllTestsCompleted,
                                    isFinalStatus: isFinalStatus
                                })
                            }

                            $('#testListContent').html(html)

                            // Update test progress bar
                            updateTestProgressBar(completedTests, testTypes.length)
                        },
                        error: function() {
                            // Fallback: build test list without checking results
                            var baseUrl = '{{ url('sipo/hr/staff-applicants') }}'
                            var html = '<div class="table-responsive">' +
                                '<table class="table table-bordered table-striped">' +
                                '<thead>' +
                                '<tr>' +
                                '<th style="width: 50px;">No</th>' +
                                '<th>Test</th>' +
                                '<th style="width: 120px;">Status</th>' +
                                '<th style="width: 150px;">Aksi</th>' +
                                '</tr>' +
                                '</thead>' +
                                '<tbody>'

                            testTypes.forEach(function(test, index) {
                                var testUrl = baseUrl + '/' + applicantId + '/test/' + test.type
                                html += '<tr>' +
                                    '<td class="text-center">' + (index + 1) + '</td>' +
                                    '<td><strong>' + test.name + '</strong></td>' +
                                    '<td><span class="badge badge-secondary"><i class="mdi mdi-clock-outline"></i> Belum Dikerjakan</span></td>' +
                                    '<td>' +
                                    '<a href="' + testUrl + '" class="btn btn-primary btn-sm">' +
                                    '<i class="mdi mdi-play"></i> Mulai Test' +
                                    '</a>' +
                                    '</td>' +
                                    '</tr>'
                            })

                            html += '</tbody></table></div>'

                            // Add "Selesai Proses Tes" button - only show if status is not "interview"
                            var applicantStatus = $('#applicantStatus').val() || ''
                            var isTestProcessFinished = applicantStatus === 'interview'

                            // Only show button if test process is not finished yet
                            if (!isTestProcessFinished) {
                                var buttonClass = 'btn-secondary'
                                var buttonDisabled = 'disabled'
                                var buttonOnclick = ''
                                var messageText =
                                    'Selesaikan semua test terlebih dahulu untuk dapat menyelesaikan proses interview test.'

                                html += '<div class="text-center mt-4 mb-3">' +
                                    '<button type="button" class="btn btn-lg ' + buttonClass +
                                    '" id="finishTestProcessBtn" ' + buttonDisabled + ' ' + buttonOnclick + '>' +
                                    '<i class="mdi mdi-check-circle"></i> Selesai Proses Tes' +
                                    '</button>' +
                                    '<p class="text-muted mt-2"><small>' + messageText + '</small></p>' +
                                    '</div>'
                            }

                            $('#testListContent').html(html)

                            // Update test progress bar (0 completed)
                            updateTestProgressBar(0, testTypes.length)
                        }
                    })
                }

                function finishTestProcess() {
                    var applicantId = $('#applicantId').val()
                    if (!applicantId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Applicant ID tidak ditemukan'
                        })
                        return
                    }

                    Swal.fire({
                        title: 'Selesai Proses Tes?',
                        html: '<div style="text-align: left; padding: 20px;">' +
                            '<p>Apakah Anda yakin ingin menyelesaikan proses interview test?</p>' +
                            '<p class="mt-3"><strong>Catatan:</strong> Setelah proses test selesai, status pelamar akan berubah menjadi "Interview".</p>' +
                            '</div>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Selesai Proses Tes',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Menyelesaikan proses...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            })

                            // Submit request
                            var finishUrl = '{{ url("sipo/hr/staff-applicants") }}/' + applicantId + '/finish-test-process';
                            $.ajax({
                                url: finishUrl,
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message ||
                                                'Proses interview test telah selesai',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            // Redirect to test completed page
                                            window.location.href = '{{ url("sipo/hr/staff-applicants") }}/' + applicantId + '/test-completed'
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message ||
                                                'Terjadi kesalahan saat menyelesaikan proses test'
                                        })
                                    }
                                },
                                error: function(xhr) {
                                    var errorMessage = 'Terjadi kesalahan saat menyelesaikan proses test'
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: errorMessage
                                    })
                                }
                            })
                        }
                    })
                }

                function updateTestProgressBar(completedTests, totalTests) {
                    // Calculate percentage
                    var percentage = Math.round((completedTests / totalTests) * 100)

                    // Update progress bar
                    $('#testProgressBar').css('width', percentage + '%')
                    $('#testProgressBar').attr('aria-valuenow', percentage)
                    $('#testProgressPercentage').text(percentage + '%')
                    $('#testProgressText').text(completedTests + ' dari ' + totalTests + ' test selesai')

                    // Update progress bar color based on progress
                    $('#testProgressBar').removeClass('bg-danger bg-warning bg-info bg-success')
                    if (percentage < 25) {
                        $('#testProgressBar').addClass('bg-danger')
                    } else if (percentage < 50) {
                        $('#testProgressBar').addClass('bg-warning')
                    } else if (percentage < 100) {
                        $('#testProgressBar').addClass('bg-info')
                    } else {
                        $('#testProgressBar').addClass('bg-success')
                    }
                }

                // Check if applicant_id exists on page load and show test list if on data-diri tab
                $(document).ready(function() {
                    // Get applicant_id from URL parameter first
                    var urlParams = new URLSearchParams(window.location.search)
                    var applicantIdFromUrl = urlParams.get('id')

                    // Restore applicant_id from URL, localStorage, or hidden field (priority: URL > localStorage > hidden field)
                    var applicantId = applicantIdFromUrl || localStorage.getItem('applicant_id') || $('#applicantId').val()

                    if (applicantId) {
                        // If ID from URL or localStorage, update hidden field
                        if (applicantIdFromUrl || localStorage.getItem('applicant_id')) {
                            $('#applicantId').val(applicantId)
                            // Save to localStorage if not already saved
                            if (!localStorage.getItem('applicant_id')) {
                                localStorage.setItem('applicant_id', applicantId)
                            }
                        }
                        console.log('Restored applicant_id:', applicantId, 'Source:', applicantIdFromUrl ? 'URL' :
                            'localStorage/hidden field')
                    }

                    // Restore current tab if exists (after reload from 419 error)
                    var savedTab = localStorage.getItem('current_tab_before_reload')
                    if (savedTab) {
                        $('#currentTab').val(savedTab)
                        updateCurrentTab('#' + savedTab)
                        // Switch to saved tab
                        $('button[data-target="#' + savedTab + '"]').tab('show')
                        localStorage.removeItem('current_tab_before_reload')
                    }

                    var applicantId = $('#applicantId').val()
                    var currentTab = $('#currentTab').val()

                    // Show test list if applicant_id exists and tanda tangan pelamar is filled
                    if (applicantId) {
                        var ttdPelamar = $('#ttd_pelamar').val()
                        if (ttdPelamar && ttdPelamar.trim() !== '') {
                            loadTestList(applicantId)
                        }
                    }

                    // Check and mark saved tabs if applicant exists
                    if (applicantId) {
                        setTimeout(function() {
                            checkAndMarkSavedTabs()
                            updateProgressBar()
                        }, 300)
                    }

                    // Initial progress bar update
                    updateProgressBar()

                    // Update button text when ttd_pelamar field changes
                    $('#ttd_pelamar').on('input change', function() {
                        updateSaveButtonText()
                    })

                    // Check tanda tangan when switching to kemampuan tab
                    $('#kemampuan-tab').on('click', function() {
                        setTimeout(function() {
                            var applicantId = $('#applicantId').val() || localStorage.getItem(
                                'applicant_id')
                            var ttdPelamar = $('#ttd_pelamar').val()
                            if (applicantId && ttdPelamar && ttdPelamar.trim() !== '') {
                                $('#applicantId').val(applicantId)
                                loadTestList(applicantId)
                            }
                        }, 300)
                    })
                })

                // Functions for dynamic table rows - Pengalaman
                var pengalamanRowCount = {{ $applicant && $applicant->pengalaman ? count($applicant->pengalaman) : 1 }};

                // Update counter based on existing rows
                $(document).ready(function() {
                    var existingRows = $('#pengalamanTableBody tr').length
                    if (existingRows > 0) {
                        // Find the highest index from existing rows
                        var maxIndex = 0
                        $('#pengalamanTableBody input[name*="[nama_perusahaan]"]').each(function() {
                            var name = $(this).attr('name')
                            var match = name.match(/pengalaman\[(\d+)\]/)
                            if (match && parseInt(match[1]) > maxIndex) {
                                maxIndex = parseInt(match[1])
                            }
                        })
                        pengalamanRowCount = maxIndex > 0 ? maxIndex : existingRows
                    }
                })

                function addPengalamanRow() {
                    pengalamanRowCount++
                    var row = '<tr>' +
                        '<td class="text-center">' + pengalamanRowCount + '</td>' +
                        '<td><input type="text" class="form-control" name="pengalaman[' + pengalamanRowCount +
                        '][nama_perusahaan]" required></td>' +
                        '<td><input type="date" class="form-control" name="pengalaman[' + pengalamanRowCount +
                        '][tanggal_masuk]" required></td>' +
                        '<td><input type="date" class="form-control" name="pengalaman[' + pengalamanRowCount +
                        '][tanggal_keluar]" required></td>' +
                        '<td><input type="text" class="form-control" name="pengalaman[' + pengalamanRowCount + '][bagian]" required></td>' +
                        '<td><input type="text" class="form-control" name="pengalaman[' + pengalamanRowCount + '][jabatan]" required></td>' +
                        '<td><input type="text" class="form-control" name="pengalaman[' + pengalamanRowCount + '][alasan]"></td>' +
                        '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm" onclick="removePengalamanRow(this)">' +
                        '<i class="mdi mdi-delete"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>'
                    $('#pengalamanTableBody').append(row)
                    updatePengalamanRowNumbers()
                }

                function removePengalamanRow(button) {
                    var row = $(button).closest('tr')
                    var rowCount = $('#pengalamanTableBody tr').length
                    if (rowCount > 1) {
                        row.remove()
                        updatePengalamanRowNumbers()
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak bisa menghapus',
                            text: 'Minimal harus ada 1 baris data'
                        })
                    }
                }

                function updatePengalamanRowNumbers() {
                    $('#pengalamanTableBody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1)
                    })
                }

                // Functions for dynamic table rows - Keluarga Anak
                var keluargaAnakRowCount = {{ $applicant && $applicant->keluarga_anak ? count($applicant->keluarga_anak) : 1 }};

                // Update counter based on existing rows
                $(document).ready(function() {
                    var existingRows = $('#keluargaAnakTableBody tr').length
                    if (existingRows > 0) {
                        var maxIndex = 0
                        $('#keluargaAnakTableBody input[name*="[nama]"]').each(function() {
                            var name = $(this).attr('name')
                            var match = name.match(/keluarga_anak\[(\d+)\]/)
                            if (match && parseInt(match[1]) > maxIndex) {
                                maxIndex = parseInt(match[1])
                            }
                        })
                        keluargaAnakRowCount = maxIndex > 0 ? maxIndex : existingRows
                    }
                })

                function addKeluargaAnakRow() {
                    keluargaAnakRowCount++
                    var row = '<tr>' +
                        '<td class="text-center">' + keluargaAnakRowCount + '</td>' +
                        '<td><input type="text" class="form-control" name="keluarga_anak[' + keluargaAnakRowCount +
                        '][nama]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_anak[' + keluargaAnakRowCount +
                        '][tempat_lahir]"></td>' +
                        '<td><input type="date" class="form-control" name="keluarga_anak[' + keluargaAnakRowCount +
                        '][tanggal_lahir]"></td>' +
                        '<td>' +
                        '<select class="form-control" name="keluarga_anak[' + keluargaAnakRowCount + '][jenis_kelamin]">' +
                        '<option value="">-</option>' +
                        '<option value="L">L</option>' +
                        '<option value="P">P</option>' +
                        '</select>' +
                        '</td>' +
                        '<td><input type="text" class="form-control" name="keluarga_anak[' + keluargaAnakRowCount +
                        '][pendidikan]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_anak[' + keluargaAnakRowCount +
                        '][pekerjaan]"></td>' +
                        '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm" onclick="removeKeluargaAnakRow(this)">' +
                        '<i class="mdi mdi-delete"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>'
                    $('#keluargaAnakTableBody').append(row)
                    updateKeluargaAnakRowNumbers()
                }

                function removeKeluargaAnakRow(button) {
                    var row = $(button).closest('tr')
                    var rowCount = $('#keluargaAnakTableBody tr').length
                    if (rowCount > 1) {
                        row.remove()
                        updateKeluargaAnakRowNumbers()
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak bisa menghapus',
                            text: 'Minimal harus ada 1 baris data'
                        })
                    }
                }

                function updateKeluargaAnakRowNumbers() {
                    $('#keluargaAnakTableBody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1)
                    })
                }

                // Functions for dynamic table rows - Keluarga Orang Tua
                var keluargaOrtuRowCount = {{ $applicant && $applicant->keluarga_ortu ? count($applicant->keluarga_ortu) : 1 }};

                // Update counter based on existing rows
                $(document).ready(function() {
                    var existingRows = $('#keluargaOrtuTableBody tr').length
                    if (existingRows > 0) {
                        var maxIndex = 0
                        $('#keluargaOrtuTableBody input[name*="[nama]"]').each(function() {
                            var name = $(this).attr('name')
                            var match = name.match(/keluarga_ortu\[(\d+)\]/)
                            if (match && parseInt(match[1]) > maxIndex) {
                                maxIndex = parseInt(match[1])
                            }
                        })
                        keluargaOrtuRowCount = maxIndex > 0 ? maxIndex : existingRows
                    }
                })

                function addKeluargaOrtuRow() {
                    keluargaOrtuRowCount++
                    var row = '<tr>' +
                        '<td class="text-center">' + keluargaOrtuRowCount + '</td>' +
                        '<td><input type="text" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][nama]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][tempat_lahir]"></td>' +
                        '<td><input type="date" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][tanggal_lahir]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][status]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][pendidikan]"></td>' +
                        '<td><input type="text" class="form-control" name="keluarga_ortu[' + keluargaOrtuRowCount +
                        '][pekerjaan]"></td>' +
                        '<td class="text-center">' +
                        '<button type="button" class="btn btn-danger btn-sm" onclick="removeKeluargaOrtuRow(this)">' +
                        '<i class="mdi mdi-delete"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>'
                    $('#keluargaOrtuTableBody').append(row)
                    updateKeluargaOrtuRowNumbers()
                }

                function removeKeluargaOrtuRow(button) {
                    var row = $(button).closest('tr')
                    var rowCount = $('#keluargaOrtuTableBody tr').length
                    if (rowCount > 1) {
                        row.remove()
                        updateKeluargaOrtuRowNumbers()
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak bisa menghapus',
                            text: 'Minimal harus ada 1 baris data'
                        })
                    }
                }

                function updateKeluargaOrtuRowNumbers() {
                    $('#keluargaOrtuTableBody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1)
                    })
                }
            </script>
</body>

</html>

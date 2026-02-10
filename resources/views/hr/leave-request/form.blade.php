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
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                {{-- JS when reload page, focused on logo, not element in above --}}
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.html">
                        <span class="text-white bebas-neue font-weight-bold">SiPO - Krisanthium</span>
                    </a>
                </div>

            </nav>
        </header>
        {{-- @include('main.layouts.topbar-nav') --}}

        <div class="page-wrapper">
            <div class="container-fluid">

                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">Form Pengajuan Karyawan</h3>
                    </div>
                    <div class="col-md-7 align-self-center">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                            <li class="breadcrumb-item active">Form Pengajuan Karyawan</li>
                        </ol>
                    </div>
                </div>

                <!-- Employee Information -->
                @if (isset($employee))
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-gradient-primary text-white">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h4 class="card-title text-white">Informasi Karyawan</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="employee-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">Nama
                                                                Lengkap</label>
                                                            <div class="info-value fw-bold text-dark"
                                                                id="employee-name">
                                                                {{ $employee->Nama ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">NIP</label>
                                                            <div class="info-value" id="employee-nip">
                                                                {{ $employee->Nip ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">Divisi</label>
                                                            <div class="info-value" id="employee-position">
                                                                {{ $employee->DivisiNama . ' - ' . $employee->BagianNama ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">Email</label>
                                                            <div class="info-value" id="employee-division">
                                                                {{ $employee->Email ?? 'Tidak ada email' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">No. HP</label>
                                                            <div class="info-value" id="employee-phone">
                                                                {{ $employee->{'No Telp'} ?? 'Tidak ada no. hp' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="info-item mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <label class="info-label text-muted small">Status</label>
                                                            <div class="info-value">
                                                                Aktif ( {{ date('d-m-Y', strtotime($employee->Begda)) }} - {{ date('d-m-Y', strtotime($employee->Endda)) }} )
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Info Row -->
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="alert alert-info d-flex align-items-center">
                                                    {{-- <i class="mdi mdi-information me-2"></i> --}}
                                                    <div>
                                                        <strong>Informasi:</strong> Data karyawan telah terdeteksi dari
                                                        barcode.
                                                        Form pengajuan cuti akan otomatis terisi dengan informasi kontak
                                                        yang
                                                        tersedia.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif



                <!-- Form Pengajuan Karyawan -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Form Pengajuan Karyawan</h4>
                                <p class="card-subtitle text-muted">Pilih jenis pengajuan yang ingin Anda buat</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Cuti Tahunan -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-primary">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-calendar text-primary" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Cuti Tahunan</h5>
                                                <p class="card-text text-muted small">C - Cuti Tahunan</p>
                                                <button class="btn btn-outline-primary btn-lg w-100"
                                                    onclick="showLeaveForm('annual')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Izin Dinas -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-info">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-briefcase text-info" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Izin Dinas</h5>
                                                <p class="card-text text-muted small">DIN - Dinas</p>
                                                <button class="btn btn-outline-info btn-lg w-100"
                                                    onclick="showLeaveForm('dinas')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cuti Khusus -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-warning">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-star text-warning" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Cuti Khusus</h5>
                                                <p class="card-text text-muted small">P1 - Cuti Khusus</p>
                                                <button class="btn btn-outline-warning btn-lg w-100"
                                                    onclick="showLeaveForm('special')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cuti Sakit -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-danger">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-hospital text-danger" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Cuti Sakit</h5>
                                                <p class="card-text text-muted small">S/SKD - Sakit</p>
                                                <button class="btn btn-outline-danger btn-lg w-100"
                                                    onclick="showLeaveForm('sick')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2 -->
                                <div class="row">
                                    <!-- Cuti Hamil -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-pink">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-baby text-pink" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Cuti Hamil</h5>
                                                <p class="card-text text-muted small">H2 - Cuti Hamil</p>
                                                <button class="btn btn-outline-pink btn-lg w-100"
                                                    onclick="showLeaveForm('maternity')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cuti Haid -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-purple">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-heart text-purple" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Cuti Haid</h5>
                                                <p class="card-text text-muted small">H1 - Cuti Haid</p>
                                                <button class="btn btn-outline-purple btn-lg w-100"
                                                    onclick="showLeaveForm('menstrual')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ijin Dengan Informasi -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-secondary">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-information text-secondary" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Ijin Dengan Informasi</h5>
                                                <p class="card-text text-muted small">I - Ijin Dengan Informasi</p>
                                                <button class="btn btn-outline-secondary btn-lg w-100"
                                                    onclick="showLeaveForm('info')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ijin Sementara Sakit -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-orange">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-thermometer text-orange" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Ijin Sementara Sakit</h5>
                                                <p class="card-text text-muted small">IS - Ijin Sementara Sakit</p>
                                                <button class="btn btn-outline-orange btn-lg w-100"
                                                    onclick="showLeaveForm('temp_sick')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3 -->
                                <div class="row">
                                    <!-- Ijin Sementara Pribadi -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-teal">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-account-clock text-teal" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Ijin Sementara Pribadi</h5>
                                                <p class="card-text text-muted small">ISP - Ijin Sementara Pribadi</p>
                                                <button class="btn btn-outline-teal btn-lg w-100"
                                                    onclick="showLeaveForm('temp_personal')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lambat Datang -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-indigo">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-clock-alert text-indigo" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Lambat Datang</h5>
                                                <p class="card-text text-muted small">LD - Lambat Datang</p>
                                                <button class="btn btn-outline-indigo btn-lg w-100"
                                                    onclick="showLeaveForm('late')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pulang Awal -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-cyan">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-clock-out text-cyan" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Pulang Awal</h5>
                                                <p class="card-text text-muted small">PA - Pulang Awal</p>
                                                <button class="btn btn-outline-cyan btn-lg w-100"
                                                    onclick="showLeaveForm('early')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mangkir -->
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-left-dark">
                                            <div class="card-body text-center">
                                                <i class="mdi mdi-account-remove text-dark" style="font-size: 3rem;"></i>
                                                <h5 class="card-title mt-3">Mangkir</h5>
                                                <p class="card-text text-muted small">M - Mangkir (tanpa pemberitahuan)</p>
                                                <button class="btn btn-outline-dark btn-lg w-100"
                                                    onclick="showLeaveForm('absent')">
                                                    <i class="mdi mdi-plus me-2"></i>Buat Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Modal -->
                <div class="modal fade" id="leaveFormModal" tabindex="-1" aria-labelledby="leaveFormModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="leaveFormModalLabel">Form Pengajuan Cuti</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('public.leave-request.store') }}"
                                    id="leave-request-form">
                                    @csrf
                                    <input type="hidden" name="request_type" value="absence">
                                    <input type="hidden" name="employee_id" id="employee-id"
                                        value="{{ $employee->Nip ?? '' }}">
                                    <input type="hidden" name="leave_type" id="selected-leave-type">

                                    <!-- Leave Type Display -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <strong>Jenis Cuti:</strong> <span id="leave-type-display"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date" class="form-control"
                                                id="start-date" value="{{ old('start_date') }}" required
                                                min="{{ date('Y-m-d') }}">
                                            @error('start_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Selesai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date" class="form-control"
                                                id="end-date" value="{{ old('end_date') }}" required
                                                min="{{ date('Y-m-d') }}">
                                            @error('end_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Duration Display -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">Durasi Cuti</label>
                                            <input type="text" class="form-control" id="duration-display" readonly
                                                placeholder="Akan dihitung otomatis">
                                        </div>
                                    </div>

                                    <!-- Reason -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">Alasan Cuti <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan alasan pengajuan cuti" required>{{ old('reason') }}</textarea>
                                            @error('reason')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kontak Selama Cuti <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="contact_during_leave" class="form-control"
                                                value="{{ old('contact_during_leave') }}"
                                                placeholder="Nomor yang bisa dihubungi selama cuti" required>
                                            @error('contact_during_leave')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kontak Darurat <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="emergency_contact" class="form-control"
                                                value="{{ old('emergency_contact') }}"
                                                placeholder="Nomor kontak darurat" required>
                                            @error('emergency_contact')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Common Fields -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">Catatan Tambahan</label>
                                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">Lampiran</label>
                                            <input type="file" name="attachment" class="form-control"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <div class="form-text">Format yang diperbolehkan: PDF, JPG, JPEG, PNG
                                                (Maksimal 2MB)
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="mdi mdi-close me-2"></i>Batal
                                </button>
                                <button type="button" class="btn btn-info" onclick="submitLeaveForm()">
                                    <i class="mdi mdi-content-save me-2"></i>Simpan Pengajuan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



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
</body>

</html>

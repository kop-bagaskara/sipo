@extends('main.layouts.main')
@section('title')
    Dashboard HR - Form Pengajuan Karyawan
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

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
        .cust-col {
            white-space: nowrap;
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

        /* Calendar Badge Styles - Kotak kecil dengan jumlah */
        .fc-event.badge-event {
            border: none !important;
            padding: 2px 6px !important;
            margin: 1px 2px !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            display: inline-block !important;
            min-width: 24px !important;
            text-align: center !important;
            line-height: 1.4 !important;
            cursor: pointer !important;
        }

        .fc-event.badge-event-absence {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .fc-event.badge-event-shift_change {
            background-color: #4ecdc4 !important;
            color: white !important;
        }

        .fc-event.badge-event-overtime {
            background-color: #ffe66d !important;
            color: #333 !important;
        }

        .fc-event.badge-event-inventory {
            background-color: #95e1d3 !important;
            color: #333 !important;
        }

        /* Container untuk badges di setiap tanggal */
        .fc-daygrid-day-events {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 2px !important;
            align-items: flex-start !important;
        }
    </style>
@endsection
@section('page-title')
    Form Pengajuan Karyawan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
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

        <div class="row">
            <div class="col-md-3">
                <!-- Statistics Tiles (Microsoft-style 2x2) -->
                <style>
                    /* Grid 2x2: tinggi seragam */
                    .ms-tiles {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        grid-auto-rows: 110px;
                        /* tinggi tile konsisten */
                        gap: 16px;
                    }

                    /* Tile dasar */
                    .ms-tile {
                        width: 100%;
                        min-width: 180px;
                        border-radius: 12px;
                        background: #fff;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                        padding: 16px;
                        display: flex;
                        align-items: center;
                        gap: 16px;
                    }

                    /* Kotak icon: square dan center */
                    .ms-tile .tile-icon {
                        width: 60px;
                        height: 60px;
                        /* sama dengan width */
                        border-radius: 12px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 28px;
                        /* ukuran icon */
                        flex: 0 0 60px;
                        /* kunci lebar */
                    }

                    /* Teks */
                    .ms-tile .tile-meta {
                        line-height: 1.1;
                        flex: 1;
                        min-width: 0;
                    }

                    .ms-tile .tile-meta .label {
                        font-size: 13px;
                        color: #6c757d;
                        margin-bottom: 6px;
                        line-height: 1.3;
                    }

                    .ms-tile .tile-meta .value {
                        font-size: 28px;
                        font-weight: 700;
                        margin: 0;
                    }

                    @media (min-width: 1200px) {
                        .ms-tiles {
                            grid-template-columns: repeat(4, minmax(0, 1fr));
                        }
                    }

                    /* Stats Cards - Small Gap */
                    .stats-row {
                        margin: 0 !important;
                        padding: 8px !important;
                    }

                    .stats-row .col-md-6 {
                        padding: 4px !important;
                    }

                    .stats-row .btn {
                        margin: 0 !important;
                        border-radius: 4px !important;
                        border-width: 2px !important;
                    }
                </style>
                <div class="card">
                    <div class="card-header text-center">
                        <p class="card-subtitle mb-0" style="padding:2px;">Count Pengajuan</p>
                    </div>
                    <div class="row g-0 stats-row">
                        <div class="col-md-6">
                            <a href="#"
                                class="btn btn-outline-info btn-lg w-100 align-items-center justify-content-center"
                                style="background-color: #fff; border-radius: 0;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="badge text-info"
                                        style="font-size: 29px; margin-bottom: 10px;">{{ $stats['total_requests'] }}</span>
                                    <span class="text-center text-info">Total Data</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#"
                                class="btn btn-outline-warning btn-lg w-100 align-items-center justify-content-center"
                                style="background-color: #fff; border-radius: 0;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="badge text-warning"
                                        style="font-size: 29px; margin-bottom: 10px;">{{ $stats['pending_requests'] }}</span>
                                    <span class="text-center text-warning">Tunggu Approve</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#"
                                class="btn btn-outline-success btn-lg w-100 align-items-center justify-content-center"
                                style="background-color: #fff; border-radius: 0;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="badge text-success"
                                        style="font-size: 29px; margin-bottom: 10px;">{{ $stats['approved_requests'] }}</span>
                                    <span class="text-center text-success">Data Disetujui</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#"
                                class="btn btn-outline-danger btn-lg w-100 align-items-center justify-content-center"
                                style="background-color: #fff; border-radius: 0;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="badge text-danger"
                                        style="font-size: 29px; margin-bottom: 10px;">{{ $stats['rejected_requests'] }}</span>
                                    <span class="text-center text-danger">Data Ditolak</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->jabatan == 5 || Auth::user()->jabatan == 4 || Auth::user()->jabatan == 3 || (method_exists(Auth::user(), 'isHR') && Auth::user()->isHR()))
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Pending Approval</h4>
                                    <table class="table browser m-t-30 no-border">
                                        <tbody>
                                            @if (Auth::user()->jabatan == 5)
                                                <tr>
                                                    <td style="width:40px"><i class="mdi mdi-account text-info"></i></td>
                                                    <td><a href="{{ route('hr.approval.supervisor-pending') }}"
                                                            class="text-info">
                                                            SPV Pending</a></td>
                                                    <td class="text-right"><span id="spvPendingCount"
                                                            class="label label-light-info">{{ $stats['spv_pending'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (Auth::user()->jabatan == 4)
                                                <tr>
                                                    <td style="width:40px"><i class="mdi mdi-account-star text-info"></i>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('hr.approval.head-pending') }}"
                                                            class="text-info">
                                                            Head Pending
                                                        </a>
                                                        @if (Auth::user()->divisi == 4)
                                                            <br><small class="text-muted">(Produksi + Prepress)</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-right"><span id="headPendingCount"
                                                            class="label label-light-info">{{ $stats['head_pending'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (Auth::user()->jabatan == 3 && Auth::user()->divisi != 13)
                                                <tr>
                                                    <td style="width:40px"><i class="mdi mdi-account text-warning"></i></td>
                                                    <td><a href="{{ route('hr.approval.manager-pending') }}"
                                                            class="text-warning">
                                                            Manager Pending</a></td>
                                                    <td class="text-right"><span id="managerPendingCount"
                                                            class="label label-light-warning">{{ $stats['manager_pending'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (Auth::user()->divisi == 13)
                                                <tr>
                                                    <td style="width:40px"><i class="mdi mdi-account-star text-primary"></i></td>
                                                    <td><a href="{{ route('hr.approval.general-manager-pending') }}"
                                                            class="text-primary">
                                                            General Manager Pending</a></td>
                                                    <td class="text-right"><span id="generalManagerPendingCount"
                                                            class="label label-light-primary">{{ $stats['general_manager_pending'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (method_exists(Auth::user(), 'isHR') && Auth::user()->isHR())
                                                <tr>
                                                    <td style="width:40px"><i class="mdi mdi-account text-success"></i></td>
                                                    <td><a href="{{ route('hr.approval.hr-pending') }}"
                                                            class="text-success">HRD
                                                            Pending</a></td>
                                                    <td class="text-right"><span id="hrPendingCount"
                                                            class="label label-light-success">{{ $stats['hr_pending'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Grafik Bundar --}}
                <div class="card">
                    <div class="card-header" style="overflow: hidden;">
                        <h4 class="card-title"
                            style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal; word-break: break-word; margin: 0;">
                            Grafik Izin per Department</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="chartDepartment" class="form-label">Department:</label>
                                <select class="form-control" id="chartDepartment">
                                    <option value="">Semua Department</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="chartMonth" class="form-label">Bulan:</label>
                                <input type="month" class="form-control" id="chartMonth" value="{{ date('Y-m') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" id="loadChartBtn">
                                    <i class="mdi mdi-refresh me-1"></i>Muat Grafik
                                </button>
                            </div>
                        </div>
                        <canvas id="doughnutChart" style="max-height: 400px;"></canvas>
                        <div id="chartNoDataMessage" class="text-center text-muted mt-3"
                            style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal; word-break: break-word; display: none;">
                            Tidak ada data untuk periode yang dipilih
                        </div>
                    </div>
                </div>

            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="card-subtitle mb-0" style="padding:2px;">Pilih jenis pengajuan yang
                            ingin Anda buat</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('hr.requests.guide') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="mdi mdi-book-open-page-variant me-1"></i>Tata Cara Penggunaan
                            </a>
                            <a href="{{ route('hr.requests.index') }}" class="btn btn-sm btn-outline-info">
                                <i class="mdi mdi-view-list me-1"></i>Lihat Semua Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Cuti Tahunan -->
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-primary">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-swap-horizontal text-primary" style="font-size: 3rem;"></i>
                                        <h5 class="card-title mt-3">Permohonan Tukar Shift</h5>
                                        <a href="{{ route('hr.requests.create', ['type' => 'shift_change']) }}"
                                            class="btn btn-outline-primary btn-lg w-100">
                                            <i class="mdi mdi-plus me-2"></i>Buat
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Izin Dinas -->
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-info">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-calendar text-info" style="font-size: 3rem;"></i>
                                        <h5 class="card-title mt-3">Izin Tidak Masuk Kerja</h5>
                                        <a href="{{ route('hr.requests.create', ['type' => 'absence']) }}"
                                            class="btn btn-outline-info btn-lg w-100">
                                            <i class="mdi mdi-plus me-2"></i>Buat
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuti Khusus -->
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-warning">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-clock text-warning" style="font-size: 3rem;"></i>
                                        <h5 class="card-title mt-3">Surat Perintah Lembur</h5>
                                        <a href="{{ route('hr.spl.create') }}"
                                            class="btn btn-outline-warning btn-lg w-100">
                                            <i class="mdi mdi-plus me-2"></i>Buat
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuti Sakit -->
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-danger">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-file-document text-danger" style="font-size: 3rem;"></i>
                                        <h5 class="card-title mt-3">Membawa Inventaris</h5>
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('hr.vehicle-asset.create', ['type' => 'vehicle']) }}"
                                                class="btn btn-outline-danger btn-lg dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-plus me-2"></i>Buat
                                            </a>
                                            <ul class="dropdown-menu w-100">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('hr.vehicle-asset.create', ['type' => 'vehicle']) }}">
                                                        <i class="mdi mdi-car me-2"></i>KENDARAAN
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('hr.vehicle-asset.create', ['type' => 'asset']) }}">
                                                        <i class="mdi mdi-package-variant me-2"></i>INVENTARIS
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title">Semua Pengajuan Karyawan</h4>
                                            <p class="card-subtitle text-muted">Daftar semua pengajuan: Form Karyawan, Data
                                                Lembur dan
                                                Permintaan Inventaris</p>
                                        </div>
                                        <div>
                                            <a href="{{ route('hr.requests.report') }}" class="btn btn-info">
                                                <i class="mdi mdi-chart-line me-1"></i>Report
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Filter Tabs -->
                                        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="requests-tab" data-toggle="tab"
                                                    data-target="#requests" type="button" role="tab">
                                                    <i class="mdi mdi-view-list me-1"></i> Semua Pengajuan
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="calendar-tab" data-toggle="tab"
                                                    data-target="#calendar" type="button" role="tab">
                                                    <i class="mdi mdi-calendar-month me-1"></i> Kalender Aktivitas
                                                </button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="approval-history-tab" data-toggle="tab"
                                                    data-target="#approval-history" type="button" role="tab">
                                                    <i class="mdi mdi-history me-1"></i> Riwayat Approval
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content" id="requestTabsContent">
                                            <!-- Semua Pengajuan -->
                                            <div class="tab-pane fade show active" id="requests" role="tabpanel">
                                                <!-- Filter Section -->
                                                <form method="GET" action="{{ route('hr.requests.index') }}"
                                                    id="filterForm">
                                                    <div class="mb-4">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label for="requestFilter" class="form-label">Filter
                                                                    Data:</label>
                                                                <select class="form-control" id="requestFilter"
                                                                    name="filter_type">
                                                                    <option value="">-- Pilih Filter --</option>
                                                                    <option value="forms"
                                                                        {{ request('filter_type') == 'forms' ? 'selected' : '' }}>
                                                                        Form Karyawan</option>
                                                                    <option value="vehicle"
                                                                        {{ request('filter_type') == 'vehicle' ? 'selected' : '' }}>
                                                                        Kendaraan</option>
                                                                    <option value="asset"
                                                                        {{ request('filter_type') == 'asset' ? 'selected' : '' }}>
                                                                        Inventaris</option>
                                                                    <option value="spl"
                                                                        {{ request('filter_type') == 'spl' ? 'selected' : '' }}>
                                                                        Surat Lembur</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="dateFrom" class="form-label">Dari
                                                                    Tanggal:</label>
                                                                <input type="date" class="form-control" id="dateFrom"
                                                                    name="date_from" value="{{ request('date_from') }}">
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="dateTo" class="form-label">Sampai
                                                                    Tanggal:</label>
                                                                <input type="date" class="form-control" id="dateTo"
                                                                    name="date_to" value="{{ request('date_to') }}">
                                                            </div>
                                                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                                                <button type="submit" class="btn btn-primary w-100">
                                                                    <i class="mdi mdi-filter me-1"></i>Filter
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!-- Message awal (default, tidak ada filter) -->
                                                <div id="noFilterMessage" class="text-center py-5"
                                                    style="display: {{ empty($filterType) ? 'block' : 'none' }};">
                                                    <i class="mdi mdi-filter-outline text-muted"
                                                        style="font-size: 4rem;"></i>
                                                    <h5 class="text-muted mt-3">Pilih Filter untuk Menampilkan Data</h5>
                                                    <p class="text-muted">Silakan pilih filter di atas untuk menampilkan
                                                        data pengajuan.</p>
                                                </div>

                                                <!-- Section Form Karyawan -->
                                                <div id="filter-forms" class="filter-section"
                                                    style="display: {{ $filterType == 'forms' ? 'block' : 'none' }};">
                                                    @if ($requests->count() > 0)
                                                        <div class="table-responsive">
                                                            <table id="formsTable"
                                                                class="table table-striped table-bordered dt-responsive nowrap"
                                                                style="width:100%">
                                                                <thead class="table-primary">
                                                                    <tr>
                                                                        <th><i
                                                                                class="mdi mdi-file-document-outline me-1"></i>
                                                                            No. Pengajuan</th>
                                                                        <th><i class="mdi mdi-tag me-1"></i> Jenis</th>
                                                                        <th><i class="mdi mdi-account me-1"></i> Pemohon
                                                                        </th>
                                                                        <th><i class="mdi mdi-flag me-1"></i> Status</th>
                                                                        <th><i class="mdi mdi-calendar me-1"></i> Tanggal
                                                                            Dibuat</th>
                                                                        <th><i class="mdi mdi-clock me-1"></i> Lama (Hari)
                                                                        </th>
                                                                        <th><i class="mdi mdi-cog me-1"></i> Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($requests as $request)
                                                                        <tr>
                                                                            <td>
                                                                                <span
                                                                                    class="fw-bold text-primary">{{ $request->request_number }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-info"
                                                                                    style="color: white;">{{ $request->request_type_label }}</span>
                                                                            </td>
                                                                            <td>{{ $request->employee->name ?? 'N/A' }}
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge {{ $request->status_badge_class }}">{{ $request->status_label }}</span>
                                                                            </td>
                                                                            <td>{{ $request->created_at->format('d/m/Y H:i') }}
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="text-muted">{{ $request->days_since_created }}
                                                                                    hari</span>
                                                                            </td>
                                                                            <td>
                                                                                <div class="btn-group" role="group">
                                                                                    <a href="{{ route('hr.requests.show', $request->id) }}"
                                                                                        class="btn btn-sm btn-outline-primary"
                                                                                        title="Lihat Detail">
                                                                                        <i class="mdi mdi-eye"></i>
                                                                                    </a>
                                                                                    {{-- @if ($request->employee_id == auth()->id() && $request->status == 'pending')
                                                                                        <a href="{{ route('hr.requests.edit', $request->id) }}"
                                                                                            class="btn btn-sm btn-outline-warning"
                                                                                            title="Edit">
                                                                                            <i class="mdi mdi-pencil"></i>
                                                                                        </a>
                                                                                        <form method="POST"
                                                                                            action="{{ route('hr.requests.cancel', $request->id) }}"
                                                                                            class="d-inline"
                                                                                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                                                                            @csrf
                                                                                            <button type="submit"
                                                                                                class="btn btn-sm btn-outline-danger"
                                                                                                title="Batalkan">
                                                                                                <i
                                                                                                    class="mdi mdi-delete"></i>
                                                                                            </button>
                                                                                        </form>
                                                                                    @endif --}}
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-5">
                                                            <i class="mdi mdi-file-document-outline text-muted"
                                                                style="font-size: 4rem;"></i>
                                                            <h5 class="text-muted mt-3">Tidak ada form pengajuan</h5>
                                                            <p class="text-muted">Belum ada pengajuan form karyawan (cuti,
                                                                shift change, dll).</p>
                                                            <a href="{{ route('hr.requests.create') }}"
                                                                class="btn btn-primary">
                                                                <i class="mdi mdi-plus me-2"></i>Buat Form Pengajuan
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Section Data Lembur -->
                                                <div id="filter-overtime" class="filter-section"
                                                    style="display: {{ $filterType == 'overtime' ? 'block' : 'none' }};">
                                                    @if ($overtimeEntries->count() > 0)
                                                        <div class="table-responsive">
                                                            <table id="overtimeTable"
                                                                class="table table-striped table-bordered dt-responsive nowrap"
                                                                style="width:100%">
                                                                <thead class="table-warning">
                                                                    <tr>
                                                                        <th><i class="mdi mdi-calendar me-1"></i> Tanggal
                                                                        </th>
                                                                        <th><i class="mdi mdi-account me-1"></i> Nama</th>
                                                                        <th><i class="mdi mdi-map-marker me-1"></i> Lokasi
                                                                        </th>
                                                                        <th><i class="mdi mdi-clock me-1"></i> Jam</th>
                                                                        <th><i class="mdi mdi-flag me-1"></i> Status</th>
                                                                        <th><i class="mdi mdi-cog me-1"></i> Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($overtimeEntries as $entry)
                                                                        <tr>
                                                                            <td>{{ $entry->request_date->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ $entry->employee_name }}</td>
                                                                            <td>{{ \Illuminate\Support\Str::limit($entry->location, 30) }}
                                                                            </td>
                                                                            <td>{{ $entry->start_time->format('H:i') }}-{{ $entry->end_time->format('H:i') }}
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    $statusMap = [
                                                                                        'pending_spv' => [
                                                                                            'class' => 'warning',
                                                                                            'text' => 'Pending SPV',
                                                                                        ],
                                                                                        'spv_approved' => [
                                                                                            'class' => 'info',
                                                                                            'text' => 'Disetujui SPV',
                                                                                        ],
                                                                                        'spv_rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak SPV',
                                                                                        ],
                                                                                        'head_approved' => [
                                                                                            'class' => 'primary',
                                                                                            'text' => 'Disetujui Head',
                                                                                        ],
                                                                                        'head_rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak Head',
                                                                                        ],
                                                                                        'hrga_approved' => [
                                                                                            'class' => 'success',
                                                                                            'text' => 'Disetujui HRGA',
                                                                                        ],
                                                                                        'hrga_rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak HRGA',
                                                                                        ],
                                                                                    ];
                                                                                    $status = $statusMap[
                                                                                        $entry->status
                                                                                    ] ?? [
                                                                                        'class' => 'secondary',
                                                                                        'text' => $entry->status,
                                                                                    ];
                                                                                @endphp
                                                                                <span
                                                                                    class="badge badge-{{ $status['class'] }} text-white">{{ $status['text'] }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ route('hr.overtime.index') }}"
                                                                                    class="btn btn-sm btn-outline-warning">
                                                                                    <i class="mdi mdi-eye"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-5">
                                                            <i class="mdi mdi-clock-outline text-warning"
                                                                style="font-size: 4rem;"></i>
                                                            <h5 class="text-muted mt-3">Tidak ada data lembur</h5>
                                                            <p class="text-muted">Belum ada data lembur yang tercatat.</p>
                                                            <a href="{{ route('hr.overtime.index') }}"
                                                                class="btn btn-warning">
                                                                <i class="mdi mdi-plus me-2"></i>Buat Data Lembur
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Section Kendaraan -->
                                                <div id="filter-vehicle" class="filter-section"
                                                    style="display: {{ $filterType == 'vehicle' ? 'block' : 'none' }};">
                                                    @if ($vehicleRequests->count() > 0)
                                                        <div class="table-responsive">
                                                            <table id="vehicleTable"
                                                                class="table table-striped table-bordered dt-responsive nowrap"
                                                                style="width:100%">
                                                                <thead class="table-success">
                                                                    <tr>
                                                                        <th><i class="mdi mdi-calendar me-1"></i> Tanggal
                                                                        </th>
                                                                        <th><i class="mdi mdi-account me-1"></i> Nama</th>
                                                                        <th><i class="mdi mdi-car me-1"></i> Jenis</th>
                                                                        <th><i class="mdi mdi-map-marker me-1"></i> Tujuan
                                                                        </th>
                                                                        <th><i class="mdi mdi-flag me-1"></i> Status</th>
                                                                        <th><i class="mdi mdi-cog me-1"></i> Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($vehicleRequests as $request)
                                                                        <tr>
                                                                            <td>{{ $request->request_date->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ $request->employee_name }}</td>
                                                                            <td>{{ $request->vehicle_type }}</td>
                                                                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    $statusMap = [
                                                                                        'pending_manager' => [
                                                                                            'class' => 'warning',
                                                                                            'text' => 'Pending Manager',
                                                                                        ],
                                                                                        'manager_approved' => [
                                                                                            'class' => 'info',
                                                                                            'text' =>
                                                                                                'Disetujui Manager',
                                                                                        ],
                                                                                        'manager_rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak Manager',
                                                                                        ],
                                                                                        'hrga_approved' => [
                                                                                            'class' => 'success',
                                                                                            'text' => 'Disetujui HRGA',
                                                                                        ],
                                                                                        'hrga_rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak HRGA',
                                                                                        ],
                                                                                    ];
                                                                                    $status = $statusMap[
                                                                                        $request->status
                                                                                    ] ?? [
                                                                                        'class' => 'secondary',
                                                                                        'text' => $request->status,
                                                                                    ];
                                                                                @endphp
                                                                                <span
                                                                                    class="badge badge-{{ $status['class'] }} text-white">{{ $status['text'] }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ route('hr.vehicle-asset.index', ['type' => 'vehicle']) }}"
                                                                                    class="btn btn-sm btn-outline-success">
                                                                                    <i class="mdi mdi-eye"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-5">
                                                            <i class="mdi mdi-car text-success"
                                                                style="font-size: 4rem;"></i>
                                                            <h5 class="text-muted mt-3">Tidak ada permintaan kendaraan</h5>
                                                            <p class="text-muted">Belum ada permintaan kendaraan yang
                                                                tercatat.</p>
                                                            <a href="{{ route('hr.vehicle-asset.index', ['type' => 'vehicle']) }}"
                                                                class="btn btn-success">
                                                                <i class="mdi mdi-plus me-2"></i>Buat Permintaan Kendaraan
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Section Inventaris -->
                                                <div id="filter-asset" class="filter-section"
                                                    style="display: {{ $filterType == 'asset' ? 'block' : 'none' }};">
                                                    @if ($assetRequests->count() > 0)
                                                        <div class="table-responsive">
                                                            <table id="assetTable"
                                                                class="table table-striped table-bordered dt-responsive nowrap"
                                                                style="width:100%">
                                                                <thead class="table-info">
                                                                    <tr>
                                                                        <th><i class="mdi mdi-calendar me-1"></i> Tanggal
                                                                        </th>
                                                                        <th><i class="mdi mdi-account me-1"></i> Nama</th>
                                                                        <th><i class="mdi mdi-package-variant me-1"></i>
                                                                            Kategori</th>
                                                                        <th><i class="mdi mdi-map-marker me-1"></i> Tujuan
                                                                        </th>
                                                                        <th><i class="mdi mdi-flag me-1"></i> Status</th>
                                                                        <th><i class="mdi mdi-cog me-1"></i> Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($assetRequests as $request)
                                                                        <tr>
                                                                            <td>{{ $request->request_date->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ $request->employee_name }}</td>
                                                                            <td>{{ $request->asset_category }}</td>
                                                                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    $statusMap = [
                                                                                        'pending_manager' => [
                                                                                            'class' =>
                                                                                                'warning text-white',
                                                                                            'text' => 'Pending Manager',
                                                                                        ],
                                                                                        'manager_approved' => [
                                                                                            'class' =>
                                                                                                'info text-white',
                                                                                            'text' =>
                                                                                                'Disetujui Manager',
                                                                                        ],
                                                                                        'manager_rejected' => [
                                                                                            'class' =>
                                                                                                'danger text-white',
                                                                                            'text' => 'Ditolak Manager',
                                                                                        ],
                                                                                        'hrga_approved' => [
                                                                                            'class' =>
                                                                                                'success text-white',
                                                                                            'text' => 'Disetujui HRGA',
                                                                                        ],
                                                                                        'hrga_rejected' => [
                                                                                            'class' =>
                                                                                                'danger text-white',
                                                                                            'text' => 'Ditolak HRGA',
                                                                                        ],
                                                                                    ];
                                                                                    $status = $statusMap[
                                                                                        $request->status
                                                                                    ] ?? [
                                                                                        'class' =>
                                                                                            'secondary text-white',
                                                                                        'text' => $request->status,
                                                                                    ];
                                                                                @endphp
                                                                                <span
                                                                                    class="badge badge-{{ $status['class'] }}"
                                                                                    style="color: white;">{{ $status['text'] }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ route('hr.vehicle-asset.index', ['type' => 'asset']) }}"
                                                                                    class="btn btn-sm btn-outline-info">
                                                                                    <i class="mdi mdi-eye"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-5">
                                                            <i class="mdi mdi-package-variant text-info"
                                                                style="font-size: 4rem;"></i>
                                                            <h5 class="text-muted mt-3">Tidak ada permintaan inventaris
                                                            </h5>
                                                            <p class="text-muted">Belum ada permintaan inventaris yang
                                                                tercatat.</p>
                                                            <a href="{{ route('hr.vehicle-asset.index', ['type' => 'asset']) }}"
                                                                class="btn btn-info">
                                                                <i class="mdi mdi-plus me-2"></i>Buat Permintaan Inventaris
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Section Surat Lembur (SPL) -->
                                                <div id="filter-spl" class="filter-section"
                                                    style="display: {{ $filterType == 'spl' ? 'block' : 'none' }};">
                                                    @if (isset($splRequests) && $splRequests->count() > 0)
                                                        <div class="table-responsive">
                                                            <table id="splTable"
                                                                class="table table-striped table-bordered dt-responsive nowrap"
                                                                style="width:100%">
                                                                <thead class="table-warning">
                                                                    <tr>
                                                                        <th><i
                                                                                class="mdi mdi-file-document-outline me-1"></i>
                                                                            No. SPL</th>
                                                                        <th><i class="mdi mdi-calendar me-1"></i> Tanggal
                                                                        </th>
                                                                        <th><i class="mdi mdi-clock me-1"></i> Shift</th>
                                                                        <th><i class="mdi mdi-account me-1"></i> Supervisor
                                                                        </th>
                                                                        <th><i class="mdi mdi-account-group me-1"></i>
                                                                            Jumlah
                                                                            Karyawan</th>
                                                                        <th><i class="mdi mdi-flag me-1"></i> Status</th>
                                                                        <th><i class="mdi mdi-cog me-1"></i> Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($splRequests as $spl)
                                                                        <tr>
                                                                            <td>
                                                                                <span
                                                                                    class="fw-bold text-warning">{{ $spl->spl_number }}</span>
                                                                            </td>
                                                                            <td>
                                                                                {{ $spl->request_date->format('d/m/Y') }}
                                                                            </td>
                                                                            <td>{{ $spl->shift }}</td>
                                                                            <td>{{ $spl->supervisor->name ?? '-' }}</td>
                                                                            <td style="text-align: center;">
                                                                                <span
                                                                                    class="badge bg-info">{{ $spl->employees->count() }}</span>
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    $statusMap = [
                                                                                        'draft' => [
                                                                                            'class' => 'secondary',
                                                                                            'text' => 'Draft',
                                                                                        ],
                                                                                        'submitted' => [
                                                                                            'class' => 'info',
                                                                                            'text' => 'Submitted',
                                                                                        ],
                                                                                        'signed' => [
                                                                                            'class' => 'warning',
                                                                                            'text' => 'Signed',
                                                                                        ],
                                                                                        'approved_hrd' => [
                                                                                            'class' => 'success',
                                                                                            'text' => 'Disetujui HRD',
                                                                                        ],
                                                                                        'rejected' => [
                                                                                            'class' => 'danger',
                                                                                            'text' => 'Ditolak',
                                                                                        ],
                                                                                    ];
                                                                                    $status = $statusMap[
                                                                                        $spl->status
                                                                                    ] ?? [
                                                                                        'class' => 'secondary',
                                                                                        'text' => $spl->status,
                                                                                    ];
                                                                                @endphp
                                                                                <span
                                                                                    class="badge badge-{{ $status['class'] }} text-white">{{ $status['text'] }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{ route('hr.spl.show', $spl->id) }}"
                                                                                    class="btn btn-sm btn-outline-warning">
                                                                                    <i class="mdi mdi-eye"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-5">
                                                            <i class="mdi mdi-file-document-outline text-warning"
                                                                style="font-size: 4rem;"></i>
                                                            <h5 class="text-muted mt-3">Tidak ada Surat Perintah Lembur
                                                            </h5>
                                                            <p class="text-muted">Belum ada Surat Perintah Lembur yang
                                                                tercatat.</p>
                                                            <a href="{{ route('hr.spl.create') }}"
                                                                class="btn btn-warning">
                                                                <i class="mdi mdi-plus me-2"></i>Buat Surat Perintah Lembur
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Kalender Aktivitas -->
                                            <div class="tab-pane fade" id="calendar" role="tabpanel">
                                                <div class="calendar-container">
                                                    <div id="activityCalendar"></div>
                                                </div>
                                            </div>

                                            <!-- Riwayat Approval -->
                                            <div class="tab-pane fade" id="approval-history" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title mb-4">
                                                            <i class="mdi mdi-history me-2"></i>Riwayat Approval Saya
                                                        </h5>
                                                        @if($approvalHistory->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-striped table-bordered" id="approvalHistoryTable">
                                                                    <thead class="table-primary">
                                                                        <tr>
                                                                            <th>No. Pengajuan</th>
                                                                            <th>Jenis</th>
                                                                            <th>Pemohon</th>
                                                                            <th>Divisi</th>
                                                                            <th>Role Approval</th>
                                                                            <th>Status</th>
                                                                            <th>Tanggal Approval</th>
                                                                            <th>Catatan</th>
                                                                            <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($approvalHistory as $history)
                                                                            @php
                                                                                $request = $history['request'];
                                                                                $requestType = $history['request_type'] ?? 'employee_request';
                                                                                $approvalRole = $history['approval_role'];
                                                                                $approvalStatus = $history['approval_status'];
                                                                                $approvalDate = $history['approval_date'];
                                                                                $approvalNotes = $history['approval_notes'];

                                                                                // Determine request number and label
                                                                                if ($requestType === 'vehicle_asset') {
                                                                                    // $requestNumber = ($request->reqeust_number === 'vehicle' ? 'VH-' : 'AS-') . str_pad($request->id, 4, '0', STR_PAD_LEFT);
                                                                                    $requestNumber = $request->request_number ?? 'N/A';
                                                                                    $requestTypeLabel = $request->request_type === 'vehicle' ? 'Pinjam Kendaraan' : 'Pinjam Inventaris';
                                                                                    $employeeName = $request->employee_name ?? 'N/A';
                                                                                    $divisiName = 'Divisi ' . ($request->divisi_id ?? 'N/A');
                                                                                    $detailRoute = route('hr.vehicle-asset.show', $request->id);

                                                                                    // Cek apakah bisa disapprove untuk vehicle/asset
                                                                                    $canDisapprove = false;
                                                                                    if ($approvalStatus === 'approved') {
                                                                                        $currentUser = auth()->user();
                                                                                        if ($approvalRole === 'GENERAL MANAGER') {
                                                                                            // General Manager bisa disapprove jika:
                                                                                            // 1. Belum ada HRGA approval (hrga_at masih null)
                                                                                            // 2. Status masih manager_approved (belum hrga_approved)
                                                                                            // 3. User adalah yang melakukan approval
                                                                                            $canDisapprove = is_null($request->hrga_at) &&
                                                                                                            ($request->status === 'manager_approved' || $request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED) &&
                                                                                                            (int) $currentUser->divisi === 13 &&
                                                                                                            $request->general_id == $currentUser->id &&
                                                                                                            !is_null($request->general_approved_at);
                                                                                        } elseif ($approvalRole === 'MANAGER') {
                                                                                            // Manager bisa disapprove jika belum ada General Manager/HRGA approval
                                                                                            // Pastikan user adalah yang melakukan approval
                                                                                            $canDisapprove = is_null($request->hrga_at) &&
                                                                                                            is_null($request->general_approved_at) &&
                                                                                                            $request->manager_id == $currentUser->id &&
                                                                                                            !is_null($request->manager_at);
                                                                                        } elseif ($approvalRole === 'HRGA') {
                                                                                            // HRGA adalah final approver, bisa disapprove
                                                                                            // Pastikan user adalah yang melakukan approval
                                                                                            $canDisapprove = $currentUser->isHR() &&
                                                                                                            $request->hrga_id == $currentUser->id &&
                                                                                                            !is_null($request->hrga_at);
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $requestNumber = $request->request_number ?? 'N/A';
                                                                                    $requestTypeLabel = $request->request_type_label ?? 'N/A';
                                                                                    $employeeName = $request->employee->name ?? 'N/A';
                                                                                    $divisiName = 'N/A';
                                                                                    if ($request->employee && $request->employee->divisiUser) {
                                                                                        $divisiName = $request->employee->divisiUser->divisi ?? $request->employee->divisiUser->nama_divisi ?? 'Divisi ' . $request->employee->divisi;
                                                                                    } elseif ($request->employee) {
                                                                                        $divisiName = 'Divisi ' . ($request->employee->divisi ?? 'N/A');
                                                                                    }
                                                                                    $detailRoute = route('hr.requests.show', $request->id);

                                                                                    // Cek apakah bisa disapprove (hanya untuk approved, bukan rejected)
                                                                                    // Hanya bisa disapprove jika belum ada approval berikutnya
                                                                                    $canDisapprove = false;
                                                                                    if ($approvalStatus === 'approved') {
                                                                                        if ($approvalRole === 'SPV') {
                                                                                            // SPV bisa disapprove jika belum ada HEAD/MANAGER/HR approval
                                                                                            $canDisapprove = !$request->head_approved_at &&
                                                                                                            !$request->manager_approved_at &&
                                                                                                            !$request->hr_approved_at &&
                                                                                                            !$request->general_approved_at;
                                                                                        } elseif ($approvalRole === 'HEAD DIVISI') {
                                                                                            // HEAD bisa disapprove jika belum ada MANAGER/HR approval
                                                                                            $canDisapprove = !$request->manager_approved_at &&
                                                                                                            !$request->hr_approved_at &&
                                                                                                            !$request->general_approved_at;
                                                                                        } elseif ($approvalRole === 'MANAGER') {
                                                                                            // MANAGER bisa disapprove jika belum ada HR approval
                                                                                            $canDisapprove = !$request->hr_approved_at &&
                                                                                                            !$request->general_approved_at;
                                                                                        } elseif ($approvalRole === 'GENERAL MANAGER') {
                                                                                            // GENERAL MANAGER bisa disapprove jika belum ada HR approval
                                                                                            $canDisapprove = !$request->hr_approved_at;
                                                                                        } elseif ($approvalRole === 'HRD') {
                                                                                            // HRD adalah final approver, bisa disapprove
                                                                                            $canDisapprove = true;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            <tr>
                                                                                <td><span class="fw-bold">{{ $requestNumber }}</span></td>
                                                                                <td>
                                                                                    <span class="badge bg-info text-white">{{ $requestTypeLabel }}</span>
                                                                                </td>
                                                                                <td>{{ $employeeName }}</td>
                                                                                <td>
                                                                                    <span class="badge badge-secondary">{{ $divisiName }}</span>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge badge-primary">{{ $approvalRole }}</span>
                                                                                </td>
                                                                                <td>
                                                                                    @if($approvalStatus === 'approved')
                                                                                        <span class="badge badge-success">
                                                                                            <i class="mdi mdi-check-circle me-1"></i>Disetujui
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="badge badge-danger">
                                                                                            <i class="mdi mdi-close-circle me-1"></i>Ditolak
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $approvalDate ? $approvalDate->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                                <td>
                                                                                    @if($approvalNotes)
                                                                                        <span class="text-muted" title="{{ $approvalNotes }}">
                                                                                            {{ \Illuminate\Support\Str::limit($approvalNotes, 50) }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <a href="{{ $detailRoute }}"
                                                                                       class="btn btn-sm btn-outline-primary"
                                                                                       title="Lihat Detail">
                                                                                        <i class="mdi mdi-eye"></i> Lihat
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="text-center py-5">
                                                                <i class="mdi mdi-information-outline text-muted" style="font-size: 4rem;"></i>
                                                                <h5 class="text-muted mt-3">Belum ada riwayat approval</h5>
                                                                <p class="text-muted">Riwayat approval yang sudah Anda lakukan akan ditampilkan di sini</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Data Table -->
        <!-- Semua Pengajuan -->

        </div>
    @endsection

    @section('scripts')
        <!-- DataTables JS -->
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

        <!-- SweetAlert2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <!-- FullCalendar JS -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

        <script>
            // Helper function untuk fetch dengan graceful error handling
            // Silent skip jika route tidak bisa diakses (403/401/404)
            async function silentFetch(url, options = {}) {
                try {
                    const response = await fetch(url, options);
                    // Cek jika response ok, jika tidak return null
                    if (!response.ok) {
                        console.warn(`Fetch warning: ${url} returned ${response.status}`);
                        return null;
                    }
                    return await response.json();
                } catch (error) {
                    // Silent error - log ke console tapi jangan hentikan eksekusi
                    console.warn(`Fetch skipped: ${url}`, error.message);
                    return null;
                }
            }

            // Load approval stats and fill SPV/HRD pending cards
            // Hanya jalankan jika card "Pending Approval" ada di DOM
            // DISABLED: Stats sudah di-render dengan benar dari server-side
            // JavaScript ini di-disable karena meng-overwrite counter yang sudah benar
            /*
            (function loadApprovalStats() {
                // Cek apakah ada elemen dengan class .card yang berisi "Pending Approval"
                const pendingApprovalCard = document.querySelector('.card-body h4.card-title');
                if (!pendingApprovalCard || pendingApprovalCard.textContent !== 'Pending Approval') {
                    console.log('Pending Approval card not found, skipping stats fetch');
                    return;
                }

                silentFetch('{{ route('hr.approval.stats') }}')
                    .then(stats => {
                        if (!stats) return; // Skip jika fetch gagal

                        // For approver (HEAD/SPV/MANAGER), controller returns pending_approval
                        if (typeof stats.pending_approval !== 'undefined') {
                            const spvEl = document.getElementById('spvPendingCount');
                            if (spvEl) spvEl.textContent = stats.pending_approval;

                            const headEl = document.getElementById('headPendingCount');
                            if (headEl) headEl.textContent = stats.pending_approval;

                            const managerEl = document.getElementById('managerPendingCount');
                            if (managerEl) managerEl.textContent = stats.pending_approval;
                        }
                        // For HR, controller returns pending_hr_approval
                        if (typeof stats.pending_hr_approval !== 'undefined') {
                            const hrEl = document.getElementById('hrPendingCount');
                            if (hrEl) hrEl.textContent = stats.pending_hr_approval;
                        }
                    });
            })();
            */

            // Enable Bootstrap tooltip for tile icons
            $(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body'
                });
            });

            function switchTab(tabName) {
                // Remove active class from all tabs
                document.querySelectorAll('.nav-link').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Add active class to target tab
                document.getElementById(tabName + '-tab').classList.add('active');
                document.getElementById(tabName).classList.add('show', 'active');

                // Initialize calendar when calendar tab is clicked
                if (tabName === 'calendar' && !window.calendarInitialized) {
                    initializeCalendar();
                }
            }

            function initializeCalendar() {
                // alert('a')
                const calendarEl = document.getElementById('activityCalendar');

                if (window.calendar && typeof window.calendar.destroy === 'function') {
                    window.calendar.destroy();
                }

                window.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    height: 'auto',
                    events: function(info, successCallback, failureCallback) {
                        // Fetch events from server
                        console.log('Fetching calendar events...');
                        silentFetch('{{ route('hr.requests.calendar-events') }}')
                            .then(data => {
                                if (!data) {
                                    console.warn('Calendar events fetch returned null, using empty array');
                                    successCallback([]);
                                    return;
                                }
                                console.log('Calendar events data:', data);
                                successCallback(data);
                            });
                    },
                    eventClick: function(info) {
                        // Show event details in modal or alert
                        showEventDetails(info.event);
                    },
                    eventDidMount: function(info) {
                        // Style events as small badges with counts
                        const event = info.event;
                        const eventType = event.extendedProps.type;
                        const count = event.extendedProps.count || event.title || '';

                        // Add badge class
                        info.el.classList.add('badge-event');
                        info.el.classList.add('badge-event-' + eventType);

                        // Update title to show count only
                        if (count) {
                            const titleEl = info.el.querySelector('.fc-event-title');
                            if (titleEl) {
                                titleEl.textContent = count;
                            } else {
                                // If no title element, set it directly
                                info.el.textContent = count;
                            }
                        }

                        // Make it compact
                        info.el.style.padding = '2px 6px';
                        info.el.style.margin = '1px 2px';
                        info.el.style.borderRadius = '4px';
                        info.el.style.fontSize = '11px';
                        info.el.style.fontWeight = '600';
                        info.el.style.minWidth = '24px';
                        info.el.style.textAlign = 'center';
                    },
                    dayMaxEvents: false,
                    moreLinkClick: 'popover'
                });

                window.calendar.render();
                window.calendarInitialized = true;
            }

            function showEventDetails(event) {
                const type = event.extendedProps.type;
                const count = event.extendedProps.count || event.title || 0;
                const date = event.extendedProps.date || event.start.toISOString().split('T')[0];
                const requestIds = event.extendedProps.request_ids || [];
                const formattedDate = event.start.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                let typeLabel = '';
                let typeIcon = '';
                switch (type) {
                    case 'absence':
                        typeLabel = 'Izin / Tidak Masuk Kerja';
                        typeIcon = 'mdi-calendar-remove';
                        break;
                    case 'shift_change':
                        typeLabel = 'Tukar Shift';
                        typeIcon = 'mdi-swap-horizontal';
                        break;
                    case 'overtime':
                        typeLabel = 'Lembur';
                        typeIcon = 'mdi-clock';
                        break;
                    case 'inventory':
                        typeLabel = 'Bawa Inventaris';
                        typeIcon = 'mdi-package-variant';
                        break;
                    default:
                        typeLabel = type;
                        typeIcon = 'mdi-information';
                }

                // Show loading modal first
                Swal.fire({
                    title: '',
                    html: `
                        <div class="p-3 text-center">
                            <i class="mdi ${typeIcon} me-2" style="font-size: 2rem; color: #4a90e2;"></i>
                            <h5 class="mt-2 mb-3">${typeLabel}</h5>
                            <p class="mb-2"><strong>Tanggal:</strong> ${formattedDate}</p>
                            <div class="mt-3">
                                <span class="badge badge-primary" style="font-size: 1.5rem; padding: 10px 20px;">
                                    Total: ${count} ${count == 1 ? 'permohonan' : 'permohonan'}
                                </span>
                            </div>
                            <div class="mt-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat data pemohon...</p>
                            </div>
                        </div>
                    `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '700px',
                    allowOutsideClick: false
                });

                // Fetch request details
                silentFetch(
                        `{{ route('hr.requests.calendar-request-details') }}?date=${date}&type=${type}&request_ids=${requestIds.join(',')}`
                        )
                    .then(result => {
                        if (!result || !result.success || !result.data || result.data.length === 0) {
                            // No data found or fetch failed
                            Swal.fire({
                                title: '',
                                html: `
                                    <div class="p-3 text-center">
                                        <i class="mdi ${typeIcon} me-2" style="font-size: 2rem; color: #4a90e2;"></i>
                                        <h5 class="mt-2 mb-3">${typeLabel}</h5>
                                        <p class="mb-2"><strong>Tanggal:</strong> ${formattedDate}</p>
                                        <div class="mt-3">
                                            <span class="badge badge-primary" style="font-size: 1.5rem; padding: 10px 20px;">
                                                Total: ${count} ${count == 1 ? 'permohonan' : 'permohonan'}
                                            </span>
                                        </div>
                                        <p class="mt-3 text-muted">Data pemohon tidak ditemukan</p>
                                    </div>
                                `,
                                showCloseButton: true,
                                showConfirmButton: true,
                                confirmButtonText: 'Tutup',
                                width: '450px'
                            });
                            return;
                        }

                        // Build table HTML
                        let tableRows = '';
                        result.data.forEach((item, index) => {
                            // Determine status badge class based on status
                            // Mengikuti logika yang sama dengan show.blade.php
                            let statusBadgeClass = 'info'; // default
                            let statusLabel = item.status_label || item.status || '-';
                            let statusStr = (item.status || '').toLowerCase();
                            let statusLabelStr = (statusLabel || '').toLowerCase();

                            // Pending statuses (warning - yellow)
                            // Cek dari status atau status_label yang mengandung "pending" atau "menunggu"
                            if (statusStr === 'pending' ||
                                statusStr === 'pending_spv' ||
                                statusStr === 'pending_manager' ||
                                statusLabelStr.includes('pending') ||
                                statusLabelStr.includes('menunggu')) {
                                statusBadgeClass = 'warning';
                            }
                            // Approved statuses (success - green)
                            // Cek dari status yang mengandung "approved" atau status_label yang mengandung "disetujui"
                            else if (statusStr === 'hr_approved' ||
                                     statusStr.includes('_approved') ||
                                     statusLabelStr.includes('disetujui')) {
                                statusBadgeClass = 'success';
                            }
                            // Rejected statuses (danger - red)
                            // Cek dari status yang mengandung "rejected" atau status_label yang mengandung "ditolak"
                            else if (statusStr.includes('rejected') ||
                                     statusLabelStr.includes('ditolak')) {
                                statusBadgeClass = 'danger';
                            }
                            // Cancelled status (secondary - gray)
                            else if (statusStr === 'cancelled' ||
                                     statusLabelStr.includes('dibatalkan')) {
                                statusBadgeClass = 'secondary';
                            }
                            // Other statuses (info - blue) - default

                            // Get status icon based on badge class
                            let statusIcon = '';
                            if (statusBadgeClass === 'success') {
                                statusIcon = '<i class="mdi mdi-check-circle me-1"></i>';
                            } else if (statusBadgeClass === 'danger') {
                                statusIcon = '<i class="mdi mdi-close-circle me-1"></i>';
                            } else if (statusBadgeClass === 'warning') {
                                statusIcon = '<i class="mdi mdi-clock-outline me-1"></i>';
                            } else if (statusBadgeClass === 'secondary') {
                                statusIcon = '<i class="mdi mdi-cancel me-1"></i>';
                            } else {
                                statusIcon = '<i class="mdi mdi-information-outline me-1"></i>';
                            }

                            tableRows += `
                                <tr>
                                    <td class="text-center" style="width: 50px;">${index + 1}</td>
                                    <td><strong>${item.request_number || '-'}</strong></td>
                                    <td>${item.employee_name || '-'}</td>
                                    <td><span class="badge badge-secondary">${item.employee_divisi || '-'}</span></td>
                                    <td><span class="badge badge-${statusBadgeClass}">${statusIcon}${statusLabel}</span></td>
                                    <td><small class="text-muted">${item.notes || '-'}</small></td>
                                </tr>
                            `;
                        });

                        const content = `
                            <div class="p-4">
                                <div class="text-center mb-4 pb-3 border-bottom">
                                    <div class="mb-3">
                                        <i class="mdi ${typeIcon}" style="font-size: 3rem; color: #4a90e2;"></i>
                                    </div>
                                    <h4 class="mb-2 font-weight-bold">${typeLabel}</h4>
                                    <p class="mb-2 text-muted">
                                        <i class="mdi mdi-calendar me-1"></i>
                                        <strong>${formattedDate}</strong>
                                    </p>
                                    <span class="badge badge-primary" style="font-size: 1rem; padding: 8px 20px; border-radius: 20px;">
                                        <i class="mdi mdi-file-document-outline me-1"></i>
                                        Total: ${count} ${count == 1 ? 'Permohonan' : 'Permohonan'}
                                    </span>
                                </div>
                                <div class="table-responsive" style="max-height: 450px; overflow-y: auto; border-radius: 8px;">
                                    <table class="table table-hover table-bordered mb-0" style="font-size: 0.9rem;">
                                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                                            <tr>
                                                <th class="text-center" style="width: 50px; background-color: #e9ecef;">No</th>
                                                <th style="background-color: #e9ecef;">No. Pengajuan</th>
                                                <th style="background-color: #e9ecef;">Nama Pemohon</th>
                                                <th style="background-color: #e9ecef;">Divisi</th>
                                                <th class="text-center" style="background-color: #e9ecef;">Status</th>
                                                <th style="background-color: #e9ecef;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${tableRows}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        // Update modal with table
                        Swal.fire({
                            title: '<i class="mdi mdi-file-document-outline me-2"></i>',
                            html: content,
                            showCloseButton: true,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="mdi mdi-close me-1"></i>Tutup',
                            confirmButtonColor: '#6c757d',
                            width: '900px',
                            customClass: {
                                popup: 'rounded-lg',
                                title: 'text-left mb-0',
                                confirmButton: 'btn btn-secondary'
                            }
                        });
                    });
            }

            // Initialize DataTables
            let formsTable, overtimeTable, vehicleTable, assetTable, splTable, approvalHistoryTable;

            function initializeDataTables() {
                // Destroy existing DataTables if they exist
                if ($.fn.DataTable.isDataTable('#formsTable')) {
                    $('#formsTable').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable('#overtimeTable')) {
                    $('#overtimeTable').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable('#vehicleTable')) {
                    $('#vehicleTable').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable('#assetTable')) {
                    $('#assetTable').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable('#splTable')) {
                    $('#splTable').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable('#approvalHistoryTable')) {
                    $('#approvalHistoryTable').DataTable().destroy();
                }

                // Initialize Forms Table
                if ($('#formsTable').length && $('#filter-forms').is(':visible')) {
                    formsTable = $('#formsTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 5,
                        order: [
                            [4, 'desc']
                        ] // Sort by date created (column index 4)
                    });
                }

                // Initialize Overtime Table
                if ($('#overtimeTable').length && $('#filter-overtime').is(':visible')) {
                    overtimeTable = $('#overtimeTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 10,
                        order: [
                            [0, 'desc']
                        ] // Sort by date (column index 0)
                    });
                }

                // Initialize Vehicle Table
                if ($('#vehicleTable').length && $('#filter-vehicle').is(':visible')) {
                    vehicleTable = $('#vehicleTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 10,
                        order: [
                            [0, 'desc']
                        ] // Sort by date (column index 0)
                    });
                }

                // Initialize Asset Table
                if ($('#assetTable').length && $('#filter-asset').is(':visible')) {
                    assetTable = $('#assetTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 10,
                        order: [
                            [0, 'desc']
                        ] // Sort by date (column index 0)
                    });
                }

                // Initialize SPL Table
                if ($('#splTable').length && $('#filter-spl').is(':visible')) {
                    splTable = $('#splTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 10,
                        order: [
                            [1, 'desc']
                        ] // Sort by date (column index 1)
                    });
                }

                // Initialize Approval History Table
                if ($('#approvalHistoryTable').length) {
                    approvalHistoryTable = $('#approvalHistoryTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data",
                            zeroRecords: "Tidak ada data yang cocok"
                        },
                        pageLength: 5,
                        order: [
                            [6, 'desc']
                        ] // Sort by approval date (column index 6)
                    });
                }
            }

            // Department Absence Chart
            let doughnutChart = null;

            function loadDepartmentAbsenceChart() {
                const departmentId = document.getElementById('chartDepartment').value;
                const month = document.getElementById('chartMonth').value;

                // Show loading
                const btn = document.getElementById('loadChartBtn');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Memuat...';

                // Fetch data from API
                silentFetch(`{{ route('hr.requests.department-absence-stats') }}?department_id=${departmentId}&month=${month}`)
                    .then(result => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;

                        if (!result || !result.success || !result.data || result.data.length === 0) {
                            // No data message or fetch failed
                            if (doughnutChart) {
                                doughnutChart.destroy();
                                doughnutChart = null;
                            }
                            const canvas = document.getElementById('doughnutChart');
                            const noDataMessage = document.getElementById('chartNoDataMessage');
                            if (canvas) {
                                canvas.style.display = 'none';
                            }
                            if (noDataMessage) {
                                noDataMessage.style.display = 'block';
                            }
                            return;
                        }

                        // Destroy existing chart if exists
                        if (doughnutChart) {
                            doughnutChart.destroy();
                        }

                        // Show canvas and hide no data message
                        const canvas = document.getElementById('doughnutChart');
                        const noDataMessage = document.getElementById('chartNoDataMessage');
                        if (canvas) {
                            canvas.style.display = 'block';
                        }
                        if (noDataMessage) {
                            noDataMessage.style.display = 'none';
                        }

                        // Prepare data
                        const labels = result.data.map(item => item.department);
                        const counts = result.data.map(item => item.count);

                        // Generate colors
                        const colors = generateColors(labels.length);

                        // Create chart
                        const ctx = document.getElementById('doughnutChart').getContext('2d');
                        doughnutChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Jumlah Izin',
                                    data: counts,
                                    backgroundColor: colors.backgrounds,
                                    borderColor: colors.borders,
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 15,
                                            usePointStyle: true
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            title: function(context) {
                                                return context[0].label || '';
                                            },
                                            label: function(context) {
                                                const department = context.label || '';
                                                const total = context.parsed || 0;
                                                return [
                                                    'Department: ' + department,
                                                    'Total Izin: ' + total
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
            }

            function generateColors(count) {
                const colorPalette = [
                    '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                    '#1abc9c', '#34495e', '#e67e22', '#16a085', '#c0392b',
                    '#27ae60', '#8e44ad', '#2980b9', '#d35400', '#7f8c8d'
                ];

                const backgrounds = [];
                const borders = [];

                for (let i = 0; i < count; i++) {
                    const color = colorPalette[i % colorPalette.length];
                    backgrounds.push(color);
                    borders.push(color);
                }

                return {
                    backgrounds,
                    borders
                };
            }

            // Tab handling for calendar and DataTables initialization
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTables on page load
                initializeDataTables();

                // Initialize chart on page load
                loadDepartmentAbsenceChart();

                // Load chart button click
                document.getElementById('loadChartBtn').addEventListener('click', loadDepartmentAbsenceChart);

                // Add click listeners to all tab buttons
                document.querySelectorAll('[data-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('click', function() {
                        const target = this.getAttribute('data-target');
                        if (target === '#calendar') {
                            setTimeout(() => {
                                if (!window.calendarInitialized) {
                                    initializeCalendar();
                                }
                            }, 100);
                        } else if (target === '#requests') {
                            // Re-initialize DataTables when switching to requests tab
                            setTimeout(() => {
                                initializeDataTables();
                            }, 100);
                        } else if (target === '#approval-history') {
                            // Initialize approval history table when switching to approval history tab
                            setTimeout(() => {
                                if ($('#approvalHistoryTable').length && !$.fn.DataTable.isDataTable('#approvalHistoryTable')) {
                                    initializeDataTables();
                                }
                            }, 100);
                        }
                    });
                });
            });
        </script>
    @endsection

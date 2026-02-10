@extends('main.layouts.main')
@section('title')
    Data Plan
@endsection
@section('css')
    <link href="{{ asset('public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        /* Timeline Styles */
        .timeline-wrapper {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            overflow: hidden;
        }

        .timeline-header {
            display: flex;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: bold;
            color: #5a5c69;
        }

        .timeline-time-header {
            width: 200px;
            padding: 12px 15px;
            border-right: 1px solid #e3e6f0;
        }

        .timeline-project-header {
            flex: 1;
            padding: 12px 15px;
            border-right: 1px solid #e3e6f0;
        }

        .timeline-status-header {
            width: 120px;
            padding: 12px 15px;
        }

        .timeline-content {
            max-height: 600px;
            overflow-y: auto;
        }

        .timeline-item {
            display: flex;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.2s;
        }

        .timeline-item:hover {
            background-color: #f8f9fc;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-time {
            width: 200px;
            padding: 15px;
            border-right: 1px solid #e3e6f0;
            background-color: #fff;
        }

        .timeline-project {
            flex: 1;
            padding: 15px;
            border-right: 1px solid #e3e6f0;
            background-color: #fff;
        }

        .timeline-status {
            width: 120px;
            padding: 15px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline-time-start {
            font-weight: bold;
            color: #1cc88a;
            font-size: 0.9rem;
        }

        .timeline-time-end {
            font-size: 0.8rem;
            color: #858796;
            margin-top: 2px;
        }

        .timeline-time-date {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
            font-style: italic;
        }

        .timeline-project-title {
            font-weight: bold;
            color: #5a5c69;
            margin-bottom: 5px;
        }

        .timeline-project-details {
            font-size: 0.85rem;
            color: #858796;
        }

        .timeline-project-customer {
            color: #4e73df;
            font-weight: 500;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-assigned {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-finished {
            background-color: #d4edda;
            color: #155724;
        }

        .status-approved {
            background-color: #cce5ff;
            color: #004085;
        }

        .timeline-empty {
            text-align: center;
            padding: 40px 20px;
            color: #858796;
            font-style: italic;
        }

        .timeline-loading {
            text-align: center;
            padding: 40px 20px;
            color: #858796;
        }

        .timeline-loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
@section('page-title')
    Data Plan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Task Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Task</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <input type="text" id="true_ppic" value="{{ $truePPic }}" style="display: none;">
                <input type="text" id="true_prepress" value="{{ $truePrepress }}" style="display: none;">
                <input type="text" id="head_divisi" value="{{ $headDivisi }}" style="display: none;">

                @if ($trueAdmin)
                    <div class="card">

                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                        <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Buat Plan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#listplan" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">List Plan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#listtask" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">List Task</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link">
                                        <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Timeline Task</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Report Job</span>
                                    </a>
                                </li>

                            </ul>

                            <div class="tab-content">
                                {{-- untuk TIMELINE --}}
                                <div class="tab-pane" id="profile">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="timeline_date">Filter Tanggal (Opsional):</label>
                                            <input type="date" id="timeline_date" class="form-control"
                                                placeholder="Kosongkan untuk tampilkan semua">
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="load_timeline">
                                                <i class="mdi mdi-refresh"></i> Load Timeline
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-secondary btn-block" id="clear_filter">
                                                <i class="mdi mdi-close"></i> Clear Filter
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Timeline Project Prepress</h5>
                                            <div id="timeline_container">
                                                <div class="timeline-wrapper">
                                                    <div class="timeline-header">
                                                        <div class="timeline-time-header">Waktu</div>
                                                        <div class="timeline-project-header">Project</div>
                                                        <div class="timeline-status-header">Status</div>
                                                    </div>
                                                    <div id="timeline_content" class="timeline-content">
                                                        <!-- Timeline items will be loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- untuk REPORT --}}
                                <div class="tab-pane" id="settings">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="report_start_date">Tanggal Mulai:</label>
                                            <input type="date" id="report_start_date" class="form-control"
                                                value="{{ date('Y-m-01') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="report_end_date">Tanggal Akhir:</label>
                                            <input type="date" id="report_end_date" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="load_report">
                                                <i class="mdi mdi-refresh"></i> Load Report
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-success btn-block" id="export_excel">
                                                <i class="mdi mdi-file-excel"></i> Export Excel
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Report Job Prepress (APPROVED/FINISH)</h5>
                                            <div class="table-responsive">
                                                <table id="datatable-report-prepress"
                                                    class="table table-striped table-bordered"
                                                    style="width: 100%; font-size:14px;">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal Job</th>
                                                            <th>Deadline</th>
                                                            <th>Customer</th>
                                                            <th>Product</th>
                                                            <th>Kode Design</th>
                                                            <th>Job Order</th>
                                                            <th>Qty Order</th>
                                                            <th>Status</th>
                                                            <th>Prioritas</th>
                                                            <th>Estimasi (Menit)</th>
                                                            <th>Received At</th>
                                                            <th>Received By</th>
                                                            <th>Created By</th>
                                                            <th>Created At</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Untuk Prepress ADMIN --}}
                                <div class="tab-pane" id="listplan">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3 mb-2 mb-sm-0">
                                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                                    aria-orientation="vertical">
                                                    <a class="nav-link active show" id="v-pills-home-tab"
                                                        data-toggle="pill" href="#v-pills-home" role="tab"
                                                        aria-controls="v-pills-home" aria-selected="true">
                                                        <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">List Plan</span>
                                                    </a>
                                                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                                                        href="#v-pills-profile" role="tab"
                                                        aria-controls="v-pills-profile" aria-selected="false">
                                                        <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">List Task</span>
                                                    </a>
                                                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
                                                        href="#v-pills-settings" role="tab"
                                                        aria-controls="v-pills-settings" aria-selected="false">
                                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">Petunjuk Pengerjaan</span>
                                                    </a>
                                                </div>
                                            </div> <!-- end col-->

                                            <div class="col-sm-9">
                                                <div class="tab-content" id="v-pills-tabContent">
                                                    <div class="tab-pane fade active show" id="v-pills-home"
                                                        role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                        <div class="table-responsive">
                                                            <table id="datatable-list-plan-prepress"
                                                                class="table table-responsive-md"
                                                                style="width: 100%; font-size:14px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Deadline</th>
                                                                        <th>Customer</th>
                                                                        <th>Product</th>
                                                                        <th style="white-space: nowrap;">Qty Order</th>
                                                                        <th style="white-space: nowrap;">Job Order</th>
                                                                        <th>Data</th>
                                                                        <th>Prioritas</th>
                                                                        <th>Status Job</th>
                                                                        <th style="white-space: nowrap;">Created By
                                                                        </th>
                                                                        <th style="white-space: nowrap;">Created At
                                                                        </th>
                                                                        <th style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                                        aria-labelledby="v-pills-profile-tab">
                                                        <div class="table-responsive" id="table-plan-assign-prepress">
                                                            <table id="datatable-list-plan-assign-prepress"
                                                                class="table table-responsive-md"
                                                                style="width: 100%; font-size:14px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Deadline</th>
                                                                        <th>Job</th>
                                                                        <th>Job Title</th>
                                                                        <th>Customer</th>
                                                                        <th>Status</th>
                                                                        <th style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="card" id="card-detail-prepress"
                                                            style="display: none;">
                                                            <div class="card-body">

                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                                        aria-labelledby="v-pills-settings-tab">
                                                        <p class="mb-0">Food truck quinoa dolor sit amet,
                                                            consectetuer
                                                            adipiscing elit. Aenean commodo ligula eget dolor. Aenean
                                                            massa.
                                                            Cum
                                                            sociis
                                                            natoque penatibus et magnis dis parturient montes, nascetur
                                                            ridiculus mus. Donec quam felis, ultricies nec, pellentesque
                                                            eu, pretium quis, sem. Nulla consequat massa quis enim.
                                                            Cillum
                                                            ad ut
                                                            irure tempor velit nostrud occaecat ullamco
                                                            aliqua anim Leggings sint. Veniam sint duis incididunt do
                                                            esse
                                                            magna
                                                            mollit excepteur laborum qui.</p>
                                                    </div>
                                                </div> <!-- end tab-content-->
                                            </div> <!-- end col-->
                                        </div>
                                        <!-- end row-->
                                    </div>
                                </div>

                                {{-- Untuk Prepress PIC --}}
                                <div class="tab-pane" id="listtask">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3 mb-2 mb-sm-0">
                                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                                    aria-orientation="vertical">
                                                    <a class="nav-link active show" id="v-pills-home-tab"
                                                        data-toggle="pill" href="#v-pills-home" role="tab"
                                                        aria-controls="v-pills-home" aria-selected="true">
                                                        <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">List Plan</span>
                                                    </a>
                                                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                                                        href="#v-pills-profile" role="tab"
                                                        aria-controls="v-pills-profile" aria-selected="false">
                                                        <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">List Task</span>
                                                    </a>
                                                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
                                                        href="#v-pills-settings" role="tab"
                                                        aria-controls="v-pills-settings" aria-selected="false">
                                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                                        <span class="d-none d-lg-block">Petunjuk Pengerjaan</span>
                                                    </a>
                                                </div>
                                            </div> <!-- end col-->

                                            <div class="col-sm-9">
                                                <div class="tab-content" id="v-pills-tabContent">
                                                    <div class="tab-pane fade active show" id="v-pills-home"
                                                        role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                        <div class="table-responsive">
                                                            <table id="datatable-list-plan-prepress"
                                                                class="table table-responsive-md"
                                                                style="width: 100%; font-size:14px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Deadline</th>
                                                                        <th>Customer</th>
                                                                        <th>Product</th>
                                                                        <th style="white-space: nowrap;">Qty Order</th>
                                                                        <th style="white-space: nowrap;">Job Order</th>
                                                                        <th>Data</th>
                                                                        <th>Prioritas</th>
                                                                        <th>Status Job</th>
                                                                        <th style="white-space: nowrap;">Created By
                                                                        </th>
                                                                        <th style="white-space: nowrap;">Created At
                                                                        </th>
                                                                        <th style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                                        aria-labelledby="v-pills-profile-tab">
                                                        <div class="table-responsive" id="table-plan-assign-prepress">
                                                            <table id="datatable-list-plan-assign-prepress-new"
                                                                class="table table-responsive-md"
                                                                style="width: 100%; font-size:14px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:5%;">#</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Deadline</th>
                                                                        <th>Job</th>
                                                                        <th>Job Title</th>
                                                                        <th>Customer</th>
                                                                        <th>Status</th>
                                                                        <th style="width:10%;">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="card" id="card-detail-prepress"
                                                            style="display: none;">
                                                            <div class="card-body">

                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                                        aria-labelledby="v-pills-settings-tab">
                                                        <p class="mb-0">Food truck quinoa dolor sit amet,
                                                            consectetuer
                                                            adipiscing elit. Aenean commodo ligula eget dolor. Aenean
                                                            massa.
                                                            Cum
                                                            sociis
                                                            natoque penatibus et magnis dis parturient montes, nascetur
                                                            ridiculus mus. Donec quam felis, ultricies nec, pellentesque
                                                            eu, pretium quis, sem. Nulla consequat massa quis enim.
                                                            Cillum
                                                            ad ut
                                                            irure tempor velit nostrud occaecat ullamco
                                                            aliqua anim Leggings sint. Veniam sint duis incididunt do
                                                            esse
                                                            magna
                                                            mollit excepteur laborum qui.</p>
                                                    </div>
                                                </div> <!-- end tab-content-->
                                            </div> <!-- end col-->
                                        </div>
                                        <!-- end row-->
                                    </div>
                                </div>

                                <div class="tab-pane active" id="home">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p>Pilih Tanggal Plan : </p>
                                            <input type="date" name="selected_date" id="selected_date"
                                                class="form-control mb-3" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <p>&nbsp;</p>
                                            <button type="button" class="btn btn-primary" id="submit-plan-prepress">Buat
                                                Plan</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="table-responsive">
                                        <table id="datatable-job-order-prepress-for-plan"
                                            class="table table-responsive-md" style="width: 100%; font-size:14px;">
                                            <thead>
                                                <tr>
                                                    <th style="width:5%;">#</th>
                                                    <th>Tanggal</th>
                                                    <th>Deadline</th>
                                                    <th>Customer</th>
                                                    <th>Product</th>
                                                    <th style="white-space: nowrap;">Qty Order</th>
                                                    <th style="white-space: nowrap;">Job Order</th>
                                                    <th>Data</th>
                                                    <th>Prioritas</th>
                                                    <th>Status Job</th>
                                                    <th style="white-space: nowrap;">Created By</th>
                                                    <th style="white-space: nowrap;">Created At</th>
                                                    {{-- <th style="width:10%;">Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @else
                    @if (!$truePPic && !$truePrepress)
                        <div class="card">

                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="timeline_date">Filter Tanggal (Opsional):</label>
                                        <input type="date" id="timeline_date" class="form-control"
                                            placeholder="Kosongkan untuk tampilkan semua">
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-block" id="load_timeline">
                                            <i class="mdi mdi-refresh"></i> Load Timeline
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-secondary btn-block" id="clear_filter">
                                            <i class="mdi mdi-close"></i> Clear Filter
                                        </button>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Timeline Project Prepress</h5>
                                        <div id="timeline_container">
                                            <div class="timeline-wrapper">
                                                <div class="timeline-header">
                                                    <div class="timeline-time-header">Waktu</div>
                                                    <div class="timeline-project-header">Project</div>
                                                    <div class="timeline-status-header">Status</div>
                                                </div>
                                                <div id="timeline_content" class="timeline-content">
                                                    <!-- Timeline items will be loaded here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @elseif ($truePPic)
                        <div class="card">

                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3">
                                    <li class="nav-item">
                                        <a href="#home" data-toggle="tab" aria-expanded="false"
                                            class="nav-link active">
                                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Buat Plan</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Timeline Plan</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="home">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p>Pilih Tanggal Plan : </p>
                                                <input type="date" name="selected_date" id="selected_date"
                                                    class="form-control mb-3" value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <p>&nbsp;</p>
                                                <button type="button" class="btn btn-primary"
                                                    id="submit-plan-prepress">Buat
                                                    Plan</button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id="datatable-job-order-prepress-for-plan"
                                                class="table table-responsive-md" style="width: 100%; font-size:14px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Tanggal</th>
                                                        <th>Deadline</th>
                                                        <th>Customer</th>
                                                        <th>Product</th>
                                                        <th style="white-space: nowrap;">Qty Order</th>
                                                        <th style="white-space: nowrap;">Job Order</th>
                                                        <th>Data</th>
                                                        <th>Prioritas</th>
                                                        <th>Status Job</th>
                                                        <th style="white-space: nowrap;">Created By</th>
                                                        <th style="white-space: nowrap;">Created At</th>
                                                        {{-- <th style="width:10%;">Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="profile">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="timeline_date">Filter Tanggal (Opsional):</label>
                                                <input type="date" id="timeline_date" class="form-control"
                                                    placeholder="Kosongkan untuk tampilkan semua">
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block"
                                                    id="load_timeline">
                                                    <i class="mdi mdi-refresh"></i> Load Timeline
                                                </button>
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-secondary btn-block"
                                                    id="clear_filter">
                                                    <i class="mdi mdi-close"></i> Clear Filter
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Timeline Project Prepress</h5>
                                                <div id="timeline_container">
                                                    <div class="timeline-wrapper">
                                                        <div class="timeline-header">
                                                            <div class="timeline-time-header">Waktu</div>
                                                            <div class="timeline-project-header">Project</div>
                                                            <div class="timeline-status-header">Status</div>
                                                        </div>
                                                        <div id="timeline_content" class="timeline-content">
                                                            <!-- Timeline items will be loaded here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @elseif ($truePrepress)
                        <div class="card">

                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3">
                                    <li class="nav-item">
                                        <a href="#listplan" data-toggle="tab" aria-expanded="false"
                                            class="nav-link active">
                                            <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">List Plan Prepress</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Timeline Plan</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                            <span class="d-none d-lg-block">Report Job</span>
                                        </a>
                                    </li>

                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane" id="profile">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="timeline_date">Filter Tanggal (Opsional):</label>
                                                <input type="date" id="timeline_date" class="form-control"
                                                    placeholder="Kosongkan untuk tampilkan semua">
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block"
                                                    id="load_timeline">
                                                    <i class="mdi mdi-refresh"></i> Load Timeline
                                                </button>
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-secondary btn-block"
                                                    id="clear_filter">
                                                    <i class="mdi mdi-close"></i> Clear Filter
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Timeline Project Prepress</h5>
                                                <div id="timeline_container">
                                                    <div class="timeline-wrapper">
                                                        <div class="timeline-header">
                                                            <div class="timeline-time-header">Waktu</div>
                                                            <div class="timeline-project-header">Project</div>
                                                            <div class="timeline-status-header">Status</div>
                                                        </div>
                                                        <div id="timeline_content" class="timeline-content">
                                                            <!-- Timeline items will be loaded here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="settings">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="report_start_date">Tanggal Mulai:</label>
                                                <input type="date" id="report_start_date" class="form-control"
                                                    value="{{ date('Y-m-01') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="report_end_date">Tanggal Akhir:</label>
                                                <input type="date" id="report_end_date" class="form-control"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block"
                                                    id="load_report">
                                                    <i class="mdi mdi-refresh"></i> Load Report
                                                </button>
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-success btn-block"
                                                    id="export_excel">
                                                    <i class="mdi mdi-file-excel"></i> Export Excel
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Report Job Prepress (APPROVED/FINISH)</h5>
                                                <div class="table-responsive">
                                                    <table id="datatable-report-prepress"
                                                        class="table table-striped table-bordered"
                                                        style="width: 100%; font-size:14px;">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Job</th>
                                                                <th>Deadline</th>
                                                                <th>Customer</th>
                                                                <th>Product</th>
                                                                <th>Kode Design</th>
                                                                <th>Job Order</th>
                                                                <th>Qty Order</th>
                                                                <th>Status</th>
                                                                <th>Prioritas</th>
                                                                <th>Estimasi (Menit)</th>
                                                                <th>Received At</th>
                                                                <th>Received By</th>
                                                                <th>Created By</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane active" id="listplan">
                                        @if ($headDivisi)
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3 mb-2 mb-sm-0">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                            role="tablist" aria-orientation="vertical">
                                                            <a class="nav-link active show" id="v-pills-home-tab"
                                                                data-toggle="pill" href="#v-pills-home" role="tab"
                                                                aria-controls="v-pills-home" aria-selected="true">
                                                                <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                                                <span class="d-none d-lg-block">List Plan</span>
                                                            </a>
                                                            <a class="nav-link" id="v-pills-profile-tab"
                                                                data-toggle="pill" href="#v-pills-profile" role="tab"
                                                                aria-controls="v-pills-profile" aria-selected="false">
                                                                <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                                                <span class="d-none d-lg-block">List Task</span>
                                                            </a>
                                                            <a class="nav-link" id="v-pills-settings-tab"
                                                                data-toggle="pill" href="#v-pills-settings"
                                                                role="tab" aria-controls="v-pills-settings"
                                                                aria-selected="false">
                                                                <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                                                <span class="d-none d-lg-block">Petunjuk Pengerjaan</span>
                                                            </a>
                                                        </div>
                                                    </div> <!-- end col-->

                                                    <div class="col-sm-9">
                                                        <div class="tab-content" id="v-pills-tabContent">
                                                            <div class="tab-pane fade active show" id="v-pills-home"
                                                                role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                                <div class="table-responsive">
                                                                    <table id="datatable-list-plan-prepress"
                                                                        class="table table-responsive-md"
                                                                        style="width: 100%; font-size:14px;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width:5%;">#</th>
                                                                                <th>Tanggal</th>
                                                                                <th>Deadline</th>
                                                                                <th>Customer</th>
                                                                                <th>Product</th>
                                                                                <th style="white-space: nowrap;">Qty Order
                                                                                </th>
                                                                                <th style="white-space: nowrap;">Job Order
                                                                                </th>
                                                                                <th>Data</th>
                                                                                <th>Prioritas</th>
                                                                                <th>Status Job</th>
                                                                                <th style="white-space: nowrap;">Created By
                                                                                </th>
                                                                                <th style="white-space: nowrap;">Created At
                                                                                </th>
                                                                                <th style="width:10%;">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="v-pills-profile"
                                                                role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                                <div class="table-responsive"
                                                                    id="table-plan-assign-prepress">
                                                                    <table id="datatable-list-plan-assign-prepress"
                                                                        class="table table-responsive-md"
                                                                        style="width: 100%; font-size:14px;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width:5%;">#</th>
                                                                                <th>Tanggal</th>
                                                                                <th>Deadline</th>
                                                                                <th>Job</th>
                                                                                <th>Job Title</th>
                                                                                <th>Customer</th>
                                                                                <th>Status</th>
                                                                                <th style="width:10%;">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="card" id="card-detail-prepress"
                                                                    style="display: none;">
                                                                    <div class="card-body">

                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="v-pills-settings"
                                                                role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                                <p class="mb-0">Food truck quinoa dolor sit amet,
                                                                    consectetuer
                                                                    adipiscing elit. Aenean commodo ligula eget dolor.
                                                                    Aenean
                                                                    massa.
                                                                    Cum
                                                                    sociis
                                                                    natoque penatibus et magnis dis parturient montes,
                                                                    nascetur
                                                                    ridiculus mus. Donec quam felis, ultricies nec,
                                                                    pellentesque
                                                                    eu, pretium quis, sem. Nulla consequat massa quis enim.
                                                                    Cillum
                                                                    ad ut
                                                                    irure tempor velit nostrud occaecat ullamco
                                                                    aliqua anim Leggings sint. Veniam sint duis incididunt
                                                                    do
                                                                    esse
                                                                    magna
                                                                    mollit excepteur laborum qui.</p>
                                                            </div>
                                                        </div> <!-- end tab-content-->
                                                    </div> <!-- end col-->
                                                </div>
                                                <!-- end row-->
                                            </div>
                                        @else
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3 mb-2 mb-sm-0">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                            role="tablist" aria-orientation="vertical">
                                                            <a class="nav-link active show" id="v-pills-profile-tab"
                                                                data-toggle="pill" href="#v-pills-profile" role="tab"
                                                                aria-controls="v-pills-profile" aria-selected="false">
                                                                <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                                                <span class="d-none d-lg-block">List Task</span>
                                                            </a>
                                                            <a class="nav-link" id="v-pills-settings-tab"
                                                                data-toggle="pill" href="#v-pills-settings"
                                                                role="tab" aria-controls="v-pills-settings"
                                                                aria-selected="false">
                                                                <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                                                <span class="d-none d-lg-block">Petunjuk Pengerjaan</span>
                                                            </a>
                                                        </div>
                                                    </div> <!-- end col-->

                                                    <div class="col-sm-9">
                                                        <div class="tab-content" id="v-pills-tabContent">
                                                            <div class="tab-pane fade active show" id="v-pills-profile"
                                                                role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                                <div class="table-responsive"
                                                                    id="table-plan-assign-prepress">
                                                                    <table id="datatable-list-plan-assign-prepress"
                                                                        class="table table-responsive-md"
                                                                        style="width: 100%; font-size:14px;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width:5%;">#</th>
                                                                                <th>Tanggal</th>
                                                                                <th>Deadline</th>
                                                                                <th>Job</th>
                                                                                <th>Job Title</th>
                                                                                <th>Customer</th>
                                                                                <th>Status</th>
                                                                                <th style="width:10%;">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="card" id="card-detail-prepress"
                                                                    style="display: none;">
                                                                    <div class="card-body">

                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="v-pills-settings"
                                                                role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                                <p class="mb-0">Food truck quinoa dolor sit amet,
                                                                    consectetuer
                                                                    adipiscing elit. Aenean commodo ligula eget dolor.
                                                                    Aenean
                                                                    massa.
                                                                    Cum
                                                                    sociis
                                                                    natoque penatibus et magnis dis parturient montes,
                                                                    nascetur
                                                                    ridiculus mus. Donec quam felis, ultricies nec,
                                                                    pellentesque
                                                                    eu, pretium quis, sem. Nulla consequat massa quis enim.
                                                                    Cillum
                                                                    ad ut
                                                                    irure tempor velit nostrud occaecat ullamco
                                                                    aliqua anim Leggings sint. Veniam sint duis incididunt
                                                                    do
                                                                    esse
                                                                    magna
                                                                    mollit excepteur laborum qui.</p>
                                                            </div>
                                                        </div> <!-- end tab-content-->
                                                    </div> <!-- end col-->
                                                </div>
                                                <!-- end row-->
                                            </div>
                                        @endif

                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                @endif

            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="submitAssignJob" id="submitAssignJob">
                    @csrf
                    <input type="hidden" name="id_plan" id="id_plan">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Assign Job</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_job" id="id_job" class="form-control" hidden>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal Job</label>
                                        <input type="date" name="tanggal_job" id="tanggal_job" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal Deadline</label>
                                        <input type="date" name="tanggal_deadline" id="tanggal_deadline"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Customer</label>
                                <input type="text" name="customer" id="customer" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Product</label>
                                <input type="text" name="product" id="product" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Qty Order</label>
                                <input type="number" name="qty_order_estimation" id="qty_order_estimation"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Job Order</label>
                                <input type="text" name="job_order" id="job_order" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">File Data</label>
                                <input type="text" name="file_data" id="file_data" class="form-control"
                                    placeholder="Masukkan nama file, pisahkan dengan koma jika ada lebih dari satu file">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prioritas</label>
                                <select name="prioritas_job" id="prioritas_job" class="form-control" required>
                                    <option value disabled selected>-- Pilih Prioritas --</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Normal">Normal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" id="assigned_to" class="form-control" required>
                                    <option value disabled selected>-- Pilih PIC --</option>
                                    @foreach ($pic as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label class="form-label">Kategori Job</label>
                                <select name="kategori_job" id="kategori_job" class="form-control" required>
                                    <option value disabled selected>-- Pilih Kategori Job --</option>
                                    @foreach ($masterData as $item)
                                        <option value="{{ $item->kode }}">{{ $item->kode }} -
                                            {{ $item->keterangan_job }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                id="close_button">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('public/new/assets/pages/datatables-demo.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-job-order-prepress-for-plan').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.job-order-plan.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            // checkbox
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" name="select-job-order-ppic[]" class="select-job-order" value="' +
                                    full.id + '" id="select-job-order-' + full.id +
                                    '"><label for="select-job-order-' + full.id + '">&nbsp;</label>';
                            }
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'product',
                            name: 'product',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'file_data',
                            name: 'file_data',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                let files = [];
                                if (typeof data === 'string') {
                                    try {
                                        files = JSON.parse(data);
                                    } catch (e) {
                                        files = [];
                                    }
                                } else if (Array.isArray(data)) {
                                    files = data;
                                }
                                if (!files.length) return '-';
                                // Tampilkan sebagai list
                                return '<ul style="padding-left:18px; margin-bottom:0;">' + files.map(
                                    f => `<li>${f}</li>`).join('') + '</ul>';
                            },
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                console.log(data);
                                return data == 'Urgent' ?
                                    '<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>' :
                                    '<button type="button" class="btn btn-success btn-sm" data-sodocno="${row.id}" data-status="Normal">Normal</button>';
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return `<button type="button" class="btn btn-info btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });

                var datatable2 = $('#datatable-list-plan-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.plan-selected.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            // checkbox
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" name="select-job-order[]" id="select-job-order-plan-' +
                                    full.id + '" class="select-job-order-plan" value="' +
                                    full.id + '"><label for="select-job-order-plan-' + full.id +
                                    '">&nbsp;</label>';
                            }
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'product',
                            name: 'product',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'file_data',
                            name: 'file_data',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                let files = [];
                                if (typeof data === 'string') {
                                    try {
                                        files = JSON.parse(data);
                                    } catch (e) {
                                        files = [];
                                    }
                                } else if (Array.isArray(data)) {
                                    files = data;
                                }
                                if (!files.length) return '-';
                                // Tampilkan sebagai list
                                return '<ul style="padding-left:18px; margin-bottom:0;">' + files.map(
                                    f => `<li>${f}</li>`).join('') + '</ul>';
                            },
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                console.log(data);
                                return data == 'Urgent' ?
                                    '<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>' :
                                    '<button type="button" class="btn btn-success btn-sm" data-sodocno="${row.id}" data-status="Normal">Normal</button>';
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return `<button type="button" class="btn btn-info btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'

                        }
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });

                var datatable3 = $('#datatable-list-plan-assign-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.plan-assigned.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'job_title',
                            name: 'job_title'
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'

                        }
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });

                var datatable4 = $('#datatable-list-plan-assign-prepress-new').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.plan-assigned.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'job_title',
                            name: 'job_title'
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'

                        }
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });

                $(document).on('click', '#submit-plan-prepress', function() {
                    // Ambil semua checkbox yang dicentang
                    let selectedIds = [];
                    $('#datatable-job-order-prepress-for-plan tbody input.select-job-order:checked').each(
                        function() {
                            selectedIds.push($(this).val());
                        });

                    let selectedDate = $('#selected_date').val();

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih data!',
                            text: 'Silakan centang minimal satu job order untuk membuat plan.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    $.ajax({
                        url: "{{ route('prepress.job-order.submit-plan') }}",
                        type: "POST",
                        data: {
                            selected_ids: selectedIds,
                            selected_date: selectedDate,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // Refresh datatable jika perlu
                            $('#datatable-job-order-prepress-for-plan').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                $('body').on('click', '.assign-job', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('prepress.job-order.assign-job', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);

                    // opened modal
                    $('#exampleModal').modal('show');

                    $.get(url, function(data) {
                        // $('#exampleModal').modal('hide');
                        $('#id_job').val(data.job_order.id);
                        // $('#nama_job').val(data.job_order.job_title);
                        $('#tanggal_job').val(data.job_order.tanggal_job_order);
                        $('#tanggal_deadline').val(data.job_order.tanggal_deadline);
                        $('#customer').val(data.job_order.customer);
                        $('#product').val(data.job_order.product);
                        $('#qty_order_estimation').val(data.job_order.qty_order_estimation);
                        $('#job_order').val(data.job_order.job_order);
                        // Cek apakah file_data adalah array atau string
                        let fileData = data.job_order.file_data;
                        if (Array.isArray(fileData)) {
                            $('#file_data').val(fileData.join(', '));
                        } else if (typeof fileData === 'string') {
                            // Coba parse jika string JSON
                            try {
                                let arr = JSON.parse(fileData);
                                if (Array.isArray(arr)) {
                                    $('#file_data').val(arr.join(', '));
                                } else {
                                    $('#file_data').val(fileData);
                                }
                            } catch (e) {
                                $('#file_data').val(fileData);
                            }
                        } else {
                            $('#file_data').val('');
                        }
                        // $('#file_data').val(data.job_order.file_data);
                        $('#prioritas_job').val(data.job_order.prioritas_job);
                        $('#status_job').val(data.job_order.status_job);
                        $('#catatan').val(data.job_order.catatan);
                        // $('#modal-body-detail-info-jo').html(data.message);
                    })

                });

                var routeAssignJob = "{{ route('prepress.job-order.assign-user') }}";

                $('#submitAssignJob').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    $.ajax({
                        url: routeAssignJob,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        },
                        success: function(data) {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(function() {
                                    datatable2.draw();
                                });
                                $('#submitAssignJob').trigger("reset");
                                $('#exampleModal').modal('hide');
                                $('#select-machines').show();
                                $('#name-machines').css('display', 'none');
                                // $('#modal-body-detail-info-jo').html(data.message);
                            } else if (data.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                                $('#modal-body-detail-info-jo').html(data.message);
                                $('#exampleModal').modal('hide');
                            } else {
                                $('#modal-body-detail-info-jo').html(data.message);
                            }
                        }
                    });
                });

                $(document).on('click', '.job-order-assign-detail', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('prepress.job-order.assign-job-data', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);
                    window.location.href = url;
                });

                // Timeline functionality
                function loadTimeline() {
                    var selectedDate = $('#timeline_date').val();
                    var timelineContent = $('#timeline_content');

                    // Show loading
                    timelineContent.html(
                        '<div class="timeline-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading timeline...</div>'
                    );

                    $.ajax({
                        url: "{{ route('prepress.timeline.data') }}",
                        type: "POST",
                        data: {
                            date: selectedDate || null,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                renderTimeline(response.data);
                            } else {
                                timelineContent.html(
                                    '<div class="timeline-empty">Tidak ada data untuk tanggal yang dipilih</div>'
                                );
                            }
                        },
                        error: function() {
                            timelineContent.html(
                                '<div class="timeline-empty">Error loading timeline data</div>');
                        }
                    });
                }

                function renderTimeline(data) {
                    var timelineContent = $('#timeline_content');

                    if (!data || data.length === 0) {
                        timelineContent.html('<div class="timeline-empty">Tidak ada data untuk ditampilkan</div>');
                        return;
                    }

                    var html = '';

                    data.forEach(function(item) {
                        var startTime = moment(item.start_time).format('HH:mm');
                        var endTime = moment(item.end_time).format('HH:mm');
                        var jobDate = moment(item.tanggal_job_order).format('DD/MM/YYYY');
                        var statusClass = getStatusClass(item.status_job);
                        var statusText = getStatusText(item.status_job);

                        html += `
                            <div class="timeline-item">
                                <div class="timeline-time">
                                    <div class="timeline-time-start">${startTime} - ${endTime}</div>
                                    <div class="timeline-time-end">${item.duration} menit</div>
                                    <div class="timeline-time-date">${jobDate}</div>
                                </div>
                                <div class="timeline-project">
                                    <div class="timeline-project-title">${item.kode_design} - ${item.product}</div>
                                    <div class="timeline-project-details">
                                        <span class="timeline-project-customer">${item.customer}</span> |
                                        Job Order: ${item.job_order} |
                                        Qty: ${item.qty_order_estimation}
                                    </div>
                                </div>
                                <div class="timeline-status">
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                </div>
                            </div>
                        `;
                    });

                    timelineContent.html(html);
                }

                function getStatusClass(status) {
                    switch (status) {
                        case 'ASSIGNED':
                            return 'status-assigned';
                        case 'IN PROGRESS':
                            return 'status-in-progress';
                        case 'FINISH':
                            return 'status-finished';
                        case 'APPROVED':
                            return 'status-approved';
                        default:
                            return 'status-assigned';
                    }
                }

                function getStatusText(status) {
                    switch (status) {
                        case 'ASSIGNED':
                            return 'Assigned';
                        case 'IN PROGRESS':
                            return 'In Progress';
                        case 'FINISH':
                            return 'Finished';
                        case 'APPROVED':
                            return 'Approved';
                        default:
                            return 'Assigned';
                    }
                }

                // Load timeline on page load
                $(document).ready(function() {
                    loadTimeline();
                });

                // Load timeline when button is clicked
                $(document).on('click', '#load_timeline', function() {
                    loadTimeline();
                });

                // Load timeline when date changes
                $(document).on('change', '#timeline_date', function() {
                    loadTimeline();
                });

                // Clear filter button
                $(document).on('click', '#clear_filter', function() {
                    $('#timeline_date').val('');
                    loadTimeline();
                });

                // Report functionality
                var reportDatatable = $('#datatable-report-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.report.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.start_date = $('#report_start_date').val();
                            d.end_date = $('#report_end_date').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'product',
                            name: 'product'
                        },
                        {
                            data: 'kode_design',
                            name: 'kode_design'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            render: function(data, type, row) {
                                var statusClass = data === 'APPROVED' ? 'btn-success' : 'btn-info';
                                return `<button type="button" class="btn ${statusClass} btn-sm">${data}</button>`;
                            }
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            render: function(data, type, row) {
                                var priorityClass = data === 'Urgent' ? 'btn-danger' : 'btn-success';
                                return `<button type="button" class="btn ${priorityClass} btn-sm">${data}</button>`;
                            }
                        },
                        {
                            data: 'est_job_default',
                            name: 'est_job_default',
                            render: function(data, type, row) {
                                return data ? data + ' menit' : '-';
                            }
                        },
                        {
                            data: 'received_at',
                            name: 'received_at',
                            render: function(data, type, row) {
                                return data ? moment(data).format('DD-MM-YYYY HH:mm') : '-';
                            }
                        },
                        {
                            data: 'received_by',
                            name: 'received_by',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ]
                });

                // Load report when button is clicked
                $(document).on('click', '#load_report', function() {
                    reportDatatable.ajax.reload();
                });

                // Export Excel functionality
                $(document).on('click', '#export_excel', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate || !endDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Tanggal!',
                            text: 'Silakan pilih tanggal mulai dan tanggal akhir untuk export.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    // Create download link
                    var url = "{{ route('prepress.report.export') }}";
                    var params = new URLSearchParams({
                        start_date: startDate,
                        end_date: endDate,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    });

                    // Create temporary form and submit
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.target = '_blank';

                    var tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = $('meta[name="csrf-token"]').attr('content');
                    form.appendChild(tokenInput);

                    var startDateInput = document.createElement('input');
                    startDateInput.type = 'hidden';
                    startDateInput.name = 'start_date';
                    startDateInput.value = startDate;
                    form.appendChild(startDateInput);

                    var endDateInput = document.createElement('input');
                    endDateInput.type = 'hidden';
                    endDateInput.name = 'end_date';
                    endDateInput.value = endDate;
                    form.appendChild(endDateInput);

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });

            });
        </script>
    @endsection

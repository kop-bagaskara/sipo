@extends('main.layouts.main')
@section('title')
    Data Plan
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
            max-width: 20%;
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

        /* Modal Styles - Clean Bootstrap 4 */
        .modal-dialog {
            max-width: 800px;
        }

        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
            font-size: 1.5rem;
            line-height: 1;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .form-label.fw-bold {
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            border-radius: 25px;
            padding: 10px 30px;
        }

        /* PIC Load Dashboard Styles */
        .pic-load-card {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .pic-load-card:hover {
            background: #f1f3f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }

        .user-details h5 {
            margin: 0;
            color: #2d3748;
            font-weight: 600;
        }

        .user-details p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }

        .progress-container {
            margin-bottom: 10px;
        }

        .progress {
            height: 12px;
            border-radius: 6px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 6px;
            transition: width 0.6s ease;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .progress-text {
            font-size: 14px;
            color: #4a5568;
            font-weight: 500;
        }

        .job-count {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
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
                <h3 class="text-themecolor">List Plan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">List Plan</li>
                </ol>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Load Distribution - PIC Prepress
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center" style="background: #f8f9fc; border: none;">
                                    <div class="card-body">
                                        <h3 class="text-primary" id="total-jobs">-</h3>
                                        <p class="text-muted mb-0">Total Active Jobs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center" style="background: #f8f9fc; border: none;">
                                    <div class="card-body">
                                        <h3 class="text-success" id="total-pic">-</h3>
                                        <p class="text-muted mb-0">Active PIC</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center" style="background: #f8f9fc; border: none;">
                                    <div class="card-body">
                                        <h3 class="text-warning" id="avg-load">-</h3>
                                        <p class="text-muted mb-0">Average Total Load</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center" style="background: #f8f9fc; border: none;">
                                    <div class="card-body">
                                        <h3 class="text-danger" id="high-load">-</h3>
                                        <p class="text-muted mb-0">High Load PIC</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PIC Load Cards -->
                        <div id="pic-load-container">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Loading PIC load data...</p>
                            </div>
                        </div>

                        <!-- Status Legend -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="d-flex align-items-center mr-4 mb-2">
                                        <div class="bg-warning rounded-circle mr-2" style="width: 12px; height: 12px;">
                                        </div>
                                        <small class="text-muted">Assigned</small>
                                    </div>
                                    <div class="d-flex align-items-center mr-4 mb-2">
                                        <div class="bg-primary rounded-circle mr-2" style="width: 12px; height: 12px;">
                                        </div>
                                        <small class="text-muted">In Progress</small>
                                    </div>
                                    <div class="d-flex align-items-center mr-4 mb-2">
                                        <div class="bg-success rounded-circle mr-2" style="width: 12px; height: 12px;">
                                        </div>
                                        <small class="text-muted">Finished</small>
                                    </div>
                                    <div class="d-flex align-items-center mr-4 mb-2">
                                        <div class="bg-info rounded-circle mr-2" style="width: 12px; height: 12px;"></div>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-danger rounded-circle mr-2" style="width: 12px; height: 12px;"></div>
                                        <small class="text-muted">Rejected</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3>List Plan Prepress</h3>
                            </div>
                            <div class="col d-flex justify-content-end" style="margin-bottom: 10px;">
                                <button class="btn btn-info" id="btn-work-order">Work Order</button>
                                <button class="btn btn-warning ml-2" id="btn-job-order">Job Order</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="datatable-list-plan-prepress" class="table table-responsive-md"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">No.</th>
                                        <th>No. Job Order</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Product</th>
                                        <th style="white-space: nowrap;">Job Order</th>
                                        <th>Prioritas</th>
                                        <th>Status Job</th>
                                        <th>PIC</th>
                                        <th style="width:10%;">Action</th>
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

        <!-- Modal Assign Job -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-lg">
                <form class="submitAssignJob" id="submitAssignJob">
                    @csrf
                    <input type="hidden" name="id_plan" id="id_plan">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Assign Job</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_job" id="id_job" class="form-control" hidden>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Tanggal Job</label>
                                        <input type="date" name="tanggal_job" id="tanggal_job" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Tanggal Deadline</label>
                                        <input type="date" name="tanggal_deadline" id="tanggal_deadline"
                                            class="form-control" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Customer</label>
                                        <input type="text" name="customer" id="customer" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Product</label>
                                        <input type="text" name="product" id="product" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Qty Order</label>
                                        <input type="number" name="qty_order_estimation" id="qty_order_estimation"
                                            class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Job Order</label>
                                        <input type="text" name="job_order" id="job_order" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">File Data</label>
                                <input type="text" name="file_data" id="file_data" class="form-control"
                                    placeholder="Masukkan nama file, pisahkan dengan koma jika ada lebih dari satu file"
                                    readonly>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Prioritas</label>
                                        <select name="prioritas_job" id="prioritas_job" class="form-control" required
                                            readonly>
                                            <option value disabled selected>-- Pilih Prioritas --</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Normal">Normal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Assigned To</label>
                                        <select name="assigned_to" id="assigned_to" class="form-control" required>
                                            <option value disabled selected>-- Pilih PIC --</option>
                                            @foreach ($pic as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3" readonly></textarea>
                            </div>
                            <hr>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Kategori Job</label>
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Work Order -->
        <div class="modal fade" id="exampleModalWO" tabindex="-1" aria-labelledby="exampleModalWOLabel">
            <div class="modal-dialog modal-lg">
                <form class="submitAssignWO" id="submitAssignWO">
                    @csrf
                    <input type="hidden" name="id_plan" id="id_plan">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalWOLabel">Assign Work Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_wo" id="id_wo" class="form-control" hidden>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Tanggal WO</label>
                                        <input type="date" name="tanggal_wo" id="tanggal_wo" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Tanggal Deadline</label>
                                        <input type="date" name="tanggal_deadline_wo" id="tanggal_deadline_wo"
                                            class="form-control" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Customer</label>
                                        <input type="text" name="customer_wo" id="customer_wo" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Product</label>
                                        <input type="text" name="product_wo" id="product_wo" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Qty Order</label>
                                        <input type="number" name="qty_order_estimation_wo" id="qty_order_estimation_wo"
                                            class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Job Order</label>
                                        <input type="text" name="job_order_wo" id="job_order_wo" class="form-control"
                                            required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">File Data</label>
                                <input type="text" name="file_data_wo" id="file_data_wo" class="form-control"
                                    placeholder="Masukkan nama file, pisahkan dengan koma jika ada lebih dari satu file"
                                    readonly>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Prioritas</label>
                                        <select name="prioritas_wo" id="prioritas_wo" class="form-control" required>
                                            <option value disabled selected>-- Pilih Prioritas --</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Normal">Normal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Assigned To</label>
                                        <select name="assigned_to_wo" id="assigned_to_wo" class="form-control" required>
                                            <option value disabled selected>-- Pilih PIC --</option>
                                            @foreach ($pic as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="catatan_wo" id="catatan_wo" class="form-control" rows="3" readonly></textarea>
                            </div>
                            <hr>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Kategori Job</label>
                                <select name="kategori_wo" id="kategori_wo" class="form-control" required>
                                    <option value disabled selected>-- Pilih Kategori Job --</option>
                                    @foreach ($masterData as $item)
                                        <option value="{{ $item->kode }}">{{ $item->kode }} -
                                            {{ $item->keterangan_job }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Reject Job -->
        <div class="modal fade" id="exampleModalReject" tabindex="-1" aria-labelledby="exampleModalRejectLabel">
            <div class="modal-dialog modal-lg">
                <form class="submitRejectJob" id="submitRejectJob">
                    @csrf
                    <input type="hidden" name="id_job_reject" id="id_job_reject">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalRejectLabel">Reject Job</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">No. Job Order</label>
                                <input type="text" name="no_job_order" id="no_job_order" class="form-control"
                                    readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Alasan Reject</label>
                                <textarea name="alasan_reject" id="alasan_reject" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detail Jobs PIC -->
        <div class="modal fade" id="modalDetailJobs" tabindex="-1" aria-labelledby="modalDetailJobsLabel">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailJobsLabel">
                            <i class="mdi mdi-animation"></i>
                            Detail Jobs - <span id="pic-name-detail"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Summary Card -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center bg-light">
                                    <div class="card-body">
                                        <h4 class="text-primary" id="total-jobs-detail">-</h4>
                                        <p class="text-muted mb-0">Total Jobs (Aktif + Selesai Hari Ini)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-warning">
                                    <div class="card-body">
                                        <h4 class="text-white" id="load-aktif-detail">-</h4>
                                        <p class="text-white mb-0">Load Aktif (menit)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-success">
                                    <div class="card-body">
                                        <h4 class="text-white" id="load-selesai-detail">-</h4>
                                        <p class="text-white mb-0">Selesai Hari Ini (menit)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-info">
                                    <div class="card-body">
                                        <h4 class="text-white" id="total-load-detail">-</h4>
                                        <p class="text-white mb-0">Total Load (menit)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jobs List -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table-detail-jobs">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Job Order</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Waktu Default</th>
                                        <th>Waktu Realtime</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-detail-jobs">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Loading State -->
                        <div id="loading-detail-jobs" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading detail jobs...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Load PIC load data
                loadPicLoadData();

                // Clean up modal backdrop when hidden
                $(document).on('hidden.bs.modal', '#exampleModal, #exampleModalWO', function() {
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                });

                // Clean up modal backdrop when hidden
                $(document).on('hidden.bs.modal', '#exampleModal, #exampleModalWO', function() {
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                });

                function initDataTable(url, csrfToken) {
                    return $('#datatable-list-plan-prepress').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: url,
                            type: "POST",
                            data: function(d) {
                                d._token = csrfToken;
                            },
                        },
                        rowGroup: {
                            dataSrc: 'MaterialCode'
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex'
                            },
                            {
                                data: 'nomor_job_order',
                                name: 'nomor_job_order',
                                render: function(data) {
                                    return `<span class="cust-col">${data}</span>`;
                                }
                            },
                            {
                                data: 'tanggal_job_order',
                                name: 'tanggal_job_order',
                                render: function(data) {
                                    return `<span class="cust-col">${moment(data).format('DD-MM-YYYY')}</span>`;
                                }
                            },
                            {
                                data: 'tanggal_deadline',
                                name: 'tanggal_deadline',
                                render: function(data) {
                                    return `<span class="cust-col">${moment(data).format('DD-MM-YYYY')}</span>`;
                                }
                            },
                            {
                                data: 'product',
                                name: 'product',
                                render: function(data) {
                                    return `<span>${data}</span>`;
                                }
                            },
                            {
                                data: 'job_order',
                                name: 'job_order'
                            },
                            {
                                data: 'prioritas_job',
                                name: 'prioritas_job',
                                className: 'cust-col',
                                render: function(data, type, row) {
                                    return data === 'Urgent' ?
                                        `<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>` :
                                        `<button type="button" class="btn btn-success btn-sm" data-sodocno="${row.id}" data-status="Normal">Normal</button>`;
                                }
                            },
                            {
                                data: 'status_job',
                                name: 'status_job',
                                className: 'cust-col',
                                render: function(data, type, row) {
                                    let btnClass = '';
                                    switch (data) {
                                        case 'CLOSED':
                                            btnClass = 'btn-danger';
                                            break;
                                        case 'FINISH':
                                            btnClass = 'btn-success';
                                            break;
                                        case 'APPROVED':
                                            btnClass = 'btn-warning';
                                            break;
                                        case 'REJECT':
                                            btnClass = 'btn-danger';
                                            break;
                                        default:
                                            btnClass = 'btn-info';
                                            break;
                                    }
                                    return `<button type="button" class="btn ${btnClass} btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                }
                            },
                            {
                                data: 'pic',
                                name: 'pic',
                                className: 'cust-col',
                                render: function(data) {
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
                }

                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Inisialisasi DataTable dengan route awal
                var datatable2 = initDataTable("{{ route('prepress.plan-selected.data') }}", csrfToken);

                // Saat tombol #btn-work-order diklik
                $('#btn-work-order').click(function() {
                    datatable2.clear().destroy();
                    datatable2 = initDataTable("{{ route('prepress.plan-selected.data-work-order') }}",
                        csrfToken);
                });

                // Saat tombol #btn-job-order diklik
                $('#btn-job-order').click(function() {
                    datatable2.clear().destroy();
                    datatable2 = initDataTable("{{ route('prepress.plan-selected.data') }}", csrfToken);
                });

                $('body').on('click', '.assign-job', function() {
                    var id = $(this).data('id');
                    console.log('Assign job clicked for ID:', id);

                    var url = "{{ route('prepress.job-order.assign-job', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);

                    // Reset form sebelum mengisi data
                    $('#submitAssignJob')[0].reset();

                    // Show modal using Bootstrap 4 method
                    $('#exampleModal').modal('show');

                    $.get(url, function(data) {
                        $('#id_job').val(data.job_order.id);

                        // Format tanggal untuk input type="date" (yyyy-MM-dd)
                        var tanggalJob = data.job_order.tanggal_job_order ? data.job_order
                            .tanggal_job_order.split(' ')[0] : '';
                        var tanggalDeadline = data.job_order.tanggal_deadline ? data.job_order
                            .tanggal_deadline.split(' ')[0] : '';

                        $('#tanggal_job').val(tanggalJob);
                        $('#tanggal_deadline').val(tanggalDeadline);

                        $('#customer').val(data.job_order.customer);
                        $('#product').val(data.job_order.product);
                        $('#qty_order_estimation').val(data.job_order.qty_order_estimation);
                        $('#job_order').val(data.job_order.job_order);

                        // Handle file_data
                        let fileData = data.job_order.file_data;
                        if (Array.isArray(fileData)) {
                            $('#file_data').val(fileData.join(', '));
                        } else if (typeof fileData === 'string') {
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

                        $('#prioritas_job').val(data.job_order.prioritas_job);
                        $('#status_job').val(data.job_order.status_job);
                        $('#catatan').val(data.job_order.catatan);
                    }).fail(function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        alert('Gagal mengambil data job order');
                    });
                });

                $('body').on('click', '.assign-job-wo', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('prepress.work-order.assign-job', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);

                    // Reset form sebelum mengisi data
                    $('#submitAssignWO')[0].reset();

                    // Show modal using Bootstrap 4 method
                    $('#exampleModalWO').modal('show');

                    $.get(url, function(data) {
                        $('#id_wo').val(data.job_order.DocNo);

                        // Format tanggal untuk input type="date" (yyyy-MM-dd)
                        var tanggalWO = data.job_order.DocDate ? data.job_order.DocDate.split(' ')[0] :
                            '';
                        var tanggalDeadlineWO = data.job_order.DocDate ? data.job_order.DocDate.split(
                            ' ')[0] : '';

                        $('#tanggal_wo').val(tanggalWO);
                        $('#tanggal_deadline_wo').val(tanggalDeadlineWO);

                        $('#customer_wo').val(data.job_order.Customer);
                        $('#product_wo').val(data.job_order.MaterialCode);
                        $('#qty_order_estimation_wo').val(data.job_order.Qty);
                        $('#job_order_wo').val('Plate');
                        $('#prioritas_wo').val('Normal');
                        $('#status_wo').val('PLAN');
                    }).fail(function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        alert('Gagal mengambil data work order');
                    });
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

                                // Hide modal using Bootstrap 4 method
                                $('#exampleModal').modal('hide');

                                $('#select-machines').show();
                                $('#name-machines').css('display', 'none');
                            } else if (data.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                                $('#modal-body-detail-info-jo').html(data.message);

                                // Hide modal using Bootstrap 4 method
                                $('#exampleModal').modal('hide');
                            } else {
                                $('#modal-body-detail-info-jo').html(data.message);
                            }
                        }
                    });
                });

                $('#submitAssignWO').submit(function(e) {
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
                                $('#submitAssignWO').trigger("reset");

                                // Hide modal using Bootstrap 4 method
                                $('#exampleModalWO').modal('hide');

                                $('#select-machines').show();
                                $('#name-machines').css('display', 'none');
                            } else if (data.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                                $('#modal-body-detail-info-jo').html(data.message);

                                // Hide modal using Bootstrap 4 method
                                $('#exampleModalWO').modal('hide');
                            } else {
                                $('#modal-body-detail-info-jo').html(data.message);
                            }
                        }
                    });
                });

                $('#submitRejectJob').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    $.ajax({
                        url: "{{ route('prepress.job-order.reject-job-order') }}",
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
                                $('#submitRejectJob').trigger("reset");

                                // Hide modal using Bootstrap 4 method
                                $('#exampleModalReject').modal('hide');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    showConfirmButton: true
                                });
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

                $('body').on('click', '.delete-job-order', function() {
                    Swal.fire({
                        title: 'Apakah anda yakin ingin menghapus JOB dari WO ini?',
                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id = $(this).data('id');
                            var url =
                                "{{ route('prepress.job-order.delete-job-order-wo', ['id' => 'ID_PLACEHOLDER']) }}";
                            url = url.replace('ID_PLACEHOLDER', id);
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    _token: csrfToken
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
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: data.message,
                                            showConfirmButton: true,
                                        });
                                    }
                                }
                            });
                        }
                    });
                });

                $('body').on('click', '.reject-job-order', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('prepress.job-order.assign-job', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        },
                        success: function(data) {
                            $('#id_job_reject').val(data.job_order.id);
                            $('#no_job_order').val(data.job_order.nomor_job_order);
                            $('#alasan_reject').val(''); // Reset alasan reject

                            $('#exampleModalReject').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching job data:', error);
                            alert('Gagal mengambil data job order');
                        }
                    });
                });

                // Handle Detail Jobs Button Click
                $('body').on('click', '.detail-jobs-btn', function() {
                    var picId = $(this).data('pic-id');
                    var picName = $(this).data('pic-name');
                    var jobsSelesai = $(this).data('jobs-selesai');
                    var waktuStandar = $(this).data('waktu-standar');
                    var totalWaktuAssigned = $(this).data('total-waktu-assigned');

                    // Set modal title
                    $('#pic-name-detail').text(picName);

                    // Set summary data will be handled in loadDetailJobs function

                    // Show modal
                    $('#modalDetailJobs').modal('show');

                    // Load detail jobs
                    loadDetailJobs(picId);
                });




            });

            // Function to load PIC load data
            // Menggunakan rumus produktivitas SIPO per hari:
            // Jam kerja per hari = 450 menit
            // Produktivitas = (Waktu realtime untuk Job yang Diselesaikan : 450 menit) × 100%
            // Target = (Master total waktu yang di assigned : 450 menit) × 100%
            // Overtime pengerjaan = Produktivitas - Target
            function loadPicLoadData() {
                $.ajax({
                    url: "{{ route('prepress.dashboard.load-data') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Update summary cards
                        $('#total-jobs').text(data.summary.total_jobs);
                        $('#total-pic').text(data.summary.total_pic);
                        $('#avg-load').text(data.summary.avg_target + '%'); // Rata-rata target
                        $('#high-load').text(data.summary.high_target_pic); // PIC dengan target tertinggi

                        // Render PIC load cards
                        renderPicLoadCards(data.pic_loads);
                    },
                    error: function(xhr, status, error) {
                        $('#pic-load-container').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error loading data: ${error}
                            </div>
                        `);
                    }
                });
            }

            // Function to render PIC load cards
            function renderPicLoadCards(picLoads) {
                let html = '';

                picLoads.forEach(function(pic) {
                    // Rumus Produktivitas SIPO - Per Hari
                    // Jam kerja per hari = 450 menit
                    const waktuKerjaTersediaPerHari = 450; // menit per hari

                    // Data dari backend
                    const waktuRealtimeSelesai = pic.waktu_realtime_selesai ||
                        0; // menit realtime untuk job yang diselesaikan
                    const totalWaktuAssigned = pic.total_waktu_assigned ||
                        0; // total waktu yang di-assign (est_job_default)

                    // Hitung Produktivitas = (Waktu realtime untuk Job yang Diselesaikan : 450 menit) × 100%
                    const produktivitas = Math.round((waktuRealtimeSelesai / waktuKerjaTersediaPerHari) * 100);

                    // Hitung Target = (Master total waktu yang di assigned : 450 menit) × 100%
                    const target = Math.round((totalWaktuAssigned / waktuKerjaTersediaPerHari) * 100);

                    // Hitung Overtime pengerjaan = produktivitas - target
                    const overtime = produktivitas - target;

                    // Gunakan target sebagai load percentage untuk progress bar
                    const loadPercentage = Math.min(target, 100);
                    const progressBarClass = getProgressBarClass(loadPercentage);

                    html += `
                        <div class="pic-load-card">
                            <div class="user-info">
                                <div class="user-avatar">
                                    ${pic.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="user-details">
                                    <h5>${pic.name}</h5>
                                    <p>PIC Prepress</p>
                                </div>
                            </div>

                            <div class="progress-container">
                                <div class="progress">
                                    <div class="progress-bar ${progressBarClass}"
                                         style="width: ${Math.min(loadPercentage, 100)}%"></div>
                                </div>
                                <div class="progress-info">
                                    <span class="progress-text">
                                        Total Load: ${totalWaktuAssigned.toLocaleString()} / ${waktuKerjaTersediaPerHari} menit
                                    </span>
                                    <span class="job-count">${target}%</span>
                                </div>
                                <div class="progress-details mt-2">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Load aktif: ${totalWaktuAssigned.toLocaleString()} menit |
                                            Selesai hari ini: ${pic.load_selesai_hari_ini || 0} menit |
                                            Total: ${totalWaktuAssigned.toLocaleString()} menit
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-info detail-jobs-btn"
                                                data-pic-id="${pic.id}"
                                                data-pic-name="${pic.name}"
                                                data-jobs-selesai="${pic.jobs_selesai || 0}"
                                                data-waktu-standar="${waktuRealtimeSelesai.toLocaleString()}"
                                                data-total-waktu-assigned="${totalWaktuAssigned}">
                                            <i class="mdi mdi-animation"></i> Detail Jobs
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <small class="text-muted">Assigned:</small><br>
                                    <strong class="text-warning">${pic.status_counts.ASSIGNED || 0}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">In Progress:</small><br>
                                    <strong class="text-primary">${pic.status_counts.IN_PROGRESS || 0}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Finished:</small><br>
                                    <strong class="text-success">${pic.status_counts.FINISH || 0}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Completed:</small><br>
                                    <strong class="text-success">${pic.status_counts.COMPLETED || 0}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Approved:</small><br>
                                    <strong class="text-info">${pic.status_counts.APPROVED || 0}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Total:</small><br>
                                    <strong class="text-dark">${pic.total_jobs}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#pic-load-container').html(html);
            }

            // Function to get progress bar class based on percentage
            function getProgressBarClass(percentage) {
                if (percentage >= 90) return 'bg-danger';
                if (percentage >= 70) return 'bg-warning';
                if (percentage >= 50) return 'bg-info';
                return 'bg-success';
            }

            // Function to load detail jobs for specific PIC
            function loadDetailJobs(picId) {
                $('#loading-detail-jobs').show();
                $('#table-detail-jobs').hide();

                var url = "{{ route('prepress.dashboard.detail-jobs', ['id' => 'ID_PLACEHOLDER']) }}";
                url = url.replace('ID_PLACEHOLDER', picId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#loading-detail-jobs').hide();
                        $('#table-detail-jobs').show();

                        // Set load breakdown data
                        if (data.load_breakdown) {
                            $('#load-aktif-detail').text(data.load_breakdown.total_waktu_assigned.toLocaleString());
                            $('#load-selesai-detail').text(data.load_breakdown.load_selesai_hari_ini
                                .toLocaleString());
                            $('#total-load-detail').text(data.load_breakdown.total_load.toLocaleString());
                        }

                        // Set total jobs selesai
                        $('#total-jobs-detail').text(data.jobs ? data.jobs.length : 0);

                        var tbody = $('#tbody-detail-jobs');
                        tbody.empty();

                        if (data.jobs && data.jobs.length > 0) {
                            data.jobs.forEach(function(job, index) {
                                var statusBadge = getStatusBadge(job.status_job);
                                var tanggalSelesai = job.tanggal_selesai ? moment(job.tanggal_selesai)
                                    .format('DD-MM-YYYY HH:mm') : '-';
                                var rowClass = job.job_type === 'finished' ? 'table-success' : '';

                                tbody.append(`
                                    <tr class="${rowClass}">
                                        <td>${index + 1}</td>
                                        <td><strong>${job.nomor_job_order}</strong></td>
                                        <td>${job.customer || '-'}</td>
                                        <td>${job.product || '-'}</td>
                                        <td>${statusBadge}</td>
                                        <td>${tanggalSelesai}</td>
                                        <td><span class="badge badge-warning">${job.waktu_default || '0'} menit</span></td>
                                        <td><span class="badge badge-info">${job.waktu_realtime || '0'} menit</span></td>
                                        <td>${job.catatan || '-'}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tbody.append(`
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        <i class="fas fa-inbox mr-2"></i>
                                        Tidak ada data jobs
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading-detail-jobs').hide();
                        $('#table-detail-jobs').show();

                        $('#tbody-detail-jobs').html(`
                            <tr>
                                <td colspan="8" class="text-center text-danger">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Error loading data: ${error}
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Function to get status badge HTML
            function getStatusBadge(status) {
                var badgeClass = '';
                var statusText = status;

                switch (status) {
                    case 'FINISH':
                        badgeClass = 'badge-success';
                        break;
                    case 'APPROVED':
                        badgeClass = 'badge-info';
                        break;
                    case 'IN_PROGRESS':
                        badgeClass = 'badge-primary';
                        break;
                    case 'ASSIGNED':
                        badgeClass = 'badge-warning';
                        break;
                    case 'REJECT':
                        badgeClass = 'badge-danger';
                        break;
                    default:
                        badgeClass = 'badge-secondary';
                }

                return `<span class="badge ${badgeClass}">${statusText}</span>`;
            }
        </script>
    @endsection

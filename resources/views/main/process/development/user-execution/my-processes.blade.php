@extends('main.layouts.main')
@section('title')
    Process Management - Development
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
    </style>
@endsection
@section('page-title')
    Process Management - Development
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Process Management - Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Process Management - Development</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-clipboard-list"></i>
                            Process Management - Development
                        </h4>
                        <p class="card-subtitle">Monitor dan kelola semua processes development (Pending, In Progress,
                            Completed)</p>
                    </div>
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="pendingCount">0</h4>
                                                <p class="mb-0">Pending</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-clock mdi-36px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-purple text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="inProgressCount">0</h4>
                                                <p class="mb-0">In Progress</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-progress-clock mdi-36px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="completedCount">0</h4>
                                                <p class="mb-0">Completed</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-check-circle mdi-36px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="totalCount">0</h4>
                                                <p class="mb-0">Total</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-chart-line mdi-36px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="processTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="my-processes-tab" data-toggle="tab"
                                            href="#my-processes" role="tab">
                                            <i class="mdi mdi-account-check"></i> My Assigned Processes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="all-processes-tab" data-toggle="tab" href="#all-processes"
                                            role="tab">
                                            <i class="mdi mdi-clipboard-text"></i> All In-Progress Processes
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="processTabsContent">
                            <!-- My Assigned Processes Tab -->
                            <div class="tab-pane fade show active" id="my-processes" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="my-processes-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Job Code</th>
                                                <th>Job Name</th>
                                                <th>Process Name</th>
                                                <th>Process Type</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th>Duration (hrs)</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <!-- All In-Progress Processes Tab -->
                            <div class="tab-pane fade" id="all-processes" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="all-processes-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Job Code</th>
                                                <th>Job Name</th>
                                                <th>Process Name</th>
                                                <th>Process Type</th>
                                                <th>Department</th>
                                                <th>Assigned User</th>
                                                <th>Status</th>
                                                <th>Duration (hrs)</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Details Modal -->
        <div class="modal fade" id="processDetailsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Process Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="processDetailsContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Process Modal -->
        <div class="modal fade" id="startProcessModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Start Process</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="startProcessForm">
                            <div class="form-group">
                                <label for="startNotes">Catatan Start Process</label>
                                <textarea class="form-control" id="startNotes" name="startNotes" rows="3"
                                    placeholder="Masukkan catatan untuk start process..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="startProcess()">Start Process</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complete Process Modal -->
        <div class="modal fade" id="completeProcessModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Process</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="completeProcessForm">
                            <div class="form-group">
                                <label for="completionNotes">Catatan Completion</label>
                                <textarea class="form-control" id="completionNotes" name="completionNotes" rows="3"
                                    placeholder="Masukkan catatan completion..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" onclick="completeProcess()">Complete
                            Process</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PPIC Production Planning Modal -->
        <div class="modal fade" id="ppicModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">PPIC - Production Planning & Item Request</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Job Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">Job Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Job Code:</strong></td>
                                        <td id="modalJobCode">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Job Name:</strong></td>
                                        <td id="modalJobName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td id="modalJobType">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td id="modalCustomerName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Specification:</strong></td>
                                        <td id="modalSpecification">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Process Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Process:</strong></td>
                                        <td id="modalProcessName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td id="modalDepartmentName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td id="modalProcessStatus">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Dynamic Form Based on Job Type -->
                        <div id="modalProofForm" style="display: none;">
                            <!-- Proof (Normal) - Production Scheduling -->
                            <div class="card bg-success text-white">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-calendar-check"></i>
                                        Production Scheduling (Proof Type)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="modalProductionSchedulingForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalProductionDate">Tanggal Produksi</label>
                                                    <input type="date" class="form-control" id="modalProductionDate"
                                                        name="production_date" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalProductionShift">Shift Produksi</label>
                                                    <select class="form-control" id="modalProductionShift"
                                                        name="production_shift" required>
                                                        <option value="">Pilih Shift</option>
                                                        <option value="shift_1">Shift 1 (06:00 - 14:00)</option>
                                                        <option value="shift_2">Shift 2 (14:00 - 22:00)</option>
                                                        <option value="shift_3">Shift 3 (22:00 - 06:00)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalProductionLine">Line Produksi</label>
                                                    <input type="text" class="form-control" id="modalProductionLine"
                                                        name="production_line" placeholder="Line A, Line B, dll" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalEstimatedQuantity">Estimasi Quantity</label>
                                                    <input type="number" class="form-control"
                                                        id="modalEstimatedQuantity" name="estimated_quantity"
                                                        min="1" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="modalProductionNotes">Catatan Produksi</label>
                                            <textarea class="form-control" id="modalProductionNotes" name="production_notes" rows="3"
                                                placeholder="Catatan khusus untuk produksi..."></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="modalTrialKhususForm" style="display: none;">
                            <!-- Trial Khusus - Item Request to Purchasing -->
                            <div class="card bg-warning text-white">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-cart-plus"></i>
                                        Item Request to Purchasing (Trial Khusus)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="modalItemRequestForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalItemName">Nama Item yang Dibutuhkan</label>
                                                    <input type="text" class="form-control" id="modalItemName"
                                                        name="item_name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalItemSpecification">Spesifikasi Item</label>
                                                    <input type="text" class="form-control"
                                                        id="modalItemSpecification" name="item_specification" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalRequiredQuantity">Quantity yang Dibutuhkan</label>
                                                    <input type="number" class="form-control" id="modalRequiredQuantity"
                                                        name="required_quantity" min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalRequiredDate">Tanggal Dibutuhkan</label>
                                                    <input type="date" class="form-control" id="modalRequiredDate"
                                                        name="required_date" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalPriorityLevel">Level Prioritas</label>
                                                    <select class="form-control" id="modalPriorityLevel"
                                                        name="priority_level" required>
                                                        <option value="">Pilih Prioritas</option>
                                                        <option value="low">Low</option>
                                                        <option value="medium">Medium</option>
                                                        <option value="high">High</option>
                                                        <option value="urgent">Urgent</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalBudgetEstimate">Estimasi Budget</label>
                                                    <input type="number" class="form-control" id="modalBudgetEstimate"
                                                        name="budget_estimate" placeholder="Rp" min="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="modalRequestReason">Alasan Permintaan</label>
                                            <textarea class="form-control" id="modalRequestReason" name="request_reason" rows="3"
                                                placeholder="Jelaskan mengapa item ini dibutuhkan untuk trial..."></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="modalAdditionalNotes">Catatan Tambahan</label>
                                            <textarea class="form-control" id="modalAdditionalNotes" name="additional_notes" rows="2"
                                                placeholder="Catatan lain untuk purchasing..."></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" id="modalSubmitBtn" onclick="submitPPICForm()"
                            style="display: none;">
                            <i class="mdi mdi-check"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchasing Tracking Modal -->
        <div class="modal fade" id="purchasingTrackingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Purchasing Tracking - Trial Item Khusus</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="purchasingTrackingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingItemName">Nama Item</label>
                                        <input type="text" class="form-control" id="trackingItemName" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingItemSpec">Spesifikasi Item</label>
                                        <input type="text" class="form-control" id="trackingItemSpec" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingOrderStatus">Status Pemesanan</label>
                                        <select class="form-control" id="trackingOrderStatus" name="order_status"
                                            required>
                                            <option value="">Pilih Status</option>
                                            <option value="not_ordered">Belum Dipesan</option>
                                            <option value="ordered">Sudah Dipesan</option>
                                            <option value="in_transit">Dalam Pengiriman</option>
                                            <option value="received">Sudah Diterima</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingOrderDate">Tanggal Pemesanan</label>
                                        <input type="date" class="form-control" id="trackingOrderDate"
                                            name="order_date">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingDeliveryDate">Tanggal Pengiriman</label>
                                        <input type="date" class="form-control" id="trackingDeliveryDate"
                                            name="delivery_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingReceivedDate">Tanggal Diterima</label>
                                        <input type="date" class="form-control" id="trackingReceivedDate"
                                            name="received_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="trackingNotes">Catatan Tracking</label>
                                <textarea class="form-control" id="trackingNotes" name="tracking_notes" rows="3"
                                    placeholder="Update status tracking item..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="trackingNextAction">Aksi Selanjutnya</label>
                                <textarea class="form-control" id="trackingNextAction" name="next_action" rows="2"
                                    placeholder="Apa yang akan dilakukan selanjutnya..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" onclick="submitPurchasingTracking()">Update
                            Tracking</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- QC Verification Modal -->
        <div class="modal fade" id="qcVerificationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">QC Verification - Trial Item Khusus</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="qcVerificationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcItemName">Nama Item</label>
                                        <input type="text" class="form-control" id="qcItemName" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcItemSpec">Spesifikasi Item</label>
                                        <input type="text" class="form-control" id="qcItemSpec" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcVerificationResult">Hasil Verifikasi</label>
                                        <select class="form-control" id="qcVerificationResult" name="verification_result"
                                            required>
                                            <option value="">Pilih Hasil</option>
                                            <option value="ok">OK</option>
                                            <option value="not_ok">NOT OK</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcQualityScore">Quality Score (1-10)</label>
                                        <input type="number" class="form-control" id="qcQualityScore"
                                            name="quality_score" min="1" max="10" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="qcDefectsFound">Defects yang Ditemukan</label>
                                <textarea class="form-control" id="qcDefectsFound" name="defects_found" rows="3"
                                    placeholder="Jelaskan defects yang ditemukan (jika ada)..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="qcRecommendations">Rekomendasi</label>
                                <textarea class="form-control" id="qcRecommendations" name="recommendations" rows="3"
                                    placeholder="Rekomendasi untuk item ini..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="qcVerificationNotes">Catatan Verifikasi</label>
                                <textarea class="form-control" id="qcVerificationNotes" name="verification_notes" rows="3"
                                    placeholder="Catatan detail verifikasi..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="qcNextAction">Aksi Selanjutnya</label>
                                <textarea class="form-control" id="qcNextAction" name="next_action" rows="2"
                                    placeholder="Apa yang akan dilakukan selanjutnya..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" onclick="submitQcVerification()">Submit
                            Verification</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('styles')
        <style>
            .card {
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                border-radius: 10px 10px 0 0 !important;
            }

            .btn-action {
                margin: 2px;
                padding: 6px 12px;
                font-size: 12px;
            }

            .badge {
                font-size: 11px;
                padding: 5px 10px;
            }

            .table th {
                background-color: #f8f9fa;
                border-top: none;
                font-weight: 600;
            }

            .modal-xl {
                max-width: 90%;
            }

            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
        </style>
    @endsection

    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
        {{-- jquery --}}
        <script>
            let currentProcessId = null;
            let currentProcessData = null;

            $(document).ready(function() {
                initializeDataTable();
                updateStatistics();

                // Set today's date as default for date inputs
                $('#modalProductionDate').val(new Date().toISOString().split('T')[0]);
                $('#modalRequiredDate').val(new Date().toISOString().split('T')[0]);
            });

            function initializeDataTable() {
                // Initialize My Processes DataTable
                $('#my-processes-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route('user-execution.my-processes.data') }}',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'job_development.job_code',
                            name: 'job_code'
                        },
                        {
                            data: 'job_development.job_name',
                            name: 'job_name',
                            render: function(data, type, row) {
                                return `<a href="#" onclick="viewProcessDetails(${row.id})" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'process_name',
                            name: 'process_name'
                        },
                        {
                            data: 'process_type',
                            name: 'process_type',
                            render: function(data, type, row) {
                                if (data === 'ppic') {
                                    return '<span class="badge badge-info">PPIC (Production)</span>';
                                } else if (data === 'purchasing') {
                                    return '<span class="badge badge-warning">Purchasing</span>';
                                } else if (data === 'qc') {
                                    return '<span class="badge badge-success">Quality Control</span>';
                                } else if (data === 'rnd_verification') {
                                    return '<span class="badge badge-primary">RnD Verification</span>';
                                } else {
                                    return '<span class="badge badge-secondary">' + (data || 'Normal') +
                                        '</span>';
                                }
                            }
                        },
                        {
                            data: 'department.divisi',
                            name: 'department',
                            render: function(data, type, row) {
                                return data ? `<span class="badge badge-secondary">${data}</span>` : '-';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'pending') badgeClass = 'badge-warning';
                                else if (data === 'in_progress') badgeClass = 'badge-purple';
                                else if (data === 'completed') badgeClass = 'badge-success';
                                else if (data === 'failed') badgeClass = 'badge-danger';

                                return `<span class="badge ${badgeClass}">${data.replace('_', ' ').toUpperCase()}</span>`;
                            }
                        },
                        {
                            data: 'estimated_duration',
                            name: 'estimated_duration',
                            render: function(data, type, row) {
                                return data ? `<span class="badge badge-orange">${data} hrs</span>` : '-';
                            }
                        },
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let buttons = '';

                                // View Details Button
                                buttons += `<button type="button" class="btn btn-info btn-action" onclick="viewProcessDetails(${row.id})" title="View Details">
                    <i class="mdi mdi-eye"></i>
                </button>`;

                                // Execute Process Button
                                buttons += `<button type="button" class="btn btn-warning btn-action" onclick="executeProcess(${row.id})" title="Execute Process">
                    <i class="mdi mdi-play-circle"></i>
                </button>`;

                                // Start Process Button (only for pending)
                                if (row.status === 'pending') {
                                    buttons += `<button type="button" class="btn btn-primary btn-action" onclick="openStartProcessModal(${row.id})" title="Start Process">
                        <i class="mdi mdi-play"></i>
                    </button>`;
                                }

                                // Complete Process Button (only for in_progress)
                                if (row.status === 'in_progress') {
                                    buttons += `<button type="button" class="btn btn-success btn-action" onclick="openCompleteProcessModal(${row.id})" title="Complete Process">
                        <i class="mdi mdi-check"></i>
                    </button>`;
                                }

                                // Conditional buttons for Trial Khusus workflow
                                if (row.job_development && row.job_development.type === 'trial_khusus') {
                                    if (row.process_type === 'ppic' && row.status === 'in_progress') {
                                        buttons += `<button type="button" class="btn btn-warning btn-action" onclick="openPPICModal(${row.id})" title="PPIC Planning">
                                    <i class="mdi mdi-calendar-clock"></i>
                                </button>`;
                                    }
                                    if (row.process_type === 'purchasing' && row.status === 'in_progress') {
                                        buttons += `<button type="button" class="btn btn-warning btn-action" onclick="openPurchasingTrackingModal(${row.id})" title="Purchasing Tracking">
                                    <i class="mdi mdi-truck-delivery"></i>
                                </button>`;
                                    }
                                    if (row.process_type === 'qc' && row.status === 'in_progress') {
                                        buttons += `<button type="button" class="btn btn-success btn-action" onclick="openQcVerificationModal(${row.id})" title="QC Verification">
                                    <i class="mdi mdi-clipboard-check"></i>
                                </button>`;
                                    }
                                }

                                return buttons;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    language: {
                        "processing": "Memproses...",
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "loadingRecords": "Memuat...",
                        "zeroRecords": "Tidak ada data yang ditemukan",
                        "emptyTable": "Tidak ada data yang tersedia",
                        "paginate": {
                            "first": "Pertama",
                            "previous": "Sebelumnya",
                            "next": "Selanjutnya",
                            "last": "Terakhir"
                        },
                        "aria": {
                            "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                            "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                        }
                    }
                });

                // Initialize All Processes DataTable
                $('#all-processes-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route('development.in-progress-processes.data') }}',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'job_development.job_code',
                            name: 'job_code'
                        },
                        {
                            data: 'job_development.job_name',
                            name: 'job_name',
                            render: function(data, type, row) {
                                return `<a href="#" onclick="viewProcessDetails(${row.id})" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'process_name',
                            name: 'process_name'
                        },
                        {
                            data: 'process_type',
                            name: 'process_type',
                            render: function(data, type, row) {
                                if (data === 'ppic') {
                                    return '<span class="badge badge-info">PPIC (Production)</span>';
                                } else if (data === 'purchasing') {
                                    return '<span class="badge badge-warning">Purchasing</span>';
                                } else if (data === 'qc') {
                                    return '<span class="badge badge-success">Quality Control</span>';
                                } else if (data === 'rnd_verification') {
                                    return '<span class="badge badge-primary">RnD Verification</span>';
                                } else {
                                    return '<span class="badge badge-secondary">' + (data || 'Normal') +
                                        '</span>';
                                }
                            }
                        },
                        {
                            data: 'department.divisi',
                            name: 'department',
                            render: function(data, type, row) {
                                return data ? `<span class="badge badge-secondary">${data}</span>` : '-';
                            }
                        },
                        {
                            data: 'assigned_user.name',
                            name: 'assigned_user',
                            render: function(data, type, row) {
                                return data ? `<span class="badge badge-info">${data}</span>` : '-';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'badge-secondary';
                                if (data === 'pending') badgeClass = 'badge-warning';
                                else if (data === 'in_progress') badgeClass = 'badge-purple';
                                else if (data === 'completed') badgeClass = 'badge-success';
                                else if (data === 'failed') badgeClass = 'badge-danger';

                                return `<span class="badge ${badgeClass}">${data.replace('_', ' ').toUpperCase()}</span>`;
                            }
                        },
                        {
                            data: 'estimated_duration',
                            name: 'estimated_duration',
                            render: function(data, type, row) {
                                return data ? `<span class="badge badge-orange">${data} hrs</span>` : '-';
                            }
                        },
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let buttons = '';

                                // View Details Button
                                buttons += `<button type="button" class="btn btn-info btn-action" onclick="viewProcessDetails(${row.id})" title="View Details">
                            <i class="mdi mdi-eye"></i>
                        </button>`;

                                // Execute Process Button
                                buttons += `<button type="button" class="btn btn-warning btn-action" onclick="executeProcess(${row.id})" title="Execute Process">
                            <i class="mdi mdi-play-circle"></i>
                        </button>`;

                                return buttons;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    language: {
                        "processing": "Memproses...",
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "loadingRecords": "Memuat...",
                        "zeroRecords": "Tidak ada data yang ditemukan",
                        "emptyTable": "Tidak ada data yang tersedia",
                        "paginate": {
                            "first": "Pertama",
                            "previous": "Sebelumnya",
                            "next": "Selanjutnya",
                            "last": "Terakhir"
                        },
                        "aria": {
                            "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                            "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                        }
                    }
                });

                // Tab switching event
                $('#all-processes-tab').on('click', function() {
                    $('#all-processes-table').DataTable().ajax.reload();
                });
            }

            function updateStatistics() {
                $.ajax({
                    url: '{{ route('user-execution.my-processes.data') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response && response.data && Array.isArray(response.data)) {
                            const data = response.data;
                            const pending = data.filter(item => item.status === 'pending').length;
                            const inProgress = data.filter(item => item.status === 'in_progress').length;
                            const completed = data.filter(item => item.status === 'completed').length;
                            const total = data.length;

                            $('#pendingCount').text(pending);
                            $('#inProgressCount').text(inProgress);
                            $('#completedCount').text(completed);
                            $('#totalCount').text(total);
                        }
                    },
                    error: function() {
                        console.error('Error updating statistics');
                    }
                });
            }

            function executeProcess(processId) {
                window.location.href = `/sipo/development/user-execution/process/${processId}/execute`;
            }

            function viewProcessDetails(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/data`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const process = response.process;
                            const job = response.job;

                            let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Job Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Job Code:</strong></td><td>${job.job_code}</td></tr>
                                    <tr><td><strong>Job Name:</strong></td><td>${job.job_name}</td></tr>
                                    <tr><td><strong>Type:</strong></td><td>${job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus'}</td></tr>
                                    <tr><td><strong>Customer:</strong></td><td>${job.customer_name || '-'}</td></tr>
                                    <tr><td><strong>Specification:</strong></td><td>${job.specification || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Process Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Process:</strong></td><td>${process.process_name}</td></tr>
                                    <tr><td><strong>Department:</strong></td><td>${process.department ? process.department.divisi : '-'}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td>${process.status}</td></tr>
                                    <tr><td><strong>Duration:</strong></td><td>${process.estimated_duration || '-'} hrs</td></tr>
                                    <tr><td><strong>Notes:</strong></td><td>${process.notes || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;

                            $('#processDetailsContent').html(content);
                            $('#processDetailsModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('Error loading process details');
                    }
                });
            }

            function openStartProcessModal(processId) {
                currentProcessId = processId;
                $('#startNotes').val('');
                $('#startProcessModal').modal('show');
            }

            function startProcess() {
                if (!currentProcessId) return;

                const notes = $('#startNotes').val();

                $.ajax({
                    url: `/sipo/development/user-execution/process/${currentProcessId}/start`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#startProcessModal').modal('hide');
                            $('#my-processes-table').DataTable().ajax.reload();
                            updateStatistics();
                            alert('Process berhasil dimulai!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat start process');
                    }
                });
            }

            function openCompleteProcessModal(processId) {
                currentProcessId = processId;
                $('#completionNotes').val('');
                $('#completeProcessModal').modal('show');
            }

            function completeProcess() {
                if (!currentProcessId) return;

                const notes = $('#completionNotes').val();

                $.ajax({
                    url: `/sipo/development/user-execution/process/${currentProcessId}/complete`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#completeProcessModal').modal('hide');
                            $('#my-processes-table').DataTable().ajax.reload();
                            updateStatistics();
                            alert('Process berhasil diselesaikan!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat complete process');
                    }
                });
            }

            function openPPICModal(processId) {
                currentProcessId = processId;
                loadProcessDataForModal(processId);
                $('#ppicModal').modal('show');
            }

            function loadProcessDataForModal(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/data`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const process = response.process;
                            const job = response.job;

                            // Populate modal fields
                            $('#modalJobCode').text(job.job_code);
                            $('#modalJobName').text(job.job_name);
                            $('#modalJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                            $('#modalCustomerName').text(job.customer_name || '-');
                            $('#modalSpecification').text(job.specification || '-');
                            $('#modalProcessName').text(process.process_name);
                            $('#modalDepartmentName').text(process.department ? process.department.divisi : '-');
                            $('#modalProcessStatus').text(process.status);

                            // Show appropriate form based on job type
                            if (job.type === 'proof') {
                                $('#modalProofForm').show();
                                $('#modalTrialKhususForm').hide();
                                $('#modalSubmitBtn').show().text('Set Production Schedule');
                            } else if (job.type === 'trial_khusus') {
                                $('#modalProofForm').hide();
                                $('#modalTrialKhususForm').show();
                                $('#modalSubmitBtn').show().text('Send Item Request');
                            }
                        }
                    },
                    error: function() {
                        alert('Error loading process data');
                    }
                });
            }

            function submitPPICForm() {
                if (!currentProcessId) return;

                let formData = {};
                let url = '';

                // Check which form is visible
                if ($('#modalProofForm').is(':visible')) {
                    // Production Scheduling form
                    formData = {
                        production_date: $('#modalProductionDate').val(),
                        production_shift: $('#modalProductionShift').val(),
                        production_line: $('#modalProductionLine').val(),
                        estimated_quantity: $('#modalEstimatedQuantity').val(),
                        production_notes: $('#modalProductionNotes').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    url = `/sipo/development/user-execution/process/${currentProcessId}/production-schedule`;
                } else {
                    // Item Request form
                    formData = {
                        item_name: $('#modalItemName').val(),
                        item_specification: $('#modalItemSpecification').val(),
                        required_quantity: $('#modalRequiredQuantity').val(),
                        required_date: $('#modalRequiredDate').val(),
                        priority_level: $('#modalPriorityLevel').val(),
                        budget_estimate: $('#modalBudgetEstimate').val(),
                        request_reason: $('#modalRequestReason').val(),
                        additional_notes: $('#modalAdditionalNotes').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    url = `/sipo/development/user-execution/process/${currentProcessId}/item-request`;
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#ppicModal').modal('hide');
                            $('#my-processes-table').DataTable().ajax.reload();
                            updateStatistics();
                            alert('Data berhasil disubmit!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat submit data');
                    }
                });
            }

            function openPurchasingTrackingModal(processId) {
                currentProcessId = processId;
                loadProcessDataForPurchasing(processId);
                $('#purchasingTrackingModal').modal('show');
            }

            function loadProcessDataForPurchasing(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/data`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const job = response.job;

                            // Populate modal fields (you might want to load existing tracking data here)
                            $('#trackingItemName').val(job.specification || '');
                            $('#trackingItemSpec').val(job.specification || '');
                        }
                    },
                    error: function() {
                        alert('Error loading process data');
                    }
                });
            }

            function submitPurchasingTracking() {
                if (!currentProcessId) return;

                const formData = {
                    order_status: $('#trackingOrderStatus').val(),
                    order_date: $('#trackingOrderDate').val(),
                    delivery_date: $('#trackingDeliveryDate').val(),
                    received_date: $('#trackingReceivedDate').val(),
                    tracking_notes: $('#trackingNotes').val(),
                    next_action: $('#trackingNextAction').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: `/sipo/development/user-execution/process/${currentProcessId}/update-tracking`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#purchasingTrackingModal').modal('hide');
                            $('#my-processes-table').DataTable().ajax.reload();
                            updateStatistics();
                            alert('Tracking berhasil diupdate!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat update tracking');
                    }
                });
            }

            function openQcVerificationModal(processId) {
                currentProcessId = processId;
                loadProcessDataForQC(processId);
                $('#qcVerificationModal').modal('show');
            }

            function loadProcessDataForQC(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/data`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const job = response.job;

                            // Populate modal fields
                            $('#qcItemName').val(job.specification || '');
                            $('#qcItemSpec').val(job.specification || '');
                        }
                    },
                    error: function() {
                        alert('Error loading process data');
                    }
                });
            }

            function submitQcVerification() {
                if (!currentProcessId) return;

                const formData = {
                    verification_result: $('#qcVerificationResult').val(),
                    quality_score: $('#qcQualityScore').val(),
                    defects_found: $('#qcDefectsFound').val(),
                    recommendations: $('#qcRecommendations').val(),
                    verification_notes: $('#qcVerificationNotes').val(),
                    next_action: $('#qcNextAction').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: `/sipo/development/user-execution/process/${currentProcessId}/qc-verification`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#qcVerificationModal').modal('hide');
                            $('#my-processes-table').DataTable().ajax.reload();
                            updateStatistics();
                            alert('QC verification berhasil disubmit!');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat submit QC verification');
                    }
                });
            }
        </script>
    @endsection

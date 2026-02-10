@extends('main.layouts.main')
@section('title')
    Execute Process
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
    Execute Process
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Execute Process</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Execute Process</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-play-circle"></i>
                            Execute Process
                        </h4>
                        <p class="card-subtitle">Eksekusi process yang ditugaskan kepada Anda</p>
                    </div>
                    <div class="card-body">
                        <!-- Process Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">Job Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Job Code:</strong></td>
                                        <td id="jobCode">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Job Name:</strong></td>
                                        <td id="jobName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td id="jobType">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td id="customerName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Specification:</strong></td>
                                        <td id="specification">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Process Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Process:</strong></td>
                                        <td id="processName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td id="departmentName">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td id="processStatus">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td id="estimatedDuration">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Notes:</strong></td>
                                        <td id="processNotes">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info">Available Actions</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" onclick="openStartProcessModal()">
                                        <i class="mdi mdi-play"></i> Start Process
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="openCompleteProcessModal()">
                                        <i class="mdi mdi-check"></i> Complete Process
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="viewProcessDetails()">
                                        <i class="mdi mdi-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Conditional Action Buttons Based on Process Type -->
                        <div class="row mb-4" id="conditionalActions" style="display: none;">
                            <div class="col-12">
                                <h6 class="text-warning">Special Actions</h6>
                                <div id="specialActionButtons">
                                    <!-- Special action buttons will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Process History -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-secondary">Process History</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="processHistoryTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Action</th>
                                                <th>User</th>
                                                <th>Result</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="processHistoryBody">
                                            <!-- History will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Include All Modals -->
        @include('main.process.development.user-execution.process-modals')
        @include('main.process.development.user-execution.ppic-modal')
        @include('main.process.development.user-execution.purchasing-tracking-modal')
        @include('main.process.development.user-execution.qc-verification-modal')
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

            .btn-group .btn {
                margin-right: 5px;
                border-radius: 8px;
            }

            .table td {
                padding: 8px 12px;
                vertical-align: middle;
            }

            .table th {
                background-color: #f8f9fa;
                border-top: none;
                font-weight: 600;
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
                // Get process ID from URL path
                const pathParts = window.location.pathname.split('/');
                currentProcessId = pathParts[pathParts.length - 2]; // Get the ID before 'execute'

                if (currentProcessId && !isNaN(currentProcessId)) {
                    loadProcessData(currentProcessId);
                    loadProcessHistory(currentProcessId);
                } else {
                    alert('Process ID tidak ditemukan');
                    window.history.back();
                }
            });

            function loadProcessData(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/data`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const process = response.process;
                            const job = response.job;
                            currentProcessData = {
                                process,
                                job
                            };

                            // Populate job information
                            $('#jobCode').text(job.job_code);
                            $('#jobName').text(job.job_name);
                            $('#jobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                            $('#customerName').text(job.customer_name || '-');
                            $('#specification').text(job.specification || '-');

                            // Populate process information
                            $('#processName').text(process.process_name);
                            $('#departmentName').text(process.department ? process.department.divisi : '-');
                            $('#processStatus').text(process.status);
                            $('#estimatedDuration').text(process.estimated_duration ? process.estimated_duration +
                                ' hrs' : '-');
                            $('#processNotes').text(process.notes || '-');

                            // Load conditional actions based on process type and job type
                            loadConditionalActions(process, job);
                        }
                    },
                    error: function() {
                        alert('Error loading process data');
                    }
                });
            }

            function loadConditionalActions(process, job) {
                let specialActions = '';

                // Show conditional actions section
                $('#conditionalActions').show();

                // PPIC specific actions
                if (process.process_type === 'ppic' && (process.status === 'pending' || process.status === 'in_progress')) {
                    specialActions += `
                <button type="button" class="btn btn-warning" onclick="openPPICModal(${process.id})">
                    <i class="mdi mdi-calendar-clock"></i> PPIC Planning
                </button>
            `;
                }

                // Purchasing specific actions for trial_khusus
                if (job.type === 'trial_khusus' && process.process_type === 'purchasing' && (process.status === 'pending' || process.status === 'in_progress')) {
                    specialActions += `
                <button type="button" class="btn btn-warning" onclick="openPurchasingTrackingModal(${process.id})">
                    <i class="mdi mdi-truck-delivery"></i> Purchasing Tracking
                </button>
            `;
                }

                // QC specific actions for trial_khusus
                if (job.type === 'trial_khusus' && process.process_type === 'qc' && (process.status === 'pending' || process.status === 'in_progress')) {
                    specialActions += `
                <button type="button" class="btn btn-success" onclick="openQcVerificationModal(${process.id})">
                    <i class="mdi mdi-clipboard-check"></i> QC Verification
                </button>
            `;
                }

                if (specialActions) {
                    $('#specialActionButtons').html(specialActions);
                } else {
                    $('#conditionalActions').hide();
                }
            }

            function loadProcessHistory(processId) {
                $.ajax({
                    url: `/sipo/development/user-execution/process/${processId}/history`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.history) {
                            let historyHtml = '';
                            response.history.forEach(item => {
                                historyHtml += `
                            <tr>
                                <td>${new Date(item.action_at).toLocaleString('id-ID')}</td>
                                <td><span class="badge badge-info">${item.action_type.replace(/_/g, ' ')}</span></td>
                                <td>${item.user ? item.user.name : '-'}</td>
                                <td><span class="badge badge-${item.action_result === 'success' ? 'success' : 'warning'}">${item.action_result}</span></td>
                                <td>${item.action_notes || '-'}</td>
                            </tr>
                        `;
                            });
                            $('#processHistoryBody').html(historyHtml);
                        } else {
                            $('#processHistoryBody').html(
                                '<tr><td colspan="5" class="text-center">No history found</td></tr>');
                        }
                    },
                    error: function() {
                        $('#processHistoryBody').html(
                            '<tr><td colspan="5" class="text-center text-danger">Error loading history</td></tr>'
                            );
                    }
                });
            }

            function viewProcessDetails() {
                if (currentProcessData) {
                    const process = currentProcessData.process;
                    const job = currentProcessData.job;

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
            }

            function openStartProcessModal() {
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
                            alert('Process berhasil dimulai!');
                            // Reload data
                            loadProcessData(currentProcessId);
                            loadProcessHistory(currentProcessId);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat start process');
                    }
                });
            }

            function openCompleteProcessModal() {
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
                            alert('Process berhasil diselesaikan!');
                            // Reload data
                            loadProcessData(currentProcessId);
                            loadProcessHistory(currentProcessId);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat complete process');
                    }
                });
            }

            // Include all modal functions
        </script>

        <!-- Modal Functions -->
        <script>
            // Set today's date as default for date inputs
            $(document).ready(function() {
                $('#modalProductionDate').val(new Date().toISOString().split('T')[0]);
                $('#modalRequiredDate').val(new Date().toISOString().split('T')[0]);
            });

            // PPIC Modal Functions
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
                                $('#modalSubmitBtn').show().text('Ajukan Permohonan ke Purchasing');
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
                            alert('Data berhasil disubmit!');
                            // Reload data
                            loadProcessData(currentProcessId);
                            loadProcessHistory(currentProcessId);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat submit data');
                    }
                });
            }

            // Purchasing Tracking Functions
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
                            const process = response.process;
                            const job = response.job;
                            
                            // Populate job information
                            $('#trackingJobCode').text(job.job_code);
                            $('#trackingJobName').text(job.job_name);
                            $('#trackingJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                            $('#trackingCustomerName').text(job.customer_name || '-');
                            $('#trackingSpecification').text(job.specification || '-');
                            
                            // Populate process information
                            $('#trackingProcessName').text(process.process_name);
                            $('#trackingDepartmentName').text(process.department ? process.department.divisi : '-');
                            $('#trackingProcessStatus').text(process.status);
                            
                            // Load item request data if available
                            if (process.tracking_data && process.tracking_data.item_request) {
                                const itemRequest = process.tracking_data.item_request;
                                $('#trackingItemName').text(itemRequest.item_name || '-');
                                $('#trackingItemSpec').text(itemRequest.item_specification || '-');
                                $('#trackingQuantity').text(itemRequest.required_quantity || '-');
                                $('#trackingRequiredDate').text(itemRequest.required_date || '-');
                                $('#trackingPriority').text(itemRequest.priority_level || '-');
                                $('#trackingBudget').text(itemRequest.budget_estimate ? 'Rp ' + itemRequest.budget_estimate : '-');
                            }
                            
                            // Set today's date as default
                            $('#trackingOrderDate').val(new Date().toISOString().split('T')[0]);
                            $('#trackingDeliveryDate').val(new Date().toISOString().split('T')[0]);
                            $('#trackingReceivedDate').val(new Date().toISOString().split('T')[0]);
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
                    supplier_name: $('#trackingSupplierName').val(),
                    order_value: $('#trackingOrderValue').val(),
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
                            alert('Purchasing tracking berhasil diupdate!');
                            // Reload data
                            loadProcessData(currentProcessId);
                            loadProcessHistory(currentProcessId);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat update purchasing tracking');
                    }
                });
            }

            // QC Verification Functions
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
                            const process = response.process;
                            const job = response.job;
                            
                            // Populate job information
                            $('#qcJobCode').text(job.job_code);
                            $('#qcJobName').text(job.job_name);
                            $('#qcJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                            $('#qcCustomerName').text(job.customer_name || '-');
                            $('#qcSpecification').text(job.specification || '-');
                            
                            // Populate process information
                            $('#qcProcessName').text(process.process_name);
                            $('#qcDepartmentName').text(process.department ? process.department.divisi : '-');
                            $('#qcProcessStatus').text(process.status);
                            
                            // Load item request data if available
                            if (process.tracking_data && process.tracking_data.item_request) {
                                const itemRequest = process.tracking_data.item_request;
                                $('#qcItemName').text(itemRequest.item_name || '-');
                                $('#qcItemSpec').text(itemRequest.item_specification || '-');
                                $('#qcQuantity').text(itemRequest.required_quantity || '-');
                            }
                            
                            // Load purchasing tracking data if available
                            if (process.tracking_data && process.tracking_data.purchasing_tracking) {
                                const purchasingTracking = process.tracking_data.purchasing_tracking;
                                $('#qcSupplier').text(purchasingTracking.supplier_name || '-');
                                $('#qcReceivedDate').text(purchasingTracking.received_date || '-');
                                $('#qcOrderValue').text(purchasingTracking.order_value ? 'Rp ' + purchasingTracking.order_value : '-');
                            }
                            
                            // Set today's date as default
                            $('#qcInspectionDate').val(new Date().toISOString().split('T')[0]);
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
                    inspection_date: $('#qcInspectionDate').val(),
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
                            alert('QC verification berhasil disubmit!');
                            // Reload data
                            loadProcessData(currentProcessId);
                            loadProcessHistory(currentProcessId);
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

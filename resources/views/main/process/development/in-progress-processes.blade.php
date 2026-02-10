@extends('main.layouts.main')
@section('title')
    In Progress Processes
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
    In Progress Processes
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">In Progress Processes</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">In Progress Processes</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-progress-clock"></i>
                            In Progress Processes - Development
                        </h4>
                        <div class="card-tools">
                            <span class="badge badge-info">Monitoring semua proses yang sedang berjalan</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="totalInProgress">0</h4>
                                                <p class="mb-0">Total In Progress</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-progress-clock fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="ppicProcesses">0</h4>
                                                <p class="mb-0">PPIC Processes</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-calendar-clock fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="purchasingProcesses">0</h4>
                                                <p class="mb-0">Purchasing</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-cart fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0" id="qcProcesses">0</h4>
                                                <p class="mb-0">QC Processes</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="mdi mdi-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="in-progress-processes-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Job Code</th>
                                        <th>Job Name</th>
                                        <th>Process Name</th>
                                        <th>Process Type</th>
                                        <th>Department</th>
                                        <th>Assigned User</th>
                                        <th>Started At</th>
                                        <th>Duration (hrs)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 10px 10px 0 0 !important;
            }

            .badge {
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
            }

            .table th {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                color: white;
                border: none;
                font-weight: 600;
            }

            .btn-action {
                margin: 2px;
                border-radius: 20px;
            }

            .process-type-badge {
                padding: 0.3rem 0.8rem;
                border-radius: 15px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .process-type-ppic {
                background-color: #ffc107;
                color: #000;
            }

            .process-type-purchasing {
                background-color: #28a745;
                color: #fff;
            }

            .process-type-qc {
                background-color: #17a2b8;
                color: #fff;
            }

            .process-type-rnd_verification {
                background-color: #6f42c1;
                color: #fff;
            }

            .process-type-normal {
                background-color: #6c757d;
                color: #fff;
            }
        </style>
    @endsection


    @section('scripts')
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>


        <script>
            $(document).ready(function() {
                // Initialize DataTable
                const table = $('#in-progress-processes-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route('development.in-progress-processes.data') }}',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'job_development.job_code',
                            render: function(data, type, row) {
                                return `<strong>${data}</strong>`;
                            }
                        },
                        {
                            data: 'job_development.job_name',
                            render: function(data, type, row) {
                                return `<span class="text-primary">${data}</span>`;
                            }
                        },
                        {
                            data: 'process_name',
                            render: function(data, type, row) {
                                return `<strong>${data}</strong>`;
                            }
                        },
                        {
                            data: 'process_type',
                            render: function(data, type, row) {
                                const typeLabels = {
                                    'ppic': 'PPIC (Production)',
                                    'purchasing': 'Purchasing',
                                    'qc': 'Quality Control',
                                    'rnd_verification': 'RnD Verification',
                                    'normal': 'Normal Process'
                                };
                                return `<span class="process-type-badge process-type-${data}">${typeLabels[data] || data}</span>`;
                            }
                        },
                        {
                            data: 'department.divisi',
                            render: function(data, type, row) {
                                return `<span class="badge badge-secondary">${data}</span>`;
                            }
                        },
                        {
                            data: 'assigned_user.name',
                            render: function(data, type, row) {
                                const currentUserId = {{ Auth::id() }};
                                const isCurrentUser = row.assigned_user_id === currentUserId;
                                return `<span class="badge ${isCurrentUser ? 'badge-success' : 'badge-info'}">${data}</span>`;
                            }
                        },
                        {
                            data: 'started_at',
                            render: function(data, type, row) {
                                if (data) {
                                    return moment(data).format('DD/MM/YYYY HH:mm');
                                }
                                return '-';
                            }
                        },
                        {
                            data: 'estimated_duration',
                            render: function(data, type, row) {
                                return `<span class="badge badge-warning">${data} hrs</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                const currentUserId = {{ Auth::id() }};
                                let buttons = '<div class="btn-group btn-group-sm" role="group">';

                                // View Details button
                                buttons += `<button type="button" class="btn btn-info btn-action" onclick="viewProcessDetails(${row.id})" title="View Details">
                            <i class="mdi mdi-eye"></i>
                        </button>`;

                                // Execute button (only for assigned user)
                                if (row.assigned_user_id === currentUserId) {
                                    buttons += `<a href="/sipo/development/user-execution/process/${row.id}/execute" class="btn btn-success btn-action" title="Execute Process">
                                <i class="mdi mdi-play"></i>
                            </a>`;
                                }

                                buttons += '</div>';
                                return buttons;
                            }
                        }
                    ],
                    order: [
                        [6, 'desc']
                    ], // Sort by started_at descending
                    pageLength: 25,
                    language: {
                        "processing": "Sedang memproses...",
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data per halaman",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "loadingRecords": "Memuat...",
                        "zeroRecords": "Tidak ada data yang ditemukan",
                        "emptyTable": "Tidak ada data tersedia",
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

                // Update statistics
                function updateStatistics() {
                    $.ajax({
                        url: '{{ route('development.in-progress-processes.data') }}',
                        type: 'GET',
                        success: function(response) {
                            const processes = response.data;

                            // Count by process type
                            const ppicCount = processes.filter(p => p.process_type === 'ppic').length;
                            const purchasingCount = processes.filter(p => p.process_type === 'purchasing')
                                .length;
                            const qcCount = processes.filter(p => p.process_type === 'qc').length;

                            $('#totalInProgress').text(processes.length);
                            $('#ppicProcesses').text(ppicCount);
                            $('#purchasingProcesses').text(purchasingCount);
                            $('#qcProcesses').text(qcCount);
                        }
                    });
                }

                // Initial statistics update
                updateStatistics();

                // Refresh statistics every 30 seconds
                setInterval(updateStatistics, 30000);
            });

            // View Process Details
            function viewProcessDetails(processId) {
                $.ajax({
                    url: `/sipo/development/rnd-planning/processes/${processId}`,
                    type: 'GET',
                    success: function(response) {
                        const process = response.processes.find(p => p.id === processId);
                        const job = response;

                        if (process) {
                            const content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Job Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Job Code:</strong></td><td>${job.job_code}</td></tr>
                                    <tr><td><strong>Job Name:</strong></td><td>${job.job_name}</td></tr>
                                    <tr><td><strong>Type:</strong></td><td>${job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus'}</td></tr>
                                    <tr><td><strong>Priority:</strong></td><td>${job.priority}</td></tr>
                                    <tr><td><strong>Customer:</strong></td><td>${job.customer_name || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Process Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Process Name:</strong></td><td>${process.process_name}</td></tr>
                                    <tr><td><strong>Process Type:</strong></td><td>${process.process_type}</td></tr>
                                    <tr><td><strong>Department:</strong></td><td>${process.department ? process.department.divisi : '-'}</td></tr>
                                    <tr><td><strong>Assigned User:</strong></td><td>${process.assigned_user ? process.assigned_user.name : '-'}</td></tr>
                                    <tr><td><strong>Started At:</strong></td><td>${process.started_at ? moment(process.started_at).format('DD/MM/YYYY HH:mm') : '-'}</td></tr>
                                    <tr><td><strong>Duration:</strong></td><td>${process.estimated_duration} hours</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Process Notes</h6>
                                <p>${process.notes || 'No notes available'}</p>
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
        </script>
    @endsection

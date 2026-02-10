@extends('main.layouts.main')
@section('title')
    Report Development Item
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <style>
        .table-responsive {
            margin-top: 20px;
        }

        .table th {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            padding: 12px 8px;
            font-weight: bold;
            color: #5a5c69;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            border: 1px solid #e3e6f0;
            padding: 10px 8px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .percentage-cell {
            background-color: #d4edda;
            font-weight: bold;
            color: #155724;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #858796;
        }

        .loading i {
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

        .no-data {
            text-align: center;
            padding: 40px;
            color: #858796;
            font-style: italic;
        }

        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }
    </style>
@endsection
@section('page-title')
    Report Development Item
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Report Development Item</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Data</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e(date('Y-m-01')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="status_filter" name="status_filter">
                                <option value="">Semua Status</option>
                                <option value="DRAFT">Draft</option>
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS_PREPRESS">In Progress Prepress</option>
                                <option value="FINISH_PREPRESS">Finish Prepress</option>
                                <option value="MEETING_OPP">Meeting OPP</option>
                                <option value="READY_FOR_CUSTOMER">Ready for Customer</option>
                                <option value="SCHEDULED_FOR_PRODUCTION">Scheduled for Production</option>
                                <option value="PRODUCTION_COMPLETED">Production Completed</option>
                                <option value="PRODUCTION_APPROVED_BY_RND">Production Approved by R&D</option>
                                <option value="WAITING_MPP">Waiting MPP</option>
                                <option value="MPP_APPROVED">MPP Approved</option>
                                <option value="SALES_ORDER_CREATED">Sales Order Created</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-info" id="filter_data">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-secondary" id="reset_filter">
                                    <i class="mdi mdi-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Development</h6>
                <div>
                    <button type="button" class="btn btn-success btn-sm" id="export_excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="export_pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="developmentTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Job Code</th>
                                <th>Job Name</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Marketing</th>
                                <th>Tanggal Mulai</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Progress (%)</th>
                                <th>Prioritas</th>
                                <th>Job Type</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection

     @section('scripts')
     <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
     <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            let developmentTable;

            $(document).ready(function() {
                $('#filter_data').click(function() {
                    filterData();
                });
            });

            $(document).ready(function() {
                $('#reset_filter').click(function() {
                    resetFilter();
                });
            });

            function resetFilter() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#status_filter').val('');
                if (developmentTable) {
                    // Reset to no data loading mode
                    developmentTable.settings()[0].deferLoading = 0;
                    developmentTable.clear().draw();
                }
            }

            function filterData() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                
                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Silakan pilih tanggal mulai dan tanggal selesai terlebih dahulu.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                if (developmentTable) {
                    // Update deferLoading to enable data loading
                    developmentTable.settings()[0].deferLoading = null;
                    developmentTable.ajax.reload();
                }
            }



            $(document).ready(function() {
                $('#export_excel').click(function() {
                    exportToExcel();
                });
            });

            $(document).ready(function() {
                $('#export_pdf').click(function() {
                    exportToPDF();
                });
            });

            window.exportToExcel = function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const status = $('#status_filter').val();

                let url = "{{ route('report.development.export.excel') }}";
                url += `?start_date=${startDate}&end_date=${endDate}&status=${status}`;

                window.open(url, '_blank');
            };

            window.exportToPDF = function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const status = $('#status_filter').val();

                let url = "{{ route('report.development.export.pdf') }}";
                url += `?start_date=${startDate}&end_date=${endDate}&status=${status}`;

                window.open(url, '_blank');
            };

            $(document).ready(function() {
                // Initialize table without data first
                initializeTable();
            });

            function initializeTable() {
                developmentTable = $('#developmentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: 0, // Don't load data initially
                    ajax: {
                        url: "{{ route('report.development.data') }}",
                        type: 'GET',
                        data: function(d) {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                            d.status_filter = $('#status_filter').val();
                        },
                        dataSrc: function(json) {
                            // Check if dates are selected
                            const startDate = $('#start_date').val();
                            const endDate = $('#end_date').val();
                            
                            if (!startDate || !endDate) {
                                // Return empty data if dates not selected
                                return [];
                            }
                            
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'job_code',
                            name: 'job_code'
                        },
                        {
                            data: 'job_name',
                            name: 'job_name'
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
                            data: 'marketing_name',
                            name: 'marketing_name'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                return getStatusBadge(data);
                            }
                        },
                        {
                            data: 'progress',
                            name: 'progress',
                            render: function(data, type, row) {
                                return data + '%';
                            }
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            render: function(data, type, row) {
                                if (data === 'Urgent') {
                                    return '<span class="badge badge-danger">' + data + '</span>';
                                } else {
                                    return '<span class="badge badge-info">' + data + '</span>';
                                }
                            }
                        },
                        {
                            data: 'job_type',
                            name: 'job_type',
                            render: function(data, type, row) {
                                if (data === 'new') {
                                    return '<span class="badge badge-primary">Produk Baru</span>';
                                } else if (data === 'repeat') {
                                    return '<span class="badge badge-warning">Produk Repeat</span>';
                                } else {
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'notes',
                            name: 'notes'
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        emptyTable: "Silakan pilih tanggal mulai dan tanggal selesai untuk melihat data",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }

            function getStatusBadge(status) {
                const statusMap = {
                    'DRAFT': {
                        class: 'status-pending',
                        text: 'Draft'
                    },
                    'OPEN': {
                        class: 'status-in-progress',
                        text: 'Open'
                    },
                    'IN_PROGRESS_PREPRESS': {
                        class: 'status-in-progress',
                        text: 'In Progress Prepress'
                    },
                    'FINISH_PREPRESS': {
                        class: 'status-in-progress',
                        text: 'Finish Prepress'
                    },
                    'MEETING_OPP': {
                        class: 'status-in-progress',
                        text: 'Meeting OPP'
                    },
                    'READY_FOR_CUSTOMER': {
                        class: 'status-in-progress',
                        text: 'Ready for Customer'
                    },
                    'SCHEDULED_FOR_PRODUCTION': {
                        class: 'status-in-progress',
                        text: 'Scheduled for Production'
                    },
                    'PRODUCTION_COMPLETED': {
                        class: 'status-in-progress',
                        text: 'Production Completed'
                    },
                    'PRODUCTION_APPROVED_BY_RND': {
                        class: 'status-in-progress',
                        text: 'Production Approved by R&D'
                    },
                    'WAITING_MPP': {
                        class: 'status-in-progress',
                        text: 'Waiting MPP'
                    },
                    'MPP_APPROVED': {
                        class: 'status-in-progress',
                        text: 'MPP Approved'
                    },
                    'SALES_ORDER_CREATED': {
                        class: 'status-in-progress',
                        text: 'Sales Order Created'
                    },
                    'COMPLETED': {
                        class: 'status-completed',
                        text: 'Completed'
                    }
                };

                const statusInfo = statusMap[status] || {
                    class: 'status-pending',
                    text: status
                };
                return `<span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>`;
            }

        </script>
    @endsection

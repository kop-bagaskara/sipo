<?php $__env->startSection('title'); ?>
    RnD Workspace - Development
<?php $__env->stopSection(); ?>
<?php $__env->startSection('meta'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        /* Enhanced Table Styling */
        .table-enhanced {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .table-enhanced thead {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .table-enhanced thead th {
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table-enhanced tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        .table-enhanced tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-enhanced tbody td {
            padding: 12px;
            vertical-align: middle;
            border: none;
            font-size: 0.9rem;
        }


        /* Status Badge Enhancement */
        .status-badge-enhanced {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-draft-enhanced {
            background-color: #ffc107;
            color: #212529;
        }

        .status-open-enhanced {
            background-color: #17a2b8;
            color: white;
        }

        .status-in-progress-enhanced {
            background-color: #fd7e14;
            color: white;
        }

        .status-completed-enhanced {
            background-color: #28a745;
            color: white;
        }

        .status-meeting-opp-enhanced {
            background-color: #6f42c1;
            color: white;
        }

        .status-ready-customer-enhanced {
            background-color: #20c997;
            color: white;
        }

        .status-production-enhanced {
            background-color: #dc3545;
            color: white;
        }

        /* Job Type Badge */
        .job-type-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .job-type-new {
            background-color: #007bff;
            color: white;
        }

        .job-type-repeat {
            background-color: #6c757d;
            color: white;
        }


        /* Simple Text Styling */
        .customer-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .product-name {
            color: #6c757d;
        }

        /* Date Styling */
        .date-cell {
            font-size: 0.9rem;
            color: #495057;
        }

        .deadline-cell {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .deadline-overdue {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .deadline-warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .deadline-normal {
            color: #155724;
            background-color: #d4edda;
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* Job Code Styling */
        .job-code {
            font-weight: 600;
            color: #007bff;
        }

        /* Marketing User Styling */
        .marketing-user {
            font-weight: 500;
            color: #495057;
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_length select,
        .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .dataTables_filter input {
            width: 250px;
        }

        .dataTables_info {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .dataTables_paginate {
            margin-top: 15px;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            background: white;
            color: #007bff;
            transition: all 0.3s ease;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .dataTables_paginate .paginate_button.current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Loading Spinner */
        .table-loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .table-loading .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        /* Empty State */
        .dataTables_empty {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }

        .dataTables_empty:before {
            content: "\f1c0";
            font-family: "Material Design Icons";
            font-size: 3rem;
            display: block;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        /* Processing Overlay */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        /* Enhanced Card Styling */
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            padding: 20px;
        }

        .card-body {
            padding: 25px;
        }

        /* Filter Section Enhancement */
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }

        .filter-section .form-group {
            margin-bottom: 0;
        }

        .filter-section label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .filter-section .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            margin-right: 8px;
            transition: all 0.3s ease;
        }

        .filter-section .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Loading State for Table */
        .table-responsive.loading {
            position: relative;
        }

        .table-responsive.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Enhanced Form Controls */
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-control-sm {
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        /* Enhanced Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .page-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .stats-cards {
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #e9ecef;
            color: #495057;
        }

        .status-planning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-proof {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .type-trial_khusus {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .type-new {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }

        .type-repeat {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #ce93d8;
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-high {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .priority-medium {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .priority-low {
            background-color: #e8f5e8;
            color: #388e3c;
        }

        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .btn-send-prepress {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-send-prepress:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        /* Department Cards Responsive */
        .department-cards {
            display: flex;
            flex-wrap: nowrap;
        }

        .department-card {
            flex: 1;
            min-width: 0;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .department-cards {
                flex-wrap: wrap;
            }

            .department-card {
                flex: 1 1 50%;
                min-width: 0;
                margin-bottom: 15px;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            .dt-buttons {
                text-align: center;
                margin-bottom: 15px;
            }

            .dt-buttons .dt-button {
                margin: 2px;
                font-size: 12px;
                padding: 6px 12px;
            }

            .filter-section .d-flex {
                flex-direction: column;
                gap: 10px;
            }

            .filter-section .btn {
                width: 100%;
                margin-right: 0;
                margin-bottom: 5px;
            }

            .action-buttons {
                min-width: 100px;
            }

            .action-buttons .btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            .table-enhanced thead th {
                padding: 10px 8px;
                font-size: 0.75rem;
            }

            .table-enhanced tbody td {
                padding: 8px;
                font-size: 0.8rem;
            }

            .status-badge-enhanced {
                font-size: 0.65rem;
                padding: 4px 8px;
                min-width: 60px;
            }

            .job-type-badge {
                font-size: 0.6rem;
                padding: 3px 6px;
            }
        }

        @media (max-width: 576px) {
            .department-card {
                flex: 1 1 100%;
            }

            .card-body {
                padding: 15px;
            }

            .filter-section {
                padding: 15px;
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 2px;
            }

            .action-buttons .btn {
                flex: 1;
                min-width: 45px;
                font-size: 0.65rem;
                padding: 3px 6px;
            }
        }

        /* DataTable URGENT Row Styling - Simplified */
        .urgent-row {
            background-color: #ffebee !important;
        }

        .urgent-row td:nth-child(1) {
            background-color: #f44336 !important;
            color: white !important;
            font-weight: bold;
        }

        /* Horizontal Timeline Styling */
        .info-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-card h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .horizontal-timeline {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            margin-top: 50px;
            /* padding: 0 30px; */
            /* min-height: 100px; */
        }

        .horizontal-timeline::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 30px;
            right: 30px;
            height: 3px;
            background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%);
            z-index: 1;
            border-radius: 2px;
        }

        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
            min-width: 0;
        }

        .timeline-dot {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            z-index: 3;
        }

        .timeline-dot.bg-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .timeline-dot.bg-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .timeline-dot.bg-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .timeline-dot.bg-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .timeline-dot.bg-secondary {
            background: linear-gradient(135deg, #6c757d, #545b62);
        }

        .timeline-item.completed .timeline-dot {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .timeline-label {
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            text-align: center;
            max-width: 70px;
            line-height: 1.2;
            margin-top: 8px;
            word-wrap: break-word;
            white-space: normal;
        }

        .timeline-item.completed .timeline-label {
            color: #28a745;
            font-weight: 700;
        }

        .progress-summary {
            margin-top: 20px;
        }

        .progress {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, #007bff, #0056b3);
            transition: width 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Workspace - Development
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Workspace - Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Workspace</li>
                </ol>
            </div>
        </div>


        <!-- Department Workload Cards -->
        <div class="row department-cards">
            <div class="col department-card">
                <div class="card border-primary" id="marketingCard">
                    <div class="card-body text-center">
                        <i class="mdi mdi-account text-primary" style="font-size: 3rem;"></i>
                        <h3 class="card-title mt-2" id="marketingCount">0</h3>
                        <p class="card-text mb-1" style="font-size: 1.2rem;">Marketing</p>
                        <button class="btn btn-info" style="width: 100%;" onclick="filterByDepartment('MEETING_OPP')">
                            <i class="mdi mdi-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
            <div class="col department-card">
                <div class="card border-info" id="rndCard">
                    <div class="card-body text-center">
                        <i class="mdi mdi-flask text-info" style="font-size: 3rem;"></i>
                        <h3 class="card-title mt-2" id="rndCount">0</h3>
                        <p class="card-text mb-1" style="font-size: 1.2rem;">RnD</p>
                        <button class="btn btn-info" style="width: 100%;"
                            onclick="filterByDepartment('OPEN,WAITING_MPP,MPP_APPROVED,MPP_REJECTED')">
                            <i class="mdi mdi-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
            <div class="col department-card">
                <div class="card border-warning" id="ppicCard">
                    <div class="card-body text-center">
                        <i class="mdi mdi-calendar-clock text-warning" style="font-size: 3rem;"></i>
                        <h3 class="card-title mt-2" id="ppicCount">0</h3>
                        <p class="card-text mb-1" style="font-size: 1.2rem;">PPIC</p>
                        <button class="btn btn-info" style="width: 100%;"
                            onclick="filterByDepartment('READY_FOR_CUSTOMER')">
                            <i class="mdi mdi-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
            <div class="col department-card">
                <div class="card border-success" id="produksiCard">
                    <div class="card-body text-center">
                        <i class="mdi mdi-printer text-success" style="font-size: 3rem;"></i>
                        <h3 class="card-title mt-2" id="produksiCount">0</h3>
                        <p class="card-text mb-1" style="font-size: 1.2rem;">Produksi</p>
                        <button class="btn btn-info" style="width: 100%;"
                            onclick="filterByDepartment('SCHEDULED_FOR_PRODUCTION,PRODUCTION_APPROVED_BY_RND')">
                            <i class="mdi mdi-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
            <div class="col department-card">
                <div class="card border-secondary" id="prepressCard">
                    <div class="card-body text-center">
                        <i class="mdi mdi-image text-secondary" style="font-size: 3rem;"></i>
                        <h3 class="card-title mt-2" id="prepressCount">0</h3>
                        <p class="card-text mb-1" style="font-size: 1.2rem;">Prepress</p>
                        <button class="btn btn-info" style="width: 100%;"
                            onclick="filterByDepartment('IN_PROGRESS_PREPRESS')">
                            <i class="mdi mdi-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="mdi mdi-view-list text-primary"></i>
                            Daftar Job Development - Workspace
                        </h3>

                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="statusFilter">
                                            <i class="mdi mdi-filter"></i> Filter Status:
                                        </label>
                                        <select class="form-control" id="statusFilter">
                                            <option value="">Semua Status</option>
                                            <option value="DRAFT">Draft</option>
                                            <option value="OPEN">Open</option>
                                            <option value="IN_PROGRESS">In Progress</option>
                                            <option value="COMPLETED">Completed</option>
                                            <option value="MEETING_OPP">Meeting OPP</option>
                                            <option value="READY_FOR_CUSTOMER">Scheduling</option>
                                            <option value="SCHEDULED_FOR_PRODUCTION,PRODUCTION_APPROVED_BY_RND">Production
                                            </option>
                                            <option value="OPEN,WAITING_MPP,MPP_APPROVED,MPP_REJECTED">Development & Map
                                                Proof</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="d-flex flex-wrap">
                                            <button type="button" class="btn btn-info" id="refreshBtn">
                                                <i class="mdi mdi-refresh"></i> Refresh
                                            </button>
                                            <button type="button" class="btn btn-warning" id="resetFilterBtn">
                                                <i class="mdi mdi-filter-remove"></i> Reset Filter
                                            </button>
                                            <button type="button" class="btn btn-success" id="exportBtn">
                                                <i class="mdi mdi-download"></i> Export Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="rnd-workspace-table" class="table table-enhanced" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th> Customer</th>
                                        <th> Product</th>
                                        <th> Tanggal</th>
                                        <th> Deadline</th>
                                        <th> Status</th>
                                        <th> Marketing</th>
                                        <th> Type</th>
                                        <th> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi oleh DataTable -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal Konfirmasi Kirim ke Prepress -->
        <div class="modal fade" id="confirmSendModal" tabindex="-1" role="dialog"
            aria-labelledby="confirmSendModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmSendModalLabel">Konfirmasi Kirim ke Prepress</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <h6><i class="mdi mdi-alert"></i> Konfirmasi Kirim ke Prepress</h6>
                            <p class="mb-2">Apakah Anda yakin ingin mengirim job ini ke prepress?</p>
                            <p class="mb-1"><strong>Job Code:</strong> <span id="confirmJobCode"></span></p>
                            <p class="mb-1"><strong>Job Name:</strong> <span id="confirmJobName"></span></p>
                            <p class="mb-1"><strong>Customer:</strong> <span id="confirmCustomer"></span></p>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="mdi mdi-information"></i> Yang akan terjadi:</h6>
                            <ul class="mb-0">
                                <li>Status job akan berubah dari "DRAFT" menjadi "OPEN"</li>
                                <li>Job baru akan dibuat di tabel job prepress dengan status "OPEN"</li>
                                <li>Deadline prepress = 3 hari setelah tanggal development</li>
                                <li>PPIC dapat melanjutkan proses prepress</li>
                                <li>Job tidak dapat diubah lagi setelah dikirim ke prepress</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" id="confirmSendBtn">
                            <i class="mdi mdi-send"></i> Ya, Kirim ke Prepress
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Progress Timeline -->
        <div class="modal fade" id="progressTimelineModal" tabindex="-1" role="dialog"
            aria-labelledby="progressTimelineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="progressTimelineModalLabel">Progress Timeline</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="timelineContent">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat timeline...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Initialize Toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            $(document).ready(function() {
                // Store current status filter in a variable
                var currentStatusFilter = '';

                // Function to update dashboard cards
                function updateDashboardCards() {
                    $.ajax({
                        url: '<?php echo e(route('development.rnd-workspace.data')); ?>',
                        type: 'GET',
                        data: {
                            dashboard_only: true
                        },
                        success: function(response) {
                            if (response.data) {
                                // Update status counts
                                $('#draftCount').text(response.data.draft || 0);
                                $('#openCount').text(response.data.open || 0);
                                $('#inProgressCount').text(response.data.in_progress || 0);
                                $('#completedCount').text(response.data.completed || 0);

                                // Update department cards
                                updateDepartmentCards(response.data);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading dashboard data:', xhr);
                        }
                    });
                }

                // Function to update all department cards
                function updateDepartmentCards(data) {
                    // Update each department card
                    $('#marketingCount').text(data.meeting_opp || 0);
                    $('#rndCount').text(data.map_proof || 0);
                    $('#ppicCount').text(data.scheduling || 0);
                    $('#produksiCount').text(data.production || 0);
                    $('#prepressCount').text(data.prepress || 0);
                    $('#allCount').text((data.draft || 0) + (data.open || 0) + (data.in_progress || 0) + (data
                        .completed || 0) + (data.meeting_opp || 0) + (data.scheduling || 0) + (data
                        .production || 0) + (data.map_proof || 0));
                }

                // Load dashboard cards on page load
                updateDashboardCards();

                // Function to filter by department
                window.filterByDepartment = function(statusFilter) {
                    var departmentName = '';

                    // Map status filter to department name
                    switch (statusFilter) {
                        case 'MEETING_OPP':
                            departmentName = 'Meeting OPP (Marketing)';
                            break;
                        case 'OPEN,WAITING_MPP,MPP_APPROVED,MPP_REJECTED':
                            departmentName = 'Development & Map Proof (RnD)';
                            break;
                        case 'READY_FOR_CUSTOMER':
                            departmentName = 'Scheduling (PPIC)';
                            break;
                        case 'SCHEDULED_FOR_PRODUCTION,PRODUCTION_APPROVED_BY_RND':
                            departmentName = 'Production (Produksi)';
                            break;
                        case 'IN_PROGRESS_PREPRESS':
                            departmentName = 'Processing (Prepress)';
                            break;
                        case '':
                            departmentName = 'Semua Job Development';
                            break;
                        default:
                            departmentName = 'Job Development';
                    }

                    // Store filter in variable first
                    currentStatusFilter = statusFilter;
                    
                    // Set filter dropdown
                    $('#statusFilter').val(statusFilter);

                    // Update table title (only the main table title, not the card titles)
                    $('.row .col-12 .card .card-body h3.card-title').text('Daftar Job Development - ' +
                        departmentName);

                    // Force DataTable to reload with new filter
                    table.ajax.reload(function(json) {
                        // Callback after reload
                        console.log('Filter applied:', statusFilter, 'Records:', json.recordsFiltered);
                    }, false);

                    // Show notification
                    toastr.info('Filter Applied: Menampilkan task untuk ' + departmentName);
                };

                // Function to check progress
                window.checkProgress = function(jobId) {
                    // Redirect to job detail view
                    window.location.href = '/sipo/development/rnd-workspace/' + jobId + '/view';
                };

                // Function to show progress timeline modal
                window.showProgressTimeline = function(jobId) {
                    $('#progressTimelineModal').modal('show');
                    loadDetailedTimeline(jobId);
                };

                // Function to load detailed timeline
                function loadDetailedTimeline(jobId) {
                    // Reset content
                    $('#timelineContent').html(`
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat timeline...</p>
                        </div>
                    `);

                    // Get job data from DataTable
                    var table = $('#rnd-workspace-table').DataTable();
                    var jobData = table.rows().data().toArray().find(row => row.id == jobId);

                    if (jobData) {
                        // Generate simple timeline HTML
                        var timelineHtml = generateSimpleTimeline(jobData);
                        $('#timelineContent').html(timelineHtml);
                    } else {
                        $('#timelineContent').html(`
                            <div class="alert alert-warning text-center">
                                <i class="mdi mdi-alert-circle"></i>
                                <p class="mb-0">Data job tidak ditemukan.</p>
                            </div>
                        `);
                    }
                }

                // Function to generate horizontal timeline like in rnd-workspace-view
                function generateSimpleTimeline(jobData) {
                    var status = jobData.status_job;
                    var createdAt = moment(jobData.tanggal).format('DD/MM/YYYY HH:mm');
                    var deadline = jobData.job_deadline ? moment(jobData.job_deadline).format('DD/MM/YYYY') : '-';

                    // Calculate progress percentage
                    var progressPercentage = 0;
                    switch (status) {
                        case 'DRAFT':
                            progressPercentage = 8;
                            break;
                        case 'OPEN':
                        case 'IN_PROGRESS_PREPRESS':
                            progressPercentage = 17;
                            break;
                        case 'FINISH_PREPRESS':
                            progressPercentage = 25;
                            break;
                        case 'MEETING_OPP':
                            progressPercentage = 33;
                            break;
                        case 'READY_FOR_CUSTOMER':
                            progressPercentage = 50;
                            break;
                        case 'SCHEDULED_FOR_PRODUCTION':
                            progressPercentage = 58;
                            break;
                        case 'PRODUCTION_COMPLETED':
                            progressPercentage = 67;
                            break;
                        case 'PRODUCTION_APPROVED_BY_RND':
                            progressPercentage = 75;
                            break;
                        case 'WAITING_MPP':
                            progressPercentage = 83;
                            break;
                        case 'MPP_APPROVED':
                            progressPercentage = 92;
                            break;
                        case 'SALES_ORDER_CREATED':
                            progressPercentage = 95;
                            break;
                        case 'COMPLETED':
                            progressPercentage = 100;
                            break;
                        default:
                            progressPercentage = 0;
                    }

                    var html = `
                        <div class="info-card">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6><i class="mdi mdi-file-document"></i> Job Code: ${jobData.job_code}</h6>
                                    <h6><i class="mdi mdi-account"></i> Customer: ${jobData.customer}</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="mdi mdi-calendar"></i> Created: ${createdAt}</h6>
                                    <h6><i class="mdi mdi-clock"></i> Deadline: ${deadline}</h6>
                                </div>
                            </div>

                            <div class="horizontal-timeline">
                                <!-- Step 1: DRAFT -->
                                <div class="timeline-item ${['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                        <i class="mdi mdi-plus"></i>
                                    </div>
                                    <div class="timeline-label">Draft</div>
                                </div>

                                <!-- Step 2: PREPRESS -->
                                <div class="timeline-item ${['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                        <i class="mdi mdi-printer"></i>
                                    </div>
                                    <div class="timeline-label">Prepress</div>
                                </div>

                                <!-- Step 3: FINISH_PREPRESS -->
                                <div class="timeline-item ${['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                        <i class="mdi mdi-calendar-clock"></i>
                                    </div>
                                    <div class="timeline-label">Finish Prepress</div>
                                </div>

                                <!-- Step 4: MEETING_OPP -->
                                <div class="timeline-item ${['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-success' : 'bg-secondary'}">
                                        <i class="mdi mdi-file-document"></i>
                                    </div>
                                    <div class="timeline-label">Meeting OPP</div>
                                </div>

                                <!-- Step 5: MEETING_OK -->
                                <div class="timeline-item ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                        <i class="mdi mdi-check-circle"></i>
                                    </div>
                                    <div class="timeline-label">Meeting OK</div>
                                </div>

                                <!-- Step 6: READY_FOR_CUSTOMER -->
                                <div class="timeline-item ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                        <i class="mdi mdi-calendar-plus"></i>
                                    </div>
                                    <div class="timeline-label">ACC Customer</div>
                                </div>

                                <!-- Step 7: SCHEDULED_FOR_PRODUCTION -->
                                <div class="timeline-item ${['SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                        <i class="mdi mdi-calendar-plus"></i>
                                    </div>
                                    <div class="timeline-label">PPIC</div>
                                </div>

                                <!-- Step 8: PRODUCTION_COMPLETED -->
                                <div class="timeline-item ${['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-success' : 'bg-secondary'}">
                                        <i class="mdi mdi-factory"></i>
                                    </div>
                                    <div class="timeline-label">Produksi</div>
                                </div>

                                <!-- Step 9: PRODUCTION_APPROVED_BY_RND -->
                                <div class="timeline-item ${['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                        <i class="mdi mdi-file-document"></i>
                                    </div>
                                    <div class="timeline-label">Map Proof</div>
                                </div>

                                <!-- Step 10: WAITING_MPP -->
                                <div class="timeline-item ${['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                        <i class="mdi mdi-clock"></i>
                                    </div>
                                    <div class="timeline-label">Waiting MPP</div>
                                </div>

                                <!-- Step 11: MPP_APPROVED -->
                                <div class="timeline-item ${['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                    <div class="timeline-dot ${['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                        <i class="mdi mdi-cart-plus"></i>
                                    </div>
                                    <div class="timeline-label">Sales Order</div>
                                </div>

                                <!-- Step 12: COMPLETED -->
                                <div class="timeline-item ${status === 'COMPLETED' ? 'completed' : ''}">
                                    <div class="timeline-dot ${status === 'COMPLETED' ? 'bg-success' : 'bg-secondary'}">
                                        <i class="mdi mdi-flag-checkered"></i>
                                    </div>
                                    <div class="timeline-label">Completed</div>
                                </div>
                            </div>

                            <div class="progress-summary mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: ${progressPercentage}%">
                                                ${progressPercentage}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <span class="badge badge-${status === 'COMPLETED' ? 'success' : status === 'DRAFT' ? 'warning' : 'info'} mr-2">
                                            ${status}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    return html;
                }

                // Inisialisasi DataTable
                var table = $('#rnd-workspace-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    autoWidth: false,
                    scrollX: true,
                    searchDelay: 500,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    ajax: {
                        url: '<?php echo e(route('development.rnd-workspace.data')); ?>',
                        type: 'GET',
                        data: function(d) {
                            // Add status filter to AJAX request
                            // Use variable first, then fallback to dropdown value
                            var filterValue = currentStatusFilter || $('#statusFilter').val();
                            if (filterValue) {
                                d.status_filter = filterValue;
                                console.log('Sending status_filter:', filterValue);
                            } else {
                                console.log('No status_filter to send');
                            }
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return '<div class="text-center font-weight-bold">' + (meta.row + meta
                                    .settings._iDisplayStart + 1) + '</div>';
                            }
                        },
                        {
                            data: 'job_code',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }
                                // For display, return formatted HTML
                                return '<span class="job-code">' + data + '</span>';
                            }
                        },
                        {
                            data: 'customer',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }
                                // For display, return formatted HTML
                                return '<span class="customer-name">' + data + '</span>';
                            }
                        },
                        {
                            data: 'product',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }
                                // For display, return formatted HTML
                                return '<span class="product-name">' + data + '</span>';
                            }
                        },
                        {
                            data: 'tanggal',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }
                                // For display, return formatted HTML
                                return '<span class="date-cell">' + moment(data).format('DD/MM/YYYY') +
                                    '</span>';
                            }
                        },
                        {
                            data: 'job_deadline',
                            render: function(data, type, row) {
                                if (!data) {
                                    if (type === 'sort' || type === 'search') {
                                        return '';
                                    }
                                    return '<span class="text-muted">-</span>';
                                }

                                var deadline = moment(data);

                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data;
                                }

                                // For display, return formatted HTML
                                var today = moment();
                                var diffDays = deadline.diff(today, 'days');
                                var deadlineClass = 'deadline-normal';

                                if (diffDays < 0) {
                                    deadlineClass = 'deadline-overdue';
                                } else if (diffDays <= 2) {
                                    deadlineClass = 'deadline-warning';
                                }

                                return '<span class="deadline-cell ' + deadlineClass + '">' +
                                    deadline.format('DD/MM/YYYY') + '</span>';
                            }
                        },
                        {
                            data: 'status_job',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }

                                // For display, return formatted HTML
                                let badgeClass = 'status-badge-enhanced status-draft-enhanced';
                                let statusText = data;

                                switch (data) {
                                    case 'DRAFT':
                                        badgeClass = 'status-badge-enhanced status-draft-enhanced';
                                        break;
                                    case 'OPEN':
                                        badgeClass = 'status-badge-enhanced status-open-enhanced';
                                        break;
                                    case 'IN_PROGRESS':
                                    case 'IN_PROGRESS_PREPRESS':
                                        badgeClass =
                                        'status-badge-enhanced status-in-progress-enhanced';
                                        break;
                                    case 'COMPLETED':
                                        badgeClass = 'status-badge-enhanced status-completed-enhanced';
                                        break;
                                    case 'MEETING_OPP':
                                        badgeClass =
                                        'status-badge-enhanced status-meeting-opp-enhanced';
                                        break;
                                    case 'READY_FOR_CUSTOMER':
                                        badgeClass =
                                            'status-badge-enhanced status-ready-customer-enhanced';
                                        break;
                                    case 'SCHEDULED_FOR_PRODUCTION':
                                    case 'PRODUCTION_APPROVED_BY_RND':
                                        badgeClass = 'status-badge-enhanced status-production-enhanced';
                                        break;
                                    default:
                                        badgeClass = 'status-badge-enhanced status-draft-enhanced';
                                }

                                return '<span class="' + badgeClass + '">' + statusText + '</span>';
                            }
                        },
                        {
                            data: 'marketingUser.name',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }
                                // For display, return formatted HTML
                                return data ? '<span class="marketing-user">' + data + '</span>' :
                                    '<span class="text-muted">-</span>';
                            },
                            defaultContent: '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'job_type',
                            render: function(data, type, row) {
                                // For sorting and searching, return the raw data
                                if (type === 'sort' || type === 'search') {
                                    return data || '';
                                }

                                // For display, return formatted HTML
                                if (data === 'new') {
                                    return '<span class="job-type-badge job-type-new">Baru</span>';
                                } else if (data === 'repeat') {
                                    return '<span class="job-type-badge job-type-repeat">Repeat</span>';
                                }
                                return '<span class="job-type-badge job-type-new">' + data + '</span>';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let currentUserId = <?php echo e(auth()->id()); ?>;
                                let isCreator = row.marketing_user_id == currentUserId;
                                let canEdit = isCreator && row.status_job === 'OPEN';
                                let canDelete = isCreator && row.status_job === 'OPEN';

                                let buttons = '';

                                // Tombol Detail - Semua user bisa lihat
                                buttons +=
                                    '<a href="<?php echo e(route('development.rnd-workspace.view', ':id')); ?>'
                                    .replace(':id', row.id) +
                                    '" class="btn btn-sm btn-info" data-id="' + row.id +
                                    '" title="Lihat Detail" style="margin-bottom: 2px;">Detail</a><br>';

                                // Tombol Edit - Hanya pembuat dan status OPEN
                                if (canEdit) {
                                    buttons +=
                                        '<a href="<?php echo e(route('development.rnd-workspace.edit', ':id')); ?>'
                                        .replace(':id', row.id) +
                                        '" class="btn btn-sm btn-warning" data-id="' + row.id +
                                        '" title="Edit Job" style="margin-bottom: 2px;">Edit</a><br>';
                                }

                                // Tombol Hapus - Hanya pembuat dan status OPEN
                                if (canDelete) {
                                    buttons +=
                                        '<button class="btn btn-sm btn-danger delete-job-btn" data-id="' +
                                        row.id +
                                        '" title="Hapus Job" style="margin-bottom: 2px;">Hapus</button><br>';
                                }

                                // Tombol Send to Prepress - Hanya jika status DRAFT
                                if (row.status_job === 'DRAFT') {
                                    buttons +=
                                        '<a href="<?php echo e(route('development.rnd-workspace.send-to-prepress', ':id')); ?>'
                                        .replace(':id', row.id) +
                                        '" class="btn btn-sm btn-success send-prepress-btn" data-id="' +
                                        row.id +
                                        '" title="Kirim ke Prepress" style="margin-bottom: 2px;">Send to Prepress</a><br>';
                                }

                                // Tombol Progress Timeline - Semua user bisa lihat
                                buttons +=
                                    '<button class="btn btn-sm btn-secondary" onclick="showProgressTimeline(' +
                                    row.id +
                                    ')" title="Lihat Progress Timeline">Progress</button>';

                                return buttons;
                            }
                        }
                    ],
                    order: [
                        [5, 'desc']
                    ], // Sort by tanggal descending
                    pageLength: 10,
                    rowCallback: function(row, data, index) {
                        // Add urgent styling for urgent jobs
                        if (data.prioritas_job === 'Urgent') {
                            $(row).addClass('urgent-row');
                        }
                    },
                    language: {
                        "sProcessing": '<div class="table-loading"><div class="spinner-border text-primary" role="status"><span class="sr-only">Memproses data...</span></div><p class="mt-2">Memproses data...</p></div>',
                        "sLengthMenu": "Tampilkan _MENU_ entri per halaman",
                        "sZeroRecords": '<div class="dataTables_empty"><i class="mdi mdi-information-outline"></i><br>Tidak ditemukan data yang sesuai dengan kriteria pencarian</div>',
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoPostFix": "",
                        "sSearch": 'Cari:',
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": 'Pertama',
                            "sPrevious": 'Sebelumnya',
                            "sNext": 'Selanjutnya',
                            "sLast": 'Terakhir'
                        }
                    },
                    initComplete: function() {
                        // Add custom styling after table initialization
                        $('.dataTables_length select').addClass('form-control-sm');
                        $('.dataTables_filter input').addClass('form-control-sm');

                        // Add loading state management
                        $(this).on('processing.dt', function(e, settings, processing) {
                            if (processing) {
                                $('.table-responsive').addClass('loading');
                            } else {
                                $('.table-responsive').removeClass('loading');
                            }
                        });
                    }
                });

                // Event handler untuk tombol view job
                // $(document).on('click', '.view-job-btn', function() {
                //     var jobId = $(this).data('id');
                //     console.log('View button clicked for job ID:', jobId);

                //     // Redirect ke halaman view detail
                //     var url = '<?php echo e(route('development.rnd-workspace.view', ':id')); ?>'.replace(':id', jobId);
                //     console.log('Redirecting to:', url);
                //     window.location.href = url;
                // });

                // Event handler untuk tombol send ke prepress
                $(document).on('click', '.send-prepress-btn', function() {
                    var jobId = $(this).data('id');
                    var jobCode = $(this).closest('tr').find('td:eq(1)').text();
                    var jobName = $(this).closest('tr').find('td:eq(2)').text();
                    var customer = $(this).closest('tr').find('td:eq(3)').text();

                    showConfirmSendModal(jobId, jobCode, jobName, customer);
                });

                // Event handler untuk tombol hapus job
                $(document).on('click', '.delete-job-btn', function() {
                    var jobId = $(this).data('id');
                    var jobCode = $(this).closest('tr').find('td:eq(1)').text();
                    var jobName = $(this).closest('tr').find('td:eq(2)').text();
                    var customer = $(this).closest('tr').find('td:eq(3)').text();

                    // Konfirmasi hapus dengan SweetAlert
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        html: `
                            <div class="text-left">
                                <p><strong>Job Code:</strong> ${jobCode}</p>
                                <p><strong>Job Name:</strong> ${jobName}</p>
                                <p><strong>Customer:</strong> ${customer}</p>
                                <br>
                                <p class="text-danger"><strong>Apakah Anda yakin ingin menghapus job ini?</strong></p>
                                <p class="text-warning">Tindakan ini tidak dapat dibatalkan!</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        width: '500px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteJob(jobId);
                        }
                    });
                });

                // Event handler untuk tombol konfirmasi send
                $('#confirmSendBtn').click(function() {
                    var jobId = $(this).data('job-id');
                    sendJobToPrepress(jobId);
                });





                // Function untuk menampilkan modal konfirmasi send
                function showConfirmSendModal(jobId, jobCode, jobName, customer) {
                    $('#confirmJobCode').text(jobCode);
                    $('#confirmJobName').text(jobName);
                    $('#confirmCustomer').text(customer);
                    $('#confirmSendBtn').data('job-id', jobId);
                    $('#confirmSendModal').modal('show');
                }

                // Function untuk send job ke prepress
                function sendJobToPrepress(jobId) {
                    // Tampilkan loading
                    toastr.info('Memproses... Sedang mengirim job ke prepress', 'Loading...', {
                        timeOut: 0,
                        extendedTimeOut: 0,
                        closeButton: false
                    });

                    $.ajax({
                        url: '<?php echo e(route('development.rnd-workspace.send-to-prepress', ':id')); ?>'.replace(':id',
                            jobId),
                        type: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            // Clear all toasts
                            toastr.clear();

                            if (response.success) {
                                // Tutup modal
                                $('#confirmSendModal').modal('hide');

                                // Refresh table
                                table.ajax.reload();

                                // Tampilkan notifikasi sukses
                                toastr.success(response.message, 'Berhasil!');
                            } else {
                                toastr.error(response.message, 'Error!');
                            }
                        },
                        error: function(xhr) {
                            // Clear all toasts
                            toastr.clear();

                            var message = 'Terjadi kesalahan saat mengirim job ke prepress';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            toastr.error(message, 'Error!');
                        }
                    });
                }

                // Function untuk hapus job
                function deleteJob(jobId) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus job development',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?php echo e(route('development.rnd-workspace.delete', ':id')); ?>'.replace(':id', jobId),
                        type: 'DELETE',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Tutup loading
                                Swal.close();

                                // Refresh table
                                table.ajax.reload();

                                // Tampilkan notifikasi sukses
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();

                            var message = 'Terjadi kesalahan saat menghapus job development';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: message,
                                icon: 'error'
                            });
                        }
                    });
                }



                // Function untuk mengupdate statistik
                function updateStatistics() {
                    var data = table.data().toArray();
                    var total = data.length;
                    var draft = data.filter(function(row) {
                        return row.status_job === 'DRAFT';
                    }).length;
                    var open = data.filter(function(row) {
                        return row.status_job === 'OPEN';
                    }).length;
                    var completed = data.filter(function(row) {
                        return row.status_job === 'COMPLETED';
                    }).length;

                    // Update statistik cards
                    $('#totalJobs').text(total);
                    $('#draftJobs').text(draft);
                    $('#openJobs').text(open);
                    $('#completedJobs').text(completed);
                }

                // Update statistik setiap kali data berubah
                table.on('draw', function() {
                    updateStatistics();
                });

                // Update statistik pertama kali
                table.on('init', function() {
                    updateStatistics();
                });

                // Event handler untuk filter status
                $('#statusFilter').change(function() {
                    // Update current filter variable
                    currentStatusFilter = $(this).val();
                    // Reload table dengan filter baru via AJAX (server-side)
                    table.ajax.reload(null, false);
                });

                // Event handler untuk tombol refresh
                $('#refreshBtn').click(function() {
                    table.ajax.reload();
                });

                // Event handler untuk tombol reset filter
                $('#resetFilterBtn').click(function() {
                    currentStatusFilter = '';
                    $('#statusFilter').val('');
                    $('.row .col-12 .card .card-body h3.card-title').text('Daftar Job Development - Workspace');
                    table.ajax.reload();

                    toastr.success('Semua filter telah direset', 'Filter Reset');
                });

                // Event handler untuk tombol export
                $('#exportBtn').click(function() {
                    // Implementasi export (bisa ke Excel atau CSV)
                    var data = table.data().toArray();
                    if (data.length === 0) {
                        toastr.warning(
                            'Tidak ada data untuk diexport. Pastikan ada job development yang tersedia.',
                            'Warning!');
                        return;
                    }

                    // Export sederhana ke CSV
                    exportToCSV(data);

                    // Tampilkan notifikasi sukses
                    toastr.success('Data berhasil diexport ke CSV', 'Export Berhasil!');
                });

                // Function untuk export ke CSV
                function exportToCSV(data) {
                    var csv =
                        'Job Code,Job Name,Customer,Product,Tanggal,Deadline,Prioritas,Status,Marketing,Job Type,Progress\n';

                    data.forEach(function(row) {
                        var progress = '';
                        if (row.started_at) progress += 'Started ';
                        if (row.assigned_to_ppic_at) progress += 'Assigned ';
                        if (row.completed_at) progress += 'Completed';
                        if (!progress) progress = 'Pending';

                        csv += '"' + row.job_code + '","' + row.job_name + '","' + row.customer + '","' + row
                            .product + '","' +
                            moment(row.tanggal).format('DD/MM/YYYY') + '","' + (row.job_deadline ? moment(row
                                .job_deadline).format('DD/MM/YYYY') : '-') + '","' +
                            row.prioritas_job + '","' + row.status_job + '","' + (row.marketing_user ? row
                                .marketing_user.name : '-') + '","' +
                            row.job_type + '","' + progress + '"\n';
                    });

                    var blob = new Blob([csv], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    var link = document.createElement("a");
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", "rnd-workspace-" + moment().format('YYYY-MM-DD') + ".csv");
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/development/rnd-workspace.blade.php ENDPATH**/ ?>
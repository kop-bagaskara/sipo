@extends('main.layouts.main')
@section('title')
    Detail Job Development - RnD Workspace
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
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

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .info-card h4 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 0;
        }

        .info-table td {
            padding: 8px 12px;
            border: none;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: #495057;
            width: 35%;
        }

        .info-table td:last-child {
            color: #212529;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-open {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-in_progress {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
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

        .btn-send-prepress {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-send-prepress:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .material-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .material-item h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        .change-details {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            /* background: #f8f9fa; */
            border-radius: 6px;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-date {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .file-list {
            list-style: none;
            padding: 0;
        }

        .file-list li {
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .file-list li i {
            margin-right: 8px;
            color: #007bff;
        }

        /* Horizontal Timeline Styles */
        .horizontal-timeline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            /* padding: 20px 0; */
            /* margin: 20px 0; */
        }

        .horizontal-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            /* background: linear-gradient(90deg, #e9ecef 0%, #007bff 50%, #e9ecef 100%); */
            transform: translateY(-50%);
            z-index: 1;
        }

        .timeline-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            flex: 1;
        }

        .timeline-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            border: 3px solid white;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); */
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .timeline-item.completed .timeline-dot {
            transform: scale(1.1);
            /* box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); */
        }

        .timeline-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-align: center;
            white-space: nowrap;
            margin-bottom: 4px;
        }

        .timeline-item.completed .timeline-label {
            color: #28a745;
            font-weight: 700;
        }

        .timeline-user {
            font-size: 0.7rem;
            color: #6c757d;
            text-align: center;
            white-space: nowrap;
            font-style: italic;
        }

        .timeline-item.completed .timeline-user {
            color: #495057;
            font-weight: 500;
        }

        /* Compact Timeline Styles - 1 Baris */
        .compact-timeline {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
        }

        .compact-timeline-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin-bottom: 6px;
            background: white;
            border-radius: 6px;
            border-left: 3px solid #dee2e6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .compact-timeline-item:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transform: translateX(2px);
        }

        .compact-timeline-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .compact-timeline-content {
            display: flex;
            align-items: center;
            flex: 1;
            gap: 12px;
            font-size: 0.85rem;
        }

        .compact-timeline-text {
            font-weight: 600;
            color: #333;
            flex: 1;
        }

        .compact-timeline-user {
            color: #6c757d;
            font-style: italic;
        }

        .compact-timeline-status {
            color: #495057;
        }

        .compact-timeline-time {
            color: #6c757d;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .badge-sm {
            font-size: 0.65rem;
            padding: 2px 6px;
        }

        /* Read-only form styling */
        .form-control.readonly,
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
        }

        .form-control:disabled {
            background-color: #e9ecef;
            border-color: #ced4da;
            color: #6c757d;
            cursor: not-allowed;
        }

        .btn.disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Detailed Timeline Styles (Fallback) */
        .timeline-vertical {
            position: relative;
            padding-left: 30px;
        }

        .timeline-vertical::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item-detailed {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-dot-detailed {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            z-index: 2;
        }

        .timeline-content-detailed {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .timeline-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .timeline-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .timeline-body {
            font-size: 0.9rem;
        }

        .timeline-body .timeline-user {
            margin: 5px 0;
            color: #495057;
        }

        .timeline-status {
            margin: 5px 0;
        }

        .timeline-notes {
            margin: 5px 0;
            font-style: italic;
            color: #6c757d;
        }

        /* Progress Summary */
        .progress-summary {
            margin-top: 15px;
        }

        .progress {
            height: 20px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #007bff, #28a745);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .status-badge.draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.planning {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-badge.open {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.in_progress {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.sales_order_created {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.finish_prepress {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Responsive Timeline */
        @media (max-width: 768px) {
            .horizontal-timeline {
                flex-wrap: wrap;
                gap: 10px;
            }

            .timeline-item {
                flex: 0 0 calc(50% - 5px);
                margin-bottom: 15px;
            }

            .timeline-dot {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .timeline-label {
                font-size: 0.7rem;
            }
        }
    </style>
@endsection
@section('page-title')
    Detail Job Development - RnD Workspace
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Job Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.index') }}">RnD Workspace</a>
                    </li>
                    <li class="breadcrumb-item active">Detail Job</li>
                </ol>
            </div>
        </div>

        <!-- Header Info -->
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 style="color: white;">{{ $job->job_name }}</h3>
                    <p class="mb-0">Job Code: {{ $job->job_code }} | Customer: {{ $job->customer }}</p>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- Informasi Dasar -->
            <div class="col-md-6">
                <div class="info-card">
                    <h4><i class="mdi mdi-information"></i> Informasi Dasar</h4>
                    <table class="info-table">
                        <tr>
                            <td>Job Code</td>
                            <td>: {{ $job->job_code }}</td>
                        </tr>
                        <tr>
                            <td>Job Name</td>
                            <td>: {{ $job->job_name }}</td>
                        </tr>
                        <tr>
                            <td>Customer</td>
                            <td>: {{ $job->customer }}</td>
                        </tr>
                        <tr>
                            <td>Product</td>
                            <td>: {{ $job->product }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($job->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Deadline</td>
                            <td>:
                                {{ $job->job_deadline ? \Carbon\Carbon::parse($job->job_deadline)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <span
                                    class="status-badge status-{{ strtolower($job->status_job) }}">{{ $job->status_job }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Prioritas</td>
                            <td>:
                                @if ($job->prioritas_job === 'Urgent')
                                    <span class="priority-badge priority-high">{{ $job->prioritas_job }}</span>
                                @else
                                    <span class="priority-badge priority-low">{{ $job->prioritas_job }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Marketing</td>
                            <td>: {{ $job->marketingUser->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Spesifikasi Teknis -->
            <div class="col-md-6">
                <div class="info-card">
                    <h4><i class="mdi mdi-file-document"></i> Spesifikasi Teknis</h4>
                    <table class="info-table">
                        <tr>
                            <td>Kode Design</td>
                            <td>: {{ $job->kode_design ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Dimension</td>
                            <td>: {{ $job->dimension ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Material</td>
                            <td>: {{ $job->material ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Total Color</td>
                            <td>: {{ $job->total_color ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Qty Order</td>
                            <td>: {{ $job->qty_order_estimation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Job Type</td>
                            <td>:
                                @if ($job->job_type === 'new')
                                    <span class="type-badge type-new">Produk Baru</span>
                                @elseif($job->job_type === 'repeat')
                                    <span class="type-badge type-repeat">Produk Repeat</span>
                                @else
                                    {{ $job->job_type }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            {{-- attachment --}}
                            <td>Attachment</td>
                            <td>:
                                @if ($job->attachment_paths && is_array($job->attachment_paths) && count($job->attachment_paths) > 0)
                                    @foreach ($job->attachment_paths as $index => $attachment)
                                        {{-- <div class="mb-1"> --}}
                                        <a href="/sipo_krisan/storage/app/public/{{ $attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-file"></i> Attachment {{ $index + 1 }}
                                        </a>
                                        {{-- </div> --}}
                                    @endforeach
                                @elseif ($job->attachment_paths && is_string($job->attachment_paths))
                                    <a href="{{ asset('storage/' . $job->attachment_paths) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-file"></i> View Attachment
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Perubahan (untuk Produk Repeat) -->
        @if ($job->job_type === 'repeat' && $job->change_details)
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-update"></i> Detail Perubahan</h4>
                        <div class="change-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Persentase Perubahan:</strong> {{ $job->change_percentage ?? 0 }}%
                                </div>
                                <div class="col-md-6">
                                    <strong>Jenis Perubahan:</strong>
                                    @if (is_array($job->change_details))
                                        {{ implode(', ', $job->change_details) }}
                                    @else
                                        {{ $job->change_details }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Job Order Details -->
        @if ($job->job_order && is_array($job->job_order) && count($job->job_order) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-details"></i> Job Order Details</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Jenis Pekerjaan</th>
                                        <th>Unit Job</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($job->job_order as $order)
                                        <tr>
                                            <td>{{ $order['jenis_pekerjaan'] ?? $order }}</td>
                                            <td>{{ $order['unit_job'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Catatan -->
        @if ($job->catatan)
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-note-text"></i> Catatan</h4>
                        <p class="mb-0">{{ $job->catatan }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Progress Timeline -->
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h4><i class="mdi mdi-history"></i> Progress Timeline</h4>

                    <div class="horizontal-timeline" style="width: 100%;">
                        <!-- Step 1: DRAFT (QUALITY) - RnD Mengirimkan Tugas ke Prepress -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-primary' : 'bg-secondary' }}">
                                <i class="mdi mdi-plus"></i>
                            </div>
                            <div class="timeline-label">Draft</div>
                        </div>

                        <!-- Step 2: IN_PROGRESS (PREPRESS) - Prepress Mengerjakan Job -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-info' : 'bg-secondary' }}">
                                <i class="mdi mdi-printer"></i>
                            </div>
                            <div class="timeline-label">Prepress</div>
                        </div>

                        <!-- Step 3: FINISH_PREPRESS (MARKETING) - Marketing Menjadwalkan Meeting OPP -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-warning' : 'bg-secondary' }}">
                                <i class="mdi mdi-calendar-clock"></i>
                            </div>
                            <div class="timeline-label">Finish Prepress</div>
                        </div>

                        <!-- Step 4: MEETING_OPP (QUALITY) - RnD Approve/Reject Hasil Job Prepress -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-success' : 'bg-secondary' }}">
                                <i class="mdi mdi-file-document"></i>
                            </div>
                            <div class="timeline-label">Meeting OPP</div>
                        </div>

                        <!-- Step 5: MEETING_OPP_OK (MARKETING) - Marketing Konfirmasi Hasil OPP -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-primary' : 'bg-secondary' }}">
                                <i class="mdi mdi-check-circle"></i>
                            </div>
                            <div class="timeline-label">Meeting OK</div>
                        </div>

                        <!-- Step 6: READY_FOR_CUSTOMER (PPIC) - PPIC Penjadwalan Setelah OPP -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-info' : 'bg-secondary' }}">
                                <i class="mdi mdi-calendar-plus"></i>
                            </div>
                            <div class="timeline-label">ACC Customer</div>
                        </div>

                        <!-- Step 7: SCHEDULED_FOR_PRODUCT (PRODUKSI) - Produksi Laporan Hasil -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['SCHEDULED_FOR_PRODUCTION', 'IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-warning' : 'bg-secondary' }}">
                                <i class="mdi mdi-calendar-plus"></i>
                            </div>
                            <div class="timeline-label">PPIC</div>
                        </div>

                        <!-- Step 8: IN_PRODUCTION (PRODUKSI) - Sedang dalam proses produksi -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['IN_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-warning' : 'bg-secondary' }}">
                                <i class="mdi mdi-factory"></i>
                            </div>
                            <div class="timeline-label">Produksi</div>
                        </div>

                        <!-- Step 9: PRODUCTION_COMPLETED (QUALITY) - RnD Approve Hasil Produksi -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-success' : 'bg-secondary' }}">
                                <i class="mdi mdi-check-circle"></i>
                            </div>
                            <div class="timeline-label">Prod. Completed</div>
                        </div>

                        <!-- Step 10: PRODUCTION_APPROVED_BY_RND (MARKETING) - Marketing Upload Map Proof -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-primary' : 'bg-secondary' }}">
                                <i class="mdi mdi-file-document"></i>
                            </div>
                            <div class="timeline-label">Map Proof</div>
                        </div>

                        <!-- Step 11: WAITING_MPP (MARKETING) - Marketing Konfirmasi Customer -->
                        <div
                            class="timeline-item {{ in_array($job->status_job, ['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-info' : 'bg-secondary' }}">
                                <i class="mdi mdi-clock"></i>
                            </div>
                            <div class="timeline-label">Waiting MPP</div>
                        </div>

                        <!-- Step 12: MPP_APPROVED (MARKETING) - Marketing Create SO -->
                        {{-- <div
                            class="timeline-item {{ in_array($job->status_job, ['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ in_array($job->status_job, ['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED']) ? 'bg-warning' : 'bg-secondary' }}">
                                <i class="mdi mdi-cart-plus"></i>
                            </div>
                            <div class="timeline-label">Sales Order</div>
                        </div> --}}

                        <!-- Step 13: COMPLETED - Development Closed -->
                        <div class="timeline-item {{ $job->status_job === 'COMPLETED' ? 'completed' : '' }}">
                            <div
                                class="timeline-dot {{ $job->status_job === 'COMPLETED' ? 'bg-success' : 'bg-secondary' }}">
                                <i class="mdi mdi-flag-checkered"></i>
                            </div>
                            <div class="timeline-label">Completed</div>
                        </div>
                    </div>

                    <div class="progress-summary mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="progress">
                                    @php
                                        $progressPercentage = 0;
                                        switch ($job->status_job) {
                                            case 'DRAFT':
                                                $progressPercentage = 8; // 1/12 * 100
                                                break;
                                            case 'OPEN':
                                            case 'IN_PROGRESS_PREPRESS':
                                                $progressPercentage = 17; // 2/12 * 100
                                                break;
                                            case 'FINISH_PREPRESS':
                                                $progressPercentage = 25; // 3/12 * 100
                                                break;
                                            case 'MEETING_OPP':
                                                $progressPercentage = 33; // 4/12 * 100
                                                break;
                                            case 'READY_FOR_CUSTOMER':
                                                $progressPercentage = 50; // 6/12 * 100
                                                break;
                                            case 'SCHEDULED_FOR_PRODUCTION':
                                                $progressPercentage = 58; // 7/12 * 100
                                                break;
                                            case 'PRODUCTION_COMPLETED':
                                                $progressPercentage = 67; // 8/12 * 100
                                                break;
                                            case 'PRODUCTION_APPROVED':
                                                $progressPercentage = 75; // 9/12 * 100
                                                break;
                                            case 'WAITING_MPP':
                                                $progressPercentage = 83; // 10/12 * 100
                                                break;
                                            case 'MPP_APPROVED':
                                                $progressPercentage = 92; // 11/12 * 100
                                                break;
                                            case 'SALES_ORDER_CREATED':
                                                $progressPercentage = 95; // Almost done
                                                break;
                                            case 'COMPLETED':
                                                $progressPercentage = 100; // 12/12 * 100
                                                break;
                                            default:
                                                $progressPercentage = 0;
                                        }
                                    @endphp
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $progressPercentage }}%">
                                        {{ $progressPercentage }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="status-badge {{ strtolower($job->status_job) }} mr-2">
                                    {{ $job->status_job }}
                                </span>
                                <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal"
                                    data-target="#detailedTimelineModal">
                                    <i class="mdi mdi-history"></i> View Detailed Timeline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Action Buttons Section -->
        <div class="row">
            <div class="col-12">
                @if ($job->status_job === 'COMPLETED')
                @else
                    @php
                        // Check if current user's department can access current process
$canAccess = false;
$currentProcess = null;

if ($masterProses) {
    $currentProcess = $masterProses;
    // Map user division to master proses department
    $divisionMapping = [
        'RnD' => 'QUALITY',
        'Research & Development' => 'QUALITY',
        'Quality Control' => 'QUALITY',
        'MARKETING' => 'MARKETING',
        'Marketing' => 'MARKETING',
        'Marketing & Sales' => 'MARKETING',
        'PPIC' => 'PPIC',
        'Production Planning' => 'PPIC',
        'Produksi' => 'PRODUKSI',
        'Production' => 'PRODUKSI',
        'Prepress' => 'PREPRESS',
                            ];

                            $userDepartment = $divisionMapping[$userDivision] ?? $userDivision;
                            $canAccess = $userDepartment === $masterProses->department_responsible;
                        }
                    @endphp

                    @if ($canAccess && $currentProcess)
                        <!-- Show action based on current process -->
                        @if ($currentProcess->status_proses === 'DRAFT' && in_array($job->status_job, ['DRAFT', 'PLANNING', 'OPEN']))
                            <button type="button" class="btn btn-success btn-lg" style="width: 100%;"
                                id="sendToPrepressBtn">
                                <i class="mdi mdi-send"></i> KIRIM JOB PREPRESS
                            </button>
                        @elseif(
                            $currentProcess->status_proses === 'IN_PROGRESS_PREPRESS' &&
                                in_array($job->status_job, ['OPEN', 'IN_PROGRESS_PREPRESS']))
                            <div class="alert alert-info text-center">
                                <i class="mdi mdi-printer"></i> Job sedang diproses di Prepress
                            </div>
                        @elseif(
                            $currentProcess->status_proses === 'MEETING_OPP' &&
                                $job->status_job === 'MEETING_OPP' &&
                                $job->meetingOpp1 &&
                                $job->meetingOpp1->customer_response === 'reject' &&
                                $job->meetingOpp1->returned_to_prepress)
                            <div class="alert alert-warning text-center">
                                <i class="mdi mdi-printer"></i> Job dikembalikan ke Prepress untuk revisi
                                <br><small class="text-muted">Menunggu Prepress menyelesaikan revisi sebelum Meeting OPP
                                    2</small>
                            </div>
                        @elseif(
                            $currentProcess->status_proses === 'FINISH_PREPRESS' &&
                                in_array($job->status_job, ['OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS']))
                            @if (in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
                                <div class="alert alert-info text-center">
                                    <i class="mdi mdi-calendar-clock"></i> Silakan jadwalkan Meeting OPP di form di bawah
                                </div>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="mdi mdi-calendar-clock"></i> Menunggu Marketing untuk menjadwalkan Meeting
                                    OPP
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info text-center">
                                <i class="mdi mdi-information"></i> Proses saat ini: {{ $currentProcess->notes }}
                            </div>
                        @endif
                    @else
                        <!-- User tidak memiliki akses untuk proses saat ini -->
                        <div class="alert alert-warning text-center">
                            <i class="mdi mdi-account-alert"></i>
                            <strong>Akses Terbatas</strong><br>
                            <small>
                                @if ($currentProcess)
                                    @if ($job->status_job === 'MEETING_OPP' && $job->meetingOpp1 && $job->meetingOpp1->customer_response === 'reject')
                                        Proses saat ini: <strong>Customer Reject Meeting OPP 1 - Marketing harus
                                            mengkonfirmasi kembali ke Prepress</strong><br>
                                        Department yang bertanggung jawab: <strong>MARKETING</strong><br>
                                        Department Anda: <strong>{{ $userDivision }}</strong><br>
                                        <small class="text-info">
                                            <i class="mdi mdi-information"></i>
                                            @if (in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
                                                Anda dapat mengkonfirmasi pengembalian job ke Prepress di form di bawah
                                            @else
                                                Menunggu Marketing untuk mengkonfirmasi pengembalian job ke Prepress
                                            @endif
                                        </small>
                                    @elseif (
                                        $job->meetingOpp1 &&
                                            in_array($job->meetingOpp1->status, ['berjalan', 'selesai']) &&
                                            $job->meetingOpp1->customer_response === 'acc' &&
                                            (!$job->rnd_customer_approval || $job->rnd_customer_approval === 'pending'))
                                        Proses saat ini: <strong>Customer sudah ACC - Menunggu RnD Customer
                                            Approval</strong><br>
                                        Department yang bertanggung jawab: <strong>QUALITY</strong><br>
                                        Department Anda: <strong>{{ $userDivision }}</strong><br>
                                        <small class="text-info">
                                            <i class="mdi mdi-information"></i>
                                            @if (in_array($userDivision, ['QUALITY', 'RnD', 'Research & Development', 'Quality Control']))
                                                Anda dapat melakukan RnD Customer Approval di form di bawah
                                            @elseif (in_array($userDivision, ['PPIC', 'Production Planning']))
                                                <strong class="text-warning">⚠️ PPIC BELUM BISA MENJADWALKAN - MENUNGGU RnD
                                                    APPROVAL</strong><br>
                                                Job belum dapat diproses PPIC karena RnD belum melakukan Customer Approval.
                                                Silakan tunggu RnD menyelesaikan approval terlebih dahulu.
                                            @else
                                                Menunggu RnD untuk melakukan Customer Approval
                                            @endif
                                        </small>
                                    @elseif (
                                        $job->meetingOpp1 &&
                                            in_array($job->meetingOpp1->status, ['berjalan', 'selesai']) &&
                                            $job->meetingOpp1->customer_response === 'pending')
                                        Proses saat ini: <strong>Menunggu Customer Approval dari Meeting OPP</strong><br>
                                        Department yang bertanggung jawab: <strong>MARKETING</strong><br>
                                        Department Anda: <strong>{{ $userDivision }}</strong><br>
                                        <small class="text-info">
                                            <i class="mdi mdi-information"></i>
                                            @if (in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
                                                Anda dapat melakukan Marketing Approval di form Meeting OPP di bawah
                                            @else
                                                Menunggu Marketing untuk melakukan Customer Approval dari Meeting OPP
                                            @endif
                                        </small>
                                    @else
                                        Proses saat ini: <strong>{{ $currentProcess->notes }}</strong><br>
                                        Department yang bertanggung jawab:
                                        <strong>{{ $currentProcess->department_responsible }}</strong><br>
                                        Department Anda: <strong>{{ $userDivision }}</strong>
                                    @endif
                                @else
                                    Tidak ada proses yang aktif untuk status ini
                                @endif
                            </small>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Meeting OPP Section - Hanya tampil untuk Marketing setelah prepress selesai -->
        @if ($job->status_job === 'COMPLETED')
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success text-center">
                        <h6><i class="mdi mdi-flag-checkered"></i> Development Item Sudah Closed</h6>
                        <p class="mb-0">Alur development telah selesai sepenuhnya. Tidak ada aksi yang dapat dilakukan
                            lagi.</p>
                        <p class="mb-0"><strong>Ditutup pada:</strong>
                            {{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif(in_array($job->status_job, ['FINISH_PREPRESS', 'MEETING_OPP']) && in_array($userDivision, ['MARKETING']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-account-group"></i> Meeting OPP</h4>

                        @php
                            // Check if Meeting OPP 1 should be read-only
                            $isMeetingOpp1ReadOnly =
                                $job->meetingOpp1 && $job->meetingOpp1->customer_response === 'reject';
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting OPP 1:</label>
                                    <div class="input-group">
                                        <input type="date"
                                            class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}"
                                            id="meetingOpp1Date" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}
                                            value="{{ $job->meetingOpp1 ? $job->meetingOpp1->meeting_date->format('Y-m-d') : '' }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @if ($isMeetingOpp1ReadOnly)
                                        <small class="text-muted"><i class="mdi mdi-lock"></i> Form Meeting OPP 1 terkunci
                                            karena customer reject</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}"
                                        id="meetingOpp1Status" {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}>
                                        <option value="belum_berjalan"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->status === 'belum_berjalan' ? 'selected' : '' }}>
                                            Belum Berjalan</option>
                                        <option value="berjalan"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->status === 'berjalan' ? 'selected' : '' }}>
                                            Berjalan</option>
                                        <option value="selesai"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->status === 'selesai' ? 'selected' : '' }}>
                                            Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Response:</label>
                                    <select class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}"
                                        id="meetingOpp1CustomerResponse" {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}>
                                        <option value="pending"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->customer_response === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="acc"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->customer_response === 'acc' ? 'selected' : '' }}>
                                            ACC</option>
                                        <option value="reject"
                                            {{ $job->meetingOpp1 && $job->meetingOpp1->customer_response === 'reject' ? 'selected' : '' }}>
                                            REJECT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Notes:</label>
                                    <textarea class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}" id="meetingOpp1CustomerNotes"
                                        rows="2" placeholder="Catatan dari customer" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}>{{ $job->meetingOpp1 ? $job->meetingOpp1->customer_notes : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marketing Notes:</label>
                                    <textarea class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}" id="meetingOpp1MarketingNotes"
                                        rows="2" placeholder="Catatan marketing" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}>{{ $job->meetingOpp1 ? $job->meetingOpp1->marketing_notes : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RnD Notes:</label>
                                    <textarea class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}" id="meetingOpp1RndNotes"
                                        rows="2" placeholder="Catatan RnD" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}>{{ $job->meetingOpp1 ? $job->meetingOpp1->rnd_notes : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- RnD Approval Section -->
                        @if (
                            $job->meetingOpp1 &&
                                in_array($job->meetingOpp1->status, ['berjalan', 'selesai']) &&
                                in_array($userDivision, ['QUALITY', 'RnD', 'Research & Development', 'Quality Control']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert {{ $isMeetingOpp1ReadOnly ? 'alert-secondary' : 'alert-info' }}">
                                        <h6><i class="mdi mdi-account-check"></i> RnD Approval Required</h6>
                                        @if ($isMeetingOpp1ReadOnly)
                                            <div class="alert alert-warning mb-3">
                                                <i class="mdi mdi-lock"></i> <strong>Form Meeting OPP 1 terkunci</strong>
                                                karena customer reject. Silakan gunakan Meeting OPP 2.
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>RnD Approval:</label>
                                                    <select
                                                        class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}"
                                                        id="rndApproval1" {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}>
                                                        <option value="pending"
                                                            {{ $job->meetingOpp1->rnd_approval === 'pending' ? 'selected' : '' }}>
                                                            Pending (Menunggu Persetujuan)</option>
                                                        <option value="approve"
                                                            {{ $job->meetingOpp1->rnd_approval === 'approve' ? 'selected' : '' }}>
                                                            Approve (Setuju)</option>
                                                        <option value="reject"
                                                            {{ $job->meetingOpp1->rnd_approval === 'reject' ? 'selected' : '' }}>
                                                            Reject (Tolak)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>RnD Notes:</label>
                                                    <textarea class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}" id="rndApprovalNotes1" rows="2"
                                                        placeholder="Catatan RnD" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}>{{ $job->meetingOpp1->rnd_approval_notes ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @if (!$isMeetingOpp1ReadOnly)
                                            <div class="text-center">
                                                <button type="button" class="btn btn-info" id="rndApproveBtn1">
                                                    <i class="mdi mdi-check"></i> Submit RnD Approval
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Marketing Approval Section -->
                        @if (
                            $job->meetingOpp1 &&
                                $job->meetingOpp1->rnd_approval === 'approve' &&
                                in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert {{ $isMeetingOpp1ReadOnly ? 'alert-secondary' : 'alert-warning' }}">
                                        <h6><i class="mdi mdi-account-star"></i> Marketing Approval Required</h6>
                                        @if ($isMeetingOpp1ReadOnly)
                                            <div class="alert alert-warning mb-3">
                                                <i class="mdi mdi-lock"></i> <strong>Form Meeting OPP 1 terkunci</strong>
                                                karena customer reject. Silakan gunakan Meeting OPP 2.
                                            </div>
                                        @else
                                            <p class="mb-2"><small><strong>Penjelasan:</strong></small></p>
                                            <ul class="mb-2" style="font-size: 0.9rem;">
                                                <li><strong>Pending:</strong> Marketing sedang memproses untuk kirim ke
                                                    Customer
                                                </li>
                                                <li><strong>Approve:</strong> Customer menyetujui (ACC)</li>
                                                <li><strong>Reject:</strong> Customer menolak (REJECT)</li>
                                            </ul>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Customer Response:</label>
                                                    <select
                                                        class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}"
                                                        id="marketingApproval1"
                                                        {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}>
                                                        <option value="pending"
                                                            {{ $job->meetingOpp1->marketing_approval === 'pending' ? 'selected' : '' }}>
                                                            Pending (Marketing sedang proses)</option>
                                                        <option value="approve"
                                                            {{ $job->meetingOpp1->marketing_approval === 'approve' ? 'selected' : '' }}>
                                                            Approve (Customer ACC)</option>
                                                        <option value="reject"
                                                            {{ $job->meetingOpp1->marketing_approval === 'reject' ? 'selected' : '' }}>
                                                            Reject (Customer REJECT)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Customer Notes:</label>
                                                    <textarea class="form-control {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}" id="marketingApprovalNotes1"
                                                        rows="2" placeholder="Catatan dari Customer" {{ $isMeetingOpp1ReadOnly ? 'readonly' : '' }}>{{ $job->meetingOpp1->marketing_approval_notes ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @if (!$isMeetingOpp1ReadOnly)
                                            <div class="text-center">
                                                <button type="button" class="btn btn-warning" id="marketingApproveBtn1">
                                                    <i class="mdi mdi-check"></i> Update Customer Response
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center">
                            @if (!$job->meetingOpp1)
                                <div class="alert alert-warning mb-3">
                                    <i class="mdi mdi-alert"></i> Meeting OPP 1 belum dibuat. Silakan isi form di atas dan
                                    klik "Simpan Meeting OPP 1" untuk membuat meeting pertama.
                                </div>
                            @endif
                            @if ($isMeetingOpp1ReadOnly)
                                <div class="alert alert-info mb-3">
                                    <i class="mdi mdi-lock"></i> <strong>Form Meeting OPP 1 terkunci</strong> karena
                                    customer reject. Silakan gunakan Meeting OPP 2 di bawah.
                                </div>
                            @endif
                            <button type="button"
                                class="btn btn-primary {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}"
                                id="saveMeetingOpp1Btn" {{ $isMeetingOpp1ReadOnly ? 'disabled' : '' }}>
                                <i class="mdi mdi-content-save"></i> Simpan Meeting OPP 1
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Customer Reject Confirmation Section -->

        @if (
            $job->status_job === 'MEETING_OPP' &&
                $job->meetingOpp1 &&
                $job->meetingOpp1->customer_response === 'reject' &&
                in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-arrow-left-bold-circle"></i> Konfirmasi Kembali ke Prepress</h4>
                        <div class="alert alert-warning mb-3">
                            <h6><i class="mdi mdi-alert"></i> Customer Reject Meeting OPP 1</h6>
                            <p class="mb-2">Customer telah menolak hasil Meeting OPP 1. Job harus dikembalikan ke
                                Prepress untuk diproses ulang.</p>
                            <p class="mb-0"><strong>Catatan Customer:</strong>
                                {{ $job->meetingOpp1->customer_notes ?: 'Tidak ada catatan' }}</p>
                        </div>

                        @if (!$job->meetingOpp1->returned_to_prepress)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Marketing Notes (Alasan Kembali ke Prepress):</label>
                                        <textarea class="form-control" id="returnToPrepressNotes" rows="3"
                                            placeholder="Jelaskan alasan job dikembalikan ke Prepress dan instruksi revisi yang diperlukan">{{ $job->meetingOpp1->return_to_prepress_notes ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Prioritas Revisi:</label>
                                        <select class="form-control" id="revisionPriority">
                                            <option value="urgent"
                                                {{ ($job->meetingOpp1->revision_priority ?? '') === 'urgent' ? 'selected' : '' }}>
                                                Urgent (3 hari)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-danger btn-lg" id="confirmReturnToPrepressBtn">
                                    <i class="mdi mdi-arrow-left-bold"></i> KONFIRMASI KEMBALI KE PREPRESS
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h6><i class="mdi mdi-check-circle"></i> Job Sudah Dikembalikan ke Prepress</h6>
                                <p class="mb-2"><strong>Dikembalikan pada:</strong>
                                    {{ $job->meetingOpp1->returned_to_prepress_at ? \Carbon\Carbon::parse($job->meetingOpp1->returned_to_prepress_at)->format('d/m/Y H:i') : '-' }}
                                </p>
                                <p class="mb-2"><strong>Marketing Notes:</strong>
                                    {{ $job->meetingOpp1->return_to_prepress_notes ?: '-' }}</p>
                                <p class="mb-0"><strong>Prioritas Revisi:</strong>
                                    {{ ucfirst($job->meetingOpp1->revision_priority ?? '-') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Meeting OPP 2 Section (setelah prepress selesai revisi) -->
        @if (
            $job->status_job === 'FINISH_PREPRESS' &&
                $job->meetingOpp1 &&
                $job->meetingOpp1->customer_response === 'reject' &&
                // $job->meetingOpp1->returned_to_prepress &&
                in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-account-group"></i> Meeting OPP 2 (Setelah Revisi Prepress)</h4>

                        @if (
                            $job->status_job === 'FINISH_PREPRESS' &&
                                $job->meetingOpp1 &&
                                $job->meetingOpp1->customer_response === 'reject' &&
                                $job->meetingOpp1->returned_to_prepress)
                            <div class="alert alert-success mb-3">
                                <h6><i class="mdi mdi-check-circle"></i> Prepress Selesai Revisi</h6>
                                <p class="mb-0">Prepress telah menyelesaikan revisi job. Sekarang Anda dapat menjadwalkan
                                    Meeting OPP 2.</p>
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <h6><i class="mdi mdi-clock"></i> Menunggu Prepress Selesai Revisi</h6>
                                <p class="mb-0">Job sedang diproses ulang di Prepress. Meeting OPP 2 dapat dijadwalkan
                                    setelah status berubah menjadi "FINISH_PREPRESS".</p>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meeting OPP 2:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="meetingOpp2Date"
                                            value="{{ $job->meetingOpp2 ? $job->meetingOpp2->meeting_date->format('Y-m-d') : '' }}"
                                            {{ $job->status_job !== 'FINISH_PREPRESS' ? 'disabled' : '' }}>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select class="form-control" id="meetingOpp2Status"
                                        {{ $job->status_job !== 'FINISH_PREPRESS' ? 'disabled' : '' }}>
                                        <option value="belum_berjalan"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->status === 'belum_berjalan' ? 'selected' : '' }}>
                                            Belum Berjalan</option>
                                        <option value="berjalan"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->status === 'berjalan' ? 'selected' : '' }}>
                                            Berjalan</option>
                                        <option value="selesai"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->status === 'selesai' ? 'selected' : '' }}>
                                            Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Response:</label>
                                    <select class="form-control" id="meetingOpp2CustomerResponse"
                                        {{ $job->status_job !== 'FINISH_PREPRESS' ? 'disabled' : '' }}>
                                        <option value="pending"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->customer_response === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="acc"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->customer_response === 'acc' ? 'selected' : '' }}>
                                            ACC</option>
                                        <option value="reject"
                                            {{ $job->meetingOpp2 && $job->meetingOpp2->customer_response === 'reject' ? 'selected' : '' }}>
                                            REJECT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Notes:</label>
                                    <textarea class="form-control" id="meetingOpp2CustomerNotes" rows="2" placeholder="Catatan dari customer"
                                        {{ $job->status_job !== 'FINISH_PREPRESS' ? 'readonly' : '' }}>{{ $job->meetingOpp2 ? $job->meetingOpp2->customer_notes : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marketing Notes:</label>
                                    <textarea class="form-control" id="meetingOpp2MarketingNotes" rows="2" placeholder="Catatan marketing"
                                        {{ $job->status_job !== 'FINISH_PREPRESS' ? 'readonly' : '' }}>{{ $job->meetingOpp2 ? $job->meetingOpp2->marketing_notes : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RnD Notes:</label>
                                    <textarea class="form-control" id="meetingOpp2RndNotes" rows="2" placeholder="Catatan RnD"
                                        {{ $job->status_job !== 'FINISH_PREPRESS' ? 'readonly' : '' }}>{{ $job->meetingOpp2 ? $job->meetingOpp2->rnd_notes : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            @if ($job->status_job === 'FINISH_PREPRESS')
                                <button type="button" class="btn btn-warning" id="saveMeetingOpp2Btn">
                                    <i class="mdi mdi-content-save"></i> Simpan Meeting OPP 2
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="mdi mdi-clock"></i> Tunggu Prepress Selesai
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RnD Customer Approval Status - Menampilkan status approval yang sudah selesai -->
        @if (
            $job->meetingOpp1 &&
                $job->meetingOpp1->marketing_approval === 'approve' &&
                $job->rnd_customer_approval &&
                $job->rnd_customer_approval !== 'pending')
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-account-check"></i> RnD Customer Approval Status</h4>
                        <div
                            class="alert {{ $job->rnd_customer_approval === 'approve' ? 'alert-success' : 'alert-danger' }} mb-3">
                            <h6><i
                                    class="mdi mdi-{{ $job->rnd_customer_approval === 'approve' ? 'check-circle' : 'close-circle' }}"></i>
                                RnD Customer Approval: {{ strtoupper($job->rnd_customer_approval) }}
                            </h6>
                            <p class="mb-1"><strong>Approved by:</strong> {{ $job->rnd_customer_approved_by ?? '-' }}
                            </p>
                            <p class="mb-1"><strong>Approved at:</strong>
                                {{ $job->rnd_customer_approved_at ? \Carbon\Carbon::parse($job->rnd_customer_approved_at)->format('d/m/Y H:i') : '-' }}
                            </p>
                            @if ($job->rnd_customer_notes)
                                <p class="mb-0"><strong>Notes:</strong> {{ $job->rnd_customer_notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Scheduling Development Section - Hanya tampil untuk PPIC setelah RnD Customer Approval -->
        @if ($job->rnd_customer_approval && in_array($userDivision, ['PPIC', 'Production Planning']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h6><i class="mdi mdi-calendar-clock"></i> Scheduling Development (PPIC)</h6>
                        {{-- <div class="alert alert-info mb-3">
                            <small>
                                <strong>Penjelasan:</strong> PPIC akan menjadwalkan development berdasarkan lead time
                                material khusus.<br>
                                <strong>Rumus:</strong> 14 hari (development) + MAX(lead time material)<br>
                                <strong>Contoh:</strong> Jika butuh kertas khusus (30 hari) + foil khusus (14 hari) = 14 +
                                MAX(30, 14) = 44 hari<br>
                                <strong>Alasan:</strong> Material bisa dipersiapkan bersamaan, jadi ambil yang terpanjang.
                            </small>
                        </div> --}}

                        @if ($job->schedulingDevelopment)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Default Days:</label>
                                        <input type="number" class="form-control" value="14" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kertas Khusus (Lead Time):</label>
                                        <input type="number" class="form-control"
                                            value="{{ $job->kertas_khusus ? 30 : 0 }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Foil Khusus (Lead Time):</label>
                                        <input type="number" class="form-control"
                                            value="{{ $job->foil_khusus ? 14 : 0 }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Estimated Days:</label>
                                        <input type="number" class="form-control"
                                            value="{{ $job->schedulingDevelopment->total_estimated_days }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>PPIC Notes:</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $job->schedulingDevelopment->ppic_notes ?: '-' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Purchasing Notes:</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $job->schedulingDevelopment->purchasing_notes ?: '-' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @elseif($job->status_job === 'SCHEDULED_FOR_PRODUCTION')
                            <div class="alert alert-success">
                                <h6><i class="mdi mdi-check-circle"></i> Scheduling Development Sudah Selesai</h6>
                                <p class="mb-0">Job sudah dijadwalkan untuk produksi. Scheduling development telah
                                    selesai diproses.</p>
                            </div>
                        @elseif($job->status_job === 'IN_PRODUCTION')
                            <div class="alert alert-success">
                                <h6><i class="mdi mdi-check-circle"></i> Scheduling Development Sudah Selesai</h6>
                                <p class="mb-0">Job sudah dijadwalkan untuk produksi. Scheduling development telah
                                    selesai diproses.</p>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h6><i class="mdi mdi-alert"></i> Scheduling Development Belum Dibuat</h6>
                                <p class="mb-0">Scheduling Development belum dibuat. Silakan akses PPIC Form untuk
                                    membuat jadwal development.</p>
                            </div>
                        @endif

                        <div class="text-center">
                            <a href="{{ route('development.ppic-form', $job->id) }}" class="btn btn-success btn-lg">
                                <i class="mdi mdi-calendar-plus"></i> PPIC Form - Material & Scheduling
                            </a>
                            @if ($job->productionSchedules && $job->productionSchedules->count() > 0)
                                @if (in_array($userDivision, ['PRODUKSI', 'Production', 'Produksi']))
                                    <a href="{{ route('development.production-report', $job->id) }}"
                                        class="btn btn-info btn-lg ml-2">
                                        <i class="mdi mdi-clipboard-check"></i> Production Report
                                    </a>
                                @endif
                                @if (
                                    $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() > 0 &&
                                        in_array($userDivision, ['QUALITY', 'RnD', 'Research & Development', 'Quality Control']))
                                    <a href="{{ route('development.rnd-production-approval', $job->id) }}"
                                        class="btn btn-warning btn-lg ml-2">
                                        <i class="mdi mdi-account-check"></i> RnD Approval
                                        <span
                                            class="badge badge-light ml-1">{{ $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() }}</span>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Production Report Section - Hanya tampil untuk PRODUKSI -->
        @if (
            $job->productionSchedules &&
                $job->productionSchedules->count() > 0 &&
                in_array($userDivision, ['PRODUKSI', 'Production', 'Produksi']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h6><i class="mdi mdi-clipboard-check"></i> Production Report (PRODUKSI)</h6>
                        <div class="alert alert-info mb-3">
                            <small>
                                <strong>Penjelasan:</strong> Divisi Produksi dapat mengisi laporan hasil produksi dan
                                melacak progress produksi.<br>
                                <strong>Status:</strong> Job sudah dijadwalkan untuk produksi dan siap untuk diproses.
                            </small>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('development.production-report', $job->id) }}"
                                class="btn btn-info btn-lg">
                                <i class="mdi mdi-clipboard-check"></i> Production Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RnD Approval Section - Hanya tampil untuk QUALITY/RnD -->
        @if (
            $job->productionSchedules &&
                $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() > 0 &&
                in_array($userDivision, ['QUALITY', 'RnD', 'Research & Development', 'Quality Control']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h6><i class="mdi mdi-account-check"></i> RnD Approval (QUALITY)</h6>
                        <div class="alert alert-warning mb-3">
                            <small>
                                <strong>Penjelasan:</strong> Divisi Quality/RnD dapat melakukan approval terhadap laporan
                                hasil produksi yang telah diselesaikan.<br>
                                <strong>Status:</strong> Ada
                                {{ $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() }}
                                production report yang menunggu approval.<br>
                                <strong>Catatan:</strong> Job status akan berubah menjadi "PRODUCTION_APPROVED_BY_RND" hanya
                                setelah <strong>SEMUA</strong> proses produksi selesai dan di-approve.
                            </small>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('development.rnd-production-approval', $job->id) }}"
                                class="btn btn-warning btn-lg">
                                <i class="mdi mdi-account-check"></i> RnD Approval
                                <span
                                    class="badge badge-light ml-2">{{ $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Map Proof Section - Hanya tampil untuk Marketing setelah RnD approve production -->
        @if (
            $job->status_job === 'PRODUCTION_APPROVED_BY_RND' || $job->status_job === 'WAITING_MPP' &&
                in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']) &&
                $job->status_job !== 'COMPLETED')
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-file-document"></i> Map Proof</h4>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Proof File:</label>
                                    <input type="file" class="form-control" id="proofFile"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($job->mapProof && $job->mapProof->proof_file_path)
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <small class="text-success">
                                                    <i class="mdi mdi-file-check"></i> File sudah diupload:
                                                    <a href="/sipo_krisan/storage/app/private/public/{{ $job->mapProof->proof_file_path }}"
                                                        target="_blank" class="text-primary">
                                                        {{ basename($job->mapProof->proof_file_path) }}
                                                    </a>
                                                </small>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    id="deleteProofFileBtn" title="Hapus file">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Notes:</label>
                                    <textarea class="form-control" id="mapProofCustomerNotes" rows="2" placeholder="Catatan dari customer">{{ $job->mapProof ? $job->mapProof->customer_notes : '' }}</textarea>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marketing Notes:</label>
                                    <textarea class="form-control" id="mapProofMarketingNotes" rows="4" placeholder="Catatan marketing">{{ $job->mapProof ? $job->mapProof->marketing_notes : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Update Progress:</label>
                                    <div>
                                        @if ($job->mapProof && $job->mapProof->customer_response === 'acc')
                                            <div class="alert alert-success mb-0">
                                                <i class="mdi mdi-check-circle"></i> Customer sudah menyetujui Map Proof
                                            </div>
                                        @elseif($job->mapProof && $job->mapProof->customer_response === 'reject')
                                            <div class="alert alert-danger mb-0">
                                                <i class="mdi mdi-close-circle"></i> Customer menolak Map Proof
                                            </div>
                                        @else
                                            <select class="form-control mb-2" id="mapProofProgress">
                                                <option value="">Pilih Progress...</option>
                                                <option value="proses_kirim"
                                                    {{ $job->mapProof && $job->mapProof->status === 'proses_kirim' ? 'selected' : '' }}>
                                                    PROSES KIRIM CUSTOMER</option>
                                                <option value="accept"
                                                    {{ $job->mapProof && $job->mapProof->status === 'accept' ? 'selected' : '' }}>
                                                    ACCEPT</option>
                                                <option value="reject"
                                                    {{ $job->mapProof && $job->mapProof->status === 'reject' ? 'selected' : '' }}>
                                                    REJECT</option>
                                            </select>
                                            <button type="button" class="btn btn-primary"
                                                id="updateMapProofProgressBtn">
                                                <i class="mdi mdi-update"></i> Update Progress
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Sales Order Section - Hanya tampil untuk Marketing -->
        @if ($job->status_job === 'MPP_APPROVED' && in_array($userDivision, ['MARKETING', 'Marketing & Sales']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-cart"></i> Sales Order</h4>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Order Number:</label>
                                    <input type="text" class="form-control" id="orderNumber"
                                        value="{{ $job->salesOrder ? $job->salesOrder->order_number : '' }}"
                                        placeholder="SO-YYYYMMDD-XXX">
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>Order Date:</label>
                                    <input type="date" class="form-control" id="orderDate"
                                        value="{{ $job->salesOrder ? $job->salesOrder->order_date->format('Y-m-d') : date('Y-m-d') }}">
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quantity:</label>
                                    <input type="number" class="form-control" id="orderQuantity"
                                        value="{{ $job->salesOrder ? $job->salesOrder->quantity : $job->qty_order_estimation }}">
                                </div>
                            </div> --}}
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unit Price:</label>
                                    <input type="number" class="form-control" id="unitPrice"
                                        value="{{ $job->salesOrder ? $job->salesOrder->unit_price : '' }}"
                                        step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Price:</label>
                                    <input type="number" class="form-control" id="totalPrice"
                                        value="{{ $job->salesOrder ? $job->salesOrder->total_price : '' }}"
                                        step="0.01" readonly>
                                </div>
                            </div>
                        </div> --}}
                        <div class="text-center">
                            @if ($job->status_job === 'COMPLETED')
                                <div class="alert alert-success mb-3">
                                    <i class="mdi mdi-check-circle"></i> Sales Order sudah dibuat:
                                    <strong>{{ $job->salesOrder->order_number }}</strong>
                                </div>
                                <div class="alert alert-info">
                                    <h4><i class="mdi mdi-flag-checkered"></i> Development Sudah Closed</h4>
                                    <p class="mb-0">Alur development telah selesai sepenuhnya. Development item tidak
                                        dapat diubah lagi.</p>
                                    <p class="mb-0"><strong>Ditutup pada:</strong>
                                        {{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>
                            @elseif(!$job->salesOrder)
                                <button type="button" class="btn btn-success" id="createSalesOrderBtn">
                                    <i class="mdi mdi-cart-plus"></i> Simpan Informasi Sales Order
                                </button>
                            @else
                                <div class="alert alert-success mb-3">
                                    <i class="mdi mdi-check-circle"></i> Sales Order sudah dibuat:
                                    <strong>{{ $job->salesOrder->order_number }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Close Development Section - Hanya tampil untuk Marketing setelah Sales Order dibuat -->
        @if (
            $job->status_job === 'SALES_ORDER_CREATED' &&
                in_array($userDivision, ['MARKETING', 'Marketing', 'Marketing & Sales']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-flag-checkered"></i> Close Development</h4>
                        <div class="alert alert-warning mb-3">
                            <small>
                                <strong>Penjelasan:</strong> Setelah Sales Order dibuat, development item dapat ditutup
                                untuk menyelesaikan alur development.<br>
                                <strong>Status:</strong> Sales Order sudah dibuat:
                                <strong>{{ $job->salesOrder->order_number ?? 'N/A' }}</strong>
                            </small>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-danger btn-lg" id="closeDevelopmentBtn">
                                <i class="mdi mdi-close-circle"></i> CLOSE DEVELOPMENT ITEM
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RnD Customer Approval Section - Setelah Customer ACC -->
        @if (
            $job->meetingOpp1 &&
                in_array($job->meetingOpp1->status, ['berjalan', 'selesai']) &&
                $job->meetingOpp1->customer_response === 'acc' &&
                (!$job->rnd_customer_approval || $job->rnd_customer_approval === 'pending') &&
                in_array($userDivision, ['QUALITY', 'RnD', 'Research & Development', 'Quality Control']))
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h4><i class="mdi mdi-account-check"></i> RnD Customer Approval</h4>
                        <div class="alert alert-info mb-3">
                            <h6><i class="mdi mdi-information"></i> Customer sudah ACC - RnD Approval Required</h6>
                            <p class="mb-0">Customer telah menyetujui hasil Meeting OPP. Sekarang RnD perlu melakukan
                                approval sebelum job dapat diproses PPIC untuk dijadwalkan.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RnD Customer Approval:</label>
                                    <select class="form-control" id="rndCustomerApproval">
                                        <option value="pending"
                                            {{ $job->rnd_customer_approval === 'pending' ? 'selected' : '' }}>
                                            Pending (Menunggu Persetujuan)
                                        </option>
                                        <option value="approve"
                                            {{ $job->rnd_customer_approval === 'approve' ? 'selected' : '' }}>
                                            Approve (Setuju)
                                        </option>
                                        <option value="reject"
                                            {{ $job->rnd_customer_approval === 'reject' ? 'selected' : '' }}>
                                            Reject (Tolak)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RnD Notes:</label>
                                    <textarea class="form-control" id="rndCustomerNotes" rows="3"
                                        placeholder="Catatan RnD untuk customer approval">{{ $job->rnd_customer_notes ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-info btn-lg" id="submitRndCustomerApproval">
                                <i class="mdi mdi-check"></i> Submit RnD Customer Approval
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bootstrap Modal untuk Konfirmasi Kirim ke Prepress -->
        <div class="modal fade" id="prepressConfirmationModal" tabindex="-1" role="dialog"
            aria-labelledby="prepressConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white" id="prepressConfirmationModalLabel">
                            <i class="mdi mdi-printer"></i> Konfirmasi Kirim ke Prepress
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h4><i class="mdi mdi-information"></i> Detail Job</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Job Code:</strong> {{ $job->job_code }}</p>
                                            <p><strong>Job Name:</strong> {{ $job->job_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Customer:</strong> {{ $job->customer }}</p>
                                            <p><strong>Product:</strong> {{ $job->product }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <h6><i class="mdi mdi-alert"></i> Yang akan terjadi:</h6>
                                    <ul class="mb-0">
                                        <li>Status job akan berubah dari <strong>"{{ $job->status_job }}"</strong> menjadi
                                            <strong>"IN PROGRESS PREPRESS"</strong>
                                        </li>
                                        <li>Job baru akan dibuat di tabel job prepress</li>
                                        <li>Deadline prepress = 3 hari setelah tanggal development</li>
                                        <li>Job tidak dapat diubah lagi setelah dikirim</li>
                                    </ul>
                                </div>

                                @if ($job->job_order && is_array($job->job_order) && count($job->job_order) > 0)
                                    <div class="alert alert-success">
                                        <h6><i class="mdi mdi-clipboard-list"></i> Job Order Details:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Jenis Pekerjaan</th>
                                                        <th>Unit Job</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($job->job_order as $index => $order)
                                                        <tr>
                                                            <td>{{ $index }}</td>
                                                            <td>{{ $order['jenis_pekerjaan'] ?? $order }}</td>
                                                            <td>{{ $order['unit_job'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted">Akan dibuat {{ count($job->job_order) }} prepress job
                                            terpisah</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success" id="confirmSendToPrepress">
                            <i class="mdi mdi-send"></i> Ya, Kirim ke Prepress
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detailed Timeline -->
        <div class="modal fade" id="detailedTimelineModal" tabindex="-1" role="dialog"
            aria-labelledby="detailedTimelineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white" id="detailedTimelineModalLabel">
                            <i class="mdi mdi-history"></i> Detailed Timeline
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="timeline-detailed" id="detailedTimeline">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Loading timeline...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        {{-- moment js  --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {
                // Event handler untuk tombol send to prepress
                $('#sendToPrepressBtn').click(function() {
                    // Tampilkan konfirmasi dengan Bootstrap Modal
                    showPrepressConfirmationModal();
                });

                // Event handler untuk tombol konfirmasi di modal
                $('#confirmSendToPrepress').click(function() {
                    // Tutup modal dan kirim ke prepress
                    $('#prepressConfirmationModal').modal('hide');
                    sendJobToPrepress();
                });

                // Function untuk menampilkan modal konfirmasi
                function showPrepressConfirmationModal() {
                    $('#prepressConfirmationModal').modal('show');
                }

                // Function untuk send job ke prepress
                function sendJobToPrepress() {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengirim job ke prepress',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.rnd-workspace.send-to-prepress', $job->id) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Response:', response);

                            if (response.success) {
                                // Tampilkan notifikasi sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Job berhasil dikirim ke prepress',
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    // Refresh halaman untuk update status
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Terjadi kesalahan saat mengirim job'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);

                            var message = 'Terjadi kesalahan saat mengirim job ke prepress';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                message = Object.values(errors).flat().join('<br>');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: message
                            });
                        }
                    });
                }

                // Meeting OPP 1 Functions
                $('#saveMeetingOpp1Btn').click(function() {
                    // Check if form is read-only
                    if ($(this).hasClass('disabled') || $(this).prop('disabled')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Form Terkunci',
                            text: 'Form Meeting OPP 1 terkunci karena customer reject. Silakan gunakan Meeting OPP 2.'
                        });
                        return;
                    }
                    saveMeetingOPP(1);
                });

                // Meeting OPP 2 Functions
                $('#saveMeetingOpp2Btn').click(function() {
                    saveMeetingOPP(2);
                });

                // RnD Approval Functions
                $('#rndApproveBtn1').click(function() {
                    // Check if form is read-only
                    if ($('#rndApproval1').prop('disabled')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Form Terkunci',
                            text: 'Form Meeting OPP 1 terkunci karena customer reject. Silakan gunakan Meeting OPP 2.'
                        });
                        return;
                    }
                    rndApproveMeetingOPP(1);
                });

                $('#rndApproveBtn2').click(function() {
                    rndApproveMeetingOPP(2);
                });

                // Marketing Approval Functions
                $('#marketingApproveBtn1').click(function() {
                    // Check if form is read-only
                    if ($('#marketingApproval1').prop('disabled')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Form Terkunci',
                            text: 'Form Meeting OPP 1 terkunci karena customer reject. Silakan gunakan Meeting OPP 2.'
                        });
                        return;
                    }
                    marketingApproveMeetingOPP(1);
                });

                $('#marketingApproveBtn2').click(function() {
                    marketingApproveMeetingOPP(2);
                });

                // Scheduling Development Functions
                $('#saveSchedulingBtn').click(function() {
                    saveScheduling();
                });

                // Map Proof Functions
                $('#updateMapProofProgressBtn').click(function() {
                    updateMapProofProgress();
                });

                $('#deleteProofFileBtn').click(function() {
                    deleteProofFile();
                });

                // Sales Order Functions
                $('#createSalesOrderBtn').click(function() {
                    createSalesOrder();
                });

                // Close Development Functions
                $('#closeDevelopmentBtn').click(function() {
                    closeDevelopment();
                });

                // Return to Prepress Functions
                $('#confirmReturnToPrepressBtn').click(function() {
                    confirmReturnToPrepress();
                });

                // Auto calculate total price
                $('#unitPrice, #orderQuantity').on('input', function() {
                    calculateTotalPrice();
                });

                // Check meeting status when date changes
                $('#meetingOpp1Date, #meetingOpp2Date').on('change', function() {
                    checkMeetingStatus();
                });

                // Load detailed timeline when modal is shown
                $('#detailedTimelineModal').on('show.bs.modal', function() {
                    loadDetailedTimeline();
                });

                // Auto-update meeting status based on date
                checkMeetingStatus();

                // Function untuk save Meeting OPP
                function saveMeetingOPP(meetingNumber) {
                    var formData = {
                        _token: '{{ csrf_token() }}',
                        meeting_number: meetingNumber,
                        meeting_date: $('#meetingOpp' + meetingNumber + 'Date').val(),
                        status: $('#meetingOpp' + meetingNumber + 'Status').val(),
                        customer_response: $('#meetingOpp' + meetingNumber + 'CustomerResponse').val(),
                        customer_notes: $('#meetingOpp' + meetingNumber + 'CustomerNotes').val(),
                        marketing_notes: $('#meetingOpp' + meetingNumber + 'MarketingNotes').val(),
                        rnd_notes: $('#meetingOpp' + meetingNumber + 'RndNotes').val()
                    };

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Sedang menyimpan Meeting OPP ' + meetingNumber,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.meeting-opp.store', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Meeting OPP ' + meetingNumber + ' berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan Meeting OPP'
                            });
                        }
                    });
                }

                // Function untuk save Scheduling
                function saveScheduling() {
                    var formData = {
                        _token: '{{ csrf_token() }}',
                        default_days: $('#defaultDays').val(),
                        kertas_khusus_days: $('#kertasKhususDays').val(),
                        foil_khusus_days: $('#foilKhususDays').val(),
                        total_estimated_days: $('#totalEstimatedDays').val(),
                        ppic_notes: $('#ppicNotes').val(),
                        purchasing_notes: $('#purchasingNotes').val()
                    };

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Sedang menyimpan Scheduling Development',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.scheduling.store', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Scheduling Development berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan Scheduling'
                            });
                        }
                    });
                }

                // Function untuk update Map Proof Progress
                function updateMapProofProgress() {
                    var progress = $('#mapProofProgress').val();

                    if (!progress) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Pilih progress terlebih dahulu'
                        });
                        return;
                    }

                    var formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('action', 'update_progress');
                    formData.append('progress', progress);
                    formData.append('customer_notes', $('#mapProofCustomerNotes').val());
                    formData.append('marketing_notes', $('#mapProofMarketingNotes').val());
                    formData.append('mapProofProgress', progress);


                    // Jika ada file baru yang diupload
                    // if ($('#proofFile')[0].files[0]) {
                    //     formData.append('proof_file', $('#proofFile')[0].files[0]);
                    // }

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate progress Map Proof',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.map-proof.store', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Progress Map Proof berhasil diupdate',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat mengupdate progress Map Proof'
                            });
                        }
                    });
                }

                // Function untuk delete proof file
                function deleteProofFile() {
                    Swal.fire({
                        title: 'Konfirmasi Hapus File',
                        text: 'Apakah Anda yakin ingin menghapus file proof yang sudah diupload?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Sedang menghapus file proof',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '{{ route('development.map-proof.delete-file', $job->id) }}',
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'File proof berhasil dihapus',
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat menghapus file proof'
                                    });
                                }
                            });
                        }
                    });
                }


                // Function untuk create Sales Order
                function createSalesOrder() {
                    var formData = {
                        _token: '{{ csrf_token() }}',
                        order_number: $('#orderNumber').val(),
                        order_date: $('#orderDate').val(),
                        quantity: $('#orderQuantity').val(),
                        unit_price: $('#unitPrice').val(),
                        total_price: $('#totalPrice').val()
                    };

                    Swal.fire({
                        title: 'Membuat...',
                        text: 'Sedang membuat Sales Order',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.sales-order.store', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Sales Order berhasil dibuat',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat membuat Sales Order'
                            });
                        }
                    });
                }

                // Function untuk calculate total price
                function calculateTotalPrice() {
                    var unitPrice = parseFloat($('#unitPrice').val()) || 0;
                    var quantity = parseInt($('#orderQuantity').val()) || 0;
                    var totalPrice = unitPrice * quantity;
                    $('#totalPrice').val(totalPrice.toFixed(2));
                }

                // Function untuk close development
                function closeDevelopment() {
                    Swal.fire({
                        title: 'Konfirmasi Close Development',
                        html: `
                            <div class="text-left">
                                <p><strong>Job Code:</strong> {{ $job->job_code }}</p>
                                <p><strong>Job Name:</strong> {{ $job->job_name }}</p>
                                <p><strong>Customer:</strong> {{ $job->customer }}</p>
                                <hr>
                                <p class="text-warning"><i class="mdi mdi-alert"></i> <strong>Yang akan terjadi:</strong></p>
                                <ul class="text-left">
                                    <li>Status job akan berubah menjadi "COMPLETED"</li>
                                    <li>Development item akan ditutup dan tidak bisa diubah lagi</li>
                                    <li>Alur development telah selesai sepenuhnya</li>
                                    <li>Job akan dipindahkan ke status final</li>
                                </ul>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="mdi mdi-close-circle"></i> Ya, Close Development',
                        cancelButtonText: 'Batal',
                        width: '500px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang menutup development item',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '{{ route('development.close-development', $job->id) }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Development item berhasil ditutup',
                                        showConfirmButton: false,
                                        timer: 3000
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat menutup development item'
                                    });
                                }
                            });
                        }
                    });
                }

                // Function untuk konfirmasi kembali ke prepress
                function confirmReturnToPrepress() {
                    var returnNotes = $('#returnToPrepressNotes').val();
                    var revisionPriority = $('#revisionPriority').val();

                    if (!returnNotes.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Silakan isi alasan job dikembalikan ke Prepress'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Kembali ke Prepress',
                        html: `
                            <div class="text-left">
                                <p><strong>Job Code:</strong> {{ $job->job_code }}</p>
                                <p><strong>Job Name:</strong> {{ $job->job_name }}</p>
                                <p><strong>Customer:</strong> {{ $job->customer }}</p>
                                <hr>
                                <p class="text-warning"><i class="mdi mdi-alert"></i> <strong>Yang akan terjadi:</strong></p>
                                <ul class="text-left">
                                    <li>Job akan dikembalikan ke Prepress untuk diproses ulang</li>
                                    <li>Status job akan berubah menjadi "IN_PROGRESS_PREPRESS"</li>
                                    <li>Prepress akan menerima notifikasi untuk revisi</li>
                                    <li>Meeting OPP 2 dapat dijadwalkan setelah Prepress selesai</li>
                                </ul>
                                <hr>
                                <p><strong>Alasan Kembali:</strong> ${returnNotes}</p>
                                <p><strong>Prioritas Revisi:</strong> ${revisionPriority}</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="mdi mdi-arrow-left-bold"></i> Ya, Kembalikan ke Prepress',
                        cancelButtonText: 'Batal',
                        width: '600px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang mengembalikan job ke Prepress',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '{{ route('development.return-to-prepress', $job->id) }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    return_to_prepress_notes: returnNotes,
                                    revision_priority: revisionPriority
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Job berhasil dikembalikan ke Prepress',
                                        showConfirmButton: false,
                                        timer: 3000
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat mengembalikan job ke Prepress'
                                    });
                                }
                            });
                        }
                    });
                }


                // Function untuk load detailed timeline
                function loadDetailedTimeline() {
                    $.ajax({
                        url: '{{ route('development.timeline', $job->id) }}',
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                displayDetailedTimeline(response.data);
                            } else {
                                $('#detailedTimeline').html(
                                    '<div class="alert alert-warning">Tidak ada data timeline</div>');
                            }
                        },
                        error: function(xhr) {
                            $('#detailedTimeline').html(
                                '<div class="alert alert-danger">Error loading timeline</div>');
                        }
                    });
                }

                // Function untuk display detailed timeline - Compact 1 Baris
                function displayDetailedTimeline(timelineData) {
                    if (timelineData.length === 0) {
                        $('#detailedTimeline').html(
                            '<div class="alert alert-info">Belum ada aktivitas yang tercatat</div>');
                        return;
                    }

                    var html = '<div class="compact-timeline">';

                    timelineData.forEach(function(item) {
                        var actionIcon = getActionIcon(item.action_type);
                        var actionColor = getActionColor(item.action_type);
                        var timeAgo = moment(item.action_time).fromNow();
                        var timeFormatted = moment(item.action_time).format('DD/MM/YYYY HH:mm');

                        // Compact single line format
                        html += '<div class="compact-timeline-item">';
                        html += '<div class="compact-timeline-dot ' + actionColor + '">';
                        html += '<i class="mdi ' + actionIcon + '"></i>';
                        html += '</div>';
                        html += '<div class="compact-timeline-content">';
                        html += '<span class="compact-timeline-text">' + item.action_description + '</span>';
                        html += '<span class="compact-timeline-user">oleh <strong>' + (item.performed_by_name ||
                            'Unknown') + '</strong></span>';
                        if (item.status_before && item.status_after) {
                            html +=
                                '<span class="compact-timeline-status">Status: <span class="badge badge-sm badge-secondary">' +
                                item.status_before + '</span> → <span class="badge badge-sm badge-primary">' +
                                item.status_after + '</span></span>';
                        }
                        html += '<span class="compact-timeline-time" title="' + timeFormatted + '">' + timeAgo +
                            '</span>';
                        html += '</div>';
                        html += '</div>';
                    });

                    html += '</div>';
                    $('#detailedTimeline').html(html);
                }

                // Function untuk get action icon
                function getActionIcon(actionType) {
                    var icons = {
                        'created': 'mdi-plus-circle',
                        'sent_to_prepress': 'mdi-send',
                        'meeting_opp_1': 'mdi-account-group',
                        'meeting_opp_2': 'mdi-account-group',
                        'scheduling': 'mdi-calendar-clock',
                        'map_proof_upload': 'mdi-upload',
                        'map_proof_sent': 'mdi-send',
                        'sales_order_created': 'mdi-cart-plus',
                        'status_changed': 'mdi-update'
                    };
                    return icons[actionType] || 'mdi-circle';
                }

                // Function untuk get action color
                function getActionColor(actionType) {
                    var colors = {
                        'created': 'bg-primary',
                        'sent_to_prepress': 'bg-success',
                        'meeting_opp_1': 'bg-info',
                        'meeting_opp_2': 'bg-warning',
                        'scheduling': 'bg-secondary',
                        'map_proof_upload': 'bg-dark',
                        'map_proof_sent': 'bg-success',
                        'sales_order_created': 'bg-primary',
                        'status_changed': 'bg-light'
                    };
                    return colors[actionType] || 'bg-secondary';
                }

                // Function untuk check meeting status berdasarkan tanggal
                function checkMeetingStatus() {
                    var today = moment().format('YYYY-MM-DD');

                    // Check Meeting OPP 1
                    var meeting1Date = $('#meetingOpp1Date').val();
                    var meeting1Status = $('#meetingOpp1Status').val();

                    if (meeting1Date && meeting1Date === today && meeting1Status === 'belum_berjalan') {
                        $('#meetingOpp1Status').val('berjalan');

                        // Auto-save ke database
                        autoSaveMeetingStatus(1, 'berjalan');

                        // Tampilkan notifikasi
                        Swal.fire({
                            icon: 'info',
                            title: 'Meeting OPP 1',
                            text: 'Status otomatis berubah menjadi "Berjalan" karena tanggal meeting adalah hari ini',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }

                    // Check Meeting OPP 2
                    var meeting2Date = $('#meetingOpp2Date').val();
                    var meeting2Status = $('#meetingOpp2Status').val();

                    if (meeting2Date && meeting2Date === today && meeting2Status === 'belum_berjalan') {
                        $('#meetingOpp2Status').val('berjalan');

                        // Auto-save ke database
                        autoSaveMeetingStatus(2, 'berjalan');

                        // Tampilkan notifikasi
                        Swal.fire({
                            icon: 'info',
                            title: 'Meeting OPP 2',
                            text: 'Status otomatis berubah menjadi "Berjalan" karena tanggal meeting adalah hari ini',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }

                // Function untuk auto-save meeting status ke database
                function autoSaveMeetingStatus(meetingNumber, newStatus) {
                    var formData = {
                        _token: '{{ csrf_token() }}',
                        meeting_number: meetingNumber,
                        meeting_date: $('#meetingOpp' + meetingNumber + 'Date').val(),
                        status: newStatus,
                        customer_response: $('#meetingOpp' + meetingNumber + 'CustomerResponse').val(),
                        customer_notes: $('#meetingOpp' + meetingNumber + 'CustomerNotes').val(),
                        marketing_notes: $('#meetingOpp' + meetingNumber + 'MarketingNotes').val(),
                        rnd_notes: $('#meetingOpp' + meetingNumber + 'RndNotes').val(),
                        is_auto_update: true // Flag untuk auto-update
                    };

                    $.ajax({
                        url: '{{ route('development.meeting-opp.store', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('Meeting OPP ' + meetingNumber + ' status auto-saved to database');
                        },
                        error: function(xhr) {
                            console.error('Error auto-saving meeting status:', xhr);
                        }
                    });
                }

                // Function untuk RnD approve meeting OPP
                function rndApproveMeetingOPP(meetingNumber) {
                    var rndApproval = $('#rndApproval' + meetingNumber).val();

                    // Debug: cek nilai yang dikirim
                    console.log('RnD Approval Value:', rndApproval);

                    if (!rndApproval || rndApproval === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Pilih status RnD Approval terlebih dahulu'
                        });
                        return;
                    }

                    var formData = {
                        _token: '{{ csrf_token() }}',
                        meeting_number: meetingNumber,
                        rnd_approval: rndApproval,
                        rnd_notes: $('#rndApprovalNotes' + meetingNumber).val()
                    };

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Sedang menyimpan RnD approval',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.meeting-opp.rnd-approve', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'RnD approval berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan RnD approval'
                            });
                        }
                    });
                }

                // Function untuk Marketing approve meeting OPP
                function marketingApproveMeetingOPP(meetingNumber) {
                    var marketingApproval = $('#marketingApproval' + meetingNumber).val();

                    // Debug: cek nilai yang dikirim
                    console.log('Marketing Approval Value:', marketingApproval);

                    if (!marketingApproval || marketingApproval === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Pilih status Marketing Approval terlebih dahulu'
                        });
                        return;
                    }

                    var formData = {
                        _token: '{{ csrf_token() }}',
                        meeting_number: meetingNumber,
                        marketing_approval: marketingApproval,
                        marketing_notes: $('#marketingApprovalNotes' + meetingNumber).val()
                    };

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Sedang menyimpan Customer response',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.meeting-opp.marketing-approve', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Customer response berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan Customer response'
                            });
                        }
                    });
                }

                // RnD Customer Approval Functions
                $('#submitRndCustomerApproval').click(function() {
                    submitRndCustomerApproval();
                });

                // Function untuk submit RnD customer approval
                function submitRndCustomerApproval() {
                    var rndApproval = $('#rndCustomerApproval').val();
                    var rndNotes = $('#rndCustomerNotes').val();

                    if (!rndApproval || rndApproval === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Pilih status RnD Approval terlebih dahulu'
                        });
                        return;
                    }

                    var formData = {
                        _token: '{{ csrf_token() }}',
                        rnd_customer_approval: rndApproval,
                        rnd_customer_notes: rndNotes
                    };

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Sedang menyimpan RnD approval',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('development.rnd-customer-approval', $job->id) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'RnD approval berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan RnD approval'
                            });
                        }
                    });
                }

            });
        </script>
    @endsection

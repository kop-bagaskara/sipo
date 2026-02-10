@extends('main.layouts.main')
@section('title')
    Dashboard
@endsection
@section('css')
    <style>
        .plan-items::-webkit-scrollbar {
            width: 6px;
        }

        .plan-items::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 3px;
        }

        .plan-items::-webkit-scrollbar-thumb {
            background: #6c757d;
            border-radius: 3px;
        }

        .plan-items::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        .dashboard-column {
            min-height: 600px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .job-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .job-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .job-item.completed { border-left-color: #28a745; }
        .job-item.in-progress { border-left-color: #ffc107; }
        .job-item.pending { border-left-color: #6c757d; }
        .job-item.urgent { border-left-color: #dc3545; }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: #495057;
            border-bottom-color: #dee2e6;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom-color: #007bff;
            background: transparent;
        }

        .tab-content {
            padding-top: 20px;
        }
    </style>
@endsection
@section('page-title')
    Dashboard
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard 1</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <h5>
                        Welcome to the Dashboard! {{ auth()->user()->name }}, {{ $user->divisi_name ?? '-' }}
                    </h5>
                    <a href="{{ route('mulai-proses.plan') }}" class="btn btn-info">
                        <i class="feather-edit-3"></i>
                        &nbsp;
                        JADWALKAN PLAN
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Navigation Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-fill" id="dashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                                <i class="mdi mdi-view-dashboard mr-2"></i>
                                Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ppic-tab" data-bs-toggle="tab" data-bs-target="#ppic" type="button" role="tab" aria-controls="ppic" aria-selected="false">
                                <i class="mdi mdi-tasks mr-2"></i>
                                Dashboard PPIC
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prepress-tab" data-bs-toggle="tab" data-bs-target="#prepress" type="button" role="tab" aria-controls="prepress" aria-selected="false">
                                <i class="mdi mdi-palette mr-2"></i>
                                Dashboard Prepress
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="row">
                <!-- PPIC Dashboard Column -->
                <div class="col-lg-6 dashboard-column">
                    <div class="card border-left-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="mdi mdi-tasks mr-2"></i>
                                    Dashboard PPIC
                                </h5>
                                <span class="badge badge-light">Production Planning</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="plan-items"
                                style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;">

                                @if ($todayPlans->count() > 5)
                                    <div class="text-center mb-2">
                                        <small class="text-muted">
                                            <i class="mdi mdi-arrow-down mr-1"></i>
                                            Scroll untuk melihat semua plan ({{ $todayPlans->count() }} items)
                                        </small>
                                    </div>
                                @endif
                                @if ($todayPlans->count() > 0)
                                    @foreach ($todayPlans as $plan)
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item {{ $plan->flag_status }}"
                                            style="min-height: 80px; gap: 10px;">
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $statusColor = 'bg-info';
                                                    $statusText = 'Scheduled';
                                                    if ($plan->flag_status == 'completed') {
                                                        $statusColor = 'bg-success';
                                                        $statusText = 'Completed';
                                                    } elseif ($plan->flag_status == 'in_progress') {
                                                        $statusColor = 'bg-warning';
                                                        $statusText = 'In Progress';
                                                    } elseif ($plan->flag_status == 'pending') {
                                                        $statusColor = 'bg-secondary';
                                                        $statusText = 'Pending';
                                                    } elseif ($plan->flag_status == 'cancelled') {
                                                        $statusColor = 'bg-danger';
                                                        $statusText = 'Cancelled';
                                                    }
                                                @endphp
                                                <div class="{{ $statusColor }} rounded-circle mr-3"
                                                    style="width: 12px; height: 12px;" title="{{ $statusText }}"></div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <h6 class="font-size-14 text-truncate">
                                                        {{ $plan->MaterialName ?? $plan->code_item }}</h6>
                                                    <h6 class="text-info d-block">{{ $plan->wo_docno }}</h6>
                                                    <h6 class="text-secondary d-block">Qty:
                                                        {{ is_numeric($plan->quantity) ? number_format($plan->quantity) : $plan->quantity }}
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                                <h6 class="font-weight-bold d-block text-truncate">{{ $plan->machine->Description ?? $plan->code_machine }}</h6>
                                                <h6 class="text-muted d-block">
                                                    @if ($plan->start_jam && $plan->end_jam)
                                                        @php
                                                            try {
                                                                $startTime = \Carbon\Carbon::parse($plan->start_jam,)->format('H:i');
                                                                $endTime = \Carbon\Carbon::parse($plan->end_jam)->format('H:i');
                                                                echo $startTime . ' - ' . $endTime;
                                                            } catch (Exception $e) {
                                                                echo '-';
                                                            }
                                                        @endphp
                                                    @elseif($plan->est_jam)
                                                        {{ is_numeric($plan->est_jam) ? number_format($plan->est_jam, 1) : $plan->est_jam }}
                                                        jam
                                                    @else
                                                        -
                                                    @endif
                                                </h6>
                                                @if ($plan->wo_docno || $plan->so_docno)
                                                    <small class="text-secondary d-block text-truncate"
                                                        style="max-width: 150px;">
                                                        @if ($plan->wo_docno)
                                                            WO: {{ $plan->wo_docno }}
                                                        @endif
                                                        @if ($plan->wo_docno && $plan->so_docno)
                                                            |
                                                        @endif
                                                        @if ($plan->so_docno)
                                                            SO: {{ $plan->so_docno }}
                                                        @endif
                                                    </small>
                                                @endif
                                                @if ($plan->delivery_date)
                                                    <small class="text-warning d-block text-truncate"
                                                        style="max-width: 150px;">Delivery: {{ $plan->delivery_date }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center p-3">
                                        <small class="text-muted">
                                            <i class="mdi mdi-calendar-blank mr-1"></i>
                                            Tidak ada plan untuk hari ini ({{ date('d/m/Y') }})
                                        </small>
                                    </div>
                                @endif
                                <div style="padding-bottom: 10px;"></div>
                            </div>

                            <div class="mt-3 pt-3 border-top">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Total Items:</small>
                                        <div class="font-weight-bold">{{ $totalPlans }} Items</div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted">Progress:</small>
                                        <div
                                            class="font-weight-bold {{ $progressPercentage > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ $progressPercentage }}% Complete</div>
                                    </div>
                                </div>
                                @if ($totalPlans > 0)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-success">✓ {{ $completedPlans }} Completed</small>
                                                <small class="text-warning">⟳ {{ $inProgressPlans }} In Progress</small>
                                                <small class="text-secondary">⏳ {{ $pendingPlans }} Pending</small>
                                                <small class="text-info">📅 {{ $scheduledPlans }} Scheduled</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prepress Dashboard Column -->
                <div class="col-lg-6 dashboard-column">
                    <div class="card border-left-success h-100">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="mdi mdi-palette mr-2"></i>
                                    Dashboard Prepress
                                </h5>
                                <span class="badge badge-light">Artwork & Plate</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="plan-items"
                                style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;">

                                <!-- Sample Job Orders with Status -->
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item completed">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0316</h6>
                                            <small class="text-muted d-block">Artwork Preparation</small>
                                            <small class="text-success d-block">Customer: PT Maju Bersama</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-success">Completed</span>
                                        <div class="mt-1">
                                            <small class="text-muted">09:00 - 11:00</small>
                                        </div>
                                        <small class="text-info d-block">Design Review</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item in-progress">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0298</h6>
                                            <small class="text-muted d-block">Color Separation</small>
                                            <small class="text-warning d-block">Customer: CV Sukses Jaya</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-warning">In Progress</span>
                                        <div class="mt-1">
                                            <small class="text-muted">14:00 - 16:00</small>
                                        </div>
                                        <small class="text-info d-block">CMYK Setup</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item urgent">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0279</h6>
                                            <small class="text-muted d-block">Plate Making</small>
                                            <small class="text-danger d-block">Customer: PT Prima Grafika</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-danger">Urgent</span>
                                        <div class="mt-1">
                                            <small class="text-muted">16:00 - 18:00</small>
                                        </div>
                                        <small class="text-info d-block">CTP Process</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item pending">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0320</h6>
                                            <small class="text-muted d-block">Proofing</small>
                                            <small class="text-secondary d-block">Customer: PT Grafika Mandiri</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-secondary">Pending</span>
                                        <div class="mt-1">
                                            <small class="text-muted">08:00 - 10:00</small>
                                        </div>
                                        <small class="text-info d-block">Digital Proof</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item in-progress">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0321</h6>
                                            <small class="text-muted d-block">RIP Processing</small>
                                            <small class="text-info d-block">Customer: PT Media Cetak</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-info">Processing</span>
                                        <div class="mt-1">
                                            <small class="text-muted">10:00 - 12:00</small>
                                        </div>
                                        <small class="text-info d-block">RIP Setup</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Total Jobs:</small>
                                        <div class="font-weight-bold">5 Jobs</div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted">Progress:</small>
                                        <div class="font-weight-bold text-success">60% Complete</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-success">✓ 1 Completed</small>
                                            <small class="text-warning">⟳ 2 In Progress</small>
                                            <small class="text-secondary">⏳ 1 Pending</small>
                                            <small class="text-danger">🚨 1 Urgent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PPIC Full Dashboard Tab -->
        <div class="tab-pane fade" id="ppic" role="tabpanel" aria-labelledby="ppic-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card border-left-primary">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0 text-white">
                                    <i class="mdi mdi-tasks mr-2"></i>
                                    Dashboard PPIC - Production Planning & Control
                                </h4>
                                <div>
                                    <span class="badge badge-light mr-2">Production Planning</span>
                                    <span class="badge badge-info">Real-time Updates</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- PPIC Statistics Row -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $totalPlans }}</h3>
                                            <small>Total Plans</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $completedPlans }}</h3>
                                            <small>Completed</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $inProgressPlans }}</h3>
                                            <small>In Progress</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $scheduledPlans }}</h3>
                                            <small>Scheduled</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed PPIC Plans -->
                            <div class="plan-items"
                                style="max-height: 500px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;">
                                @if ($todayPlans->count() > 0)
                                    @foreach ($todayPlans as $plan)
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item {{ $plan->flag_status }}"
                                            style="min-height: 80px; gap: 10px;">
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $statusColor = 'bg-info';
                                                    $statusText = 'Scheduled';
                                                    if ($plan->flag_status == 'completed') {
                                                        $statusColor = 'bg-success';
                                                        $statusText = 'Completed';
                                                    } elseif ($plan->flag_status == 'in_progress') {
                                                        $statusColor = 'bg-warning';
                                                        $statusText = 'In Progress';
                                                    } elseif ($plan->flag_status == 'pending') {
                                                        $statusColor = 'bg-secondary';
                                                        $statusText = 'Pending';
                                                    } elseif ($plan->flag_status == 'cancelled') {
                                                        $statusColor = 'bg-danger';
                                                        $statusText = 'Cancelled';
                                                    }
                                                @endphp
                                                <div class="{{ $statusColor }} rounded-circle mr-3"
                                                    style="width: 12px; height: 12px;" title="{{ $statusText }}"></div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <h6 class="font-size-14 text-truncate">
                                                        {{ $plan->MaterialName ?? $plan->code_item }}</h6>
                                                    <h6 class="text-info d-block">{{ $plan->wo_docno }}</h6>
                                                    <h6 class="text-secondary d-block">Qty:
                                                        {{ is_numeric($plan->quantity) ? number_format($plan->quantity) : $plan->quantity }}
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                                <h6 class="font-weight-bold d-block text-truncate">{{ $plan->machine->Description ?? $plan->code_machine }}</h6>
                                                <h6 class="text-muted d-block">
                                                    @if ($plan->start_jam && $plan->end_jam)
                                                        @php
                                                            try {
                                                                $startTime = \Carbon\Carbon::parse($plan->start_jam,)->format('H:i');
                                                                $endTime = \Carbon\Carbon::parse($plan->end_jam)->format('H:i');
                                                                echo $startTime . ' - ' . $endTime;
                                                            } catch (Exception $e) {
                                                                echo '-';
                                                            }
                                                        @endphp
                                                    @elseif($plan->est_jam)
                                                        {{ is_numeric($plan->est_jam) ? number_format($plan->est_jam, 1) : $plan->est_jam }}
                                                        jam
                                                    @else
                                                        -
                                                    @endif
                                                </h6>
                                                @if ($plan->wo_docno || $plan->so_docno)
                                                    <small class="text-secondary d-block text-truncate"
                                                        style="max-width: 150px;">
                                                        @if ($plan->wo_docno)
                                                            WO: {{ $plan->wo_docno }}
                                                        @endif
                                                        @if ($plan->wo_docno && $plan->so_docno)
                                                            |
                                                        @endif
                                                        @if ($plan->so_docno)
                                                            SO: {{ $plan->so_docno }}
                                                        @endif
                                                    </small>
                                                @endif
                                                @if ($plan->delivery_date)
                                                    <small class="text-warning d-block text-truncate"
                                                        style="max-width: 150px;">Delivery: {{ $plan->delivery_date }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center p-3">
                                        <small class="text-muted">
                                            <i class="mdi mdi-calendar-blank mr-1"></i>
                                            Tidak ada plan untuk hari ini ({{ date('d/m/Y') }})
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prepress Full Dashboard Tab -->
        <div class="tab-pane fade" id="prepress" role="tabpanel" aria-labelledby="prepress-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card border-left-success">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0 text-white">
                                    <i class="mdi mdi-palette mr-2"></i>
                                    Dashboard Prepress - Artwork & Plate Management
                                </h4>
                                <div>
                                    <span class="badge badge-light mr-2">Artwork & Plate</span>
                                    <span class="badge badge-info">Job Tracking</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Prepress Statistics Row -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">1</h3>
                                            <small>Completed</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">2</h3>
                                            <small>In Progress</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">1</h3>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">1</h3>
                                            <small>Urgent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Prepress Jobs -->
                            <div class="plan-items"
                                style="max-height: 500px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;">

                                <!-- Sample Job Orders with Status -->
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item completed">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0316</h6>
                                            <small class="text-muted d-block">Artwork Preparation</small>
                                            <small class="text-success d-block">Customer: PT Maju Bersama</small>
                                            <small class="text-info d-block">Priority: High</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-success">Completed</span>
                                        <div class="mt-1">
                                            <small class="text-muted">09:00 - 11:00</small>
                                        </div>
                                        <small class="text-info d-block">Design Review</small>
                                        <small class="text-success d-block">✓ Approved</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item in-progress">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0298</h6>
                                            <small class="text-muted d-block">Color Separation</small>
                                            <small class="text-warning d-block">Customer: CV Sukses Jaya</small>
                                            <small class="text-info d-block">Priority: Medium</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-warning">In Progress</span>
                                        <div class="mt-1">
                                            <small class="text-muted">14:00 - 16:00</small>
                                        </div>
                                        <small class="text-info d-block">CMYK Setup</small>
                                        <small class="text-warning d-block">⟳ Processing</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item urgent">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0279</h6>
                                            <small class="text-muted d-block">Plate Making</small>
                                            <small class="text-danger d-block">Customer: PT Prima Grafika</small>
                                            <small class="text-danger d-block">Priority: Critical</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-danger">Urgent</span>
                                        <div class="mt-1">
                                            <small class="text-muted">16:00 - 18:00</small>
                                        </div>
                                        <small class="text-info d-block">CTP Process</small>
                                        <small class="text-danger d-block">🚨 Rush Order</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item pending">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0320</h6>
                                            <small class="text-muted d-block">Proofing</small>
                                            <small class="text-secondary d-block">Customer: PT Grafika Mandiri</small>
                                            <small class="text-info d-block">Priority: Normal</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-secondary">Pending</span>
                                        <div class="mt-1">
                                            <small class="text-muted">08:00 - 10:00</small>
                                        </div>
                                        <small class="text-info d-block">Digital Proof</small>
                                        <small class="text-secondary d-block">⏳ Waiting</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item in-progress">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h6 class="mb-0 font-size-14 text-truncate">TC.0021.0321</h6>
                                            <small class="text-muted d-block">RIP Processing</small>
                                            <small class="text-info d-block">Customer: PT Media Cetak</small>
                                            <small class="text-info d-block">Priority: Medium</small>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                        <span class="status-badge badge badge-info">Processing</span>
                                        <div class="mt-1">
                                            <small class="text-muted">10:00 - 12:00</small>
                                        </div>
                                        <small class="text-info d-block">RIP Setup</small>
                                        <small class="text-info d-block">⟳ RIP Running</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Status Legend:</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">Processing</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">Urgent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Add hover effects for job items
            $('.job-item').hover(
                function() {
                    $(this).addClass('shadow-sm');
                },
                function() {
                    $(this).removeClass('shadow-sm');
                }
            );

            // Auto-refresh status indicators (optional)
            setInterval(function() {
                // You can add AJAX calls here to refresh job statuses
                console.log('Status refresh check...');
            }, 30000); // Check every 30 seconds

            // Tab switching with smooth transitions
            $('#dashboardTabs button').on('click', function() {
                $('.tab-pane').removeClass('show active');
                $('.nav-link').removeClass('active');

                $(this).addClass('active');
                $($(this).attr('data-bs-target')).addClass('show active');
            });
        });
    </script>
@endsection

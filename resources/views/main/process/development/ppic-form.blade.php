@extends('main.layouts.main')
@section('title')
    PPIC Form - Production Planning & Inventory Control
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

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .info-card h4 {
            /* color: #28a745; */
            font-weight: 1000;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }

        .material-status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-ready {
            background-color: #d4edda;
            color: #155724;
        }

        .status-not-ready {
            background-color: #f8d7da;
            color: #721c24;
        }


        .production-schedule-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .btn-ppic {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-ppic:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .alert-material {
            border-left: 4px solid #dc3545;
        }

        .alert-ready {
            border-left: 4px solid #28a745;
        }

        /* Countdown Styles */
        .countdown-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .countdown-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px 10px;
            margin: 5px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .countdown-box:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.2);
        }

        .countdown-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .countdown-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        /* Timeline Info Styles */
        .timeline-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-item i {
            font-size: 1.2rem;
            margin-right: 10px;
            width: 20px;
        }

        .timeline-item span {
            font-weight: 600;
            flex: 1;
        }

        /* Progress Bar Animation */
        .progress-bar {
            transition: width 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background-image: linear-gradient(
                -45deg,
                rgba(255, 255, 255, .2) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, .2) 50%,
                rgba(255, 255, 255, .2) 75%,
                transparent 75%,
                transparent
            );
            background-size: 50px 50px;
            animation: move 2s linear infinite;
        }

        @keyframes move {
            0% { background-position: 0 0; }
            100% { background-position: 50px 50px; }
        }

        /* Production Process List Styles */
        .process-table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .process-table thead th {
            /* background: #007bff; */
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .process-table tbody tr {
            transition: all 0.3s ease;
        }

        .process-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .process-table tbody tr.table-active {
            background-color: #e3f2fd !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .process-table tbody td {
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .process-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }

        .process-badge.prepress {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .process-badge.produksi {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .process-badge.finishing {
            background-color: #e8f5e8;
            color: #388e3c;
        }

        .process-badge.quality {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .process-badge.packaging {
            background-color: #fce4ec;
            color: #c2185b;
        }

        .process-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
        }

        .process-status.scheduled {
            background-color: #d4edda;
            color: #155724;
        }

        .process-status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .process-status.in-progress {
            background-color: #d4edda;
            color: #155724;
        }

        .process-status.completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .process-status {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .process-status:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .process-schedule-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
            font-weight: 600;
        }

        .process-schedule-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .process-schedule-btn.scheduled {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .process-schedule-btn.scheduled:hover {
            background-color: #c3e6cb;
            border-color: #1e7e34;
        }

        .process-schedule-btn.in-progress {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }

        .process-schedule-btn.in-progress:hover {
            background-color: #bee5eb;
            border-color: #138496;
        }

        .process-schedule-btn.completed {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }

        .process-schedule-btn.completed:hover {
            background-color: #bee5eb;
            border-color: #138496;
        }

        /* Responsive Countdown */
        @media (max-width: 768px) {
            .countdown-number {
                font-size: 2rem;
            }

            .countdown-box {
                padding: 10px 5px;
                margin: 2px;
            }
        }
    </style>
@endsection
@section('page-title')
    PPIC Form - Production Planning & Inventory Control
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">PPIC Form</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.index') }}">RnD Workspace</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.view', $job->id) }}">Detail Job</a></li>
                    <li class="breadcrumb-item active">PPIC Form</li>
                </ol>
            </div>
        </div>

        <!-- Header Info -->
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 style="color: white; font-weight: bold;">{{ $job->job_name }}</h3>
                    <p class="mb-0">Job Code: {{ $job->job_code }} | Customer: {{ $job->customer }}</p>
                </div>
            </div>
        </div>

        <!-- Timeline Countdown -->
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h4><i class="mdi mdi-timeline-clock"></i> Timeline & Countdown</h4>
                    @php
                        // Tanggal dimulai = tanggal PPIC mengatur lead time (created_at dari leadTimeConfiguration)
                        $leadTimeStartedAt = $job->leadTimeConfiguration ?
                            \Carbon\Carbon::parse($job->leadTimeConfiguration->created_at) :
                            \Carbon\Carbon::parse($job->created_at);

                        $maxLeadTime = $job->leadTimeConfiguration->max_lead_time_days ?? 0;
                        $totalLeadTime = $job->leadTimeConfiguration->total_lead_time_days ?? $maxLeadTime;
                        $developmentDays = 14;
                        
                        // Cek apakah job sudah dijadwalkan (ada lead time configuration)
                        $isScheduled = $job->leadTimeConfiguration && $totalLeadTime > 0;

                        // Countdown hanya berdasarkan total_lead_time_days (44 hari), bukan development + lead time
                        $countdownDays = $totalLeadTime; // 44 hari
                        
                        // Pastikan countdownDays minimal 1 hari untuk menghindari error
                        if ($countdownDays <= 0) {
                            $countdownDays = 1; // Default minimal 1 hari
                        }
                        
                        $deadline = $leadTimeStartedAt->copy()->addDays($countdownDays);
                        $now = \Carbon\Carbon::now();
                        $daysLeft = $now->diffInDays($deadline, false);
                        $hoursLeft = $now->diffInHours($deadline, false);
                        
                        // Cegah division by zero jika countdownDays = 0
                        if ($countdownDays > 0) {
                            $progress = min(($now->diffInDays($leadTimeStartedAt) / $countdownDays) * 100, 100);
                        } else {
                            $progress = 0; // Default progress jika countdownDays = 0
                        }
                        
                        // Pastikan progress tidak negatif
                        $progress = max($progress, 0);

                        // Total timeline untuk display info (development + lead time)
                        $totalTimelineDays = $developmentDays + $totalLeadTime;
                        
                        // Pastikan totalTimelineDays minimal 1 hari
                        if ($totalTimelineDays <= 0) {
                            $totalTimelineDays = 1;
                        }
                    @endphp

                    {{-- <div class="alert alert-info mb-3">
                        <h6><i class="mdi mdi-information"></i> Timeline Development</h6>
                        @if($isScheduled)
                            <p class="mb-2">Berikut adalah timeline dan countdown untuk job development ini berdasarkan lead time yang dikonfigurasi.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Debug Info:</strong></small><br>
                                    <small>Max Lead Time: {{ $maxLeadTime }} hari</small><br>
                                    <small>Total Lead Time: {{ $totalLeadTime }} hari</small><br>
                                    <small>Development: {{ $developmentDays }} hari</small><br>
                                    <small>Countdown: {{ $countdownDays }} hari</small><br>
                                    <small>Total Timeline: {{ $totalTimelineDays }} hari</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Timeline:</strong></small><br>
                                    <small>Dimulai: {{ $leadTimeStartedAt->format('d/m/Y H:i') }}</small><br>
                                    <small>Deadline: {{ $deadline->format('d/m/Y H:i') }}</small><br>
                                    <small>Sisa: {{ $daysLeft }} hari</small>
                                </div>
                            </div>
                        @else
                            <p class="mb-2">Job ini belum memiliki jadwal development. Silakan konfigurasi lead time material di bawah untuk membuat jadwal.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Status:</strong></small><br>
                                    <small>Lead Time Configuration: <span class="text-warning">Belum ada</span></small><br>
                                    <small>Total Lead Time: <span class="text-warning">Belum dikonfigurasi</span></small><br>
                                    <small>Development: {{ $developmentDays }} hari (default)</small><br>
                                    <small>Countdown: <span class="text-warning">Belum tersedia</span></small><br>
                                    <small>Total Timeline: <span class="text-warning">Belum tersedia</span></small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Langkah Selanjutnya:</strong></small><br>
                                    <small>1. Konfigurasi lead time material</small><br>
                                    <small>2. Simpan lead time configuration</small><br>
                                    <small>3. Timeline akan otomatis terhitung</small><br>
                                    <small>4. Countdown akan mulai berjalan</small>
                                </div>
                            </div>
                        @endif
                    </div> --}}

                    @php
                        if (!$isScheduled) {
                            // Job belum dijadwalkan
                            $statusClass = 'secondary';
                            $statusText = 'BELUM DIJADWALKAN';
                            $statusIcon = 'mdi-calendar-clock';
                        } else {
                            // Status berdasarkan sisa waktu
                            if ($daysLeft < 0) {
                                $statusClass = 'danger';
                                $statusText = 'OVERDUE';
                                $statusIcon = 'mdi-alert-circle';
                            } elseif ($daysLeft <= 1) {
                                $statusClass = 'warning';
                                $statusText = 'DEADLINE BESOK!';
                                $statusIcon = 'mdi-clock-alert';
                            } elseif ($daysLeft <= 3) {
                                $statusClass = 'warning';
                                $statusText = 'DEADLINE DEKAT';
                                $statusIcon = 'mdi-clock-fast';
                            } else {
                                $statusClass = 'success';
                                $statusText = 'ON TRACK';
                                $statusIcon = 'mdi-check-circle';
                            }
                        }
                    @endphp

                    <div class="row">
                        <div class="col-md-8">
                            @if($isScheduled)
                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold">Progress Development</span>
                                        <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $statusClass }}"
                                             style="width: {{ $progress }}%"
                                             role="progressbar">
                                            {{ round($progress) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Dimulai: {{ $leadTimeStartedAt->format('d/m/Y H:i') }} |
                                        Deadline: {{ $deadline->format('d/m/Y H:i') }}
                                    </small>
                                </div>

                                <!-- Countdown Timer -->
                                <div class="countdown-container mb-3">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="countdown-box">
                                                <div class="countdown-number" id="countdown-days">{{ abs($daysLeft) }}</div>
                                                <div class="countdown-label">HARI</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-box">
                                                <div class="countdown-number" id="countdown-hours">{{ $hoursLeft % 24 }}</div>
                                                <div class="countdown-label">JAM</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-box">
                                                <div class="countdown-number" id="countdown-minutes">0</div>
                                                <div class="countdown-label">MENIT</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="countdown-box">
                                                <div class="countdown-number" id="countdown-seconds">0</div>
                                                <div class="countdown-label">DETIK</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Job Belum Dijadwalkan -->
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-calendar-clock text-info" style="font-size: 2rem; margin-right: 15px;"></i>
                                        <div>
                                            <h6 class="mb-1"><strong>Job Belum Dijadwalkan</strong></h6>
                                            <p class="mb-0">Job ini belum memiliki jadwal development. Silakan konfigurasi lead time material di bawah untuk membuat jadwal.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <!-- Timeline Info -->
                            <div class="timeline-info">
                                <h6><i class="mdi {{ $statusIcon }} text-{{ $statusClass }}"></i> Status Timeline</h6>
                                
                                @if($isScheduled)
                                    <div class="timeline-item">
                                        <i class="mdi mdi-play-circle text-success"></i>
                                        <span>Lead Time Dimulai</span>
                                        <small class="text-muted d-block">{{ $leadTimeStartedAt->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-clock-outline text-info"></i>
                                        <span>Development Phase</span>
                                        <small class="text-muted d-block">{{ $developmentDays }} hari</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-package-variant text-warning"></i>
                                        <span>Material Lead Time</span>
                                        <small class="text-muted d-block">{{ $maxLeadTime }} hari</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-calculator text-info"></i>
                                        <span>Total Lead Time</span>
                                        <small class="text-muted d-block">{{ $totalLeadTime }} hari</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-timer text-warning"></i>
                                        <span>Countdown</span>
                                        <small class="text-muted d-block">{{ $countdownDays }} hari</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-flag-checkered text-{{ $statusClass }}"></i>
                                        <span>Deadline</span>
                                        <small class="text-muted d-block">{{ $deadline->format('d/m/Y H:i') }}</small>
                                    </div>
                                @else
                                    <div class="timeline-item">
                                        <i class="mdi mdi-calendar-clock text-secondary"></i>
                                        <span>Status</span>
                                        <small class="text-muted d-block">Belum Dijadwalkan</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-information text-info"></i>
                                        <span>Langkah Selanjutnya</span>
                                        <small class="text-muted d-block">Konfigurasi Lead Time</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-clock-outline text-info"></i>
                                        <span>Development Phase</span>
                                        <small class="text-muted d-block">{{ $developmentDays }} hari (default)</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-package-variant text-warning"></i>
                                        <span>Material Lead Time</span>
                                        <small class="text-muted d-block">Belum dikonfigurasi</small>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="mdi mdi-calculator text-info"></i>
                                        <span>Total Lead Time</span>
                                        <small class="text-muted d-block">Belum dikonfigurasi</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <!-- Lead Time Configuration -->
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="mdi mdi-clock-outline"></i> Lead Time Configuration</h6>
                    <div class="alert alert-info mb-3">
                        <h6><i class="mdi mdi-information"></i> Konfigurasi Lead Time Material</h6>
                        <p class="mb-2">PPIC harus mengatur lead time untuk setiap kategori material berdasarkan kebutuhan job ini.</p>
                        <p class="mb-2"><strong>Material yang digunakan di job ini:</strong></p>
                        <ul class="mb-2">
                            @if($job->tinta_khusus)
                                <li><i class="mdi mdi-palette text-success"></i> <strong>Tinta Khusus</strong> - {{ $job->tinta_khusus_detail ?? 'Ya, menggunakan tinta khusus' }}</li>
                            @endif
                            @if($job->kertas_khusus)
                                <li><i class="mdi mdi-file-document text-primary"></i> <strong>Kertas Khusus</strong> - {{ $job->kertas_khusus_detail ?? 'Ya, menggunakan kertas khusus' }}</li>
                            @endif
                            @if($job->foil_khusus)
                                <li><i class="mdi mdi-star text-warning"></i> <strong>Foil Khusus</strong> - {{ $job->foil_khusus_detail ?? 'Ya, menggunakan foil khusus' }}</li>
                            @endif
                            @if($job->pale_tooling_khusus)
                                <li><i class="mdi mdi-hammer text-danger"></i> <strong>Pale Tooling Khusus</strong> - {{ $job->pale_tooling_khusus_detail ?? 'Ya, menggunakan pale tooling khusus' }}</li>
                            @endif
                            @if(!$job->tinta_khusus && !$job->kertas_khusus && !$job->foil_khusus && !$job->pale_tooling_khusus)
                                <li><i class="mdi mdi-check-circle text-success"></i> <strong>Tidak ada material khusus</strong> - Job ini tidak menggunakan material khusus</li>
                            @endif
                        </ul>
                        <p class="mb-0"><strong>Catatan:</strong> Hanya material yang digunakan yang akan dihitung dalam total estimasi hari development.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tinta & Material:
                                    @if($job->tinta_khusus)
                                        <span class="badge badge-success">DIGUNAKAN</span>
                                    @else
                                        <span class="badge badge-secondary">TIDAK DIGUNAKAN</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control {{ $job->tinta_khusus ? '' : 'bg-light' }}" id="tintaMaterialDays"
                                           value="{{ $job->leadTimeConfiguration->tinta_material_days ?? 7 }}" min="0"
                                           {{ $job->tinta_khusus ? '' : 'readonly' }}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">hari</span>
                                    </div>
                                </div>
                                <small class="text-muted">Lead time untuk develop tinta & material</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kertas Baru:
                                    @if($job->kertas_khusus)
                                        <span class="badge badge-success">DIGUNAKAN</span>
                                    @else
                                        <span class="badge badge-secondary">TIDAK DIGUNAKAN</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control {{ $job->kertas_khusus ? '' : 'bg-light' }}" id="kertasBaruDays"
                                           value="{{ $job->leadTimeConfiguration->kertas_baru_days ?? 30 }}" min="0"
                                           {{ $job->kertas_khusus ? '' : 'readonly' }}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">hari</span>
                                    </div>
                                </div>
                                <small class="text-muted">Lead time untuk kertas baru</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Foil:
                                    @if($job->foil_khusus)
                                        <span class="badge badge-success">DIGUNAKAN</span>
                                    @else
                                        <span class="badge badge-secondary">TIDAK DIGUNAKAN</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control {{ $job->foil_khusus ? '' : 'bg-light' }}" id="foilDays"
                                           value="{{ $job->leadTimeConfiguration->foil_days ?? 7 }}" min="0"
                                           {{ $job->foil_khusus ? '' : 'readonly' }}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">hari</span>
                                    </div>
                                </div>
                                <small class="text-muted">Lead time untuk develop foil</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tooling:
                                    @if($job->pale_tooling_khusus)
                                        <span class="badge badge-success">DIGUNAKAN</span>
                                    @else
                                        <span class="badge badge-secondary">TIDAK DIGUNAKAN</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control {{ $job->pale_tooling_khusus ? '' : 'bg-light' }}" id="toolingDays"
                                           value="{{ $job->leadTimeConfiguration->tooling_days ?? 14 }}" min="0"
                                           {{ $job->pale_tooling_khusus ? '' : 'readonly' }}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">hari</span>
                                    </div>
                                </div>
                                <small class="text-muted">Lead time untuk develop tooling</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Proses Produksi:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="produksiHours"
                                           value="{{ $job->leadTimeConfiguration->produksi_hours ?? 1 }}" min="0" step="0.5">
                                    <div class="input-group-append">
                                        <span class="input-group-text">jam</span>
                                    </div>
                                </div>
                                <small class="text-muted">Estimasi waktu produksi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Lead Time (Hari):</label>
                                <input type="number" class="form-control" id="totalLeadTimeDays" readonly>
                                <small class="text-muted">MAX(lead time material) + 14 hari development</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" id="saveLeadTimeBtn">
                            <i class="mdi mdi-content-save"></i> Simpan Lead Time Configuration
                        </button>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Production Process List -->
        @if($job->proses && is_array($job->proses) && count($job->proses) > 0)
        @php
            // Check material status first
            $allMaterialsReady = true;
            $materials = [];

            if ($job->kertas_khusus) {
                $kertasPurchasing = $job->materialPurchasing->where('material_type', 'kertas')->first();
                $materials[] = [
                    'type' => 'kertas',
                    'name' => 'Kertas Khusus',
                    'detail' => $job->kertas_khusus_detail ?? 'Ya, menggunakan kertas khusus',
                    'status' => $kertasPurchasing ? $kertasPurchasing->purchasing_status : 'belum',
                    'info' => $kertasPurchasing ? $kertasPurchasing->purchasing_info : '',
                    'icon' => 'mdi-file-document',
                    'color' => 'text-primary'
                ];
                if (!$kertasPurchasing || $kertasPurchasing->purchasing_status !== 'sudah') {
                    $allMaterialsReady = false;
                }
            }

            if ($job->tinta_khusus) {
                $tintaPurchasing = $job->materialPurchasing->where('material_type', 'tinta')->first();
                $materials[] = [
                    'type' => 'tinta',
                    'name' => 'Tinta Khusus',
                    'detail' => $job->tinta_khusus_detail ?? 'Ya, menggunakan tinta khusus',
                    'status' => $tintaPurchasing ? $tintaPurchasing->purchasing_status : 'belum',
                    'info' => $tintaPurchasing ? $tintaPurchasing->purchasing_info : '',
                    'icon' => 'mdi-palette',
                    'color' => 'text-success'
                ];
                if (!$tintaPurchasing || $tintaPurchasing->purchasing_status !== 'sudah') {
                    $allMaterialsReady = false;
                }
            }

            if ($job->foil_khusus) {
                $foilPurchasing = $job->materialPurchasing->where('material_type', 'foil')->first();
                $materials[] = [
                    'type' => 'foil',
                    'name' => 'Foil Khusus',
                    'detail' => $job->foil_khusus_detail ?? 'Ya, menggunakan foil khusus',
                    'status' => $foilPurchasing ? $foilPurchasing->purchasing_status : 'belum',
                    'info' => $foilPurchasing ? $foilPurchasing->purchasing_info : '',
                    'icon' => 'mdi-star',
                    'color' => 'text-warning'
                ];
                if (!$foilPurchasing || $foilPurchasing->purchasing_status !== 'sudah') {
                    $allMaterialsReady = false;
                }
            }

            if ($job->pale_tooling_khusus) {
                $paleToolingPurchasing = $job->materialPurchasing->where('material_type', 'pale_tooling')->first();
                $materials[] = [
                    'type' => 'pale_tooling',
                    'name' => 'Pale Tooling Khusus',
                    'detail' => $job->pale_tooling_khusus_detail ?? 'Ya, menggunakan pale tooling khusus',
                    'status' => $paleToolingPurchasing ? $paleToolingPurchasing->purchasing_status : 'belum',
                    'info' => $paleToolingPurchasing ? $paleToolingPurchasing->purchasing_info : '',
                    'icon' => 'mdi-hammer',
                    'color' => 'text-danger'
                ];
                if (!$paleToolingPurchasing || $paleToolingPurchasing->purchasing_status !== 'sudah') {
                    $allMaterialsReady = false;
                }
            }

            // If no special materials, set to ready
            if (count($materials) === 0) {
                $allMaterialsReady = true;
            }
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h4><i class="mdi mdi-cog"></i> Production Process List</h4>
                    

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Proses</th>
                                    <th width="15%">Kategori</th>
                                    <th width="12%">Estimasi Waktu</th>
                                    <th width="18%">Keterangan</th>
                                    <th width="15%">Mesin</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->proses as $index => $process)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $icon = 'mdi-cog';
                                                switch(strtoupper($process)) {
                                                    case 'CETAK':
                                                        $icon = 'mdi-printer';
                                                        break;
                                                    case 'PLONG':
                                                        $icon = 'mdi-punch';
                                                        break;
                                                    case 'POTONG':
                                                        $icon = 'mdi-content-cut';
                                                        break;
                                                    case 'EMBOSS':
                                                        $icon = 'mdi-texture';
                                                        break;
                                                }
                                            @endphp
                                            <i class="mdi {{ $icon }} text-primary mr-2"></i>
                                            <strong>{{ $process }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $kategori = strtolower($process);
                                                $badgeClass = 'process-badge badge-info';
                                                if (strpos($kategori, 'cetak') !== false) {
                                                    $badgeClass = 'process-badge produksi';
                                                } elseif (strpos($kategori, 'plong') !== false) {
                                                    $badgeClass = 'process-badge finishing';
                                                } elseif (strpos($kategori, 'potong') !== false) {
                                                    $badgeClass = 'process-badge finishing';
                                                } elseif (strpos($kategori, 'emboss') !== false) {
                                                    $badgeClass = 'process-badge finishing';
                                                }
                                            @endphp
                                            <span class="{{ $badgeClass }}">
                                                {{ $process }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="mdi mdi-clock text-warning"></i>
                                            @php
                                                $estimasiWaktu = '1 jam'; // default
                                                switch(strtoupper($process)) {
                                                    case 'CETAK':
                                                        $estimasiWaktu = '2 jam';
                                                        break;
                                                    case 'PLONG':
                                                        $estimasiWaktu = '1 jam';
                                                        break;
                                                    case 'POTONG':
                                                        $estimasiWaktu = '1 jam';
                                                        break;
                                                    case 'EMBOSS':
                                                        $estimasiWaktu = '2 jam';
                                                        break;
                                                }
                                            @endphp
                                            {{ $estimasiWaktu }}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                @php
                                                    $deskripsi = '';
                                                    switch(strtoupper($process)) {
                                                        case 'CETAK':
                                                            $deskripsi = 'Proses pencetakan menggunakan mesin cetak offset atau digital';
                                                            break;
                                                        case 'PLONG':
                                                            $deskripsi = 'Proses pelubangan kertas sesuai dengan desain yang ditentukan';
                                                            break;
                                                        case 'POTONG':
                                                            $deskripsi = 'Proses pemotongan kertas sesuai ukuran final produk';
                                                            break;
                                                        case 'EMBOSS':
                                                            $deskripsi = 'Proses pemberian efek timbul pada kertas';
                                                            break;
                                                        default:
                                                            $deskripsi = 'Proses ' . $process . ' untuk job ' . $job->job_name;
                                                            break;
                                                    }
                                                @endphp
                                                {{ $deskripsi }}
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                // Cek apakah proses ini sudah dijadwalkan dan ada mesin yang dipilih
                                                $scheduledProcess = $job->productionSchedules->where('proses', $process)->first();
                                                $machineInfo = '';
                                                if ($scheduledProcess && $scheduledProcess->machine_code) {
                                                    $machineInfo = $scheduledProcess->machine_code;
                                                }
                                            @endphp
                                            @if($machineInfo)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-cog"></i> {{ $machineInfo }}
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="mdi mdi-help-circle"></i> Belum dipilih
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Cek apakah proses ini sudah dijadwalkan
                                                $scheduledProcess = $job->productionSchedules->where('proses', $process)->first();
                                                $isScheduled = $scheduledProcess && $scheduledProcess->machine_code;
                                            @endphp
                                            @if($isScheduled)
                                                <button type="button" class="btn btn-success btn-sm" disabled>
                                                    <i class="mdi mdi-calendar-check"></i> Dijadwalkan
                                                </button>
                                            @elseif($allMaterialsReady)
                                                <button type="button" class="btn btn-outline-warning btn-sm process-schedule-btn" 
                                                        data-process="{{ $process }}" 
                                                        data-process-index="{{ $index }}"
                                                        title="Klik untuk menjadwalkan proses {{ $process }}">
                                                    <i class="mdi mdi-calendar-plus"></i> Jadwalkan
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                        disabled
                                                        title="Material belum ready, tidak dapat menjadwalkan">
                                                    <i class="mdi mdi-calendar-remove"></i> Material Belum Ready
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h4><i class="mdi mdi-cog"></i> Production Process List</h4>
                    <div class="alert alert-warning">
                        <h6><i class="mdi mdi-alert"></i> Data Proses Belum Tersedia</h6>
                        <p class="mb-0">Marketing belum menetapkan proses produksi untuk job ini. Silakan hubungi Marketing untuk melengkapi data proses produksi.</p>
                        <hr class="my-2">
                        <small class="text-muted">
                            <i class="mdi mdi-information"></i> 
                            Data proses produksi harus diisi oleh Marketing pada saat input job development.
                        </small>
                        <div class="mt-3">
                            <h6><i class="mdi mdi-lightbulb"></i> Proses yang Dapat Ditetapkan:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>CETAK:</strong> Proses pencetakan menggunakan mesin cetak offset atau digital</li>
                                        <li><strong>PLONG:</strong> Proses pelubangan kertas sesuai dengan desain yang ditentukan</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>POTONG:</strong> Proses pemotongan kertas sesuai ukuran final produk</li>
                                        <li><strong>EMBOSS:</strong> Proses pemberian efek timbul pada kertas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Material Status Check & Purchasing Info -->
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h4><i class="mdi mdi-palette"></i> Material Status Check & Purchasing Info</h4>


                    @if(count($materials) > 0)
                        @if($allMaterialsReady)
                            <div class="alert alert-ready">
                                <h6><i class="mdi mdi-check-circle"></i> Semua Material Ready!</h6>
                                <p class="mb-0">Semua material khusus sudah siap untuk produksi.</p>
                            </div>
                        @else
                            <div class="alert alert-material">
                                <h6><i class="mdi mdi-alert-circle"></i> Material Belum Ready!</h6>
                                <p class="mb-0">Masih ada material yang belum siap. Tidak dapat menjadwalkan produksi.</p>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="15%">Material</th>
                                        <th width="25%">Detail</th>
                                        <th width="15%">Status</th>
                                        <th width="25%">Info Purchasing</th>
                                        <th width="20%">Update Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materials as $material)
                                        <tr>
                                            <td>
                                                <i class="mdi {{ $material['icon'] }} {{ $material['color'] }}"></i>
                                                <strong>{{ $material['name'] }}</strong>
                                            </td>
                                            <td>{{ $material['detail'] }}</td>
                                            <td>
                                                @if($material['status'] === 'sudah')
                                                    <span class="material-status status-ready">
                                                        <i class="mdi mdi-check-circle"></i> Ready
                                                    </span>
                                                @elseif($material['status'] === 'proses')
                                                    <span class="material-status" style="background-color: #fff3cd; color: #856404;">
                                                        <i class="mdi mdi-clock"></i> Proses Kedatangan
                                                    </span>
                                                @elseif($material['status'] === 'tidak_ada')
                                                    <span class="material-status" style="background-color: #f8d7da; color: #721c24;">
                                                        <i class="mdi mdi-close-circle"></i> Tidak Ada
                                                    </span>
                                                @else
                                                    <span class="material-status status-not-ready">
                                                        <i class="mdi mdi-alert-circle"></i> Not Ready
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm" id="{{ $material['type'] }}PurchasingInfo" rows="2"
                                                          placeholder="Catatan info purchasing...">{{ $material['info'] }}</textarea>
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm" id="{{ $material['type'] }}PurchasingStatus">
                                                    <option value="belum" {{ $material['status'] === 'belum' ? 'selected' : '' }}>Belum Info</option>
                                                    <option value="proses" {{ $material['status'] === 'proses' ? 'selected' : '' }}>Masih Proses Kedatangan</option>
                                                    <option value="sudah" {{ $material['status'] === 'sudah' ? 'selected' : '' }}>Sudah Ready</option>
                                                    <option value="tidak_ada" {{ $material['status'] === 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                                </select>
                                                <button type="button" class="btn btn-sm btn-primary mt-1" onclick="updatePurchasingInfo('{{ $material['type'] }}')">
                                                    <i class="mdi mdi-check"></i> Update
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($materials) > 0)
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" id="saveAllPurchasingInfoBtn">
                                <i class="mdi mdi-content-save"></i> Simpan Semua Info Purchasing
                            </button>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <h6><i class="mdi mdi-information"></i> Tidak Ada Material Khusus</h6>
                            <p class="mb-0">Job ini tidak menggunakan material khusus, langsung dapat dijadwalkan untuk produksi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Scheduling Development -->
        {{-- <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="mdi mdi-calendar-clock"></i> Scheduling Development</h6>
                    <div class="alert alert-info mb-3">
                        <small>
                            <strong>Penjelasan:</strong> Development schedule berdasarkan lead time yang sudah dikonfigurasi di atas.<br>
                            <strong>Rumus:</strong> 14 hari (development) + MAX(lead time material yang digunakan)<br>
                            <strong>Contoh:</strong> Jika job menggunakan kertas baru (30 hari) + develop foil (7 hari) = 14 + MAX(30, 7) = 44 hari<br>
                            <strong>Alasan:</strong> Material bisa dipersiapkan bersamaan, jadi ambil yang terpanjang.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Development Days:</label>
                                <input type="number" class="form-control" id="developmentDays" value="14" readonly>
                                <small class="text-muted">Waktu development tetap</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max Lead Time Material:</label>
                                <input type="number" class="form-control" id="maxLeadTimeDays" readonly>
                                <small class="text-muted">Lead time terpanjang dari material yang digunakan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total Estimated Days:</label>
                                <input type="number" class="form-control" id="totalEstimatedDays"
                                       value="{{ $job->schedulingDevelopment ? $job->schedulingDevelopment->total_estimated_days : 14 }}" readonly>
                                <small class="text-muted">Development + Max Lead Time</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Proses Produksi:</label>
                                <input type="text" class="form-control" id="produksiTimeDisplay" readonly>
                                <small class="text-muted">Estimasi waktu produksi</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PPIC Notes:</label>
                                <textarea class="form-control" id="ppicNotes" rows="3"
                                          placeholder="Catatan PPIC">{{ $job->schedulingDevelopment ? $job->schedulingDevelopment->ppic_notes : '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchasing Notes:</label>
                                <textarea class="form-control" id="purchasingNotes" rows="3"
                                          placeholder="Catatan Purchasing">{{ $job->schedulingDevelopment ? $job->schedulingDevelopment->purchasing_notes : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        @if(!$job->schedulingDevelopment)
                            <div class="alert alert-warning mb-3">
                                <i class="mdi mdi-alert"></i> Scheduling Development belum dibuat. Silakan isi form di atas dan klik "Simpan Scheduling" untuk membuat jadwal development.
                            </div>
                        @endif
                        <button type="button" class="btn btn-info" id="saveSchedulingBtn">
                            <i class="mdi mdi-content-save"></i> Simpan Scheduling
                        </button>
                    </div>
                </div>
            </div>
        </div> --}}


        <!-- Existing Production Schedules -->
        @if($job->productionSchedules && $job->productionSchedules->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="mdi mdi-calendar-multiple"></i> Existing Production Schedules</h6>

                    @foreach($job->productionSchedules as $schedule)
                        <div class="production-schedule-card">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Tanggal & Waktu:</strong><br>
                                    {{ $schedule->production_date_time }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Mesin:</strong><br>
                                    {{ $schedule->machine_name }} ({{ $schedule->machine_code }})
                                </div>
                                <div class="col-md-2">
                                    <strong>Status:</strong><br>
                                    <span class="badge {{ $schedule->status_color_class }}">{{ $schedule->status_label }}</span>
                                </div>
                                <div class="col-md-2">
                                    <strong>Created By:</strong><br>
                                    {{ $schedule->createdBy ? $schedule->createdBy->name : '-' }}
                                </div>
                                <div class="col-md-2">
                                    <div class="btn-group" role="group">
                                        @if($schedule->status === 'scheduled')
                                            <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editSchedule({{ $schedule->id }})" title="Edit Schedule">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </button>
                                        @endif
                                        @if($schedule->status === 'scheduled' || $schedule->status === 'ready')
                                            <button type="button" class="btn btn-sm btn-success"
                                                    onclick="updateScheduleStatus({{ $schedule->id }}, 'completed')">
                                                Selesai
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="updateScheduleStatus({{ $schedule->id }}, 'cancelled')">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($schedule->production_notes || $schedule->quality_notes)
                                <div class="row mt-2">
                                    @if($schedule->production_notes)
                                        <div class="col-md-6">
                                            <strong>Production Notes:</strong><br>
                                            <small>{{ $schedule->production_notes }}</small>
                                        </div>
                                    @endif
                                    @if($schedule->quality_notes)
                                        <div class="col-md-6">
                                            <strong>Quality Notes:</strong><br>
                                            <small>{{ $schedule->quality_notes }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Edit Production Schedule -->
        <div class="modal fade" id="editScheduleModal" tabindex="-1" role="dialog" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white" id="editScheduleModalLabel">
                            <i class="mdi mdi-pencil"></i> Edit Production Schedule
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editScheduleForm">
                            @csrf
                            <input type="hidden" id="editScheduleId" name="schedule_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Produksi:</label>
                                        <input type="date" class="form-control" id="editProductionDate" name="production_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Waktu Produksi:</label>
                                        <input type="time" class="form-control" id="editProductionTime" name="production_time" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pilih Mesin:</label>
                                        <select class="form-control" id="editMachineCode" name="machine_code" required>
                                            <option value="">Pilih Mesin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status:</label>
                                        <select class="form-control" id="editProductionStatus" name="production_status" required>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="ready">Ready</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Production Notes:</label>
                                        <textarea class="form-control" id="editProductionNotes" name="production_notes" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Quality Notes:</label>
                                        <textarea class="form-control" id="editQualityNotes" name="quality_notes" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="button" class="btn btn-warning" id="saveEditScheduleBtn">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            $(document).ready(function() {
                // Load available machines when date/time changes
                // $('#productionDate, #productionTime').on('change', function() {
                //     loadAvailableMachines();
                // });

                // Initial load
                loadAvailableMachines();

                // Form submission
                $('#productionScheduleForm').on('submit', function(e) {
                    e.preventDefault();
                    createProductionSchedule();
                });

                // Scheduling Development Functions
                $('#saveSchedulingBtn').click(function() {
                    saveScheduling();
                });

                // Auto calculate lead time and total estimated days
                $('#tintaMaterialDays, #kertasBaruDays, #foilDays, #toolingDays, #produksiHours').on('input', function() {
                    calculateLeadTime();
                    calculateTotalDays();
                });

                // Initial calculation
                calculateLeadTime();
                calculateTotalDays();

                // Lead Time Configuration Functions
                $('#saveLeadTimeBtn').click(function() {
                    saveLeadTimeConfiguration();
                });

                // Purchasing Info Functions
                $('#saveAllPurchasingInfoBtn').click(function() {
                    saveAllPurchasingInfo();
                });

            // Start countdown timer hanya jika job sudah dijadwalkan
            @if($isScheduled)
                startCountdownTimer();
            @endif

            // Update process status based on production schedules
            updateProcessStatus();
            
            // Add hover effects for process table rows
            $('.process-table tbody tr').hover(
                function() {
                    $(this).addClass('table-active');
                },
                function() {
                    $(this).removeClass('table-active');
                }
            );

            // Handle process schedule button clicks
            $('.process-schedule-btn').click(function() {
                var processName = $(this).data('process');
                var processIndex = $(this).data('process-index');
                
                // Set modal data
                $('#selected-process').val(processName);
                $('#selected-process-index').val(processIndex);
                $('#modal-process-name').text(processName);
                
                // Set estimated time based on process
                var estimatedTime = getEstimatedTime(processName);
                $('#modal-process-time').text(estimatedTime);
                $('#estimated-duration').val(parseFloat(estimatedTime.replace(' jam', '')));
                
                // Set description
                var description = getProcessDescription(processName);
                $('#modal-process-description').text(description);
                
                // Set default date to today
                $('#schedule-date').val(new Date().toISOString().split('T')[0]);
                
                // Populate dependencies dropdown
                populateDependencies(processName);
                
                // Show modal
                $('#productionScheduleModal').modal('show');
            });

            // Handle save schedule button
            $('#save-schedule-btn').click(function() {
                saveProductionSchedule();
            });
            });

            function loadAvailableMachines() {
                $.ajax({
                    url: '{{ route('development.available-machines') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            displayMachines(response.data);
                        }
                    },
                    error: function(xhr) {
                        $('#machineCode').html('<option value="">Error loading machines</option>');
                    }
                });
            }

            function displayMachines(machines) {
                var html = '<option value="">Pilih Mesin</option>';

                if (machines.length === 0) {
                    html = '<option value="">Tidak ada mesin yang tersedia</option>';
                } else {
                    machines.forEach(function(machine) {
                        html += '<option value="' + machine.code + '" data-machine-name="' + machine.name + '" data-capacity="' + machine.capacity + '">';
                        html += machine.name + ' (' + machine.code + ') - ' + machine.capacity;
                        html += '</option>';
                    });
                }

                $('#machineCode').html(html);
            }

            function createProductionSchedule() {
                var selectedMachineCode = $('#machineCode').val();
                if (!selectedMachineCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Mesin',
                        text: 'Silakan pilih mesin terlebih dahulu'
                    });
                    return;
                }

                var selectedOption = $('#machineCode option:selected');
                var machineName = selectedOption.data('machine-name');
                var capacity = selectedOption.data('capacity');

                var formData = {
                    _token: '{{ csrf_token() }}',
                    production_date: $('#productionDate').val(),
                    production_time: $('#productionTime').val(),
                    machine_name: machineName,
                    machine_code: selectedMachineCode,
                    production_notes: $('#productionNotes').val(),
                    quality_notes: $('#qualityNotes').val(),
                    production_status: $('#productionStatus').val()
                };

                Swal.fire({
                    title: 'Membuat Production Schedule...',
                    text: 'Sedang menyimpan jadwal produksi',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.production-schedule.store', $job->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Production schedule berhasil dibuat',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var message = 'Terjadi kesalahan saat membuat production schedule';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message
                        });
                    }
                });
            }

            function updateScheduleStatus(scheduleId, newStatus) {
                var statusText = {
                    'in_progress': 'Start Production',
                    'completed': 'Complete Production',
                    'cancelled': 'Cancel Production'
                };

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin ' + statusText[newStatus] + '?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: newStatus === 'cancelled' ? '#dc3545' : '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, ' + statusText[newStatus],
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('development.production-schedule.update-status', [$job->id, '']) }}/' + scheduleId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: newStatus
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Status production schedule berhasil diupdate',
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
                                    text: 'Terjadi kesalahan saat mengupdate status'
                                });
                            }
                        });
                    }
                });
            }

            // Function untuk save Lead Time Configuration
            function saveLeadTimeConfiguration() {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    tinta_material_days: $('#tintaMaterialDays').val(),
                    kertas_baru_days: $('#kertasBaruDays').val(),
                    foil_days: $('#foilDays').val(),
                    tooling_days: $('#toolingDays').val(),
                    produksi_hours: $('#produksiHours').val(),
                    total_lead_time_days: $('#totalLeadTimeDays').val()
                };

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan Lead Time Configuration',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.lead-time.store', $job->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Lead Time Configuration berhasil disimpan',
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
                            text: 'Terjadi kesalahan saat menyimpan Lead Time Configuration'
                        });
                    }
                });
            }

            // Function untuk save Scheduling
            function saveScheduling() {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    development_days: $('#developmentDays').val(),
                    max_lead_time_days: $('#maxLeadTimeDays').val(),
                    total_estimated_days: $('#totalEstimatedDays').val(),
                    produksi_hours: $('#produksiHours').val(),
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

            // Function untuk calculate lead time
            function calculateLeadTime() {
                var tintaMaterialDays = parseInt($('#tintaMaterialDays').val()) || 0;
                var kertasBaruDays = parseInt($('#kertasBaruDays').val()) || 0;
                var foilDays = parseInt($('#foilDays').val()) || 0;
                var toolingDays = parseInt($('#toolingDays').val()) || 0;
                var produksiHours = parseFloat($('#produksiHours').val()) || 0;

                // Cek material mana yang digunakan berdasarkan job
                var usedMaterials = [];
                var materialNames = [];

                @if($job->tinta_khusus)
                    usedMaterials.push(tintaMaterialDays);
                    materialNames.push('Tinta & Material (' + tintaMaterialDays + ' hari)');
                @endif
                @if($job->kertas_khusus)
                    usedMaterials.push(kertasBaruDays);
                    materialNames.push('Kertas Baru (' + kertasBaruDays + ' hari)');
                @endif
                @if($job->foil_khusus)
                    usedMaterials.push(foilDays);
                    materialNames.push('Foil (' + foilDays + ' hari)');
                @endif
                @if($job->pale_tooling_khusus)
                    usedMaterials.push(toolingDays);
                    materialNames.push('Tooling (' + toolingDays + ' hari)');
                @endif

                // Jika tidak ada material khusus, gunakan default 0
                var maxLeadTime = 0;
                if (usedMaterials.length > 0) {
                    maxLeadTime = Math.max(...usedMaterials);
                }

                $('#maxLeadTimeDays').val(maxLeadTime);
                $('#totalLeadTimeDays').val(maxLeadTime + 14);

                // Display produksi time
                $('#produksiTimeDisplay').val(produksiHours + ' jam');

                // Debug info (bisa dihapus nanti)
                console.log('Material yang digunakan:', materialNames);
                console.log('Lead time per material:', usedMaterials);
                console.log('Max lead time:', maxLeadTime);
            }

            // Function untuk calculate total days
            function calculateTotalDays() {
                var developmentDays = 14; // Development time
                var maxLeadTime = parseInt($('#maxLeadTimeDays').val()) || 0;

                // Total = Development time + Max lead time material
                var totalDays = developmentDays + maxLeadTime;
                $('#totalEstimatedDays').val(totalDays);
            }

            // Function untuk update purchasing info per material
            function updatePurchasingInfo(materialType) {
                var status = $('#' + materialType + 'PurchasingStatus').val();
                var info = $('#' + materialType + 'PurchasingInfo').val();

                if ((status === 'sudah' || status === 'proses') && !info.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Jika status "Sudah Ready" atau "Masih Proses Kedatangan", mohon isi catatan info purchasing-nya'
                    });
                    return;
                }

                var formData = {
                    _token: '{{ csrf_token() }}',
                    material_type: materialType,
                    purchasing_status: status,
                    purchasing_info: info
                };

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan info purchasing ' + materialType,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.update-purchasing-info', $job->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Info purchasing ' + materialType + ' berhasil disimpan',
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
                            text: 'Terjadi kesalahan saat menyimpan info purchasing'
                        });
                    }
                });
            }

            // Function untuk save semua purchasing info
            function saveAllPurchasingInfo() {
                var materials = ['kertas', 'tinta', 'foil', 'pale_tooling'];
                var purchasingData = {};
                var hasData = false;

                materials.forEach(function(material) {
                    if ($('#' + material + 'PurchasingStatus').length > 0) {
                        var status = $('#' + material + 'PurchasingStatus').val();
                        var info = $('#' + material + 'PurchasingInfo').val();

                        if ((status === 'sudah' || status === 'proses') && !info.trim()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian!',
                                text: 'Material ' + material + ' status "' + (status === 'sudah' ? 'Sudah Ready' : 'Masih Proses Kedatangan') + '" tapi catatan kosong. Mohon isi catatan info purchasing-nya'
                            });
                            return;
                        }

                        purchasingData[material] = {
                            status: status,
                            info: info
                        };
                        hasData = true;
                    }
                });

                if (!hasData) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: 'Tidak ada material khusus yang perlu diupdate'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan semua info purchasing',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.update-all-purchasing-info', $job->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        purchasing_data: purchasingData
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Semua info purchasing berhasil disimpan',
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
                            text: 'Terjadi kesalahan saat menyimpan info purchasing'
                        });
                    }
                });
            }

            // Edit Production Schedule Functions
            function editSchedule(scheduleId) {
                // Get schedule data via AJAX
                $.ajax({
                    url: '{{ route('development.production-schedule.show', ['id' => $job->id, 'scheduleId' => ':scheduleId']) }}'.replace(':scheduleId', scheduleId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var schedule = response.data;

                            // Fill form with current data
                            $('#editScheduleId').val(schedule.id);
                            $('#editProductionDate').val(schedule.production_date);
                            $('#editProductionTime').val(schedule.production_time);
                            $('#editProductionNotes').val(schedule.production_notes || '');
                            $('#editQualityNotes').val(schedule.quality_notes || '');
                            $('#editProductionStatus').val(schedule.status);

                            // Load machines and select current machine
                            loadMachinesForEdit(schedule.machine_code);

                            // Show modal
                            $('#editScheduleModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengambil data schedule'
                        });
                    }
                });
            }

            function loadMachinesForEdit(selectedMachineCode) {
                $.ajax({
                    url: '{{ route('development.get-machines') }}',
                    type: 'GET',
                    data: {
                        production_date: $('#editProductionDate').val(),
                        production_time: $('#editProductionTime').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            displayEditMachines(response.data, selectedMachineCode);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading machines:', xhr);
                    }
                });
            }

            function displayEditMachines(machines, selectedMachineCode) {
                var html = '<option value="">Pilih Mesin</option>';

                if (machines.length === 0) {
                    html = '<option value="">Tidak ada mesin yang tersedia</option>';
                } else {
                    machines.forEach(function(machine) {
                        var selected = machine.code === selectedMachineCode ? 'selected' : '';
                        html += '<option value="' + machine.code + '" data-machine-name="' + machine.name + '" data-capacity="' + machine.capacity + '" ' + selected + '>';
                        html += machine.name + ' (' + machine.code + ') - ' + machine.capacity;
                        html += '</option>';
                    });
                }

                $('#editMachineCode').html(html);
            }

            // Save edited schedule
            $('#saveEditScheduleBtn').click(function() {
                var scheduleId = $('#editScheduleId').val();
                var selectedMachineCode = $('#editMachineCode').val();

                if (!selectedMachineCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Mesin',
                        text: 'Silakan pilih mesin terlebih dahulu'
                    });
                    return;
                }

                var selectedOption = $('#editMachineCode option:selected');
                var machineName = selectedOption.data('machine-name');

                var formData = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    production_date: $('#editProductionDate').val(),
                    production_time: $('#editProductionTime').val(),
                    machine_name: machineName,
                    machine_code: selectedMachineCode,
                    production_notes: $('#editProductionNotes').val(),
                    quality_notes: $('#editQualityNotes').val(),
                    production_status: $('#editProductionStatus').val()
                };

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan perubahan schedule',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.production-schedule.update', ['id' => $job->id, 'scheduleId' => ':scheduleId']) }}'.replace(':scheduleId', scheduleId),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#editScheduleModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Production schedule berhasil diupdate',
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
                            text: 'Terjadi kesalahan saat mengupdate schedule'
                        });
                    }
                });
            });

            // Reload machines when date/time changes in edit modal
            $('#editProductionDate, #editProductionTime').on('change', function() {
                var currentMachineCode = $('#editMachineCode').val();
                loadMachinesForEdit(currentMachineCode);
            });

            // Countdown Timer Function
            function startCountdownTimer() {
                // Get deadline from PHP (berdasarkan lead time yang dikonfigurasi PPIC)
                var deadline = new Date('{{ $deadline->format('Y-m-d H:i:s') }}').getTime();

                // Update countdown every second
                var countdownInterval = setInterval(function() {
                    var now = new Date().getTime();
                    var timeLeft = deadline - now;

                    // Calculate time units
                    var days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                    // Update display
                    $('#countdown-days').text(Math.abs(days));
                    $('#countdown-hours').text(hours);
                    $('#countdown-minutes').text(minutes);
                    $('#countdown-seconds').text(seconds);

                    // Add visual effects based on time left
                    if (timeLeft < 0) {
                        // Overdue
                        $('.countdown-container').css('background', 'linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%)');
                        $('.countdown-number').addClass('text-danger');
                        clearInterval(countdownInterval);
                    } else if (timeLeft < 24 * 60 * 60 * 1000) {
                        // Less than 1 day
                        $('.countdown-container').css('background', 'linear-gradient(135deg, #ffa726 0%, #ff7043 100%)');
                        $('.countdown-number').addClass('text-warning');
                    } else if (timeLeft < 3 * 24 * 60 * 60 * 1000) {
                        // Less than 3 days
                        $('.countdown-container').css('background', 'linear-gradient(135deg, #ffb74d 0%, #ff8a65 100%)');
                    } else {
                        // More than 3 days
                        $('.countdown-container').css('background', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)');
                    }

                    // Add pulse effect for last hour
                    if (timeLeft < 60 * 60 * 1000 && timeLeft > 0) {
                        $('.countdown-box').css('animation', 'pulse 1s infinite');
                    } else {
                        $('.countdown-box').css('animation', 'none');
                    }

                }, 1000);
            }

            // Function untuk update status proses berdasarkan production schedules
            function updateProcessStatus() {
                // Cek apakah ada production schedules
                @if($job->productionSchedules && $job->productionSchedules->count() > 0)
                    // Update hanya button yang sesuai dengan proses yang sudah dijadwalkan
                    @foreach($job->productionSchedules as $schedule)
                        var processName = '{{ $schedule->proses }}';
                        var button = $('.process-schedule-btn[data-process="' + processName + '"]');
                        if (button.length > 0) {
                            button.removeClass('btn-outline-warning').addClass('scheduled');
                            button.html('<i class="mdi mdi-calendar-check"></i> Dijadwalkan');
                        }
                    @endforeach
                @endif
            }

            // Function untuk mendapatkan estimasi waktu berdasarkan proses
            function getEstimatedTime(processName) {
                switch(processName.toUpperCase()) {
                    case 'CETAK':
                        return '2 jam';
                    case 'PLONG':
                        return '1 jam';
                    case 'POTONG':
                        return '1 jam';
                    case 'EMBOSS':
                        return '2 jam';
                    default:
                        return '1 jam';
                }
            }

            // Function untuk mendapatkan deskripsi proses
            function getProcessDescription(processName) {
                switch(processName.toUpperCase()) {
                    case 'CETAK':
                        return 'Proses pencetakan menggunakan mesin cetak offset atau digital';
                    case 'PLONG':
                        return 'Proses pelubangan kertas sesuai dengan desain yang ditentukan';
                    case 'POTONG':
                        return 'Proses pemotongan kertas sesuai ukuran final produk';
                    case 'EMBOSS':
                        return 'Proses pemberian efek timbul pada kertas';
                    default:
                        return 'Proses ' + processName + ' untuk job ' + '{{ $job->job_name }}';
                }
            }

            // Function untuk mengisi dropdown dependencies
            function populateDependencies(selectedProcess) {
                var dependencies = $('#dependencies');
                dependencies.empty();
                dependencies.append('<option value="">Pilih proses yang harus selesai dulu</option>');
                
                // Get all processes except the selected one
                var allProcesses = @json($job->proses);
                allProcesses.forEach(function(process, index) {
                    if (process !== selectedProcess) {
                        dependencies.append('<option value="' + process + '">' + process + '</option>');
                    }
                });
            }

            // Function untuk menyimpan production schedule
            function saveProductionSchedule() {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    process_name: $('#selected-process').val(),
                    process_index: $('#selected-process-index').val(),
                    schedule_date: $('#schedule-date').val(),
                    schedule_time: $('#schedule-time').val(),
                    estimated_duration: $('#estimated-duration').val(),
                    machine_assignment: $('#machine-assignment').val(),
                    schedule_notes: $('#schedule-notes').val()
                };

                // Validation
                if (!formData.schedule_date || !formData.schedule_time || !formData.estimated_duration || !formData.machine_assignment) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Silakan isi semua field yang wajib diisi (Tanggal, Waktu, Durasi, Mesin)'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan jadwal produksi',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX call to save production schedule
                $.ajax({
                    url: '{{ route("development.process-schedule.store", $job->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                // Update button status - Hanya update button yang sesuai
                                var processName = formData.process_name;
                                var button = $('.process-schedule-btn[data-process="' + processName + '"]');
                                if (button.length > 0) {
                                    button.removeClass('btn-outline-warning').addClass('scheduled');
                                    button.html('<i class="mdi mdi-calendar-check"></i> Dijadwalkan');
                                }
                                
                                // Close modal
                                $('#productionScheduleModal').modal('hide');
                                
                                // Reset form
                                $('#productionScheduleForm')[0].reset();
                                
                                // Reload page to show updated data
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Terjadi kesalahan saat menyimpan jadwal'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Terjadi kesalahan saat menyimpan jadwal produksi';
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

            // Add pulse animation for CSS
            var style = document.createElement('style');
            style.textContent = `
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
            `;
            document.head.appendChild(style);
        </script>

        <!-- Modal Production Schedule Form -->
        <div class="modal fade" id="productionScheduleModal" tabindex="-1" role="dialog" aria-labelledby="productionScheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white" id="productionScheduleModalLabel">
                            <i class="mdi mdi-calendar-plus"></i> Production Schedule Form
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="mdi mdi-information"></i> Informasi Proses</h6>
                            <p class="mb-0">
                                <strong>Nama Proses:</strong> <span id="modal-process-name">-</span><br>
                                <strong>Estimasi Waktu:</strong> <span id="modal-process-time">-</span><br>
                                <strong>Keterangan:</strong> <span id="modal-process-description">-</span>
                            </p>
                        </div>

                        <form id="productionScheduleForm">
                            <input type="hidden" id="selected-process" name="process_name">
                            <input type="hidden" id="selected-process-index" name="process_index">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule-date">Tanggal Jadwal:</label>
                                        <input type="date" class="form-control" id="schedule-date" name="schedule_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule-time">Waktu Mulai:</label>
                                        <input type="time" class="form-control" id="schedule-time" name="schedule_time" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estimated-duration">Durasi Estimasi (jam):</label>
                                        <input type="number" class="form-control" id="estimated-duration" name="estimated_duration" min="0.5" step="0.5" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="machine-assignment">Penugasan Mesin:</label>
                                        <select class="form-control" id="machine-assignment" name="machine_assignment">
                                            <option value="">Pilih Mesin</option>
                                            @if(isset($machines) && count($machines) > 0)
                                                @foreach($machines as $machine)
                                                    <option value="{{ $machine->code }}" 
                                                            data-machine-name="{{ $machine->name }}" 
                                                            data-capacity="{{ $machine->capacity }}">
                                                        {{ $machine->name }} ({{ $machine->code }}) - {{ $machine->capacity }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">Tidak ada mesin yang tersedia</option>
                                            @endif
                                        </select>
                                        
                                        <!-- Debug Info -->
                                        <small class="text-muted">
                                            Debug: {{ isset($machines) ? count($machines) : 0 }} mesin tersedia
                                        </small>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="schedule-notes">Catatan Jadwal:</label>
                                <textarea class="form-control" id="schedule-notes" name="schedule_notes" rows="3" placeholder="Catatan khusus untuk jadwal produksi"></textarea>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="save-schedule-btn">
                            <i class="mdi mdi-content-save"></i> Simpan Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

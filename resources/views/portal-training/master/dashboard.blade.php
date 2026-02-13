@extends('main.layouts.main')
@section('title')
    Dashboard Portal Training
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <style>
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-body {
            padding: 25px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-change {
            font-size: 12px;
            margin-top: 5px;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-success-gradient {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .bg-info-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .bg-purple-gradient {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .bg-orange-gradient {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .bg-teal-gradient {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }

        .quick-link-card {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .quick-link-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
            text-decoration: none;
            color: inherit;
        }

        .quick-link-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-right: 15px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        .recent-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .recent-item:hover {
            background-color: #f8f9fa;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .progress-ring {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .progress-ring svg {
            transform: rotate(-90deg);
        }

        .progress-ring-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
        }
    </style>
@endsection
@section('page-title')
    Dashboard Portal Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Dashboard Portal Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Portal Training</li>
                </ol>
            </div>
        </div>



        {{-- Main Content Row --}}
        <div class="row">
            {{-- Left Column: Quick Links & Recent Activities --}}
            <div class="col-lg-4 col-md-12 mb-4">
                {{-- Quick Links --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="mdi mdi-menu mr-2"></i>Menu Cepat
                        </h4>
                        <div class="d-grid gap-2">
                            @if (canAccessTrainingMenu())
                                {{-- Menu untuk Admin/Koordinator Training --}}
                                <a href="{{ route('hr.portal-training.master.training-masters.index') }}"
                                    class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-primary-gradient">
                                            <i class="mdi mdi-book-open-variant"></i>
                                        </div>
                                        <div>
                                            <strong>Master Data Training</strong>
                                            <br>
                                            <small class="text-muted">Kelola data training</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.master.assignments.index') }}"
                                    class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-success-gradient">
                                            <i class="mdi mdi-account-multiple"></i>
                                        </div>
                                        <div>
                                            <strong>Training Assignments</strong>
                                            <br>
                                            <small class="text-muted">Kelola penugasan</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.master.question-banks.index') }}"
                                    class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-info-gradient">
                                            <i class="mdi mdi-help-circle"></i>
                                        </div>
                                        <div>
                                            <strong>Bank Soal</strong>
                                            <br>
                                            <small class="text-muted">Kelola soal pertanyaan</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.master.materials.index') }}" class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-warning-gradient">
                                            <i class="mdi mdi-file-video"></i>
                                        </div>
                                        <div>
                                            <strong>Materi Training</strong>
                                            <br>
                                            <small class="text-muted">Kelola materi & video</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.master.reports.index') }}" class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-danger-gradient">
                                            <i class="mdi mdi-chart-bar"></i>
                                        </div>
                                        <div>
                                            <strong>Report Training</strong>
                                            <br>
                                            <small class="text-muted">Laporan & analitik</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.master.test-google-drive') }}" class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon" style="background: linear-gradient(135deg, #4285F4 0%, #34A853 100%);">
                                            <i class="mdi mdi-google-drive"></i>
                                        </div>
                                        <div>
                                            <strong>Test Google Drive API</strong>
                                            <br>
                                            <small class="text-muted">Cek koneksi Google Drive</small>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('hr.portal-training.scores.index') }}" class="quick-link-card p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="quick-link-icon bg-success-gradient">
                                            <i class="mdi mdi-chart-line"></i>
                                        </div>
                                        <div>
                                            <strong>Nilai Training Saya</strong>
                                            <br>
                                            <small class="text-muted">Lihat nilai training Anda</small>
                                        </div>
                                    </div>
                                </a>
                            @endif

                            {{-- Menu untuk Semua User (Pelaksana Training) --}}
                            <a href="{{ route('hr.portal-training.index') }}" class="quick-link-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="quick-link-icon bg-primary-gradient">
                                        <i class="mdi mdi-play-circle"></i>
                                    </div>
                                    <div>
                                        <strong>Training Saya</strong>
                                        <br>
                                        <small class="text-muted">Lihat training Anda</small>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('hr.portal-training.history') }}" class="quick-link-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="quick-link-icon bg-info-gradient">
                                        <i class="mdi mdi-history"></i>
                                    </div>
                                    <div>
                                        <strong>History Training Saya</strong>
                                        <br>
                                        <small class="text-muted">Lihat history training Anda</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Status Overview --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="mdi mdi-chart-pie mr-2"></i>Status Overview
                        </h4>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Selesai</span>
                                <span class="font-weight-bold">{{ $completedCount }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $totalAssignments > 0 ? ($completedCount / $totalAssignments) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sedang Dikerjakan</span>
                                <span class="font-weight-bold">{{ $inProgressCount }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ $totalAssignments > 0 ? ($inProgressCount / $totalAssignments) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Belum Dimulai</span>
                                <span class="font-weight-bold">{{ $assignedCount }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $totalAssignments > 0 ? ($assignedCount / $totalAssignments) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        @if ($expiredCount > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Expired</span>
                                    <span class="font-weight-bold text-danger">{{ $expiredCount }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: {{ $totalAssignments > 0 ? ($expiredCount / $totalAssignments) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Charts & Recent Activities --}}
            <div class="col-lg-8 col-md-12">

                {{-- Reminder Training Saya --}}
                @if ($myAssignments->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="mdi mdi-bell-ring-outline mr-2 text-primary"></i>Reminder Training Saya
                                    </h4>

                                    @if ($myNotOpenedAssignments->count() > 0)
                                        <div class="alert alert-warning mb-3">
                                            <h5 class="alert-heading">
                                                <i class="mdi mdi-lock mr-2"></i>{{ $myNotOpenedAssignments->count() }}
                                                Training Belum Dibuka
                                            </h5>
                                            <div class="row mt-3">
                                                @foreach ($myNotOpenedAssignments->take(3) as $assignment)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="p-3 bg-light rounded">
                                                            <strong>{{ $assignment->training ? $assignment->training->training_name : 'Training Tidak Ditemukan' }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-calendar mr-1"></i>
                                                                {{ $assignment->assigned_date ? $assignment->assigned_date->format('d M Y') : '-' }}
                                                            </small>
                                                            <br>
                                                            <a href="{{ route('hr.portal-training.index') }}"
                                                                class="btn btn-sm btn-warning mt-2">
                                                                <i class="mdi mdi-play mr-1"></i>Mulai Training
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($myNotOpenedAssignments->count() > 3)
                                                <a href="{{ route('hr.portal-training.index') }}"
                                                    class="btn btn-link p-0 mt-2">
                                                    Lihat semua ({{ $myNotOpenedAssignments->count() }} training)
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($myInProgressAssignments->count() > 0)
                                        <div class="alert alert-info mb-3">
                                            <h5 class="alert-heading">
                                                <i
                                                    class="mdi mdi-play-circle mr-2"></i>{{ $myInProgressAssignments->count() }}
                                                Training Sedang Dikerjakan
                                            </h5>
                                            <div class="row mt-3">
                                                @foreach ($myInProgressAssignments->take(3) as $assignment)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="p-3 bg-light rounded">
                                                            <strong>{{ $assignment->training ? $assignment->training->training_name : 'Training Tidak Ditemukan' }}</strong>
                                                            <br>
                                                            <div class="progress mt-2" style="height: 8px;">
                                                                <div class="progress-bar bg-info" role="progressbar"
                                                                    style="width: {{ number_format($assignment->progress_percentage, 1) }}%">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ number_format($assignment->progress_percentage, 1) }}%
                                                                selesai
                                                            </small>
                                                            <br>
                                                            <a href="{{ route('hr.portal-training.index') }}"
                                                                class="btn btn-sm btn-info mt-2">
                                                                <i class="mdi mdi-arrow-right mr-1"></i>Lanjutkan
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($myInProgressAssignments->count() > 3)
                                                <a href="{{ route('hr.portal-training.index') }}"
                                                    class="btn btn-link p-0 mt-2">
                                                    Lihat semua ({{ $myInProgressAssignments->count() }} training)
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($myUpcomingExpired->count() > 0)
                                        <div class="alert alert-danger mb-3">
                                            <h5 class="alert-heading">
                                                <i class="mdi mdi-alert-circle mr-2"></i>{{ $myUpcomingExpired->count() }}
                                                Training Akan Expired
                                            </h5>
                                            <div class="row mt-3">
                                                @foreach ($myUpcomingExpired->take(3) as $assignment)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="p-3 bg-light rounded">
                                                            <strong>{{ $assignment->training ? $assignment->training->training_name : 'Training Tidak Ditemukan' }}</strong>
                                                            <br>
                                                            <small class="text-danger">
                                                                <i class="mdi mdi-calendar-alert mr-1"></i>
                                                                Deadline:
                                                                {{ $assignment->deadline_date ? $assignment->deadline_date->format('d M Y') : '-' }}
                                                                ({{ $assignment->deadline_date ? $assignment->deadline_date->diffForHumans() : '-' }})
                                                            </small>
                                                            <br>
                                                            <a href="{{ route('hr.portal-training.index') }}"
                                                                class="btn btn-sm btn-danger mt-2">
                                                                <i class="mdi mdi-exclamation mr-1"></i>Segera Selesaikan
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($myUpcomingExpired->count() > 3)
                                                <a href="{{ route('hr.portal-training.index') }}"
                                                    class="btn btn-link p-0 mt-2">
                                                    Lihat semua ({{ $myUpcomingExpired->count() }} training)
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($myNotOpenedAssignments->count() == 0 && $myInProgressAssignments->count() == 0 && $myUpcomingExpired->count() == 0)
                                        <div class="alert alert-success">
                                            <i class="mdi mdi-check-circle mr-2"></i>
                                            <strong>Semua training Anda sudah selesai atau tidak ada training yang perlu
                                                ditindaklanjuti.</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Recent Assignments --}}
                <div class="card ">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="mdi mdi-clock-outline mr-2"></i>Penugasan Terbaru
                        </h4>
                        @if ($recentAssignments->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentAssignments as $assignment)
                                    <div class="recent-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    {{ $assignment->training ? $assignment->training->training_name : 'Training Tidak Ditemukan' }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="mdi mdi-account mr-1"></i>
                                                    {{ $assignment->employee ? $assignment->employee->name : 'Karyawan Tidak Ditemukan' }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="mdi mdi-calendar mr-1"></i>
                                                    {{ $assignment->assigned_date ? $assignment->assigned_date->format('d M Y') : '-' }}
                                                </small>
                                            </div>
                                            <div>
                                                @if ($assignment->status == 'completed')
                                                    <span class="status-badge badge badge-success">Selesai</span>
                                                @elseif($assignment->status == 'in_progress')
                                                    <span class="status-badge badge badge-info">Sedang Dikerjakan</span>
                                                @elseif($assignment->status == 'assigned')
                                                    <span class="status-badge badge badge-warning">Belum Dimulai</span>
                                                @else
                                                    <span class="status-badge badge badge-danger">Expired</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">Belum ada penugasan</p>
                        @endif
                    </div>
                </div>

                {{-- Additional Info Cards --}}
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">
                                    <i class="mdi mdi-chart-line mr-2"></i>Statistik Session
                                </h4>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-value" style="font-size: 28px;">
                                            {{ number_format($passedSessions) }}
                                        </div>
                                        <div class="stat-label">Lulus</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-value" style="font-size: 28px;">
                                            {{ number_format($failedSessions) }}
                                        </div>
                                        <div class="stat-label">Tidak Lulus</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-value" style="font-size: 28px;">
                                            {{ number_format($inProgressSessions) }}
                                        </div>
                                        <div class="stat-label">Sedang Dikerjakan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">
                                    <i class="mdi mdi-alert mr-2"></i>Peringatan & Notifikasi
                                </h4>
                                <div class="list-group list-group-flush">
                                    @if ($upcomingExpired > 0)
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-alert-circle text-warning mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>{{ $upcomingExpired }} Training</strong> akan expired dalam 7
                                                    hari
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($notOpenedCount > 0)
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-lock text-info mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>{{ $notOpenedCount }} Training</strong> belum dibuka
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($expiredCount > 0)
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-alert-octagon text-danger mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>{{ $expiredCount }} Training</strong> sudah expired
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($upcomingExpired == 0 && $notOpenedCount == 0 && $expiredCount == 0)
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-check-circle text-success mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>Tidak ada peringatan</strong> - Semua training berjalan dengan
                                                    baik
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Sedang Dikerjakan', 'Belum Dimulai', 'Expired'],
                    datasets: [{
                        data: [
                            {{ $completedCount }},
                            {{ $inProgressCount }},
                            {{ $assignedCount }},
                            {{ $expiredCount }}
                        ],
                        backgroundColor: [
                            '#11998e',
                            '#4facfe',
                            '#f5576c',
                            '#fa709a'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const total = {{ $totalAssignments }};
                                    const value = context.parsed;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    label += value + ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endsection

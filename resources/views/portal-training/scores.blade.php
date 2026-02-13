@extends('main.layouts.main')
@section('title')
    Nilai Training - {{ $assignment->training->training_name ?? 'Training' }}
@endsection
@section('css')
    <style>
        .score-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .score-card.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .score-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        .score-card.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        .score-value {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
        }
        .stat-box .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-box .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .session-score-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .session-score-card.passed {
            border-left-color: #28a745;
        }
        .session-score-card.failed {
            border-left-color: #dc3545;
        }
        .session-score-card.in-progress {
            border-left-color: #ffc107;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-passed {
            background: #d4edda;
            color: #155724;
        }
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-in-progress {
            background: #fff3cd;
            color: #856404;
        }
        .status-not-started {
            background: #e2e3e5;
            color: #383d41;
        }
    </style>
@endsection
@section('page-title')
    Nilai Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Nilai Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item active">Nilai Training</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Training Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-book-open-variant mr-2"></i>
                        {{ $assignment->training->training_name ?? 'Training' }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Status Assignment:</strong>
                                @if($assignment->status === 'completed')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($assignment->status === 'in_progress')
                                    <span class="badge badge-warning">Sedang Dikerjakan</span>
                                @elseif($assignment->status === 'assigned')
                                    <span class="badge badge-info">Ditetapkan</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($assignment->status) }}</span>
                                @endif
                            </p>
                            <p class="mb-2">
                                <strong>Tanggal Ditetapkan:</strong>
                                {{ $assignment->assigned_date ? $assignment->assigned_date->format('d M Y') : '-' }}
                            </p>
                            @if($assignment->deadline_date)
                            <p class="mb-2">
                                <strong>Deadline:</strong>
                                {{ $assignment->deadline_date->format('d M Y') }}
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Progress:</strong>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $assignment->progress_percentage ?? 0 }}%"
                                         aria-valuenow="{{ $assignment->progress_percentage ?? 0 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($assignment->progress_percentage ?? 0, 1) }}%
                                    </div>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">{{ $completedSessions }}/{{ $totalSessions }}</div>
                        <div class="stat-label">Sesi Selesai</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value text-success">{{ $passedSessions }}</div>
                        <div class="stat-label">Sesi Lulus</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value text-danger">{{ $failedSessions }}</div>
                        <div class="stat-label">Sesi Gagal</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value text-primary">{{ number_format($averageScore, 1) }}</div>
                        <div class="stat-label">Rata-rata Nilai</div>
                    </div>
                </div>
            </div>

            <!-- Overall Score Card -->
            @if($completedSessions > 0)
            <div class="row mb-4">
                <div class="col-12">
                    @php
                        $overallPercentage = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
                        $cardClass = $overallPercentage >= 70 ? 'success' : ($overallPercentage >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="score-card {{ $cardClass }}">
                        <h5 class="mb-3">
                            <i class="mdi mdi-chart-line mr-2"></i>
                            Nilai Keseluruhan
                        </h5>
                        <div class="score-value">{{ number_format($overallPercentage, 1) }}%</div>
                        <p class="mb-0">
                            Total Skor: {{ number_format($totalScore, 1) }} / {{ number_format($maxPossibleScore, 1) }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Session Scores Detail -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-format-list-bulleted mr-2"></i>
                        Detail Nilai per Sesi
                    </h4>
                </div>
                <div class="card-body">
                    @if($sessionProgresses->isEmpty())
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Belum ada sesi yang dikerjakan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sesi</th>
                                        <th>Judul Sesi</th>
                                        <th>Status</th>
                                        <th>Nilai</th>
                                        <th>Benar/Total</th>
                                        <th>Nilai Min. Lulus</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessionProgresses as $progress)
                                        @php
                                            $session = $progress->session;
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($progress->status) {
                                                case 'passed':
                                                    $statusClass = 'status-passed';
                                                    $statusText = 'Lulus';
                                                    break;
                                                case 'failed':
                                                    $statusClass = 'status-failed';
                                                    $statusText = 'Gagal';
                                                    break;
                                                case 'completed':
                                                    $statusClass = 'status-completed';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'in_progress':
                                                    $statusClass = 'status-in-progress';
                                                    $statusText = 'Sedang Dikerjakan';
                                                    break;
                                                default:
                                                    $statusClass = 'status-not-started';
                                                    $statusText = 'Belum Dimulai';
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>Sesi {{ $session->session_order ?? '-' }}</strong>
                                            </td>
                                            <td>{{ $session->session_title ?? '-' }}</td>
                                            <td>
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($progress->score !== null)
                                                    <strong>{{ number_format($progress->score, 1) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress->total_questions > 0)
                                                    {{ $progress->correct_answers_count ?? 0 }} / {{ $progress->total_questions }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->passing_score)
                                                    {{ number_format($session->passing_score, 1) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress->started_at)
                                                    {{ $progress->started_at->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress->completed_at)
                                                    {{ $progress->completed_at->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('hr.portal-training.scores.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left mr-2"></i>
                        Kembali ke Daftar Nilai
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


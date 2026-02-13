@extends('main.layouts.main')
@section('title')
    Nilai Training
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .assignment-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
        }
        .assignment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-assigned { background: #e3f2fd; color: #1976d2; }
        .status-in-progress { background: #fff3e0; color: #f57c00; }
        .status-completed { background: #e8f5e9; color: #388e3c; }
        .status-expired { background: #ffebee; color: #d32f2f; }
        .score-badge {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
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
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-chart-line mr-2"></i>
                        Daftar Nilai Training
                    </h4>
                </div>
                <div class="card-body">
                    @if($assignments->isEmpty())
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Belum ada training yang memiliki nilai. Silakan selesaikan training terlebih dahulu.
                        </div>
                    @else
                        <div class="row">
                            @foreach($assignments as $assignment)
                                @php
                                    // Hitung statistik untuk assignment ini
                                    $sessionProgresses = $assignment->sessionProgress->where('employee_id', Auth::id());
                                    $totalSessions = $assignment->training->sessions()->active()->count();
                                    $completedSessions = $sessionProgresses->whereIn('status', [
                                        \App\Models\TrainingSessionProgress::STATUS_PASSED,
                                        \App\Models\TrainingSessionProgress::STATUS_COMPLETED,
                                        \App\Models\TrainingSessionProgress::STATUS_FAILED
                                    ])->count();
                                    $passedSessions = $sessionProgresses->where('status', \App\Models\TrainingSessionProgress::STATUS_PASSED)->count();
                                    $totalScore = $sessionProgresses->sum('score');
                                    $maxPossibleScore = 0;
                                    foreach ($sessionProgresses as $sp) {
                                        if ($sp->questions_data && is_array($sp->questions_data)) {
                                            foreach ($sp->questions_data as $q) {
                                                $maxPossibleScore += $q['score'] ?? 0;
                                            }
                                        }
                                    }
                                    $averageScore = $sessionProgresses->where('score', '>', 0)->count() > 0 
                                        ? $sessionProgresses->where('score', '>', 0)->avg('score') 
                                        : 0;
                                    $overallPercentage = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
                                @endphp
                                <div class="col-md-6">
                                    <div class="card assignment-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0">{{ $assignment->training->training_name ?? 'Training Assignment' }}</h5>
                                                <span class="status-badge status-{{ $assignment->status }}">
                                                    @if($assignment->status == 'assigned')
                                                        Ditetapkan
                                                    @elseif($assignment->status == 'in_progress')
                                                        Sedang Dikerjakan
                                                    @elseif($assignment->status == 'completed')
                                                        Selesai
                                                    @else
                                                        Expired
                                                    @endif
                                                </span>
                                            </div>

                                            <p class="text-muted mb-2">
                                                <i class="mdi mdi-calendar mr-1"></i>
                                                Ditetapkan: {{ $assignment->assigned_date->format('d M Y') }}
                                            </p>

                                            @if($completedSessions > 0)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span><strong>Nilai Keseluruhan:</strong></span>
                                                        <span class="score-badge">{{ number_format($overallPercentage, 1) }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar {{ $overallPercentage >= 70 ? 'bg-success' : ($overallPercentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $overallPercentage }}%"
                                                             aria-valuenow="{{ $overallPercentage }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        Total: {{ number_format($totalScore, 1) }} / {{ number_format($maxPossibleScore, 1) }}
                                                    </small>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0 text-success">{{ $passedSessions }}</div>
                                                            <small class="text-muted">Sesi Lulus</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $completedSessions }}/{{ $totalSessions }}</div>
                                                            <small class="text-muted">Sesi Selesai</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-3">
                                                    <i class="mdi mdi-information mr-2"></i>
                                                    Belum ada sesi yang selesai.
                                                </div>
                                            @endif

                                            <div class="text-center">
                                                <a href="{{ route('hr.portal-training.scores.show', $assignment->id) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-eye mr-1"></i>
                                                    Lihat Detail Nilai
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('hr.portal-training.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left mr-2"></i>
                        Kembali ke Portal Training
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
@endsection


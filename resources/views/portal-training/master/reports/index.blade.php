@extends('main.layouts.main')
@section('title')
    Report Training Portal
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .filter-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('page-title')
    Report Training Portal
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Report Training Portal</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.master.assignments.index') }}">Master</a></li>
                <li class="breadcrumb-item active">Report</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Filter Card -->
            <div class="card filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-filter mr-2"></i>
                        Filter Report
                    </h5>
                    <form method="GET" action="{{ route('hr.portal-training.master.reports.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Dari <span class="text-danger">*</span></label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Sampai <span class="text-danger">*</span></label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Training</label>
                                    <select name="training_id" class="form-control">
                                        <option value="">Semua Training</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" 
                                                    {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                                {{ $training->training_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditetapkan</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Karyawan</label>
                                    <select name="employee_id" class="form-control">
                                        <option value="">Semua Karyawan</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-magnify mr-1"></i>
                                            Filter
                                        </button>
                                        @if(request()->has('date_from') && request()->has('date_to'))
                                            <a href="{{ route('hr.portal-training.master.reports.export', request()->all()) }}"
                                               class="btn btn-success">
                                                <i class="mdi mdi-file-excel mr-1"></i>
                                                Export Excel
                                            </a>
                                        @endif
                                        <a href="{{ route('hr.portal-training.master.reports.index') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh mr-1"></i>
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Table -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-table mr-2"></i>
                        Data Report
                    </h4>
                </div>
                <div class="card-body">
                    @if(!request()->has('date_from') || !request()->has('date_to'))
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Silakan pilih <strong>Tanggal Dari</strong> dan <strong>Tanggal Sampai</strong> untuk menampilkan data report.
                        </div>
                    @elseif($assignments->isEmpty())
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert mr-2"></i>
                            Tidak ada data training assignment pada periode yang dipilih.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Assign</th>
                                        <th>Nama Training</th>
                                        <th>Nama Karyawan</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Total Sesi</th>
                                        <th>Sesi Selesai</th>
                                        <th>Sesi Lulus</th>
                                        <th>Sesi Gagal</th>
                                        <th>Total Nilai</th>
                                        <th>Rata-rata Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $index => $assignment)
                                        @php
                                            $sessionProgresses = $assignment->sessionProgress;
                                            $totalSessions = $assignment->training->sessions()->active()->count();
                                            $completedSessions = $sessionProgresses->whereIn('status', [
                                                \App\Models\TrainingSessionProgress::STATUS_PASSED,
                                                \App\Models\TrainingSessionProgress::STATUS_COMPLETED,
                                                \App\Models\TrainingSessionProgress::STATUS_FAILED
                                            ])->count();
                                            $passedSessions = $sessionProgresses->where('status', \App\Models\TrainingSessionProgress::STATUS_PASSED)->count();
                                            $failedSessions = $sessionProgresses->where('status', \App\Models\TrainingSessionProgress::STATUS_FAILED)->count();
                                            $totalScore = $sessionProgresses->sum('score');
                                            $averageScore = $sessionProgresses->where('score', '>', 0)->count() > 0 
                                                ? $sessionProgresses->where('score', '>', 0)->avg('score') 
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                            <td>{{ $assignment->training->training_name ?? '-' }}</td>
                                            <td>{{ $assignment->employee->name ?? '-' }}</td>
                                            <td>
                                                @if($assignment->status == 'completed')
                                                    <span class="badge badge-success">Selesai</span>
                                                @elseif($assignment->status == 'in_progress')
                                                    <span class="badge badge-warning">Sedang Dikerjakan</span>
                                                @elseif($assignment->status == 'assigned')
                                                    <span class="badge badge-info">Ditetapkan</span>
                                                @else
                                                    <span class="badge badge-danger">Expired</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $assignment->progress_percentage ?? 0 }}%"
                                                         aria-valuenow="{{ $assignment->progress_percentage ?? 0 }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($assignment->progress_percentage ?? 0, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $totalSessions }}</td>
                                            <td>{{ $completedSessions }}</td>
                                            <td><span class="text-success">{{ $passedSessions }}</span></td>
                                            <td><span class="text-danger">{{ $failedSessions }}</span></td>
                                            <td>{{ number_format($totalScore, 2) }}</td>
                                            <td>{{ number_format($averageScore, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if(request()->has('date_from') && request()->has('date_to') && $assignments->isNotEmpty())
                $('#reportTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 25,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    }
                });
            @endif
        });
    </script>
@endsection


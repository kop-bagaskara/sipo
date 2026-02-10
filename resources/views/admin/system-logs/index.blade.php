@extends('main.layouts.main')

@section('title', 'System Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> System Logs
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.system-logs.export', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $statistics['total_logs'] }}</h3>
                                    <p>Total Logs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $statistics['today_logs'] }}</h3>
                                    <p>Hari Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $statistics['this_week_logs'] }}</h3>
                                    <p>Minggu Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $statistics['this_month_logs'] }}</h3>
                                    <p>Bulan Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.system-logs.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="log_type" class="form-control form-control-sm">
                                    <option value="">Semua Log Type</option>
                                    @foreach($logTypes as $logType)
                                        <option value="{{ $logType }}" {{ $filters['log_type'] == $logType ? 'selected' : '' }}>
                                            {{ ucfirst($logType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="action_type" class="form-control form-control-sm">
                                    <option value="">Semua Action</option>
                                    @foreach($actionTypes as $actionType)
                                        <option value="{{ $actionType }}" {{ $filters['action_type'] == $actionType ? 'selected' : '' }}>
                                            {{ ucfirst($actionType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="table_name" class="form-control form-control-sm">
                                    <option value="">Semua Table</option>
                                    @foreach($tableNames as $tableName)
                                        <option value="{{ $tableName }}" {{ $filters['table_name'] == $tableName ? 'selected' : '' }}>
                                            {{ $tableName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="user_id" class="form-control form-control-sm">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $filters['user_id'] == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="start_date" class="form-control form-control-sm" 
                                       value="{{ $filters['start_date'] }}" placeholder="Tanggal Mulai">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="end_date" class="form-control form-control-sm" 
                                       value="{{ $filters['end_date'] }}" placeholder="Tanggal Akhir">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <input type="text" name="record_identifier" class="form-control form-control-sm" 
                                       value="{{ $filters['record_identifier'] }}" placeholder="Job Code / Record ID">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       value="{{ $filters['search'] }}" placeholder="Cari dalam deskripsi...">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.system-logs.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Log Type</th>
                                    <th>Action</th>
                                    <th>Table</th>
                                    <th>Record</th>
                                    <th>Description</th>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <small>{{ $log->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $log->log_type_badge_color }}">
                                            {{ ucfirst($log->log_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $log->action_type_badge_color }}">
                                            {{ ucfirst($log->action_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code>{{ $log->table_name }}</code>
                                    </td>
                                    <td>
                                        @if($log->record_identifier)
                                            <strong>{{ $log->record_identifier }}</strong><br>
                                            <small class="text-muted">ID: {{ $log->record_id }}</small>
                                        @else
                                            {{ $log->record_id }}
                                        @endif
                                    </td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            {{ Str::limit($log->description, 100) }}
                                            @if($log->changed_fields)
                                                <br><small class="text-info">
                                                    <i class="fas fa-edit"></i> {{ $log->changed_fields }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->user_name)
                                            <strong>{{ $log->user_name }}</strong>
                                            @if($log->user_jabatan)
                                                <br><small class="text-muted">{{ $log->user_jabatan }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.system-logs.show', $log->id) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada log yang ditemukan</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filter changes
    $('select[name="log_type"], select[name="action_type"], select[name="table_name"], select[name="user_id"]').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush

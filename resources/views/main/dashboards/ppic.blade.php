@extends('main.layouts.main')

@section('title')
    Dashboard PPIC
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
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- List Dashboard Navigation (Left Side) --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary">List Dashboard</h5>
                    <hr>
                    <a href="{{ route('dashboard') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Overview Data
                    </a>
                    <a href="{{ route('dashboard.ppic') }}" class="btn btn-info w-100 text-left mb-2 active">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Dashboard PPIC
                    </a>
                    <a href="{{ route('dashboard.prepress') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Dashboard Prepress
                    </a>
                    <a href="{{ route('dashboard.development') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-flask mr-2"></i>Development Item
                    </a>
                    @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                    <a href="{{ route('dashboard.supplier') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-truck mr-2"></i>Dashboard Supplier
                    </a>
                    @endif
                    @if(auth()->user()->divisi == 11)
                    <a href="{{ route('dashboard.security') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-shield-account mr-2"></i>Dashboard Security
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- PPIC Dashboard Content (Right Side) --}}
        <div class="col-md-9">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Plan Produksi Hari Ini</h4>
                    
                    {{-- Progress Stats --}}
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h3>{{ $totalPlans }}</h3>
                                    <p class="mb-0">Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h3>{{ $completedPlans }}</h3>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h3>{{ $inProgressPlans }}</h3>
                                    <p class="mb-0">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h3>{{ $pendingPlans }}</h3>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h3>{{ $scheduledPlans }}</h3>
                                    <p class="mb-0">Scheduled</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h3>{{ $progressPercentage }}%</h3>
                                    <p class="mb-0">Progress</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Plans List --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Job Order</th>
                                    <th>Mesin</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayPlans as $plan)
                                <tr>
                                    <td>{{ $plan->nomor_job_order }}</td>
                                    <td>{{ $plan->code_machine }}</td>
                                    <td>{{ $plan->start_jam ? \Carbon\Carbon::parse($plan->start_jam)->format('H:i') : '-' }}</td>
                                    <td>{{ $plan->end_jam ? \Carbon\Carbon::parse($plan->end_jam)->format('H:i') : '-' }}</td>
                                    <td>
                                        @if($plan->flag_status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($plan->flag_status == 'in_progress')
                                            <span class="badge bg-warning">In Progress</span>
                                        @elseif($plan->flag_status == 'pending')
                                            <span class="badge bg-info">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Scheduled</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada plan untuk hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

            {{-- Charts --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Machine Utilization</h4>
                    <canvas id="machineUtilChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Weekly Trend</h4>
                    <canvas id="weeklyTrendChart" height="200"></canvas>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Machine Utilization Chart
    @if(isset($machineUtilization) && count($machineUtilization) > 0)
    const machineCtx = document.getElementById('machineUtilChart');
    if (machineCtx) {
        new Chart(machineCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($machineUtilization)->pluck('name')) !!},
                datasets: [{
                    label: 'Utilization (%)',
                    data: {!! json_encode(collect($machineUtilization)->pluck('utilization')) !!},
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
    @endif

    // Weekly Trend Chart
    @if(isset($weeklyTrend) && count($weeklyTrend) > 0)
    const weeklyCtx = document.getElementById('weeklyTrendChart');
    if (weeklyCtx) {
        new Chart(weeklyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($weeklyTrend)->pluck('label')) !!},
                datasets: [
                    {
                        label: 'Completed',
                        data: {!! json_encode(collect($weeklyTrend)->pluck('completed')) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)'
                    },
                    {
                        label: 'In Progress',
                        data: {!! json_encode(collect($weeklyTrend)->pluck('inProgress')) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)'
                    },
                    {
                        label: 'Pending',
                        data: {!! json_encode(collect($weeklyTrend)->pluck('pending')) !!},
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    @endif
});
</script>
@endsection


@extends('main.layouts.main')

@section('title')
    HR Reports Dashboard
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    HR Reports Dashboard
@endsection

@section('body')

    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">HR Reports Dashboard</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>



        <!-- Report Menu -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                                <i class="mdi mdi-account"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">User Report</h5>
                                        <p class="text-muted mb-3">Comprehensive user data report</p>
                                        <a href="{{ route('hr.reports.user-report') }}" class="btn btn-primary">
                                            <i class="mdi mdi-file-document"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                                                <i class="mdi mdi-shield"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Security User Report</h5>
                                        <p class="text-muted mb-3">Security personnel report</p>
                                        <a href="{{ route('hr.reports.security-user-report') }}" class="btn btn-success">
                                            <i class="mdi mdi-file-document"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                                                <i class="mdi mdi-file-document"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Employee Request Report</h5>
                                        <p class="text-muted mb-3">Employee request analysis</p>
                                        <a href="{{ route('hr.reports.request-report') }}" class="btn btn-warning">
                                            <i class="mdi mdi-file-document"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                                                <i class="mdi mdi-school"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Training Report</h5>
                                        <p class="text-muted mb-3">Training program report</p>
                                        <a href="{{ route('hr.reports.training-report') }}" class="btn btn-info">
                                            <i class="mdi mdi-file-document"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-danger text-danger rounded-circle font-size-24">
                                                <i class="mdi mdi-calendar-clock"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Training Schedule Report</h5>
                                        <p class="text-muted mb-3">Training schedule analysis</p>
                                        <a href="{{ route('hr.reports.training-schedule-report') }}"
                                            class="btn btn-danger">
                                            <i class="mdi mdi-file-document"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Reports Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="card-title mb-3">
                                    <i class="mdi mdi-shield-account text-primary"></i> Security Reports
                                </h4>
                                <p class="text-muted mb-4">Laporan terkait aktivitas dan keamanan security</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                                <i class="mdi mdi-truck"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Vehicle Checklist Report</h5>
                                        <p class="text-muted mb-3">Laporan checklist kendaraan</p>
                                        <a href="{{ route('hr.reports.security.vehicle-checklist') }}"
                                            class="btn btn-primary">
                                            <i class="mdi mdi-file-document"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                                                <i class="mdi mdi-package-variant"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Goods Movement Report</h5>
                                        <p class="text-muted mb-3">Laporan keluar/masuk barang</p>
                                        <a href="{{ route('hr.reports.security.goods-movement') }}"
                                            class="btn btn-success">
                                            <i class="mdi mdi-file-document"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div
                                                class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                                                <i class="mdi mdi-clipboard-text"></i>
                                            </div>
                                        </div>
                                        <h5 class="font-size-16 mb-1">Daily Activity Report</h5>
                                        <p class="text-muted mb-3">Laporan aktivitas harian security</p>
                                        <a href="{{ route('hr.reports.security.daily-activity') }}"
                                            class="btn btn-warning">
                                            <i class="mdi mdi-file-document"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Recent Requests</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentRequests as $request)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                        <i class="mdi mdi-file-document"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $request->employee->name ?? 'Unknown' }}</h6>
                                    <p class="text-muted mb-0">{{ $request->requestType->name ?? 'Unknown Type' }}</p>
                                    <small
                                        class="text-muted">{{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                                <div>
                                    <span
                                        class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">No recent requests</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="mdi mdi-school"></i> Recent Trainings</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentTrainings as $schedule)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-soft-info text-info rounded-circle">
                                        <i class="mdi mdi-school"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $schedule->training->title ?? 'Unknown Training' }}</h6>
                                    <p class="text-muted mb-0">
                                        {{ $schedule->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : 'No Date' }}
                                    </p>
                                    <small
                                        class="text-muted">{{ $schedule->created_at ? $schedule->created_at->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                                <div>
                                    <span
                                        class="badge bg-{{ $schedule->status == 'completed' ? 'success' : ($schedule->status == 'cancelled' ? 'danger' : 'info') }}">
                                        {{ ucfirst($schedule->status ?? 'Scheduled') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">No recent trainings</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Any additional JavaScript for the dashboard
        </script>
    @endsection

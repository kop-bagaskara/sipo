@extends('main.layouts.main')

@section('title')
    Security Reports Dashboard
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Security Reports Dashboard
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Security Reports Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Security Reports</li>
            </ol>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                <i class="mdi mdi-truck font-size-24"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ number_format($stats['total_vehicle_checklists']) }}</h4>
                            <p class="text-muted mb-0">Vehicle Checklists</p>
                            <small class="text-success">{{ $stats['today_vehicle_checklists'] }} today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                <i class="mdi mdi-package-variant font-size-24"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ number_format($stats['total_goods_movements']) }}</h4>
                            <p class="text-muted mb-0">Goods Movements</p>
                            <small class="text-success">{{ $stats['today_goods_movements'] }} today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                <i class="mdi mdi-clipboard-text font-size-24"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ number_format($stats['total_daily_activities']) }}</h4>
                            <p class="text-muted mb-0">Daily Activities</p>
                            <small class="text-success">{{ $stats['today_daily_activities'] }} today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title bg-soft-info text-info rounded-circle">
                                <i class="mdi mdi-chart-line font-size-24"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ number_format($stats['total_vehicle_checklists'] + $stats['total_goods_movements'] + $stats['total_daily_activities']) }}</h4>
                            <p class="text-muted mb-0">Total Reports</p>
                            <small class="text-success">{{ $stats['today_vehicle_checklists'] + $stats['today_goods_movements'] + $stats['today_daily_activities'] }} today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Menu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Security Reports</h4>
                    <p class="card-title-desc">Select a report to generate</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                            <i class="mdi mdi-truck"></i>
                                        </div>
                                    </div>
                                    <h5 class="font-size-16 mb-1">Vehicle Checklist Report</h5>
                                    <p class="text-muted mb-3">Laporan checklist kendaraan</p>
                                    <a href="{{ route('hr.reports.security.vehicle-checklist') }}" class="btn btn-primary">
                                        <i class="mdi mdi-file-document"></i> Generate Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                                            <i class="mdi mdi-package-variant"></i>
                                        </div>
                                    </div>
                                    <h5 class="font-size-16 mb-1">Goods Movement Report</h5>
                                    <p class="text-muted mb-3">Laporan keluar/masuk barang</p>
                                    <a href="{{ route('hr.reports.security.goods-movement') }}" class="btn btn-success">
                                        <i class="mdi mdi-file-document"></i> Generate Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                                            <i class="mdi mdi-clipboard-text"></i>
                                        </div>
                                    </div>
                                    <h5 class="font-size-16 mb-1">Daily Activity Report</h5>
                                    <p class="text-muted mb-3">Laporan aktivitas harian security</p>
                                    <a href="{{ route('hr.reports.security.daily-activity') }}" class="btn btn-warning">
                                        <i class="mdi mdi-file-document"></i> Generate Report
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Vehicle Checklists</h5>
                </div>
                <div class="card-body">
                    @forelse($recentVehicleChecklists as $checklist)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="mdi mdi-truck font-size-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $checklist->nama_driver }}</h6>
                                <p class="text-muted mb-0">{{ $checklist->tanggal->format('d M Y') }} - {{ $checklist->shift_formatted }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No recent vehicle checklists</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Goods Movements</h5>
                </div>
                <div class="card-body">
                    @forelse($recentGoodsMovements as $movement)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-success text-success rounded-circle">
                                    <i class="mdi mdi-package-variant font-size-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $movement->nama_pengunjung }}</h6>
                                <p class="text-muted mb-0">{{ $movement->tanggal->format('d M Y') }} - {{ $movement->jenis_movement }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No recent goods movements</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Daily Activities</h5>
                </div>
                <div class="card-body">
                    @forelse($recentDailyActivities as $activity)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                    <i class="mdi mdi-clipboard-text font-size-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $activity->personil_jaga }}</h6>
                                <p class="text-muted mb-0">{{ $activity->tanggal->format('d M Y') }} - {{ $activity->shift_formatted }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No recent daily activities</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

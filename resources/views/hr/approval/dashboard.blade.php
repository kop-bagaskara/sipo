@extends('main.layouts.main')
@section('title')
    Approval Dashboard
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

    <style>
        .cust-col {
            white-space: nowrap;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
@section('page-title')
    Approval Dashboard
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Approval Dashboard</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Approval Dashboard</li>
                </ol>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['employee_requests']['pending'] }}</h4>
                                <p class="card-text">Form Pengajuan</p>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-file-document-outline fs-1"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small>Total: {{ $stats['employee_requests']['total'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['overtime_entries']['pending'] }}</h4>
                                <p class="card-text">Data Lembur</p>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-clock-outline fs-1"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small>Total: {{ $stats['overtime_entries']['total'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['vehicle_requests']['pending'] }}</h4>
                                <p class="card-text">Permintaan Kendaraan</p>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-car fs-1"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small>Total: {{ $stats['vehicle_requests']['total'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['asset_requests']['pending'] }}</h4>
                                <p class="card-text">Permintaan Inventaris</p>
                            </div>
                            <div class="align-self-center">
                                <i class="mdi mdi-package-variant fs-1"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small>Total: {{ $stats['asset_requests']['total'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="row">
            <!-- Form Pengajuan Karyawan -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-file-document-outline text-primary"></i> Form Pengajuan Karyawan
                        </h5>
                        <a href="{{ auth()->user()->isHR() ? route('hr.approval.hr-pending') : route('hr.approval.supervisor-pending') }}"
                           class="btn btn-primary btn-sm">
                            <i class="mdi mdi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($pendingEmployeeRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingEmployeeRequests as $request)
                                            <tr>
                                                <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $request->request_type)) }}</td>
                                                <td>
                                                    <span class="badge badge-warning">Pending</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('hr.approval.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="mdi mdi-check-circle text-success fs-1"></i>
                                <p class="text-muted mt-2">Tidak ada pengajuan yang menunggu approval</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Lembur -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-clock-outline text-warning"></i> Data Lembur
                        </h5>
                        <a href="{{ auth()->user()->isHR() ? route('hr.overtime.hrga-pending') : route('hr.overtime.spv-pending') }}"
                           class="btn btn-warning btn-sm">
                            <i class="mdi mdi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($pendingOvertimeEntries->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>Lokasi</th>
                                            <th>Jam</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingOvertimeEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $entry->employee_name }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($entry->location, 15) }}</td>
                                                <td>{{ $entry->start_time->format('H:i') }}-{{ $entry->end_time->format('H:i') }}</td>
                                                <td>
                                                    <a href="{{ auth()->user()->isHR() ? route('hr.overtime.hrga-pending') : route('hr.overtime.spv-pending') }}"
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="mdi mdi-check-circle text-success fs-1"></i>
                                <p class="text-muted mt-2">Tidak ada data lembur yang menunggu approval</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permintaan Kendaraan -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-car text-success"></i> Permintaan Kendaraan
                        </h5>
                        <a href="{{ auth()->user()->isHR() ? route('hr.approval.hrga-pending') : route('hr.approval.manager-pending') }}"
                           class="btn btn-success btn-sm">
                            <i class="mdi mdi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($pendingVehicleRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Tujuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingVehicleRequests as $request)
                                            <tr>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee_name }}</td>
                                                <td>{{ $request->vehicle_type }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->destination, 15) }}</td>
                                                <td>
                                                    <a href="{{ auth()->user()->isHR() ? route('hr.approval.hrga-pending') : route('hr.approval.manager-pending') }}"
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="mdi mdi-check-circle text-success fs-1"></i>
                                <p class="text-muted mt-2">Tidak ada permintaan kendaraan yang menunggu approval</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permintaan Inventaris -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-package-variant text-info"></i> Permintaan Inventaris
                        </h5>
                        <a href="{{ auth()->user()->isHR() ? route('hr.approval.hrga-pending') : route('hr.approval.manager-pending') }}"
                           class="btn btn-info btn-sm">
                            <i class="mdi mdi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if($pendingAssetRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Tujuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingAssetRequests as $request)
                                            <tr>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee_name }}</td>
                                                <td>{{ $request->asset_category }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->destination, 15) }}</td>
                                                <td>
                                                    <a href="{{ auth()->user()->isHR() ? route('hr.approval.hrga-pending') : route('hr.approval.manager-pending') }}"
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="mdi mdi-check-circle text-success fs-1"></i>
                                <p class="text-muted mt-2">Tidak ada permintaan inventaris yang menunggu approval</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection

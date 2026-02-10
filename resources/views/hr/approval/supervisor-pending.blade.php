@extends('main.layouts.main')
@section('title')
    SPV Pending Approval
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    SPV Pending Approval
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">SPV Pending Approval</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">SPV Pending Approval</li>
                </ol>
            </div>
        </div>

        <!-- Info Card: Permohonan yang Bisa Di-Approve -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 text-white">
                            <i class="mdi mdi-information-outline me-2"></i>
                            Informasi Permohonan yang Bisa Di-Approve
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-account-check me-2"></i>Sebagai SPV (Jabatan {{ Auth::user()->jabatanUser->jabatan }})</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="mdi {{ $approvalInfo['can_approve_absence'] ? 'mdi-check-circle text-success' : 'mdi-close-circle text-danger' }} me-2"></i>
                                        <strong>Permohonan Tidak Masuk Kerja (Absence):</strong>
                                        @if($approvalInfo['can_approve_absence'])
                                            <span class="badge badge-success">Bisa Approve</span>
                                            @if(!empty($approvalInfo['absence_approval_order']))
                                                <div class="mt-2">
                                                    <small class="text-muted d-block mb-1"><strong>Urutan Approval untuk Divisi {{ Auth::user()->divisiUser->divisi }}:</strong></small>
                                                    <ol class="mb-0" style="padding-left: 20px;">
                                                        @foreach($approvalInfo['absence_approval_order'] as $orderItem)
                                                            <li class="mb-1">
                                                                <span class="badge badge-{{ $orderItem['enabled'] ? 'primary' : 'secondary' }}">
                                                                    Urutan {{ $orderItem['order'] }}: {{ $orderItem['role'] }}
                                                                    @if($orderItem['is_current_user'])
                                                                        <i class="mdi mdi-account-check text-warning" title="Anda"></i>
                                                                    @endif
                                                                </span>
                                                                @if(!$orderItem['enabled'])
                                                                    <small class="text-muted">(Disabled)</small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">Tidak Bisa Approve</span>
                                            <small class="text-muted d-block">SPV disabled untuk divisi {{ Auth::user()->divisiUser->divisi }}</small>
                                        @endif
                                    </li>
                                    <li class="mt-2">
                                        <i class="mdi {{ $approvalInfo['can_approve_shift_change'] ? 'mdi-check-circle text-success' : 'mdi-close-circle text-danger' }} me-2"></i>
                                        <strong>Permohonan Tukar Shift:</strong>
                                        @if($approvalInfo['can_approve_shift_change'])
                                            <span class="badge badge-success">Bisa Approve</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Bisa Approve</span>
                                            <small class="text-muted d-block">SPV tidak ada di approval flow</small>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-map-marker me-2"></i>Divisi</h6>
                                <p class="mb-2"><strong>Divisi Anda:</strong> {{ Auth::user()->divisiUser->divisi }}</p>
                                <p class="mb-0 text-muted"><small>Anda hanya bisa approve permohonan dari divisi yang sama dengan divisi Anda.</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        @php
            $totalPending =
                $formRequestsWithAccess->count() +
                $overtimeRequests->count() +
                $vehicleRequests->count() +
                $assetRequests->count();
        @endphp
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning">{{ $totalPending }}</h3>
                        <p class="text-muted mb-0 font-weight-bold"><b>Total Menunggu</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-info">{{ $formRequestsWithAccess->count() }}</h3>
                        <p class="text-muted mb-0 font-weight-bold"><b>Form Karyawan</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning">{{ $overtimeRequests->count() }}</h3>
                        <p class="text-muted mb-0 font-weight-bold"><b>Data Lembur</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $vehicleRequests->count() + $assetRequests->count() }}</h3>
                        <p class="text-muted mb-0 font-weight-bold"><b>Kendaraan & Inventaris</b></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table with Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Pengajuan Menunggu Approval SPV</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="forms-tab" data-toggle="tab" data-target="#forms"
                                    type="button" role="tab">
                                    <i class="mdi mdi-file-document me-1"></i> Form Karyawan
                                    <span class="badge badge-info">{{ $formRequestsWithAccess->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="overtime-tab" data-toggle="tab" data-target="#overtime"
                                    type="button" role="tab">
                                    <i class="mdi mdi-clock me-1"></i> Data Lembur
                                    <span class="badge badge-warning">{{ $overtimeRequests->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="vehicle-tab" data-toggle="tab" data-target="#vehicle"
                                    type="button" role="tab">
                                    <i class="mdi mdi-car me-1"></i> Kendaraan
                                    <span class="badge badge-success">{{ $vehicleRequests->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="asset-tab" data-toggle="tab" data-target="#asset"
                                    type="button" role="tab">
                                    <i class="mdi mdi-package-variant me-1"></i> Inventaris
                                    <span class="badge badge-info">{{ $assetRequests->count() }}</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="requestTabsContent">
                            <!-- Form Karyawan Tab -->
                            <div class="tab-pane fade show active" id="forms" role="tabpanel">
                                @if ($formRequestsWithAccess->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No. Pengajuan</th>
                                                    <th>Jenis</th>
                                                    <th>Pemohon</th>
                                                    <th>Divisi</th>
                                                    <th>Status Approval</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($formRequestsWithAccess as $request)
                                                    <tr>
                                                        <td><span class="fw-bold">{{ $request->request_number }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-info text-white">{{ $request->request_type_label }}</span>
                                                        </td>
                                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($request->employee && $request->employee->divisiUser)
                                                                <span class="badge badge-secondary">
                                                                    {{ $request->employee->divisiUser->divisi ?? 'Divisi ' . $request->employee->divisi }}
                                                                </span>
                                                            @elseif($request->employee)
                                                                <span class="badge badge-secondary">
                                                                    Divisi {{ $request->employee->divisi ?? 'N/A' }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-secondary">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {!! $request->current_approval_status_text !!}
                                                            @if(!($request->can_approve ?? false) && isset($request->cannot_approve_reason))
                                                                <br><small class="text-danger">
                                                                    <i class="mdi mdi-alert-circle"></i> {{ $request->cannot_approve_reason }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @if($request->can_approve ?? false)
                                                                <a href="{{ route('hr.approval.show', $request->id) }}"
                                                                    class="btn btn-sm btn-outline-primary" title="Bisa di-approve">
                                                                    <i class="mdi mdi-eye"></i> Lihat
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-outline-secondary" disabled
                                                                    title="{{ $request->cannot_approve_reason ?? 'Tidak bisa di-approve' }}">
                                                                    <i class="mdi mdi-eye-off"></i> Lihat
                                                                </button>
                                                                @if(isset($request->cannot_approve_reason))
                                                                    <small class="d-block text-danger mt-1" style="font-size: 0.75rem; max-width: 200px;">
                                                                        <i class="mdi mdi-alert-circle"></i> {{ $request->cannot_approve_reason }}
                                                                    </small>
                                                                @else
                                                                    <small class="d-block text-muted mt-1">
                                                                        <i class="mdi mdi-alert-circle"></i> Tidak bisa approve
                                                                    </small>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada form pengajuan yang menunggu approval</h5>
                                    </div>
                                @endif
                            </div>

                            <!-- Data Lembur Tab -->
                            <div class="tab-pane fade" id="overtime" role="tabpanel">
                                @if ($overtimeRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                @foreach ($overtimeRequests as $entry)
                                                    <tr>
                                                        <td>{{ $entry->request_date->format('d/m/Y') }}</td>
                                                        <td>{{ $entry->employee_name }}</td>
                                                        <td>{{ \Illuminate\Support\Str::limit($entry->location, 30) }}</td>
                                                        <td>{{ $entry->start_time->format('H:i') }}-{{ $entry->end_time->format('H:i') }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('hr.overtime.index') }}"
                                                                class="btn btn-sm btn-outline-warning">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada data lembur yang menunggu approval</h5>
                                    </div>
                                @endif
                            </div>

                            <!-- Kendaraan Tab -->
                            <div class="tab-pane fade" id="vehicle" role="tabpanel">
                                @if ($vehicleRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                @foreach ($vehicleRequests as $request)
                                                    <tr>
                                                        <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                        <td>{{ $request->employee_name }}</td>
                                                        <td>{{ $request->vehicle_type }}</td>
                                                        <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('hr.vehicle-asset.index', ['type' => 'vehicle']) }}"
                                                                class="btn btn-sm btn-outline-success">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada permintaan kendaraan yang menunggu approval
                                        </h5>
                                    </div>
                                @endif
                            </div>

                            <!-- Inventaris Tab -->
                            <div class="tab-pane fade" id="asset" role="tabpanel">
                                @if ($assetRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                @foreach ($assetRequests as $request)
                                                    <tr>
                                                        <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                        <td>{{ $request->employee_name }}</td>
                                                        <td>{{ $request->asset_category }}</td>
                                                        <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('hr.vehicle-asset.index', ['type' => 'asset']) }}"
                                                                class="btn btn-sm btn-outline-info">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada permintaan inventaris yang menunggu approval
                                        </h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    @endsection

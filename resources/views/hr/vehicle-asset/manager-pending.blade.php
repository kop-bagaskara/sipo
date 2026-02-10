@extends('main.layouts.main')
@section('title')
    Manager Pending Approval
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .cust-col { white-space: nowrap; }
    </style>
@endsection
@section('page-title')
    Manager Pending Approval
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Manager Pending Approval</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">Manager Pending Approval</li>
                </ol>
            </div>
        </div>

    <!-- Statistics -->
    @php
        $dataLemburCount = $overtimeRequests->count() + ($splRequests ?? collect())->count();
        $totalPending = $formRequests->count() + $dataLemburCount + $vehicleRequests->count() + $assetRequests->count();
    @endphp
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $totalPending }}</h3>
                    <p class="text-muted mb-0">Total Menunggu</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $formRequests->count() }}</h3>
                    <p class="text-muted mb-0">Form Karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $dataLemburCount }}</h3>
                    <p class="text-muted mb-0">Data Lembur</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $vehicleRequests->count() + $assetRequests->count() }}</h3>
                    <p class="text-muted mb-0">Kendaraan & Inventaris</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table with Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-account-key me-2"></i>Daftar Pengajuan Menunggu Approval Manager
                    </h4>
                    <p class="card-subtitle text-muted mb-0">
                        Halaman ini menampilkan semua pengajuan yang menunggu persetujuan Anda sebagai Manager.
                        Klik "Lihat & Approve" untuk melihat detail dan melakukan approval.
                    </p>
                    </div>
                    <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="forms-tab" data-toggle="tab" data-target="#forms" type="button" role="tab">
                                <i class="mdi mdi-file-document me-1"></i> Form Karyawan
                                <span class="badge badge-info">{{ $formRequests->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="overtime-tab" data-toggle="tab" data-target="#overtime" type="button" role="tab">
                                <i class="mdi mdi-clock me-1"></i> Data Lembur
                                <span class="badge badge-warning">{{ $dataLemburCount }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="vehicle-tab" data-toggle="tab" data-target="#vehicle" type="button" role="tab">
                                <i class="mdi mdi-car me-1"></i> Kendaraan
                                <span class="badge badge-success">{{ $vehicleRequests->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="asset-tab" data-toggle="tab" data-target="#asset" type="button" role="tab">
                                <i class="mdi mdi-package-variant me-1"></i> Inventaris
                                <span class="badge badge-info">{{ $assetRequests->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="requestTabsContent">
                        <!-- Form Karyawan Tab -->
                        <div class="tab-pane fade show active" id="forms" role="tabpanel">
                            @if($formRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Pengajuan</th>
                                                <th>Jenis</th>
                                                <th>Pemohon</th>
                                                <th>Divisi</th>
                                                <th>Tanggal Dibuat</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($formRequests as $request)
                                            <tr>
                                                <td><span class="fw-bold">{{ $request->request_number }}</span></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $request->request_type === 'absence' ? 'Tidak Masuk Kerja' : 'Tukar Shift' }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $request->employee->divisiUser->nama_divisi ?? 'Divisi ' . ($request->employee->divisi ?? 'N/A') }}
                                                </td>
                                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-warning">Menunggu Approval Manager</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('hr.approval.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> Lihat & Approve
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
                                    <h5 class="text-muted mt-3">Tidak ada form pengajuan yang menunggu approval</h5>
                                </div>
                            @endif
                        </div>

                        <!-- Data Lembur Tab -->
                        <div class="tab-pane fade" id="overtime" role="tabpanel">
                            @if($dataLemburCount > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Dokumen</th>
                                                <th>Tanggal</th>
                                                <th>Shift</th>
                                                <th>Divisi</th>
                                                <th>Supervisor</th>
                                                <th>Jml Karyawan</th>
                                                <th>Keperluan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($splRequests ?? collect() as $spl)
                                                <tr>
                                                    <td><span class="fw-bold">{{ $spl->spl_number }}</span></td>
                                                    <td>{{ $spl->request_date->format('d/m/Y') }}</td>
                                                    <td><span class="badge bg-info text-white">{{ $spl->shift }}</span></td>
                                                    <td>{{ $spl->divisi_name ?? 'N/A' }}</td>
                                                    <td>{{ $spl->supervisor->name ?? 'N/A' }}</td>
                                                    <td><span class="badge bg-info text-white">{{ $spl->employees->count() ?? 0 }} orang</span></td>
                                                    <td><small>{{ \Illuminate\Support\Str::limit($spl->keperluan, 40) }}</small></td>
                                                    <td><span class="badge bg-warning text-white">{{ $spl->status_label }}</span></td>
                                                    <td><a href="{{ route('hr.spl.show', $spl->id) }}" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-eye"></i> Lihat</a></td>
                                                </tr>
                                            @endforeach
                                            @foreach($overtimeRequests as $entry)
                                                <tr>
                                                    <td><span class="fw-bold">-</span></td>
                                                    <td>{{ $entry->request_date->format('d/m/Y') }}</td>
                                                    <td><span class="badge bg-secondary text-white">-</span></td>
                                                    <td>{{ $entry->employee_name }}</td>
                                                    <td>-</td>
                                                    <td><span class="badge bg-info text-white">1 orang</span></td>
                                                    <td><small>{{ \Illuminate\Support\Str::limit($entry->location ?? '-', 40) }}</small></td>
                                                    <td><span class="badge bg-warning text-white">Pending</span></td>
                                                    <td><a href="{{ route('hr.overtime.index') }}" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-eye"></i> Lihat</a></td>
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
                            @if($vehicleRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Pengajuan</th>
                                                <th>Tanggal</th>
                                                <th>Nama</th>
                                                <th>Jenis</th>
                                                <th>Tujuan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vehicleRequests as $request)
                                            <tr>
                                                <td><span class="fw-bold">{{ $request->request_number ?? 'N/A' }}</span></td>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee_name }}</td>
                                                <td>{{ $request->vehicle_type }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                                                <td>
                                                    <span class="badge bg-warning">Menunggu Approval Manager</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('hr.vehicle-asset.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> Lihat & Approve
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
                                    <h5 class="text-muted mt-3">Tidak ada permintaan kendaraan yang menunggu approval</h5>
                            </div>
                        @endif
                        </div>

                        <!-- Inventaris Tab -->
                        <div class="tab-pane fade" id="asset" role="tabpanel">
                            @if($assetRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Pengajuan</th>
                                                <th>Tanggal</th>
                                                <th>Nama</th>
                                                <th>Kategori</th>
                                                <th>Tujuan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assetRequests as $request)
                                            <tr>
                                                <td><span class="fw-bold">{{ $request->request_number ?? 'N/A' }}</span></td>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee_name }}</td>
                                                <td>{{ $request->asset_category }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                                                <td>
                                                    <span class="badge bg-warning">Menunggu Approval Manager</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('hr.vehicle-asset.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> Lihat & Approve
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
                                    <h5 class="text-muted mt-3">Tidak ada permintaan inventaris yang menunggu approval</h5>
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


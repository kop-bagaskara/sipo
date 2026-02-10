@extends('main.layouts.main')

@section('title')
    Employee Request Report
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Employee Request Report
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Employee Request Report</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Request Report</li>
            </ol>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Filter Request Report</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('hr.reports.request-report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="supervisor_approved" {{ request('status') == 'supervisor_approved' ? 'selected' : '' }}>Supervisor Approved</option>
                                        <option value="hr_approved" {{ request('status') == 'hr_approved' ? 'selected' : '' }}>HR Approved</option>
                                        <option value="supervisor_rejected" {{ request('status') == 'supervisor_rejected' ? 'selected' : '' }}>Supervisor Rejected</option>
                                        <option value="hr_rejected" {{ request('status') == 'hr_rejected' ? 'selected' : '' }}>HR Rejected</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Request Type</label>
                                    <select class="form-control" name="request_type">
                                        <option value="">Semua Tipe</option>
                                        @foreach($requestTypes as $type)
                                            <option value="{{ $type['id'] }}" {{ request('request_type') == $type['id'] ? 'selected' : '' }}>
                                                {{ $type['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Filter
                                    </button>
                                    <a href="{{ route('hr.reports.request-report') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                    <button type="button" class="btn btn-success" onclick="exportReport()">
                                        <i class="mdi mdi-download"></i> Export Excel
                                    </button>
                                    <button onclick="window.print()" class="btn btn-info">
                                        <i class="mdi mdi-printer"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Data -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Employee Request Report Data</h4>
                    <p class="card-title-desc">Total: {{ $requests->count() }} requests</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="requestReportTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Employee</th>
                                    <th>Request Type</th>
                                    <th>Status</th>
                                    <th>Supervisor</th>
                                    <th>HR</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $index => $request)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <h6 class="mb-0">{{ $request->employee->name ?? 'Unknown' }}</h6>
                                            <small class="text-muted">{{ $request->employee->email ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $request->request_type_label }}</span>
                                        </td>
                                        <td>
                                            @if($request->status == 'hr_approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($request->status == 'supervisor_rejected' || $request->status == 'hr_rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($request->status == 'supervisor_approved')
                                                <span class="badge bg-info">HR Review</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->supervisor->name ?? '-' }}</td>
                                        <td>{{ $request->hr->name ?? '-' }}</td>
                                        <td>{{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('hr.requests.show', $request->id) }}"
                                               class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-file-document font-size-48 text-muted"></i>
                                                <h5 class="text-muted mt-2">Tidak ada data request</h5>
                                                <p class="text-muted">Coba ubah filter untuk mendapatkan hasil.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                            <i class="mdi mdi-file-document"></i>
                        </div>
                    </div>
                    <h4 class="text-primary">{{ $requests->count() }}</h4>
                    <p class="text-muted mb-0">Total Requests</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                            <i class="mdi mdi-clock"></i>
                        </div>
                    </div>
                    <h4 class="text-warning">{{ $requests->where('status', 'pending')->count() }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                            <i class="mdi mdi-check"></i>
                        </div>
                    </div>
                    <h4 class="text-success">{{ $requests->where('status', 'hr_approved')->count() }}</h4>
                    <p class="text-muted mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-danger text-danger rounded-circle font-size-24">
                            <i class="mdi mdi-close"></i>
                        </div>
                    </div>
                    <h4 class="text-danger">{{ $requests->whereIn('status', ['supervisor_rejected', 'hr_rejected'])->count() }}</h4>
                    <p class="text-muted mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#requestReportTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                order: [[6, 'desc']]
            });
        });

        function exportReport() {
            Swal.fire({
                title: 'Export Report',
                text: 'Fitur export akan segera tersedia',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Print styles
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('print-mode');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('print-mode');
        });
    </script>

    <style>
        @media print {
            .btn, .breadcrumb, .page-title-box, .card-header {
                display: none !important;
            }

            .table {
                border: 1px solid #000 !important;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
            }
        }
    </style>
@endsection

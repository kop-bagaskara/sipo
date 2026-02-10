@extends('main.layouts.main')

@section('title')
    Security User Report
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Security User Report
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Security User Report</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Security User Report</li>
            </ol>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-shield-account"></i> Filter Security User Report</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('hr.reports.security-user-report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <select class="form-control" name="jabatan">
                                        <option value="">Semua Jabatan</option>
                                        @foreach($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}" {{ request('jabatan') == $jabatan->id ? 'selected' : '' }}>
                                                {{ $jabatan->jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Level</label>
                                    <select class="form-control" name="level">
                                        <option value="">Semua Level</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                                {{ $level->level }}
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
                                    <a href="{{ route('hr.reports.security-user-report') }}" class="btn btn-secondary">
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
                    <h4 class="card-title">Security User Report Data</h4>
                    <p class="card-title-desc">Total: {{ $users->count() }} security users</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="securityUserReportTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Jabatan</th>
                                    <th>Level</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $user->jabatanUser->jabatan ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->levelUser->level ?? '-' }}</span>
                                        </td>
                                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('hr.security-master.show', $user->id) }}"
                                               class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-shield-account font-size-48 text-muted"></i>
                                                <h5 class="text-muted mt-2">Tidak ada data security user</h5>
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                            <i class="mdi mdi-account-group"></i>
                        </div>
                    </div>
                    <h4 class="text-primary">{{ $users->count() }}</h4>
                    <p class="text-muted mb-0">Total Security Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                            <i class="mdi mdi-calendar-today"></i>
                        </div>
                    </div>
                    <h4 class="text-success">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                    <p class="text-muted mb-0">Added This Month</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                            <i class="mdi mdi-calendar-week"></i>
                        </div>
                    </div>
                    <h4 class="text-info">{{ $users->where('created_at', '>=', now()->startOfWeek())->count() }}</h4>
                    <p class="text-muted mb-0">Added This Week</p>
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
            $('#securityUserReportTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                order: [[1, 'asc']]
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

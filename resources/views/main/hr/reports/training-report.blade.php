@extends('main.layouts.main')

@section('title')
    Training Report
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Training Report
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Training Report</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Training Report</li>
            </ol>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-school"></i> Filter Training Report</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('hr.reports.training-report') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-magnify"></i> Filter
                                        </button>
                                        <a href="{{ route('hr.reports.training-report') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
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
                    <h4 class="card-title">Training Report Data</h4>
                    <p class="card-title-desc">Total: {{ $trainings->count() }} trainings</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="trainingReportTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Schedules</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainings as $index => $training)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <h6 class="mb-0">{{ $training->training_name }}</h6>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ strlen($training->description) > 50 ? substr($training->description, 0, 50) . '...' : $training->description }}</span>
                                        </td>
                                        <td>{{ $training->duration_hours }} hours</td>
                                        <td>
                                            @if($training->status == 'published')
                                                <span class="badge bg-success">Published</span>
                                            @elseif($training->status == 'draft')
                                                <span class="badge bg-warning">Draft</span>
                                            @elseif($training->status == 'completed')
                                                <span class="badge bg-info">Completed</span>
                                            @elseif($training->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($training->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $training->schedules->count() }} schedules</span>
                                        </td>
                                        <td>{{ $training->created_at ? $training->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('hr.training.show', $training->id) }}"
                                               class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-school font-size-48 text-muted"></i>
                                                <h5 class="text-muted mt-2">Tidak ada data training</h5>
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
                            <i class="mdi mdi-school"></i>
                        </div>
                    </div>
                    <h4 class="text-primary">{{ $trainings->count() }}</h4>
                    <p class="text-muted mb-0">Total Trainings</p>
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
                    <h4 class="text-success">{{ $trainings->where('status', 'published')->count() }}</h4>
                    <p class="text-muted mb-0">Published</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                    </div>
                    <h4 class="text-info">{{ $trainings->where('status', 'completed')->count() }}</h4>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                            <i class="mdi mdi-calendar-clock"></i>
                        </div>
                    </div>
                    <h4 class="text-warning">{{ $trainings->sum(function($training) { return $training->schedules->count(); }) }}</h4>
                    <p class="text-muted mb-0">Total Schedules</p>
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
            $('#trainingReportTable').DataTable({
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

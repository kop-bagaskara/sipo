@extends('main.layouts.main')

@section('title')
    Vehicle Checklist Report
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Vehicle Checklist Report
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Vehicle Checklist Report</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.reports.security') }}">Security Reports</a></li>
                <li class="breadcrumb-item active">Vehicle Checklist</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Vehicle Checklist Report</h4>
                            <p class="card-title-desc">Laporan checklist kendaraan security</p>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <a href="{{ route('hr.reports.security.export.vehicle-checklist', request()->all()) }}" class="btn btn-success me-2" target="_blank">
                                <i class="mdi mdi-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('hr.reports.security') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-filter-variant"></i> Filter Data
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('hr.reports.security.vehicle-checklist') }}">
                                <div class="row">
                                    <!-- Date Range -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}" placeholder="Pilih tanggal">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Dari</label>
                                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Dari tanggal">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Sampai</label>
                                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Sampai tanggal">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Filters -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Shift</label>
                                            <select name="shift" class="form-control">
                                                <option value="">Semua Shift</option>
                                                @foreach($shifts as $key => $value)
                                                    <option value="{{ $key }}" {{ request('shift') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control">
                                                <option value="">Semua Status</option>
                                                @foreach($statuses as $key => $value)
                                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Driver</label>
                                            <select name="driver" class="form-control">
                                                <option value="">Semua Driver</option>
                                                @foreach($drivers as $driver)
                                                    <option value="{{ $driver }}" {{ request('driver') == $driver ? 'selected' : '' }}>
                                                        {{ $driver }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="mdi mdi-magnify"></i> Filter
                                                </button>
                                                <a href="{{ route('hr.reports.security.vehicle-checklist') }}" class="btn btn-outline-secondary">
                                                    <i class="mdi mdi-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="vehicleChecklistTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>No. Urut</th>
                                    <th>Driver</th>
                                    <th>Model Kendaraan</th>
                                    <th>No. Polisi</th>
                                    <th>Shift</th>
                                    <th>Jam Out</th>
                                    <th>Jam In</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checklists as $index => $checklist)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $checklist->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $checklist->no_urut }}</td>
                                        <td>{{ $checklist->nama_driver }}</td>
                                        <td>{{ $checklist->model_kendaraan }}</td>
                                        <td>{{ $checklist->no_polisi }}</td>
                                        <td>
                                            <span class="badge badge-{{ $checklist->shift == 'pagi' ? 'success' : ($checklist->shift == 'siang' ? 'warning' : 'info') }}">
                                                {{ $checklist->shift_formatted }}
                                            </span>
                                        </td>
                                        <td>{{ $checklist->jam_out ? \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') : '-' }}</td>
                                        <td>{{ $checklist->jam_in ? \Carbon\Carbon::parse($checklist->jam_in)->format('H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $checklist->status == 'keluar' ? 'warning' : 'success' }}">
                                                {{ ucfirst($checklist->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $checklist->petugas_security }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information"></i>
                                                Tidak ada data checklist kendaraan
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
@endsection

@section('script')
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

    <script>
        $(document).ready(function() {
            $('#vehicleChecklistTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0] }
                ]
            });
        });
    </script>
@endsection

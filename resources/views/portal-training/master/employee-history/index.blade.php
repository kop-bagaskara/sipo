@extends('main.layouts.main')
@section('title')
    Riwayat Training Karyawan
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .training-type-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .type-portal {
            background: #007bff;
            color: white;
        }
        .type-hr {
            background: #28a745;
            color: white;
        }
    </style>
@endsection
@section('page-title')
    Riwayat Training Karyawan
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Riwayat Training Karyawan</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.master.assignments.index') }}">Master</a></li>
                <li class="breadcrumb-item active">Riwayat Training Karyawan</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-account-search mr-2"></i>
                        Pilih Karyawan
                    </h5>
                    <form method="GET" action="{{ route('hr.portal-training.employee-history.index') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Karyawan</label>
                                    <select name="employee_id" class="form-control" required onchange="this.form.submit()">
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" 
                                                    {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ $emp->divisi ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($employee)
                <!-- Employee Info Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-account mr-2"></i>
                            Informasi Karyawan
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong> {{ $employee->name }}</p>
                                <p><strong>Divisi:</strong> {{ $employee->divisi ?? '-' }}</p>
                                <p><strong>Email:</strong> {{ $employee->email ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Training Portal:</strong> {{ $portalTrainings->count() }}</p>
                                <p><strong>Total Training HR:</strong> {{ $hrTrainings->count() }}</p>
                                <p><strong>Total Semua Training:</strong> {{ $portalTrainings->count() + $hrTrainings->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Training History Table -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-history mr-2"></i>
                            Riwayat Training
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($portalTrainings->isEmpty() && $hrTrainings->isEmpty())
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                Karyawan ini belum memiliki riwayat training.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tipe</th>
                                            <th>Nama Training</th>
                                            <th>Tanggal Assign</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Sesi</th>
                                            <th>Nilai</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $allTrainings = $portalTrainings->merge($hrTrainings)->sortByDesc('assigned_date');
                                        @endphp
                                        @foreach($allTrainings as $index => $training)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="training-type-badge type-{{ $training['type'] }}">
                                                        {{ strtoupper($training['type']) }}
                                                    </span>
                                                </td>
                                                <td>{{ $training['training_name'] }}</td>
                                                <td>{{ $training['assigned_date'] ? \Carbon\Carbon::parse($training['assigned_date'])->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($training['status'] == 'completed')
                                                        <span class="badge badge-success">Selesai</span>
                                                    @elseif($training['status'] == 'in_progress')
                                                        <span class="badge badge-warning">Sedang Dikerjakan</span>
                                                    @elseif($training['status'] == 'assigned')
                                                        <span class="badge badge-info">Ditetapkan</span>
                                                    @elseif($training['status'] == 'approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                    @elseif($training['status'] == 'registered')
                                                        <span class="badge badge-secondary">Terdaftar</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($training['status']) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $training['progress_percentage'] }}%"
                                                             aria-valuenow="{{ $training['progress_percentage'] }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ number_format($training['progress_percentage'], 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($training['type'] == 'portal')
                                                        {{ $training['completed_sessions'] }}/{{ $training['total_sessions'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($training['type'] == 'portal' && $training['average_score'] > 0)
                                                        {{ number_format($training['average_score'], 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($training['type'] == 'portal')
                                                        <a href="{{ route('hr.portal-training.scores.show', $training['id']) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Silakan pilih karyawan untuk melihat riwayat training.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if($employee && ($portalTrainings->isNotEmpty() || $hrTrainings->isNotEmpty()))
                $('#historyTable').DataTable({
                    "order": [[3, "desc"]],
                    "pageLength": 25,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    }
                });
            @endif
        });
    </script>
@endsection


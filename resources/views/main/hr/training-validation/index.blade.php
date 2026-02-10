@extends('main.layouts.main')

@section('title')
    Training Validation Dashboard
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Training Validation Dashboard
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Training Validation Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.training.dashboard') }}">Training</a></li>
                <li class="breadcrumb-item active">Validation</li>
            </ol>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                            <i class="mdi mdi-school"></i>
                        </div>
                    </div>
                    <h4 class="text-primary">{{ $completedSchedules->total() }}</h4>
                    <p class="text-muted mb-0">Completed Trainings</p>
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
                    <h4 class="text-warning">{{ $upcomingSchedules->count() }}</h4>
                    <p class="text-muted mb-0">Need Validation</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                            <i class="mdi mdi-calendar-check"></i>
                        </div>
                    </div>
                    <h4 class="text-info">{{ $upcomingSchedules->where('schedule_date', '<=', now())->count() }}</h4>
                    <p class="text-muted mb-0">Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                            <i class="mdi mdi-chart-line"></i>
                        </div>
                    </div>
                    <h4 class="text-success">{{ number_format(($completedSchedules->total() / max($completedSchedules->total() + $upcomingSchedules->count(), 1)) * 100, 1) }}%</h4>
                    <p class="text-muted mb-0">Completion Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Trainings Need Validation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Training yang Perlu Divalidasi</h4>
                    <p class="card-title-desc">Training yang sudah selesai dan perlu divalidasi kehadiran</p>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Training</th>
                                        <th>Schedule Date</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0">{{ $schedule->training->training_name ?? 'Unknown Training' }}</h6>
                                                <small class="text-muted">{{ $schedule->training->training_code ?? '' }}</small>
                                            </td>
                                            <td>{{ $schedule->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($schedule->start_time && $schedule->end_time)
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $schedule->location ?? '-' }}</td>
                                            <td>
                                                @if($schedule->schedule_date <= now())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('hr.training-validation.show', $schedule->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="mdi mdi-check"></i> Validate Attendance
                                                </a>
                                                <button onclick="markCompleted({{ $schedule->id }})"
                                                        class="btn btn-sm btn-success">
                                                    <i class="mdi mdi-check-all"></i> Mark Completed
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-check-circle font-size-48 text-success"></i>
                            <h5 class="text-success mt-2">Semua training sudah divalidasi!</h5>
                            <p class="text-muted">Tidak ada training yang perlu divalidasi saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Trainings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Training yang Sudah Selesai</h4>
                    <p class="card-title-desc">Riwayat training yang sudah divalidasi</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="completedTrainingsTable">
                            <thead>
                                <tr>
                                    <th>Training</th>
                                    <th>Schedule Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Completed At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedSchedules as $schedule)
                                    <tr>
                                        <td>
                                            <h6 class="mb-0">{{ $schedule->training->training_name ?? 'Unknown Training' }}</h6>
                                            <small class="text-muted">{{ $schedule->training->training_code ?? '' }}</small>
                                        </td>
                                        <td>{{ $schedule->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @if($schedule->start_time && $schedule->end_time)
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $schedule->location ?? '-' }}</td>
                                        <td>{{ $schedule->completed_at ? \Carbon\Carbon::parse($schedule->completed_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('hr.training-validation.show', $schedule->id) }}"
                                               class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-eye"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-school font-size-48 text-muted"></i>
                                                <h5 class="text-muted mt-2">Belum ada training yang selesai</h5>
                                                <p class="text-muted">Training yang sudah divalidasi akan muncul di sini.</p>
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

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#completedTrainingsTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[4, 'desc']]
            });
        });

        function markCompleted(scheduleId) {
            Swal.fire({
                title: 'Mark Training as Completed',
                text: 'Apakah Anda yakin ingin menandai training ini sebagai selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Mark Completed',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('hr.training-validation.mark-completed', '') }}/${scheduleId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection

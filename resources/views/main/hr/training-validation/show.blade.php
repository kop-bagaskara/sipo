@extends('main.layouts.main')

@section('title')
    Validate Training Attendance
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Validate Training Attendance
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Validate Training Attendance</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.training-validation.index') }}">Training Validation</a></li>
                <li class="breadcrumb-item active">Validate Attendance</li>
            </ol>
        </div>
    </div>

    <!-- Training Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Training Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-1">Training Name</h6>
                            <p class="text-muted">{{ $schedule->training->training_name ?? 'Unknown Training' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Training Code</h6>
                            <p class="text-muted">{{ $schedule->training->training_code ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Schedule Date</h6>
                            <p class="text-muted">{{ $schedule->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Time</h6>
                            <p class="text-muted">
                                @if($schedule->start_time && $schedule->end_time)
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Location</h6>
                            <p class="text-muted">{{ $schedule->location ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Status</h6>
                            <p class="text-muted">
                                @if($schedule->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">Pending Validation</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Validation Form -->
    @if($schedule->status != 'completed')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Validate Attendance</h4>
                        <p class="card-title-desc">Tandai peserta yang hadir dan tidak hadir</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.training-validation.validate', $schedule->id) }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Participant Name</th>
                                            <th>Email</th>
                                            <th>Registration Status</th>
                                            <th>Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($participants as $index => $participant)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <h6 class="mb-0">{{ $participant->employee->name ?? 'Unknown' }}</h6>
                                                </td>
                                                <td>{{ $participant->employee->email ?? '-' }}</td>
                                                <td>
                                                    @if($participant->registration_status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($participant->registration_status == 'registered')
                                                        <span class="badge bg-warning">Registered</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($participant->registration_status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="attendance[{{ $participant->id }}]"
                                                               id="present_{{ $participant->id }}"
                                                               value="present"
                                                               {{ $participant->attendance_status == 'present' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-success" for="present_{{ $participant->id }}">
                                                            <i class="mdi mdi-check"></i> Present
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="attendance[{{ $participant->id }}]"
                                                               id="absent_{{ $participant->id }}"
                                                               value="absent"
                                                               {{ $participant->attendance_status == 'absent' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-danger" for="absent_{{ $participant->id }}">
                                                            <i class="mdi mdi-close"></i> Absent
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <div class="py-4">
                                                        <i class="mdi mdi-account-off font-size-48 text-muted"></i>
                                                        <h5 class="text-muted mt-2">Tidak ada peserta</h5>
                                                        <p class="text-muted">Belum ada peserta yang terdaftar untuk training ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($participants->count() > 0)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Notes (Optional)</label>
                                            <textarea class="form-control" name="notes" rows="3"
                                                      placeholder="Catatan tambahan tentang training atau peserta..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-check"></i> Validate Attendance
                                        </button>
                                        <a href="{{ route('hr.training-validation.index') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Attendance Summary (if completed) -->
    @if($schedule->status == 'completed')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Attendance Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                                                <i class="mdi mdi-check"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-success">{{ $participants->where('attendance_status', 'present')->count() }}</h4>
                                        <p class="text-muted mb-0">Present</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-danger text-danger rounded-circle font-size-24">
                                                <i class="mdi mdi-close"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-danger">{{ $participants->where('attendance_status', 'absent')->count() }}</h4>
                                        <p class="text-muted mb-0">Absent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                                                <i class="mdi mdi-account-group"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-info">{{ $participants->count() }}</h4>
                                        <p class="text-muted mb-0">Total Participants</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                                                <i class="mdi mdi-chart-pie"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-warning">{{ $participants->count() > 0 ? number_format(($participants->where('attendance_status', 'present')->count() / $participants->count()) * 100, 1) : 0 }}%</h4>
                                        <p class="text-muted mb-0">Attendance Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Participant Lists -->
                        <div class="row mt-4">
                            <!-- Present Participants -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-soft-success">
                                        <h5 class="card-title text-success mb-0">
                                            <i class="mdi mdi-check-circle me-2"></i>
                                            Present Participants ({{ $participants->where('attendance_status', 'present')->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($participants->where('attendance_status', 'present')->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>NIP</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($participants->where('attendance_status', 'present') as $participant)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $participant->employee->Nama ?? 'Unknown' }}</strong>
                                                                </td>
                                                                <td>
                                                                    <code>{{ $participant->employee_id }}</code>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-success">Present</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center">No participants present</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Absent Participants -->
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-soft-danger">
                                        <h5 class="card-title text-danger mb-0">
                                            <i class="mdi mdi-close-circle me-2"></i>
                                            Absent Participants ({{ $participants->where('attendance_status', 'absent')->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($participants->where('attendance_status', 'absent')->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>NIP</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($participants->where('attendance_status', 'absent') as $participant)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $participant->employee->Nama ?? 'Unknown' }}</strong>
                                                                </td>
                                                                <td>
                                                                    <code>{{ $participant->employee_id }}</code>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-danger">Absent</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center">All participants present</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($participants->where('attendance_status', 'absent')->count() > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <h5 class="alert-heading">Reschedule Required</h5>
                                        <p>Ada {{ $participants->where('attendance_status', 'absent')->count() }} peserta yang tidak hadir.
                                           Apakah Anda ingin membuat jadwal ulang untuk mereka?</p>
                                        <a href="{{ route('hr.training-validation.reschedule', $schedule->id) }}"
                                           class="btn btn-warning">
                                            <i class="mdi mdi-calendar-plus"></i> Create Reschedule
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto-select present for approved participants
        document.addEventListener('DOMContentLoaded', function() {
            const approvedParticipants = document.querySelectorAll('input[value="present"]');
            approvedParticipants.forEach(function(radio) {
                const participantRow = radio.closest('tr');
                const statusBadge = participantRow.querySelector('.badge');
                if (statusBadge && statusBadge.textContent.includes('Approved')) {
                    radio.checked = true;
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const attendanceInputs = document.querySelectorAll('input[name^="attendance"]:checked');
            if (attendanceInputs.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validation Required',
                    text: 'Silakan pilih kehadiran untuk minimal satu peserta.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
@endsection

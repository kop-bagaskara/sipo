@extends('main.layouts.main')

@section('title')
    Reschedule Training
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Reschedule Training
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Reschedule Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.training-validation.index') }}">Training Validation</a></li>
                <li class="breadcrumb-item active">Reschedule</li>
            </ol>
        </div>
    </div>

    <!-- Original Training Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Original Training Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-1">Training Name</h6>
                            <p class="text-muted">{{ $schedule->training->training_name ?? 'Unknown Training' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Original Date</h6>
                            <p class="text-muted">{{ $schedule->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Original Time</h6>
                            <p class="text-muted">
                                @if($schedule->start_time && $schedule->end_time)
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Original Location</h6>
                            <p class="text-muted">{{ $schedule->location ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absent Participants -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Absent Participants</h4>
                    <p class="card-title-desc">Peserta yang tidak hadir dan akan dijadwalkan ulang</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Participant Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absentParticipants as $index => $participant)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <h6 class="mb-0">{{ $participant->employee->name ?? 'Unknown' }}</h6>
                                        </td>
                                        <td>{{ $participant->employee->email ?? '-' }}</td>
                                        <td>{{ $participant->employee->department ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-danger">Absent</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-check-circle font-size-48 text-success"></i>
                                                <h5 class="text-success mt-2">Semua peserta hadir!</h5>
                                                <p class="text-muted">Tidak ada peserta yang tidak hadir untuk training ini.</p>
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

    <!-- Reschedule Form -->
    @if($absentParticipants->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Schedule</h4>
                        <p class="card-title-desc">Buat jadwal baru untuk peserta yang tidak hadir</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.training-validation.reschedule', $schedule->id) }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">New Schedule Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="new_schedule_date"
                                               value="{{ old('new_schedule_date') }}" required>
                                        @error('new_schedule_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="new_start_time"
                                               value="{{ old('new_start_time') }}" required>
                                        @error('new_start_time')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="new_end_time"
                                               value="{{ old('new_end_time') }}" required>
                                        @error('new_end_time')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">New Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_location"
                                               value="{{ old('new_location') }}" placeholder="Enter new location" required>
                                        @error('new_location')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Select Participants to Reschedule</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($absentParticipants as $participant)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="participants[]"
                                                           value="{{ $participant->id }}"
                                                           id="participant_{{ $participant->id }}"
                                                           checked>
                                                    <label class="form-check-label" for="participant_{{ $participant->id }}">
                                                        {{ $participant->employee->name ?? 'Unknown' }}
                                                        <small class="text-muted">({{ $participant->employee->email ?? '-' }})</small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('participants')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Additional Notes (Optional)</label>
                                        <textarea class="form-control" name="notes" rows="3"
                                                  placeholder="Catatan tambahan untuk reschedule...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-calendar-plus"></i> Create Reschedule
                                    </button>
                                    <a href="{{ route('hr.training-validation.show', $schedule->id) }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to tomorrow
            const dateInput = document.querySelector('input[name="new_schedule_date"]');
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dateInput.min = tomorrow.toISOString().split('T')[0];

            // Set default time based on original schedule
            const originalStartTime = '{{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format("H:i") : "" }}';
            const originalEndTime = '{{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format("H:i") : "" }}';

            if (originalStartTime) {
                document.querySelector('input[name="new_start_time"]').value = originalStartTime;
            }
            if (originalEndTime) {
                document.querySelector('input[name="new_end_time"]').value = originalEndTime;
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const selectedParticipants = document.querySelectorAll('input[name="participants[]"]:checked');
                if (selectedParticipants.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'No Participants Selected',
                        text: 'Silakan pilih minimal satu peserta untuk dijadwalkan ulang.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endsection

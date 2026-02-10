@extends('main.layouts.main')

@section('title', 'Edit RnD Planning')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-pencil"></i>
                        Edit RnD Planning - {{ $job->job_code }}
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('development.rnd-planning.list') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali ke List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Job Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Job Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Job Code:</strong></td>
                                    <td>{{ $job->job_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Job Name:</strong></td>
                                    <td>{{ $job->job_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($job->type === 'proof')
                                            <span class="badge badge-info">Proof (Normal)</span>
                                        @else
                                            <span class="badge badge-warning">Trial Item Khusus</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        @if($job->priority === 'high')
                                            <span class="badge badge-danger">High</span>
                                        @elseif($job->priority === 'medium')
                                            <span class="badge badge-warning">Medium</span>
                                        @else
                                            <span class="badge badge-success">Low</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Additional Info</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Customer:</strong></td>
                                    <td>{{ $job->customer_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Specification:</strong></td>
                                    <td>
                                        @if(strlen($job->specification) > 100)
                                            {{ substr($job->specification, 0, 100) }}...
                                        @else
                                            {{ $job->specification }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Attachment:</strong></td>
                                    <td>
                                        @if($job->attachment)
                                            <a href="/sipo_krisan/public/storage/{{ $job->attachment }}" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-file-pdf"></i> Download
                                            </a>
                                        @else
                                            <span class="text-muted">No attachment</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Edit Planning Form -->
                    <form id="rndPlanningEditForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Edit Process Planning</h5>
                                <p class="text-muted">Modify the existing process planning below:</p>
                            </div>
                        </div>

                        <div id="processesContainer">
                            <!-- Process rows will be loaded here -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="addProcessRow()">
                                    <i class="mdi mdi-plus"></i> Add Process
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Update Planning
                                </button>
                                <a href="{{ route('development.rnd-planning.list') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

@section('scripts')
<script>
    let processCounter = 0;

    $(document).ready(function() {
        // Load existing processes
        loadExistingProcesses();

        // Handle form submission
        $('#rndPlanningEditForm').on('submit', function(e) {
            e.preventDefault();
            submitPlanning();
        });
    });

    function loadExistingProcesses() {
        @if($job->processes->count() > 0)
            @foreach($job->processes->sortBy('process_order') as $process)
                addProcessRow(
                    '{{ $process->process_name }}',
                    '{{ $process->department_id }}',
                    '{{ $process->assigned_user_id }}',
                    '{{ $process->estimated_duration }}'
                );
            @endforeach
        @endif
    }

    function addProcessRow(processName = '', departmentId = '', assignedUserId = '', duration = '') {
        processCounter++;
        const processRow = `
            <div class="row mb-3 process-row" id="process_row_${processCounter}">
                <div class="col-md-3">
                    <label class="form-label">Process Name</label>
                    <input type="text" class="form-control" id="process_name_${processCounter}" 
                           name="processes[${processCounter}][process_name]" required
                           placeholder="e.g., Design Review, Material Testing" value="${processName}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Department</label>
                    <select class="form-control" id="department_${processCounter}" 
                            name="processes[${processCounter}][department]" required>
                        <option value="">Select Department</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->id }}" ${departmentId === '{{ $divisi->id }}' ? 'selected' : ''}>
                                {{ $divisi->divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Assigned User</label>
                    <select class="form-control" id="assigned_user_${processCounter}" 
                            name="processes[${processCounter}][assigned_user_id]" required>
                        <option value="">Select User</option>
                        @foreach(App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" ${assignedUserId === '{{ $user->id }}' ? 'selected' : ''}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Duration (hours)</label>
                    <input type="number" class="form-control" id="duration_${processCounter}" 
                           name="processes[${processCounter}][estimated_duration]" required
                           min="1" max="365" placeholder="e.g., 5" value="${duration}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sequence</label>
                    <input type="number" class="form-control" id="sequence_${processCounter}" 
                           name="processes[${processCounter}][process_order]" required
                           min="1" value="${processCounter}" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block" 
                            onclick="removeProcessRow(${processCounter})" 
                            ${processCounter === 1 ? 'disabled' : ''}>
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#processesContainer').append(processRow);
        updateSequenceNumbers();
    }

    function removeProcessRow(counter) {
        if (processCounter > 1) {
            $(`#process_row_${counter}`).remove();
            processCounter--;
            updateSequenceNumbers();
        }
    }

    function updateSequenceNumbers() {
        $('.process-row').each(function(index) {
            const sequenceInput = $(this).find('input[name*="[process_order]"]');
            sequenceInput.val(index + 1);
        });
    }

    function validateForm() {
        // Check if at least one process is added
        if (processCounter === 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Minimal harus ada 1 proses',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Validate all required fields
        let isValid = true;
        $('.process-row input[required], .process-row select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Error!',
                text: 'Mohon lengkapi semua field yang required',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }

        return true;
    }

    function submitPlanning() {
        // Validate form
        if (!validateForm()) {
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Updating Planning...',
            text: 'Please wait while we update your process planning',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const formData = new FormData($('#rndPlanningEditForm')[0]);

        // Submit via AJAX
        $.ajax({
            url: '{{ route("development.rnd-planning.update", $job->id) }}',
            type: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect to planning list
                        window.location.href = '{{ route("development.rnd-planning.list") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat update planning';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
@endsection

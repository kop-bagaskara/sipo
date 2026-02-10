@extends('main.layouts.main')
@section('title')
    RnD Planning Form - {{ $job->job_code }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .job-info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #28a745;
        }

        .process-form {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .process-row {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .process-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-add-process {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-process:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-remove-process {
            background: #dc3545;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .btn-remove-process:hover {
            background: #c82333;
        }

        .btn-submit {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .sequence-badge {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
@endsection
@section('page-title')
    RnD Planning Form
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">RnD Planning Form</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('development.rnd-planning.list') }}">RnD Planning</a></li>
                    <li class="breadcrumb-item active">Planning Form</li>
                </ol>
            </div>
        </div>

        <div class="page-header">
            <h3 class="text-white mb-0">
                <i class="mdi mdi-clipboard-text"></i> RnD Planning Form
            </h3>
            <p class="mb-0 mt-2">Buat planning proses untuk job development</p>
        </div>

        <!-- Job Information Card -->
        <div class="job-info-card">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-success mb-3">
                        <i class="mdi mdi-information"></i> Job Information
                    </h5>
                    <div class="row">
                        <div class="col-6">
                            <strong>Job Code:</strong><br>
                            <span class="text-primary">{{ $job->job_code }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Job Name:</strong><br>
                            <span>{{ $job->job_name }}</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <strong>Type:</strong><br>
                            <span class="badge badge-{{ $job->type === 'proof' ? 'info' : 'purple' }}">
                                {{ $job->type === 'proof' ? 'Proof (Normal)' : 'Trial Khusus' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <strong>Priority:</strong><br>
                            <span class="badge badge-{{ $job->priority === 'high' ? 'danger' : ($job->priority === 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($job->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-success mb-3">
                        <i class="mdi mdi-account"></i> Customer & Marketing
                    </h5>
                    <div class="row">
                        <div class="col-6">
                            <strong>Customer:</strong><br>
                            <span>{{ $job->customer_name ?: '-' }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Marketing User:</strong><br>
                            <span>{{ $job->marketingUser->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <strong>Specification:</strong><br>
                            <small class="text-muted">{{ strlen($job->specification) > 100 ? substr($job->specification, 0, 100) . '...' : $job->specification }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Planning Form -->
        <div class="process-form">
            <h4 class="text-success mb-4">
                <i class="mdi mdi-clipboard-list"></i> Process Planning
            </h4>
            
            <form id="rndPlanningForm">
                @csrf
                <div id="processesContainer">
                    <!-- Process rows will be added here -->
                </div>

                <div class="text-center mb-4">
                    <button type="button" class="btn btn-add-process" onclick="addProcessRow()">
                        <i class="mdi mdi-plus-circle"></i> Add Process
                    </button>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">
                        <i class="mdi mdi-content-save"></i> Save Planning
                    </button>
                </div>
            </form>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            let processCounter = 0;

            $(document).ready(function() {
                // Add initial process row
                addProcessRow();

                // Handle form submission
                $('#rndPlanningForm').on('submit', function(e) {
                    e.preventDefault();
                    submitPlanning();
                });
            });

            function addProcessRow() {
                processCounter++;
                const processRow = `
                    <div class="row mb-3 process-row" id="process_row_${processCounter}">
                        <div class="col-md-2">
                            <label class="form-label">Process Name</label>
                            <input type="text" class="form-control" id="process_name_${processCounter}" 
                                   name="processes[${processCounter}][process_name]" required
                                   placeholder="e.g., Design Review, Material Testing">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Process Type</label>
                            <select class="form-control" id="process_type_${processCounter}" 
                                    name="processes[${processCounter}][process_type]" required>
                                <option value="">Select Type</option>
                                <option value="normal">Normal Process</option>
                                <option value="ppic">PPIC (Production Planning)</option>
                                <option value="purchasing">Purchasing</option>
                                <option value="qc">Quality Control</option>
                                <option value="rnd_verification">RnD Verification</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Department</label>
                            <select class="form-control" id="department_${processCounter}" 
                                    name="processes[${processCounter}][department]" required>
                                <option value="">Select Department</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Assigned User</label>
                            <select class="form-control" id="assigned_user_${processCounter}" 
                                    name="processes[${processCounter}][assigned_user_id]" required>
                                <option value="">Select User</option>
                                @foreach(App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Duration (hours)</label>
                            <input type="number" class="form-control" id="duration_${processCounter}" 
                                   name="processes[${processCounter}][estimated_duration]" required
                                   min="1" max="365" placeholder="e.g., 5">
                        </div>
                        <div class="col-md-1">
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
            }

            function removeProcessRow(processId) {
                if (processCounter > 1) {
                    $(`#process_row_${processId}`).remove();
                    processCounter--;
                    
                    // Reorder sequence numbers
                    $('.process-row').each(function(index) {
                        const newSequence = index + 1;
                        $(this).find('.sequence-badge').text(newSequence);
                        $(this).find('input[name*="[process_order]"]').val(newSequence);
                        
                        // Update array index in name attributes
                        $(this).find('input, select').each(function() {
                            const name = $(this).attr('name');
                            if (name) {
                                const newName = name.replace(/\[\d+\]/, `[${newSequence}]`);
                                $(this).attr('name', newName);
                            }
                        });
                        
                        // Update IDs
                        $(this).attr('id', `process_${newSequence}`);
                        $(this).find('input, select, label').each(function() {
                            const id = $(this).attr('id');
                            if (id) {
                                const newId = id.replace(/\d+$/, newSequence);
                                $(this).attr('id', newId);
                            }
                        });
                        
                        // Update onclick
                        $(this).find('.btn-remove-process').attr('onclick', `removeProcessRow(${newSequence})`);
                    });
                    
                    // Enable/disable remove buttons
                    $('.btn-remove-process').prop('disabled', false);
                    $('.btn-remove-process:first').prop('disabled', true);
                }
            }

            function submitPlanning() {
                // Validate form
                if (!validateForm()) {
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Saving Planning...',
                    text: 'Please wait while we save your process planning',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get form data
                const formData = new FormData($('#rndPlanningForm')[0]);

                // Determine if this is create or update
                const isEdit = {{ $job->status === 'planning' ? 'true' : 'false' }};
                const url = isEdit 
                    ? '{{ route("development.rnd-planning.update", $job->id) }}'
                    : '{{ route("development.rnd-planning.store", $job->id) }}';
                const method = isEdit ? 'PUT' : 'POST';

                // Submit via AJAX
                $.ajax({
                    url: url,
                    type: method,
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
                        let errorMessage = 'Terjadi kesalahan saat menyimpan planning';
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

            function validateForm() {
                let isValid = true;
                let errorMessage = '';

                // Check if at least one process is added
                if (processCounter === 0) {
                    errorMessage = 'Minimal harus ada 1 proses';
                    isValid = false;
                }

                // Validate each process row
                $('.process-row').each(function() {
                    const processName = $(this).find('input[name*="[process_name]"]').val();
                    const department = $(this).find('select[name*="[department]"]').val();
                    const assignedUser = $(this).find('select[name*="[assigned_user_id]"]').val();
                    const duration = $(this).find('input[name*="[estimated_duration]"]').val();

                    if (!processName || !department || !assignedUser || !duration) {
                        errorMessage = 'Semua field harus diisi untuk setiap proses';
                        isValid = false;
                        return false;
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        title: 'Validation Error!',
                        text: errorMessage,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }

                return isValid;
            }
        </script>
    @endsection

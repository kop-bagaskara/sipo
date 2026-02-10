@extends('main.layouts.main')
@section('title')
    Input Job Development
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .form-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-submit:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .file-upload-area.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-high {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }

        .priority-medium {
            background-color: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffcc02;
        }

        .priority-low {
            background-color: #e8f5e8;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
@section('page-title')
    Data Development
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Development</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="form-header">
                    <h4 style="text-align: center;color: white;"><i class="mdi mdi-plus-circle"></i> Form Input Job Development</h4>
                    <p class="mb-0">Job Development dapat diinput melalui form ini</p>
                </div>

                <form id="jobDevelopmentForm" enctype="multipart/form-data" method="POST" action="javascript:void(0);">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="job_name">Job Name *</label>
                                <input type="text"
                                    class="form-control" id="job_name"
                                    name="job_name"
                                    placeholder="Masukkan nama job development" required>
                                <div class="invalid-feedback" id="job_name_error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Tipe Job *</label>
                                <select class="form-control" id="type"
                                    name="type" required>
                                    <option value="">Pilih tipe job</option>
                                    <option value="proof">Proof (Normal)</option>
                                    <option value="trial_khusus">Trial Khusus</option>
                                </select>
                                <div class="invalid-feedback" id="type_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority">Prioritas *</label>
                                <select class="form-control" id="priority"
                                    name="priority" required>
                                    <option value="">Pilih prioritas</option>
                                    <option value="high">High (Tinggi)</option>
                                    <option value="medium">Medium (Sedang)</option>
                                    <option value="low">Low (Rendah)</option>
                                </select>
                                <div class="invalid-feedback" id="priority_error"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">Nama Customer</label>
                                <input type="text"
                                    class="form-control"
                                    id="customer_name" name="customer_name"
                                    placeholder="Masukkan nama customer (opsional)">
                                <div class="invalid-feedback" id="customer_name_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="attachment">Lampiran</label>
                                <div class="file-upload-area"
                                    onclick="document.getElementById('attachment').click()">
                                    <i class="mdi mdi-cloud-upload"
                                        style="font-size: 3rem; color: #6c757d;"></i>
                                    <p class="mt-2 mb-1">Klik untuk upload file</p>
                                    <small class="text-muted">PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                    <input type="file" id="attachment" name="attachment"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;"
                                        onchange="updateFileName(this)">
                                </div>
                                <div id="file-info" class="mt-2" style="display: none;">
                                    <small class="text-success">
                                        <i class="mdi mdi-check-circle"></i>
                                        <span id="file-name"></span>
                                    </small>
                                </div>
                                <div class="invalid-feedback" id="attachment_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="specification">Spesifikasi Lengkap *</label>
                        <textarea class="form-control" id="specification" name="specification"
                            rows="6" placeholder="Masukkan spesifikasi lengkap job development" required></textarea>
                        <div class="invalid-feedback" id="specification_error"></div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="mdi mdi-send"></i> Submit Job Development
                        </button>
                        <a href="{{ route('development.marketing-jobs.list') }}"
                            class="btn btn-secondary ml-2">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            // File upload functions
            function updateFileName(input) {
                const fileInfo = document.getElementById('file-info');
                const fileName = document.getElementById('file-name');

                if (input.files && input.files[0]) {
                    fileName.textContent = input.files[0].name;
                    fileInfo.style.display = 'block';
                } else {
                    fileInfo.style.display = 'none';
                }
            }

            // Drag and drop functionality
            $(document).ready(function() {
                const fileUploadArea = document.querySelector('.file-upload-area');
                const fileInput = document.getElementById('attachment');

                if (fileUploadArea && fileInput) {
                    fileUploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        fileUploadArea.classList.add('dragover');
                    });

                    fileUploadArea.addEventListener('dragleave', () => {
                        fileUploadArea.classList.remove('dragover');
                    });

                    fileUploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        fileUploadArea.classList.remove('dragover');

                        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                            fileInput.files = e.dataTransfer.files;
                            updateFileName(fileInput);
                        }
                    });
                }

                // Form submission with AJAX
                console.log('Form ready, setting up AJAX submission...');

                $('#jobDevelopmentForm').on('submit', function(e) {
                    console.log('Form submitted, preventing default...');
                    e.preventDefault();
                    e.stopPropagation();

                    // Clear previous error states
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').hide();

                    // Show loading state
                    const submitBtn = $('#submitBtn');
                    const originalText = submitBtn.html();
                    submitBtn.prop('disabled', true);
                    submitBtn.html('<span class="loading-spinner"></span>Processing...');

                    // Create FormData object
                    const formData = new FormData(this);
                    console.log('FormData created, sending AJAX request...');

                    $.ajax({
                        url: '{{ route("development.marketing-input.store") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Success response:', response);
                            // Success alert
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Job Development berhasil dibuat!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745'
                            }).then((result) => {
                                // Redirect to list page
                                window.location.href = '{{ route('development.rnd-workspace.index') }}';
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log('Error response:', xhr, status, error);
                            submitBtn.prop('disabled', false);
                            submitBtn.html(originalText);

                            if (xhr.status === 422) {
                                // Validation errors
                                const errors = xhr.responseJSON.errors;
                                console.log('Validation errors:', errors);

                                // Display validation errors
                                Object.keys(errors).forEach(function(key) {
                                    const field = $('#' + key);
                                    const errorDiv = $('#' + key + '_error');

                                    if (field.length && errorDiv.length) {
                                        field.addClass('is-invalid');
                                        errorDiv.text(errors[key][0]).show();
                                    }
                                });

                                // Show error alert
                                Swal.fire({
                                    title: 'Validasi Error!',
                                    text: 'Mohon periksa kembali form yang diisi',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545'
                                });
                            } else {
                                // General error
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        }
                    });

                    return false;
                });
            });
        </script>
    @endsection

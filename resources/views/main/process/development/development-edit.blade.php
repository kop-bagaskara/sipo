@extends('main.layouts.main')
@section('title')
    Edit Development Job - Job Development
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .form-header {
            background: linear-gradient(135deg, #28a745, #20c997);
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

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .file-upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #0056b3;
            background: #e3f2fd;
        }

        .file-upload-area.dragover {
            border-color: #28a745;
            background: #e8f5e8;
        }

        .file-list {
            margin-top: 15px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .file-item .file-info {
            display: flex;
            align-items: center;
        }

        .file-item .file-info i {
            margin-right: 10px;
            color: #007bff;
        }

        .file-item .remove-file {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
        }

        .file-item .remove-file:hover {
            color: #c82333;
        }

        .job-order-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .job-order-item .remove-job-order {
            color: #dc3545;
            cursor: pointer;
            float: right;
            font-size: 1.2rem;
        }

        .job-order-item .remove-job-order:hover {
            color: #c82333;
        }

        .unit-job-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .unit-job-input.valid {
            border-color: #28a745;
        }

        .unit-job-input.invalid {
            border-color: #dc3545;
        }

        .unit-job-dropdown {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .unit-job-dropdown.valid {
            border-color: #28a745;
        }

        .unit-job-dropdown.invalid {
            border-color: #dc3545;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .change-percentage-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .change-percentage-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            border-color: #dee2e6;
        }

        .required {
            color: #dc3545;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }

            .form-container {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="form-container">
                    <div class="form-header">
                        <h4><i class="fas fa-edit"></i> Edit Development Job - {{ $job->job_code }}</h4>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form id="updateJobDevelopment" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job_code">Job Code <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="job_code" name="job_code"
                                           value="{{ $job->job_code }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job_name">Job Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="job_name" name="job_name"
                                           value="{{ $job->job_name }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                           value="{{ $job->tanggal }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job_deadline">Job Deadline <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="job_deadline" name="job_deadline"
                                           value="{{ $job->job_deadline }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer">Customer <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="customer" name="customer"
                                           value="{{ $job->customer }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product">Product <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="product" name="product"
                                           value="{{ $job->product }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_design">Kode Design</label>
                                    <input type="text" class="form-control" id="kode_design" name="kode_design"
                                           value="{{ $job->kode_design }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dimension">Dimension <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="dimension" name="dimension"
                                           value="{{ $job->dimension }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="material">Material <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="material" name="material"
                                           value="{{ $job->material }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_color">Total Color <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="total_color" name="total_color"
                                           value="{{ $job->total_color }}" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qty_order_estimation">Qty Order Estimation <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="qty_order_estimation" name="qty_order_estimation"
                                           value="{{ $job->qty_order_estimation }}" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job_type">Job Type <span class="required">*</span></label>
                                    <select class="form-select" id="job_type" name="job_type" required>
                                        <option value="new" {{ $job->job_type == 'new' ? 'selected' : '' }}>Produk Baru</option>
                                        <option value="repeat" {{ $job->job_type == 'repeat' ? 'selected' : '' }}>Produk Repeat</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Change Percentage Row (hidden by default, shown for repeat) -->
                        <div class="row" id="change_percentage_row" style="{{ $job->job_type == 'repeat' ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="change_percentage">Persentase Perubahan (%)</label>
                                    <input type="number" class="form-control change-percentage-input" id="change_percentage"
                                           name="change_percentage" value="{{ $job->change_percentage }}" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <!-- Change Details Row (hidden by default, shown for repeat) -->
                        <div class="row" id="change_details_row" style="{{ $job->job_type == 'repeat' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Detail Perubahan</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="ukuran"
                                                   {{ in_array('ukuran', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Ukuran/dimensi</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="material"
                                                   {{ in_array('material', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Material</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="warna"
                                                   {{ in_array('warna', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Warna</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="finishing"
                                                   {{ in_array('finishing', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Finishing</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="struktur"
                                                   {{ in_array('struktur', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Struktur packaging</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="change_details[]" value="lainnya"
                                                   {{ in_array('lainnya', $job->change_details ?? []) ? 'checked' : '' }}>
                                            <label>Lainnya</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Order Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Job Order <span class="required">*</span></label>
                                    <div id="job_order_container">
                                        @foreach($job->job_order as $index => $jobOrder)
                                        <div class="job-order-item" data-index="{{ $index }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Jenis Pekerjaan</label>
                                                    <select class="form-select jenis-pekerjaan" name="job_order[{{ $index }}][jenis_pekerjaan]" required>
                                                        <option value="">Pilih jenis pekerjaan...</option>
                                                        @foreach($jenisPekerjaan as $jenis)
                                                            <option value="{{ $jenis->kode }}" {{ $jobOrder['jenis_pekerjaan'] == $jenis->kode ? 'selected' : '' }}>
                                                                {{ $jenis->kode }} - {{ $jenis->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Unit Job</label>
                                                    <input type="text" class="form-control unit-job-input" name="job_order[{{ $index }}][unit_job]"
                                                           value="{{ $jobOrder['unit_job'] }}" required>
                                                </div>
                                            </div>
                                            <span class="remove-job-order" onclick="removeJobOrder(this)">×</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addJobOrder()">
                                        <i class="fas fa-plus"></i> Tambah Job Order
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>File atau Data</label>
                                    <div class="file-upload-area" onclick="document.getElementById('file_data').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                        <h5>Klik untuk upload file atau drag & drop</h5>
                                        <p class="text-muted">Support: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max 10MB per file)</p>
                                        <input type="file" id="file_data" name="attachments[]" multiple style="display: none;"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </div>
                                    <div id="file_list" class="file-list">
                                        @if($job->attachment_paths)
                                            @foreach($job->attachment_paths as $index => $path)
                                                <div class="file-item">
                                                    <div class="file-info">
                                                        <i class="fas fa-file"></i>
                                                        <span>{{ basename($path) }}</span>
                                                    </div>
                                                    <span class="remove-file" onclick="removeExistingFile({{ $index }})">×</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prioritas_job">Prioritas Job <span class="required">*</span></label>
                                    <select class="form-select" id="prioritas_job" name="prioritas_job" required>
                                        <option value="low" {{ $job->prioritas_job == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $job->prioritas_job == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $job->prioritas_job == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ $job->prioritas_job == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ $job->catatan }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-secondary me-3" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Update Job
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Show/hide change percentage and details based on job type
            $('select[name="job_type"]').on('change', function() {
                var jobType = $(this).val();
                console.log('Job type changed to:', jobType);

                if (jobType === 'repeat') {
                    console.log('Showing change percentage and details rows');
                    $('#change_percentage_row').show();
                    $('#change_details_row').show();
                    // Update row numbers for "Produk Repeat"
                    $('#change_percentage_row td:first').html('<b>8. </b>');
                    $('#change_details_row td:first').html('<b>9. </b>');
                    $('tr:has(td:contains("Job Order")) td:first').html('<b>10. </b>'); // Job Order
                    $('tr:has(td:contains("File atau Data")) td:first').html('<b>11. </b>'); // File atau Data
                    $('tr:has(td:contains("Prioritas")) td:first').html('<b>12. </b>'); // Prioritas
                    $('tr:has(td:contains("Lampiran")) td:first').html('<b>13. </b>'); // Lampiran
                    $('tr:has(td:contains("Catatan")) td:first').html('<b>14. </b>'); // Catatan
                } else {
                    console.log('Hiding change percentage and details rows');
                    $('#change_percentage_row').hide();
                    $('#change_details_row').hide();
                    // Reset row numbers - fix the numbering sequence for "Produk Baru"
                    // Use more specific selectors to target the correct rows
                    $('tr:has(td:contains("Job Order")) td:first').html('<b>8. </b>'); // Job Order
                    $('tr:has(td:contains("File atau Data")) td:first').html('<b>9. </b>'); // File atau Data
                    $('tr:has(td:contains("Prioritas")) td:first').html('<b>10. </b>'); // Prioritas
                    $('tr:has(td:contains("Lampiran")) td:first').html('<b>11. </b>'); // Lampiran
                    $('tr:has(td:contains("Catatan")) td:first').html('<b>12. </b>'); // Catatan
                }
            });

            var updateJobDevelopment = "{{ route('development.update', $job->id) }}";

            $('#updateJobDevelopment').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                // Add selected files to FormData
                var fileInput = document.getElementById('file_data');
                if (fileInput.files.length > 0) {
                    for (var i = 0; i < fileInput.files.length; i++) {
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }

                // Show loading
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                $('.form-container').addClass('loading');

                $.ajax({
                    url: updateJobDevelopment,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('development.marketing-jobs.list') }}";
                                }
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
                        var errorMessage = 'Terjadi kesalahan saat mengupdate job.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Update Job');
                        $('.form-container').removeClass('loading');
                    }
                });
            });

            // File upload handling
            $('#file_data').on('change', function() {
                handleFileSelection(this.files);
            });

            // Drag and drop handling
            $('.file-upload-area').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            $('.file-upload-area').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            $('.file-upload-area').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                var files = e.originalEvent.dataTransfer.files;
                handleFileSelection(files);
            });

            // Initialize unit job dropdowns for existing job orders
            $('.jenis-pekerjaan').each(function() {
                var $this = $(this);
                if ($this.val()) {
                    updateUnitJob($this);
                }
            });
        });

        var selectedFiles = [];
        var existingFiles = @json($job->attachment_paths ?? []);

        function handleFileSelection(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File terlalu besar!',
                        text: 'File ' + file.name + ' melebihi 10MB',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    continue;
                }

                // Validate file type
                var allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        title: 'Tipe file tidak didukung!',
                        text: 'File ' + file.name + ' tidak didukung',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    continue;
                }

                selectedFiles.push(file);
            }

            showSelectedFiles();
        }

        function showSelectedFiles() {
            var fileList = $('#file_list');
            fileList.empty();

            // Show existing files
            existingFiles.forEach(function(file, index) {
                var fileItem = $('<div class="file-item">' +
                    '<div class="file-info">' +
                        '<i class="fas fa-file"></i>' +
                        '<span>' + file.split('/').pop() + '</span>' +
                    '</div>' +
                    '<span class="remove-file" onclick="removeExistingFile(' + index + ')">×</span>' +
                '</div>');
                fileList.append(fileItem);
            });

            // Show new files
            selectedFiles.forEach(function(file, index) {
                var fileItem = $('<div class="file-item">' +
                    '<div class="file-info">' +
                        '<i class="fas fa-file"></i>' +
                        '<span>' + file.name + '</span>' +
                    '</div>' +
                    '<span class="remove-file" onclick="removeFile(' + index + ')">×</span>' +
                '</div>');
                fileList.append(fileItem);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            showSelectedFiles();
        }

        function removeExistingFile(index) {
            existingFiles.splice(index, 1);
            showSelectedFiles();
        }

        function addJobOrder() {
            var container = $('#job_order_container');
            var index = container.children().length;

            var jobOrderHtml = '<div class="job-order-item" data-index="' + index + '">' +
                '<div class="row">' +
                    '<div class="col-md-6">' +
                        '<label>Jenis Pekerjaan</label>' +
                        '<select class="form-select jenis-pekerjaan" name="job_order[' + index + '][jenis_pekerjaan]" required>' +
                            '<option value="">Pilih jenis pekerjaan...</option>' +
                            @foreach($jenisPekerjaan as $jenis)
                            '<option value="{{ $jenis->kode }}">{{ $jenis->kode }} - {{ $jenis->nama }}</option>' +
                            @endforeach
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Unit Job</label>' +
                        '<input type="text" class="form-control unit-job-input" name="job_order[' + index + '][unit_job]" required>' +
                    '</div>' +
                '</div>' +
                '<span class="remove-job-order" onclick="removeJobOrder(this)">×</span>' +
            '</div>';

            container.append(jobOrderHtml);

            // Initialize Select2 for new select
            container.find('.jenis-pekerjaan').last().select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Add change event for new select
            container.find('.jenis-pekerjaan').last().on('change', function() {
                updateUnitJob($(this));
            });
        }

        function removeJobOrder(element) {
            $(element).closest('.job-order-item').remove();
        }

        function updateUnitJob(selectElement) {
            var jenisPekerjaan = selectElement.val();
            var unitJobInput = selectElement.closest('.job-order-item').find('.unit-job-input');

            if (!jenisPekerjaan) {
                resetUnitJobField(unitJobInput);
                return;
            }

            $.ajax({
                url: "{{ route('development.job-order.get-unit-job') }}",
                type: 'POST',
                data: {
                    jenis_pekerjaan: jenisPekerjaan,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.unit_jobs && response.unit_jobs.length > 0) {
                            createUnitJobDropdown(unitJobInput, response.unit_jobs);
                        } else {
                            unitJobInput.val(jenisPekerjaan).addClass('valid').removeClass('invalid');
                        }
                    } else {
                        unitJobInput.val(jenisPekerjaan).addClass('valid').removeClass('invalid');
                    }
                },
                error: function() {
                    unitJobInput.val(jenisPekerjaan).addClass('valid').removeClass('invalid');
                }
            });
        }

        function resetUnitJobField(unitJobInput) {
            unitJobInput.val('').removeClass('valid invalid');
            var existingDropdown = unitJobInput.siblings('.unit-job-dropdown');
            if (existingDropdown.length) {
                existingDropdown.remove();
            }
        }

        function createUnitJobDropdown(unitJobInput, unitJobs) {
            // Remove existing dropdown if any
            var existingDropdown = unitJobInput.siblings('.unit-job-dropdown');
            if (existingDropdown.length) {
                existingDropdown.remove();
            }

            // Create new dropdown
            var dropdown = $('<select class="form-control unit-job-dropdown">' +
                '<option value="">Pilih unit job...</option>' +
                unitJobs.map(function(unit) {
                    return '<option value="' + unit + '">' + unit + '</option>';
                }).join('') +
            '</select>');

            // Insert dropdown after input
            unitJobInput.after(dropdown);

            // Hide original input
            unitJobInput.hide();

            // Handle dropdown change
            dropdown.on('change', function() {
                var selectedValue = $(this).val();
                unitJobInput.val(selectedValue);

                if (selectedValue) {
                    $(this).addClass('valid').removeClass('invalid');
                    unitJobInput.addClass('valid').removeClass('invalid');
                } else {
                    $(this).removeClass('valid invalid');
                    unitJobInput.removeClass('valid invalid');
                }
            });

            // Initialize Select2 for dropdown
            dropdown.select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
    </script>
@endsection

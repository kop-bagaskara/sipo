@extends('main.layouts.main')
@section('title')
    View Development Job - {{ $job->job_code }}
@endsection
@section('css')
    <style>
        .view-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .view-header {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .view-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .info-section h5 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 200px;
            margin-right: 15px;
        }

        .info-value {
            color: #212529;
            flex: 1;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-open {
            background: #d4edda;
            color: #155724;
        }

        .status-planning {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-finished {
            background: #d4edda;
            color: #155724;
        }

        .priority-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-low {
            background: #e2e3e5;
            color: #383d41;
        }

        .priority-medium {
            background: #fff3cd;
            color: #856404;
        }

        .priority-high {
            background: #f8d7da;
            color: #721c24;
        }

        .priority-urgent {
            background: #f5c6cb;
            color: #721c24;
        }

        .type-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-new {
            background: #d1ecf1;
            color: #0c5460;
        }

        .type-repeat {
            background: #fff3cd;
            color: #856404;
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
            border: 1px solid #dee2e6;
        }

        .file-item .file-info {
            display: flex;
            align-items: center;
        }

        .file-item .file-info i {
            margin-right: 10px;
            color: #007bff;
        }

        .file-item .file-download {
            color: #007bff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            background: #e3f2fd;
            transition: all 0.3s ease;
        }

        .file-item .file-download:hover {
            background: #bbdefb;
            color: #0056b3;
        }

        .job-order-list {
            margin-top: 15px;
        }

        .job-order-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .job-order-item .job-order-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .job-order-item .job-order-label {
            font-weight: 600;
            color: #495057;
        }

        .job-order-item .job-order-value {
            color: #212529;
        }

        .checkbox-list {
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
            pointer-events: none;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .action-buttons .btn {
            margin: 0 10px;
        }

        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }

            .view-container {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="view-container">
                    <div class="view-header">
                        <h4><i class="fas fa-eye"></i> View Development Job - {{ $job->job_code }}</h4>
                    </div>

                    <!-- Basic Information -->
                    <div class="info-section">
                        <h5><i class="fas fa-info-circle"></i> Informasi Dasar</h5>
                        <div class="info-row">
                            <div class="info-label">Job Code:</div>
                            <div class="info-value"><strong>{{ $job->job_code }}</strong></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Job Name:</div>
                            <div class="info-value">{{ $job->job_name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal:</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($job->tanggal)->format('d/m/Y') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Job Deadline:</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($job->job_deadline)->format('d/m/Y') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Customer:</div>
                            <div class="info-value">{{ $job->customer }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Product:</div>
                            <div class="info-value">{{ $job->product }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Kode Design:</div>
                            <div class="info-value">{{ $job->kode_design ?: '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Dimension:</div>
                            <div class="info-value">{{ $job->dimension }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Material:</div>
                            <div class="info-value">{{ $job->material }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Total Color:</div>
                            <div class="info-value">{{ $job->total_color }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Qty Order Estimation:</div>
                            <div class="info-value">{{ number_format($job->qty_order_estimation) }}</div>
                        </div>
                    </div>

                    <!-- Job Type & Status -->
                    <div class="info-section">
                        <h5><i class="fas fa-tags"></i> Status & Prioritas</h5>
                        <div class="info-row">
                            <div class="info-label">Job Type:</div>
                            <div class="info-value">
                                <span class="type-badge type-{{ $job->job_type }}">
                                    {{ $job->job_type == 'new' ? 'Produk Baru' : 'Produk Repeat' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Status Job:</div>
                            <div class="info-value">
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $job->status_job)) }}">
                                    {{ $job->status_job }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Prioritas Job:</div>
                            <div class="info-value">
                                <span class="priority-badge priority-{{ $job->prioritas_job }}">
                                    {{ ucfirst($job->prioritas_job) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Dibuat Oleh:</div>
                            <div class="info-value">{{ $job->marketingUser->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Dibuat Tanggal:</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($job->created_at)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <!-- Change Information (for repeat products) -->
                    @if($job->job_type == 'repeat')
                    <div class="info-section">
                        <h5><i class="fas fa-exchange-alt"></i> Informasi Perubahan</h5>
                        <div class="info-row">
                            <div class="info-label">Persentase Perubahan:</div>
                            <div class="info-value">{{ $job->change_percentage ? $job->change_percentage . '%' : '-' }}</div>
                        </div>
                        @if($job->change_details && count($job->change_details) > 0)
                        <div class="info-row">
                            <div class="info-label">Detail Perubahan:</div>
                            <div class="info-value">
                                <div class="checkbox-list">
                                    @foreach(['ukuran' => 'Ukuran/dimensi', 'material' => 'Material', 'warna' => 'Warna', 'finishing' => 'Finishing', 'struktur' => 'Struktur packaging', 'lainnya' => 'Lainnya'] as $key => $label)
                                        <div class="checkbox-item">
                                            <input type="checkbox" {{ in_array($key, $job->change_details) ? 'checked' : '' }} disabled>
                                            <label>{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Job Order -->
                    <div class="info-section">
                        <h5><i class="fas fa-tasks"></i> Job Order</h5>
                        @if($job->job_order && count($job->job_order) > 0)
                            <div class="job-order-list">
                                @foreach($job->job_order as $index => $jobOrder)
                                <div class="job-order-item">
                                    <div class="job-order-info">
                                        <div class="job-order-label">Jenis Pekerjaan:</div>
                                        <div class="job-order-value">{{ $jobOrder['jenis_pekerjaan'] }}</div>
                                    </div>
                                    <div class="job-order-info">
                                        <div class="job-order-label">Unit Job:</div>
                                        <div class="job-order-value">{{ $jobOrder['unit_job'] }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-data">Tidak ada job order</div>
                        @endif
                    </div>

                    <!-- File Attachments -->
                    <div class="info-section">
                        <h5><i class="fas fa-paperclip"></i> File Attachments</h5>
                        @if($job->attachment_paths && count($job->attachment_paths) > 0)
                            <div class="file-list">
                                @foreach($job->attachment_paths as $path)
                                <div class="file-item">
                                    <div class="file-info">
                                        <i class="fas fa-file"></i>
                                        <span>{{ basename($path) }}</span>
                                    </div>
                                    <a href="{{ asset($path) }}" target="_blank" class="file-download">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-data">Tidak ada file attachment</div>
                        @endif
                    </div>

                    <!-- Notes -->
                    @if($job->catatan)
                    <div class="info-section">
                        <h5><i class="fas fa-sticky-note"></i> Catatan</h5>
                        <div class="info-value">{{ $job->catatan }}</div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('development.marketing-jobs.list') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>

                        @if($job->marketing_user_id == auth()->id() && $job->status_job == 'OPEN')
                            <a href="{{ route('development.edit', $job->id) }}" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <button type="button" class="btn-delete" onclick="deleteJob({{ $job->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        function deleteJob(jobId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Job ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('development.destroy', '') }}/" + jobId,
                        type: 'DELETE',
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
                            var errorMessage = 'Terjadi kesalahan saat menghapus job.';
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
            });
        }
    </script>
@endsection

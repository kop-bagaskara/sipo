@extends('main.layouts.main')
@section('title')
    RnD Production Approval
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }
        .approval-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        .approval-card h6 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }
        .production-schedule-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .production-schedule-item h6 {
            color: #495057;
            margin-bottom: 15px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-badge.approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-approve {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
        }
        .btn-approve:hover, .btn-reject:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .production-details {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .production-details h6 {
            color: #495057;
            margin-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #495057;
        }
    </style>
@endsection
@section('page-title')
    RnD Production Approval
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">RnD Production Approval</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.index') }}">RnD Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.view', $job->id) }}">Detail Job</a></li>
                <li class="breadcrumb-item active">Production Approval</li>
            </ol>
        </div>
    </div>

    <!-- Header Info -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h3 style="color: white;">Production Approval - {{ $job->job_name }}</h3>
                <p class="mb-0">Job Code: {{ $job->job_code }} | Customer: {{ $job->customer }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="approval-card">
                <h6><i class="mdi mdi-clipboard-check"></i> Production Reports - Completed Processes</h6>

                @if($job->productionSchedules->count() > 0)
                    @foreach($job->productionSchedules as $index => $schedule)
                        @if($schedule->status === 'completed')
                            <div class="production-schedule-item">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6><i class="mdi mdi-printer"></i> Production Schedule #{{ $index + 1 }} - {{ $schedule->proses ?? 'Process' }}</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Tanggal & Waktu:</strong> {{ $schedule->production_date_time }}<br>
                                                <strong>Mesin:</strong> {{ $schedule->machine_name }} ({{ $schedule->machine_code }})<br>
                                                <strong>Dibuat Oleh:</strong> {{ $schedule->createdBy->name ?? '-' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Status:</strong> <span class="status-badge completed">{{ $schedule->status_label }}</span><br>
                                                <strong>RnD Approval:</strong> 
                                                @if($schedule->rnd_approval_status === 'approved')
                                                    <span class="status-badge approved">{{ $schedule->rnd_approval_status_label }}</span>
                                                @elseif($schedule->rnd_approval_status === 'rejected')
                                                    <span class="status-badge rejected">{{ $schedule->rnd_approval_status_label }}</span>
                                                @else
                                                    <span class="status-badge pending">{{ $schedule->rnd_approval_status_label }}</span>
                                                @endif
                                                <br>
                                                <strong>Revisi:</strong> {{ $schedule->revision_count }}x
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if($schedule->rnd_approval_status === 'pending')
                                            <button type="button" class="btn btn-approve btn-sm mb-2"
                                                    onclick="showApprovalModal({{ $schedule->id }}, 'approved')">
                                                <i class="mdi mdi-check"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-reject btn-sm mb-2"
                                                    onclick="showApprovalModal({{ $schedule->id }}, 'rejected')">
                                                <i class="mdi mdi-close"></i> Reject
                                            </button>
                                        @else
                                            <div class="text-muted">
                                                <small>
                                                    @if($schedule->rnd_approval_status === 'approved')
                                                        <i class="mdi mdi-check-circle text-success"></i> Sudah Di-approve
                                                    @elseif($schedule->rnd_approval_status === 'rejected')
                                                        <i class="mdi mdi-close-circle text-danger"></i> Sudah Di-reject
                                                    @endif
                                                    <br>
                                                    <small>Oleh: {{ $schedule->rndApprovedBy->name ?? '-' }}</small>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Production Details -->
                                @if($schedule->production_qty || $schedule->reject_qty || $schedule->production_notes)
                                    <div class="production-details">
                                        <h6><i class="mdi mdi-chart-line"></i> Production Results</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="detail-row">
                                                    <span class="detail-label">Production Qty:</span>
                                                    <span class="detail-value">{{ $schedule->production_qty ?? '-' }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Reject Qty:</span>
                                                    <span class="detail-value">{{ $schedule->reject_qty ?? '-' }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Start Time:</span>
                                                    <span class="detail-value">{{ $schedule->start_time ? $schedule->start_time->format('H:i') : '-' }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">End Time:</span>
                                                    <span class="detail-value">{{ $schedule->end_time ? $schedule->end_time->format('H:i') : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-row">
                                                    <span class="detail-label">Completion Date:</span>
                                                    <span class="detail-value">{{ $schedule->completion_date ? $schedule->completion_date->format('d/m/Y') : '-' }}</span>
                                                </div>
                                                @if($schedule->issues_found)
                                                    <div class="detail-row">
                                                        <span class="detail-label">Issues Found:</span>
                                                        <span class="detail-value">{{ $schedule->issues_found }}</span>
                                                    </div>
                                                @endif
                                                @if($schedule->recommendations)
                                                    <div class="detail-row">
                                                        <span class="detail-label">Recommendations:</span>
                                                        <span class="detail-value">{{ $schedule->recommendations }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($schedule->production_notes)
                                            <div class="mt-3">
                                                <strong>Production Notes:</strong><br>
                                                <div class="alert alert-info mt-2">{{ $schedule->production_notes }}</div>
                                            </div>
                                        @endif

                                        @if($schedule->quality_notes)
                                            <div class="mt-3">
                                                <strong>Quality Notes:</strong><br>
                                                <div class="alert alert-warning mt-2">{{ $schedule->quality_notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach

                    @if($job->productionSchedules->where('status', 'completed')->count() === 0)
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i> Belum ada production report yang selesai untuk di-approve.
                        </div>
                    @elseif($job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count() === 0)
                        <div class="alert alert-success">
                            <i class="mdi mdi-check-circle"></i> Semua production report yang selesai sudah di-approve RnD.
                        </div>
                    @endif

                    <!-- Status Summary -->
                    @if($job->productionSchedules && $job->productionSchedules->count() > 0)
                        <div class="approval-card">
                            <h6><i class="mdi mdi-chart-pie"></i> Status Summary</h6>
                            @php
                                $totalSchedules = $job->productionSchedules->count();
                                $completedSchedules = $job->productionSchedules->where('status', 'completed')->count();
                                $approvedSchedules = $job->productionSchedules->where('rnd_approval_status', 'approved')->count();
                                $pendingApprovalSchedules = $job->productionSchedules->where('status', 'completed')->where('rnd_approval_status', 'pending')->count();
                            @endphp
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-primary">{{ $totalSchedules }}</h4>
                                        <small>Total Proses</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-success">{{ $completedSchedules }}</h4>
                                        <small>Selesai Produksi</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-warning">{{ $approvedSchedules }}</h4>
                                        <small>Sudah Di-approve</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-danger">{{ $pendingApprovalSchedules }}</h4>
                                        <small>Menunggu Approval</small>
                                    </div>
                                </div>
                            </div>
                            
                            @if($completedSchedules > 0 && $completedSchedules < $totalSchedules)
                                <div class="alert alert-warning mt-3">
                                    <i class="mdi mdi-alert"></i> 
                                    <strong>Perhatian:</strong> Masih ada {{ $totalSchedules - $completedSchedules }} proses produksi yang belum selesai. 
                                    Job status akan berubah menjadi "PRODUCTION_APPROVED_BY_RND" hanya setelah <strong>SEMUA</strong> proses produksi selesai dan di-approve.
                                </div>
                            @elseif($completedSchedules === $totalSchedules && $approvedSchedules < $completedSchedules)
                                <div class="alert alert-info mt-3">
                                    <i class="mdi mdi-information"></i> 
                                    <strong>Info:</strong> Semua proses produksi sudah selesai. 
                                    Job status akan berubah menjadi "PRODUCTION_APPROVED_BY_RND" setelah semua laporan produksi di-approve.
                                </div>
                            @elseif($completedSchedules === $totalSchedules && $approvedSchedules === $completedSchedules)
                                <div class="alert alert-success mt-3">
                                    <i class="mdi mdi-check-circle"></i> 
                                    <strong>Lengkap:</strong> Semua proses produksi sudah selesai dan di-approve. 
                                    Job status sudah berubah menjadi "PRODUCTION_APPROVED_BY_RND".
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert"></i> Belum ada production schedule untuk job ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" id="approvalModalHeader">
                    <h5 class="modal-title" id="approvalModalLabel">RnD Approval</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="approvalForm">
                    @csrf
                    <input type="hidden" id="approvalScheduleId" name="schedule_id">
                    <input type="hidden" id="approvalStatus" name="rnd_approval_status">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="approvalNotes">Catatan RnD:</label>
                            <textarea class="form-control" id="approvalNotes" name="rnd_approval_notes" rows="4"
                                      placeholder="Berikan catatan untuk approval ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" id="approvalSubmitBtn">
                            <i class="mdi mdi-check"></i> Submit Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle approval form submission
            $('#approvalForm').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var scheduleId = $('#approvalScheduleId').val();
                var approvalStatus = $('#approvalStatus').val();

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan RnD approval',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('development.rnd-production-approval.approve', [$job->id, 'SCHEDULE_ID']) }}'.replace('SCHEDULE_ID', scheduleId),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var message = 'Terjadi kesalahan saat menyimpan approval';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message
                        });
                    }
                });
            });
        });

        function showApprovalModal(scheduleId, status) {
            $('#approvalScheduleId').val(scheduleId);
            $('#approvalStatus').val(status);
            $('#approvalNotes').val('');

            var modalHeader = $('#approvalModalHeader');
            var submitBtn = $('#approvalSubmitBtn');

            if (status === 'approved') {
                modalHeader.removeClass('bg-danger').addClass('bg-success');
                submitBtn.removeClass('btn-danger').addClass('btn-success');
                submitBtn.html('<i class="mdi mdi-check"></i> Approve');
                $('#approvalModalLabel').text('Approve Production Report');
            } else {
                modalHeader.removeClass('bg-success').addClass('bg-danger');
                submitBtn.removeClass('btn-success').addClass('btn-danger');
                submitBtn.html('<i class="mdi mdi-close"></i> Reject');
                $('#approvalModalLabel').text('Reject Production Report');
            }

            $('#approvalModal').modal('show');
        }
    </script>
@endsection

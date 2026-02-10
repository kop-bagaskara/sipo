@extends('main.layouts.main')
@section('title')
    Production Report - Development
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            text-align: center;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .info-card h6 {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }

        .production-schedule-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .status-scheduled {
            background: #cce5ff;
            color: #004085;
        }

        .status-ready {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-in_progress {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-production {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-production:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .required {
            color: #dc3545;
        }
    </style>
@endsection
@section('page-title')
    Production Report - Development
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Production Report</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('development.rnd-workspace.index') }}">RnD Workspace</a>
                    </li>
                    <li class="breadcrumb-item active">Production Report</li>
                </ol>
            </div>
        </div>

        <!-- Header Info -->
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 style="color: white;">{{ $job->job_name }}</h3>
                    <p class="mb-0">Job Code: {{ $job->job_code }} | Customer: {{ $job->customer }}</p>
                </div>
            </div>
        </div>

        <!-- Production Schedules -->
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="mdi mdi-calendar-multiple"></i> Production Schedules</h6>

                    @if($job->productionSchedules && $job->productionSchedules->count() > 0)
                        @foreach($job->productionSchedules as $schedule)
                            <div class="production-schedule-card">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Tanggal & Waktu:</strong><br>
                                        {{ $schedule->production_date_time }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Mesin:</strong><br>
                                        {{ $schedule->machine_name }} ({{ $schedule->machine_code }})
                                    </div>
                                <div class="col-md-2">
                                    <strong>Status:</strong><br>
                                    <span class="status-badge status-{{ $schedule->status }}">{{ $schedule->status_label }}</span>
                                </div>
                                <div class="col-md-2">
                                    <strong>RnD Approval:</strong><br>
                                    <span class="badge {{ $schedule->rnd_approval_status_color_class }}">{{ $schedule->rnd_approval_status_label }}</span>
                                </div>
                                <div class="col-md-2">
                                    @if($schedule->status === 'scheduled' || $schedule->status === 'ready')
                                        <button type="button" class="btn btn-sm btn-success"
                                                onclick="showProductionReportForm({{ $schedule->id }})">
                                            <i class="mdi mdi-check"></i> Report Selesai
                                        </button>
                                    @elseif($schedule->status === 'completed')
                                        @if($schedule->canBeRevised())
                                            <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="showRevisionForm({{ $schedule->id }})">
                                                <i class="mdi mdi-pencil"></i> Revisi
                                            </button>
                                        @else
                                            <span class="text-success"><i class="mdi mdi-check-circle"></i> Selesai</span>
                                        @endif
                                    @elseif($schedule->status === 'cancelled')
                                        <span class="text-danger"><i class="mdi mdi-cancel"></i> Dibatalkan</span>
                                    @endif
                                </div>
                                </div>

                                @if($schedule->production_notes || $schedule->quality_notes)
                                    <div class="row mt-2">
                                        @if($schedule->production_notes)
                                            <div class="col-md-6">
                                                <strong>Production Notes:</strong><br>
                                                <small>{{ $schedule->production_notes }}</small>
                                            </div>
                                        @endif
                                        @if($schedule->quality_notes)
                                            <div class="col-md-6">
                                                <strong>Quality Notes:</strong><br>
                                                <small>{{ $schedule->quality_notes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i> Belum ada production schedule untuk job ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Production Report Modal -->
        <div class="modal fade" id="productionReportModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="mdi mdi-clipboard-check"></i> Production Report
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="productionReportForm">
                            @csrf
                            <input type="hidden" id="scheduleId" name="schedule_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status Produksi <span class="required">*</span></label>
                                        <select class="form-control" id="productionStatus" name="production_status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="completed">Selesai</option>
                                            <option value="cancelled">Dibatalkan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="completionDate" name="completion_date"
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jumlah Produksi (Qty)</label>
                                        <input type="number" class="form-control" id="productionQty" name="production_qty"
                                               min="0" placeholder="Jumlah yang diproduksi">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jumlah Reject (Qty)</label>
                                        <input type="number" class="form-control" id="rejectQty" name="reject_qty"
                                               min="0" placeholder="Jumlah yang reject">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Waktu Mulai Produksi</label>
                                        <input type="time" class="form-control" id="startTime" name="start_time">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Waktu Selesai Produksi</label>
                                        <input type="time" class="form-control" id="endTime" name="end_time">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Catatan Produksi</label>
                                <textarea class="form-control" id="productionNotes" name="production_notes" rows="3"
                                          placeholder="Catatan hasil produksi..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Catatan Quality Control</label>
                                <textarea class="form-control" id="qualityNotes" name="quality_notes" rows="3"
                                          placeholder="Catatan quality control..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Masalah yang Ditemukan</label>
                                <textarea class="form-control" id="issuesFound" name="issues_found" rows="3"
                                          placeholder="Masalah atau kendala yang ditemukan..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Rekomendasi</label>
                                <textarea class="form-control" id="recommendations" name="recommendations" rows="3"
                                          placeholder="Rekomendasi untuk perbaikan..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-production" onclick="submitProductionReport()">
                            <i class="mdi mdi-check"></i> Submit Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            $(document).ready(function() {
                // Auto-fill completion date when status changes
                $('#productionStatus').on('change', function() {
                    if ($(this).val() === 'completed') {
                        $('#completionDate').val('{{ date('Y-m-d') }}');
                    }
                });
            });

            function showProductionReportForm(scheduleId) {
                // Reset revision flag for new report
                window.isRevision = false;
                $('#scheduleId').val(scheduleId);
                $('#productionReportModal').modal('show');
            }

            function showRevisionForm(scheduleId) {
                // Set revision flag
                window.isRevision = true;
                // Load existing data for revision
                loadScheduleDataForRevision(scheduleId);
                $('#scheduleId').val(scheduleId);
                $('#productionReportModal').modal('show');
            }

            // Function to load schedule data for revision
            function loadScheduleDataForRevision(scheduleId) {
                $.ajax({
                    url: '{{ route('development.production-report', $job->id) }}',
                    type: 'GET',
                    data: { schedule_id: scheduleId },
                    success: function(response) {
                        if (response.success && response.schedule) {
                            var schedule = response.schedule;
                            $('#productionQty').val(schedule.production_qty || '');
                            $('#rejectQty').val(schedule.reject_qty || '');
                            $('#startTime').val(schedule.start_time || '');
                            $('#endTime').val(schedule.end_time || '');
                            $('#productionNotes').val(schedule.production_notes || '');
                            $('#qualityNotes').val(schedule.quality_notes || '');
                            $('#issuesFound').val(schedule.issues_found || '');
                            $('#recommendations').val(schedule.recommendations || '');
                            $('#productionStatus').val(schedule.status || '');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading schedule data:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data schedule untuk revisi'
                        });
                    }
                });
            }

            function submitProductionReport() {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    schedule_id: $('#scheduleId').val(),
                    production_status: $('#productionStatus').val(),
                    completion_date: $('#completionDate').val(),
                    production_qty: $('#productionQty').val(),
                    reject_qty: $('#rejectQty').val(),
                    start_time: $('#startTime').val(),
                    end_time: $('#endTime').val(),
                    production_notes: $('#productionNotes').val(),
                    quality_notes: $('#qualityNotes').val(),
                    issues_found: $('#issuesFound').val(),
                    recommendations: $('#recommendations').val()
                };

                // Validation
                if (!formData.production_status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih status produksi terlebih dahulu'
                    });
                    return;
                }

                // Determine if this is a revision or new report
                var isRevision = window.isRevision || false;
                var url = isRevision ?
                    '{{ route('development.production-report.revise', [$job->id, 'SCHEDULE_ID']) }}'.replace('SCHEDULE_ID', formData.schedule_id) :
                    '{{ route('development.production-report.store', $job->id) }}';

                Swal.fire({
                    title: isRevision ? 'Merevisi...' : 'Menyimpan...',
                    text: isRevision ? 'Sedang merevisi production report' : 'Sedang menyimpan production report',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: isRevision ? 'Production report berhasil direvisi' : 'Production report berhasil disimpan',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var message = isRevision ? 'Terjadi kesalahan saat merevisi production report' : 'Terjadi kesalahan saat menyimpan production report';
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
            }
        </script>
    @endsection

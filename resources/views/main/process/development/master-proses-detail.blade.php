@extends('main.layouts.main')

@section('head')
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        .proses-timeline {
            position: relative;
            padding-left: 30px;
        }

        .proses-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }

        .proses-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 30px;
        }

        .proses-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e0e0e0;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e0e0e0;
        }

        .proses-item.completed::before {
            background: #28a745;
            box-shadow: 0 0 0 2px #28a745;
        }

        .proses-item.in-progress::before {
            background: #ffc107;
            box-shadow: 0 0 0 2px #ffc107;
        }

        .proses-item.overdue::before {
            background: #dc3545;
            box-shadow: 0 0 0 2px #dc3545;
        }

        .proses-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .proses-card.completed {
            border-color: #28a745;
            background: #f8fff9;
        }

        .proses-card.in-progress {
            border-color: #ffc107;
            background: #fffdf5;
        }

        .proses-card.overdue {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .proses-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 15px;
        }

        .proses-number {
            background: #007bff;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        .proses-title {
            flex: 1;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .proses-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .proses-description {
            color: #666;
            margin-bottom: 15px;
        }

        .proses-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
        }

        .proses-actions {
            margin-top: 15px;
        }

        .proses-actions .btn {
            margin-right: 10px;
        }

        .department-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .department-marketing { background: #e3f2fd; color: #1976d2; }
        .department-rnd { background: #e0f7fa; color: #00acc1; }
        .department-prepress { background: #f5f5f5; color: #616161; }
        .department-customer { background: #fff8e1; color: #f57c00; }
        .department-ppic { background: #fff8e1; color: #f57c00; }
        .department-production { background: #e8f5e8; color: #388e3c; }

        .progress-overview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .progress-bar-custom {
            height: 20px;
            border-radius: 10px;
        }

        .job-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
    </style>
@endsection

@section('page-title')
    Master Proses Detail - {{ $job->job_code }}
@endsection

@section('body')
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Proses Detail</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('development.master-proses') }}">Master Proses</a></li>
                    <li class="breadcrumb-item active">{{ $job->job_code }}</li>
                </ol>
            </div>
        </div>

        <!-- Job Info -->
        <div class="row">
            <div class="col-12">
                <div class="job-info">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Job Code:</strong> {{ $job->job_code }}
                        </div>
                        <div class="col-md-3">
                            <strong>Customer:</strong> {{ $job->customer->nama_customer ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Product:</strong> {{ $job->product->nama_produk ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Priority:</strong>
                            <span class="badge {{ $job->prioritas_job === 'Urgent' ? 'badge-danger' : 'badge-info' }}">
                                {{ $job->prioritas_job }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="row">
            <div class="col-12">
                <div class="progress-overview">
                    <h5>Overall Progress</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Completed:</strong> {{ $completedCount }}/{{ $totalCount }}
                        </div>
                        <div class="col-md-3">
                            <strong>Progress:</strong> {{ $progressPercentage }}%
                        </div>
                        <div class="col-md-3">
                            <strong>Current:</strong> {{ $currentProses->proses_name ?? 'Completed' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Department:</strong>
                            <span class="department-badge department-{{ strtolower($currentProses->department_responsible ?? 'completed') }}">
                                {{ $currentProses->department_responsible ?? 'Completed' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Proses Timeline -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Master Proses Timeline</h5>
                        <div class="proses-timeline">
                            @foreach($masterProses as $proses)
                                <div class="proses-item {{ $proses->status_proses }} {{ $proses->is_overdue ? 'overdue' : '' }}">
                                    <div class="proses-card {{ $proses->status_proses }} {{ $proses->is_overdue ? 'overdue' : '' }}">
                                        <div class="proses-header">
                                            <div class="proses-number">{{ $proses->urutan_proses }}</div>
                                            <div class="proses-title">{{ $proses->proses_name }}</div>
                                            <div class="proses-status badge {{ $proses->status_badge }}">
                                                {{ $proses->status_text }}
                                            </div>
                                        </div>

                                        <div class="proses-description">
                                            <strong>Department:</strong>
                                            <span class="department-badge department-{{ strtolower($proses->department_responsible) }}">
                                                {{ $proses->department_responsible }}
                                            </span>
                                        </div>

                                        @if($proses->notes)
                                            <div class="proses-description">
                                                <strong>Notes:</strong> {{ $proses->notes }}
                                            </div>
                                        @endif

                                        <div class="proses-meta">
                                            <div>
                                                @if($proses->started_at)
                                                    <strong>Started:</strong> {{ $proses->started_at->format('d/m/Y H:i') }}
                                                @endif
                                                @if($proses->completed_at)
                                                    <br><strong>Completed:</strong> {{ $proses->completed_at->format('d/m/Y H:i') }}
                                                @endif
                                                @if($proses->completed_by)
                                                    <br><strong>By:</strong> {{ $proses->completedBy->name ?? 'Unknown' }}
                                                @endif
                                            </div>
                                            <div>
                                                @if($proses->duration)
                                                    <strong>Duration:</strong> {{ $proses->duration }} days
                                                @endif
                                                @if($proses->is_overdue)
                                                    <br><span class="text-danger"><strong>OVERDUE!</strong></span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($proses->status_proses === 'pending' && $proses->can_start)
                                            <div class="proses-actions">
                                                <button class="btn btn-primary btn-sm" onclick="startProses({{ $proses->id }})">
                                                    <i class="mdi mdi-play"></i> Start
                                                </button>
                                                <button class="btn btn-info btn-sm" onclick="skipProses({{ $proses->id }})">
                                                    <i class="mdi mdi-skip-next"></i> Skip
                                                </button>
                                            </div>
                                        @elseif($proses->status_proses === 'in_progress')
                                            <div class="proses-actions">
                                                <button class="btn btn-success btn-sm" onclick="completeProses({{ $proses->id }})">
                                                    <i class="mdi mdi-check"></i> Complete
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="addNotes({{ $proses->id }})">
                                                    <i class="mdi mdi-note-text"></i> Add Notes
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        function startProses(prosesId) {
            Swal.fire({
                title: 'Start Proses',
                text: 'Are you sure you want to start this process?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Start',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateProsesStatus(prosesId, 'start');
                }
            });
        }

        function completeProses(prosesId) {
            Swal.fire({
                title: 'Complete Proses',
                text: 'Are you sure you want to complete this process?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Complete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateProsesStatus(prosesId, 'complete');
                }
            });
        }

        function skipProses(prosesId) {
            Swal.fire({
                title: 'Skip Proses',
                text: 'Are you sure you want to skip this process?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Skip',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateProsesStatus(prosesId, 'skip');
                }
            });
        }

        function addNotes(prosesId) {
            Swal.fire({
                title: 'Add Notes',
                input: 'textarea',
                inputPlaceholder: 'Enter notes...',
                showCancelButton: true,
                confirmButtonText: 'Save Notes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateProsesStatus(prosesId, 'complete', result.value);
                }
            });
        }

        function updateProsesStatus(prosesId, status, notes = null) {
            $.ajax({
                url: '/sipo/development/master-proses/update-status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    proses_id: prosesId,
                    status: status,
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the process status'
                    });
                }
            });
        }
    </script>
@endsection

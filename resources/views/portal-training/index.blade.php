@extends('main.layouts.main')
@section('title')
    Portal Training Karyawan
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
        .cust-col {
            white-space: nowrap;
        }
        .assignment-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
        }
        .assignment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .progress-bar-custom {
            height: 25px;
            border-radius: 15px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-assigned { background: #e3f2fd; color: #1976d2; }
        .status-in-progress { background: #fff3e0; color: #f57c00; }
        .status-completed { background: #e8f5e9; color: #388e3c; }
        .status-expired { background: #ffebee; color: #d32f2f; }
    </style>
@endsection
@section('page-title')
    Portal Training Karyawan
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Portal Training Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Portal Training Karyawan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-school mr-2"></i>
                            Training Assignment Saya
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($assignments->isEmpty())
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                Belum ada training yang di-assign untuk Anda.
                            </div>
                        @else
                            <div class="row">
                                @foreach($assignments as $assignment)
                                    <div class="col-md-6">
                                        <div class="card assignment-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title mb-0">{{ $assignment->training_id ? $assignment->training->training_name : 'Training Assignment' }}</h5>
                                                    <span class="status-badge status-{{ $assignment->status }}">
                                                        @if($assignment->status == 'assigned')
                                                            Ditetapkan
                                                        @elseif($assignment->status == 'in_progress')
                                                            Sedang Dikerjakan
                                                        @elseif($assignment->status == 'completed')
                                                            Selesai
                                                        @else
                                                            Expired
                                                        @endif
                                                    </span>
                                                </div>

                                                <p class="text-muted mb-2">
                                                    <i class="mdi mdi-calendar mr-1"></i>
                                                    Ditetapkan: {{ $assignment->assigned_date->format('d M Y') }}
                                                </p>

                                                @if($assignment->deadline_date)
                                                    <p class="text-muted mb-2">
                                                        <i class="mdi mdi-clock-outline mr-1"></i>
                                                        Deadline: {{ $assignment->deadline_date->format('d M Y') }}
                                                    </p>
                                                @endif

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small class="text-muted">Progress</small>
                                                        <small class="text-muted"><strong>{{ number_format($assignment->progress_percentage, 1) }}%</strong></small>
                                                    </div>
                                                    <div class="progress progress-bar-custom">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: {{ $assignment->progress_percentage }}%"
                                                             aria-valuenow="{{ $assignment->progress_percentage }}"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <a href="{{ route('hr.portal-training.materials.show', $assignment->materials->first()->id ?? '#') }}"
                                                       class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-play-circle mr-1"></i>
                                                        Mulai Training
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('js')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    @endsection


@extends('main.layouts.main')
@section('title')
    Riwayat Training
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .status-passed {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
        .score-passed {
            background: #28a745;
            color: white;
        }
        .score-failed {
            background: #dc3545;
            color: white;
        }
    </style>
@endsection
@section('page-title')
    Riwayat Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Riwayat Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item active">Riwayat</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Hasil Training Ujian</h4>
                </div>
                <div class="card-body">
                    @if($results->isEmpty())
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Belum ada riwayat ujian training.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Training</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Skor</th>
                                        <th>Status</th>
                                        <th>Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $index => $result)
                                        <tr>
                                            <td>{{ $results->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $result->assignment->training->training_name ?? '-' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $result->assignment->materials->first()->title ?? '-' }}</small>
                                            </td>
                                            <td>{{ $result->completed_date->format('d M Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="score-circle {{ $result->status == 'passed' ? 'score-passed' : 'score-failed' }} mr-2">
                                                        {{ $result->total_score }}
                                                    </div>
                                                    <div>
                                                        <div>Max: {{ $result->max_score }}</div>
                                                        <div class="text-muted small">Passing: {{ $result->passing_score }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($result->status == 'passed')
                                                    <span class="status-passed">
                                                        <i class="mdi mdi-check-circle"></i> LULUS
                                                    </span>
                                                @else
                                                    <span class="status-failed">
                                                        <i class="mdi mdi-close-circle"></i> TIDAK LULUS
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($result->status == 'passed' && $result->certificate_path)
                                                    <a href="{{ asset($result->certificate_path) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="mdi mdi-download"></i> Download
                                                    </a>
                                                @elseif($result->status == 'passed')
                                                    <span class="text-muted">
                                                        <i class="mdi mdi-clock-outline"></i> Sedang diproses
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($results->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $results->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
@endsection

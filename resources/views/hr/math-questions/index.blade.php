@extends('main.layouts.main')

@section('title')
    Master Soal Matematika
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .question-text {
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .answer-badge {
            font-size: 16px;
            padding: 8px 15px;
            font-weight: bold;
        }
        .badge-difficulty {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .difficulty-1 { background-color: #28a745; color: white; }
        .difficulty-2 { background-color: #ffc107; color: #333; }
        .difficulty-3 { background-color: #fd7e14; color: white; }
        .difficulty-4 { background-color: #dc3545; color: white; }
        .difficulty-5 { background-color: #6f42c1; color: white; }
        .badge-type {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .type-pattern { background-color: #17a2b8; color: white; }
        .type-calculation { background-color: #007bff; color: white; }
        .type-conversion { background-color: #6c757d; color: white; }
        .type-word_problem { background-color: #e83e8c; color: white; }
    </style>
@endsection

@section('page-title')
    Master Soal Matematika
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master Soal Matematika</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.applicants.index') }}">Applicants</a></li>
                <li class="breadcrumb-item active">Master Soal Matematika</li>
            </ol>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center round-info">
                            <i class="mdi mdi-calculator" style="font-size: 24px;"></i>
                        </div>
                        <div class="m-l-10 align-self-center">
                            <h3 class="m-b-0">{{ $totalQuestions }}</h3>
                            <h5 class="text-muted m-b-0">Total Soal</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center round-success">
                            <i class="mdi mdi-check-circle" style="font-size: 24px;"></i>
                        </div>
                        <div class="m-l-10 align-self-center">
                            <h3 class="m-b-0">{{ $activeQuestions }}</h3>
                            <h5 class="text-muted m-b-0">Aktif</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center round-danger">
                            <i class="mdi mdi-close-circle" style="font-size: 24px;"></i>
                        </div>
                        <div class="m-l-10 align-self-center">
                            <h3 class="m-b-0">{{ $inactiveQuestions }}</h3>
                            <h5 class="text-muted m-b-0">Tidak Aktif</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Daftar Soal Matematika</h4>
                            <p class="text-muted mb-0">Master data untuk test matematika</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('hr.math-questions.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Nomor soal, pertanyaan, atau jawaban">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipe Soal</label>
                                    <select name="question_type" class="form-control">
                                        <option value="">Semua Tipe</option>
                                        @foreach($questionTypes as $key => $label)
                                            <option value="{{ $key }}" {{ request('question_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Difficulty Level</label>
                                    <select name="difficulty_level" class="form-control">
                                        <option value="">Semua Level</option>
                                        <option value="1" {{ request('difficulty_level') == '1' ? 'selected' : '' }}>Level 1 (Easy)</option>
                                        <option value="2" {{ request('difficulty_level') == '2' ? 'selected' : '' }}>Level 2 (Medium)</option>
                                        <option value="3" {{ request('difficulty_level') == '3' ? 'selected' : '' }}>Level 3 (Hard)</option>
                                        <option value="4" {{ request('difficulty_level') == '4' ? 'selected' : '' }}>Level 4 (Very Hard)</option>
                                        <option value="5" {{ request('difficulty_level') == '5' ? 'selected' : '' }}>Level 5 (Expert)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="mdi mdi-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 80px;">No Soal</th>
                                    <th>Soal</th>
                                    <th style="width: 150px;">Jawaban</th>
                                    <th style="width: 120px;">Tipe Soal</th>
                                    <th style="width: 100px;">Difficulty</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 100px;">Display Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $index => $question)
                                    <tr>
                                        <td>{{ $questions->firstItem() + $index }}</td>
                                        <td class="text-center">
                                            <strong>{{ $question->question_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="question-text">
                                                {{ $question->question }}
                                            </div>
                                            @if($question->explanation)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    {{ $question->explanation }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-success answer-badge">
                                                {{ $question->answer }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-type type-{{ $question->question_type }}">
                                                {{ $questionTypes[$question->question_type] ?? $question->question_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-difficulty difficulty-{{ $question->difficulty_level }}">
                                                Level {{ $question->difficulty_level }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($question->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>{{ $question->display_order }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-information-outline" style="font-size: 48px; color: #999;"></i>
                                                <p class="text-muted mt-2">Tidak ada data ditemukan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($questions->hasPages())
                        <div class="mt-3">
                            {{ $questions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection


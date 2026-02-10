@extends('main.layouts.main')
@section('title')
    Daftar Materi Training
@endsection
@section('css')
    <style>
        .material-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
        }
        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .material-thumbnail {
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        .progress-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 10px;
            color: white;
        }
        .status-completed { color: #28a745; }
        .status-watching { color: #ffc107; }
        .status-not-started { color: #6c757d; }
    </style>
@endsection
@section('page-title')
    Daftar Materi Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Daftar Materi Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item active">Materi</li>
            </ol>
        </div>
    </div>

    @if($assignments->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="mdi mdi-information mr-2"></i>
                    Belum ada training assignment untuk Anda.
                </div>
            </div>
        </div>
    @else
        @foreach($assignments as $assignment)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">{{ $assignment->training->training_name ?? 'Training Assignment' }}</h4>
                </div>
                @foreach($assignment->materials as $material)
                    <div class="col-md-4 mb-3">
                        <div class="card material-card">
                            <div class="material-thumbnail position-relative">
                                <i class="mdi mdi-play-circle"></i>
                                @if($material->progress)
                                    <div class="progress-overlay">
                                        <small>{{ number_format($material->progress->progress_percentage, 1) }}% Selesai</small>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-{{ $material->progress->status == 'completed' ? 'success' : 'warning' }}"
                                                 style="width: {{ $material->progress->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $material->title }}</h5>
                                <p class="text-muted small">{{ Str::limit($material->description, 80) }}</p>
                                <div class="mb-2">
                                    <span class="badge badge-info">{{ $material->category->name ?? '-' }}</span>
                                    <span class="badge badge-secondary">
                                        <i class="mdi mdi-clock-outline"></i>
                                        {{ floor($material->duration / 60) }}m
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-{{ $material->progress && $material->progress->status == 'completed' ? 'success' : 'warning' }}">
                                        @if($material->progress && $material->progress->status == 'completed')
                                            <i class="mdi mdi-check-circle"></i> Selesai
                                        @elseif($material->progress && $material->progress->status == 'watching')
                                            <i class="mdi mdi-play-circle"></i> Sedang Menonton
                                        @else
                                            <i class="mdi mdi-clock-outline"></i> Belum Dimulai
                                        @endif
                                    </span>
                                    <a href="{{ route('hr.portal-training.materials.show', $material->id) }}"
                                       class="btn btn-sm btn-primary">
                                        @if($material->progress && $material->progress->status == 'completed')
                                            <i class="mdi mdi-replay"></i> Tonton Lagi
                                        @else
                                            <i class="mdi mdi-play"></i> Mulai
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection

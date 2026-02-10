@extends('main.layouts.main')
@section('title')
    Hasil Ujian - {{ $material->material_title }}
@endsection
@section('css')
    <style>
        .result-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .score-card {
            text-align: center;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .score-passed {
            background: linear-gradient(135deg, #16a34a 0%, #34d399 100%);
            color: white;
        }
        .score-failed {
            background: linear-gradient(135deg, #dc2626 0%, #f87171 100%);
            color: white;
        }
        .score-value {
            font-size: 4rem;
            font-weight: bold;
            margin: 20px 0;
        }
        .question-review {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .answer-correct {
            border-left: 4px solid #16a34a;
            background: #d1fae5;
        }
        .answer-wrong {
            border-left: 4px solid #dc2626;
            background: #fee2e2;
        }
    </style>
@endsection
@section('page-title')
    Hasil Ujian
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Hasil Ujian - {{ $material->material_title }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                    <li class="breadcrumb-item active">Hasil Ujian</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="result-container">
                    <div class="score-card {{ $exam->isPassed() ? 'score-passed' : 'score-failed' }}">
                        <h3>{{ $exam->isPassed() ? 'Selamat! Anda Lulus' : 'Anda Belum Lulus' }}</h3>
                        <div class="score-value">
                            {{ number_format(($exam->score / $exam->max_score) * 100, 1) }}%
                        </div>
                        <p class="mb-0">
                            Skor: {{ number_format($exam->score, 1) }} / {{ number_format($exam->max_score, 1) }}<br>
                            <small>Skor Minimum: {{ $exam->passing_score }}%</small>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h4>Ringkasan</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="text-success">{{ $exam->correct_answers }}</h5>
                                        <p class="mb-0">Jawaban Benar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="text-danger">{{ $exam->wrong_answers }}</h5>
                                        <p class="mb-0">Jawaban Salah</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5>{{ $exam->total_questions }}</h5>
                                        <p class="mb-0">Total Soal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>Review Jawaban</h4>
                        @foreach($exam->examQuestions->sortBy('question_order') as $index => $examQuestion)
                            @php
                                $question = $examQuestion->questionBank;
                                $isCorrect = $examQuestion->is_correct;
                            @endphp
                            <div class="question-review {{ $isCorrect ? 'answer-correct' : 'answer-wrong' }}">
                                <h5>
                                    <span class="badge {{ $isCorrect ? 'badge-success' : 'badge-danger' }} mr-2">
                                        {{ $isCorrect ? '✓' : '✗' }}
                                    </span>
                                    Soal {{ $index + 1 }}
                                </h5>
                                <p class="mb-2"><strong>{{ $question->question }}</strong></p>
                                <p class="mb-1">
                                    <strong>Jawaban Anda:</strong> 
                                    <span class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                        {{ $examQuestion->user_answer ?? '-' }}
                                    </span>
                                </p>
                                @if(!$isCorrect)
                                    <p class="mb-0">
                                        <strong>Jawaban Benar:</strong> 
                                        <span class="text-success">{{ $question->correct_answer }}</span>
                                    </p>
                                @endif
                                @if($question->explanation)
                                    <p class="mt-2 mb-0">
                                        <small><strong>Penjelasan:</strong> {{ $question->explanation }}</small>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('hr.portal-training.index') }}" class="btn btn-primary btn-lg">
                            <i class="mdi mdi-arrow-left mr-2"></i>
                            Kembali ke Portal Training
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endsection


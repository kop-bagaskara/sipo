@extends('main.layouts.main')
@section('title')
    Ujian - {{ $material->material_title }}
@endsection
@section('css')
    <style>
        .exam-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .question-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .answer-option {
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .answer-option:hover {
            border-color: #007bff;
            background: #e7f3ff;
        }
        .answer-option.selected {
            border-color: #007bff;
            background: #cfe2ff;
        }
        .timer-badge {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
            padding: 15px 25px;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .timer-warning {
            background: #ffc107;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
@endsection
@section('page-title')
    Ujian Training
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Ujian - {{ $material->material_title }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                    <li class="breadcrumb-item active">Ujian</li>
                </ol>
            </div>
        </div>

        @if(!$exam)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Mulai Ujian</h4>
                            <p class="text-muted">Klik tombol di bawah untuk memulai ujian.</p>
                            <form action="{{ route('hr.portal-training.exams.start', $material->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="mdi mdi-play-circle mr-2"></i>
                                    Mulai Ujian
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if($exam->time_limit_seconds)
                <div class="timer-badge" id="timer">
                    <i class="mdi mdi-timer mr-2"></i>
                    <span id="time-remaining">--:--</span>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="exam-container">
                        <form id="exam-form" action="{{ route('hr.portal-training.exams.finish', $exam->id) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <h4>Soal Ujian</h4>
                                <p class="text-muted">Jawab semua pertanyaan dengan benar.</p>
                            </div>

                            @foreach($exam->examQuestions->sortBy('question_order') as $index => $examQuestion)
                                @php
                                    $question = $examQuestion->questionBank;
                                @endphp
                                <div class="question-card" data-question-id="{{ $examQuestion->id }}">
                                    <h5 class="mb-3">
                                        <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                        {{ $question->question }}
                                    </h5>

                                    @if($question->question_type == 'multiple_choice')
                                        @php
                                            $options = $question->answer_options ?? [];
                                        @endphp
                                        @foreach($options as $option)
                                            <div class="answer-option" onclick="selectAnswer({{ $examQuestion->id }}, '{{ $option }}')">
                                                <input type="radio"
                                                       name="answers[{{ $examQuestion->id }}]"
                                                       value="{{ $option }}"
                                                       id="answer_{{ $examQuestion->id }}_{{ $loop->index }}"
                                                       {{ $examQuestion->user_answer == $option ? 'checked' : '' }}>
                                                <label for="answer_{{ $examQuestion->id }}_{{ $loop->index }}" class="mb-0 ml-2">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @elseif($question->question_type == 'true_false')
                                        <div class="answer-option" onclick="selectAnswer({{ $examQuestion->id }}, 'true')">
                                            <input type="radio" name="answers[{{ $examQuestion->id }}]" value="true" id="answer_{{ $examQuestion->id }}_true" {{ $examQuestion->user_answer == 'true' ? 'checked' : '' }}>
                                            <label for="answer_{{ $examQuestion->id }}_true" class="mb-0 ml-2">Benar</label>
                                        </div>
                                        <div class="answer-option" onclick="selectAnswer({{ $examQuestion->id }}, 'false')">
                                            <input type="radio" name="answers[{{ $examQuestion->id }}]" value="false" id="answer_{{ $examQuestion->id }}_false" {{ $examQuestion->user_answer == 'false' ? 'checked' : '' }}>
                                            <label for="answer_{{ $examQuestion->id }}_false" class="mb-0 ml-2">Salah</label>
                                        </div>
                                    @else
                                        <textarea name="answers[{{ $examQuestion->id }}]"
                                                  class="form-control"
                                                  rows="4"
                                                  placeholder="Tulis jawaban Anda di sini...">{{ $examQuestion->user_answer }}</textarea>
                                    @endif
                                </div>
                            @endforeach

                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                    <i class="mdi mdi-check-circle mr-2"></i>
                                    Selesai & Submit Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endsection
    @section('js')
        <script>
            function selectAnswer(questionId, answer) {
                const questionCard = document.querySelector(`[data-question-id="${questionId}"]`);
                const options = questionCard.querySelectorAll('.answer-option');
                options.forEach(opt => opt.classList.remove('selected'));

                const selectedOption = Array.from(options).find(opt =>
                    opt.querySelector('input[type="radio"]')?.value === answer
                );
                if (selectedOption) {
                    selectedOption.classList.add('selected');
                    selectedOption.querySelector('input[type="radio"]').checked = true;

                    // Auto-save answer
                    saveAnswer(questionId, answer);
                }
            }

            function saveAnswer(questionId, answer) {
                fetch('{{ route("hr.portal-training.exams.answer", ":examId") }}'.replace(':examId', {{ $exam->id ?? 0 }}), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: answer
                    })
                })
                .catch(error => console.error('Error saving answer:', error));
            }

            @if($exam && $exam->time_limit_seconds)
                let timeRemaining = {{ $exam->time_limit_seconds }} - {{ now()->diffInSeconds($exam->started_at) }};
                const timerElement = document.getElementById('time-remaining');
                const timerBadge = document.getElementById('timer');

                function updateTimer() {
                    if (timeRemaining <= 0) {
                        timerElement.textContent = '00:00';
                        timerBadge.classList.add('timer-warning');
                        alert('Waktu habis! Ujian akan otomatis disubmit.');
                        document.getElementById('exam-form').submit();
                        return;
                    }

                    const minutes = Math.floor(timeRemaining / 60);
                    const seconds = timeRemaining % 60;
                    timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                    if (timeRemaining <= 60) {
                        timerBadge.classList.add('timer-warning');
                    }

                    timeRemaining--;
                }

                updateTimer();
                setInterval(updateTimer, 1000);
            @endif

            // Prevent accidental page leave
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = '';
            });

            document.getElementById('exam-form').addEventListener('submit', function() {
                window.removeEventListener('beforeunload', arguments.callee);
            });
        </script>
    @endsection


@extends('main.layouts.main')

@section('title')
    Sesi Training - {{ $session->session_title }}
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        /* Override global radio button styles - Force radio buttons to be visible */
        #quizSection input[type="radio"],
        .question-card input[type="radio"],
        .option-label input[type="radio"],
        form#quizForm input[type="radio"] {
            position: relative !important;
            left: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
            -webkit-appearance: radio !important;
            -moz-appearance: radio !important;
            appearance: radio !important;
        }

        /* Remove any pseudo-elements that might hide the radio */
        #quizSection input[type="radio"]:before,
        #quizSection input[type="radio"]:after,
        .question-card input[type="radio"]:before,
        .question-card input[type="radio"]:after {
            display: none !important;
            content: none !important;
        }
        .session-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .session-header {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
        }
        .session-item {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .session-item.active {
            border-color: #667eea;
            background-color: #f0f4ff;
        }
        .session-item.completed {
            border-color: #28a745;
            background-color: #f0fff4;
        }
        .session-item.locked {
            border-color: #dc3545;
            background-color: #fff5f5;
            opacity: 0.7;
        }
        .question-card {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }
        .option-label {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin: 8px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .option-label:hover {
            border-color: #667eea;
            background-color: #f0f4ff;
        }
        .option-label.selected {
            border-color: #667eea;
            background-color: #e8edff;
        }
        .option-label input[type="radio"] {
            position: relative !important;
            left: 0 !important;
            opacity: 1 !important;
            margin-right: 12px;
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            cursor: pointer;
            flex-shrink: 0;
            -webkit-appearance: radio;
            -moz-appearance: radio;
            appearance: radio;
            accent-color: #667eea;
        }
        .option-label input[type="radio"]:checked {
            accent-color: #667eea;
        }
        .option-label input[type="radio"]:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
        .video-container {
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            margin-bottom: 20px;
        }
        .video-container video {
            width: 100%;
            max-height: 500px;
        }
        /* Sembunyikan progress bar untuk mencegah skip */
        .plyr__progress {
            pointer-events: none !important;
            cursor: not-allowed !important;
        }
        .plyr__progress__buffer,
        .plyr__progress__played {
            pointer-events: none !important;
        }
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background-color: #e0e0e0;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
    </style>
@endsection

@section('page-title')
    Sesi Training
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Training: {{ $assignment->training->training_name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                <li class="breadcrumb-item active">{{ $session->session_title }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar - Session List -->
        <div class="col-md-4">
            <div class="card session-card">
                <div class="session-header bg-info">
                    <h4 class="mb-0 text-white"><i class="mdi mdi-format-list-bulleted-type"></i> Daftar Sesi</h4>
                    <small>{{ $allSessions ? count($allSessions) : 0 }} Sesi</small>
                </div>
                <div class="card-body p-3">
                    @if($allSessions && count($allSessions) > 0)
                        @foreach($allSessions as $sess)
                        @php
                            $progress = $sessionProgressList->get($sess->id);
                            $isCurrent = $sess->id === $session->id;
                            $isCompleted = $progress && in_array($progress->status, ['passed', 'completed']);
                            // Check if session is locked (previous session not completed)
                            $isLocked = false;
                            if (!$isCurrent && !$isCompleted) {
                                // Check if this session can be started
                                $isLocked = !$sess->canUserStart($assignment->employee_id, $assignment->id);
                            }
                        @endphp
                        <div class="session-item @if($isCurrent) active @elseif($isCompleted) completed @elseif($isLocked) locked @endif"
                             @if(!$isCurrent && !$isCompleted && !$isLocked) onclick="window.location.href='{{ route('hr.portal-training.sessions.show', [$assignment->id, $sess->id]) }}'" style="cursor: pointer;" @elseif($isLocked) style="cursor: not-allowed; opacity: 0.6;" @endif>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    @if($isCompleted)
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 24px;"></i>
                                    @elseif($isCurrent)
                                        <i class="mdi mdi-play-circle text-primary" style="font-size: 24px;"></i>
                                    @elseif($isLocked)
                                        <i class="mdi mdi-lock text-danger" style="font-size: 24px;"></i>
                                    @else
                                        <i class="mdi mdi-play-circle-outline text-primary" style="font-size: 24px;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Sesi {{ $sess->session_order }}: {{ $sess->session_title }}</h6>
                                    <small class="text-muted">
                                        @if($isCompleted)
                                            <span class="text-success">Selesai - Nilai: {{ $progress->score ?? 0 }}</span>
                                        @elseif($isCurrent)
                                            <span class="text-primary">Sesi Saat Ini</span>
                                        @elseif($isLocked)
                                            <span class="text-danger">Terkunci - Selesaikan sesi sebelumnya terlebih dahulu</span>
                                        @else
                                            <span class="text-info">Klik untuk mengakses</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @else
                        <p class="text-muted">Belum ada sesi tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card session-card">
                <div class="card-body p-4">
                    <!-- Session Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="card-title mb-1">Sesi {{ $session->session_order }}: {{ $session->session_title }}</h3>
                            @if($session->description)
                                <p class="text-muted mb-0">{{ $session->description }}</p>
                            @endif
                        </div>
                        @if($sessionProgress->status === 'passed')
                            <span class="badge badge-success p-2" style="font-size: 14px;">
                                <i class="mdi mdi-check-circle"></i> LULUS
                            </span>
                        @elseif($sessionProgress->status === 'failed')
                            <span class="badge badge-danger p-2" style="font-size: 14px;">
                                <i class="mdi mdi-close-circle"></i> TIDAK LULUS
                            </span>
                        @endif
                    </div>

                    <!-- Quiz Section -->
                    <div id="quizSection">
                        @if($sessionProgress->status === 'not_started')
                            {{-- Video hanya ditampilkan saat status not_started --}}
                            @if($session->has_video && ($session->video_url || $session->google_drive_file_id))
                                <!-- Video Section -->
                                <div class="video-container mb-4">
                                    <video id="sessionPlayer" playsinline controls>
                                        @if($session->google_drive_file_id)
                                            {{-- Video dari Google Drive - stream via Laravel untuk kontrol penuh --}}
                                            <source src="{{ route('hr.portal-training.video.stream', ['fileId' => $session->google_drive_file_id]) }}" type="video/mp4">
                                        @elseif($session->video_url)
                                            {{-- Video dari local storage --}}
                                            <source src="{{ asset("$session->video_url") }}" type="video/mp4">
                                        @endif
                                        Browser Anda tidak mendukung tag video.
                                    </video>
                                    {{-- Button untuk skip video (development only) --}}
                                    <div class="text-center mt-3" id="skip-video-btn" style="display: {{ (config('app.env') === 'local' || config('app.debug')) ? 'block' : 'none' }};">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="skipSessionVideo()">
                                            <i class="mdi mdi-skip-forward mr-2"></i>
                                            Skip Video (Dev Only)
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <!-- Not Started -->
                            {{-- Informasi Materi dan Filter untuk Tracking --}}
                            <div class="alert alert-info mb-4">
                                <h6 class="mb-2"><i class="mdi mdi-information"></i> Informasi Filter Soal:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Materi yang Digunakan:</strong>
                                        @if($assignmentMaterials && $assignmentMaterials->count() > 0)
                                            <ul class="mb-0 mt-1">
                                                @foreach($assignmentMaterials as $mat)
                                                    <li>{{ $mat->material_title }} (ID: {{ $mat->id }})</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-danger">Tidak ada materi yang dipilih</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Tingkat Kesulitan:</strong>
                                        @if($difficultyLevel)
                                            <span class="badge badge-secondary">{{ $difficultyLevel->level_name }} (ID: {{ $difficultyLevel->id }})</span>
                                        @else
                                            <span class="text-warning">Tidak di-set</span>
                                        @endif
                                        <br>
                                        <strong>Tema:</strong>
                                        @if($session->theme)
                                            <span class="badge badge-info">{{ $session->theme }}</span>
                                        @else
                                            <span class="text-muted">Tidak di-set</span>
                                        @endif
                                        <br>
                                        <strong>Jumlah Soal Dibutuhkan:</strong>
                                        <span class="badge badge-primary">{{ $session->question_count ?? 0 }} soal</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="mdi mdi-lightbulb-on"></i>
                                    Sistem akan mencari soal dari Bank Soal yang sesuai dengan materi, tingkat kesulitan, dan tema di atas.
                                </small>
                            </div>

                            <div class="text-center py-5">
                                <i class="mdi mdi-book-open-page-variant text-primary" style="font-size: 80px;"></i>
                                <h4 class="mt-3">Siap untuk Memulai Sesi?</h4>
                                <p class="text-muted">
                                    Sesi ini berisi {{ $session->question_count ?? 0 }} soal.
                                    @if($session->passing_score)
                                    Nilai minimum untuk lulus: {{ $session->passing_score }}
                                    @endif
                                </p>
                                @if(!$canStart)
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert"></i> Anda harus menyelesaikan sesi sebelumnya terlebih dahulu.
                                    </div>
                                @else
                                    @if($session->has_video && $session->video_url)
                                        <button type="button" class="btn btn-primary btn-lg" id="startSessionBtn" disabled>
                                            <i class="mdi mdi-play"></i> Mulai Sesi
                                        </button>
                                        <p class="text-muted mt-2" id="videoWarning">
                                            <i class="mdi mdi-information"></i> Silakan selesaikan video terlebih dahulu sebelum memulai sesi.
                                        </p>
                                    @else
                                        <button type="button" class="btn btn-primary btn-lg" id="startSessionBtn">
                                            <i class="mdi mdi-play"></i> Mulai Sesi
                                        </button>
                                    @endif
                                @endif
                            </div>

                        @elseif($sessionProgress->status === 'in_progress')
                            {{-- Jika ada video dan questions_data masih kosong, berarti video belum selesai - tampilkan video dulu --}}
                            @if($session->has_video && ($session->video_url || $session->google_drive_file_id) && (empty($sessionProgress->questions_data) || (is_array($sessionProgress->questions_data) && count($sessionProgress->questions_data) === 0)))
                                <!-- Video Section - Tampilkan video jika belum selesai -->
                                <div class="video-container mb-4">
                                    <video id="sessionPlayer" playsinline controls>
                                        @if($session->google_drive_file_id)
                                            {{-- Video dari Google Drive - stream via Laravel untuk kontrol penuh --}}
                                            <source src="{{ route('hr.portal-training.video.stream', ['fileId' => $session->google_drive_file_id]) }}" type="video/mp4">
                                        @elseif($session->video_url)
                                            {{-- Video dari local storage --}}
                                            <source src="{{ asset("$session->video_url") }}" type="video/mp4">
                                        @endif
                                        Browser Anda tidak mendukung tag video.
                                    </video>
                                    {{-- Button untuk skip video (development only) --}}
                                    <div class="text-center mt-3" id="skip-video-btn" style="display: {{ (config('app.env') === 'local' || config('app.debug')) ? 'block' : 'none' }};">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="skipSessionVideo()">
                                            <i class="mdi mdi-skip-forward mr-2"></i>
                                            Skip Video (Dev Only)
                                        </button>
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <i class="mdi mdi-information"></i> Silakan selesaikan video terlebih dahulu untuk melanjutkan ke soal.
                                    </div>
                                </div>
                            @else
                                <!-- In Progress - Show Questions -->
                                <div id="questionsContainer">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Jawab Pertanyaan Berikut:</h5>
                                    <span class="badge badge-info">Soal {{ is_countable($sessionProgress->questions_data ?? null) ? count($sessionProgress->questions_data) : 0 }}</span>
                                </div>

                                {{-- Informasi Materi dan Filter untuk Tracking --}}
                                <div class="alert alert-info mb-4">
                                    <h6 class="mb-2"><i class="mdi mdi-information"></i> Informasi Filter Soal:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Materi yang Digunakan:</strong>
                                            @if($assignmentMaterials && $assignmentMaterials->count() > 0)
                                                <ul class="mb-0 mt-1">
                                                    @foreach($assignmentMaterials as $mat)
                                                        <li>{{ $mat->material_title }} (ID: {{ $mat->id }})</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-danger">Tidak ada materi yang dipilih</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tingkat Kesulitan:</strong>
                                            @if($difficultyLevel)
                                                <span class="badge badge-secondary">{{ $difficultyLevel->level_name }} (ID: {{ $difficultyLevel->id }})</span>
                                            @else
                                                <span class="text-warning">Tidak di-set</span>
                                            @endif
                                            <br>
                                            <strong>Tema:</strong>
                                            @if($session->theme)
                                                <span class="badge badge-info">{{ $session->theme }}</span>
                                            @else
                                                <span class="text-muted">Tidak di-set</span>
                                            @endif
                                            <br>
                                            <strong>Jumlah Soal Dibutuhkan:</strong>
                                            <span class="badge badge-primary">{{ $session->question_count ?? 0 }} soal</span>
                                            <br>
                                            <strong>Video:</strong>
                                            @if($session->has_video && $session->video_url)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-video"></i> Ada Video
                                                </span>
                                                <small class="text-muted d-block mt-1">
                                                    Video harus diselesaikan sebelum mulai sesi
                                                </small>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="mdi mdi-video-off"></i> Tidak Ada Video
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="mdi mdi-lightbulb-on"></i>
                                        Sistem akan mencari soal dari Bank Soal yang sesuai dengan materi, tingkat kesulitan, dan tema di atas.
                                    </small>
                                </div>

                                <form id="quizForm">
                                    @if(isset($sessionProgress->questions_data) && is_array($sessionProgress->questions_data) && count($sessionProgress->questions_data) > 0)
                                        @foreach($sessionProgress->questions_data as $index => $question)
                                            <div class="question-card">
                                                <h6 class="mb-3">
                                                    <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                                    {!! $question['question'] !!}
                                                </h6>

                                                <div class="options-list">
                                                    @if(isset($question['answer_options']) && is_array($question['answer_options']))
                                                        @foreach($question['answer_options'] as $key => $option)
                                                            @php
                                                                $inputId = 'answer_' . $question['id'] . '_' . $key;
                                                            @endphp
                                                            <label class="option-label" for="{{ $inputId }}">
                                                                <input type="radio"
                                                                    id="{{ $inputId }}"
                                                                    name="answers[{{ $question['id'] }}]"
                                                                    value="{{ $key }}"
                                                                    required>
                                                                <span>{{ $option }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="mdi mdi-alert"></i> <strong>Pertanyaan belum tersedia.</strong>
                                            <br>
                                            <small class="mt-2 d-block">
                                                Kemungkinan penyebab:
                                                <ul class="mb-0 mt-2">
                                                    <li>Tidak ada soal di Bank Soal yang sesuai dengan filter di atas</li>
                                                    <li>Materi yang dipilih tidak memiliki soal dengan tingkat kesulitan "{{ $difficultyLevel->level_name ?? 'Tidak di-set' }}"</li>
                                                    <li>Soal di Bank Soal belum diaktifkan (is_active = false)</li>
                                                </ul>
                                                Silakan hubungi administrator untuk menambahkan soal yang sesuai.
                                            </small>
                                        </div>
                                    @endif

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="mdi mdi-check"></i> Submit Jawaban
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif {{-- End if video check for in_progress --}}

                        @elseif(in_array($sessionProgress->status, ['passed', 'failed']))
                            <!-- Completed - Show Result -->
                            <div class="text-center py-5">
                                @if($sessionProgress->status === 'passed')
                                    <i class="mdi mdi-trophy text-success" style="font-size: 80px;"></i>
                                    <h4 class="mt-3 text-success">Selamat! Anda Lulus</h4>
                                @else
                                    <i class="mdi mdi-emoticon-sad text-danger" style="font-size: 80px;"></i>
                                    <h4 class="mt-3 text-danger">Belum Lulus</h4>
                                @endif

                                <div class="card mt-4 mx-auto" style="max-width: 400px;">
                                    <div class="card-body">
                                        <h2 class="mb-0">{{ $sessionProgress->score ?? 0 }}</h2>
                                        <small class="text-muted">Nilai Anda</small>
                                        <hr>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="mb-0 text-success">{{ $sessionProgress->correct_answers_count ?? 0 }}</h4>
                                                <small>Benar</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="mb-0 text-danger">{{ ($sessionProgress->total_questions ?? 0) - ($sessionProgress->correct_answers_count ?? 0) }}</h4>
                                                <small>Salah</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($sessionProgress->status === 'failed')
                                    @if(isset($allowRetry) && $allowRetry)
                                        <button type="button" class="btn btn-warning btn-lg mt-4" id="retrySessionBtn">
                                            <i class="mdi mdi-replay"></i> Coba Lagi
                                        </button>
                                    @else
                                        <div class="alert alert-info mt-4">
                                            <i class="mdi mdi-information"></i> Training ini tidak mengizinkan pengulangan.
                                        </div>
                                    @endif
                                @endif

                                @php
                                    // Get next session - user can always proceed to next session regardless of pass/fail
                                    $currentSession = $sessionProgress->session;
                                    $nextSession = \App\Models\TrainingSession::where('training_id', $currentSession->training_id)
                                        ->where('session_order', '>', $currentSession->session_order)
                                        ->active()
                                        ->orderBy('session_order', 'asc')
                                        ->first();
                                @endphp
                                @if($nextSession)
                                    <a href="{{ route('hr.portal-training.sessions.show', [$assignment->id, $nextSession->id]) }}"
                                       class="btn btn-primary btn-lg mt-3">
                                        <i class="mdi mdi-arrow-right"></i> Lanjut ke Sesi Berikutnya
                                    </a>
                                    @if($sessionProgress->status === 'failed')
                                        <p class="text-muted mt-2">
                                            <small><i class="mdi mdi-information-outline"></i> Anda dapat melanjutkan ke sesi berikutnya meskipun belum lulus.</small>
                                        </p>
                                    @endif
                                @else
                                    <div class="alert alert-success mt-3">
                                        <i class="mdi mdi-check-circle"></i> Selamat! Anda telah menyelesaikan semua sesi training.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        const assignmentId = {{ $assignment->id }};
        const sessionId = {{ $session->id }};

        // Initialize Plyr video player if video exists and (status is not_started OR in_progress but questions_data is empty)
        @if($session->has_video && ($session->video_url || $session->google_drive_file_id) && ($sessionProgress->status === 'not_started' || ($sessionProgress->status === 'in_progress' && (empty($sessionProgress->questions_data) || (is_array($sessionProgress->questions_data) && count($sessionProgress->questions_data) === 0)))))
            const sessionPlayer = new Plyr('#sessionPlayer', {
                controls: ['play-large', 'play', 'current-time', 'mute', 'volume', 'fullscreen'],
                // Tidak ada progress bar dan settings untuk mencegah skip dan speed control
                settings: [], // Hapus settings menu
                speed: {
                    selected: 1,
                    options: [1] // Hanya 1x speed, tidak bisa diubah
                },
                // Disable keyboard shortcuts untuk skip
                keyboard: {
                    focused: true,
                    global: false
                }
            });

            let sessionVideoProgressInterval;
            let lastVideoPosition = 0;
            let maxWatchedPosition = 0;
            let isSeeking = false;
            let videoCompleted = false;
            const startSessionBtn = document.getElementById('startSessionBtn');
            const videoWarning = document.getElementById('videoWarning');

            // Function untuk enable button "Mulai Sesi"
            function enableStartButton() {
                if (startSessionBtn) {
                    startSessionBtn.disabled = false;
                    if (videoWarning) {
                        videoWarning.style.display = 'none';
                    }
                }
            }

            // Cek saat video loaded - jika sudah selesai sebelumnya, enable button
            sessionPlayer.on('loadedmetadata', () => {
                // Jika video sudah pernah ditonton sampai selesai, enable button
                // Ini untuk handle case ketika user kembali ke halaman ini
                if (sessionPlayer.duration && maxWatchedPosition >= sessionPlayer.duration * 0.95) {
                    videoCompleted = true;
                    enableStartButton();
                }
            });

            // Mencegah skip video - hanya bisa maju jika sudah menonton bagian tersebut
            sessionPlayer.on('seeked', (e) => {
                const currentTime = sessionPlayer.currentTime;
                // Jika user mencoba skip ke depan (lebih dari 5 detik dari posisi maksimal yang sudah ditonton)
                if (currentTime > maxWatchedPosition + 5) {
                    // Kembalikan ke posisi maksimal yang sudah ditonton
                    sessionPlayer.currentTime = maxWatchedPosition;
                    alert('Anda tidak dapat melewati bagian video yang belum ditonton. Silakan tonton video secara berurutan.');
                } else {
                    // Update maxWatchedPosition jika user mundur atau dalam range yang diizinkan
                    if (currentTime <= maxWatchedPosition) {
                        maxWatchedPosition = currentTime;
                    }
                }
            });

            // Mencegah keyboard shortcuts untuk skip
            document.addEventListener('keydown', (e) => {
                const video = sessionPlayer.media;
                // Blokir arrow keys untuk skip
                if (e.target === video || e.target.closest('.plyr')) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            // Update maxWatchedPosition saat video diputar
            sessionPlayer.on('timeupdate', () => {
                const currentTime = sessionPlayer.currentTime;
                const duration = sessionPlayer.duration;

                // Hanya update jika video sedang diputar dan tidak sedang seek
                if (!sessionPlayer.paused && !isSeeking) {
                    if (currentTime > maxWatchedPosition) {
                        maxWatchedPosition = currentTime;
                    }
                }

                // Cek apakah video sudah selesai (95% atau lebih untuk toleransi)
                if (duration && maxWatchedPosition >= duration * 0.95 && !videoCompleted) {
                    videoCompleted = true;
                    enableStartButton();
                    // Jika status sudah in_progress, langsung generate questions
                    @if($sessionProgress->status === 'in_progress')
                    generateQuestionsAfterVideo();
                    @endif
                }
            });

            // Cek saat video selesai diputar
            sessionPlayer.on('ended', () => {
                videoCompleted = true;
                maxWatchedPosition = sessionPlayer.duration;
                enableStartButton();
                // Jika status sudah in_progress, langsung generate questions
                @if($sessionProgress->status === 'in_progress')
                generateQuestionsAfterVideo();
                @endif
            });

            // Function untuk generate questions setelah video selesai (untuk status in_progress)
            @if($sessionProgress->status === 'in_progress')
            function generateQuestionsAfterVideo() {
                // Panggil startSession untuk generate questions
                $.ajax({
                    url: '{{ route("hr.portal-training.sessions.start", [$assignment->id, $session->id]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload halaman untuk menampilkan soal
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error generating questions:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat soal. Silakan refresh halaman.'
                        });
                    }
                });
            }
            @endif

            // Function untuk enable button "Mulai Sesi"
            function enableStartButton() {
                if (startSessionBtn) {
                    startSessionBtn.disabled = false;
                    if (videoWarning) {
                        videoWarning.style.display = 'none';
                    }
                }
            }

            // Track saat user mulai seek
            sessionPlayer.on('seeking', () => {
                isSeeking = true;
            });

            // Reset flag setelah seek selesai
            sessionPlayer.on('seeked', () => {
                isSeeking = false;
            });

            // Function untuk skip video (development only)
            function skipSessionVideo() {
                if (confirm('Apakah Anda yakin ingin melewati video ini? (Development Only)')) {
                    const duration = sessionPlayer.duration || 100;
                    // Set video ke akhir
                    sessionPlayer.currentTime = duration;
                    maxWatchedPosition = duration;
                    videoCompleted = true;
                    // Enable button setelah skip
                    enableStartButton();
                }
            }
        @endif

        $(document).ready(function() {
            // Option selection styling
            $('.option-label input[type="radio"]').on('change', function() {
                $('.option-label').removeClass('selected');
                $(this).closest('.option-label').addClass('selected');
            });

            // Start Session
            $('#startSessionBtn').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memuat...');

                $.ajax({
                    url: "{{ route('hr.portal-training.sessions.start', [$assignment->id, $session->id]) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="mdi mdi-play"></i> Mulai Sesi');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // Submit Quiz
            $('#quizForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Mengirim...');

                $.ajax({
                    url: "{{ route('hr.portal-training.sessions.submit', [$assignment->id, $session->id]) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Submit response:', response);

                        // Cek apakah ada next session (gunakan has_next_session atau next_session_url)
                        const hasNextSession = response.has_next_session === true || (response.next_session_url && response.next_session_url !== null && response.next_session_url !== '');
                        const nextSessionUrl = response.next_session_url;
                        const nextSessionLocked = response.next_session_locked === true;

                        // Jika next session locked, tampilkan pesan error
                        if (nextSessionLocked) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sesi Berikutnya Terkunci',
                                text: 'Sesi berikutnya masih terkunci. Pastikan semua sesi sebelumnya sudah selesai. Silakan refresh halaman.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                            return;
                        }

                        // Jika ada next session, langsung redirect tanpa tampilkan nilai
                        if (hasNextSession && nextSessionUrl) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Jawaban Disimpan',
                                text: 'Mengalihkan ke sesi berikutnya...',
                                timer: 1000,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                // Pastikan URL valid sebelum redirect
                                if (nextSessionUrl && nextSessionUrl !== 'null' && nextSessionUrl !== '' && nextSessionUrl !== 'undefined') {
                                    console.log('Redirecting to:', nextSessionUrl);
                                    // Coba redirect, jika gagal tampilkan error
                                    try {
                                        window.location.href = nextSessionUrl;
                                        // Fallback: jika redirect tidak terjadi dalam 2 detik, tampilkan error
                                        setTimeout(function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Redirect Gagal',
                                                text: 'Gagal mengalihkan ke sesi berikutnya. Silakan klik tombol "Lanjut ke Sesi Berikutnya" secara manual.',
                                                confirmButtonText: 'OK'
                                            });
                                        }, 2000);
                                    } catch (e) {
                                        console.error('Redirect error:', e);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Terjadi kesalahan saat redirect. Silakan refresh halaman dan coba lagi.',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    }
                                } else {
                                    console.error('Invalid next_session_url:', nextSessionUrl);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'URL sesi berikutnya tidak valid. Silakan refresh halaman dan coba lagi.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            });
                        } else {
                            // Tidak ada next session, reload untuk tampilkan hasil akhir
                            console.log('No next session, reloading to show result');
                            Swal.fire({
                                icon: response.passed ? 'success' : 'info',
                                title: response.passed ? 'Lulus!' : 'Selesai',
                                text: response.message || 'Jawaban telah disimpan.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Submit Jawaban');

                        // Tampilkan error message yang lebih detail
                        let errorMessage = 'Terjadi kesalahan saat menyimpan jawaban.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.errors) {
                                // Jika ada validation errors
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('<br>');
                            }
                        } else if (xhr.status === 0) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Halaman tidak ditemukan. Silakan refresh halaman.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan pada server. Silakan hubungi administrator.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });

                        console.error('Submit error:', xhr);
                    }
                });
            });

            // Retry Session
            $('#retrySessionBtn').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memuat...');

                $.ajax({
                    url: "{{ route('hr.portal-training.sessions.retry', [$assignment->id, $session->id]) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="mdi mdi-replay"></i> Coba Lagi');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });
        });
    </script>
@endsection

@extends('main.layouts.main')
@section('title')
    Materi Training - {{ $material->material_title }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        .video-container {
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .material-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .progress-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
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
    </style>
@endsection
@section('page-title')
    Materi Training
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ $material->material_title }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.index') }}">Portal Training</a></li>
                    <li class="breadcrumb-item active">Materi</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="material-info">
                            <h4>{{ $material->material_title }}</h4>
                            @if($material->description)
                                <p class="text-muted">{{ $material->description }}</p>
                            @endif
                            @if($material->category)
                                <p class="mb-0">
                                    <span class="badge badge-info">{{ $material->category->category_name }}</span>
                                </p>
                            @endif
                        </div>

                        @if($materialProgress)
                            <div class="progress-info">
                                <h5>Progress Menonton</h5>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $materialProgress->progress_percentage }}%"
                                         aria-valuenow="{{ $materialProgress->progress_percentage }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($materialProgress->progress_percentage, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Durasi menonton: {{ gmdate('H:i:s', $materialProgress->watch_duration_seconds) }}
                                </small>
                            </div>
                        @endif

                        @if($material->video_path)
                            <div class="video-container">
                                <video id="player" playsinline controls>
                                    <source src="{{ asset('sipo_krisan/' . $material->video_path) }}" type="video/mp4">
                                </video>
                                {{-- Button untuk skip video (development only) --}}
                                <div class="text-center mt-3" id="skip-video-btn" style="display: {{ (config('app.env') === 'local' || config('app.debug')) ? 'block' : 'none' }};">
                                    <button type="button" class="btn btn-warning btn-sm" onclick="skipVideo()">
                                        <i class="mdi mdi-skip-forward mr-2"></i>
                                        Skip Video (Dev Only)
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Video belum tersedia.
                            </div>
                        @endif

                        <div class="mt-4">
                            @if($materialProgress && $materialProgress->status == 'completed')
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        @if($previousMaterial)
                                            <a href="{{ route('hr.portal-training.materials.show', $previousMaterial->id) }}"
                                               class="btn btn-secondary btn-lg">
                                                <i class="mdi mdi-arrow-left mr-2"></i>
                                                Materi Sebelumnya
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            // Cari session pertama dari training untuk redirect
                                            $firstSession = null;
                                            if ($assignment && $assignment->training) {
                                                $firstSession = \App\Models\TrainingSession::where('training_id', $assignment->training_id)
                                                    ->active()
                                                    ->orderBy('session_order', 'asc')
                                                    ->first();
                                            }
                                        @endphp
                                        @if($firstSession && $assignment)
                                            <a href="{{ route('hr.portal-training.sessions.show', [$assignment->id, $firstSession->id]) }}"
                                               class="btn btn-success btn-lg">
                                                <i class="mdi mdi-play-circle mr-2"></i>
                                                Lanjut ke Sesi Training
                                            </a>
                                        @else
                                            <a href="{{ route('hr.portal-training.exams.show', $material->id) }}"
                                               class="btn btn-success btn-lg">
                                                <i class="mdi mdi-file-document-box-check mr-2"></i>
                                                Mulai Ujian
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        @if($nextMaterial)
                                            <a href="{{ route('hr.portal-training.materials.show', $nextMaterial->id) }}"
                                               class="btn btn-primary btn-lg">
                                                Materi Berikutnya
                                                <i class="mdi mdi-arrow-right ml-2"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('hr.portal-training.index') }}"
                                               class="btn btn-info btn-lg">
                                                <i class="mdi mdi-home mr-2"></i>
                                                Kembali ke Dashboard
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information mr-2"></i>
                                    Selesaikan menonton video terlebih dahulu untuk mengakses ujian dan materi berikutnya.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
        <script>
            const player = new Plyr('#player', {
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

            let progressUpdateInterval;
            let lastPosition = {{ $materialProgress->last_position_seconds ?? 0 }};
            let maxWatchedPosition = lastPosition; // Track posisi maksimal yang sudah ditonton
            let isSeeking = false;

            // Set video position hanya jika belum pernah menonton
            if (lastPosition > 0) {
                player.currentTime = lastPosition;
                maxWatchedPosition = lastPosition;
            }

            // Mencegah skip video - hanya bisa maju jika sudah menonton bagian tersebut
            player.on('seeked', (e) => {
                const currentTime = player.currentTime;
                // Jika user mencoba skip ke depan (lebih dari 5 detik dari posisi maksimal yang sudah ditonton)
                if (currentTime > maxWatchedPosition + 5) {
                    // Kembalikan ke posisi maksimal yang sudah ditonton
                    player.currentTime = maxWatchedPosition;
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
                const video = player.media;
                // Blokir arrow keys untuk skip
                if (e.target === video || e.target.closest('.plyr')) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        return false;
                    }
                    // Blokir space bar untuk play/pause (opsional, bisa dihapus jika ingin tetap bisa pause)
                    // if (e.key === ' ') {
                    //     e.preventDefault();
                    //     return false;
                    // }
                }
            });

            // Update maxWatchedPosition saat video diputar
            player.on('timeupdate', () => {
                const currentTime = player.currentTime;
                // Hanya update jika video sedang diputar dan tidak sedang seek
                if (!player.paused && !isSeeking) {
                    if (currentTime > maxWatchedPosition) {
                        maxWatchedPosition = currentTime;
                    }
                }
            });

            // Track saat user mulai seek
            player.on('seeking', () => {
                isSeeking = true;
            });

            // Reset flag setelah seek selesai
            player.on('seeked', () => {
                isSeeking = false;
            });

            // Update progress saat video mulai diputar
            player.on('play', () => {
                // Update progress pertama kali saat video mulai diputar
                if (player.duration > 0) {
                    updateProgress(player.currentTime, player.duration);
                }
            });

            // Update progress saat video diputar
            player.on('timeupdate', () => {
                const currentTime = player.currentTime;
                const duration = player.duration;
                const percentage = duration > 0 ? (currentTime / duration) * 100 : 0;
                lastPosition = currentTime;

                // Update progress bar di UI secara real-time
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar && duration > 0) {
                    progressBar.style.width = percentage + '%';
                    progressBar.setAttribute('aria-valuenow', percentage);
                    const progressText = progressBar.parentElement.parentElement.querySelector('small');
                    if (progressText) {
                        progressText.textContent = 'Durasi menonton: ' + formatTime(currentTime);
                    }
                    // Update percentage text di progress bar
                    const percentageText = progressBar.parentElement.querySelector('small.text-muted strong');
                    if (percentageText) {
                        percentageText.textContent = percentage.toFixed(1) + '%';
                    }
                }

                // Update progress ke server setiap 5 detik
                if (!progressUpdateInterval && duration > 0) {
                    // Update pertama kali
                    updateProgress(currentTime, duration);
                    // Set interval untuk update berikutnya
                    progressUpdateInterval = setInterval(() => {
                        updateProgress(player.currentTime, player.duration);
                    }, 5000);
                }
            });

            // Helper function untuk format waktu
            function formatTime(seconds) {
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = Math.floor(seconds % 60);
                return (h > 0 ? h + ':' : '') + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            }

            // Function untuk skip video (development only)
            function skipVideo() {
                if (confirm('Apakah Anda yakin ingin melewati video ini? (Development Only)')) {
                    const duration = player.duration || 100; // Fallback jika duration belum loaded
                    // Update progress ke 100% dan reload page untuk update status
                    updateProgress(duration, duration).then(() => {
                        // Reload page setelah update selesai untuk memastikan status terupdate
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    });
                }
            }

            // Update progress saat video selesai
            player.on('ended', () => {
                clearInterval(progressUpdateInterval);
                updateProgress(player.duration, player.duration);
                // Tampilkan tombol next setelah video selesai
                showNextButton();
            });

            function updateProgress(currentTime, duration) {
                if (!duration || duration === 0) {
                    console.warn('Video duration not available yet');
                    return Promise.reject('Duration not available');
                }

                return fetch('{{ route("hr.portal-training.materials.watch", $material->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        current_time: currentTime,
                        duration: duration,
                        assignment_id: {{ $assignment ? $assignment->id : 'null' }}
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update progress bar di UI jika ada
                        const progressBar = document.querySelector('.progress-bar');
                        if (progressBar && data.progress !== undefined) {
                            progressBar.style.width = data.progress + '%';
                            progressBar.setAttribute('aria-valuenow', data.progress);
                            progressBar.textContent = data.progress.toFixed(1) + '%';
                        }

                        // Jika progress >= 100%, reload untuk update status
                        if (data.progress >= 100) {
                            // Tampilkan tombol next setelah video selesai
                            showNextButton();
                            // Reload page untuk update status setelah 1 detik
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                        return data;
                    }
                })
                .catch(error => {
                    console.error('Error updating progress:', error);
                    throw error;
                });
            }

            function showNextButton() {
                // Sembunyikan alert info jika ada
                const alertInfo = document.querySelector('.alert-info');
                if (alertInfo) {
                    alertInfo.style.display = 'none';
                }

                // Tampilkan tombol next jika ada
                @if($nextMaterial)
                    const nextButtonHtml = `
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    @if($previousMaterial)
                                        <a href="{{ route('hr.portal-training.materials.show', $previousMaterial->id) }}"
                                           class="btn btn-secondary btn-lg">
                                            <i class="mdi mdi-arrow-left mr-2"></i>
                                            Materi Sebelumnya
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    @php
                                        // Cari session pertama dari training untuk redirect
                                        $firstSession = null;
                                        if ($assignment && $assignment->training) {
                                            $firstSession = \App\Models\TrainingSession::where('training_id', $assignment->training_id)
                                                ->active()
                                                ->orderBy('session_order', 'asc')
                                                ->first();
                                        }
                                    @endphp
                                    @if($firstSession && $assignment)
                                        <a href="{{ route('hr.portal-training.sessions.show', [$assignment->id, $firstSession->id]) }}"
                                           class="btn btn-success btn-lg">
                                            <i class="mdi mdi-play-circle mr-2"></i>
                                            Lanjut ke Sesi Training
                                        </a>
                                    @else
                                        <a href="{{ route('hr.portal-training.exams.show', $material->id) }}"
                                           class="btn btn-success btn-lg">
                                            <i class="mdi mdi-file-document-box-check mr-2"></i>
                                            Mulai Ujian
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('hr.portal-training.materials.show', $nextMaterial->id) }}"
                                       class="btn btn-primary btn-lg">
                                        Materi Berikutnya
                                        <i class="mdi mdi-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    const buttonContainer = document.querySelector('.mt-4');
                    if (buttonContainer) {
                        buttonContainer.innerHTML = nextButtonHtml;
                    }
                @else
                    const nextButtonHtml = `
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    @if($previousMaterial)
                                        <a href="{{ route('hr.portal-training.materials.show', $previousMaterial->id) }}"
                                           class="btn btn-secondary btn-lg">
                                            <i class="mdi mdi-arrow-left mr-2"></i>
                                            Materi Sebelumnya
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    @php
                                        // Cari session pertama dari training untuk redirect
                                        $firstSession = null;
                                        if ($assignment && $assignment->training) {
                                            $firstSession = \App\Models\TrainingSession::where('training_id', $assignment->training_id)
                                                ->active()
                                                ->orderBy('session_order', 'asc')
                                                ->first();
                                        }
                                    @endphp
                                    @if($firstSession && $assignment)
                                        <a href="{{ route('hr.portal-training.sessions.show', [$assignment->id, $firstSession->id]) }}"
                                           class="btn btn-success btn-lg">
                                            <i class="mdi mdi-play-circle mr-2"></i>
                                            Lanjut ke Sesi Training
                                        </a>
                                    @else
                                        <a href="{{ route('hr.portal-training.exams.show', $material->id) }}"
                                           class="btn btn-success btn-lg">
                                            <i class="mdi mdi-file-document-box-check mr-2"></i>
                                            Mulai Ujian
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('hr.portal-training.index') }}"
                                       class="btn btn-info btn-lg">
                                        <i class="mdi mdi-home mr-2"></i>
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    const buttonContainer = document.querySelector('.mt-4');
                    if (buttonContainer) {
                        buttonContainer.innerHTML = nextButtonHtml;
                    }
                @endif
            }

            // Cleanup interval saat page unload
            window.addEventListener('beforeunload', () => {
                if (progressUpdateInterval) {
                    clearInterval(progressUpdateInterval);
                }
            });
        </script>
    @endsection


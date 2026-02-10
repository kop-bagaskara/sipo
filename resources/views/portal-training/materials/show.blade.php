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
                                <video id="player" playsinline controls data-plyr-config='{"controls": ["play-large", "play", "progress", "current-time", "mute", "volume", "settings", "fullscreen"], "settings": ["quality", "speed"], "speed": {"selected": 1, "options": [1]}}'>
                                    <source src="{{ asset('storage/' . $material->video_path) }}" type="video/mp4">
                                </video>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Video belum tersedia.
                            </div>
                        @endif

                        <div class="mt-4">
                            @if($materialProgress && $materialProgress->status == 'completed')
                                <a href="{{ route('hr.portal-training.exams.show', $material->id) }}"
                                   class="btn btn-success btn-lg">
                                    <i class="mdi mdi-file-document-box-check mr-2"></i>
                                    Mulai Ujian
                                </a>
                            @else
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information mr-2"></i>
                                    Selesaikan menonton video terlebih dahulu untuk mengakses ujian.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('js')
        <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
        <script>
            const player = new Plyr('#player', {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'],
                settings: ['quality'],
                speed: {
                    selected: 1,
                    options: [1] // Hanya 1x speed (tidak bisa di-accelerate)
                }
            });

            let progressUpdateInterval;
            let lastPosition = {{ $materialProgress->last_position_seconds ?? 0 }};

            // Set video position
            if (lastPosition > 0) {
                player.currentTime = lastPosition;
            }

            // Update progress saat video diputar
            player.on('timeupdate', () => {
                const currentTime = player.currentTime;
                const duration = player.duration;
                const percentage = duration > 0 ? (currentTime / duration) * 100 : 0;
                lastPosition = currentTime;

                // Update progress setiap 5 detik
                if (!progressUpdateInterval) {
                    progressUpdateInterval = setInterval(() => {
                        updateProgress(percentage, currentTime);
                    }, 5000);
                }
            });

            // Update progress saat video selesai
            player.on('ended', () => {
                updateProgress(100, player.duration);
                clearInterval(progressUpdateInterval);
            });

            function updateProgress(percentage, position) {
                fetch('{{ route("hr.portal-training.materials.watch", $material->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        progress_percentage: percentage,
                        position_seconds: position
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && percentage >= 100) {
                        // Reload page untuk update status
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error updating progress:', error);
                });
            }

            // Cleanup interval saat page unload
            window.addEventListener('beforeunload', () => {
                if (progressUpdateInterval) {
                    clearInterval(progressUpdateInterval);
                }
            });
        </script>
    @endsection


@extends('main.layouts.main')
@section('title')
    Edit Master Data Training
@endsection
@section('css')
    <style>
        .session-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        .session-item .card-header {
            background-color: #f8f9fa;
        }
    </style>
@endsection
@section('page-title')
    Edit Master Data Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Edit Master Data Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.master.training-masters.index') }}">Master Data Training</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Form Edit Training</h4>
                </div>
                <form id="editTrainingForm" action="{{ route('hr.portal-training.master.training-masters.update', $training->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_name">Nama Training <span class="text-danger">*</span></label>
                                    <input type="text" name="training_name" id="training_name" class="form-control"
                                        value="{{ old('training_name', $training->training_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
                                    <select name="training_type" id="training_type" class="form-control" required>
                                        <option value="">Pilih Tipe Training</option>
                                        <option value="mandatory" {{ old('training_type', $training->training_type) == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                                        <option value="optional" {{ old('training_type', $training->training_type) == 'optional' ? 'selected' : '' }}>Optional</option>
                                        <option value="certification" {{ old('training_type', $training->training_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                                        <option value="skill_development" {{ old('training_type', $training->training_type) == 'skill_development' ? 'selected' : '' }}>Skill Development</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_method">Metode Training <span class="text-danger">*</span></label>
                                    <select name="training_method" id="training_method" class="form-control" required>
                                        <option value="">Pilih Metode Training</option>
                                        <option value="classroom" {{ old('training_method', $training->training_method) == 'classroom' ? 'selected' : '' }}>Kelas Tatap Muka</option>
                                        <option value="online" {{ old('training_method', $training->training_method) == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="hybrid" {{ old('training_method', $training->training_method) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        <option value="workshop" {{ old('training_method', $training->training_method) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                        <option value="seminar" {{ old('training_method', $training->training_method) == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="draft" {{ old('status', $training->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $training->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="ongoing" {{ old('status', $training->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $training->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="allow_retry" name="allow_retry" value="1" {{ old('allow_retry', $training->allow_retry) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="allow_retry">
                                            <strong>Izinkan Pengulangan Training</strong>
                                            <small class="text-muted d-block">Jika dicentang, peserta dapat mengulangi training ini setelah selesai</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="notes">Catatan Tambahan</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $training->notes) }}</textarea>
                        </div>

                        <!-- Pengaturan Sesi Training -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="mb-0"><strong>Pengaturan Sesi Training</strong></label>
                                <button type="button" class="btn btn-sm btn-primary" id="addSessionBtn">
                                    <i class="mdi mdi-plus mr-1"></i>
                                    Tambah Sesi
                                </button>
                            </div>
                            <div id="sessionsContainer">
                                @if($training->sessions && $training->sessions->count() > 0)
                                    @foreach($training->sessions as $session)
                                        <div class="card mb-3 session-item" data-session-index="{{ $session->session_order }}">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Sesi {{ $session->session_order }}</h5>
                                                    <button type="button" class="btn btn-sm btn-danger remove-session-btn">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <input type="hidden" name="sessions[{{ $session->session_order }}][id]" value="{{ $session->id }}">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Judul Sesi <span class="text-danger">*</span></label>
                                                            <input type="text" name="sessions[{{ $session->session_order }}][session_title]"
                                                                class="form-control" required value="{{ $session->session_title }}" placeholder="Contoh: Pengenalan K3">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Deskripsi</label>
                                                            <textarea name="sessions[{{ $session->session_order }}][description]"
                                                                class="form-control" rows="2"
                                                                placeholder="Deskripsi singkat tentang sesi ini">{{ $session->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Tingkat Kesulitan <span class="text-danger">*</span></label>
                                                            <select name="sessions[{{ $session->session_order }}][difficulty_level_id]" class="form-control" required>
                                                                <option value="">Pilih Tingkat Kesulitan</option>
                                                                @if(isset($difficultyLevels) && $difficultyLevels->count() > 0)
                                                                    @foreach($difficultyLevels as $level)
                                                                        <option value="{{ $level->id }}" {{ $session->difficulty_level_id == $level->id ? 'selected' : '' }}>
                                                                            {{ $level->level_name }}
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    {{-- Fallback jika data tidak ada --}}
                                                                    <option value="1" {{ $session->difficulty_level_id == 1 ? 'selected' : '' }}>Mudah</option>
                                                                    <option value="2" {{ $session->difficulty_level_id == 2 ? 'selected' : '' }}>Sedang</option>
                                                                    <option value="3" {{ $session->difficulty_level_id == 3 ? 'selected' : '' }}>Sulit</option>
                                                                    <option value="4" {{ $session->difficulty_level_id == 4 ? 'selected' : '' }}>Sangat Sulit</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Tema Pertanyaan</label>
                                                            <input type="text" name="sessions[{{ $session->session_order }}][theme]"
                                                                class="form-control"
                                                                placeholder="Contoh: K3, ISO, dll (opsional)"
                                                                value="{{ $session->theme ?? '' }}">
                                                            <small class="text-muted">Untuk filter pertanyaan dari Question Bank</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Jumlah Soal <span class="text-danger">*</span></label>
                                                            <input type="number" name="sessions[{{ $session->session_order }}][question_count]"
                                                                class="form-control" min="1" value="{{ $session->question_count ?? 5 }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Nilai Min. Lulus <span class="text-danger">*</span></label>
                                                            <input type="number" name="sessions[{{ $session->session_order }}][passing_score]"
                                                                class="form-control" min="0" max="100" step="0.01"
                                                                value="{{ $session->passing_score ?? 70.00 }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-check mb-3">
                                                            <input type="checkbox" name="sessions[{{ $session->session_order }}][has_video]"
                                                                class="form-check-input has-video-checkbox"
                                                                id="has_video_{{ $session->session_order }}" value="1"
                                                                {{ $session->has_video ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="has_video_{{ $session->session_order }}">
                                                                <strong>Sesi ini memiliki video</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row video-fields" style="display: {{ $session->has_video ? 'block' : 'none' }};">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>URL Video atau Link Google Drive <span class="text-danger">*</span></label>
                                                            <input type="text" name="sessions[{{ $session->session_order }}][video_input]"
                                                                class="form-control video-input-field"
                                                                id="video_input_{{ $session->session_order }}"
                                                                value="{{ $session->google_drive_file_id ? 'https://drive.google.com/file/d/' . $session->google_drive_file_id . '/view' : $session->video_url }}"
                                                                placeholder="https://drive.google.com/file/d/... atau path/to/video.mp4"
                                                                {{ $session->has_video ? 'required' : '' }}>
                                                            <small class="text-muted">
                                                                <strong>Opsi 1 (Google Drive):</strong> Paste link Google Drive (contoh: https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j/view)<br>
                                                                <strong>Opsi 2 (Local):</strong> Masukkan path video (contoh: storage/training/video.mp4 atau https://example.com/video.mp4)
                                                            </small>
                                                            <input type="hidden" name="sessions[{{ $session->session_order }}][video_url]" class="video-url-hidden" value="{{ $session->video_url }}">
                                                            <input type="hidden" name="sessions[{{ $session->session_order }}][google_drive_file_id]" class="google-drive-hidden" value="{{ $session->google_drive_file_id }}">
                                                            <input type="hidden" name="sessions[{{ $session->session_order }}][video_source]" class="video-source-hidden" value="{{ $session->video_source ?? 'local' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Durasi Video (detik)</label>
                                                            <input type="number" name="sessions[{{ $session->session_order }}][video_duration_seconds]"
                                                                class="form-control" min="1"
                                                                value="{{ $session->video_duration_seconds }}"
                                                                placeholder="3600">
                                                            <small class="text-muted">Durasi dalam detik (contoh: 3600 = 1 jam)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="sessions[{{ $session->session_order }}][session_order]" value="{{ $session->session_order }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <small class="text-muted">Atur urutan sesi training. Setiap sesi bisa memiliki video atau tidak.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-save mr-1"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('hr.portal-training.master.training-masters.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-times mr-1"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            let sessionCounter = {{ $training->sessions ? $training->sessions->max('session_order') : 0 }};

            // Difficulty levels data untuk JavaScript
            const difficultyLevels = @json(isset($difficultyLevels) ? $difficultyLevels->map(function($level) {
                return ['id' => $level->id, 'name' => $level->level_name];
            }) : []);

            // Add new session
            $('#addSessionBtn').on('click', function() {
                sessionCounter++;

                // Build difficulty level options
                let difficultyOptions = '<option value="">Pilih Tingkat Kesulitan</option>';
                if (difficultyLevels.length > 0) {
                    difficultyLevels.forEach(function(level) {
                        difficultyOptions += `<option value="${level.id}">${level.name}</option>`;
                    });
                } else {
                    // Fallback
                    difficultyOptions += '<option value="1">Mudah</option>';
                    difficultyOptions += '<option value="2">Sedang</option>';
                    difficultyOptions += '<option value="3">Sulit</option>';
                    difficultyOptions += '<option value="4">Sangat Sulit</option>';
                }

                const sessionHtml = `
                    <div class="card mb-3 session-item" data-session-index="${sessionCounter}">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Sesi ${sessionCounter}</h5>
                                <button type="button" class="btn btn-sm btn-danger remove-session-btn">
                                    <i class="mdi mdi-delete"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Judul Sesi <span class="text-danger">*</span></label>
                                        <input type="text" name="sessions[${sessionCounter}][session_title]"
                                            class="form-control" required placeholder="Contoh: Pengenalan K3">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="sessions[${sessionCounter}][description]"
                                            class="form-control" rows="2"
                                            placeholder="Deskripsi singkat tentang sesi ini"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tingkat Kesulitan <span class="text-danger">*</span></label>
                                        <select name="sessions[${sessionCounter}][difficulty_level_id]" class="form-control" required>
                                            ${difficultyOptions}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tema Pertanyaan</label>
                                        <input type="text" name="sessions[${sessionCounter}][theme]"
                                            class="form-control"
                                            placeholder="Contoh: K3, ISO, dll (opsional)">
                                        <small class="text-muted">Untuk filter pertanyaan dari Question Bank</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Jumlah Soal <span class="text-danger">*</span></label>
                                        <input type="number" name="sessions[${sessionCounter}][question_count]"
                                            class="form-control" min="1" value="5" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nilai Min. Lulus <span class="text-danger">*</span></label>
                                        <input type="number" name="sessions[${sessionCounter}][passing_score]"
                                            class="form-control" min="0" max="100" step="0.01" value="70.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="sessions[${sessionCounter}][has_video]"
                                            class="form-check-input has-video-checkbox"
                                            id="has_video_${sessionCounter}" value="1">
                                        <label class="form-check-label" for="has_video_${sessionCounter}">
                                            <strong>Sesi ini memiliki video</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row video-fields" style="display: none;">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>URL Video atau Link Google Drive <span class="text-danger">*</span></label>
                                        <input type="text" name="sessions[${sessionCounter}][video_input]"
                                            class="form-control video-input-field"
                                            id="video_input_${sessionCounter}"
                                            placeholder="https://drive.google.com/file/d/... atau path/to/video.mp4">
                                        <small class="text-muted">
                                            <strong>Opsi 1 (Google Drive):</strong> Paste link Google Drive (contoh: https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j/view)<br>
                                            <strong>Opsi 2 (Local):</strong> Masukkan path video (contoh: storage/training/video.mp4 atau https://example.com/video.mp4)
                                        </small>
                                        <input type="hidden" name="sessions[${sessionCounter}][video_url]" class="video-url-hidden" value="">
                                        <input type="hidden" name="sessions[${sessionCounter}][google_drive_file_id]" class="google-drive-hidden" value="">
                                        <input type="hidden" name="sessions[${sessionCounter}][video_source]" class="video-source-hidden" value="local">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Durasi Video (detik)</label>
                                        <input type="number" name="sessions[${sessionCounter}][video_duration_seconds]"
                                            class="form-control" min="1"
                                            placeholder="3600">
                                        <small class="text-muted">Durasi dalam detik (contoh: 3600 = 1 jam)</small>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="sessions[${sessionCounter}][session_order]" value="${sessionCounter}">
                        </div>
                    </div>
                `;
                $('#sessionsContainer').append(sessionHtml);
                updateSessionNumbers();
            });

            // Remove session
            $(document).on('click', '.remove-session-btn', function() {
                $(this).closest('.session-item').remove();
                updateSessionNumbers();
            });

            // Function to detect if input is Google Drive link
            function isGoogleDriveLink(input) {
                if (!input || input.trim() === '') return false;
                const value = input.trim();
                return value.includes('drive.google.com') ||
                       value.includes('docs.google.com') ||
                       /^[a-zA-Z0-9_-]{20,}$/.test(value);
            }

            // Function to extract Google Drive file ID
            function extractGoogleDriveFileId(input) {
                if (!input) return null;
                const value = input.trim();
                if (/^[a-zA-Z0-9_-]{20,}$/.test(value)) return value;
                const match = value.match(/\/d\/([a-zA-Z0-9_-]+)/);
                if (match) return match[1];
                const match2 = value.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match2) return match2[1];
                return null;
            }

            // Auto-detect video source and update hidden fields
            $(document).on('input blur', '.video-input-field', function() {
                const input = $(this).val();
                const sessionItem = $(this).closest('.session-item, .card-body');
                const videoUrlHidden = sessionItem.find('.video-url-hidden');
                const googleDriveHidden = sessionItem.find('.google-drive-hidden');
                const videoSourceHidden = sessionItem.find('.video-source-hidden');

                if (isGoogleDriveLink(input)) {
                    const fileId = extractGoogleDriveFileId(input);
                    if (fileId) {
                        googleDriveHidden.val(fileId);
                        videoUrlHidden.val('');
                        videoSourceHidden.val('google_drive');
                    }
                } else if (input.trim() !== '') {
                    videoUrlHidden.val(input);
                    googleDriveHidden.val('');
                    videoSourceHidden.val('local');
                } else {
                    videoUrlHidden.val('');
                    googleDriveHidden.val('');
                    videoSourceHidden.val('local');
                }
            });

            // Toggle video fields
            $(document).on('change', '.has-video-checkbox', function() {
                const videoFields = $(this).closest('.card-body').find('.video-fields');
                const videoInput = videoFields.find('.video-input-field');

                if ($(this).is(':checked')) {
                    videoFields.slideDown();
                    videoInput.prop('required', true);
                } else {
                    videoFields.slideUp();
                    videoInput.prop('required', false);
                    videoInput.val('');
                    $(this).closest('.card-body').find('.video-url-hidden').val('');
                    $(this).closest('.card-body').find('.google-drive-hidden').val('');
                    $(this).closest('.card-body').find('.video-source-hidden').val('local');
                }
            });

            // Update session numbers
            function updateSessionNumbers() {
                $('.session-item').each(function(index) {
                    const newNumber = index + 1;
                    $(this).find('.card-header h5').text('Sesi ' + newNumber);
                    $(this).find('input[name*="[session_order]"]').val(newNumber);
                    $(this).attr('data-session-index', newNumber);

                    // Update all input names
                    $(this).find('input, textarea, select').each(function() {
                        const name = $(this).attr('name');
                        if (name && name.includes('sessions[')) {
                            const newName = name.replace(/sessions\[\d+\]/, `sessions[${newNumber}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            // Submit form
            $('#editTrainingForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            window.location.href = "{{ route('hr.portal-training.master.training-masters.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validasi gagal:\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection


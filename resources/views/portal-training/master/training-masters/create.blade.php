@extends('main.layouts.main')
@section('title')
    Tambah Master Data Training
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
    Tambah Master Data Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Tambah Master Data Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.portal-training.master.training-masters.index') }}">Master Data Training</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Form Tambah Training</h4>
                </div>
                <form id="addTrainingForm" action="{{ route('hr.portal-training.master.training-masters.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_name">Nama Training <span class="text-danger">*</span></label>
                                    <input type="text" name="training_name" id="training_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
                                    <select name="training_type" id="training_type" class="form-control" required>
                                        <option value="">Pilih Tipe Training</option>
                                        <option value="mandatory">Mandatory</option>
                                        <option value="optional">Optional</option>
                                        <option value="certification">Certification</option>
                                        <option value="skill_development">Skill Development</option>
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
                                        <option value="classroom">Kelas Tatap Muka</option>
                                        <option value="online">Online</option>
                                        <option value="hybrid">Hybrid</option>
                                        <option value="workshop">Workshop</option>
                                        <option value="seminar">Seminar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="allow_retry" name="allow_retry" value="1">
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
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
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
                                <!-- Sessions will be added here dynamically -->
                            </div>
                            <small class="text-muted">Atur urutan sesi training. Setiap sesi bisa memiliki video atau tidak.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-save mr-1"></i>
                            Simpan Training
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
            let sessionCounter = 0;

            // Add new session
            $('#addSessionBtn').on('click', function() {
                sessionCounter++;
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="sessions[${sessionCounter}][description]"
                                            class="form-control" rows="2"
                                            placeholder="Deskripsi singkat tentang sesi ini"></textarea>
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
            $('#addTrainingForm').on('submit', function(e) {
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

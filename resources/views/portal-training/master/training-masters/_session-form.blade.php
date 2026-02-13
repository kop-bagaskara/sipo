@php
    $sessionIndex = $sessionIndex ?? 0;
    $session = $session ?? null;
@endphp

<div class="card mb-3 session-item" data-session-index="{{ $sessionIndex }}">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sesi {{ $sessionIndex + 1 }}</h5>
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
                    <input type="text" name="sessions[{{ $sessionIndex }}][session_title]"
                        class="form-control" required placeholder="Contoh: Pengenalan K3"
                        value="{{ $session['session_title'] ?? '' }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="sessions[{{ $sessionIndex }}][description]"
                        class="form-control" rows="2"
                        placeholder="Deskripsi singkat tentang sesi ini">{{ $session['description'] ?? '' }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tingkat Kesulitan <span class="text-danger">*</span></label>
                    <select name="sessions[{{ $sessionIndex }}][difficulty_level_id]" class="form-control" required>
                        <option value="">Pilih Tingkat Kesulitan</option>
                        <option value="1" {{ isset($session['difficulty_level_id']) && $session['difficulty_level_id'] == 1 ? 'selected' : '' }}>Mudah</option>
                        <option value="2" {{ isset($session['difficulty_level_id']) && $session['difficulty_level_id'] == 2 ? 'selected' : '' }}>Sedang</option>
                        <option value="3" {{ isset($session['difficulty_level_id']) && $session['difficulty_level_id'] == 3 ? 'selected' : '' }}>Sulit</option>
                        <option value="4" {{ isset($session['difficulty_level_id']) && $session['difficulty_level_id'] == 4 ? 'selected' : '' }}>Sangat Sulit</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tema Pertanyaan</label>
                    <input type="text" name="sessions[{{ $sessionIndex }}][theme]"
                        class="form-control"
                        placeholder="Contoh: K3, ISO, dll (opsional)"
                        value="{{ $session['theme'] ?? '' }}">
                    <small class="text-muted">Untuk filter pertanyaan dari Question Bank</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Jumlah Soal <span class="text-danger">*</span></label>
                    <input type="number" name="sessions[{{ $sessionIndex }}][question_count]"
                        class="form-control" min="1" value="{{ $session['question_count'] ?? 5 }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nilai Min. Lulus <span class="text-danger">*</span></label>
                    <input type="number" name="sessions[{{ $sessionIndex }}][passing_score]"
                        class="form-control" min="0" max="100" step="0.01"
                        value="{{ $session['passing_score'] ?? 70.00 }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-check mb-3">
                    <input type="checkbox" name="sessions[{{ $sessionIndex }}][has_video]"
                        class="form-check-input has-video-checkbox"
                        id="has_video_{{ $sessionIndex }}" value="1"
                        @if(isset($session['has_video']) && $session['has_video']) checked @endif>
                    <label class="form-check-label" for="has_video_{{ $sessionIndex }}">
                        <strong>Sesi ini memiliki video</strong>
                    </label>
                </div>
            </div>
        </div>
        <div class="row video-fields" @if(empty($session['has_video'])) style="display: none;" @endif>
            <div class="col-md-8">
                <div class="form-group">
                    <label>URL Video atau Link Google Drive <span class="text-danger">*</span></label>
                    <input type="text"
                        name="sessions[{{ $sessionIndex }}][video_input]"
                        class="form-control video-input-field"
                        id="video_input_{{ $sessionIndex }}"
                        placeholder="https://drive.google.com/file/d/... atau path/to/video.mp4"
                        value="{{ $session['google_drive_file_id'] ?? $session['video_url'] ?? '' }}"
                        @if(isset($session['has_video']) && $session['has_video']) required @endif>
                    <small class="text-muted">
                        <strong>Opsi 1 (Google Drive):</strong> Paste link Google Drive (contoh: https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j/view)<br>
                        <strong>Opsi 2 (Local):</strong> Masukkan path video (contoh: storage/training/video.mp4 atau https://example.com/video.mp4)
                    </small>
                    {{-- Hidden fields untuk backend --}}
                    <input type="hidden" name="sessions[{{ $sessionIndex }}][video_url]"
                        class="video-url-hidden"
                        value="{{ $session['video_url'] ?? '' }}">
                    <input type="hidden" name="sessions[{{ $sessionIndex }}][google_drive_file_id]"
                        class="google-drive-hidden"
                        value="{{ $session['google_drive_file_id'] ?? '' }}">
                    <input type="hidden" name="sessions[{{ $sessionIndex }}][video_source]"
                        class="video-source-hidden"
                        value="{{ $session['video_source'] ?? 'local' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Durasi Video (detik)</label>
                    <input type="number" name="sessions[{{ $sessionIndex }}][video_duration_seconds]"
                        class="form-control" min="1"
                        placeholder="3600"
                        value="{{ $session['video_duration_seconds'] ?? '' }}">
                    <small class="text-muted">Durasi dalam detik (contoh: 3600 = 1 jam)</small>
                </div>
            </div>
        </div>
        <input type="hidden" name="sessions[{{ $sessionIndex }}][session_order]" value="{{ $sessionIndex + 1 }}">
    </div>
</div>

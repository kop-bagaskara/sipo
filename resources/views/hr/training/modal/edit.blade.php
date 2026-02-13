<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="training_name">Nama Training <span class="text-danger">*</span></label>
            <input type="text" name="training_name" id="training_name"
                class="form-control @error('training_name') is-invalid @enderror"
                value="{{ old('training_name', $training->training_name) }}" required>
            @error('training_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
            <select name="training_type" id="training_type"
                class="form-control @error('training_type') is-invalid @enderror" required>
                <option value="">Pilih Tipe Training</option>
                <option value="mandatory"
                    {{ old('training_type', $training->training_type) == 'mandatory' ? 'selected' : '' }}>Mandatory
                </option>
                <option value="optional"
                    {{ old('training_type', $training->training_type) == 'optional' ? 'selected' : '' }}>Optional</option>
                <option value="certification"
                    {{ old('training_type', $training->training_type) == 'certification' ? 'selected' : '' }}>
                    Certification</option>
                <option value="skill_development"
                    {{ old('training_type', $training->training_type) == 'skill_development' ? 'selected' : '' }}>Skill
                    Development</option>
            </select>
            @error('training_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="training_method">Metode Training <span class="text-danger">*</span></label>
            <select name="training_method" id="training_method"
                class="form-control @error('training_method') is-invalid @enderror" required>
                <option value="">Pilih Metode Training</option>
                <option value="classroom"
                    {{ old('training_method', $training->training_method) == 'classroom' ? 'selected' : '' }}>Kelas Tatap
                    Muka</option>
                <option value="online"
                    {{ old('training_method', $training->training_method) == 'online' ? 'selected' : '' }}>Online</option>
                <option value="hybrid"
                    {{ old('training_method', $training->training_method) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                <option value="workshop"
                    {{ old('training_method', $training->training_method) == 'workshop' ? 'selected' : '' }}>Workshop
                </option>
                <option value="seminar"
                    {{ old('training_method', $training->training_method) == 'seminar' ? 'selected' : '' }}>Seminar
                </option>
            </select>
            @error('training_method')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status"
                class="form-control @error('status') is-invalid @enderror" required>
                <option value="active"
                    {{ old('status', $training->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive"
                    {{ old('status', $training->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Target Departments -->
<div class="form-group">
    <label>Departemen Target</label>
    <div class="row">
        @foreach ($departments as $department)
            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox" name="target_departments[]"
                        value="{{ $department->id }}" class="form-check-input"
                        id="dept_{{ $department->id }}"
                        {{ in_array($department->id, old('target_departments', $training->target_departments ?? [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="dept_{{ $department->id }}">
                        {{ $department->divisi }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label for="notes">Catatan Tambahan</label>
    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $training->notes) }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="sessions[{{ $session->session_order }}][description]" 
                                        class="form-control" rows="2" 
                                        placeholder="Deskripsi singkat tentang sesi ini">{{ $session->description }}</textarea>
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
                                    <label>URL Video <span class="text-danger">*</span></label>
                                    <input type="text" name="sessions[{{ $session->session_order }}][video_url]" 
                                        class="form-control video-url-input" 
                                        value="{{ $session->video_url }}"
                                        placeholder="https://example.com/video.mp4 atau path/to/video.mp4"
                                        {{ $session->has_video ? 'required' : '' }}>
                                    <small class="text-muted">URL lengkap atau path relatif dari storage/public</small>
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

<script>
$(document).ready(function() {
    let sessionCounter = {{ $training->sessions ? $training->sessions->max('session_order') : 0 }};

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
                                <label>URL Video <span class="text-danger">*</span></label>
                                <input type="text" name="sessions[${sessionCounter}][video_url]" 
                                    class="form-control video-url-input" 
                                    placeholder="https://example.com/video.mp4 atau path/to/video.mp4">
                                <small class="text-muted">URL lengkap atau path relatif dari storage/public</small>
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

    // Toggle video fields
    $(document).on('change', '.has-video-checkbox', function() {
        const videoFields = $(this).closest('.card-body').find('.video-fields');
        const videoUrlInput = videoFields.find('.video-url-input');
        
        if ($(this).is(':checked')) {
            videoFields.slideDown();
            videoUrlInput.prop('required', true);
        } else {
            videoFields.slideUp();
            videoUrlInput.prop('required', false);
            videoUrlInput.val('');
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
});
</script>
@extends('main.layouts.app')

@section('title', 'Edit Training')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Training: {{ $training->training_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('hr.training.show', $training->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye mr-1"></i>
                            Lihat Detail
                        </a>
                        <a href="{{ route('hr.training.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('hr.training.update', $training->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_name">Nama Training <span class="text-danger">*</span></label>
                                    <input type="text" name="training_name" id="training_name" class="form-control @error('training_name') is-invalid @enderror" 
                                           value="{{ old('training_name', $training->training_name) }}" required>
                                    @error('training_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
                                    <select name="training_type" id="training_type" class="form-control @error('training_type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe Training</option>
                                        <option value="mandatory" {{ old('training_type', $training->training_type) == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                                        <option value="optional" {{ old('training_type', $training->training_type) == 'optional' ? 'selected' : '' }}>Optional</option>
                                        <option value="certification" {{ old('training_type', $training->training_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                                        <option value="skill_development" {{ old('training_type', $training->training_type) == 'skill_development' ? 'selected' : '' }}>Skill Development</option>
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
                                    <select name="training_method" id="training_method" class="form-control @error('training_method') is-invalid @enderror" required>
                                        <option value="">Pilih Metode Training</option>
                                        <option value="classroom" {{ old('training_method', $training->training_method) == 'classroom' ? 'selected' : '' }}>Kelas Tatap Muka</option>
                                        <option value="online" {{ old('training_method', $training->training_method) == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="hybrid" {{ old('training_method', $training->training_method) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        <option value="workshop" {{ old('training_method', $training->training_method) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                        <option value="seminar" {{ old('training_method', $training->training_method) == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    </select>
                                    @error('training_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration_hours">Durasi (Jam) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_hours" id="duration_hours" class="form-control @error('duration_hours') is-invalid @enderror" 
                                           value="{{ old('duration_hours', $training->duration_hours) }}" min="1" required>
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_participants">Maksimal Peserta</label>
                                    <input type="number" name="max_participants" id="max_participants" class="form-control @error('max_participants') is-invalid @enderror" 
                                           value="{{ old('max_participants', $training->max_participants) }}" min="1">
                                    @error('max_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_participants">Minimal Peserta <span class="text-danger">*</span></label>
                                    <input type="number" name="min_participants" id="min_participants" class="form-control @error('min_participants') is-invalid @enderror" 
                                           value="{{ old('min_participants', $training->min_participants) }}" min="1" required>
                                    @error('min_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cost_per_participant">Biaya per Peserta</label>
                                    <input type="number" name="cost_per_participant" id="cost_per_participant" class="form-control @error('cost_per_participant') is-invalid @enderror" 
                                           value="{{ old('cost_per_participant', $training->cost_per_participant) }}" min="0" step="0.01">
                                    @error('cost_per_participant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor_name">Nama Instruktur</label>
                                    <input type="text" name="instructor_name" id="instructor_name" class="form-control @error('instructor_name') is-invalid @enderror" 
                                           value="{{ old('instructor_name', $training->instructor_name) }}">
                                    @error('instructor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor_contact">Kontak Instruktur</label>
                                    <input type="text" name="instructor_contact" id="instructor_contact" class="form-control @error('instructor_contact') is-invalid @enderror" 
                                           value="{{ old('instructor_contact', $training->instructor_contact) }}">
                                    @error('instructor_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="draft" {{ old('status', $training->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $training->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="ongoing" {{ old('status', $training->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $training->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                               {{ old('is_active', $training->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Training Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $training->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="objectives">Tujuan Training</label>
                            <textarea name="objectives" id="objectives" class="form-control @error('objectives') is-invalid @enderror" rows="3">{{ old('objectives', $training->objectives) }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prerequisites">Prasyarat</label>
                            <textarea name="prerequisites" id="prerequisites" class="form-control @error('prerequisites') is-invalid @enderror" rows="3">{{ old('prerequisites', $training->prerequisites) }}</textarea>
                            @error('prerequisites')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Target Departments -->
                        <div class="form-group">
                            <label>Departemen Target</label>
                            <div class="row">
                                @foreach($departments as $department)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="target_departments[]" value="{{ $department->id }}" 
                                                   class="form-check-input" id="dept_{{ $department->id }}"
                                                   {{ in_array($department->id, old('target_departments', $training->target_departments ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dept_{{ $department->id }}">
                                                {{ $department->divisi }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Training Departments -->
                        <div class="form-group">
                            <label>Konfigurasi Departemen Training</label>
                            <div id="training-departments">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label>Departemen</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Wajib</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Prioritas</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Catatan</label>
                                    </div>
                                </div>
                                @foreach($training->departments as $index => $dept)
                                    <div class="training-department-row">
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <select name="training_departments[{{ $index }}][department_id]" class="form-control">
                                                    <option value="">Pilih Departemen</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}" {{ $dept->department_id == $department->id ? 'selected' : '' }}>
                                                            {{ $department->divisi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="training_departments[{{ $index }}][is_mandatory]" class="form-check-input" 
                                                           {{ $dept->is_mandatory ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="training_departments[{{ $index }}][priority]" class="form-control">
                                                    <option value="1" {{ $dept->priority == 1 ? 'selected' : '' }}>Tinggi</option>
                                                    <option value="2" {{ $dept->priority == 2 ? 'selected' : '' }}>Sedang</option>
                                                    <option value="3" {{ $dept->priority == 3 ? 'selected' : '' }}>Rendah</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="training_departments[{{ $index }}][notes]" class="form-control" 
                                                       value="{{ $dept->notes }}" placeholder="Catatan">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-department" 
                                                        {{ $training->departments->count() <= 1 ? 'style=display:none;' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-department">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Departemen
                            </button>
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan Tambahan</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $training->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Update Training
                        </button>
                        <a href="{{ route('hr.training.show', $training->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let departmentIndex = {{ $training->departments->count() }};

    // Add department row
    $('#add-department').click(function() {
        const newRow = `
            <div class="training-department-row">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <select name="training_departments[${departmentIndex}][department_id]" class="form-control">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="training_departments[${departmentIndex}][is_mandatory]" class="form-check-input">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="training_departments[${departmentIndex}][priority]" class="form-control">
                            <option value="1">Tinggi</option>
                            <option value="2">Sedang</option>
                            <option value="3">Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="training_departments[${departmentIndex}][notes]" class="form-control" placeholder="Catatan">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-department">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#training-departments').append(newRow);
        departmentIndex++;
        updateRemoveButtons();
    });

    // Remove department row
    $(document).on('click', '.remove-department', function() {
        $(this).closest('.training-department-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const rows = $('.training-department-row').length;
        $('.remove-department').toggle(rows > 1);
    }

    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endpush

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

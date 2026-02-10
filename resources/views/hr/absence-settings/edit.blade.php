@extends('layouts.app')

@section('title', 'Edit Setting Absence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Setting Absence</h4>
                    <a href="{{ route('hr.absence-settings.index') }}" class="btn btn-secondary btn-sm float-end">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form id="editForm" action="{{ route('hr.absence-settings.update', $setting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Absence <span class="text-danger">*</span></label>
                                    <input type="text" name="absence_type" class="form-control" value="{{ old('absence_type', $setting->absence_type) }}" required>
                                    @error('absence_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Minimum (Hari)</label>
                                    <input type="number" name="min_deadline_days" class="form-control" value="{{ old('min_deadline_days', $setting->min_deadline_days) }}" placeholder="0">
                                    <small class="text-muted">
                                        0 = Hari ini<br>
                                        7 = H+7 (7 hari ke depan)<br>
                                        -1 = H-1 (1 hari ke belakang)<br>
                                        Kosongkan = Unlimited
                                    </small>
                                    @error('min_deadline_days')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Maksimum (Hari)</label>
                                    <input type="number" name="max_deadline_days" class="form-control" value="{{ old('max_deadline_days', $setting->max_deadline_days) }}" placeholder="Kosongkan untuk unlimited">
                                    <small class="text-muted">
                                        Kosongkan = Unlimited<br>
                                        1 = H+1 (maksimal 1 hari setelah tanggal izin)<br>
                                        -1 = H-1 (maksimal 1 hari sebelum tanggal izin)
                                    </small>
                                    @error('max_deadline_days')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Attachment Required</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="attachment_required" value="1" {{ old('attachment_required', $setting->attachment_required) ? 'checked' : '' }} id="attachmentRequired">
                                        <label class="form-check-label" for="attachmentRequired">
                                            Wajib Lampiran
                                        </label>
                                    </div>
                                    @error('attachment_required')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Info Deadline Text</label>
                                    <input type="text" name="deadline_text" class="form-control" value="{{ old('deadline_text', $setting->deadline_text) }}" placeholder="Contoh: Pengajuan harus H-7 (7 hari sebelum tanggal izin)">
                                    @error('deadline_text')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $setting->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }} id="isActive">
                                        <label class="form-check-label" for="isActive">
                                            Aktif
                                        </label>
                                    </div>
                                    @error('is_active')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('hr.absence-settings.index') }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    window.location.href = '{{ route("hr.absence-settings.index") }}';
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || {};
                let errorMessage = '';

                for (let field in errors) {
                    errorMessage += errors[field].join('<br>') + '<br>';
                }

                if (!errorMessage) {
                    errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                }

                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });
});
</script>
@endsection

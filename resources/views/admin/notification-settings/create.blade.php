@extends('main.layouts.main')

@section('title', 'Tambah Pengaturan Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-plus-circle mr-2"></i>
                        Tambah Pengaturan Notifikasi
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('notification-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left mr-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('notification-settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notification_type">Tipe Notifikasi <span class="text-danger">*</span></label>
                                    <select name="notification_type" id="notification_type" class="form-control @error('notification_type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe Notifikasi</option>
                                        <option value="job_order_prepress" {{ old('notification_type') == 'job_order_prepress' ? 'selected' : '' }}>
                                            Job Order Prepress
                                        </option>
                                        <option value="job_order_production" {{ old('notification_type') == 'job_order_production' ? 'selected' : '' }}>
                                            Job Order Production
                                        </option>
                                        <option value="job_order_finishing" {{ old('notification_type') == 'job_order_finishing' ? 'selected' : '' }}>
                                            Job Order Finishing
                                        </option>
                                        <option value="machine_maintenance" {{ old('notification_type') == 'machine_maintenance' ? 'selected' : '' }}>
                                            Machine Maintenance
                                        </option>
                                        <option value="quality_control" {{ old('quality_control') == 'quality_control' ? 'selected' : '' }}>
                                            Quality Control
                                        </option>
                                    </select>
                                    @error('notification_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih jenis notifikasi yang akan dikirim
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="target_type">Target Type <span class="text-danger">*</span></label>
                                    <select name="target_type" id="target_type" class="form-control @error('target_type') is-invalid @enderror" required>
                                        <option value="">Pilih Target Type</option>
                                        <option value="divisi" {{ old('target_type') == 'divisi' ? 'selected' : '' }}>
                                            Divisi
                                        </option>
                                        <option value="jabatan" {{ old('target_type') == 'jabatan' ? 'selected' : '' }}>
                                            Jabatan
                                        </option>
                                        <option value="specific_user" {{ old('target_type') == 'specific_user' ? 'selected' : '' }}>
                                            User Spesifik
                                        </option>
                                    </select>
                                    @error('target_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih cara targeting notifikasi
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="target_value">Target Value <span class="text-danger">*</span></label>
                                    <select name="target_value" id="target_value" class="form-control @error('target_value') is-invalid @enderror" required>
                                        <option value="">Pilih Target Value</option>
                                    </select>
                                    @error('target_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih target berdasarkan target type yang dipilih
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi pengaturan notifikasi (opsional)">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Metode Pengiriman</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="send_email" name="send_email" {{ old('send_email') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="send_email">
                                            Kirim Email
                                        </label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="send_website" name="send_website" {{ old('send_website') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="send_website">
                                            Notifikasi Website
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Pilih metode pengiriman notifikasi
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save mr-1"></i>
                                Simpan Pengaturan
                            </button>
                            <a href="{{ route('notification-settings.index') }}" class="btn btn-secondary ml-2">
                                <i class="mdi mdi-close mr-1"></i>
                                Batal
                            </a>
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
    // Load target values when target type changes
    $('#target_type').on('change', function() {
        const targetType = $(this).val();
        const targetValueSelect = $('#target_value');
        
        // Clear current options
        targetValueSelect.empty().append('<option value="">Pilih Target Value</option>');
        
        if (!targetType) return;

        console.log(targetType);
        
        // Load target values based on target type
        $.ajax({
            url: '{{ route("notification-settings.get-target-values") }}',
            method: 'GET',
            data: { target_type: targetType },
            success: function(response) {
                if (targetType === 'specific_user') {
                    // For specific users, show name with divisi and jabatan
                    response.forEach(function(user) {
                        targetValueSelect.append(`<option value="${user.id}">${user.text}</option>`);
                    });
                } else {
                    // For divisi and jabatan, show simple values
                    response.forEach(function(value) {
                        targetValueSelect.append(`<option value="${value}">${value}</option>`);
                    });
                }
            },
            error: function() {
                toastr.error('Gagal memuat target values');
            }
        });
    });
    
    // Set default values if form has errors
    @if(old('target_type'))
        $('#target_type').trigger('change');
        setTimeout(function() {
            $('#target_value').val('{{ old("target_value") }}');
        }, 500);
    @endif
});
</script>
@endsection

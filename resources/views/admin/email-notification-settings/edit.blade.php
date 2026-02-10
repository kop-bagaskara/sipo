@extends('main.layouts.main')

@section('title', 'Edit Setting Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-pencil mr-2"></i>
                        Edit Setting Email Notification
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('email-notification-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left mr-1"></i>
                            Kembali
                        </a>
                        <a href="{{ route('email-notification-settings.show', $setting->id) }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-eye mr-1"></i>
                            Detail
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('email-notification-settings.update', $setting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notification_name">Nama Notifikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('notification_name') is-invalid @enderror" 
                                           id="notification_name" name="notification_name" 
                                           value="{{ old('notification_name', $setting->notification_name) }}" 
                                           placeholder="Contoh: Input Job Order" required>
                                    @error('notification_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notification_type">Tipe Notifikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('notification_type') is-invalid @enderror" 
                                           id="notification_type" name="notification_type" 
                                           value="{{ old('notification_type', $setting->notification_type) }}" 
                                           placeholder="Contoh: job_order_prepress" required>
                                    @error('notification_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Gunakan format snake_case (huruf kecil, underscore)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Deskripsi singkat tentang notifikasi ini">{{ old('description', $setting->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Pilih User yang Akan Menerima Email <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                Pilih user yang akan menerima notifikasi email untuk setting ini.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 50px;">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="select-all-users" class="custom-control-input">
                                                    <label class="custom-control-label" for="select-all-users"></label>
                                                </div>
                                            </th>
                                            <th>Nama</th>
                                            <th>Divisi</th>
                                            <th>Jabatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="user_ids[]" 
                                                               value="{{ $user->id }}" 
                                                               class="custom-control-input user-checkbox"
                                                               id="user_{{ $user->id }}"
                                                               {{ in_array($user->id, old('user_ids', $setting->users->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="user_{{ $user->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $user->divisi ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $user->jabatan ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @error('user_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                       {{ $setting->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Setting ini aktif
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save mr-1"></i>
                                Update Setting
                            </button>
                            <a href="{{ route('email-notification-settings.index') }}" class="btn btn-secondary">
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

@section('styles')
<style>
/* Styling untuk custom checkbox */
.custom-control {
    margin: 0;
    padding-left: 0;
}

.custom-control-input {
    cursor: pointer;
    margin: 0 auto;
}

.custom-control-label {
    cursor: pointer;
    margin: 0;
    padding: 0;
}

.custom-control-label::before {
    margin: 0 auto;
}

.custom-control-label::after {
    margin: 0 auto;
}

/* Hover effect */
.custom-control-input:hover {
    transform: scale(1.1);
}

/* Centering checkbox dalam cell */
td .custom-control {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 20px;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select all users functionality
    $('#select-all-users').change(function() {
        const isChecked = $(this).prop('checked');
        $('.user-checkbox').prop('checked', isChecked);
    });

    // Update select all when individual checkboxes change
    $('.user-checkbox').change(function() {
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#select-all-users').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all-users').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all-users').prop('indeterminate', true);
        }
    });

    // Initialize select all state
    const totalCheckboxes = $('.user-checkbox').length;
    const checkedCheckboxes = $('.user-checkbox:checked').length;
    
    if (checkedCheckboxes === 0) {
        $('#select-all-users').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#select-all-users').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#select-all-users').prop('indeterminate', true);
    }

    // Form validation
    $('form').submit(function(e) {
        const checkedUsers = $('.user-checkbox:checked').length;
        
        if (checkedUsers === 0) {
            e.preventDefault();
            toastr.error('Pilih minimal satu user untuk menerima email!');
            return false;
        }
    });
});
</script>
@endsection


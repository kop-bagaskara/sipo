@extends('main.layouts.main')

@section('title', 'Pengaturan Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-bell-settings mr-2"></i>
                        Pengaturan Notifikasi
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('notification-settings.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus mr-1"></i>
                            Tambah Pengaturan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Tipe Notifikasi</th>
                                    <th width="15%">Target Type</th>
                                    <th width="20%">Target Value</th>
                                    <th width="10%">Email</th>
                                    <th width="10%">Website</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $index => $setting)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucwords(str_replace('_', ' ', $setting->notification_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($setting->target_type)
                                                @case('divisi')
                                                    <span class="badge badge-primary">Divisi</span>
                                                    @break
                                                @case('jabatan')
                                                    <span class="badge badge-success">Jabatan</span>
                                                    @break
                                                @case('specific_user')
                                                    <span class="badge badge-warning">User Spesifik</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($setting->target_type === 'specific_user')
                                                @php
                                                    $user = $users->firstWhere('id', $setting->target_value);
                                                @endphp
                                                @if($user)
                                                    <strong>{{ $user->name }}</strong><br>
                                                    <small class="text-muted">{{ $user->divisi }} - {{ $user->jabatan }}</small>
                                                @else
                                                    <span class="text-danger">User tidak ditemukan</span>
                                                @endif
                                            @else
                                                <strong>{{ $setting->target_value }}</strong>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($setting->send_email)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-check"></i>
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="mdi mdi-close"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($setting->send_website)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-check"></i>
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="mdi mdi-close"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input toggle-status" 
                                                       id="status_{{ $setting->id }}" 
                                                       data-id="{{ $setting->id }}"
                                                       {{ $setting->is_active ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="status_{{ $setting->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('notification-settings.show', $setting->id) }}" 
                                                   class="btn btn-info btn-sm" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('notification-settings.edit', $setting->id) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm delete-setting" 
                                                        data-id="{{ $setting->id }}" title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information mr-2"></i>
                                                Belum ada pengaturan notifikasi. 
                                                <a href="{{ route('notification-settings.create') }}" class="alert-link">Buat pengaturan pertama</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengaturan notifikasi ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle status
    $('.toggle-status').on('change', function() {
        const settingId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        
        $.ajax({
            url: `/notification-settings/${settingId}/toggle-status`,
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    // Reset checkbox jika gagal
                    $(this).prop('checked', !isChecked);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat mengubah status');
                // Reset checkbox jika gagal
                $(this).prop('checked', !isChecked);
            }
        });
    });

    // Delete confirmation
    $('.delete-setting').on('click', function() {
        const settingId = $(this).data('id');
        const deleteForm = $('#deleteForm');
        
        deleteForm.attr('action', `/notification-settings/${settingId}`);
        $('#deleteModal').modal('show');
    });
});
</script>
@endsection

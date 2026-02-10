@extends('main.layouts.main')

@section('title', 'Master Setting Email')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-email-outline mr-2"></i>
                            Master Setting Email
                        </h4>
                        <div class="card-tools">
                            <a href="{{ route('email-notification-settings.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus mr-1"></i>
                                Tambah Setting
                            </a>
                            <a href="{{ route('test-notification') }}" class="btn btn-info btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-bell-ring mr-1"></i>
                                Test Notifikasi
                            </a>
                            {{-- <a href="{{ route('check-email-config') }}" class="btn btn-warning btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-cog mr-1"></i>
                                Cek Konfigurasi Email
                            </a>
                            <a href="{{ route('check-notification-config') }}" class="btn btn-info btn-sm ml-2"
                                target="_blank">
                                <i class="mdi mdi-cog-check mr-1"></i>
                                Cek Konfigurasi Notifikasi
                            </a>
                            <a href="{{ route('simple-email-test') }}" class="btn btn-success btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-email-check mr-1"></i>
                                Test Email Sederhana
                            </a>
                            <a href="{{ route('check-users-email') }}" class="btn btn-secondary btn-sm ml-2"
                                target="_blank">
                                <i class="mdi mdi-account-check mr-1"></i>
                                Cek User Email
                            </a>
                            <a href="{{ route('fix-user-email') }}" class="btn btn-dark btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-account-edit mr-1"></i>
                                Perbaiki Email User
                            </a> --}}
                            <a href="{{ route('test-direct-email') }}" class="btn btn-danger btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-email-send mr-1"></i>
                                Test Email ke ..
                            </a>
                            {{-- <a href="{{ route('run-email-seeder') }}" class="btn btn-success btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-database-plus mr-1"></i>
                                Run Email Seeder
                            </a>
                            <a href="{{ route('check-recent-logs') }}" class="btn btn-warning btn-sm ml-2" target="_blank">
                                <i class="mdi mdi-file-document mr-1"></i>
                                Cek Log Terbaru
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Notifikasi</th>
                                        <th>Tipe</th>
                                        <th>Deskripsi</th>
                                        <th>Users</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($settings as $index => $setting)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $setting->notification_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $setting->notification_type }}</span>
                                            </td>
                                            <td>{{ $setting->description ?? '-' }}</td>
                                            <td>
                                                @if ($setting->users->count() > 0)
                                                    <span class="badge badge-success">{{ $setting->users->count() }}
                                                        users</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach ($setting->users->take(3) as $user)
                                                            {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                        @if ($setting->users->count() > 3)
                                                            +{{ $setting->users->count() - 3 }} more
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="badge badge-warning">No users</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input toggle-status"
                                                        id="status_{{ $setting->id }}" data-id="{{ $setting->id }}"
                                                        {{ $setting->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="status_{{ $setting->id }}">
                                                        {{ $setting->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('email-notification-settings.show', $setting->id) }}"
                                                        class="btn btn-info btn-sm" title="Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('email-notification-settings.edit', $setting->id) }}"
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
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="mdi mdi-information mr-2"></i>
                                                Belum ada setting email notification
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
                    <p>Apakah Anda yakin ingin menghapus setting email notification ini?</p>
                    <p class="text-danger"><strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan!</p>
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

@section('styles')
    <style>
        /* Styling untuk custom checkbox dan toggle */
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
            // Toggle status
            $('.toggle-status').change(function() {
                const settingId = $(this).data('id');
                const isChecked = $(this).prop('checked');

                $.ajax({
                    url: `/sipo/email-notification-settings/${settingId}/toggle-status`,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Update label
                            const label = $(`label[for="status_${settingId}"]`);
                            label.text(isChecked ? 'Aktif' : 'Tidak Aktif');
                        } else {
                            toastr.error(response.message);
                            // Revert checkbox
                            $(this).prop('checked', !isChecked);
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan saat mengubah status');
                        // Revert checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Delete confirmation
            $('.delete-setting').click(function() {
                const settingId = $(this).data('id');
                const deleteUrl = `/email-notification-settings/${settingId}`;

                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection

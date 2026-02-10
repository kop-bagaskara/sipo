@extends('main.layouts.main')
@section('title')
    Master Email Development Settings
@endsection
@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-action {
            padding: 4px 8px;
            margin: 2px;
            font-size: 0.8rem;
        }

        .reminder-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            position: relative;
        }

        .reminder-card .remove-card {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            font-size: 12px;
        }

        .reminder-card .remove-card:hover {
            background: #c82333;
        }

        .users-checkbox-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .divisi-group {
            border-left: 3px solid #007bff;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .divisi-group:last-child {
            margin-bottom: 0;
        }

        .divisi-header {
            background-color: #e3f2fd;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 10px;
            border: 1px solid #bbdefb;
        }

        .remove-card {
            z-index: 10;
            user-select: none;
        }

        .remove-card:hover {
            transform: scale(1.1);
            transition: all 0.2s ease;
        }
    </style>
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master Email Development Settings</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                <li class="breadcrumb-item active">Email Development Settings</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-email-settings"></i> Email Notification Settings
                            </h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#emailSettingModal" onclick="openCreateModal()">
                                <i class="mdi mdi-plus"></i> Tambah Setting
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Process Name</th>
                                    <th>Process Code</th>
                                    <th>Recipient Users</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $index => $setting)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $setting->process_name }}</strong>
                                            @if ($setting->description)
                                                <br><small class="text-muted">{{ $setting->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $setting->process_code }}</code>
                                        </td>
                                        <td>
                                            @if ($setting->reminder_schedule)
                                                @foreach ($setting->reminder_schedule as $reminder)
                                                    <div class="mb-2">
                                                        <strong>H-{{ $reminder['days'] ?? 'N/A' }}:</strong>
                                                        @if (isset($reminder['description']) && $reminder['description'])
                                                            <small
                                                                class="text-muted d-block">{{ $reminder['description'] }}</small>
                                                        @endif
                                                        @if (isset($reminder['users']) && is_array($reminder['users']))
                                                            @php
                                                                $users = \App\Models\User::whereIn(
                                                                    'id',
                                                                    $reminder['users'],
                                                                )->get();
                                                            @endphp
                                                            @foreach ($users as $user)
                                                                <span
                                                                    class="badge badge-success mr-1">{{ $user->name }}</span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($setting->is_active)
                                                <span class="badge-status badge-active">Active</span>
                                            @else
                                                <span class="badge-status badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-action"
                                                    title="Edit" onclick="openEditModal({{ $setting->id }})">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>

                                                <form
                                                    action="{{ route('development-email-notification-settings.toggle-active', $setting->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-{{ $setting->is_active ? 'warning' : 'success' }} btn-action"
                                                        title="{{ $setting->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="mdi mdi-{{ $setting->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-outline-danger btn-action"
                                                    title="Delete"
                                                    onclick="openDeleteModal({{ $setting->id }}, '{{ $setting->process_name }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="mdi mdi-email-settings-outline"
                                                    style="font-size: 48px; color: #ccc;"></i>
                                                <p class="text-muted mt-2">Belum ada email notification settings</p>

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

    <!-- Modal Create/Edit Email Setting -->
    <div class="modal fade" id="emailSettingModal" tabindex="-1" role="dialog" aria-labelledby="emailSettingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailSettingModalLabel">Tambah Email Development Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="emailSettingForm" method="POST" action="{{ route('development-email-notification-settings.store') }}">
                    @csrf
                    <div id="formMethod"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="process_name">Process Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('process_name') is-invalid @enderror"
                                        id="process_name" name="process_name" value="{{ old('process_name', '') }}"
                                        placeholder="e.g., Input Awal Development" required>
                                    @error('process_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="process_code">Process Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('process_code') is-invalid @enderror"
                                        id="process_code" name="process_code" value="{{ old('process_code', '') }}"
                                        placeholder="e.g., input_awal" required>
                                    @error('process_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode unik untuk identifikasi proses (huruf kecil,
                                        underscore)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3" placeholder="Deskripsi singkat tentang proses ini">{{ old('description', '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="reminder-schedule">
                            <h5><i class="mdi mdi-clock-outline"></i> Reminder Schedule (Opsional)</h5>
                            <p class="text-muted">Atur reminder berdasarkan hari sebelum deadline. Klik "Tambah Reminder"
                                untuk menambah jadwal reminder baru.</p>

                            <div id="reminder-cards">
                                <!-- Reminder cards will be added here dynamically -->
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-reminder-btn">
                                    <i class="mdi mdi-plus"></i> Tambah Reminder
                                </button>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="send_to_rnd_on_every_change"
                                        name="send_to_rnd_on_every_change" value="1"
                                        {{ old('send_to_rnd_on_every_change') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_to_rnd_on_every_change">
                                        Kirim ke RnD untuk setiap perubahan
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus setting <strong id="deleteSettingName"></strong>?</p>
                    <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-delete"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let reminderCardCount = 0;
            let isEditMode = false;
            let editSettingId = null;

            // Add reminder card
            $('#add-reminder-btn').click(function() {
                addReminderCard();
            });

            // Remove reminder card - using direct event binding
            $(document).on('click', '.remove-card', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Remove card clicked'); // Debug

                if (confirm('Apakah Anda yakin ingin menghapus reminder ini?')) {
                    $(this).closest('.reminder-card').remove();
                }
            });

            // Select all divisi functionality
            $(document).on('change', '.select-all-divisi', function() {
                const divisi = $(this).data('divisi');
                const isChecked = $(this).is(':checked');
                const cardId = $(this).closest('.reminder-card').attr('id');

                // Find all checkboxes in this divisi group within this card
                $(this).closest('.divisi-group').find('input[type="checkbox"]:not(.select-all-divisi)')
                    .prop('checked', isChecked);
            });

            function addReminderCard(reminderData = null) {
                reminderCardCount++;
                const cardId = 'reminder_' + reminderCardCount;

                console.log('Adding reminder card:', cardId);

                const cardHtml = `
                    <div class="reminder-card" id="${cardId}">
                        <div class="remove-card" title="Hapus Reminder" style="position: absolute; top: 10px; right: 10px; width: 25px; height: 25px; border-radius: 50%; background: #dc3545; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold;">×</div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hari Sebelum Deadline</label>
                                    <select class="form-control" name="reminder_schedule[${reminderCardCount}][days]">
                                        <option value="10" ${reminderData && reminderData.days == 10 ? 'selected' : ''}>H-10 (10 hari sebelum)</option>
                                        <option value="5" ${reminderData && reminderData.days == 5 ? 'selected' : ''}>H-5 (5 hari sebelum)</option>
                                        <option value="4" ${reminderData && reminderData.days == 4 ? 'selected' : ''}>H-4 (4 hari sebelum)</option>
                                        <option value="3" ${reminderData && reminderData.days == 3 ? 'selected' : ''}>H-3 (3 hari sebelum)</option>
                                        <option value="2" ${reminderData && reminderData.days == 2 ? 'selected' : ''}>H-2 (2 hari sebelum)</option>
                                        <option value="1" ${reminderData && reminderData.days == 1 ? 'selected' : ''}>H-1 (1 hari sebelum)</option>
                                        <option value="0" ${reminderData && reminderData.days == 0 ? 'selected' : ''}>Hari H (pada hari deadline)</option>
                                        <option value="first" ${reminderData && reminderData.days == 'first' ? 'selected' : ''}>Awal Input (pada inputan awal)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Keterangan (Opsional)</label>
                                    <input type="text" class="form-control" name="reminder_schedule[${reminderCardCount}][description]"
                                        placeholder="e.g., Reminder untuk PIC dan RnD"
                                        value="${reminderData ? reminderData.description || '' : ''}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Users yang Akan Menerima Reminder</label>
                            <div class="users-checkbox-container">
                                @php
                                    // Pastikan data users fresh setiap kali modal dibuka
                                    $freshUsers = \App\Models\User::with('divisiUser')->get();
                                    $usersByDivisi = $freshUsers->groupBy(function ($user) {
                                        return $user->divisiUser->divisi ?? 'Lainnya';
                                    });
                                @endphp
                                @foreach ($usersByDivisi as $divisi => $divisiUsers)
                                    <div class="divisi-group">
                                        <div class="divisi-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="text-primary mb-0">
                                                    <i class="mdi mdi-office-building"></i> {{ $divisi }}
                                                    <small class="text-muted">({{ $divisiUsers->count() }} users)</small>
                                                </h6>
                                                <div class="form-check">
                                                    <input class="form-check-input select-all-divisi" type="checkbox"
                                                        data-divisi="{{ $divisi }}"
                                                        id="select_all_{{ $divisi }}">
                                                    <label class="form-check-label text-muted" for="select_all_{{ $divisi }}">
                                                        <small>Pilih Semua</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach ($divisiUsers as $user)
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="reminder_schedule[${reminderCardCount}][users][]"
                                                            value="{{ $user->id }}"
                                                            id="${cardId}_user_{{ $user->id }}"
                                                            ${reminderData && reminderData.users && reminderData.users.includes({{ $user->id }}) ? 'checked' : ''}>
                                                        <label class="form-check-label" for="${cardId}_user_{{ $user->id }}">
                                                            {{ $user->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                `;

                $('#reminder-cards').append(cardHtml);

                // Add direct event handler to the new card
                $(`#${cardId} .remove-card`).click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Direct remove card clicked for:', cardId);

                    if (confirm('Apakah Anda yakin ingin menghapus reminder ini?')) {
                        $(`#${cardId}`).remove();
                    }
                });

                // Debug: Check if remove button exists
                setTimeout(function() {
                    const removeButtons = $('.remove-card');
                    console.log('Total remove buttons found:', removeButtons.length);
                    console.log('Remove buttons:', removeButtons);
                }, 100);
            }

             // Add one default reminder card
             addReminderCard();

             // Debug form submission
             $('#emailSettingForm').on('submit', function(e) {
                 console.log('Form submitted!');
                 console.log('Form action:', $(this).attr('action'));
                 console.log('Form method:', $(this).attr('method'));
                 console.log('Form data:', $(this).serialize());

                 // Let form submit normally
                 console.log('Form will submit to:', $(this).attr('action'));

             });
         });

         // Global functions for modal handling
         function openCreateModal() {
             $('#emailSettingModalLabel').text('Tambah Email Development Setting');
             $('#emailSettingForm').attr('action', '{{ route('development-email-notification-settings.store') }}');
             $('#emailSettingForm').attr('method', 'POST');
             $('#formMethod').html('');
             $('#emailSettingForm')[0].reset();
             $('#reminder-cards').empty();

             // Reset reminder card count untuk memastikan ID unik
             reminderCardCount = 0;

             // Tambah reminder card baru dengan data users yang fresh
             addReminderCard();
             isEditMode = false;
             editSettingId = null;

             console.log('Create modal opened, form action:', $('#emailSettingForm').attr('action'));
         }

        function openEditModal(settingId) {
            // This would need to be implemented with AJAX to fetch the setting data
            // For now, we'll redirect to the edit page
            window.location.href = '{{ route('development-email-notification-settings.edit', ':id') }}'.replace(':id',
                settingId);
        }

        function openDeleteModal(settingId, settingName) {
            $('#deleteSettingName').text(settingName);
            $('#deleteForm').attr('action', '{{ route('development-email-notification-settings.destroy', ':id') }}'
                .replace(':id', settingId));
            $('#deleteModal').modal('show');
        }
    </script>
@endsection

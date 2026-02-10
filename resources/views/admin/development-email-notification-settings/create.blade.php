@extends('main.layouts.main')
@section('title')
    Tambah Email Development Setting
@endsection
@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        .reminder-schedule {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .role-tag {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            margin: 2px;
            font-size: 0.8rem;
        }
        .role-tag .remove {
            cursor: pointer;
            margin-left: 5px;
            color: #dc3545;
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
        .remove-card {
            z-index: 10;
            user-select: none;
        }
        .remove-card:hover {
            transform: scale(1.1);
            transition: all 0.2s ease;
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
    </style>
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Tambah Email Development Setting</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('development-email-notification-settings.index') }}">Email Development Settings</a></li>
                <li class="breadcrumb-item active">Tambah Setting</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-plus-circle"></i> Form Tambah Email Development Setting
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('development-email-notification-settings.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="process_name">Process Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('process_name') is-invalid @enderror"
                                           id="process_name" name="process_name" value="{{ old('process_name') }}"
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
                                           id="process_code" name="process_code" value="{{ old('process_code') }}"
                                           placeholder="e.g., input_awal" required>
                                    @error('process_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode unik untuk identifikasi proses (huruf kecil, underscore)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Deskripsi singkat tentang proses ini">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="reminder-schedule">
                            <h5><i class="mdi mdi-clock-outline"></i> Reminder Schedule (Opsional)</h5>
                            <p class="text-muted">Atur reminder berdasarkan hari sebelum deadline. Klik "Tambah Reminder" untuk menambah jadwal reminder baru.</p>

                            <div id="reminder-cards">
                                <!-- Reminder cards will be added here dynamically -->
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-reminder-btn">
                                    <i class="mdi mdi-plus"></i> Tambah Reminder
                                </button>
                            </div>
                        </div>

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

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan Setting
                            </button>
                            <a href="{{ route('development-email-notification-settings.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
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

    // Add reminder card
    $('#add-reminder-btn').click(function() {
        console.log('Add reminder button clicked');
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
        $(this).closest('.divisi-group').find('input[type="checkbox"]:not(.select-all-divisi)').prop('checked', isChecked);
    });

    function addReminderCard() {
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
                                <option value="10">H-10 (10 hari sebelum)</option>
                                <option value="5">H-5 (5 hari sebelum)</option>
                                <option value="3">H-3 (3 hari sebelum)</option>
                                <option value="2">H-2 (2 hari sebelum)</option>
                                <option value="1">H-1 (1 hari sebelum)</option>
                                <option value="0">Hari H (pada hari deadline)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Keterangan (Opsional)</label>
                            <input type="text" class="form-control" name="reminder_schedule[${reminderCardCount}][description]"
                                   placeholder="e.g., Reminder untuk PIC dan RnD">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Users yang Akan Menerima Reminder</label>
                    <div class="users-checkbox-container">
                        @php
                            $usersByDivisi = $users->groupBy(function($user) {
                                return $user->divisiUser->divisi ?? 'Lainnya';
                            });
                        @endphp
                        @foreach($usersByDivisi as $divisi => $divisiUsers)
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
                                    @foreach($divisiUsers as $user)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="reminder_schedule[${reminderCardCount}][users][]"
                                                       value="{{ $user->id }}"
                                                       id="${cardId}_user_{{ $user->id }}">
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
});
</script>
@endsection

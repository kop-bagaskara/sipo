@extends('main.layouts.main')
@section('title')
    Dashboard HR - Master Setting Approval
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
        .cust-col {
            white-space: nowrap;
        }

        /* Calendar Styles */
        .calendar-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .fc-event {
            border-radius: 4px !important;
            border: none !important;
            padding: 2px 4px !important;
            font-size: 12px !important;
        }

        .absence-event {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .shift-change-event {
            background-color: #4ecdc4 !important;
            color: white !important;
        }

        .overtime-event {
            background-color: #ffe66d !important;
            color: #333 !important;
        }

        .vehicle-event {
            background-color: #95e1d3 !important;
            color: #333 !important;
        }

        .asset-event {
            background-color: #a8e6cf !important;
            color: #333 !important;
        }
    </style>
@endsection
@section('page-title')
    Master Setting Approval
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Setting Approval</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Master Setting Approval</li>
                </ol>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter by Request Type -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-control" id="filterRequestType">
                                    <option value="">-- Pilih Jenis Pengajuan --</option>
                                    <option value="shift_change">Tukar Shift</option>
                                    <option value="absence">Tidak Masuk Kerja</option>
                                    <option value="overtime">Data Lembur</option>
                                    <option value="vehicle_asset">Permintaan Kendaraan</option>
                                    <option value="asset_request">Permintaan Inventaris</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info" onclick="filterApprovalSettings()">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                            </div>
                            {{-- Button Add Approval Setting --}}
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info d-flex justify-content-end" onclick="openAddModal()">
                                    <i class="mdi mdi-plus"></i> Tambah Setting Approval
                                </button>
                            </div>
                        </div>

                        <!-- Approval Settings Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped w-100" id="approvalSettingsTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Pengajuan</th>
                                        <th>Level Approval</th>
                                        <th>Urutan</th>
                                        <th>Nama Approver</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($approvalSettings as $index => $setting)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst(str_replace('_', ' ', $setting->request_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ ucfirst($setting->approval_level) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $setting->approval_order }}</span>
                                            </td>
                                            <td>
                                                @if($setting->approver_type === 'role')
                                                    @if($setting->role_key === 'hr')
                                                        <span class="badge badge-info">Semua HRD</span>
                                                    @elseif($setting->role_key === 'head_division')
                                                        <span class="badge badge-warning">HEAD per Divisi</span>
                                                    @elseif($setting->role_key === 'spv_division')
                                                        <span class="badge badge-secondary">SPV per Divisi</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($setting->role_key ?? 'Role') }}</span>
                                                    @endif
                                                @else
                                                    {{ $setting->user_name ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($setting->approver_type === 'role')
                                                    @if(!empty($setting->allowed_jabatan) && is_array($setting->allowed_jabatan))
                                                        @foreach($setting->allowed_jabatan as $jabatanId)
                                                            @if($jabatanId == 3)
                                                                <span class="badge badge-primary">Manager</span>
                                                            @elseif($jabatanId == 4)
                                                                <span class="badge badge-info">HEAD</span>
                                                            @elseif($jabatanId == 5)
                                                                <span class="badge badge-warning">SPV</span>
                                                            @elseif($jabatanId == 7)
                                                                <span class="badge badge-secondary">Staff</span>
                                                            @else
                                                                <span class="badge badge-light">Jabatan {{ $jabatanId }}</span>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @if($setting->role_key === 'hr')
                                                            <span class="badge badge-info">HRD</span>
                                                        @elseif($setting->role_key === 'head_division')
                                                            <span class="badge badge-warning">HEAD/MANAGER/SPV</span>
                                                        @elseif($setting->role_key === 'spv_division')
                                                            <span class="badge badge-secondary">SPV</span>
                                                        @else
                                                            {{ ucfirst($setting->role_key ?? '-') }}
                                                        @endif
                                                    @endif
                                                @else
                                                    {{ $setting->user_position ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($setting->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editApprovalSetting({{ $setting->id }})">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deleteApprovalSetting({{ $setting->id }})">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="showApprovers({ approverType: '{{ $setting->approver_type ?? 'user' }}', roleKey: '{{ $setting->role_key ?? '' }}', userId: '{{ $setting->user_id ?? '' }}' })">
                                                    <i class="mdi mdi-account"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Approver Modal -->
        <div class="modal fade" id="approverModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Daftar Approver</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3" id="divisionPicker" style="display:none;">
                            <div class="col-md-6">
                                <label>Pilih Divisi</label>
                                <select id="divisionId" class="form-control">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach(\App\Models\Divisi::orderBy('divisi')->get() as $div)
                                        <option value="{{ $div->id }}">{{ $div->divisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-info" onclick="loadApprovers()"><i class="mdi mdi-refresh"></i> Muat</button>
                            </div>
                        </div>
                        <div id="approverCards" class="row"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Approval Modal -->
        <div class="modal fade" id="addApprovalModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Setting Approval</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="approvalSettingForm">
                        <div class="modal-body">
                            <input type="hidden" id="settingId" name="id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Pengajuan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="requestType" name="request_type" required>
                                            <option value="">-- Pilih Jenis Pengajuan --</option>
                                            <option value="shift_change">Tukar Shift</option>
                                            <option value="absence">Tidak Masuk Kerja</option>
                                            <option value="overtime">Data Lembur</option>
                                            <option value="vehicle_asset">Permintaan Kendaraan</option>
                                            <option value="asset_request">Permintaan Inventaris</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Level Approval <span class="text-danger">*</span></label>
                                        <select class="form-control" id="approvalLevel" name="approval_level" required>
                                            <option value="">-- Pilih Level Approval --</option>
                                            <option value="supervisor">Supervisor / Atasan Langsung (HEAD/SPV/MANAGER)</option>
                                            <option value="hr">HR / HRD</option>
                                            <option value="manager">Manager</option>
                                            <option value="director">Direktur</option>
                                        </select>
                                        <small class="form-text text-muted">Level approval ini menentukan urutan proses approval</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipe Approver <span class="text-danger">*</span></label>
                                        <select class="form-control" id="approverType" name="approver_type" required>
                                            <option value="user" selected>Per User</option>
                                            <option value="role">Per Role</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Urutan Approval <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="approvalOrder"
                                            name="approval_order" min="1" required>
                                        <small class="form-text text-muted">1 = Pertama, 2 = Kedua, dst</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="isActive" name="is_active">
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="userApproverRow">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pilih User <span class="text-danger">*</span></label>
                                        <select class="form-control" id="userId" name="user_id">
                                            <option value="">-- Pilih User --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    data-position="{{ $user->jabatanUser->jabatan ?? '' }}">
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jabatan</label>
                                        <input type="text" class="form-control" id="userPosition"
                                            name="user_position" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row d-none" id="roleApproverRow">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Pilih Role <span class="text-danger">*</span></label>
                                        <select class="form-control" id="roleKey" name="role_key">
                                            <option value="">-- Pilih Role --</option>
                                            <option value="hr">Semua HRD</option>
                                            <option value="head_division">Head per Divisi (otomatis sesuai divisi pemohon)</option>
                                            <option value="spv_division">Supervisor per Divisi (otomatis sesuai divisi pemohon)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row d-none" id="jabatanRow">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Pilih Jabatan yang Bisa Approve <span class="text-danger">*</span></label>
                                        <small class="form-text text-muted">Centang jabatan yang diizinkan untuk approve level ini. Kosongkan jika pakai role di atas.</small>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="allowed_jabatan[]" value="3" id="jabatan_3">
                                                <label class="form-check-label" for="jabatan_3">
                                                    Manager (3)
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="allowed_jabatan[]" value="4" id="jabatan_4">
                                                <label class="form-check-label" for="jabatan_4">
                                                    HEAD (4)
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="allowed_jabatan[]" value="5" id="jabatan_5">
                                                <label class="form-check-label" for="jabatan_5">
                                                    SPV (5)
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="allowed_jabatan[]" value="7" id="jabatan_7">
                                                <label class="form-check-label" for="jabatan_7">
                                                    Staff (7)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Deskripsi approval level..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Toggle approver input by type - Global function
            function toggleApproverRows() {
                var type = $('#approverType').val();
                if (type === 'role') {
                    $('#roleApproverRow').removeClass('d-none');
                    $('#jabatanRow').removeClass('d-none');
                    $('#userApproverRow').addClass('d-none');
                    $('#roleKey').attr('required', false);
                    $('#userId').removeAttr('required');
                } else {
                    $('#roleApproverRow').addClass('d-none');
                    $('#jabatanRow').addClass('d-none');
                    $('#userApproverRow').removeClass('d-none');
                    $('#roleKey').removeAttr('required');
                    $('#userId').attr('required', true);
                    // Uncheck all jabatan checkboxes
                    $('input[name="allowed_jabatan[]"]').prop('checked', false);
                }
            }

            $(document).ready(function() {
                // Auto-fill position when user is selected
                $('#userId').on('change', function() {
                    var selectedOption = $(this).find('option:selected');
                    var userPosition = selectedOption.data('position');

                    $('#userPosition').val(userPosition);
                });

                // Bind toggle function to approver type change
                $('#approverType').on('change', toggleApproverRows);
                toggleApproverRows();

                // Initialize DataTable
                $('#approvalSettingsTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    scrollX: true,
                    scrollY: 300,
                    scrollCollapse: true,
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    order: [[1, 'asc'], [2, 'asc']]
                });

                // Handle form submission
                $('#approvalSettingForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    var settingId = $('#settingId').val();
                    var baseUrl = "{{ route('hr.approval-settings.index') }}";
                    var url = settingId ? baseUrl + '/' + settingId : baseUrl;

                    // Add _method for Laravel PUT/PATCH support
                    if (settingId) {
                        formData.append('_method', 'PUT');
                    }

                    // Debug: Log formData to console
                    console.log('Submitting to:', url);
                    console.log('FormData contents:');
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    $.ajax({
                        url: url,
                        type: 'POST', // Always use POST, _method will override for Laravel
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reset form and close modal
                                $('#approvalSettingForm')[0].reset();
                                $('#settingId').val('');
                                $('#addApprovalModal').modal('hide');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errorMessages = [];
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    errorMessages.push(messages.join(', '));
                                });

                                Swal.fire({
                                    title: 'Validasi Error',
                                    html: errorMessages.join('<br>'),
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat menyimpan data',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    });
                });
            });

            function openAddModal() {
                // Reset form
                $('#approvalSettingForm')[0].reset();
                $('#settingId').val('');
                $('#modalTitle').text('Tambah Setting Approval');
                // Uncheck all allowed_jabatan checkboxes
                $('input[name="allowed_jabatan[]"]').prop('checked', false);
                $('#addApprovalModal').modal('show');
                // Ensure approver type is set to user by default
                $('#approverType').val('user');
                toggleApproverRows();
            }

            function editApprovalSetting(id) {
                // Load data from API
                $.ajax({
                    url: "{{ route('hr.approval-settings.index') }}/" + id,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            var setting = response.data;

                            // Set form values
                            $('#settingId').val(setting.id);
                            $('#requestType').val(setting.request_type);
                            $('#approvalLevel').val(setting.approval_level);
                            $('#approvalOrder').val(setting.approval_order);
                            $('#approverType').val(setting.approver_type || 'user');
                            $('#roleKey').val(setting.role_key || '');
                            $('#userId').val(setting.user_id || '');
                            $('#userPosition').val(setting.user_position || '');
                            $('#isActive').val(setting.is_active ? '1' : '0');
                            $('#description').val(setting.description || '');

                            // Toggle approver rows based on type
                            toggleApproverRows();

                            // Check allowed_jabatan checkboxes
                            if (setting.allowed_jabatan && Array.isArray(setting.allowed_jabatan)) {
                                setting.allowed_jabatan.forEach(function(jabatanId) {
                                    $('#jabatan_' + jabatanId).prop('checked', true);
                                });
                            }

                            // Update modal title
                            $('#modalTitle').text('Edit Setting Approval');

                            // Show modal
                            $('#addApprovalModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal memuat data setting approval',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memuat data',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            function deleteApprovalSetting(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus setting approval ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Delete request
                        $.ajax({
                            url: "{{ route('hr.approval-settings.index') }}/" + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message || 'Setting approval berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                var message = 'Terjadi kesalahan saat menghapus data';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }

            function filterApprovalSettings() {
                var requestType = $('#filterRequestType').val();
                var baseUrl = "{{ route('hr.approval-settings.index') }}";
                var url = requestType ? (baseUrl + '?request_type=' + encodeURIComponent(requestType)) : baseUrl;
                window.location.href = url;
            }

            // Approver modal helpers
            window.showApprovers = function(params) {
                window.__approverCtx = params || {};
                if (params.roleKey === 'head_division' || params.roleKey === 'spv_division') {
                    $('#divisionPicker').show();
                } else {
                    $('#divisionPicker').hide();
                }
                $('#approverCards').html('<div class="col-12 text-center py-4 text-muted">Memuat data approver...</div>');
                $('#approverModal').modal('show');
                if (!$('#divisionPicker').is(':visible')) {
                    loadApprovers();
                }
            }

            window.loadApprovers = function() {
                var ctx = window.__approverCtx || {};
                var divId = $('#divisionId').val();
                $.get("{{ route('hr.approval-settings.approvers') }}", {
                    approver_type: ctx.approverType,
                    role_key: ctx.roleKey,
                    user_id: ctx.userId,
                    division_id: divId
                }).done(function(res){
                    var html = '';
                    if (res.data && res.data.length) {
                        res.data.forEach(function(u){
                            html += `
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="mb-1">${u.name}</h6>
                                        <small class="text-muted">${u.email || '-'}</small>
                                        <div class="mt-2">
                                            <span class="badge badge-info mr-1">${u.jabatan || '-'}</span>
                                            <span class="badge badge-secondary">${u.divisi || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                    } else {
                        html = '<div class="col-12 text-center py-4 text-muted">Tidak ada approver yang cocok.</div>';
                    }
                    $('#approverCards').html(html);
                }).fail(function(){
                    $('#approverCards').html('<div class="col-12 text-center py-4 text-danger">Gagal memuat data approver.</div>');
                });
            }
        </script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
    @endsection

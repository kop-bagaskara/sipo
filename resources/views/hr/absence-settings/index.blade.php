@extends('main.layouts.main')

@section('title', 'Master Setting Absence')

@section('content')
    {{-- <div class="container-fluid"> --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Master Setting Absence</h4>
                    <button class="btn btn-success btn-sm float-end" id="createBtn">
                        <i class="mdi mdi-plus"></i> Tambah Setting
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="absenceSettingsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Absence</th>
                                    <th>Batas Minimum</th>
                                    <th>Batas Maksimum</th>
                                    <th>Attachment</th>
                                    <th>Info Deadline</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Setting Absence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Absence <span class="text-danger">*</span></label>
                                    <input type="text" name="absence_type" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Minimum (Hari)</label>
                                    <input type="number" name="min_deadline_days" class="form-control" placeholder="0">
                                    <small class="text-muted">0 = Hari ini, 7 = H+7, -1 = H-1, null = Unlimited</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Maksimum (Hari)</label>
                                    <input type="number" name="max_deadline_days" class="form-control"
                                        placeholder="Kosongkan untuk unlimited">
                                    <small class="text-muted">Kosongkan = Unlimited, 1 = H+1, -1 = H-1</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Attachment Required</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="attachment_required"
                                            value="1" id="attachmentRequired">
                                        <label class="form-check-label" for="attachmentRequired">
                                            Wajib Lampiran
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Info Deadline Text</label>
                                    <input type="text" name="deadline_text" class="form-control"
                                        placeholder="Contoh: Pengajuan harus H-7 (7 hari sebelum tanggal izin)">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Sub Absence Categories</label>
                                    <button type="button" class="btn btn-sm btn-primary mb-2" id="addSubBtn">
                                        <i class="mdi mdi-plus"></i> Tambah Sub Absence
                                    </button>
                                    <div id="subAbsenceContainer">
                                        <!-- Sub absence items will be added here dynamically -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="isActive" checked>
                                        <label class="form-check-label" for="isActive">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Setting Absence</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"><i class="mdi mdi-close-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="editId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Absence <span class="text-danger">*</span></label>
                                    <input type="text" name="absence_type" id="editAbsenceType" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Minimum (Hari)</label>
                                    <input type="number" name="min_deadline_days" id="editMinDeadline"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Batas Maksimum (Hari)</label>
                                    <input type="number" name="max_deadline_days" id="editMaxDeadline"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Attachment Required</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="attachment_required"
                                            value="1" id="editAttachmentRequired">
                                        <label class="form-check-label" for="editAttachmentRequired">
                                            Wajib Lampiran
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Info Deadline Text</label>
                                    <input type="text" name="deadline_text" id="editDeadlineText"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Sub Absence Categories</label>
                                    <button type="button" class="btn btn-sm btn-primary mb-2" id="addSubEditBtn">
                                        <i class="mdi mdi-plus"></i> Tambah Sub Absence
                                    </button>
                                    <div id="subAbsenceEditContainer">
                                        <!-- Sub absence items will be added here dynamically -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="editIsActive">
                                        <label class="form-check-label" for="editIsActive">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="updateBtn">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#absenceSettingsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('hr.absence-settings.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'absence_type',
                        name: 'absence_type'
                    },
                    {
                        data: 'min_deadline_days',
                        name: 'min_deadline_days'
                    },
                    {
                        data: 'max_deadline_days',
                        name: 'max_deadline_days'
                    },
                    {
                        data: 'attachment_required',
                        name: 'attachment_required'
                    },
                    {
                        data: 'deadline_text',
                        name: 'deadline_text'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // Create
            $('#createBtn').click(function() {
                $('#createModal').modal('show');
            });

            // Add Sub Absence
            let subCount = 0;
            $('#addSubBtn').click(function() {
                subCount++;
                const subHtml = `
                    <div class="row sub-absence-item align-items-center mb-2" data-index="${subCount}">
                        <div class="col-md-5">
                            <input type="text" name="master_sub_absence[${subCount}][name]" class="form-control"
                                placeholder="Nama Sub Absence (contoh: Pernikahan Karyawan)">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="master_sub_absence[${subCount}][duration_days]" class="form-control"
                                placeholder="Hari" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-sm btn-danger remove-sub-btn">
                                <i class="mdi mdi-delete"></i> Hapus
                            </button>
                        </div>
                    </div>
                `;
                $('#subAbsenceContainer').append(subHtml);
            });

            // Remove Sub Absence
            $(document).on('click', '.remove-sub-btn', function() {
                $(this).closest('.sub-absence-item').remove();
            });

            // Edit - Load data dari server dan load sub absence
            $(document).on('click', 'a[href*="/edit"]', function(e) {
                e.preventDefault();
                const editUrl = $(this).attr('href');

                $.ajax({
                    url: editUrl,
                    type: 'GET',
                    success: function(response) {
                        console.log('Response:', response);

                        // Fill edit form fields
                        $('#editId').val(response.id);
                        $('#editAbsenceType').val(response.absence_type);
                        $('#editMinDeadline').val(response.min_deadline_days);
                        $('#editMaxDeadline').val(response.max_deadline_days);
                        $('#editAttachmentRequired').prop('checked', response.attachment_required);
                        $('#editDeadlineText').val(response.deadline_text);
                        $('#editDescription').val(response.description);
                        $('#editIsActive').prop('checked', response.is_active);

                        // Load sub absence data
                        $('#subAbsenceEditContainer').empty();
                        console.log('master_sub_absence:', response.master_sub_absence);

                        if (response.master_sub_absence) {
                            $.each(response.master_sub_absence, function(index, sub) {
                                console.log('Loading sub item:', index, sub);

                                // Create elements directly instead of template literal to avoid escaping issues
                                const row = $('<div class="row sub-absence-item align-items-center mb-2"></div>').attr('data-index', index);

                                const colName = $('<div class="col-md-5"></div>');
                                const inputName = $('<input type="text" class="form-control">')
                                    .attr('name', 'master_sub_absence[' + index + '][name]')
                                    .attr('placeholder', 'Nama Sub Absence')
                                    .val(sub.name);
                                colName.append(inputName);

                                const colDuration = $('<div class="col-md-3"></div>');
                                const inputDuration = $('<input type="number" class="form-control">')
                                    .attr('name', 'master_sub_absence[' + index + '][duration_days]')
                                    .attr('placeholder', 'Hari')
                                    .attr('min', '1')
                                    .val(sub.duration_days);
                                colDuration.append(inputDuration);

                                const colBtn = $('<div class="col-md-3"></div>');
                                const deleteBtn = $('<button type="button" class="btn btn-sm btn-danger remove-sub-btn"></button>');
                                deleteBtn.html('<i class="mdi mdi-delete"></i> Hapus');
                                colBtn.append(deleteBtn);

                                row.append(colName);
                                row.append(colDuration);
                                row.append(colBtn);

                                $('#subAbsenceEditContainer').append(row);
                                console.log('Appended row for index:', index);
                            });
                            console.log('Total rows in container:', $('#subAbsenceEditContainer .sub-absence-item').length);
                        }

                        $('#editModal').modal('show');
                    },
                    error: function() {
                        window.location.href = editUrl;
                    }
                });
            });

            // Add Sub Absence for Edit
            let editSubCount = 0;
            $('#addSubEditBtn').click(function() {
                // Get current highest index from existing items
                const existingItems = $('#subAbsenceEditContainer .sub-absence-item');
                let maxIndex = 0;
                existingItems.each(function() {
                    const currentIndex = parseInt($(this).attr('data-index')) || 0;
                    if (currentIndex > maxIndex) {
                        maxIndex = currentIndex;
                    }
                });

                // Use the next available index
                const newIndex = maxIndex + 1;
                console.log('Adding new sub item with index:', newIndex, 'maxIndex was:', maxIndex);

                // Use jQuery creation to avoid template literal issues
                const row = $('<div class="row sub-absence-item align-items-center mb-2"></div>').attr('data-index', newIndex);

                const colName = $('<div class="col-md-5"></div>');
                const inputName = $('<input type="text" class="form-control">')
                    .attr('name', 'master_sub_absence[' + newIndex + '][name]')
                    .attr('placeholder', 'Nama Sub Absence (contoh: Pernikahan Karyawan)');
                colName.append(inputName);

                const colDuration = $('<div class="col-md-3"></div>');
                const inputDuration = $('<input type="number" class="form-control">')
                    .attr('name', 'master_sub_absence[' + newIndex + '][duration_days]')
                    .attr('placeholder', 'Hari')
                    .attr('min', '1');
                colDuration.append(inputDuration);

                const colBtn = $('<div class="col-md-3"></div>');
                const deleteBtn = $('<button type="button" class="btn btn-sm btn-danger remove-sub-btn"></button>');
                deleteBtn.html('<i class="mdi mdi-delete"></i> Hapus');
                colBtn.append(deleteBtn);

                row.append(colName);
                row.append(colDuration);
                row.append(colBtn);

                $('#subAbsenceEditContainer').append(row);
                console.log('Total sub items after add:', $('#subAbsenceEditContainer .sub-absence-item').length);
            });

            $('#saveBtn').click(function() {
                // Log all form data before sending
                const formData = new FormData($('#createForm')[0]);
                console.log('=== Form Data Being Sent ===');
                for (let pair of formData.entries()) {
                    console.log(pair[0], '=', pair[1]);
                }

                $.ajax({
                    url: '{{ route('hr.absence-settings.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#createModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Sukses', response.message, 'success');
                        $('#createForm')[0].reset();
                        $('#subAbsenceContainer').empty();
                        subCount = 0;
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

            // Update
            $('#updateBtn').click(function() {
                const id = $('#editId').val();
                const formData = new FormData($('#editForm')[0]);

                // Log all form data before sending
                console.log('=== Update Form Data Being Sent ===');
                for (let pair of formData.entries()) {
                    console.log(pair[0], '=', pair[1]);
                }

                $.ajax({
                    url: '{{ route('hr.absence-settings.update', '') }}/' + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Sukses', response.message, 'success');
                        $('#subAbsenceEditContainer').empty();
                        editSubCount = 0;
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

            // Delete
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus Setting?',
                    text: 'Data akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('hr.absence-settings.destroy', '') }}/' + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

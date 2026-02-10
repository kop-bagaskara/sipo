@extends('main.layouts.main')
@section('title')
    Master Training Assignments
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .action-buttons .btn {
            margin: 0 2px;
            min-width: 32px;
            padding: 6px 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }

        .action-buttons .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .action-buttons .btn-primary:hover {
            background-color: #0069d9;
            transform: scale(1.1);
        }

        .action-buttons .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .action-buttons .btn-info:hover {
            background-color: #138496;
            transform: scale(1.1);
        }

        .action-buttons .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .action-buttons .btn-danger:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        #datatable-assignments_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        #datatable-assignments_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        #datatable-assignments th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }

        .page-link {
            color: #667eea;
        }

        .page-link:hover {
            color: #764ba2;
        }

        #datatable-assignments_wrapper .dataTables_info {
            padding-top: 15px;
            color: #666;
        }

        #datatable-assignments_wrapper .dataTables_paginate {
            padding-top: 15px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #667eea;
            padding: 20px 25px;
            border-radius: 10px 10px 0 0 !important;
        }

        .modal-header {
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-header h5 {
            color: white;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-body .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .modal-body .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .modal-body label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .font-weight-600 {
            font-weight: 600;
        }

        .progress {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }
    </style>
@endsection

@section('page-title')
    Master Training Assignments
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <!-- Page Header -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="mt-2"><i class="mdi mdi-account-multiple"></i> Master Training Assignments</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                <li class="breadcrumb-item active">Master Assignments</li>
            </ol>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0" style="font-weight: 600;">
                            <i class="mdi mdi-format-list-bulleted"></i> Daftar Training Assignments
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" onclick="bulkAssign()" style="margin-right: 10px;">
                            <i class="mdi mdi-account-multiple-plus"></i> Bulk Assign
                        </button>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">
                            <i class="mdi mdi-plus-circle"></i> Assignment Baru
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-assignments" class="table table-hover" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="200">Training</th>
                                    <th width="200">Karyawan</th>
                                    <th width="100">Jml Materi</th>
                                    <th width="200">Tanggal</th>
                                    <th width="150">Progress</th>
                                    <th width="120">Status</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-plus-circle"></i> Assignment Training Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trainingSelect" class="font-weight-600">Training <span class="text-danger">*</span></label>
                                    <select name="training_id" id="trainingSelect" class="form-control" required>
                                        <option value="">Pilih Training</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}">{{ $training->training_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employeeSelect" class="font-weight-600">Karyawan <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employeeSelect" class="form-control" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="materialSelect" class="font-weight-600">Materi <span class="text-danger">*</span></label>
                            <select name="material_ids[]" id="materialSelect" class="form-control" multiple required>
                                <option value="">Pilih Materi (bisa lebih dari satu)</option>
                                @foreach($allMaterials as $mat)
                                    <option value="{{ $mat->id }}">{{ $mat->material_title }} ({{ $mat->category ? $mat->category->category_name : '-' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih lebih dari satu materi</small>
                        </div>
                        <div class="form-group">
                            <label for="deadlineDate" class="font-weight-600">Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="deadline_date" id="deadlineDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="notes" class="font-weight-600">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-content-save"></i> Buat Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Modal -->
    <div class="modal fade" id="bulkModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="mdi mdi-account-multiple-plus"></i> Bulk Assignment Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="bulkForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulkTrainingSelect" class="font-weight-600">Training <span class="text-danger">*</span></label>
                                    <select name="training_id" id="bulkTrainingSelect" class="form-control" required>
                                        <option value="">Pilih Training</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}">{{ $training->training_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulkEmployeeSelect" class="font-weight-600">Karyawan <span class="text-danger">*</span></label>
                                    <select name="employee_ids[]" id="bulkEmployeeSelect" class="form-control" multiple required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Tahan Ctrl/Cmd untuk memilih lebih dari satu karyawan</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bulkMaterialSelect" class="font-weight-600">Materi <span class="text-danger">*</span></label>
                            <select name="material_ids[]" id="bulkMaterialSelect" class="form-control" multiple required>
                                <option value="">Pilih Materi</option>
                                @foreach($allMaterials as $mat)
                                    <option value="{{ $mat->id }}">{{ $mat->material_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bulkDeadline" class="font-weight-600">Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="deadline_date" id="bulkDeadline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bulkNotes" class="font-weight-600">Catatan (Opsional)</label>
                            <textarea name="notes" id="bulkNotes" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-account-multiple-plus"></i> Buat Bulk Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-eye"></i> Detail Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Training:</label>
                                <p id="viewTraining" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Karyawan:</label>
                                <p id="viewEmployee" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Materi:</label>
                        <p id="viewMaterials" class="form-control-plaintext">-</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Tanggal Assign:</label>
                                <p id="viewAssignDate" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Deadline:</label>
                                <p id="viewDeadline" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Progress:</label>
                        <p id="viewProgress" class="form-control-plaintext">-</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Status:</label>
                                <p id="viewStatus" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Catatan:</label>
                        <p id="viewNotes" class="form-control-plaintext">-</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-pencil"></i> Edit Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editTrainingId" class="font-weight-600">Training <span class="text-danger">*</span></label>
                                    <select name="training_id" id="editTrainingId" class="form-control" required>
                                        <option value="">Pilih Training</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}">{{ $training->training_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editEmployeeId" class="font-weight-600">Karyawan <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="editEmployeeId" class="form-control" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editMaterialIds" class="font-weight-600">Materi <span class="text-danger">*</span></label>
                            <select name="material_ids[]" id="editMaterialIds" class="form-control" multiple required>
                                <option value="">Pilih Materi</option>
                                @foreach($allMaterials as $mat)
                                    <option value="{{ $mat->id }}">{{ $mat->material_title }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih lebih dari satu materi</small>
                        </div>
                        <div class="form-group">
                            <label for="editDeadline" class="font-weight-600">Deadline <span class="text-danger">*</span></label>
                            <input type="date" name="deadline_date" id="editDeadline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editNotes" class="font-weight-600">Catatan</label>
                            <textarea name="notes" id="editNotes" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-content-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var table = $('#datatable-assignments').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("hr.portal-training.master.assignments.getData") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'training_name', name: 'training_name' },
                    { data: 'employee_name', name: 'employee_name' },
                    { data: 'materials_count', name: 'materials_count' },
                    { data: 'dates', name: 'dates' },
                    { data: 'progress_bar', name: 'progress_bar' },
                    { data: 'status_badge', name: 'status_badge' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            // Add Form
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("hr.portal-training.master.assignments.store") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#addModal').modal('hide');
                        table.ajax.reload();
                        $('#addForm')[0].reset();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // Bulk Assign button
            window.bulkAssign = function() {
                $('#bulkModal').modal('show');
            }

            // Bulk Form
            $('#bulkForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("hr.portal-training.master.assignments.bulkAssign") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#bulkModal').modal('hide');
                        table.ajax.reload();
                        $('#bulkForm')[0].reset();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // View button
            $(document).on('click', '.btn-view', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.assignments.show', ':id') }}`.replace(':id', id), function(data) {
                    $('#viewTraining').text(data.training_name || '-');
                    $('#viewEmployee').text(data.employee_name || '-');
                    $('#viewMaterials').html(data.materials_html || '-');
                    $('#viewAssignDate').text(data.assigned_date || '-');
                    $('#viewDeadline').text(data.deadline_date || '-');
                    $('#viewProgress').html(data.progress_html || '-');
                    $('#viewStatus').html(data.status_badge || '-');
                    $('#viewNotes').text(data.notes || '-');
                    $('#viewModal').modal('show');
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.assignments.edit', ':id') }}`.replace(':id', id), function(data) {
                    $('#editId').val(data.id);
                    $('#editTrainingId').val(data.training_id);
                    $('#editEmployeeId').val(data.employee_id);
                    $('#editMaterialIds').val(data.material_ids);
                    $('#editDeadline').val(data.deadline_date);
                    $('#editNotes').val(data.notes || '');
                    $('#editModal').modal('show');
                });
            });

            // Edit Form
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                $.ajax({
                    url: `{{ route('hr.portal-training.master.assignments.update', ':id') }}`.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // Delete button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Assignment?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('hr.portal-training.master.assignments.destroy', ':id') }}`.replace(':id', id),
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

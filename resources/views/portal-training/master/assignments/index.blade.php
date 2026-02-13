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

        /* Style untuk checkbox employee dan material */
        .employee-checkbox-container,
        .material-checkbox-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .divisi-group,
        .category-group {
            margin-bottom: 20px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .divisi-group-header,
        .category-group-header {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .divisi-group-header .select-all-divisi,
        .category-group-header .select-all-category {
            font-size: 12px;
            cursor: pointer;
            color: #667eea;
            text-decoration: underline;
        }

        .divisi-group-header .select-all-divisi:hover,
        .category-group-header .select-all-category:hover {
            color: #764ba2;
        }

        .employee-checkbox-item,
        .material-checkbox-item {
            padding: 8px 10px;
            margin: 5px 0;
            border-radius: 3px;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
        }

        .employee-checkbox-item:hover,
        .material-checkbox-item:hover {
            background-color: #f0f0f0;
        }

        .employee-checkbox-item input[type="checkbox"],
        .employee-checkbox-item input[type="radio"],
        .material-checkbox-item input[type="checkbox"] {
            margin-right: 10px;
            cursor: pointer;
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            pointer-events: auto;
            position: relative;
            z-index: 1;
        }

        .employee-checkbox-item input[type="checkbox"],
        .material-checkbox-item input[type="checkbox"] {
            /* Pastikan checkbox bisa dicentang lebih dari satu */
            appearance: checkbox;
            -webkit-appearance: checkbox;
            -moz-appearance: checkbox;
        }

        .employee-checkbox-item input[type="radio"] {
            /* Pastikan radio button hanya bisa pilih satu */
            appearance: radio;
            -webkit-appearance: radio;
            -moz-appearance: radio;
        }

        .employee-checkbox-item label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: normal;
            flex: 1;
            user-select: none;
            pointer-events: auto;
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
                       
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-assignments" class="table table-hover" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="200">Training</th>
                                    <th width="200">Karyawan / Jumlah</th>
                                    <th width="100">Jml Materi</th>
                                    <th width="150">Kode Sesi</th>
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


    <!-- Bulk Assign Modal -->
    <div class="modal fade" id="bulkModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-account-multiple-plus"></i> Bulk Assignment Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="bulkForm" novalidate>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-600">Karyawan <span class="text-danger">*</span></label>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Pilih satu atau lebih karyawan untuk bulk assignment</small>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllEmployees">
                                            <i class="mdi mdi-checkbox-multiple-marked"></i> Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllEmployees">
                                            <i class="mdi mdi-checkbox-multiple-blank"></i> Batal Pilih Semua
                                        </button>
                                    </div>
                                    <div class="employee-checkbox-container">
                                        @foreach($divisis as $divisi)
                                            @if(isset($employeesByDivisi[$divisi->id]) && $employeesByDivisi[$divisi->id]->count() > 0)
                                                <div class="divisi-group">
                                                    <div class="divisi-group-header">
                                                        <span><i class="mdi mdi-office-building"></i> {{ $divisi->divisi }}</span>
                                                        <span class="select-all-divisi" data-divisi="{{ $divisi->id }}">Pilih Semua</span>
                                                    </div>
                                                    @foreach($employeesByDivisi[$divisi->id] as $emp)
                                                        <div class="employee-checkbox-item">
                                                            <input type="checkbox" 
                                                                   name="employee_ids[]" 
                                                                   id="bulk_employee_{{ $emp->id }}" 
                                                                   value="{{ $emp->id }}" 
                                                                   class="employee-checkbox bulk-employee-checkbox"
                                                                   data-employee-id="{{ $emp->id }}"
                                                                   data-no-limit="true">
                                                            <label for="bulk_employee_{{ $emp->id }}">{{ $emp->name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-600">Materi <span class="text-danger">*</span></label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Pilih satu atau lebih materi training</small>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mr-2" id="selectAllBulkMaterials">
                                        <i class="mdi mdi-checkbox-multiple-marked"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBulkMaterials">
                                        <i class="mdi mdi-checkbox-multiple-blank"></i> Batal Pilih Semua
                                    </button>
                                </div>
                            </div>
                            <div class="material-checkbox-container">
                                @foreach($categories as $category)
                                    @if(isset($materialsByCategory[$category->id]) && count($materialsByCategory[$category->id]['materials']) > 0)
                                        <div class="category-group">
                                            <div class="category-group-header">
                                                <span><i class="mdi mdi-folder"></i> {{ $category->category_name }}</span>
                                                <span class="select-all-category" data-category="{{ $category->id }}">Pilih Semua</span>
                                            </div>
                                            @foreach($materialsByCategory[$category->id]['materials'] as $mat)
                                                <div class="material-checkbox-item">
                                                    <input type="checkbox" name="material_ids[]" id="bulk_material_{{ $mat->id }}" value="{{ $mat->id }}" class="material-checkbox bulk-material-checkbox">
                                                    <label for="bulk_material_{{ $mat->id }}">{{ $mat->material_title }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                                @if(count($materialsWithoutCategory) > 0)
                                    <div class="category-group">
                                        <div class="category-group-header">
                                            <span><i class="mdi mdi-folder-outline"></i> Tanpa Kategori</span>
                                            <span class="select-all-category" data-category="no-category">Pilih Semua</span>
                                        </div>
                                        @foreach($materialsWithoutCategory as $mat)
                                            <div class="material-checkbox-item">
                                                <input type="checkbox" name="material_ids[]" id="bulk_material_{{ $mat->id }}" value="{{ $mat->id }}" class="material-checkbox bulk-material-checkbox">
                                                <label for="bulk_material_{{ $mat->id }}">{{ $mat->material_title }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bulkStartDate" class="font-weight-600">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="bulkStartDate" class="form-control" required>
                            <small class="text-muted">Training hanya bisa dimulai setelah tanggal ini</small>
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
                        <label class="font-weight-600">Riwayat Pengerjaan Sesi:</label>
                        <div id="viewSessions" class="form-control-plaintext">-</div>
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
                                    <label class="font-weight-600">Karyawan <span class="text-danger">*</span></label>
                                    <div class="employee-checkbox-container">
                                        @foreach($divisis as $divisi)
                                            @if(isset($employeesByDivisi[$divisi->id]) && $employeesByDivisi[$divisi->id]->count() > 0)
                                                <div class="divisi-group">
                                                    <div class="divisi-group-header">
                                                        <span><i class="mdi mdi-office-building"></i> {{ $divisi->divisi }}</span>
                                                    </div>
                                                    @foreach($employeesByDivisi[$divisi->id] as $emp)
                                                        <div class="employee-checkbox-item">
                                                            <input type="radio" name="employee_id" id="edit_employee_{{ $emp->id }}" value="{{ $emp->id }}" class="employee-radio edit-employee-radio" required>
                                                            <label for="edit_employee_{{ $emp->id }}">{{ $emp->name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <small class="text-muted">Pilih satu karyawan untuk assignment ini</small>
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
                            <label for="editStartDate" class="font-weight-600">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="editStartDate" class="form-control" required>
                            <small class="text-muted">Training hanya bisa dimulai setelah tanggal ini</small>
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
                    { data: 'session_code', name: 'session_code', orderable: true, searchable: true },
                    { data: 'dates', name: 'dates' },
                    { data: 'progress_bar', name: 'progress_bar' },
                    { data: 'status_badge', name: 'status_badge' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            // Select all per divisi (hanya untuk checkbox, bukan radio)
            $(document).on('click', '.select-all-divisi', function() {
                var divisiId = $(this).data('divisi');
                var divisiGroup = $(this).closest('.divisi-group');
                var checkboxes = divisiGroup.find('input[type="checkbox"]'); // Hanya checkbox, bukan radio
                var allChecked = checkboxes.filter(':checked').length === checkboxes.length;
                
                if (allChecked) {
                    // Uncheck all
                    checkboxes.prop('checked', false);
                    $(this).text('Pilih Semua');
                } else {
                    // Check all
                    checkboxes.prop('checked', true);
                    $(this).text('Batal Pilih Semua');
                }
            });

            // Select all per category untuk materials
            $(document).on('click', '.select-all-category', function() {
                var categoryId = $(this).data('category');
                var categoryGroup = $(this).closest('.category-group');
                var checkboxes = categoryGroup.find('input[type="checkbox"].material-checkbox');
                var allChecked = checkboxes.filter(':checked').length === checkboxes.length;
                
                if (allChecked) {
                    // Uncheck all
                    checkboxes.prop('checked', false);
                    $(this).text('Pilih Semua');
                } else {
                    // Check all
                    checkboxes.prop('checked', true);
                    $(this).text('Batal Pilih Semua');
                }
            });


            // Select all materials untuk bulk modal
            $('#selectAllBulkMaterials').on('click', function() {
                $('#bulkModal .bulk-material-checkbox').prop('checked', true);
                $('#bulkModal .select-all-category').text('Batal Pilih Semua');
            });

            // Deselect all materials untuk bulk modal
            $('#deselectAllBulkMaterials').on('click', function() {
                $('#bulkModal .bulk-material-checkbox').prop('checked', false);
                $('#bulkModal .select-all-category').text('Pilih Semua');
            });

            // Select all materials untuk edit modal
            $('#selectAllEditMaterials').on('click', function() {
                $('#editModal .edit-material-checkbox').prop('checked', true);
                $('#editModal .select-all-category').text('Batal Pilih Semua');
            });

            // Deselect all materials untuk edit modal
            $('#deselectAllEditMaterials').on('click', function() {
                $('#editModal .edit-material-checkbox').prop('checked', false);
                $('#editModal .select-all-category').text('Pilih Semua');
            });

            // Select all employees untuk bulk assign
            $('#selectAllEmployees').on('click', function() {
                $('#bulkModal .bulk-employee-checkbox').prop('checked', true);
                $('#bulkModal .select-all-divisi').text('Batal Pilih Semua');
            });

            // Deselect all employees untuk bulk assign
            $('#deselectAllEmployees').on('click', function() {
                $('#bulkModal .bulk-employee-checkbox').prop('checked', false);
                $('#bulkModal .select-all-divisi').text('Pilih Semua');
            });

            // Update text "Pilih Semua" berdasarkan status checkbox di divisi (hanya untuk checkbox di bulk modal)
            // Jangan gunakan stopPropagation agar checkbox bisa berfungsi normal
            $(document).on('change', '#bulkModal input[type="checkbox"].employee-checkbox', function(e) {
                var divisiGroup = $(this).closest('.divisi-group');
                var checkboxes = divisiGroup.find('input[type="checkbox"]'); // Hanya checkbox
                var checkedCount = checkboxes.filter(':checked').length;
                var totalCount = checkboxes.length;
                var selectAllBtn = divisiGroup.find('.select-all-divisi');
                
                if (selectAllBtn.length > 0) {
                    if (checkedCount === totalCount) {
                        selectAllBtn.text('Batal Pilih Semua');
                    } else {
                        selectAllBtn.text('Pilih Semua');
                    }
                }
            });

            // Reset form saat modal ditutup
            $('#bulkModal').on('hidden.bs.modal', function() {
                $('#bulkForm')[0].reset();
                $('#bulkModal .bulk-employee-checkbox').prop('checked', false);
                $('#bulkModal .bulk-material-checkbox').prop('checked', false);
                $('#bulkModal .select-all-divisi').text('Pilih Semua');
                $('#bulkModal .select-all-category').text('Pilih Semua');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#editModal .edit-employee-radio').prop('checked', false);
                $('#editModal .edit-material-checkbox').prop('checked', false);
                $('#editModal .select-all-category').text('Pilih Semua');
            });


            // Bulk Assign button
            window.bulkAssign = function() {
                // Pastikan semua checkbox di bulk modal bisa dicentang tanpa batas
                $('#bulkModal input[type="checkbox"].bulk-employee-checkbox').each(function() {
                    // Pastikan checkbox tidak disabled dan bisa dicentang
                    $(this).prop('disabled', false);
                    $(this).removeAttr('disabled');
                });
                $('#bulkModal').modal('show');
            }
            
            // Pastikan checkbox di bulk modal bisa dicentang tanpa batas
            // Hapus semua event handler yang mungkin mengintervensi
            $(document).off('click change', '#bulkModal input[type="checkbox"].bulk-employee-checkbox');
            
            // Tambahkan handler sederhana yang tidak membatasi
            $(document).on('click', '#bulkModal input[type="checkbox"].bulk-employee-checkbox', function(e) {
                // Biarkan browser handle click secara default - jangan prevent default
                // Checkbox akan toggle secara otomatis
                console.log('Checkbox clicked:', $(this).val(), 'Current checked:', $(this).prop('checked'));
            });
            
            // Update text "Pilih Semua" saat checkbox di bulk modal berubah (tanpa mengintervensi behavior checkbox)
            $(document).on('change', '#bulkModal input[type="checkbox"].bulk-employee-checkbox', function(e) {
                // Update text "Pilih Semua" di divisi
                var divisiGroup = $(this).closest('.divisi-group');
                var checkboxes = divisiGroup.find('input[type="checkbox"]');
                var checkedCount = checkboxes.filter(':checked').length;
                var totalCount = checkboxes.length;
                var selectAllBtn = divisiGroup.find('.select-all-divisi');
                
                if (selectAllBtn.length > 0) {
                    if (checkedCount === totalCount) {
                        selectAllBtn.text('Batal Pilih Semua');
                    } else {
                        selectAllBtn.text('Pilih Semua');
                    }
                }
                
                // Debug: log jumlah checkbox yang dicentang
                var totalChecked = $('#bulkModal input[type="checkbox"].bulk-employee-checkbox:checked').length;
                console.log('Total checkbox dicentang:', totalChecked);
            });
            
            // Debug: cek apakah semua checkbox di bulk modal benar-benar checkbox
            $('#bulkModal').on('shown.bs.modal', function() {
                var checkboxes = $(this).find('input[type="checkbox"].bulk-employee-checkbox');
                var radios = $(this).find('input[type="radio"]');
                console.log('Bulk Modal - Checkboxes:', checkboxes.length, 'Radios:', radios.length);
                if (radios.length > 0) {
                    console.error('Ada radio button di Bulk Modal! Seharusnya hanya checkbox.');
                }
                
                // Pastikan semua checkbox memiliki name yang benar
                checkboxes.each(function() {
                    var name = $(this).attr('name');
                    if (name !== 'employee_ids[]') {
                        console.error('Checkbox name salah:', name, 'Seharusnya: employee_ids[]');
                    }
                });
            });

            // Bulk Form
            $('#bulkForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validasi: pastikan minimal 1 karyawan dipilih
                var selectedEmployees = $('#bulkModal input[name="employee_ids[]"]:checked');
                if (selectedEmployees.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal 1 karyawan'
                    });
                    return;
                }

                // Validasi: pastikan minimal 1 materi dipilih
                var selectedMaterials = $('#bulkModal input[name="material_ids[]"]:checked').length;
                if (selectedMaterials === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal 1 materi training'
                    });
                    return;
                }

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
                        $('#bulkModal .bulk-employee-checkbox').prop('checked', false);
                        $('#bulkModal .select-all-divisi').text('Pilih Semua');
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
                    $('#viewSessions').html(data.sessions_html || '<p class="text-muted">Tidak ada data sesi.</p>');
                    $('#viewModal').modal('show');
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.assignments.edit', ':id') }}`.replace(':id', id), function(data) {
                    $('#editId').val(data.id);
                    $('#editTrainingId').val(data.training_id);
                    
                    // Set employee radio button
                    $('#editModal .edit-employee-radio').prop('checked', false);
                    $('#editModal input[name="employee_id"][value="' + data.employee_id + '"]').prop('checked', true);
                    
                    // Set material checkboxes
                    $('#editModal .edit-material-checkbox').prop('checked', false);
                    if (data.material_ids && Array.isArray(data.material_ids)) {
                        data.material_ids.forEach(function(materialId) {
                            $('#editModal input[type="checkbox"][name="material_ids[]"][value="' + materialId + '"]').prop('checked', true);
                        });
                    }
                    $('#editStartDate').val(data.start_date || '');
                    $('#editDeadline').val(data.deadline_date);
                    $('#editNotes').val(data.notes || '');
                    $('#editModal').modal('show');
                });
            });

            // Edit Form
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validasi: pastikan minimal 1 karyawan dipilih
                var selectedEmployee = $('#editModal input[name="employee_id"]:checked').val();
                if (!selectedEmployee) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal 1 karyawan'
                    });
                    return;
                }

                // Validasi: pastikan minimal 1 materi dipilih
                var selectedMaterials = $('#editModal input[name="material_ids[]"]:checked').length;
                if (selectedMaterials === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal 1 materi training'
                    });
                    return;
                }

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

            // View session button (untuk bulk assign)
            $(document).on('click', '.btn-view-session', function() {
                var sessionCode = $(this).data('session-code');
                $.get(`{{ route('hr.portal-training.master.assignments.viewSession', ':code') }}`.replace(':code', sessionCode), function(data) {
                    // Tampilkan detail semua assignment dalam sesi
                    var html = '<div class="table-responsive"><table class="table table-sm">';
                    html += '<thead><tr><th>No</th><th>Karyawan</th><th>Status</th><th>Progress</th></tr></thead><tbody>';
                    data.assignments.forEach(function(assignment, index) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + assignment.employee_name + '</td>';
                        html += '<td>' + assignment.status_badge + '</td>';
                        html += '<td>' + assignment.progress_html + '</td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table></div>';
                    
                    Swal.fire({
                        title: 'Detail Sesi: ' + sessionCode,
                        html: html,
                        width: '800px',
                        showCloseButton: true,
                        showConfirmButton: false
                    });
                });
            });

            // Delete session button (untuk bulk assign)
            $(document).on('click', '.btn-delete-session', function() {
                var sessionCode = $(this).data('session-code');
                Swal.fire({
                    title: 'Hapus Sesi Assignment?',
                    text: 'Semua assignment dalam sesi ini akan dihapus. Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('hr.portal-training.master.assignments.destroySession', ':code') }}`.replace(':code', sessionCode),
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

            // Start button (single assign)
            $(document).on('click', '.btn-start', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Mulai Training?',
                    text: 'Training akan dimulai dan status berubah menjadi "Sedang Dikerjakan".',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Mulai!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('hr.portal-training.master.assignments.start', ':id') }}`.replace(':id', id),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
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

            // Start session button (bulk assign)
            $(document).on('click', '.btn-start-session', function() {
                var sessionCode = $(this).data('session-code');
                Swal.fire({
                    title: 'Buka Training Sesi?',
                    text: 'Semua assignment dalam sesi ini akan dibuka untuk bisa dikerjakan oleh karyawan. Status akan tetap "Ditetapkan" sampai karyawan mulai mengerjakan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Mulai Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('hr.portal-training.master.assignments.startSession', ':code') }}`.replace(':code', sessionCode),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
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

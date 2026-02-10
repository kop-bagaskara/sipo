@extends('main.layouts.main')
@section('title')
    Master Tingkat Kesulitan
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

        #datatable-levels_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        #datatable-levels_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        #datatable-levels th {
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

        #datatable-levels_wrapper .dataTables_info {
            padding-top: 15px;
            color: #666;
        }

        #datatable-levels_wrapper .dataTables_paginate {
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

        .btn-add-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .btn-add-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
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
    </style>
@endsection
@section('page-title')
    Master Tingkat Kesulitan
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <!-- Page Header -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="mt-2"><i class="mdi mdi-speedometer"></i> Master Tingkat Kesulitan</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                <li class="breadcrumb-item active">Master Tingkat Kesulitan</li>
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
                            <i class="mdi mdi-format-list-bulleted"></i> Daftar Tingkat Kesulitan
                        </h4>
                    </div>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">
                        <i class="mdi mdi-plus-circle"></i> Tingkat Kesulitan Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-levels" class="table table-hover" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="200">Nama Level</th>
                                    <th width="150">Kode</th>
                                    <th width="150">Score Multiplier</th>
                                    <th width="100">Urutan</th>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-plus-circle"></i> Tambah Tingkat Kesulitan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="levelName" class="font-weight-600">Nama Level <span class="text-danger">*</span></label>
                            <select name="level_name" id="levelName" class="form-control" required>
                                <option value="">Pilih Level</option>
                                <option value="paling mudah">Paling Mudah</option>
                                <option value="mudah">Mudah</option>
                                <option value="cukup">Cukup</option>
                                <option value="menengah ke atas">Menengah ke Atas</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="levelCode" class="font-weight-600">Kode Level <span class="text-danger">*</span></label>
                            <input type="text" name="level_code" id="levelCode" class="form-control"
                                required placeholder="Contoh: VERY_EASY">
                        </div>
                        <div class="form-group">
                            <label for="scoreMultiplier" class="font-weight-600">Score Multiplier</label>
                            <input type="number" name="score_multiplier" id="scoreMultiplier" class="form-control"
                                value="1" step="0.1" min="0" max="10">
                            <small class="text-muted">Multiplier untuk skor soal (default: 1)</small>
                        </div>
                        <div class="form-group">
                            <label for="levelOrder" class="font-weight-600">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="level_order" id="levelOrder" class="form-control" required min="1" placeholder="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-pencil"></i> Edit Tingkat Kesulitan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editLevelName" class="font-weight-600">Nama Level <span class="text-danger">*</span></label>
                            <select name="level_name" id="editLevelName" class="form-control" required>
                                <option value="">Pilih Level</option>
                                <option value="paling mudah">Paling Mudah</option>
                                <option value="mudah">Mudah</option>
                                <option value="cukup">Cukup</option>
                                <option value="menengah ke atas">Menengah ke Atas</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editLevelCode" class="font-weight-600">Kode Level <span class="text-danger">*</span></label>
                            <input type="text" name="level_code" id="editLevelCode" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="editScoreMultiplier" class="font-weight-600">Score Multiplier</label>
                            <input type="number" name="score_multiplier" id="editScoreMultiplier" class="form-control"
                                step="0.1" min="0" max="10">
                        </div>
                        <div class="form-group">
                            <label for="editLevelOrder" class="font-weight-600">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="level_order" id="editLevelOrder" class="form-control" required min="1">
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
            var table = $('#datatable-levels').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("hr.portal-training.master.difficulty-levels.getData") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'level_name', name: 'level_name' },
                    { data: 'level_code', name: 'level_code' },
                    { data: 'score_multiplier', name: 'score_multiplier' },
                    { data: 'level_order', name: 'level_order' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[4, 'asc']]
            });

            // Add Form
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("hr.portal-training.master.difficulty-levels.store") }}',
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

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.difficulty-levels.show', ':id') }}`.replace(':id', id), function(data) {
                    $('#editId').val(data.id);
                    $('#editLevelName').val(data.level_name);
                    $('#editLevelCode').val(data.level_code);
                    $('#editScoreMultiplier').val(data.score_multiplier);
                    $('#editLevelOrder').val(data.level_order);
                    $('#editModal').modal('show');
                });
            });

            // Edit Form
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                $.ajax({
                    url: `{{ route('hr.portal-training.master.difficulty-levels.update', ':id') }}`.replace(':id', id),
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
                    title: 'Hapus Tingkat Kesulitan?',
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
                            url: `{{ route('hr.portal-training.master.difficulty-levels.destroy', ':id') }}`.replace(':id', id),
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

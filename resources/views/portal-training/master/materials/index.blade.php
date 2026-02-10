@extends('main.layouts.main')
@section('title')
    Master Materi Training
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

        #datatable-materials_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        #datatable-materials_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        #datatable-materials th {
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

        #datatable-materials_wrapper .dataTables_info {
            padding-top: 15px;
            color: #666;
        }

        #datatable-materials_wrapper .dataTables_paginate {
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

        .badge-active {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }

        .badge-inactive {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
    </style>
@endsection
@section('page-title')
    Master Materi Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <!-- Page Header -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="mt-2"><i class="mdi mdi-video"></i> Master Materi Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                <li class="breadcrumb-item active">Master Materi</li>
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
                            <i class="mdi mdi-format-list-bulleted"></i> Daftar Materi Training
                        </h4>
                    </div>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">
                        <i class="mdi mdi-plus-circle"></i> Tambah Materi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-materials" class="table table-hover" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="250">Judul</th>
                                    <th width="150">Kategori</th>
                                    <th width="100">Durasi</th>
                                    <th width="80">Urutan</th>
                                    <th width="100">Status</th>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-plus-circle"></i> Tambah Materi Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materialTitle" class="font-weight-600">Judul Materi <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="materialTitle" class="form-control"
                                        placeholder="Contoh: Pengenalan Leadership" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materialCategory" class="font-weight-600">Kategori <span class="text-danger">*</span></label>
                                    <select name="category_id" id="materialCategory" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="materialDescription" class="font-weight-600">Deskripsi</label>
                            <textarea name="description" id="materialDescription" class="form-control" rows="3"
                                placeholder="Jelaskan materi training ini..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="materialVideoPath" class="font-weight-600">Video Path/URL <span class="text-danger">*</span></label>
                            <input type="text" name="video_path" id="materialVideoPath" class="form-control"
                                required placeholder="path/to/video.mp4 atau URL lengkap">
                            <small class="text-muted">Path relatif dari storage/public atau URL lengkap video</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materialDuration" class="font-weight-600">Durasi (detik) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="materialDuration" class="form-control"
                                        required min="1" placeholder="Contoh: 300 untuk 5 menit">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materialOrder" class="font-weight-600">Urutan <span class="text-danger">*</span></label>
                                    <input type="number" name="order" id="materialOrder" class="form-control"
                                        required min="1" placeholder="1">
                                    <small class="text-muted">Semakin kecil angka, semakin atas posisinya</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActiveAdd" checked>
                            <label class="form-check-label" for="isActiveAdd">
                                <strong>Aktif</strong>
                            </label>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-pencil"></i> Edit Materi Training</h5>
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
                                    <label for="editTitle" class="font-weight-600">Judul Materi <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="editTitle" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editCategoryId" class="font-weight-600">Kategori <span class="text-danger">*</span></label>
                                    <select name="category_id" id="editCategoryId" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editDescription" class="font-weight-600">Deskripsi</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editVideoPath" class="font-weight-600">Video Path/URL <span class="text-danger">*</span></label>
                            <input type="text" name="video_path" id="editVideoPath" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editDuration" class="font-weight-600">Durasi (detik) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="editDuration" class="form-control" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editOrder" class="font-weight-600">Urutan <span class="text-danger">*</span></label>
                                    <input type="number" name="order" id="editOrder" class="form-control" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActiveEdit">
                            <label class="form-check-label" for="isActiveEdit">
                                <strong>Aktif</strong>
                            </label>
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

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-eye"></i> Detail Materi Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Judul Materi:</label>
                                <p id="viewTitle" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Kategori:</label>
                                <p id="viewCategory" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Deskripsi:</label>
                        <p id="viewDescription" class="form-control-plaintext">-</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Video Path/URL:</label>
                                <p id="viewVideoPath" class="form-control-plaintext text-break">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Durasi:</label>
                                <p id="viewDuration" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Urutan:</label>
                                <p id="viewOrder" class="form-control-plaintext">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Status:</label>
                                <p id="viewStatus" class="form-control-plaintext">-</p>
                            </div>
                        </div>
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
            var table = $('#datatable-materials').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("hr.portal-training.master.materials.getData") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'category_name', name: 'category_name' },
                    { data: 'duration_formatted', name: 'duration_formatted' },
                    { data: 'order', name: 'order' },
                    { data: 'status_badge', name: 'status_badge' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[4, 'asc']]
            });

            // Add Form
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('is_active', $('#isActiveAdd').is(':checked') ? 1 : 0);

                $.ajax({
                    url: '{{ route("hr.portal-training.master.materials.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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

            // View button
            $(document).on('click', '.btn-view', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.materials.show', ':id') }}`.replace(':id', id), function(data) {
                    $('#viewTitle').text(data.title || '-');
                    $('#viewDescription').text(data.description || '-');
                    $('#viewCategory').text(data.category_name || '-');
                    $('#viewVideoPath').text(data.video_path || '-');
                    $('#viewDuration').text(data.duration ? data.duration + ' detik' : '-');
                    $('#viewOrder').text(data.order || '-');
                    $('#viewStatus').html(data.is_active ?
                        '<span class="badge badge-success">Aktif</span>' :
                        '<span class="badge badge-secondary">Tidak Aktif</span>');
                    $('#viewModal').modal('show');
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.materials.show', ':id') }}`.replace(':id', id), function(data) {
                    $('#editId').val(data.id);
                    $('#editTitle').val(data.title);
                    $('#editDescription').val(data.description || '');
                    $('#editCategoryId').val(data.category_id);
                    $('#editVideoPath').val(data.video_path);
                    $('#editDuration').val(data.duration);
                    $('#editOrder').val(data.order);
                    $('#isActiveEdit').prop('checked', data.is_active == 1);
                    $('#editModal').modal('show');
                });
            });

            // Edit Form
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                var formData = new FormData(this);
                formData.append('is_active', $('#isActiveEdit').is(':checked') ? 1 : 0);

                $.ajax({
                    url: `{{ route('hr.portal-training.master.materials.update', ':id') }}`.replace(':id', id),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'PUT'
                    },
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
                    title: 'Hapus Materi?',
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
                            url: `{{ route('hr.portal-training.master.materials.destroy', ':id') }}`.replace(':id', id),
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

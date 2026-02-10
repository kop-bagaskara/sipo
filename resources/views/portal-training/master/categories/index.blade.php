@extends('main.layouts.main')
@section('title')
    Master Kategori Materi
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <style>
        /* .page-header-wrapper {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 25px 0;
                    margin-bottom: 25px;
                    border-radius: 10px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .page-header-wrapper h3 {
                    color: white;
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                } */
        /* .breadcrumb-custom {
                    background: transparent;
                    padding: 0;
                    margin: 0;
                    margin-bottom: 0;
                }
                .breadcrumb-custom .breadcrumb-item {
                    color: rgba(255,255,255,0.8);
                }
                .breadcrumb-custom .breadcrumb-item a {
                    color: rgba(255,255,255,0.9);
                    text-decoration: none;
                }
                .breadcrumb-custom .breadcrumb-item a:hover {
                    color: white;
                }
                .breadcrumb-custom .breadcrumb-item.active {
                    color: white;
                    font-weight: 500;
                } */
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

        #datatable-categories_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        #datatable-categories_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        #datatable-categories th {
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

        #datatable-categories_wrapper .dataTables_info {
            padding-top: 15px;
            color: #666;
        }

        #datatable-categories_wrapper .dataTables_paginate {
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
            /* background-c: background-primary; */
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
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
    Master Kategori Materi
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        {{-- <div class="container-fluid"> --}}
        <!-- Page Header -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="mt-2"><i class="mdi mdi-folder-multiple"></i> Master Kategori Materi</h3>

            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                    <li class="breadcrumb-item active">Master Kategori</li>
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
                                <i class="mdi mdi-format-list-bulleted"></i> Daftar Kategori Materi
                            </h4>
                        </div>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">
                            <i class="mdi mdi-plus-circle"></i> Tambah Kategori
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-categories" class="table table-hover" style="width: 100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="250">Nama Kategori</th>
                                        <th>Deskripsi</th>
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
        {{-- </div> --}}

        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="mdi mdi-plus-circle"></i> Tambah Kategori Materi</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="categoryName" class="font-weight-600">Nama Kategori <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="categoryName" class="form-control"
                                    placeholder="Contoh: Leadership, Technical Skills, dll" required>
                            </div>
                            <div class="form-group">
                                <label for="categoryDesc" class="font-weight-600">Deskripsi</label>
                                <textarea name="description" id="categoryDesc" class="form-control" rows="3"
                                    placeholder="Jelaskan kategori materi ini..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="categoryOrder" class="font-weight-600">Urutan <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="order" id="categoryOrder" class="form-control" required
                                    min="1" placeholder="1">
                                <small class="text-muted">Semakin kecil angka, semakin atas posisinya</small>
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
                        <h5 class="modal-title"><i class="mdi mdi-pencil"></i> Edit Kategori Materi</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editForm">
                        <input type="hidden" name="id" id="editId">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="editCategoryName" class="font-weight-600">Nama Kategori <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="editCategoryName" class="form-control"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="editCategoryDesc" class="font-weight-600">Deskripsi</label>
                                <textarea name="description" id="editCategoryDesc" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editCategoryOrder" class="font-weight-600">Urutan <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="order" id="editCategoryOrder" class="form-control"
                                    required min="1">
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
                var table = $('#datatable-categories').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('hr.portal-training.master.categories.getData') }}',
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'order',
                            name: 'order'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [3, 'asc']
                    ]
                });

                // Add Form
                $('#addForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '{{ route('hr.portal-training.master.categories.store') }}',
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
                    $.get(`{{ route('hr.portal-training.master.categories.show', ':id') }}`.replace(':id', id),
                        function(data) {
                            $('#editId').val(data.id);
                            $('#editCategoryName').val(data.name);
                            $('#editCategoryDesc').val(data.description);
                            $('#editCategoryOrder').val(data.order);
                            $('#editModal').modal('show');
                        });
                });

                // Edit Form
                $('#editForm').on('submit', function(e) {
                    e.preventDefault();
                    var id = $('#editId').val();
                    $.ajax({
                        url: `{{ route('hr.portal-training.master.categories.update', ':id') }}`
                            .replace(':id', id),
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
                        title: 'Hapus Kategori?',
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
                                url: `{{ route('hr.portal-training.master.categories.destroy', ':id') }}`
                                    .replace(':id', id),
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
                                        text: xhr.responseJSON?.message ||
                                            'Terjadi kesalahan.'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection

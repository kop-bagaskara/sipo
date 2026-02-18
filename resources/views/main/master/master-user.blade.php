@extends('main.layouts.main')
@section('title')
    Master User
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Master User
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master User</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                <li class="breadcrumb-item active">User</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Master User</h4>
                        </div>
                        <div class="col">
                            <div class="header-right d-flex flex-wrap justify-content-end">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModal">
                                    Add User
                                </button>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filter-divisi" class="form-label font-weight-bold">Filter Divisi</label>
                            <select id="filter-divisi" class="form-control">
                                <option value="">-- Semua Divisi --</option>
                                @foreach ($divisis as $item)
                                    <option value="{{ $item->id }}">{{ $item->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary btn-sm" id="btn-reset-filter" title="Reset Filter">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div style="font-size: 15px;">
                        <table id="datatable-user" class="table table-responsive-md" style="width: 100%; font-size: 15px;">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th>Fullname</th>
                                    <th>Username</th>
                                    <th>Divisi</th>
                                    <th>Email</th>
                                    <th>Jabatan</th>
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="submitMachine" id="submitMachine">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_button_1">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_usersystem" id="id_usersystem" value="">
                        <input type="hidden" name="role_usersystem_name" id="role_usersystem_name" value="">

                        <div id="error-messages" class="alert alert-danger" style="display: none;">
                            <ul class="mb-0" id="error-list"></ul>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" id="fullname" class="form-control" required>
                            <small class="text-danger error-message" id="fullname-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" class="form-control" required>
                            <small class="text-danger error-message" id="username-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                            <small class="text-danger error-message" id="email-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Password <span class="text-danger" id="password-required">*</span>
                                <span class="text-muted" id="password-hint" style="display: none;">(Kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <input type="password" name="password" id="password" class="form-control">
                            <small class="text-danger error-message" id="password-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" id="level" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Level --</option>
                                @foreach ($levels as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->level }}">
                                        {{ $item->level }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-message" id="level-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Divisi <span class="text-danger">*</span></label>
                            <select name="divisi" id="divisi" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Divisi --</option>
                                @foreach ($divisis as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->divisi }}">
                                        {{ $item->divisi }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-message" id="divisi-error"></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select name="jabatan" id="jabatan" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->jabatan }}">
                                        {{ $item->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-message" id="jabatan-error"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_button">Close</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <span class="spinner-border spinner-border-sm d-none" id="submit-spinner" role="status" aria-hidden="true"></span>
                            <span id="submit-text">Save</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
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
    <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var datatable = $('#datatable-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('master.user-data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.filter_divisi = $('#filter-divisi').val(); // Tambahkan filter divisi
                    }
                },
                order: [[0, 'asc']],
                columns: [
                    {
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
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'divisi_user.divisi',
                        name: 'divisi',
                        defaultContent: '-'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'jabatan_user.jabatan',
                        name: 'jabatan',
                        defaultContent: '-'
                    },
                    {
                        data: 'level_user.level',
                        name: 'level',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            var routeSubmitUser = "{{ route('master.user-data-submit') }}";
            var routeEditUser = "{{ route('master.user-data-detail', ['id' => 'ID_PLACEHOLDER']) }}";
            var routeDeleteUser = "{{ route('master.delete-master-user-data', ['id' => 'ID_PLACEHOLDER']) }}";
            var isEditMode = false;

            // Reset form dan error messages
            function resetForm() {
                $('#submitMachine')[0].reset();
                $('#id_usersystem').val('');
                $('#role_usersystem_name').val('');
                $('.error-message').text('').hide();
                $('#error-messages').hide();
                $('#error-list').empty();
                $('#password').removeAttr('required');
                $('#password-required').show();
                $('#password-hint').hide();
                isEditMode = false;
                $('#exampleModalLabel').text('Add User');
                $('#submit-text').text('Save');
            }

            // Clear error messages
            function clearErrors() {
                $('.error-message').text('').hide();
                $('#error-messages').hide();
                $('#error-list').empty();
                $('.form-control, .form-control select').removeClass('is-invalid');
            }

            // Show error messages
            function showErrors(errors) {
                clearErrors();
                var errorList = $('#error-list');

                if (typeof errors === 'object') {
                    $.each(errors, function(key, value) {
                        var errorMsg = Array.isArray(value) ? value[0] : value;
                        var field = $('#' + key);

                        if (field.length) {
                            field.addClass('is-invalid');
                            var errorElement = $('#' + key + '-error');
                            if (errorElement.length) {
                                errorElement.text(errorMsg).show();
                            }
                        }

                        errorList.append('<li>' + errorMsg + '</li>');
                    });

                    if (errorList.children().length > 0) {
                        $('#error-messages').show();
                    }
                } else if (typeof errors === 'string') {
                    errorList.append('<li>' + errors + '</li>');
                    $('#error-messages').show();
                }
            }

            // Set loading state
            function setLoading(isLoading) {
                var submitBtn = $('#submit-btn');
                var spinner = $('#submit-spinner');
                var submitText = $('#submit-text');

                if (isLoading) {
                    submitBtn.prop('disabled', true);
                    spinner.removeClass('d-none');
                    submitText.text('Menyimpan...');
                } else {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    submitText.text(isEditMode ? 'Update' : 'Save');
                }
            }

            // Modal events
            $('#exampleModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            $('#exampleModal').on('show.bs.modal', function() {
                clearErrors();
            });

            // Button Add User
            $('button[data-target="#exampleModal"]').on('click', function() {
                resetForm();
                $('#exampleModal').modal('show');
            });

            // Form submit
            $('#submitMachine').submit(function(e) {
                e.preventDefault();
                clearErrors();

                var formData = $(this).serializeArray();
                var userId = $('#id_usersystem').val();
                isEditMode = userId !== '';

                // Validasi password untuk mode add
                if (!isEditMode && !$('#password').val()) {
                    showErrors({ password: 'Password wajib diisi' });
                    $('#password').addClass('is-invalid');
                    return;
                }

                // Jika edit dan password kosong, hapus dari formData
                // if (isEditMode && !$('#password').val()) {
                //     formData = formData.filter(function(item) {
                //         return item.name !== 'password';
                //     });
                // }

                setLoading(true);

                $.ajax({
                    url: routeSubmitUser,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        setLoading(false);

                        if (response.errors) {
                            showErrors(response.errors);
                        } else {
                            $('#exampleModal').modal('hide');
                            resetForm();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || response.success || 'Data berhasil disimpan',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(function() {
                                datatable.draw();
                            });
                        }
                    },
                    error: function(xhr) {
                        setLoading(false);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || xhr.responseJSON.message;
                            showErrors(errors);
                        } else {
                            var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                showConfirmButton: true
                            });
                        }
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-data', function() {
                var id = $(this).data('id');
                var url = routeEditUser.replace('ID_PLACEHOLDER', id);

                resetForm();
                isEditMode = true;
                $('#exampleModalLabel').text('Edit User');
                $('#submit-text').text('Update');
                $('#password-required').hide();
                $('#password-hint').show();
                $('#password').removeAttr('required');
                $('#exampleModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data) {
                            var data = response.data;

                            // Set values
                            $('#id_usersystem').val(data.id || '');
                            $('#fullname').val(data.name || '');
                            $('#username').val(data.username || '');
                            $('#email').val(data.email || '');
                            $('#level').val(data.level || '').trigger('change');
                            $('#divisi').val(data.divisi || '').trigger('change');
                            $('#jabatan').val(data.jabatan || '').trigger('change');

                            // Clear password field (security - jangan tampilkan password yang sudah di-hash)
                            $('#password').val('');

                            clearErrors();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data user tidak ditemukan',
                                showConfirmButton: true
                            });
                            $('#exampleModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        $('#exampleModal').modal('hide');
                        var errorMessage = 'Gagal memuat data user';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'Data user tidak ditemukan';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            showConfirmButton: true
                        });
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-data', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'user ini';
                var url = routeDeleteUser.replace('ID_PLACEHOLDER', id);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    html: `Data user <strong>${name}</strong> akan dihapus dan tidak dapat dikembalikan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Data berhasil dihapus',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                datatable.draw();
                            },
                            error: function(xhr) {
                                var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Remove invalid class on input
            $('.form-control').on('input change', function() {
                $(this).removeClass('is-invalid');
                var errorElement = $('#' + $(this).attr('id') + '-error');
                if (errorElement.length) {
                    errorElement.text('').hide();
                }
            });

            // Filter Divisi
            $('#filter-divisi').on('change', function() {
                datatable.draw();
            });

            // Reset Filter
            $('#btn-reset-filter').on('click', function() {
                $('#filter-divisi').val('');
                datatable.draw();
            });
        });
    </script>
@endsection

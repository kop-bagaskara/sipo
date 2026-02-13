<?php $__env->startSection('title'); ?>
    Master Setting
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Master Setting
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Setting</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Master Setting</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">Data Setting</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#settingModal" onclick="resetForm()">
                                <i class="mdi mdi-plus"></i> Tambah Setting
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="settings-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Master Key</th>
                                        <th>Value</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Setting -->
        <div class="modal fade" id="settingModal" tabindex="-1" role="dialog" aria-labelledby="settingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingModalLabel">Tambah Setting</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="settingForm">
                        <div class="modal-body">
                            <input type="hidden" id="setting_id" name="setting_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="master">Master Key <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="master" name="master"
                                               placeholder="Contoh: active_machines_plan" required>
                                        <small class="form-text text-muted">
                                            Gunakan format snake_case untuk master key
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="value">Value <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="value" name="value"
                                               placeholder="Contoh: CD6-3,CX-104,POL1,POL2" required>
                                        <small class="form-text text-muted">
                                            Untuk multiple values, pisahkan dengan koma
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            let table;
            let isEdit = false;

            $(document).ready(function() {
                // Initialize DataTable
                table = $('#settings-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "<?php echo e(route('settings.index')); ?>",
                        type: "GET"
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'master', name: 'master'},
                        {data: 'value', name: 'value'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    }
                });

                // Form submit
                $('#settingForm').submit(function(e) {
                    e.preventDefault();
                    submitForm();
                });
            });

            function resetForm() {
                isEdit = false;
                $('#settingForm')[0].reset();
                $('#setting_id').val('');
                $('#settingModalLabel').text('Tambah Setting');
                $('#btnSubmit').text('Simpan');
            }

            function editSetting(id) {
                isEdit = true;
                $('#settingModalLabel').text('Edit Setting');
                $('#btnSubmit').text('Update');

                $.ajax({
                    url: "<?php echo e(route('settings.index')); ?>/" + id + "/edit",
                    type: "GET",
                    success: function(response) {
                        $('#setting_id').val(response.id);
                        $('#master').val(response.master);
                        $('#value').val(response.value);
                        $('#settingModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil data setting!'
                        });
                    }
                });
            }

            function deleteSetting(id) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Yakin ingin menghapus setting ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(route('settings.index')); ?>/" + id,
                            type: "DELETE",
                            data: {
                                _token: "<?php echo e(csrf_token()); ?>"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Setting berhasil dihapus!'
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal menghapus setting!'
                                });
                            }
                        });
                    }
                });
            }

            function submitForm() {
                let url = isEdit ?
                    "<?php echo e(route('settings.index')); ?>/" + $('#setting_id').val() :
                    "<?php echo e(route('settings.store')); ?>";

                let method = isEdit ? 'PUT' : 'POST';
                let data = {
                    _token: "<?php echo e(csrf_token()); ?>",
                    master: $('#master').val(),
                    value: $('#value').val()
                };

                if (isEdit) {
                    data._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(response) {
                        $('#settingModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: isEdit ? 'Setting berhasil diupdate!' : 'Setting berhasil ditambahkan!'
                        });
                        table.ajax.reload();
                        resetForm();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';

                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '\n';
                            });
                        } else {
                            errorMessage = 'Terjadi kesalahan!';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage
                        });
                    }
                });
            }
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>
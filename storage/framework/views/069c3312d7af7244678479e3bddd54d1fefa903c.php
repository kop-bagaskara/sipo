<?php $__env->startSection('title'); ?>
    Data Plan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        /* Timeline Styles */
        .timeline-wrapper {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            overflow: hidden;
        }

        .timeline-header {
            display: flex;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: bold;
            color: #5a5c69;
        }

        .timeline-time-header {
            width: 200px;
            padding: 12px 15px;
            border-right: 1px solid #e3e6f0;
        }

        .timeline-project-header {
            flex: 1;
            padding: 12px 15px;
            border-right: 1px solid #e3e6f0;
        }

        .timeline-status-header {
            width: 120px;
            padding: 12px 15px;
        }

        .timeline-content {
            max-height: 600px;
            overflow-y: auto;
        }

        .timeline-item {
            display: flex;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.2s;
        }

        .timeline-item:hover {
            background-color: #f8f9fc;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-time {
            width: 200px;
            padding: 15px;
            border-right: 1px solid #e3e6f0;
            background-color: #fff;
        }

        .timeline-project {
            flex: 1;
            padding: 15px;
            border-right: 1px solid #e3e6f0;
            background-color: #fff;
        }

        .timeline-status {
            width: 120px;
            padding: 15px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline-time-start {
            font-weight: bold;
            color: #1cc88a;
            font-size: 0.9rem;
        }

        .timeline-time-end {
            font-size: 0.8rem;
            color: #858796;
            margin-top: 2px;
        }

        .timeline-time-date {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
            font-style: italic;
        }

        .timeline-project-title {
            font-weight: bold;
            color: #5a5c69;
            margin-bottom: 5px;
        }

        .timeline-project-details {
            font-size: 0.85rem;
            color: #858796;
        }

        .timeline-project-customer {
            color: #4e73df;
            font-weight: 500;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-assigned {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-finished {
            background-color: #d4edda;
            color: #155724;
        }

        .status-approved {
            background-color: #cce5ff;
            color: #004085;
        }

        .timeline-empty {
            text-align: center;
            padding: 40px 20px;
            color: #858796;
            font-style: italic;
        }

        .timeline-loading {
            text-align: center;
            padding: 40px 20px;
            color: #858796;
        }

        .timeline-loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes  spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Data Plan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">List Task Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">List Task</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">


                    <div class="card-body">
                        <h3>To Do List</h3>
                        <div class="table-responsive" id="table-plan-assign-prepress">
                            <table id="datatable-list-plan-assign-prepress" class="table table-responsive-md"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Job Title</th>
                                        <th>Customer</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th style="width:10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- end row-->
                </div>

            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="submitAssignJob" id="submitAssignJob">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id_plan" id="id_plan">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Assign Job</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_job" id="id_job" class="form-control" hidden>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal Job</label>
                                        <input type="date" name="tanggal_job" id="tanggal_job" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal Deadline</label>
                                        <input type="date" name="tanggal_deadline" id="tanggal_deadline"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Customer</label>
                                <input type="text" name="customer" id="customer" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Product</label>
                                <input type="text" name="product" id="product" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Qty Order</label>
                                <input type="number" name="qty_order_estimation" id="qty_order_estimation"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Job Order</label>
                                <input type="text" name="job_order" id="job_order" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">File Data</label>
                                <input type="text" name="file_data" id="file_data" class="form-control"
                                    placeholder="Masukkan nama file, pisahkan dengan koma jika ada lebih dari satu file">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prioritas</label>
                                <select name="prioritas_job" id="prioritas_job" class="form-control" required>
                                    <option value disabled selected>-- Pilih Prioritas --</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Normal">Normal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" id="assigned_to" class="form-control" required>
                                    <option value disabled selected>-- Pilih PIC --</option>
                                    <?php $__currentLoopData = $pic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label class="form-label">Kategori Job</label>
                                <select name="kategori_job" id="kategori_job" class="form-control" required>
                                    <option value disabled selected>-- Pilih Kategori Job --</option>
                                    <?php $__currentLoopData = $masterData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->kode); ?>"><?php echo e($item->kode); ?> -
                                            <?php echo e($item->keterangan_job); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                id="close_button">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
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
        <script src="<?php echo e(asset('sipo_krisan/public/new/assets/pages/datatables-demo.js')); ?>"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable3 = $('#datatable-list-plan-assign-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?php echo e(route('prepress.plan-assigned.data')); ?>",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${moment(data).format('DD-MM-YYYY')}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${moment(data).format('DD-MM-YYYY')}</span>`;
                            }
                        },
                        // {
                        //     data: 'job_order',
                        //     name: 'job_order'
                        // },
                        {
                            data: 'job_title',
                            name: 'job_title'
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                if (data === 'Urgent') {
                                    return `<button type="button" class="btn btn-danger btn-sm">${data}</button>`;
                                } else {
                                    return `<button type="button" class="btn btn-primary btn-sm">${data}</button>`;
                                }
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                if (data === 'ASSIGNED') {
                                    return `<button type="button" class="btn btn-warning btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                } else if (data === 'FINISH') {
                                    return `<button type="button" class="btn btn-success btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                } else if (data === 'SHIFT_2') {
                                    return `<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                } else if (data === 'IN PROGRESS') {
                                    return `<button type="button" class="btn btn-primary btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                } else if (data === 'COMPLETED') {
                                    return `<button type="button" class="btn btn-secondary btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                } else {
                                    return `<button type="button" class="btn btn-secondary btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                                }
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'

                        }
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });

                $(document).on('click', '.job-order-assign-detail', function() {
                    var id = $(this).data('id');
                    var url = "<?php echo e(route('prepress.job-order.assign-job-data', ['id' => 'ID_PLACEHOLDER'])); ?>";
                    url = url.replace('ID_PLACEHOLDER', id);
                    window.location.href = url;
                });

            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/prepress/data/data-listtask.blade.php ENDPATH**/ ?>
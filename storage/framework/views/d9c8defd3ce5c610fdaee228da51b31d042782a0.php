<?php $__env->startSection('title'); ?>
    Data Job Order
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

        .rejection-info-btn {
            font-size: 11px;
            padding: 2px 8px;
            animation: pulse 2s infinite;
        }

        @keyframes  pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Data Job Order Prepress
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Job Order Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Job Order Prepress</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <a class="btn btn-info" href="<?php echo e(route('prepress.job-order.index')); ?>" style="width: 100%;">Input
                            Job Order</a>
                        <br>
                        <br>

                        <div class="">
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Filter Data</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="filter-tanggal-dari" class="form-label">Tanggal Dari:</label>
                                            <input type="date" class="form-control" id="filter-tanggal-dari" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="filter-tanggal-sampai" class="form-label">Tanggal Sampai:</label>
                                            <input type="date" class="form-control" id="filter-tanggal-sampai" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Status:</label>
                                            <div class="border p-2 rounded" style="max-height: 100px; overflow-y: auto; background-color: #f8f9fa;">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="OPEN" id="status-open">
                                                    <label class="form-check-label" for="status-open">OPEN</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="PLAN" id="status-plan">
                                                    <label class="form-check-label" for="status-plan">PLAN</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="ASSIGNED" id="status-assigned">
                                                    <label class="form-check-label" for="status-assigned">ASSIGNED</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="IN PROGRESS" id="status-inprogress">
                                                    <label class="form-check-label" for="status-inprogress">IN PROGRESS</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="FINISH" id="status-finish">
                                                    <label class="form-check-label" for="status-finish">FINISH</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="APPROVED" id="status-approved">
                                                    <label class="form-check-label" for="status-approved">APPROVED</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="COMPLETED" id="status-completed">
                                                    <label class="form-check-label" for="status-completed">COMPLETED</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="PENDING" id="status-pending">
                                                    <label class="form-check-label" for="status-pending">PENDING</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="CLOSED" id="status-closed">
                                                    <label class="form-check-label" for="status-closed">CLOSED</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-status-checkbox" type="checkbox" value="SHIFT_2" id="status-shift2">
                                                    <label class="form-check-label" for="status-shift2">SHIFT_2</label>
                                                </div>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" id="btn-select-all-status">Pilih Semua</button>
                                                    <button type="button" class="btn btn-sm btn-secondary ml-1" id="btn-unselect-all-status">Hapus Semua</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <div class="w-100">
                                                <button class="btn btn-primary w-100 mb-2" id="btn-filter-tanggal">
                                                    <i class="mdi mdi-filter"></i> Filter Data
                                                </button>
                                                <div class="d-flex">
                                                    <button class="btn btn-info flex-fill mr-2" id="btn-filter-pribadi">
                                                        <i class="mdi mdi-account"></i> Filter Data Pribadi
                                                    </button>
                                                    <button class="btn btn-success flex-fill" id="btn-export-excel">
                                                        <i class="mdi mdi-file-excel"></i> Export Excel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="datatable-search" class="form-label">Cari Data:</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                                <input type="text" class="form-control" id="datatable-search" placeholder="Cari berdasarkan No. Job Order, Customer, Product, dll...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table id="datatable-job-order-prepress" class="table"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        
                                        <th>No. Job Order</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        
                                        <th style="white-space: nowrap;">Job Order</th>
                                        
                                        <th>Prioritas</th>
                                        <th style="white-space: nowrap;">Status Job</th>
                                        <th style="white-space: nowrap; display: none;">PIC</th>
                                        <th style="white-space: nowrap; display: none;">Pending Reason</th>
                                        <th style="white-space: nowrap;">Created By</th>
                                        
                                        <th style="width:10%;">Action</th>
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
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="submitDivisi" id="submitDivisi">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Divisi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_divisi" id="id_divisi" class="form-control" hidden>

                            <div class="form-group">
                                <label class="form-label">Divisi</label>
                                <input type="text" name="d_divisi" id="d_divisi" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control">
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

        <!-- Modal Rejection Info -->
        <div class="modal fade" id="modalRejectionInfo" tabindex="-1" role="dialog" aria-labelledby="modalRejectionInfoLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalRejectionInfoLabel">
                            <i class="mdi mdi-alert-circle"></i>Detail Rejection
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Ditolak Oleh:</strong>
                                <p id="rejected-by-info" class="text-muted">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Rejection:</strong>
                                <p id="rejected-at-info" class="text-muted">-</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <strong>Alasan Rejection:</strong>
                                <div class="alert alert-danger mt-2" id="rejection-reason-info">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
        

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-job-order-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false, // Tampilkan semua data tanpa pagination
                    lengthChange: false, // Hilangkan dropdown length
                    searching: true,
                    deferLoading: 0, // Jangan load data otomatis
                    ajax: {
                        url: "<?php echo e(route('prepress.job-order.data')); ?>",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');

                            // Filter tanggal (wajib)
                            var tanggalDari = $('#filter-tanggal-dari').val();
                            var tanggalSampai = $('#filter-tanggal-sampai').val();

                            if (tanggalDari) {
                                d.tanggal_dari = tanggalDari;
                            }
                            if (tanggalSampai) {
                                d.tanggal_sampai = tanggalSampai;
                            }

                            // Filter status (multiple selection dari checkbox)
                            var selectedStatuses = [];
                            $('.filter-status-checkbox:checked').each(function() {
                                selectedStatuses.push($(this).val());
                            });
                            if (selectedStatuses.length > 0) {
                                d.status_job = selectedStatuses; // Kirim sebagai array
                            }

                            // inject filter created_by jika mode pribadi aktif
                            if (window.__laporanPribadi) {
                                d.created_by = '<?php echo e(auth()->user()->name); ?>';
                            }
                        },
                    },
                    dom: '<"row"<"col-sm-12"rt>>',
                    buttons: [
                        {
                            extend: 'excel',
                            text: 'Export Excel',
                            className: 'btn btn-success',
                            title: function() {
                                var tanggalDari = $('#filter-tanggal-dari').val();
                                var tanggalSampai = $('#filter-tanggal-sampai').val();
                                var selectedStatuses = [];
                                $('.filter-status-checkbox:checked').each(function() {
                                    selectedStatuses.push($(this).val());
                                });
                                var title = 'Data Job Order Prepress';
                                if (selectedStatuses.length > 0) {
                                    title += ' - Status ' + selectedStatuses.join(', ');
                                }
                                title += ' (Tanggal: ' + tanggalDari + ' s/d ' + tanggalSampai + ')';
                                return title;
                            },
                            filename: function() {
                                var tanggalDari = $('#filter-tanggal-dari').val();
                                var tanggalSampai = $('#filter-tanggal-sampai').val();
                                var selectedStatuses = [];
                                $('.filter-status-checkbox:checked').each(function() {
                                    selectedStatuses.push($(this).val());
                                });
                                var filename = 'Data_Job_Order_Prepress_' + tanggalDari + '_' + tanggalSampai;
                                if (selectedStatuses.length > 0) {
                                    filename += '_' + selectedStatuses.join('_');
                                }
                                return filename;
                            },
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], // Include semua kolom kecuali Action (index 11), termasuk PIC (index 8) dan Pending Reason (index 9)
                                format: {
                                    header: function(data, column, row) {
                                        // Custom header untuk kolom PIC (index 8) dan Pending Reason (index 9)
                                        // Urutan kolom: 0=No. Job Order, 1=Tanggal, 2=Deadline, 3=Customer, 4=Product, 5=Job Order, 6=Prioritas, 7=Status Job, 8=PIC, 9=Pending Reason, 10=Created By
                                        if (column === 8) {
                                            return 'PIC';
                                        }
                                        if (column === 9) {
                                            return 'Pending Reason';
                                        }
                                        // Ambil header dari thead jika data kosong
                                        if (!data || data === '') {
                                            var headerCell = $('#datatable-job-order-prepress thead tr th').eq(column);
                                            if (headerCell.length > 0) {
                                                return headerCell.text().trim();
                                            }
                                        }
                                        return data || '';
                                    },
                                    body: function(data, row, column, node) {
                                        // Kolom Status Job (index 7) - hanya ambil status, pisahkan dari PIC
                                        if (column === 7) {
                                            // Prioritas 1: Ambil dari row data (paling reliable, sudah bersih dari HTML)
                                            if (row && row.status_job) {
                                                // Pastikan hanya status, tanpa PIC
                                                var status = row.status_job.toString().trim();
                                                // Hapus "PIC:" jika ada di status
                                                status = status.split('PIC:')[0].trim();
                                                return status;
                                            }
                                            // Prioritas 2: Ambil dari DOM node
                                            if (node) {
                                                var $node = $(node);
                                                if ($node.is('button')) {
                                                    // Ambil hanya text dari button (status), tanpa PIC
                                                    var buttonText = $node.text().trim();
                                                    // Ambil baris pertama saja (status), hapus "PIC:" jika ada
                                                    var status = buttonText.split('\n')[0].split('PIC:')[0].trim();
                                                    // Hapus "ASSIGN", "IN PROGRESS", dll jika ada di akhir
                                                    return status;
                                                }
                                                // Jika bukan button, coba ambil dari cell text
                                                var cellText = $node.text().trim();
                                                return cellText.split('PIC:')[0].trim();
                                            }
                                            // Fallback: ambil dari data parameter
                                            if (data) {
                                                return data.toString().split('PIC:')[0].trim();
                                            }
                                            return '';
                                        }

                                        // Kolom PIC (index 8) - ambil PIC terpisah
                                        if (column === 8) {
                                            // Ambil PIC dari row data (paling reliable)
                                            if (row && row.pic) {
                                                return row.pic;
                                            }
                                            // Fallback: coba ambil dari DOM jika ada
                                            if (node) {
                                                var $node = $(node);
                                                var cellText = $node.text().trim();
                                                // Cari "PIC:" di cell status (kolom sebelumnya)
                                                // Tapi karena ini kolom PIC sendiri, langsung return
                                                return cellText || '-';
                                            }
                                            return data || '-';
                                        }

                                        // Kolom Pending Reason (index 9) - ambil pending reason terpisah
                                        if (column === 9) {
                                            // Ambil pending reason dari row data (paling reliable)
                                            if (row && row.pending_reason) {
                                                return row.pending_reason;
                                            }
                                            // Fallback: coba ambil dari DOM jika ada
                                            if (node) {
                                                var $node = $(node);
                                                var cellText = $node.text().trim();
                                                return cellText || '';
                                            }
                                            return data || '';
                                        }

                                        // Kolom lainnya - format normal
                                        if (node) {
                                            var $node = $(node);
                                            if ($node.is('button')) {
                                                return $node.text().trim().split('\n')[0].trim();
                                            }
                                            return $node.text().trim() || data;
                                        }
                                        return data || '';
                                    }
                                }
                            }
                        }
                    ],
                    columns: [
                        // {
                        //     data: 'DT_RowIndex',
                        //     name: 'DT_RowIndex'
                        // },
                        {
                            data: 'nomor_job_order',
                            name: 'nomor_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
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
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'product',
                            name: 'product'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                console.log(data);
                                return data == 'Urgent' ?
                                    '<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>' :
                                    '<button type="button" class="btn btn-success btn-sm w-100" data-sodocno="${row.id}" data-status="Normal">Normal</button>';
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                        },
                        {
                            data: 'pic',
                            name: 'pic',
                            visible: false, // Hidden di tampilan, tapi tersedia untuk export
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'pending_reason',
                            name: 'pending_reason',
                            visible: false, // Hidden di tampilan, tapi tersedia untuk export
                            render: function(data, type, row) {
                                return data || '';
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            render: function(data, type, row) {
                                return `<span>${data}</span>`;
                            }
                        },
                        // {
                        //     data: 'created_at',
                        //     name: 'created_at',
                        //     render: function(data, type, row) {
                        //         return moment(data).format('DD-MM-YYYY HH:mm:ss');
                        //     }
                        // },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col',
                            // show action button in export excel
                        },
                    ],
                    order: [
                        [8, 'desc'],
                        [1, 'asc']
                    ]
                });
                // State filter pribadi
                window.__laporanPribadi = false;

                // Filter Tanggal: wajib pilih tanggal dulu sebelum load data (tanpa filter pribadi)
                $('#btn-filter-tanggal').on('click', function () {
                    var tanggalDari = $('#filter-tanggal-dari').val();
                    var tanggalSampai = $('#filter-tanggal-sampai').val();

                    if (!tanggalDari || !tanggalSampai) {
                        alert('Silakan pilih Tanggal Dari dan Tanggal Sampai terlebih dahulu!');
                        return;
                    }

                    // Validasi: tanggal dari tidak boleh lebih besar dari tanggal sampai
                    if (new Date(tanggalDari) > new Date(tanggalSampai)) {
                        alert('Tanggal Dari tidak boleh lebih besar dari Tanggal Sampai!');
                        return;
                    }

                    // Set filter pribadi = false (filter semua data)
                    window.__laporanPribadi = false;

                    // Reload DataTables dengan filter tanggal
                    datatable.ajax.reload(null, false);
                });

                // Filter Data Pribadi: filter data yang dibuat oleh user yang sedang login
                $('#btn-filter-pribadi').on('click', function () {
                    var tanggalDari = $('#filter-tanggal-dari').val();
                    var tanggalSampai = $('#filter-tanggal-sampai').val();

                    if (!tanggalDari || !tanggalSampai) {
                        alert('Silakan pilih Tanggal Dari dan Tanggal Sampai terlebih dahulu!');
                        return;
                    }

                    // Validasi: tanggal dari tidak boleh lebih besar dari tanggal sampai
                    if (new Date(tanggalDari) > new Date(tanggalSampai)) {
                        alert('Tanggal Dari tidak boleh lebih besar dari Tanggal Sampai!');
                        return;
                    }

                    // Set filter pribadi = true (hanya data user yang sedang login)
                    window.__laporanPribadi = true;

                    // Reload DataTables dengan filter tanggal + created_by
                    datatable.ajax.reload(null, false);
                });

                // Refresh All: reset filter pribadi, tetap pakai filter tanggal dan status
                $('#btn-refresh-all').on('click', function () {
                    var tanggalDari = $('#filter-tanggal-dari').val();
                    var tanggalSampai = $('#filter-tanggal-sampai').val();

                    if (!tanggalDari || !tanggalSampai) {
                        alert('Silakan pilih Tanggal Dari dan Tanggal Sampai terlebih dahulu!');
                        return;
                    }

                    // Reset filter pribadi
                    window.__laporanPribadi = false;

                    // Reload dengan filter tanggal dan status yang sudah dipilih
                    datatable.ajax.reload(null, false);
                });

                // Handle klik button rejection info
                $(document).on('click', '.rejection-info-btn', function() {
                    var rejectedBy = $(this).data('rejected-by');
                    var rejectedAt = $(this).data('rejected-at');
                    var rejectionReason = $(this).data('rejection-reason');

                    // Isi data ke modal
                    $('#rejected-by-info').text(rejectedBy || '-');
                    $('#rejected-at-info').text(rejectedAt || '-');
                    $('#rejection-reason-info').text(rejectionReason || 'Tidak ada alasan yang dicantumkan');

                    // Tampilkan modal
                    $('#modalRejectionInfo').modal('show');
                });

                // Pilih Semua Status
                $('#btn-select-all-status').on('click', function() {
                    $('.filter-status-checkbox').prop('checked', true);
                });

                // Hapus Semua Status
                $('#btn-unselect-all-status').on('click', function() {
                    $('.filter-status-checkbox').prop('checked', false);
                });

                // Export Excel menggunakan button dari DataTables
                $('#btn-export-excel').on('click', function() {
                    var tanggalDari = $('#filter-tanggal-dari').val();
                    var tanggalSampai = $('#filter-tanggal-sampai').val();

                    if (!tanggalDari || !tanggalSampai) {
                        alert('Silakan pilih Tanggal Dari dan Tanggal Sampai terlebih dahulu!');
                        return;
                    }

                    // Trigger click pada button export Excel dari DataTables
                    // Button akan otomatis menggunakan konfigurasi yang sudah didefinisikan
                    datatable.button('.buttons-excel').trigger();
                });

                // Search box custom - terhubung dengan DataTables search
                $('#datatable-search').on('keyup', function() {
                    datatable.search($(this).val()).draw();
                });

            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/prepress/data-job-order-prepress.blade.php ENDPATH**/ ?>
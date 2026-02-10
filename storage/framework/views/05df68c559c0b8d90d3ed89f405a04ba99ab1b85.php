<?php $__env->startSection('title'); ?>
    Report Form Pengajuan Karyawan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Report Form Pengajuan Karyawan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <?php
            // Check if any filter is applied - define early so it can be used throughout the view
            $hasFilter = request()->hasAny(['status', 'request_type', 'employee_name', 'date_from', 'date_to']);
            // Ensure allData is defined and is a collection
            $allData = $allData ?? collect();
        ?>

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Report Form Pengajuan Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('hr.requests.index')); ?>">Form Pengajuan</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 text-white">
                            <i class="mdi mdi-filter me-2"></i>Filter Report
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo e(route('hr.requests.report')); ?>" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">
                                            <i class="mdi mdi-check-circle me-1"></i>Status
                                        </label>
                                        <select name="status" id="status" class="form-select form-control">
                                            <option value="">Semua Status</option>
                                            <?php if(isset($filterOptions['statuses'])): ?>
                                                <?php $__currentLoopData = $filterOptions['statuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                                        <?php echo e($label); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="request_type">
                                            <i class="mdi mdi-file-document me-1"></i>Modul/Jenis
                                        </label>
                                        <select name="request_type" id="request_type" class="form-select form-control">
                                            <option value="">Semua Modul</option>
                                            <?php if(isset($filterOptions['modules'])): ?>
                                                <?php $__currentLoopData = $filterOptions['modules']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>" <?php echo e(request('request_type') == $key ? 'selected' : ''); ?>>
                                                        <?php echo e($label); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee_name">
                                            <i class="mdi mdi-account me-1"></i>Nama Pemohon
                                        </label>
                                        <input type="text" name="employee_name" id="employee_name" class="form-control"
                                            value="<?php echo e(request('employee_name')); ?>" placeholder="Ketik nama pemohon...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <i class="mdi mdi-calendar me-1"></i>Range Tanggal
                                        </label>
                                        <div class="input-group">
                                            <input type="date" name="date_from" id="date_from" class="form-control"
                                                value="<?php echo e(request('date_from')); ?>" placeholder="Dari">
                                            <span class="input-group-text">s/d</span>
                                            <input type="date" name="date_to" id="date_to" class="form-control"
                                                value="<?php echo e(request('date_to')); ?>" placeholder="Sampai">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            <small>
                                                <i class="mdi mdi-information me-1"></i>
                                                Menampilkan <?php echo e(count($allData ?? [])); ?> data pengajuan
                                            </small>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter me-1"></i>Terapkan Filter
                                            </button>
                                            <a href="<?php echo e(route('hr.requests.report')); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh me-1"></i>Reset
                                            </a>
                                            <button type="button" class="btn btn-info" id="clearFilters">
                                                <i class="mdi mdi-eraser me-1"></i>Hapus Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Data Pengajuan</h4>
                        <div>
                            <?php if($hasFilter && count($allData) > 0): ?>
                                <button class="btn btn-success" onclick="exportToExcel()" id="btnExportExcel">
                                    <i class="mdi mdi-file-excel me-1"></i>Export Excel
                                </button>
                                <button class="btn btn-danger" onclick="exportToPDF()" id="btnExportPdf">
                                    <i class="mdi mdi-file-pdf me-1"></i>Export PDF
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success" onclick="exportToExcel()" id="btnExportExcel" disabled>
                                    <i class="mdi mdi-file-excel me-1"></i>Export Excel
                                </button>
                                <button class="btn btn-danger" onclick="exportToPDF()" id="btnExportPdf" disabled>
                                    <i class="mdi mdi-file-pdf me-1"></i>Export PDF
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(!$hasFilter || count($allData) == 0): ?>
                            <!-- No Filter / No Data Message -->
                            <div class="text-center py-5" id="noFilterMessage">
                                <div class="mb-4">
                                    <i class="mdi mdi-filter-outline" style="font-size: 80px; color: #6c757d;"></i>
                                </div>
                                <h4 class="text-muted mb-3">
                                    <?php if(!$hasFilter): ?>
                                        Silakan Terapkan Filter Terlebih Dahulu
                                    <?php else: ?>
                                        Tidak Ada Data Ditemukan
                                    <?php endif; ?>
                                </h4>
                                <p class="text-muted mb-4">
                                    <?php if(!$hasFilter): ?>
                                        Pilih filter di atas (Status, Modul/Jenis, Nama Pemohon, atau Range Tanggal) untuk menampilkan data pengajuan.
                                    <?php else: ?>
                                        Coba ubah parameter filter untuk mendapatkan data yang diinginkan.
                                    <?php endif; ?>
                                </p>
                                <?php if(!$hasFilter): ?>
                                    <button type="submit" form="filterForm" class="btn btn-primary btn-lg">
                                        <i class="mdi mdi-filter me-2"></i>Terapkan Filter Sekarang
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($hasFilter && count($allData) > 0): ?>
                            <div class="table-responsive" id="tableContainer">
                                <table class="table table-striped table-bordered" id="reportTable">
                                    <thead>
                                        <tr>
                                            <th>No. Pengajuan</th>
                                            <th>Jenis</th>
                                            <th>Pemohon</th>
                                            <th>Status</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No. Pengajuan</th>
                                            <th>Jenis</th>
                                            <th>Pemohon</th>
                                            <th>Status</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php $__currentLoopData = $allData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-primary"><?php echo e($request['request_number']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info" style="color: white;"><?php echo e($request['type_label']); ?></span>
                                                </td>
                                                <td><?php echo e($request['employee_name']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo e($request['status_badge_class']); ?>"><?php echo e($request['status_label']); ?></span>
                                                </td>
                                                <td><?php echo e($request['created_at'] ? $request['created_at']->format('d/m/Y H:i') : '-'); ?></td>

                                                <td>
                                                    <?php if($request['notes']): ?>
                                                        <?php echo e(\Illuminate\Support\Str::limit($request['notes'], 30)); ?>

                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if(isset($request['detail_route'])): ?>
                                                        <a href="<?php echo e($request['detail_route']); ?>" class="btn btn-sm btn-info" target="_blank">
                                                            <i class="mdi mdi-eye me-1"></i>Detail
                                                        </a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('scripts'); ?>
        <!-- DataTables -->
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

        <script>
            $(document).ready(function() {
                // Check if table exists and has data
                <?php if($hasFilter && count($allData) > 0): ?>
                    // Initialize DataTable
                    var table = $('#reportTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: {
                            processing: "Memproses...",
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data per halaman",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            loadingRecords: "Memuat data...",
                            zeroRecords: "Tidak ada data yang ditemukan",
                            emptyTable: "Tidak ada data dalam tabel",
                            paginate: {
                                first: "Pertama",
                                previous: "Sebelumnya",
                                next: "Selanjutnya",
                                last: "Terakhir"
                            },
                            aria: {
                                sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                                sortDescending: ": aktifkan untuk mengurutkan kolom turun"
                            }
                        },
                        order: [[4, 'desc']], // Sort by Tanggal Dibuat descending
                        columnDefs: [
                            { orderable: false, targets: -1 }, // Make last column (Aksi) not sortable
                            { width: '150px', targets: 0 }, // No. Pengajuan width
                            { width: '100px', targets: -1 }, // Aksi width (last column)
                        ],
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                             "<'row'<'col-sm-12'tr>>" +
                             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                    });

                    // Enable export buttons
                    $('#btnExportExcel').prop('disabled', false);
                    $('#btnExportPdf').prop('disabled', false);
                <?php endif; ?>

                // Clear Filters button
                $('#clearFilters').on('click', function() {
                    $('#filterForm')[0].reset();
                    window.location.href = "<?php echo e(route('hr.requests.report')); ?>";
                });
            });

            // Export to Excel
            function exportToExcel() {
                const table = document.getElementById('reportTable');
                
                if (!table) {
                    alert('Tabel tidak ditemukan. Silakan terapkan filter terlebih dahulu.');
                    return;
                }

                try {
                    // Get data without the last column (Aksi)
                    const wb = XLSX.utils.table_to_book(table, {
                        sheet: "Report Pengajuan",
                        raw: true
                    });

                    // Get the worksheet
                    const ws = wb.Sheets["Report Pengajuan"];

                    // Remove the last column (Aksi column)
                    if (ws['!ref']) {
                        const range = XLSX.utils.decode_range(ws['!ref']);
                        if (range.e.c > 0) {
                            range.e.c = range.e.c - 1; // Remove last column
                            ws['!ref'] = XLSX.utils.encode_range(range);
                            
                            // Remove cells in the last column
                            for (let R = range.s.r; R <= range.e.r; ++R) {
                                const cellAddress = XLSX.utils.encode_cell({c: range.e.c + 1, r: R});
                                if (ws[cellAddress]) {
                                    delete ws[cellAddress];
                                }
                            }
                        }
                    }

                    // Generate filename with timestamp
                    const date = new Date();
                    const timestamp = date.toISOString().slice(0,10).replace(/-/g,'');
                    XLSX.writeFile(wb, `Report_Pengajuan_${timestamp}.xlsx`);
                } catch (error) {
                    console.error('Error exporting to Excel:', error);
                    alert('Terjadi kesalahan saat mengekspor ke Excel. Pastikan tabel sudah dimuat dengan benar.');
                }
            }

            // Export to PDF
            function exportToPDF() {
                const table = document.getElementById('reportTable');
                
                if (!table) {
                    alert('Tabel tidak ditemukan. Silakan terapkan filter terlebih dahulu.');
                    return;
                }

                try {
                    if (typeof window.jspdf === 'undefined') {
                        alert('Library jsPDF tidak dimuat. Silakan refresh halaman.');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation

                    // Add title
                    doc.setFontSize(16);
                    doc.text('Report Form Pengajuan Karyawan', 14, 15);

                    doc.setFontSize(10);
                    doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 14, 22);

                    // Get table data
                    const rows = [];

                    // Get header (exclude last column - Aksi)
                    const headers = [];
                    table.querySelectorAll('thead th').forEach((th, index) => {
                        if (index < 6) { // Only take first 6 columns, skip Aksi
                            headers.push(th.innerText.trim());
                        }
                    });

                    // Get body rows (exclude last column - Aksi)
                    table.querySelectorAll('tbody tr').forEach(tr => {
                        const rowData = [];
                        tr.querySelectorAll('td').forEach((td, index) => {
                            if (index < 6) { // Only take first 6 columns, skip Aksi
                                rowData.push(td.innerText.trim());
                            }
                        });
                        if (rowData.length > 0) {
                            rows.push(rowData);
                        }
                    });

                    if (rows.length === 0) {
                        alert('Tidak ada data untuk diekspor.');
                        return;
                    }

                    // Generate table in PDF
                    doc.autoTable({
                        head: [headers],
                        body: rows,
                        startY: 30,
                        styles: {
                            fontSize: 8,
                            cellPadding: 2,
                        },
                        headStyles: {
                            fillColor: [66, 133, 244],
                            textColor: 255,
                            fontStyle: 'bold'
                        },
                        alternateRowStyles: {
                            fillColor: [245, 245, 245]
                        },
                        margin: { top: 30, right: 10, bottom: 10, left: 10 }
                    });

                    // Save PDF
                    const date = new Date();
                    const timestamp = date.toISOString().slice(0,10).replace(/-/g,'');
                    doc.save(`Report_Pengajuan_${timestamp}.pdf`);
                } catch (error) {
                    console.error('Error exporting to PDF:', error);
                    alert('Terjadi kesalahan saat mengekspor ke PDF. Pastikan tabel sudah dimuat dengan benar.');
                }
            }

            // Module Chart (if element exists)
            if (document.getElementById('moduleChart')) {
                const moduleCtx = document.getElementById('moduleChart').getContext('2d');
                const moduleChart = new Chart(moduleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($requestsByModule->keys() ?? []); ?>,
                        datasets: [{
                            data: <?php echo json_encode($requestsByModule->values() ?? []); ?>,
                            backgroundColor: [
                                '#36A2EB',  // Form Karyawan
                                '#FFCE56',  // Data Lembur
                                '#4BC0C0',  // Permintaan Kendaraan
                                '#9966FF'   // Permintaan Inventaris
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Status Chart (if element exists)
            if (document.getElementById('statusChart')) {
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($requestsByStatus->keys() ?? []); ?>,
                        datasets: [{
                            label: 'Jumlah',
                            data: <?php echo json_encode($requestsByStatus->values() ?? []); ?>,
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF',
                                '#FF9F40',
                                '#C9CBCF'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/requests/report.blade.php ENDPATH**/ ?>
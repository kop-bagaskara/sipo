@extends('main.layouts.main')
@section('title')
    Admin - Supplier Arrival Report
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        .bg-info {
            /* background: #667eea ; */
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .filter-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-generate {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .report-summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .summary-item {
            text-align: center;
            padding: 15px;
        }

        .summary-item .number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }

        .summary-item .label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .data-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        /* Export Buttons Styling */
        .dt-buttons {
            margin-bottom: 20px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .dt-buttons .btn i {
            margin-right: 5px;
        }

        /* DataTable Export Buttons */
        .dt-button {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            border: none !important;
            color: white !important;
            padding: 8px 16px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            transition: all 0.3s ease !important;
        }

        .dt-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4) !important;
        }

        .dt-button.buttons-excel {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
        }

        .dt-button.buttons-csv {
            background: linear-gradient(45deg, #17a2b8, #6f42c1) !important;
        }

        .dt-button.buttons-pdf {
            background: linear-gradient(45deg, #dc3545, #e83e8c) !important;
        }

        .dt-button.buttons-print {
            background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
        }

        /* Ensure buttons are visible and aligned */
        .dt-buttons {
            display: inline-block !important;
            margin-bottom: 0 !important;
            vertical-align: middle !important;
        }

        .dt-button {
            display: inline-block !important;
            margin-right: 5px !important;
            margin-bottom: 0 !important;
            vertical-align: middle !important;
        }

        /* Align search and buttons in same row */
        .dataTables_filter {
            display: inline-block !important;
            margin-left: 10px !important;
            vertical-align: middle !important;
        }

        .dataTables_length {
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('page-title')
    Supplier Arrival Report
@endsection

@section('body')

    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Supplier Arrival Report</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.supplier-tickets.index') }}">Supplier Tickets</a>
                    </li>
                    <li class="breadcrumb-item active">Arrival Report</li>
                </ol>
            </div>
        </div>

        <!-- Report Header -->
        <div class="bg-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2" style="color:#fff;">
                        <i class="mdi mdi-truck-delivery mr-2"></i>
                        Laporan Kedatangan Supplier
                    </h4>
                    <p class="mb-0">Laporan kedatangan supplier berdasarkan periode yang dipilih</p>
                </div>
                <div class="col-md-4 text-right">
                    <i class="mdi mdi-chart-line" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                    value="{{ date('Y-m-d', strtotime('-7 days')) }}">

                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                    value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-generate w-100" id="generateReport">
                        <i class="mdi mdi-refresh mr-2"></i>
                        Generate Report
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">
                        <small>Export options tersedia di tabel setelah data dimuat</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        {{-- <div class="report-summary" id="reportSummary" style="display: none;">
            <div class="row">
                <div class="col-md-3">
                    <div class="summary-item">
                        <div class="number" id="totalDeliveries">0</div>
                        <div class="label">Total Kedatangan</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-item">
                        <div class="number" id="uniqueSuppliers">0</div>
                        <div class="label">Supplier Unik</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-item">
                        <div class="number" id="dateRange">-</div>
                        <div class="label">Periode</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-item">
                        <div class="number" id="avgPerDay">0</div>
                        <div class="label">Rata-rata/Hari</div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Data Table -->
        <div class="data-table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="mdi mdi-table mr-2"></i>
                    Data Kedatangan Supplier
                </h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshData">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="arrivalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ticket Number</th>
                            <th>Supplier</th>
                            <th>PO Number</th>
                            <th>Tanggal Kedatangan</th>
                            <th>Jam</th>
                            <th>No. Kendaraan</th>
                            <th>No. Surat Jalan</th>
                            <th>Status</th>
                            <th>GRD Number</th>
                            <th>PQC Number</th>
                            <th>Dibuat Oleh</th>
                            <th>Diproses Oleh</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="arrivalTableBody">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- No Data Message -->
            <div class="no-data" id="noDataMessage" style="display: none;">
                <i class="mdi mdi-information-outline"></i>
                <h5>Belum ada data</h5>
                <p>Pilih periode tanggal dan klik "Generate Report" untuk melihat data kedatangan supplier.</p>
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
    
    <!-- Excel Export Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>
        <script>
            $(document).ready(function() {
                let dataTable = null;
                
                // Initialize empty DataTable to avoid conflicts
                try {
                    dataTable = $('#arrivalTable').DataTable({
                        responsive: true,
                        pageLength: 25,
                        data: [], // Empty data initially
                        columns: [
                            { title: "No" },
                            { title: "Ticket Number" },
                            { title: "Supplier" },
                            { title: "PO Number" },
                            { title: "Tanggal Kedatangan" },
                            { title: "Jam" },
                            { title: "No. Kendaraan" },
                            { title: "No. Surat Jalan" },
                            { title: "Status" },
                            { title: "GRD Number" },
                            { title: "PQC Number" },
                            { title: "Dibuat Oleh" },
                            { title: "Diproses Oleh" },
                            { title: "Action" }
                        ],
                        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-right"fB>>rtip',
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="mdi mdi-file-excel"></i> Excel',
                                className: 'btn btn-secondary',
                                title: 'Laporan Kedatangan Supplier',
                                filename: function() {
                                    const startDate = $('#start_date').val();
                                    const endDate = $('#end_date').val();
                                    return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                                },
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="mdi mdi-file-document"></i> CSV',
                                className: 'btn btn-secondary',
                                title: 'Laporan Kedatangan Supplier',
                                filename: function() {
                                    const startDate = $('#start_date').val();
                                    const endDate = $('#end_date').val();
                                    return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                                },
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                                }
                            },
                            // {
                            //     extend: 'pdf',
                            //     text: '<i class="mdi mdi-file-pdf"></i> PDF',
                            //     className: 'btn btn-secondary',
                            //     title: 'Laporan Kedatangan Supplier',
                            //     filename: function() {
                            //         const startDate = $('#start_date').val();
                            //         const endDate = $('#end_date').val();
                            //         return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                            //     },
                            //     exportOptions: {
                            //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                            //     },
                            //     customize: function(doc) {
                            //         doc.content[1].table.widths = ['5%', '12%', '15%', '12%', '12%', '8%', '10%', '12%', '8%', '10%', '10%', '12%', '12%'];
                            //         doc.styles.tableHeader.fontSize = 8;
                            //         doc.styles.tableBody.fontSize = 7;
                            //     }
                            // },
                        ],
                    });
                    
                    // Debug: Check initial buttons
                    setTimeout(function() {
                        console.log('Initial DataTable buttons count:', $('.dt-buttons .dt-button').length);
                        console.log('Initial buttons:', $('.dt-buttons .dt-button').map(function() { return $(this).text(); }).get());
                    }, 500);
                } catch (error) {
                    console.error('Initial DataTable setup error:', error);
                }

                // Generate Report
                $('#generateReport').on('click', function() {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();

                    if (!startDate || !endDate) {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Silakan pilih tanggal mulai dan tanggal akhir',
                            icon: 'warning'
                        });
                        return;
                    }

                    if (new Date(startDate) > new Date(endDate)) {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
                            icon: 'warning'
                        });
                        return;
                    }

                    loadReportData(startDate, endDate);
                });

                // Load Report Data
                function loadReportData(startDate, endDate) {
                    $('#loadingOverlay').show();
                    $('#reportSummary').hide();
                    $('#noDataMessage').hide();

                    $.ajax({
                        url: '{{ route('admin.supplier-tickets.supplier-arrival-data') }}',
                        method: 'POST',
                        data: {
                            start_date: startDate,
                            end_date: endDate,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                updateReportSummary(response);
                                updateDataTable(response.data);
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Gagal mengambil data',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading report data:', xhr);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal mengambil data: ' + xhr.responseText,
                                icon: 'error'
                            });
                        },
                        complete: function() {
                            $('#loadingOverlay').hide();
                        }
                    });
                }

                // Update Report Summary
                function updateReportSummary(response) {
                    const data = response.data;
                    const uniqueSuppliers = [...new Set(data.map(item => item.supplier_name))].length;
                    const startDate = new Date(response.start_date);
                    const endDate = new Date(response.end_date);
                    const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                    const avgPerDay = daysDiff > 0 ? (data.length / daysDiff).toFixed(1) : 0;

                    $('#totalDeliveries').text(data.length);
                    $('#uniqueSuppliers').text(uniqueSuppliers);
                    $('#dateRange').text(response.start_date + ' - ' + response.end_date);
                    $('#avgPerDay').text(avgPerDay);

                    $('#reportSummary').show();
                }

                // Update Data Table
                function updateDataTable(data) {
                    try {
                        if (data.length === 0) {
                            $('#noDataMessage').show();
                            // Clear existing data
                            if (dataTable) {
                                dataTable.clear().draw();
                            }
                            return;
                        }

                        // Hide no data message
                        $('#noDataMessage').hide();

                        // Prepare data for DataTable
                        const tableData = data.map((item, index) => [
                            index + 1,
                            `<code>${item.ticket_number}</code>`,
                            item.supplier_name,
                            `<code>${item.po_number}</code>`,
                            item.delivery_date,
                            item.delivery_time,
                            item.vehicle_number,
                            item.supplier_delivery_doc,
                            `<span class="status-badge status-completed">${item.status}</span>`,
                            item.grd_number,
                            item.pqc_number,
                            item.created_by,
                            item.processed_by,
                            `<a href="{{ route('admin.supplier-tickets.index') }}/${item.id}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="mdi mdi-eye"></i></a>`
                        ]);

                        // Clear and add new data
                        if (dataTable) {
                            dataTable.clear();
                            dataTable.rows.add(tableData);
                            dataTable.draw();
                        } else {
                            // If no DataTable exists, create one
                            dataTable = $('#arrivalTable').DataTable({
                                responsive: true,
                                pageLength: 25,
                                data: tableData,
                                order: [[4, 'desc']], // Sort by delivery date
                                columnDefs: [{
                                    orderable: false,
                                    targets: [0, 14]
                                }], // Disable sorting for No and Action columns
                                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-right"fB>>rtip',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        text: '<i class="mdi mdi-file-excel"></i> Excel',
                                        className: 'btn btn-secondary',
                                        title: 'Laporan Kedatangan Supplier',
                                        filename: function() {
                                            const startDate = $('#start_date').val();
                                            const endDate = $('#end_date').val();
                                            return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                                        },
                                        exportOptions: {
                                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                                        }
                                    },
                                    {
                                        extend: 'csv',
                                        text: '<i class="mdi mdi-file-document"></i> CSV',
                                        className: 'btn btn-secondary',
                                        title: 'Laporan Kedatangan Supplier',
                                        filename: function() {
                                            const startDate = $('#start_date').val();
                                            const endDate = $('#end_date').val();
                                            return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                                        },
                                        exportOptions: {
                                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                                        }
                                    },
                                    {
                                        extend: 'pdf',
                                        text: '<i class="mdi mdi-file-pdf"></i> PDF',
                                        className: 'btn btn-secondary',
                                        title: 'Laporan Kedatangan Supplier',
                                        filename: function() {
                                            const startDate = $('#start_date').val();
                                            const endDate = $('#end_date').val();
                                            return `Supplier_Arrival_Report_${startDate}_to_${endDate}`;
                                        },
                                        exportOptions: {
                                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Exclude Action column
                                        },
                                        customize: function(doc) {
                                            doc.content[1].table.widths = ['5%', '12%', '15%', '12%', '12%', '8%', '10%', '12%', '8%', '10%', '10%', '12%', '12%'];
                                            doc.styles.tableHeader.fontSize = 8;
                                            doc.styles.tableBody.fontSize = 7;
                                        }
                                    },
                                ],
                            });
                            
                            // Debug: Check if buttons are loaded
                            setTimeout(function() {
                                console.log('DataTable buttons count:', $('.dt-buttons .dt-button').length);
                                console.log('Available buttons:', $('.dt-buttons .dt-button').map(function() { return $(this).text(); }).get());
                            }, 500);
                        }
                    } catch (error) {
                        console.error('DataTable update error:', error);
                    }
                }

                // Refresh Data
                $('#refreshData').on('click', function() {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();

                    if (startDate && endDate) {
                        loadReportData(startDate, endDate);
                    } else {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Silakan pilih periode tanggal terlebih dahulu',
                            icon: 'warning'
                        });
                    }
                });

                // Export functionality is now handled by DataTables buttons

                // Set default date range (last 7 days)
                const today = new Date();
                const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);

                $('#start_date').val(lastWeek.toISOString().split('T')[0]);
                $('#end_date').val(today.toISOString().split('T')[0]);
            });
        </script>
    @endsection

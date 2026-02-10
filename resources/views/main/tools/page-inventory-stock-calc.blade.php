@extends('main.layouts.main')
@section('title')
    Inventory Calculation Stock
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('page-title')
    Inventory Calculation Stock
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Inventory Calculation Stock</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Tools</a></li>
                    <li class="breadcrumb-item active">Inventory Calculation Stock</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="date_range" class="form-label">Date Range</label>
                                    <input type="text" class="form-control" id="date_range" name="date_range">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnSearch" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>

                        <!-- Table to display results -->
                        <div class="table-responsive mt-4">
                            <table id="salesOrderTable" class="table table-bordered nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check ms-0">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th>No</th>
                                        <th>Doc No</th>
                                        <th>Delivery Date</th>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Qty Delivered</th>
                                        <th>Qty Open</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                            <div id="loading-spinner" class="text-center" style="display: none;">
                                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="btnCalculateMaterial" class="btn btn-success">Calculate Material
                                Needs</button>
                            <button type="button" id="btnExportMaterialNeeds" class="btn btn-info ms-2">Export
                                Excel</button>
                        </div>

                        <div class="table-responsive mt-4" id="materialNeedsSection" style="display: none;">
                            <h5>Material Needs Summary</h5>
                            <table id="materialNeedsTable" class="table table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Material Code</th>
                                        <th>Material Name</th>
                                        <th>Needed Quantity</th>
                                        <th>Qty Current Stock</th>
                                        <th>Qty Stock G19PR</th>
                                        <th>Stock Sim</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Material needs data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Bootstrap 5 (if needed for specific features) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Initialize date range picker
                $('#date_range').daterangepicker({
                    opens: 'left',
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                var salesOrderTable = $('#salesOrderTable').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    pageLength: 10,
                    order: [
                        [1, 'asc']
                    ]
                });

                var materialNeedsTable = $('#materialNeedsTable').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    pageLength: 10,
                    order: [
                        [8, 'asc']
                    ], // Order by Earliest Delivery Date (index 8)
                    columns: [{
                            "data": null,
                            "orderable": false,
                            "render": function(data, type, row, meta) {
                                // Accordion control column
                                return '<button class="btn btn-sm btn-link p-0 material-detail-toggle" type="button" data-bs-toggle="collapse"><i class="fa fa-plus-square"></i></button>';
                            }
                        },
                        {
                            "data": "MaterialCode"
                        },
                        {
                            "data": "Name"
                        },
                        {
                            "data": "NeededQuantity",
                            "render": function(data, type, row) {
                                return data.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 4
                                });
                            }
                        },
                        {
                            "data": "CurrentStock",
                            "render": function(data, type, row) {
                                return data.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 4
                                });
                            }
                        },
                        {
                            "data": "StockG19PR",
                            "render": function(data, type, row) {
                                return data.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 4
                                });
                            }
                        },
                        {
                            "data": "StockSim",
                            "render": function(data, type, row) {
                                var needed = row.NeededQuantity || 0;
                                var stockBK = row.CurrentStock || 0;
                                var stockPR = row.StockG19PR || 0;
                                var sisa = needed - stockBK;
                                var sim;
                                if (sisa <= 0) {
                                    sim = stockBK - needed;
                                } else {
                                    sim = (stockBK + stockPR) - needed;
                                }
                                return sim.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 4
                                });
                            }
                        },
                        {
                            "data": "Unit"
                        },
                        {
                            "data": "SourceItems",
                            "visible": false
                        }, // This column will be hidden
                        {
                            "data": "EarliestDeliveryDate",
                            "visible": false,
                            "orderable": true
                        } // Hidden column for sorting
                    ]
                });

                // Handle accordion toggle for material needs table
                $('#materialNeedsTable tbody').on('click', 'td > .material-detail-toggle', function() {
                    var tr = $(this).closest('tr');
                    var row = materialNeedsTable.row(tr);

                    if (row.child.isShown()) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).find('i').removeClass('fa-minus-square').addClass('fa-plus-square');
                    } else {
                        // Open this row
                        var rowData = row.data();
                        var sourceItems = rowData.SourceItems;

                        console.log("Source Items for clicked row:", sourceItems);

                        var detailHtml = '';
                        if (sourceItems && sourceItems.length > 0) {
                            detailHtml +=
                                '<div class="card card-body p-0"><table class="table table-bordered table-sm mb-0"><thead><tr><th>Sales Order Doc No</th><th>Delivery Date</th><th>SO Item</th><th>Needed Item Code</th><th>Needed Quantity for (SO)</th><th>Cumulative Stock Sim</th></tr></thead><tbody>';
                            var stockBK = rowData.CurrentStock || 0;
                            var stockPR = rowData.StockG19PR || 0;
                            var currentRunningStock = stockBK;
                            var currentRunningStockPR = stockPR;
                            sourceItems.forEach(function(item) {
                                var statusText;
                                var statusClass;
                                var needed = item.needed_qty_for_so;
                                if (currentRunningStock >= needed) {
                                    currentRunningStock -= needed;
                                    statusText = 'LUNAS';
                                    statusClass = 'text-success';
                                } else if ((currentRunningStock + currentRunningStockPR) >= needed) {
                                    var kekurangan = needed - currentRunningStock;
                                    currentRunningStockPR -= kekurangan;
                                    currentRunningStock = 0;
                                    statusText = 'LUNAS (ambil G19PR)';
                                    statusClass = 'text-primary';
                                } else {
                                    var totalStock = currentRunningStock + currentRunningStockPR;
                                    var kurang = needed - totalStock;
                                    currentRunningStock = 0;
                                    currentRunningStockPR = 0;
                                    statusText =
                                        `Kurang ${kurang.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 4 })}`;
                                    statusClass = 'text-danger';
                                }
                                detailHtml +=
                                    `<tr><td>${item.doc_no}</td><td>${item.delivery_date || 'N/A'}</td><td>${item.so_item}</td><td>${item.needed_item_code}</td><td>${item.needed_qty_for_so.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 4 })}</td><td><span class="${statusClass}">${statusText}</span></td></tr>`;
                            });
                            detailHtml += '</tbody></table></div>';
                        } else {
                            detailHtml =
                                '<div class="card card-body p-0"><p>No specific Sales Order details available for this material.</p></div>';
                        }

                        row.child(detailHtml).show();
                        tr.addClass('shown');
                        $(this).find('i').removeClass('fa-plus-square').addClass('fa-minus-square');
                    }
                });

                // Select all checkbox functionality
                $('#selectAll').change(function() {
                    $('.row-checkbox').prop('checked', $(this).prop('checked'));
                });

                // Calculate Material Needs button click handler
                $('#btnCalculateMaterial').click(function() {
                    var selectedSalesOrders = [];
                    salesOrderTable.rows().every(function() {
                        var row = this.node();
                        var checkbox = $(row).find('.row-checkbox');
                        if (checkbox.is(':checked')) {
                            var rowData = this.data();
                            // Assuming doc_no is at index 2, item is at index 4, and qty_open is at index 7 (0-indexed)
                            selectedSalesOrders.push({
                                doc_no: rowData[2],
                                del_date: rowData[3],
                                item: rowData[4],
                                qty_open: parseFloat(rowData[7]) || 0,
                                unit: rowData[8],
                            });
                        }
                    });

                    if (selectedSalesOrders.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Selection',
                            text: 'Please select at least one Sales Order to calculate material needs.'
                        });
                        return;
                    }

                    // Show loading spinner
                    $('#loading-spinner').show();
                    $('#materialNeedsSection').hide(); // Hide previous results

                    // Make AJAX request to calculate material needs endpoint
                    $.ajax({
                        url: '{{ route('material.calculate') }}',
                        type: 'POST',
                        data: {
                            sales_orders: selectedSalesOrders
                        },
                        success: function(response) {
                            // Hide loading spinner
                            $('#loading-spinner').hide();

                            if (response.success) {
                                materialNeedsTable.clear(); // Clear existing material needs data

                                if (response.data.length > 0) {
                                    materialNeedsTable.rows.add(response.data).draw();
                                    $('#materialNeedsSection')
                                        .show(); // Show the material needs table
                                } else {
                                    materialNeedsTable.rows.add([])
                                        .draw(); // Add empty data and draw
                                    $('#materialNeedsSection')
                                        .show(); // Show the material needs table even if empty
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message ||
                                        'Failed to calculate material needs'
                                });
                            }
                        },
                        error: function(xhr) {
                            $('#loading-spinner').hide();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while calculating material needs'
                            });
                        }
                    });
                });

                // Search button click handler
                $('#btnSearch').click(function() {
                    var dateRange = $('#date_range').val();
                    var dates = dateRange.split(' - ');
                    var startDate = dates[0];
                    var endDate = dates[1];

                    // Show loading spinner
                    $('#loading-spinner').show();

                    // Make AJAX request to search endpoint
                    $.ajax({
                        url: '{{ route('salesorder.search') }}',
                        type: 'POST',
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            // Hide loading spinner
                            $('#loading-spinner').hide();

                            if (response.success) {
                                // Clear existing table data
                                salesOrderTable.clear();

                                if (response.data.length > 0) {
                                    response.data.forEach(function(item, index) {
                                        salesOrderTable.row.add([
                                            `<div class="form-check ms-0"><input type="checkbox" id="row_cb_${index}" class="form-check-input row-checkbox" data-id="${item.doc_no}"><label class="form-check-label" for="row_cb_${index}"></label></div>`,
                                            index + 1,
                                            item.doc_no,
                                            item.delivery_date,
                                            item.item,
                                            item.quantity,
                                            item.qty_delivered,
                                            item.qty_open,
                                            item.unit,
                                        ]);
                                    });
                                }
                                salesOrderTable.draw();

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to fetch data'
                                });
                            }
                        },
                        error: function(xhr) {
                            $('#loading-spinner').hide();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while fetching data'
                            });
                        }
                    });
                });

                $('#btnExportMaterialNeeds').click(function() {
                    var data = materialNeedsTable.rows().data().toArray();
                    $.ajax({
                        url: '{{ route('material.needs.export') }}',
                        type: 'POST',
                        data: {
                            data: data,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = 'material_needs_export.xlsx';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Export Error',
                                text: 'Failed to export Excel.'
                            });
                        }
                    });
                });
            });
        </script>
    @endsection

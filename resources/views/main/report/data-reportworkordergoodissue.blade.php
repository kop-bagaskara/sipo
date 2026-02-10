@extends('main.layouts.main')
@section('title')
    Report Work Order Good Issue
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <style>
        .table-responsive {
            margin-top: 20px;
        }

        .table th {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            padding: 12px 8px;
            font-weight: bold;
            color: #5a5c69;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            border: 1px solid #e3e6f0;
            padding: 10px 8px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .percentage-cell {
            background-color: #d4edda;
            font-weight: bold;
            color: #155724;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #858796;
        }

        .loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #858796;
            font-style: italic;
        }

        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }
    </style>
@endsection
@section('page-title')
    Report Work Order Good Issue
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Report Work Order Good Issue</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Data</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Report Work Order Good Issue</h5>
                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="report_start_date">Tanggal Mulai:</label>
                                <input type="date" id="report_start_date" class="form-control"
                                    value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="report_end_date">Tanggal Akhir:</label>
                                <input type="date" id="report_end_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-info" id="load_report">
                                        <i class="mdi mdi-refresh"></i> Tampilkan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="workorder-good-issue-table"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">WO Doc</th>
                                        <th style="width: 15%">SO Doc</th>
                                        <th style="width: 14%">Material Code</th>
                                        <th style="width: 20%">Material Name</th>
                                        <th style="width: 10%">QTY WO</th>
                                        <th style="width: 10%">GI Doc</th>
                                        <th style="width: 10%">Qty GI</th>
                                        <th style="width: 10%">Unit</th>
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
    @endsection
    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var table = null;

                // Initialize DataTable
                function initializeDataTable() {
                    if (table) {
                        table.destroy();
                    }

                    table = $('#workorder-good-issue-table').DataTable({
                        processing: true,
                        serverSide: false,
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: {
                            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                        },
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="mdi mdi-file-excel"></i> Export Excel',
                                className: 'btn btn-info text-white',
                                title: 'Report Work Order Good Issue',
                                filename: function() {
                                    var startDate = $('#report_start_date').val();
                                    var endDate = $('#report_end_date').val();
                                    return 'Report_WO_GoodIssue_' + startDate + '_to_' + endDate;
                                },
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="mdi mdi-file-pdf"></i> Export PDF',
                                className: 'btn btn-info text-white',
                                title: 'Report Work Order Good Issue',
                                filename: function() {
                                    var startDate = $('#report_start_date').val();
                                    var endDate = $('#report_end_date').val();
                                    return 'Report_WO_GoodIssue_' + startDate + '_to_' + endDate;
                                },
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="mdi mdi-printer"></i> Print',
                                className: 'btn btn-info text-white',
                                title: 'Report Work Order Good Issue',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                }
                            }
                        ],
                        columnDefs: [
                            { 
                                targets: 0, 
                                className: 'text-center',
                                orderable: false
                            },
                            { 
                                targets: [1, 2, 6], 
                                className: 'text-center',
                                style: 'white-space: nowrap;'
                            },
                            { 
                                targets: 7, 
                                className: 'text-right'
                            }
                        ],
                        order: [[1, 'asc']]
                    });
                }

                // Load report when button is clicked
                $(document).on('click', '#load_report', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate && !endDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih tanggal mulai atau tanggal akhir terlebih dahulu!'
                        });
                        return;
                    }

                    loadWorkOrderGoodIssueData();
                });

                function loadWorkOrderGoodIssueData() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    // Show loading
                    if (table) {
                        table.clear().draw();
                    }

                    $.ajax({
                        url: "{{ route('report.work-order-good-issue.data') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            console.log('Response received:', response);

                            var data = [];
                            if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                                data = response.data;
                            } else if (response && Array.isArray(response) && response.length > 0) {
                                data = response;
                            }

                            if (data.length > 0) {
                                console.log('Data found:', data.length, 'items');
                                
                                // Initialize DataTable if not already done
                                if (!table) {
                                    initializeDataTable();
                                }

                                // Clear and populate table
                                table.clear();
                                
                                data.forEach(function(item, index) {
                                    table.row.add([
                                        index + 1,
                                        item.DocNo || '-',
                                        item.SODocNo || '-',
                                        item.MaterialCode || '-',
                                        item.material_name || '-',
                                        item.Qty || '-',
                                        item.GoodsIssueDocNo || 'Belum Ada Goods Issue',
                                        parseFloat(item.GoodsIssueQty),
                                        item.Unit || '-'
                                    ]);
                                });

                                table.draw();
                            } else {
                                console.log('No data in response');
                                
                                // Initialize DataTable if not already done
                                if (!table) {
                                    initializeDataTable();
                                }
                                
                                table.clear().draw();
                                
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Informasi',
                                    text: 'Tidak ada data untuk periode yang dipilih'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memuat data'
                            });
                        }
                    });
                }

                // Initialize empty DataTable on page load
                initializeDataTable();
            });
        </script>
    @endsection

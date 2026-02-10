@extends('main.layouts.main')
@section('title')
    Report Work Order Percentage
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

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
    </style>
@endsection
@section('page-title')
    Report Work Order Percentage
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Report Work Order Percentage</h3>
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
                        <h5 class="card-title">Report Work Order Percentage</h5>
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
                                    <button type="button" class="btn btn-primary" id="load_report">
                                        <i class="mdi mdi-refresh"></i> Tampilkan
                                    </button>

                                    <button type="button" class="btn btn-success" id="export_excel">
                                        <i class="mdi mdi-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="workorder-percentage-table"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">Work Order Doc</th>
                                        <th style="width: 14%">Material Code</th>
                                        <th style="width: 20%">Material Name</th>
                                        <th style="width: 10%">JOS</th>
                                        <th style="width: 10%">Target PTG</th>
                                        <th style="width: 10%; white-space: nowrap;">Output Finished</th>
                                        <th style="width: 10%">Up</th>
                                        <th style="width: 10%">Unit</th>
                                        <th style="width: 10%;white-space: nowrap;">Percentage (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <tr>
                                        <td colspan="9" class="no-data">Silakan pilih tanggal dan klik tombol "Tampilkan"
                                            untuk memuat data</td>
                                    </tr>
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

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Load report when button is clicked
                $(document).on('click', '#load_report', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate && !endDate) {
                        alert('Silakan pilih tanggal mulai atau tanggal akhir terlebih dahulu!');
                        return;
                    }

                    loadWorkOrderPercentageData();
                });

                // Export Excel when button is clicked
                $(document).on('click', '#export_excel', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate && !endDate) {
                        alert('Silakan pilih tanggal mulai atau tanggal akhir terlebih dahulu!');
                        return;
                    }

                    exportWorkOrderPercentageToExcel();
                });

                function loadWorkOrderPercentageData() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    // Show loading
                    $('#table-body').html(
                        '<tr><td colspan="9" class="loading"><i class="mdi mdi-loading mdi-spin"></i> Memuat data...</td></tr>'
                        );

                    $.ajax({
                        url: "{{ route('report.work-order-percentage.data') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            console.log('Response received:', response);

                            if (response && response.data && Array.isArray(response.data) && response.data
                                .length > 0) {
                                console.log('Data found:', response.data.length, 'items');
                                renderTableData(response.data);
                            } else if (response && Array.isArray(response) && response.length > 0) {
                                console.log('Direct array response:', response.length, 'items');
                                renderTableData(response);
                            } else {
                                console.log('No data in response');
                                $('#table-body').html(
                                    '<tr><td colspan="9" class="no-data">Tidak ada data untuk periode yang dipilih</td></tr>'
                                    );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);
                            $('#table-body').html(
                                '<tr><td colspan="9" class="no-data">Terjadi kesalahan saat memuat data</td></tr>'
                                );
                        }
                    });
                }

                function renderTableData(data) {
                    console.log('Rendering data:', data);

                    var html = '';
                    var rowIndex = 1;

                    data.forEach(function(item, index) {
                        console.log('Processing item:', index, item);

                        html += '<tr>';

                        // Row number
                        html += '<td class="text-center">' + rowIndex + '</td>';

                        // Work Order Doc
                        html += '<td style="white-space: nowrap;">' + (item.work_order_doc || '-') + '</td>';

                        // JOS


                        // Material Code
                        html += '<td>' + (item.material_code || '-') + '</td>';

                        // Material Name
                        html += '<td>' + (item.material_name || '-') + '</td>';

                        html += '<td>' + (item.jos || '-') + '</td>';

                        // Total Qty WIP.PTG
                        // html += '<td class="text-right">' + (item.total_qty_wip_ptg ? parseFloat(item.total_qty_wip_ptg).toLocaleString('id-ID') : '0') + '</td>';

                        // Total Qty WIP.PTG Adjusted
                        html += '<td class="text-right">' + (item.total_qty_wip_ptg_adjusted ? parseFloat(item
                            .total_qty_wip_ptg_adjusted).toLocaleString('id-ID') : '0') + '</td>';

                        // Total Qty Finished
                        html += '<td class="text-right">' + (item.total_qty_finished ? parseFloat(item
                            .total_qty_finished).toLocaleString('id-ID') : '0') + '</td>';

                        // UP (zupcetak)
                        html += '<td class="text-center">' + (item.zupcetak || '0') + '</td>';

                        // Unit
                        html += '<td class="text-center">' + (item.unit || '-') + '</td>';

                        // Percentage
                        var percentageValue = item.percentage ? parseFloat(item.percentage) : 0;
                        var percentageClass = percentageValue > 100 ?
                            'percentage-cell text-center text-danger' : 'percentage-cell text-center';
                        html += '<td class="' + percentageClass + '">' + (percentageValue > 0 ? percentageValue
                            .toFixed(2) + '%' : '0%') + '</td>';


                        html += '</tr>';
                        rowIndex++;
                    });

                    console.log('Generated HTML length:', html.length);
                    $('#table-body').html(html);
                }

                function exportWorkOrderPercentageToExcel() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    // Buat form untuk POST request
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': "{{ route('report.work-order-percentage.export') }}"
                    });

                    // Tambahkan CSRF token
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': $('meta[name="csrf-token"]').attr('content')
                    }));

                    // Tambahkan tanggal
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'start_date',
                        'value': startDate
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'end_date',
                        'value': endDate
                    }));

                    // Tambahkan form ke body dan submit
                    $('body').append(form);
                    form.submit();
                    form.remove();
                }
            });
        </script>
    @endsection

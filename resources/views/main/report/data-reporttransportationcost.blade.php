@extends('main.layouts.main')
@section('title')
    Data Plan
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

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

        .plat-nomor-cell {
            background-color: #f8f9fc;
            font-weight: bold;
            color: #4e73df;
        }

        .total-cost-cell {
            background-color: #d4edda;
            font-weight: bold;
            color: #155724;
            text-align: right;
        }

        .rupiah-per-kg-cell {
            background-color: #fff3cd;
            font-weight: bold;
            color: #856404;
            text-align: right;
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
    Data Plan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Report Transportation Cost</h3>
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
                        <h5 class="card-title">Report Transportation Cost</h5>
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
                            <table class="table table-bordered" id="transportation-cost-table" style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">Plat Nomor</th>
                                        <th style="width: 15%">PCN DocNo</th>
                                        <th style="width: 15%">DocNo</th>
                                        <th style="width: 15%">Customer</th>
                                        <th style="width: 15%">Material</th>
                                        <th style="width: 10%">Qty (KG)</th>
                                        <th style="width: 10%">Tanggal</th>
                                        <th style="width: 10%; white-space: nowrap;" >Total Cost (Rp)</th>
                                        <th style="width: 10%; white-space: nowrap;">Rupiah per KG</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <tr>
                                        <td colspan="10" class="no-data">Silakan pilih tanggal dan klik tombol "Tampilkan" untuk memuat data</td>
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

                    loadTransportationCostData();
                });

                // Export Excel when button is clicked
                $(document).on('click', '#export_excel', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate && !endDate) {
                        alert('Silakan pilih tanggal mulai atau tanggal akhir terlebih dahulu!');
                        return;
                    }

                    exportTransportationCostToExcel();
                });

                function loadTransportationCostData() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    // Show loading
                    $('#table-body').html('<tr><td colspan="10" class="loading"><i class="mdi mdi-loading mdi-spin"></i> Memuat data...</td></tr>');

                    $.ajax({
                        url: "{{ route('report.transportation-cost.data') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            console.log('Response received:', response); // Debug log

                            // Check if response has data property
                            if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                                console.log('Data found:', response.data.length, 'items'); // Debug log
                                renderTableData(response.data);
                            } else if (response && Array.isArray(response) && response.length > 0) {
                                // If response is directly an array
                                console.log('Direct array response:', response.length, 'items'); // Debug log
                                renderTableData(response);
                            } else {
                                console.log('No data in response'); // Debug log
                                $('#table-body').html('<tr><td colspan="10" class="no-data">Tidak ada data untuk periode yang dipilih</td></tr>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText); // Debug log
                            $('#table-body').html('<tr><td colspan="10" class="no-data">Terjadi kesalahan saat memuat data</td></tr>');
                        }
                    });
                }

                function renderTableData(data) {
                    console.log('Rendering data:', data); // Debug log

                    var html = '';
                    var rowIndex = 1;
                    var currentPlat = '';
                    var currentPCN = '';
                    var currentCost = '';
                    var platRowspan = 0;
                    var pcnRowspan = 0;
                    var costRowspan = 0;
                    var rupiahPerKgRowspan = 0;

                    // Hitung rowspan untuk setiap item
                    data.forEach(function(item, index) {
                        // Hitung rowspan untuk plat nomor
                        if (index === 0 || data[index - 1].plat_nomor !== item.plat_nomor) {
                            platRowspan = 1;
                            for (var i = index + 1; i < data.length; i++) {
                                if (data[i].plat_nomor === item.plat_nomor) {
                                    platRowspan++;
                                    } else {
                                    break;
                                }
                            }
                            item._platRowspan = platRowspan;
                                    } else {
                            item._platRowspan = 0;
                        }

                        // Hitung rowspan untuk PCN DocNo
                        if (index === 0 || data[index - 1].purchase_cost_doc !== item.purchase_cost_doc) {
                            pcnRowspan = 1;
                            for (var i = index + 1; i < data.length; i++) {
                                if (data[i].purchase_cost_doc === item.purchase_cost_doc) {
                                    pcnRowspan++;
                                } else {
                                    break;
                                }
                            }
                            item._pcnRowspan = pcnRowspan;
                        } else {
                            item._pcnRowspan = 0;
                        }

                        // Hitung rowspan untuk cost (berdasarkan plat nomor + cost)
                        var costKey = item.plat_nomor + '|' + item.cost;
                        if (index === 0 || (data[index - 1].plat_nomor + '|' + data[index - 1].cost) !== costKey) {
                            costRowspan = 1;
                            for (var i = index + 1; i < data.length; i++) {
                                if (data[i].plat_nomor === item.plat_nomor && data[i].cost === item.cost) {
                                    costRowspan++;
                                } else {
                                    break;
                                }
                            }
                            item._costRowspan = costRowspan;
                        } else {
                            item._costRowspan = 0;
                        }

                        // Rupiah per KG sama dengan plat rowspan
                        item._rupiahRowspan = item._platRowspan;
                    });

                    // Render table
                    data.forEach(function(item, index) {
                        console.log('Processing item:', index, item); // Debug log

                        html += '<tr>';

                        // Row number
                        html += '<td class="text-center">' + rowIndex + '</td>';

                        // Plat Nomor - dengan rowspan untuk merge
                        if (item._platRowspan > 0) {
                            html += '<td class="plat-nomor-cell text-center" rowspan="' + item._platRowspan + '">' + (item.plat_nomor || '-') + '</td>';
                        }

                        // Purchase Cost Doc - dengan rowspan untuk merge
                        if (item._pcnRowspan > 0) {
                            html += '<td rowspan="' + item._pcnRowspan + '">' + (item.purchase_cost_doc || '-') + '</td>';
                        }

                        // Goods Issue Doc
                        html += '<td>' + (item.goods_issue_doc || '-') + '</td>';

                        // Customer - kosong dulu
                        html += '<td>' + (item.shipto || '-') + '</td>';

                        // Material - kosong dulu
                        html += '<td>' + (item.material || '-') + '</td>';

                        // Total Qty
                        html += '<td class="text-right">' + (item.qty_kg ? parseFloat(item.qty_kg).toLocaleString('id-ID') + ' KG' : '0 KG') + '</td>';

                        // Tanggal
                        html += '<td class="text-center">' + (item.doc_date ? moment(item.doc_date).format('DD-MM-YYYY') : '-') + '</td>';

                        // Cost - dengan rowspan untuk merge
                        if (item._costRowspan > 0) {
                            html += '<td class="total-cost-cell text-right" rowspan="' + item._costRowspan + '">Rp ' + (item.cost ? parseFloat(item.cost).toLocaleString('id-ID') : '0') + '</td>';
                        }

                        // Rupiah per KG - di-merge berdasarkan plat nomor
                        if (item._rupiahRowspan > 0) {
                            // Hitung total qty untuk plat nomor ini
                            var totalQtyForPlat = 0;
                            for (var j = 0; j < data.length; j++) {
                                if (data[j].plat_nomor === item.plat_nomor) {
                                    totalQtyForPlat += parseFloat(data[j].qty_kg || 0);
                                }
                            }

                            // Hitung rupiah per KG (total cost / total qty per plat)
                            var rupiahPerKg = totalQtyForPlat > 0 ? (parseFloat(item.cost || 0) / totalQtyForPlat) : 0;

                            html += '<td class="rupiah-per-kg-cell text-right" rowspan="' + item._rupiahRowspan + '">Rp ' + (rupiahPerKg ? parseFloat(rupiahPerKg).toLocaleString('id-ID') : '0') + '</td>';
                        }

                        html += '</tr>';
                        rowIndex++;
                    });

                    console.log('Generated HTML length:', html.length); // Debug log
                    $('#table-body').html(html);
                }

                function exportTransportationCostToExcel() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    // Buat form untuk POST request
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': "{{ route('report.transportation-cost.export') }}"
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

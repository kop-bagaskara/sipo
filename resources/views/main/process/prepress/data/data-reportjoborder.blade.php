@extends('main.layouts.main')
@section('title')
    Data Plan
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

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

        /* Export Label Styles */
        .export-label {
            display: inline-block;
            margin-right: 10px;
            font-weight: bold;
            color: #5a5c69;
            font-size: 14px;
            vertical-align: middle;
        }

        .dt-buttons {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Fix alignment for DataTable elements */
        .dataTables_wrapper .row:first-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .row:first-child .col-sm-12:first-child {
            display: flex;
            align-items: center;
        }

        .dataTables_filter {
            display: flex;
            align-items: center;
        }

        /* Ensure proper vertical alignment */
        .dataTables_wrapper .row:first-child .col-sm-6 {
            display: flex;
            align-items: center;
        }

        .dataTables_wrapper .row:first-child .col-sm-6:first-child {
            justify-content: flex-start;
        }

        .dataTables_wrapper .row:first-child .col-sm-6:last-child {
            justify-content: flex-end;
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

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                <h3 class="text-themecolor">Report Job Prepress</h3>
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
                        <h5 class="card-title">Report Job Prepress (APPROVED/FINISH)</h5>
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
                            <div class="col-md-2">
                                <label for="report_end_date">Opsi Data</label>
                                <select name="report_type" id="report_type" class="form-control">
                                    <option value="wo">Report WO</option>
                                    <option value="job">Report Job</option>
                                </select>
                            </div>
                            <div class="col" style="margin-left:200px;">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="load_report">
                                    <i class="mdi mdi-refresh"></i> Tampilkan
                                </button>
                            </div>

                            <div class="col w-50">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-success btn-block" id="export_excel">
                                    <i class="mdi mdi-file-excel"></i> Export Excel
                                </button>
                            </div>



                        </div>
                        <div class="row">

                        </div>

                        <div class="table-responsive">
                            <table id="datatable-report-prepress" class="table table-striped table-bordered"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>PIC</th>
                                        <th>Kode</th>
                                        <th>Job Rate</th>
                                        <th>Point</th>
                                        <th>Sub Unit Job</th>
                                        <th style="white-space: nowrap;">Tanggal Job</th>
                                        <th>Deadline</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th style="white-space: nowrap;">Kode Design</th>
                                        <th style="white-space: nowrap;">Dimension</th>
                                        <th style="white-space: nowrap;">Material</th>
                                        <th style="white-space: nowrap;">Job Order</th>
                                        <th style="white-space: nowrap;">Qty Order</th>
                                        <th style="white-space: nowrap;">Status</th>
                                        <th style="white-space: nowrap;">Prioritas</th>
                                        <th style="white-space: nowrap;">In Progress At</th>
                                        <th style="white-space: nowrap;">Finish At</th>
                                        <th style="white-space: nowrap;">Estimasi (Menit)</th>
                                        <th style="white-space: nowrap;">Received At</th>
                                        <th style="white-space: nowrap;">Received By</th>
                                        <th style="white-space: nowrap;">Created By</th>
                                        <th style="white-space: nowrap;">Created At</th>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>

        <script>
            // Set locale Indonesia secara global
            moment.locale('id');
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var reportDatatable = $('#datatable-report-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: 0,
                    ajax: {
                        url: "{{ route('prepress.report.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.start_date = $('#report_start_date').val();
                            d.end_date = $('#report_end_date').val();
                            d.report_type = $('#report_type').val();
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTable Error:', error);
                            console.error('Response:', xhr.responseText);
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'in_progress',
                            name: 'hari',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('dddd');
                                }
                                return data;
                            },
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'pic',
                            name: 'pic',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'kode',
                            name: 'kode',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'job_rate',
                            name: 'job_rate',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'point',
                            name: 'point',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'sub_unit_job',
                            name: 'sub_unit_job',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY');
                                }
                                return data;
                            },
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY');
                                }
                                return data;
                            },
                            searchable: true,
                            orderable: true
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
                            data: 'kode_design',
                            name: 'kode_design'
                        },
                        {
                            data: 'dimension',
                            name: 'dimension'
                        },
                        {
                            data: 'material',
                            name: 'material'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'in_progress',
                            name: 'in_progress',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                                }
                                return data;
                            },
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'finish',
                            name: 'finish',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                                }
                                return data;
                            },
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'estimated_time',
                            name: 'estimated_time',
                            render: function(data, type, row) {
                                return data ? data : '-';
                            }
                        },
                        {
                            data: 'received_at',
                            name: 'received_at',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                                }
                                return data;
                            },
                            searchable: true,
                            orderable: true
                        },
                        {
                            data: 'received_by',
                            name: 'received_by',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'display' || type === 'type') {
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                                }
                                return data;
                            },
                            searchable: true,
                            orderable: true
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>' +
                         '<"row"<"col-sm-12"rt>>' +
                         '<"row"<"col-sm-12"ip>>',
                    buttons: [
                        {
                            extend: 'copy',
                            text: 'Copy',
                            title: 'Data Report Job Order Prepress',
                            className: 'btn btn-secondary btn-sm'
                        },
                        {
                            extend: 'csv',
                            text: 'CSV',
                            title: 'Data Report Job Order Prepress',
                            className: 'btn btn-secondary btn-sm'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            title: 'Data Report Job Order Prepress',
                            className: 'btn btn-secondary btn-sm'
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            title: 'Data Report Job Order Prepress',
                            className: 'btn btn-secondary btn-sm'
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            title: 'Data Report Job Order Prepress',
                            className: 'btn btn-secondary btn-sm'
                        }
                    ],
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ]
                });

                // Add export label to buttons
                setTimeout(function() {
                    var buttonsContainer = $('.dt-buttons');
                    if (buttonsContainer.length > 0) {
                        var label = $('<span class="export-label">Export To : </span>');
                        buttonsContainer.prepend(label);
                    }
                }, 200);

                // Load report when button is clicked
                $(document).on('click', '#load_report', function() {
                    reportDatatable.ajax.reload();
                });

                // Export Excel functionality
                $(document).on('click', '#export_excel', function() {
                    var startDate = $('#report_start_date').val();
                    var endDate = $('#report_end_date').val();

                    if (!startDate || !endDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Tanggal!',
                            text: 'Silakan pilih tanggal mulai dan tanggal akhir untuk export.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    // Create download link
                    var url = "{{ route('prepress.report.export') }}";
                    var params = new URLSearchParams({
                        start_date: startDate,
                        end_date: endDate,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    });

                    // Create temporary form and submit
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.target = '_blank';

                    var tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = $('meta[name="csrf-token"]').attr('content');
                    form.appendChild(tokenInput);

                    var startDateInput = document.createElement('input');
                    startDateInput.type = 'hidden';
                    startDateInput.name = 'start_date';
                    startDateInput.value = startDate;
                    form.appendChild(startDateInput);

                    var endDateInput = document.createElement('input');
                    endDateInput.type = 'hidden';
                    endDateInput.name = 'end_date';
                    endDateInput.value = endDate;
                    form.appendChild(endDateInput);

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });

            });
        </script>
    @endsection

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

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
                <h3 class="text-themecolor">Plan Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Plan Prepress</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col">

                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p>Pilih Tanggal Plan : </p>
                                <input type="date" name="selected_date" id="selected_date" class="form-control mb-3"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <p>&nbsp;</p>
                                <button type="button" class="btn btn-primary" id="submit-plan-prepress">Buat
                                    Plan</button>
                                <button type="button" class="btn btn-success ml-2" id="export-plan-prepress">
                                    <i class="mdi mdi-file-excel mr-1"></i>
                                    Export Excel
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="datatable-job-order-prepress-for-plan" class="table table-responsive-md"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>No. Job Order</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th style="white-space: nowrap;">Qty Order</th>
                                        <th style="white-space: nowrap;">Job Order</th>
                                        <th>Data</th>
                                        <th>Prioritas</th>
                                        <th>Status Job</th>
                                        <th style="white-space: nowrap;">Created By</th>
                                        <th style="white-space: nowrap;">Created At</th>
                                        {{-- <th style="width:10%;">Action</th> --}}
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

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-job-order-prepress-for-plan').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.job-order-plan.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            // checkbox
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" name="select-job-order-ppic[]" class="select-job-order" value="' +
                                    full.id + '" id="select-job-order-' + full.id +
                                    '"><label for="select-job-order-' + full.id + '">&nbsp;</label>';
                            }
                        },
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
                            name: 'customer',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'product',
                            name: 'product',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'file_data',
                            name: 'file_data',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                let files = [];
                                if (typeof data === 'string') {
                                    try {
                                        files = JSON.parse(data);
                                    } catch (e) {
                                        files = [];
                                    }
                                } else if (Array.isArray(data)) {
                                    files = data;
                                }
                                if (!files.length) return '-';
                                // Tampilkan sebagai list
                                return '<ul style="padding-left:18px; margin-bottom:0;">' + files.map(
                                    f => `<li>${f}</li>`).join('') + '</ul>';
                            },
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                console.log(data);
                                return data == 'Urgent' ?
                                    '<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>' :
                                    '<button type="button" class="btn btn-success btn-sm" data-sodocno="${row.id}" data-status="Normal">Normal</button>';
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return `<button type="button" class="btn btn-info btn-sm" data-sodocno="${row.id}" data-status="${data}">${data}</button>`;
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                    ],
                    order: [
                        [8, 'desc'],
                        [1, 'asc']
                    ]
                });


                $(document).on('click', '#submit-plan-prepress', function() {
                    // Ambil semua checkbox yang dicentang
                    let selectedIds = [];
                    $('#datatable-job-order-prepress-for-plan tbody input.select-job-order:checked').each(
                        function() {
                            selectedIds.push($(this).val());
                        });

                    let selectedDate = $('#selected_date').val();

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih data!',
                            text: 'Silakan centang minimal satu job order untuk membuat plan.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    $.ajax({
                        url: "{{ route('prepress.job-order.submit-plan') }}",
                        type: "POST",
                        data: {
                            selected_ids: selectedIds,
                            selected_date: selectedDate,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // Refresh datatable jika perlu
                            $('#datatable-job-order-prepress-for-plan').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                // Export Excel functionality
                $(document).on('click', '#export-plan-prepress', function() {
                    let selectedDate = $('#selected_date').val();

                    if (!selectedDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih tanggal!',
                            text: 'Silakan pilih tanggal plan terlebih dahulu.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Menyiapkan Export...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create form untuk download
                    let form = $('<form>', {
                        'method': 'POST',
                        'action': "{{ route('prepress.job-order.export-plan') }}"
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'selected_date',
                        'value': selectedDate
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': $('meta[name="csrf-token"]').attr('content')
                    }));

                    $('body').append(form);
                    form.submit();
                    form.remove();

                    // Hide loading after a moment
                    setTimeout(() => {
                        Swal.close();
                    }, 2000);
                });


            });
        </script>
    @endsection

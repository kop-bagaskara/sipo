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
            width: 200px;
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
            width: 200px;
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
                <h3 class="text-themecolor">Data Task Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Task</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="timeline_date">Filter Tanggal (Opsional):</label>
                                <input type="date" id="timeline_date" class="form-control"
                                    placeholder="Kosongkan untuk tampilkan semua">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="load_timeline">
                                    <i class="mdi mdi-refresh"></i> Load Timeline
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-secondary btn-block" id="clear_filter">
                                    <i class="mdi mdi-close"></i> Clear Filter
                                </button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Timeline Project Prepress</h5>
                                <div id="timeline_container">
                                    <div class="timeline-wrapper">
                                        <div class="timeline-header">
                                            <div class="timeline-time-header">Waktu</div>
                                            <div class="timeline-project-header">Project</div>
                                            <div class="timeline-status-header">Status</div>
                                        </div>
                                        <div id="timeline_content" class="timeline-content">
                                            <!-- Timeline items will be loaded here -->
                                        </div>
                                    </div>
                                </div>
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

                // Timeline functionality
                function loadTimeline() {
                    var selectedDate = $('#timeline_date').val();
                    var timelineContent = $('#timeline_content');

                    // Show loading
                    timelineContent.html(
                        '<div class="timeline-loading"><i class="mdi mdi-loading mdi-spin"></i> Loading timeline...</div>'
                    );

                    $.ajax({
                        url: "{{ route('prepress.timeline.data') }}",
                        type: "POST",
                        data: {
                            date: selectedDate || null,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                renderTimeline(response.data);
                            } else {
                                timelineContent.html(
                                    '<div class="timeline-empty">Tidak ada data untuk tanggal yang dipilih</div>'
                                );
                            }
                        },
                        error: function() {
                            timelineContent.html(
                                '<div class="timeline-empty">Error loading timeline data</div>');
                        }
                    });
                }

                function renderTimeline(data) {
                    var timelineContent = $('#timeline_content');

                    if (!data || data.length === 0) {
                        timelineContent.html('<div class="timeline-empty">Tidak ada data untuk ditampilkan</div>');
                        return;
                    }

                    var html = '';

                    data.forEach(function(item) {
                        console.log('item', item);
                        var startTime = moment(item.start_time).format('HH:mm');
                        var endTime = moment(item.end_time).format('HH:mm');
                        var jobDate = moment(item.tanggal_job_order).format('DD/MM/YYYY');
                        var statusClass = getStatusClass(item.status_job);
                        var statusText = getStatusText(item.status_job);

                        html += `
                            <div class="timeline-item">
                                <div class="timeline-time">
                                    <div class="timeline-time-start">${startTime} - ${endTime}</div>
                                    <div class="timeline-time-end">${item.duration} menit</div>
                                    <div class="timeline-time-date">${jobDate}</div>
                                </div>
                                <div class="timeline-project">
                                    <div class="timeline-project-title">${item.kode_design} - ${item.product}</div>
                                    <div class="timeline-project-details">
                                        <span class="timeline-project-customer">${item.customer}</span> |
                                        Job Order: ${item.job_order} |
                                        Qty: ${item.qty_order_estimation}
                                    </div>
                                </div>
                                <div class="timeline-status">
                                    <button type="button" class="btn ${statusClass}">${statusText}</button>
                                </div>
                            </div>
                        `;
                    });

                    timelineContent.html(html);
                }

                function getStatusClass(status) {
                    switch (status) {
                        case 'ASSIGNED':
                            return 'btn-warning';
                        case 'IN PROGRESS':
                            return 'btn-info';
                        case 'FINISH':
                            return 'btn-success';
                        case 'APPROVED':
                            return 'btn-primary';
                        default:
                            return 'btn-warning';
                    }
                }

                function getStatusText(status) {
                    switch (status) {
                        case 'ASSIGNED':
                            return 'ASSIGNED';
                        case 'IN PROGRESS':
                            return 'IN PROGRESS';
                        case 'FINISH':
                            return 'FINISH';
                        case 'APPROVED':
                            return 'APPROVED';
                        default:
                            return 'ASSIGNED';
                    }
                }

                // Load timeline on page load
                $(document).ready(function() {
                    loadTimeline();
                });

                // Load timeline when button is clicked
                $(document).on('click', '#load_timeline', function() {
                    loadTimeline();
                });

                // Load timeline when date changes
                $(document).on('change', '#timeline_date', function() {
                    loadTimeline();
                });

                // Clear filter button
                $(document).on('click', '#clear_filter', function() {
                    $('#timeline_date').val('');
                    loadTimeline();
                });


            });
        </script>
    @endsection

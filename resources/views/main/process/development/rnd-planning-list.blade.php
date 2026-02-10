@extends('main.layouts.main')
@section('title')
    RnD Planning - Development
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
        .page-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .stats-cards {
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-proof {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .type-trial_khusus {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-high {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .priority-medium {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .priority-low {
            background-color: #e8f5e8;
            color: #388e3c;
        }

        .btn-action {
            padding: 4px 8px;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .planning-needed {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
@endsection
@section('page-title')
    RnD Planning - Development
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">RnD Planning</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Development</a></li>
                    <li class="breadcrumb-item active">RnD Planning</li>
                </ol>
            </div>
        </div>

        <div class="page-header">
            <h3 class="text-white"><i class="mdi mdi-clipboard-text"></i> RnD Planning</h3>
            <p class="mb-0">Daftar job development yang memerlukan planning proses dari RnD</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row stats-cards">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-warning" id="total-draft">0</div>
                    <div class="stat-label">Draft Jobs</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-info" id="total-planning">0</div>
                    <div class="stat-label">In Planning</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number text-success" id="total-completed">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="rnd-planning-table">
                        <thead>
                            <tr>
                                <th>Job Code</th>
                                <th>Job Name</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Marketing User</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTable will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
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
                var table = $('#rnd-planning-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route('development.rnd-planning.data') }}',
                        type: 'GET',
                        dataSrc: function(json) {
                            updateStatistics(json.data);
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'job_code',
                            render: function(data) {
                                return '<strong>' + data + '</strong>';
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<div>' + row.job_name + '</div>' +
                                    '<small class="text-muted">' +
                                    (row.specification ? row.specification.substring(0, 50) + '...' : '-') +
                                    '</small>';
                            }
                        },
                        {
                            data: 'customer_name',
                            render: function(data) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'type',
                            render: function(data) {
                                const typeClass = data === 'proof' ? 'type-proof' : 'type-trial_khusus';
                                const typeText = data === 'proof' ? 'Proof' : 'Trial Khusus';
                                return '<span class="type-badge ' + typeClass + '">' + typeText + '</span>';
                            }
                        },
                        {
                            data: 'priority',
                            render: function(data) {
                                let priorityClass = '';
                                let priorityText = '';

                                switch(data) {
                                    case 'high':
                                        priorityClass = 'priority-high';
                                        priorityText = 'High';
                                        break;
                                    case 'medium':
                                        priorityClass = 'priority-medium';
                                        priorityText = 'Medium';
                                        break;
                                    case 'low':
                                        priorityClass = 'priority-low';
                                        priorityText = 'Low';
                                        break;
                                    default:
                                        priorityClass = 'priority-medium';
                                        priorityText = 'Medium';
                                }

                                return '<span class="priority-badge ' + priorityClass + '">' + priorityText + '</span>';
                            }
                        },
                        {
                            data: 'marketing_user.name',
                            render: function(data) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'created_at',
                            render: function(data) {
                                const date = new Date(data);
                                const formattedDate = date.toLocaleDateString('id-ID');
                                const formattedTime = date.toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                return '<div>' + formattedDate + '</div>' +
                                    '<small class="text-muted">' + formattedTime + '</small>';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let buttons = '<div class="btn-group btn-group-sm" role="group">';

                                // View button
                                buttons += '<button type="button" class="btn btn-info btn-action" ' +
                                    'onclick="viewJobDetails(' + row.id + ')" title="View Details">' +
                                    '<i class="mdi mdi-eye"></i></button>';

                                // Plan button (only for draft status)
                                if (row.status === 'draft') {
                                    buttons += '<a href="/sipo/development/rnd-planning/' + row.id + '/plan" ' +
                                        'class="btn btn-success btn-action" title="Create Planning">' +
                                        '<i class="mdi mdi-clipboard-text"></i></a>';
                                }

                                // Edit Planning button (for planning status)
                                if (row.status === 'planning') {
                                    buttons += '<a href="/sipo/development/rnd-planning/' + row.id + '/edit" ' +
                                        'class="btn btn-warning btn-action" title="Edit Planning">' +
                                        '<i class="mdi mdi-pencil"></i></a>';
                                }

                                // Download attachment button
                                if (row.attachment) {
                                    buttons += '<a href="/sipo_krisan/public/storage/' + row.attachment + '" ' +
                                        'class="btn btn-secondary btn-action" target="_blank" ' +
                                        'title="Download Attachment">' +
                                        '<i class="mdi mdi-file-pdf"></i></a>';
                                }

                                buttons += '</div>';
                                return buttons;
                            }
                        }
                    ],
                    pageLength: 10,
                    order: [[6, 'desc']], // Sort by created date desc
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Data tidak ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        processing: "Memproses data...",
                        loadingRecords: "Memuat data..."
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
            });

            function viewJobDetails(jobId) {
                // Load job details via AJAX
                $.ajax({
                    url: `/sipo/development/job-details/${jobId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success === false) {
                            alert('Gagal memuat detail job');
                            return;
                        }

                        const job = response;
                        
                        // Create simple alert with job details
                        let details = `Job Code: ${job.job_code}\n`;
                        details += `Job Name: ${job.job_name}\n`;
                        details += `Type: ${job.type}\n`;
                        details += `Priority: ${job.priority}\n`;
                        details += `Status: ${job.status}\n`;
                        details += `Customer: ${job.customer_name || '-'}\n`;
                        details += `Specification: ${job.specification}`;

                        alert(details);
                    },
                    error: function(xhr) {
                        alert('Gagal memuat detail job');
                    }
                });
            }

            function updateStatistics(data) {
                let draft = 0;
                let planning = 0;
                let completed = 0;

                data.forEach(function(job) {
                    switch (job.status) {
                        case 'draft':
                            draft++;
                            break;
                        case 'planning':
                            planning++;
                            break;
                        case 'completed':
                            completed++;
                            break;
                    }
                });

                $('#total-draft').text(draft);
                $('#total-planning').text(planning);
                $('#total-completed').text(completed);
            }
        </script>
    @endsection

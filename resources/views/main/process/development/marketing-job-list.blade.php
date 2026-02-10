@extends('main.layouts.main')
@section('title')
    Data Development
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <style>
        .cust-col {
            max-width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .page-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #e9ecef;
            color: #495057;
        }

        .status-planning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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

        .type-new {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }

        .type-repeat {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #ce93d8;
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


        /* Responsive improvements */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            .dt-buttons {
                text-align: center;
                margin-bottom: 15px;
            }

            .dt-buttons .dt-button {
                margin: 2px;
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>
@endsection
@section('page-title')
    Data Development
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Development</li>
                </ol>
            </div>
        </div>

        <div class="page-header">
            <h3 class="text-white"><i class="mdi mdi-view-list"></i> Development Job List</h3>
            <p class="mb-0">Daftar semua job development yang telah diinput</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row stats-cards">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-primary" id="total-jobs">0</div>
                    <div class="stat-label">Total Jobs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-warning" id="draft-jobs">0</div>
                    <div class="stat-label">Draft</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-info" id="in-progress-jobs">0</div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-success" id="completed-jobs">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        {{-- <div class="row mb-3">
            <div class="col">
                <a href="{{ route('development.development-input.form') }}" class="btn btn-info" style="width: 100%;">
                    <i class="mdi mdi-plus-circle"></i> Input Job Development Baru
                </a>
            </div>
        </div> --}}

        <div class="card">
            <div class="card-body">

                <!-- Table Container -->
                <div class="table">
                    <div class="table-responsive">
                        <table class="table table-striped" id="marketing-jobs-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Job Name</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
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
        </div>




    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
        {{-- jquery --}}

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            $(document).ready(function() {
                var table = $('#marketing-jobs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('development.marketing-jobs.data') }}',
                        type: 'GET',
                        dataSrc: function(json) {
                            // Update statistics cards
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
                                return '<div>' + (row.job_name || '-') + '</div>' +
                                    '<small class="text-muted">' +
                                    (row.product ? row.product.substring(0, 50) + '...' : '-') +
                                    '</small>';
                            }
                        },
                        {
                            data: 'customer',
                            render: function(data) {
                                if (!data) return '-';
                                return data;
                            }
                        },
                        {
                            data: 'job_type',
                            render: function(data) {
                                const typeClass = data === 'new' ? 'type-new' : 'type-repeat';
                                const typeText = data === 'new' ? 'Produk Baru' : 'Produk Repeat';
                                return '<span class="type-badge ' + typeClass + '">' + typeText +
                                    '</span>';
                            }
                        },
                        {
                            data: 'prioritas_job',
                            render: function(data) {
                                let priorityClass = '';
                                let priorityText = '';

                                switch(data) {
                                    case 'Urgent':
                                        priorityClass = 'priority-high';
                                        priorityText = 'Urgent';
                                        break;
                                    case 'Normal':
                                        priorityClass = 'priority-medium';
                                        priorityText = 'Normal';
                                        break;
                                    default:
                                        priorityClass = 'priority-medium';
                                        priorityText = 'Normal';
                                }

                                return '<span class="priority-badge ' + priorityClass + '">' + priorityText + '</span>';
                            }
                        },
                        {
                            data: 'status_job',
                            render: function(data) {
                                const statusClass = 'status-' + (data || 'open').toLowerCase();
                                const statusText = (data || 'OPEN').replace('_', ' ').replace(/\b\w/g, l => l
                                    .toUpperCase());
                                return '<span class="status-badge ' + statusClass + '">' + statusText +
                                    '</span>';
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

                                // View button (always available for everyone) - redirect to development-input form
                                buttons += '<a href="/sipo/development/development-input/' + row.id + '/view" ' +
                                    'class="btn btn-info btn-action" title="View Details">' +
                                    '<i class="mdi mdi-eye"></i></a>';

                                // Edit button (only for OPEN status and creator) - redirect to development-input form
                                if (row.status_job === 'OPEN' && row.marketing_user_id == {{ auth()->id() }}) {
                                    buttons += '<a href="/sipo/development/development-input/' + row.id + '/edit" ' +
                                        'class="btn btn-warning btn-action" title="Edit Job">' +
                                        '<i class="mdi mdi-pencil"></i></a>';
                                }

                                // Delete button (only for OPEN status and creator)
                                if (row.status_job === 'OPEN' && row.marketing_user_id == {{ auth()->id() }}) {
                                    buttons += '<button type="button" class="btn btn-danger btn-action" ' +
                                        'onclick="deleteJob(' + row.id + ')" title="Delete Job">' +
                                        '<i class="mdi mdi-delete"></i></button>';
                                }

                                buttons += '</div>';
                                return buttons;
                            }
                        }
                    ],
                    pageLength: 10,
                    order: [
                        [6, 'desc']
                    ], // Sort by created date desc
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







            function deleteJob(jobId) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus job ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/development/delete-job/${jobId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        // Refresh DataTable
                                        $('#marketing-jobs-table').DataTable().ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menghapus job';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }



            function updateStatistics(data) {
                let total = data.length;
                let draft = 0;
                let inProgress = 0;
                let completed = 0;

                data.forEach(function(job) {
                    switch (job.status_job) {
                        case 'OPEN':
                            draft++;
                            break;
                        case 'PLANNING':
                        case 'IN_PROGRESS':
                            inProgress++;
                            break;
                        case 'COMPLETED':
                        case 'FINISHED':
                            completed++;
                            break;
                    }
                });

                $('#total-jobs').text(total);
                $('#draft-jobs').text(draft);
                $('#in-progress-jobs').text(inProgress);
                $('#completed-jobs').text(completed);
            }

        </script>
    @endsection

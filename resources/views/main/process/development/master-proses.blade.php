@extends('main.layouts.main')

@section('head')
    <link href="{{ asset('assets/plugins/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatables/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        .department-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 3px 6px;
        }

        .urgent-row {
            background-color: #ffebee !important;
        }

        .urgent-row td:nth-child(1),
        .urgent-row td:nth-child(2) {
            background-color: #f44336 !important;
            color: white !important;
            font-weight: bold;
        }

        .department-filter-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .department-filter-card {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .department-filter-card:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .department-filter-card.active {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .department-filter-card h6 {
            margin: 0;
            font-weight: bold;
        }

        .department-filter-card .count {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0;
        }

        .department-filter-card.marketing {
            border-color: #007bff;
        }

        .department-filter-card.rnd {
            border-color: #17a2b8;
        }

        .department-filter-card.ppic {
            border-color: #ffc107;
        }

        .department-filter-card.production {
            border-color: #28a745;
        }

        .department-filter-card.prepress {
            border-color: #6c757d;
        }

        .department-filter-card.all {
            border-color: #6f42c1;
        }

        .department-filter-card.marketing.active {
            background-color: #e3f2fd;
        }

        .department-filter-card.rnd.active {
            background-color: #e0f7fa;
        }

        .department-filter-card.ppic.active {
            background-color: #fff8e1;
        }

        .department-filter-card.production.active {
            background-color: #e8f5e8;
        }

        .department-filter-card.prepress.active {
            background-color: #f5f5f5;
        }

        .department-filter-card.all.active {
            background-color: #f3e5f5;
        }
    </style>
@endsection
@section('page-title')
    Workspace - Development
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Proses Development</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Master Proses</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Daftar Master Proses</h5>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addProsesModal">
                                <i class="mdi mdi-plus"></i> Add Proses
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="master-proses-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Urutan Proses</th>
                                        <th>Proses</th>
                                        <th>Department</th>
                                        <th>Expected Days</th>
                                        <th>Action</th>
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

    <!-- Add Proses Modal -->
    <div class="modal fade" id="addProsesModal" tabindex="-1" role="dialog" aria-labelledby="addProsesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProsesModalLabel">Add New Proses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addProsesForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="urutan_proses">Urutan Proses <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="urutan_proses" name="urutan_proses" min="1" max="20" required>
                                    <small class="form-text text-muted">Urutan proses dalam alur development (1-20)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_responsible">Department <span class="text-danger">*</span></label>
                                    <select class="form-control" id="department_responsible" name="department_responsible" required>
                                        <option value="">-- Pilih Department --</option>
                                        @foreach($department as $data)
                                            <option value="{{ $data['divisi'] }}">{{ $data['divisi'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="proses_name">Nama Proses <span class="text-danger">*</span></label>
                                    <select class="form-control" id="proses_name" name="proses_name" required>
                                        <option value="">-- Pilih Proses --</option>
                                        <option value="ONE_RND_SEND_TO_PREPRESS">1. RnD Send to Prepress</option>
                                        <option value="TWO_PREPRESS_PROCESS">2. Prepress Process</option>
                                        <option value="THREE_MARKETING_CREATE_MEETING_OPP">3. Marketing Create Meeting OPP</option>
                                        <option value="FOUR_RND_ACC_RESULT_MEETING_OPP">4. RnD ACC/REJECT Meeting OPP</option>
                                        <option value="FIVE_MARKETING_CONFIRM_RESULT_ONE">5. Marketing Confirm Result Customer After Send Result OPP</option>
                                        <option value="SIX_PPIC_SCHEDULING">6. PPIC Scheduling Development & Production</option>
                                        <option value="SEVEN_PRODUCTION_REPORT_RESULTS">7. Production Report Results</option>
                                        <option value="EIGHT_RND_APPROVE_PRODUCTION_REPORT">8. RnD Approve Production Report</option>
                                        <option value="NINE_MARKETING_UPLOAD_MAP_PROOF">9. Marketing Upload Map Proof</option>
                                        <option value="TEN_MARKETING_CONFIRM_RESULT_CUSTOMER_AFTER_SEND_RESULT_MAP_PROOF">10. Marketing Confirm Result Customer After Send Result Map Proof</option>
                                        <option value="ELEVEN_MARKETING_CREATE_SALES_ORDER">11. Marketing Create Sales Order</option>
                                        <option value="TWELVE_MARKETING_CLOSE_DEVELOPMENT_ITEM">12. Marketing Close Development Item</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Deskripsi Proses</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi detail proses ini..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_days">Expected Days</label>
                                    <input type="number" class="form-control" id="expected_days" name="expected_days" min="1" max="30" placeholder="Berapa hari proses ini diharapkan selesai">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_required">Required Process</label>
                                    <select class="form-control" id="is_required" name="is_required">
                                        <option value="1">Yes (Required)</option>
                                        <option value="0">No (Optional)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-plus"></i> Add Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Proses Modal -->
    <div class="modal fade" id="editProsesModal" tabindex="-1" role="dialog" aria-labelledby="editProsesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProsesModalLabel">Edit Proses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editProsesForm">
                    <input type="hidden" id="edit_proses_id" name="proses_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_urutan_proses">Urutan Proses <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_urutan_proses" name="urutan_proses" min="1" max="20" required>
                                    <small class="form-text text-muted">Urutan proses dalam alur development (1-20)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_department_responsible">Department <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_department_responsible" name="department_responsible" required>
                                        <option value="">-- Pilih Department --</option>
                                        @foreach($department as $dept)
                                            <option value="{{ $dept->divisi }}">{{ $dept->divisi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_proses_name">Nama Proses <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_proses_name" name="proses_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_description">Deskripsi Proses</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Deskripsi detail proses ini..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_expected_days">Expected Days</label>
                                    <input type="number" class="form-control" id="edit_expected_days" name="expected_days" min="1" max="30" placeholder="Berapa hari proses ini diharapkan selesai">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_is_required">Required Process</label>
                                    <select class="form-control" id="edit_is_required" name="is_required">
                                        <option value="1">Yes (Required)</option>
                                        <option value="0">No (Optional)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Update Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Proses Modal -->
    <div class="modal fade" id="deleteProsesModal" tabindex="-1" role="dialog" aria-labelledby="deleteProsesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProsesModalLabel">Delete Proses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus proses ini?</p>
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan dan akan menghapus proses dari semua job development yang terkait.
                    </div>
                    <div id="deleteProsesInfo" class="mt-3">
                        <strong>Proses:</strong> <span id="delete_proses_name"></span><br>
                        <strong>Department:</strong> <span id="delete_department"></span><br>
                        <strong>Urutan:</strong> <span id="delete_urutan"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteProses">
                        <i class="mdi mdi-delete"></i> Delete Proses
                    </button>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            $(document).ready(function() {
                // Department filter cards click handler
                $('.department-filter-card').click(function() {
                    $('.department-filter-card').removeClass('active');
                    $(this).addClass('active');

                    var department = $(this).data('department');
                    filterByDepartment(department);
                });

                // Load dashboard counts on page load
                // updateDashboardCounts();

                // Initialize DataTable
                var table = $('#master-proses-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '/sipo/development/master-proses/data',
                        type: 'GET',
                        data: function(d) {
                            d.status_filter = $('#statusFilter').val();
                            d.department_filter = $('.department-filter-card.active').data('department');
                            d.expected_days_filter = $('#expectedDaysFilter').val();
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: 'urutan_proses'
                        },
                        {
                            data: 'proses_name'
                        },
                        {
                            data: 'department_responsible',
                        },
                        {
                            data: 'expected_days'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let buttons = '';
                                buttons += '<button class="btn btn-warning btn-sm" onclick="editProses(' + row.id + ')" title="Edit Proses"><i class="mdi mdi-pencil"></i></button> ';
                                buttons += '<button class="btn btn-danger btn-sm" onclick="deleteProses(' + row.id + ')" title="Delete Proses"><i class="mdi mdi-delete"></i></button>';
                                return buttons;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    rowCallback: function(row, data, index) {
                        // Add urgent styling for urgent jobs
                        if (data.expected_days > 10) {
                            $(row).addClass('urgent-row');
                        }
                    },
                    language: {
                        "sProcessing": "Sedang memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ditemukan data yang sesuai",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoPostFix": "",
                        "sSearch": "Cari:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Selanjutnya",
                            "sLast": "Terakhir"
                        }
                    }
                });

                // Filter change handlers
                $('#statusFilter, #expectedDaysFilter').change(function() {
                    table.ajax.reload();
                });

                // Refresh button
                $('#refreshBtn').click(function() {
                    table.ajax.reload();
                    // updateDashboardCounts();
                });

                // Reset filter button
                $('#resetFilterBtn').click(function() {
                    $('#statusFilter, #priorityFilter').val('');
                    $('.department-filter-card').removeClass('active');
                    $('.department-filter-card[data-department="all"]').addClass('active');
                    table.ajax.reload();
                });

                // Export button
                $('#exportBtn').click(function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Export Feature',
                        text: 'Export feature akan segera tersedia!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                // Function to filter by department
                function filterByDepartment(department) {
                    table.ajax.reload();
                }


            // Function to check progress
            window.checkProgress = function(jobId) {
                window.location.href = '/sipo/development/rnd-workspace/' + jobId + '/view';
            };

            // Function to edit proses
            window.editProses = function(prosesId) {
                // Get proses data
                $.ajax({
                    url: '/sipo/development/master-proses/get-proses/' + prosesId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var proses = response.data;

                            // Fill edit form
                            $('#edit_proses_id').val(proses.id);
                            $('#edit_urutan_proses').val(proses.urutan_proses);
                            $('#edit_department_responsible').val(proses.department_responsible);
                            $('#edit_proses_name').val(proses.proses_name);
                            $('#edit_description').val(proses.notes);
                            $('#edit_expected_days').val(proses.expected_days);
                            $('#edit_is_required').val(proses.is_required ? '1' : '0');

                            // Show modal
                            $('#editProsesModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while loading proses data'
                        });
                    }
                });
            };

            // Function to delete proses
            window.deleteProses = function(prosesId) {
                // Get proses data for confirmation
                $.ajax({
                    url: '/sipo/development/master-proses/get-proses/' + prosesId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var proses = response.data;

                            // Fill delete confirmation
                            $('#delete_proses_name').text(proses.proses_name);
                            $('#delete_department').text(proses.department_responsible);
                            $('#delete_urutan').text(proses.urutan_proses);

                            // Store proses ID for deletion
                            $('#confirmDeleteProses').data('proses-id', prosesId);

                            // Show modal
                            $('#deleteProsesModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while loading proses data'
                        });
                    }
                });
            };

            // Handle Add Proses Form
            $('#addProsesForm').on('submit', function(e) {
                e.preventDefault();

                var formData = {
                    _token: '{{ csrf_token() }}',
                    urutan_proses: $('#urutan_proses').val(),
                    department_responsible: $('#department_responsible').val(),
                    proses_name: $('#proses_name').val(),
                    description: $('#description').val(),
                    expected_days: $('#expected_days').val(),
                    is_required: $('#is_required').val()
                };

                // Validate required fields
                if (!formData.urutan_proses || !formData.department_responsible || !formData.proses_name) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Adding Proses...',
                    text: 'Please wait while we add the new proses',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                $.ajax({
                    url: '/sipo/development/master-proses/add-proses',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(() => {
                                $('#addProsesModal').modal('hide');
                                $('#addProsesForm')[0].reset();
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while adding the proses';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            });

            // Handle Edit Proses Form
            $('#editProsesForm').on('submit', function(e) {
                e.preventDefault();

                var formData = {
                    _token: '{{ csrf_token() }}',
                    proses_id: $('#edit_proses_id').val(),
                    urutan_proses: $('#edit_urutan_proses').val(),
                    department_responsible: $('#edit_department_responsible').val(),
                    proses_name: $('#edit_proses_name').val(),
                    description: $('#edit_description').val(),
                    expected_days: $('#edit_expected_days').val(),
                    is_required: $('#edit_is_required').val()
                };

                // Validate required fields
                if (!formData.urutan_proses || !formData.department_responsible || !formData.proses_name) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Updating Proses...',
                    text: 'Please wait while we update the proses',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                $.ajax({
                    url: '/sipo/development/master-proses/update-proses',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(() => {
                                $('#editProsesModal').modal('hide');
                                $('#editProsesForm')[0].reset();
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while updating the proses';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            });

            // Handle Delete Proses Confirmation
            $('#confirmDeleteProses').on('click', function() {
                var prosesId = $(this).data('proses-id');

                // Show loading
                Swal.fire({
                    title: 'Deleting Proses...',
                    text: 'Please wait while we delete the proses',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Delete proses
                $.ajax({
                    url: '/sipo/development/master-proses/delete-proses',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        proses_id: prosesId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(() => {
                                $('#deleteProsesModal').modal('hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while deleting the proses';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            });
        });
    </script>
@endsection

@extends('main.layouts.main')
@section('title')
    Planning - {{ request()->get('processes', 'Semua Proses') }}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
@section('page-title')
    Planning
@endsection
@section('body')
    <style>
        .status-deleted {
            background-color: #f8d7da;
        }

        .status-in-progress {
            background-color: #d4edda;
        }

        .status-open {
            background-color: #d1ecf1;
        }

        /* Styling untuk checkbox */
        .select-wodocno {
            cursor: pointer;
            transform: scale(1.2);
        }

        #select-all-work-orders {
            cursor: pointer;
            transform: scale(1.2);
        }

        /* Highlight row ketika checkbox dipilih */
        .table tbody tr.selected {
            background-color: #e3f2fd !important;
        }

        .process-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            margin: 0.2rem;
            display: inline-block;
        }

        .process-badge.potong {
            background-color: #dc3545;
            color: white;
        }

        .process-badge.cetak {
            background-color: #007bff;
            color: white;
        }

        .process-badge.plong {
            background-color: #ffc107;
            color: #212529;
        }

        .process-badge.emboss {
            background-color: #28a745;
            color: white;
        }

        .process-badge.sortir {
            background-color: #17a2b8;
            color: white;
        }

        .process-badge.glueing {
            background-color: #fd7e14;
            color: white;
        }

        .process-badge.lem {
            background-color: #6c757d;
            color: white;
        }

        .header-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .btn-planning {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-planning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
    </style>

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Planning Produksi GLUEING</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Penjadwalan</a></li>
                    <li class="breadcrumb-item active">Planning Produksi GLUEING</li>
                </ol>
            </div>
        </div>
        @php
            $processes = request()->get('processes', '');
            $processList = $processes ? explode(',', $processes) : [];
        @endphp


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="card-title">Pilih Work Order untuk Planning</h4>
                                <p class="text-muted">Pilih item yang akan direncanakan untuk proses:
                                    @foreach ($processList as $process)
                                        <span class="process-badge {{ strtolower($process) }}">{{ $process }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <div class="col text-right">
                                <button type="button" class="btn btn-success" id="btnCreatePlan">
                                    <i class="fas fa-calendar-plus"></i> Buat Planning
                                </button>
                            </div>
                        </div>

                        <div id="table-work-order" style="display: block;">
                            <div id="advanced_filter_wo">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="filter_label_from">SO Date - From</label>
                                        <input type="date" class="form-control" name="from_date_advanced_filter_wo"
                                            id="from_date_advanced_filter_wo">
                                    </div>
                                    <div class="col-md-4">
                                        <label id="filter_label_to">SO Date - To</label>
                                        <input type="date" class="form-control" name="to_date_advanced_filter_wo"
                                            id="to_date_advanced_filter_wo">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn btn-info" type="button" id="filterButton">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr>

                            <table id="datatable-work-order" class="table table-hover nowrap">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-work-orders" title="Select All"></th>
                                        <th>WO DocNo</th>
                                        <th>SO DocNo</th>
                                        <th>Delivery Date</th>
                                        <th>Material Code</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Up</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                        <th>Urutan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Dipilih: <span id="selected-count">0</span> item
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetOrder">
                                            <i class="fas fa-undo"></i> Reset Urutan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="table-work-order-cetak" style="display: none;">
                            <div id="advanced_filter_wo_cetak">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Hasil Cetak - From</label>
                                        <input type="date" class="form-control" name="from_date_advanced_filter_wo_cetak"
                                            id="from_date_advanced_filter_wo_cetak">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Hasil Cetak - To</label>
                                        <input type="date" class="form-control" name="to_date_advanced_filter_wo_cetak"
                                            id="to_date_advanced_filter_wo_cetak">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn btn-info" type="button" id="filterButtonCetak">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <table id="datatable-work-order-cetak" class="table table-hover nowrap">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-work-orders-cetak" title="Select All">
                                        </th>
                                        <th>WO DocNo</th>
                                        <th>SO DocNo</th>
                                        <th>Delivery Date</th>
                                        <th>Material Code</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Up</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                        <th>Urutan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Dipilih: <span id="selected-count-cetak">0</span> item
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Planning -->
        <div class="modal fade" id="planningModal" tabindex="-1" aria-labelledby="planningModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form class="submitPlanning" id="submitPlanning">
                        <div class="modal-header">
                            <h5 class="modal-title" id="planningModalLabel">Buat Planning Produksi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label><strong>Start Date</strong></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label><strong>End Date</strong></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" readonly>
                                </div>
                            </div>


                            <div id="planningTableContainer">
                                <!-- Tabel planning akan di-generate di sini -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="btnPreviewPlanning">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Plan Preview Modal -->
        <div class="modal fade" id="planPreviewModal" tabindex="-1" role="dialog"
            aria-labelledby="planPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="planPreviewModalLabel">Preview Timeline Rencana Produksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" id="planPreviewModalBody" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Preview content will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="savePlanFromPreview">Simpan ke
                            Database</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali/Edit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal PPOS Schedule -->
        <div class="modal fade" id="pposScheduleModal" tabindex="-1" role="dialog" aria-labelledby="pposScheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="pposScheduleModalLabel">
                            <i class="fas fa-calendar-alt"></i> Jadwal PPOS
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="pposScheduleForm">
                        <div class="modal-body">
                            <input type="hidden" id="ppos_machine_code" name="machine_code">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_start_date"><strong>Tanggal Mulai PPOS</strong></label>
                                        <input type="date" class="form-control" id="ppos_start_date" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_start_time"><strong>Jam Mulai PPOS</strong></label>
                                        <input type="time" class="form-control" id="ppos_start_time" name="start_time" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_end_date"><strong>Tanggal Selesai PPOS</strong></label>
                                        <input type="date" class="form-control" id="ppos_end_date" name="end_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_end_time"><strong>Jam Selesai PPOS</strong></label>
                                        <input type="time" class="form-control" id="ppos_end_time" name="end_time" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_item"><strong>Item yang akan di-PPOS</strong></label>
                                        <input type="text" class="form-control" id="ppos_item" name="item" placeholder="Nama Item/Produk yang akan di-PPOS" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ppos_notes"><strong>Catatan PPOS</strong></label>
                                        <input type="text" class="form-control" id="ppos_notes" name="notes" placeholder="Catatan tambahan (opsional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save"></i> Simpan PPOS
                            </button>
                        </div>
                    </form>
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
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Ambil parameter proses dari URL
                const urlParams = new URLSearchParams(window.location.search);
                const processes = urlParams.get('processes');

                if (!processes) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Proses Tidak Dipilih',
                        text: 'Silakan pilih proses terlebih dahulu.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route('mulai-proses.plan') }}';
                    });
                    return;
                }

                console.log('=== PROSES ===');
                $('#table-work-order').show();
                $('#table-work-order-cetak').hide();
                // Label filter default untuk GLUEING
                $('#filter_label_from').text('SO Date - From');
                $('#filter_label_to').text('SO Date - To');

                // Set default dates
                const today = new Date();
                const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

                $('#start_date').val(today.toISOString().split('T')[0]);
                $('#end_date').val(nextWeek.toISOString().split('T')[0]);

                // Auto-update end date when start date changes
                $('#start_date').on('change', function() {
                    const startDate = new Date(this.value);
                    const endDate = new Date(startDate.getTime() + 7 * 24 * 60 * 60 * 1000);
                    $('#end_date').val(endDate.toISOString().split('T')[0]);
                });

                // Pastikan elemen tabel ada sebelum inisialisasi DataTable
                if ($('#datatable-work-order').length === 0) {
                    console.error('Table element #datatable-work-order not found');
                    return;
                }

                // Initialize DataTable (jangan load data awal)
                const table = $('#datatable-work-order').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    autoWidth: false, // Disable auto width untuk mencegah error dengan kolom tersembunyi
                    deferLoading: 0, // biar kosong dulu sampai user klik Filter
                    ajax: {
                        url: "{{ route('wo-data.index') }}",
                        data: function(d) {
                            const from = $('#from_date_advanced_filter_wo').val();
                            const to = $('#to_date_advanced_filter_wo').val();
                            d.from = from;
                            d.to = to;
                            d.processes = processes;
                        }
                    },
                    lengthMenu: [25, 50, 100],
                    paginate: false,
                    searching: false,
                    columns: [{
                            data: 'WODocNo',
                            name: 'select',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" id="select-wo-' + data +
                                    '" class="select-wodocno" value="' + data +
                                    '"> <label for="select-wo-' + data + '">&nbsp;</label>';
                            }
                        }].concat([{
                            data: 'WODocNo',
                            name: 'WODocNo'
                        },
                        {
                            data: 'SODocNo',
                            name: 'SODocNo'
                        },
                        {
                            data: 'DeliveryDate',
                            name: 'DeliveryDate'
                        },
                        {
                            data: 'MaterialCode',
                            name: 'MaterialCode'
                        },
                        {
                            data: 'MaterialName',
                            name: 'MaterialName'
                        },
                        {
                            data: 'Quantity',
                            name: 'Quantity'
                        },
                        {
                            data: 'Unit',
                            name: 'Unit'
                        },
                        {
                            data: 'Up',
                            name: 'Up'
                        },
                        {
                            data: 'Status',
                            name: 'Status'
                        },
                        {
                            data: 'Detail',
                            name: 'Detail'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<input type="number" class="counter-order form-control form-control-sm" disabled style="width: 60px; text-align: center;">';
                            }
                        }]),
                    columnDefs: [],
                    drawCallback: function(settings) {
                        try {
                            // Inisialisasi tooltip setelah DataTable selesai render
                            $('[data-toggle="tooltip"]').tooltip();
                        } catch (e) {
                            console.warn('Error in drawCallback:', e);
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable error:', error, thrown);
                    }
                });

                const tableCetak = $('#datatable-work-order-cetak').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    deferLoading: 0, // biar kosong dulu sampai user klik Filter
                    ajax: {
                        url: "{{ route('hasil-cetak.index') }}",
                        data: function(d) {
                            const from = $('#from_date_advanced_filter_wo_cetak').val();
                            const to = $('#to_date_advanced_filter_wo_cetak').val();
                            d.from = from;
                            d.to = to;
                            d.processes = processes;
                        }
                    },
                    lengthMenu: [25, 50, 100],
                    paginate: false,
                    searching: false,
                    columns: [{
                            data: 'WODocNo',
                            name: 'select',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" id="select-wo-' + data +
                                    '" class="select-wodocno" value="' + data +
                                    '"> <label for="select-wo-' + data + '">&nbsp;</label>';
                            }
                        },
                        {
                            data: 'WODocNo',
                            name: 'WODocNo'
                        },
                        {
                            data: 'SODocNo',
                            name: 'SODocNo'
                        },
                        {
                            data: 'DeliveryDate',
                            name: 'DeliveryDate'
                        },
                        {
                            data: 'MaterialCode',
                            name: 'MaterialCode'
                        },
                        {
                            data: 'MaterialName',
                            name: 'MaterialName'
                        },
                        {
                            data: 'Quantity',
                            name: 'Quantity'
                        },
                        {
                            data: 'Unit',
                            name: 'Unit'
                        },
                        {
                            data: 'Up',
                            name: 'Up'
                        },
                        {
                            data: 'Status',
                            name: 'Status'
                        },
                        {
                            data: 'Detail',
                            name: 'Detail'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return '<input type="number" class="counter-order form-control form-control-sm" disabled style="width: 60px; text-align: center;">';
                            }
                        }
                    ]
                });

                // Event handlers
                let selectedRows = [];

                // Checkbox change handler
                $(document).on('change', '.select-wodocno', function() {
                    updateSelection();
                });

                // Select all handler
                $(document).on('change', '#select-all-work-orders', function() {
                    const isChecked = $(this).is(':checked');
                    $('.select-wodocno').prop('checked', isChecked).trigger('change');
                });

                // Filter button handler
                $('#filterButton').on('click', function() {
                    const fromDate = $('#from_date_advanced_filter_wo').val();
                    const toDate = $('#to_date_advanced_filter_wo').val();

                    if (fromDate && toDate) {
                        table.ajax.reload();
                    } else {
                        // kosongkan tabel jika filter tidak valid
                        table.clear().draw();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal Harus Diisi',
                            text: 'Silakan pilih tanggal awal dan akhir untuk filtering.',
                            confirmButtonText: 'OK'
                        });
                    }
                });

                // Reset order button
                $('#btnResetOrder').on('click', function() {
                    $('.counter-order').val('').prop('disabled', true);
                    updateSelection();
                });

                // Create planning button
                $('#btnCreatePlan').on('click', function() {
                    if (selectedRows.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Item Dipilih',
                            text: 'Silakan pilih minimal satu item untuk dibuat planning.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Show confirmation with process info
                    Swal.fire({
                        icon: 'question',
                        title: 'Konfirmasi Planning',
                        html: `
                            <p>Anda akan membuat planning untuk:</p>
                            <p><strong>Proses:</strong> <span class="badge badge-info">${processes}</span></p>
                            <p><strong>Jumlah Item:</strong> ${selectedRows.length} item</p>
                            <p>Lanjutkan?</p>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showPlanningModal();
                        }
                    });
                });

                // Update selection function
                function updateSelection() {
                    selectedRows = [];
                    let counter = 1;

                    $('.select-wodocno:checked').each(function() {
                        const checkbox = $(this);
                        const row = checkbox.closest('tr');
                        const rowData = table.row(row).data();

                        console.log('=== DATATABLE ROW ANALYSIS ===');
                        console.log('Checkbox value:', checkbox.val());
                        console.log('Row data yang dipilih:', rowData);
                        console.log('Row data type:', typeof rowData);
                        console.log('Row data keys:', Object.keys(rowData || {}));
                        console.log('MaterialName in rowData:', rowData?.MaterialName);
                        console.log('MaterialName type:', typeof rowData?.MaterialName);
                        console.log('---');

                        if (rowData) {
                            // Pastikan semua field yang diperlukan ada
                            const processedRowData = {
                                MaterialCode: rowData.MaterialCode || rowData.materialCode || rowData
                                    .MaterialCode || '',
                                MaterialName: rowData.MaterialName || rowData.materialName || rowData
                                    .MaterialName || '',
                                Quantity: parseInt(rowData.Quantity) || parseInt(rowData.quantity) || 1,
                                DeliveryDate: rowData.DeliveryDate || rowData.deliveryDate || rowData
                                    .DeliveryDate || new Date().toISOString().split('T')[0],
                                WODocNo: rowData.WODocNo || rowData.woDocNo || rowData.WODocNo || '',
                                SODocNo: rowData.SODocNo || rowData.soDocNo || rowData.SODocNo || '',
                                Unit: rowData.Unit || rowData.unit || 'PCS',
                                Up: parseInt(rowData.Up) || parseInt(rowData.up) || 1,
                                counterOrder: counter
                            };

                            console.log('=== PROCESSED ROW DATA ===');
                            console.log('Processed row data:', processedRowData);
                            console.log('MaterialName in processedRowData:', processedRowData.MaterialName);
                            console.log('MaterialName type:', typeof processedRowData.MaterialName);
                            console.log('MaterialName length:', processedRowData.MaterialName ? processedRowData
                                .MaterialName.length : 'null/undefined');
                            console.log('---');
                            selectedRows.push(processedRowData);

                            // Update counter order input
                            const counterInput = row.find('.counter-order');
                            counterInput.val(counter).prop('disabled', false);

                            counter++;
                        } else {
                            console.warn('No row data found for checkbox:', checkbox.val());
                        }
                    });

                    // Reset counter for unchecked rows
                    $('.select-wodocno:not(:checked)').each(function() {
                        const row = $(this).closest('tr');
                        row.find('.counter-order').val('').prop('disabled', true);
                    });

                    // Update UI
                    $('#selected-count').text(selectedRows.length);
                    $('#btnCreatePlan').prop('disabled', selectedRows.length === 0);

                    // Update visual selection
                    $('.select-wodocno:checked').closest('tr').addClass('selected');
                    $('.select-wodocno:not(:checked)').closest('tr').removeClass('selected');
                }

                // Show planning modal
                function showPlanningModal() {
                    // Generate summary
                    // const summaryHtml = selectedRows.map((row, index) => `
                    //     <div class="mb-2 p-2 border rounded">
                    //         <strong>${index + 1}.</strong> ${row.MaterialCode} - ${row.MaterialName}
                    //         <br><small class="text-muted">WO: ${row.WODocNo} | Qty: ${row.Quantity} | Delivery: ${row.DeliveryDate}</small>
                    //     </div>
                    // `).join('');

                    // $('#selectedItemsSummary').html(summaryHtml);

                    // Generate planning table
                    const tableHtml = `
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Urutan</th>
                                    <th>Material Code</th>
                                    <th>Material Name</th>
                                    <th>Quantity</th>
                                    <th>Delivery Date</th>
                                    <th>WO DocNo</th>
                                    <th>SO DocNo</th>
                                    <th>UP</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${selectedRows.map((row, index) => `
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="text" class="form-control form-control-sm" name="order[]" value="${row.counterOrder}" readonly>
                                                                </td>
                                                                <td>${row.MaterialCode}</td>
                                                                <td>${row.MaterialName}</td>
                                                                <td class="text-center">${row.Quantity}</td>
                                                                <td class="text-center">${row.DeliveryDate}</td>
                                                                <td>${row.WODocNo}</td>
                                                                <td>${row.SODocNo}</td>
                                                                <td>${row.Up}</td>
                                                                <td>${row.Unit}</td>
                                                            </tr>
                                                        `).join('')}
                            </tbody>
                        </table>
                    `;

                    $('#planningTableContainer').html(tableHtml);

                    // Show modal
                    $('#planningModal').modal('show');
                }

                // Tampilkan preview dari modal planning
                $('#btnPreviewPlanning').on('click', async function() {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();

                    if (!startDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Start Date Harus Diisi',
                            text: 'Silakan pilih tanggal mulai planning.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Validate order sequence
                    const orders = $('input[name="order[]"]').map(function() {
                        return parseInt($(this).val()) || 0;
                    }).get();

                    if (!isSequential(orders)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Nilai input tidak berurutan!',
                        });
                        return;
                    }

                    // Validasi mapping item sebelum submit
                    console.log('=== VALIDASI MAPPING ITEM SEBELUM SUBMIT ===');
                    const missingMappingItems = [];

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memvalidasi Mapping Item...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Cek mapping untuk setiap item yang dipilih
                    for (let i = 0; i < selectedRows.length; i++) {
                        const row = selectedRows[i];
                        const materialCode = row.MaterialCode || row.materialCode || '';
                        const processType = processes || 'CETAK';

                        console.log('processType:', processType);

                        if (!materialCode) continue;

                        try {
                            const response = await fetch('{{ route('get-machine-mapping') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    materialCode: materialCode,
                                    processType: processType
                                })
                            });

                            const result = await response.json();

                            if (!result.success || !result.machine ||
                                result.machine.trim() === '' ||
                                result.machine === 'Default Machine' ||
                                result.machine === 'CTK-001') {
                                missingMappingItems.push({
                                    MaterialCode: materialCode,
                                    MaterialName: row.MaterialName || row.materialName || '-',
                                    WODocNo: row.WODocNo || row.woDocNo || '-',
                                    Machine: result.machine || 'Tidak ada'
                                });
                            }
                        } catch (error) {
                            console.error(`Error checking mapping for ${materialCode}:`, error);
                            missingMappingItems.push({
                                MaterialCode: materialCode,
                                MaterialName: row.MaterialName || row.materialName || '-',
                                WODocNo: row.WODocNo || row.woDocNo || '-',
                                Machine: 'Error checking'
                            });
                        }
                    }

                    // Tutup loading
                    Swal.close();

                    // Jika ada item tanpa mapping, tampilkan alert
                    if (missingMappingItems.length > 0) {
                        const missingItemsList = missingMappingItems.map((item, index) => {
                            return `${index + 1}. <strong>${item.MaterialCode}</strong> - ${item.MaterialName}<br>&nbsp;&nbsp;&nbsp;&nbsp;WO: ${item.WODocNo}`;
                        }).join('<br>');

                        const result = await Swal.fire({
                            icon: 'warning',
                            title: 'Item Tanpa Mapping Mesin',
                            html: `
                                <p><strong>${missingMappingItems.length} item</strong> belum memiliki mapping mesin di tb_mapping_items:</p>
                                <div style="text-align: left; max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; border: 1px solid #ffc107;">
                                    ${missingItemsList}
                                </div>
                                <p style="margin-top: 15px; color: #856404;"><small><i class="fas fa-info-circle"></i> Silakan lengkapi mapping di tb_mapping_items terlebih dahulu.</small></p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Tetap Lanjutkan',
                            cancelButtonText: 'Batal',
                            width: '700px',
                            allowOutsideClick: false
                        });

                        if (!result.isConfirmed) {
                            return; // User membatalkan
                        }
                    }

                    // Prepare data untuk dikirim ke submit-plan-first
                    // Backend mengharapkan 'data' bukan 'planPerItem'

                    // Untuk PLONG: Duplikasi data sesuai JumlahPlong dengan Material Code BOM
                    let expandedRows = [];
                    if (processes === 'PLONG') {
                        console.log('=== STARTING PLONG DUPLICATION ===');
                        console.log('Selected rows count:', selectedRows.length);

                        selectedRows.forEach((row, index) => {
                            const jumlahPlong = parseInt(row.JumlahPlong) || 1;
                            let materialCodesPlong = row.MaterialCodesPlong || [];

                            // Pastikan materialCodesPlong adalah array
                            if (!Array.isArray(materialCodesPlong)) {
                                if (typeof materialCodesPlong === 'string') {
                                    try {
                                        materialCodesPlong = JSON.parse(materialCodesPlong);
                                    } catch (e) {
                                        console.warn('Failed to parse MaterialCodesPlong as JSON:', e);
                                        materialCodesPlong = [];
                                    }
                                } else {
                                    materialCodesPlong = [];
                                }
                            }

                            console.log(`Processing row ${index + 1}:`, {
                                WODocNo: row.WODocNo,
                                MaterialCode: row.MaterialCode,
                                JumlahPlong: jumlahPlong,
                                MaterialCodesPlong: materialCodesPlong,
                                MaterialCodesPlongLength: materialCodesPlong.length,
                                MaterialCodesPlongType: typeof materialCodesPlong
                            });

                            // Duplikasi data sesuai jumlah PLONG, setiap duplikat dapat material code BOM yang berbeda
                            for (let i = 0; i < jumlahPlong; i++) {
                                // Ambil material code BOM sesuai index (jika ada)
                                // Jika MaterialCodesPlong kosong atau tidak ada, gunakan index sebagai fallback
                                const bomMaterialCode = materialCodesPlong[i] || (materialCodesPlong.length > 0 ? null : `PLONG_${i + 1}`);

                                console.log(`  Creating duplicate ${i + 1}/${jumlahPlong} with CodeItemBOM:`, bomMaterialCode);

                                expandedRows.push({
                                    ...row,
                                    plongIndex: i + 1, // Index untuk tracking duplikasi
                                    originalIndex: index,
                                    CodeItemBOM: bomMaterialCode, // Material Code BOM untuk duplikat ini
                                    MaterialCodeBOM: bomMaterialCode // Alias untuk konsistensi
                                });
                            }
                        });
                        console.log('=== PLONG DATA DUPLICATION ===');
                        console.log('Original rows:', selectedRows.length);
                        console.log('Expanded rows:', expandedRows.length);
                        console.log('Expanded data:', expandedRows);
                        // Debug: Tampilkan Material Code BOM untuk setiap duplikat
                        expandedRows.forEach((row, idx) => {
                            console.log(`Duplikat ${idx + 1}:`, {
                                WODocNo: row.WODocNo,
                                MaterialCode: row.MaterialCode,
                                CodeItemBOM: row.CodeItemBOM,
                                plongIndex: row.plongIndex
                            });
                        });
                    } else {
                        expandedRows = selectedRows;
                    }

                    const planningData = {
                        data: expandedRows.map((row, index) => {
                            console.log('=== ROW DATA ANALYSIS ===');
                            console.log('Row index:', index);
                            console.log('Full row object:', row);
                            console.log('Row keys:', Object.keys(row));
                            console.log('MaterialName value:', row.MaterialName);
                            console.log('MaterialName type:', typeof row.MaterialName);
                            console.log('MaterialName length:', row.MaterialName ? row.MaterialName.length : 'null/undefined');
                            console.log('MaterialName trimmed:', row.MaterialName ? row.MaterialName.trim() : 'null/undefined');
                            console.log('---');

                            // Khusus untuk WOP, quantity diset 500 PCS
                            let finalQuantity = row.Quantity;
                            if (row.WODocNo && row.WODocNo.startsWith('WOP')) {
                                console.log(
                                    `WOP detected for ${row.WODocNo}, changing quantity from ${row.Quantity} to 500`
                                    );
                                finalQuantity = 500;
                            }

                            // Validasi dan sanitasi data untuk setiap row
                            const sanitizedRow = {
                                MaterialCode: String(row.MaterialCode || row.materialCode || '')
                                    .trim(),
                                MaterialName: String(row.MaterialName || row.materialName || '')
                                    .trim(),
                                Quantity: Math.max(1,
                                finalQuantity), // Gunakan finalQuantity yang sudah diproses
                                DeliveryDate: String(row.DeliveryDate || row.deliveryDate ||
                                    startDate).trim(),
                                WODocNo: String(row.WODocNo || row.woDocNo || '').trim(),
                                SODocNo: String(row.SODocNo || row.soDocNo || '').trim(),
                                order: Math.max(1, parseInt(orders[index]) || 0),
                                processes: String(processes || '')
                            .trim(), // Backend mengharapkan 'processes'
                                Estimation: Math.max(1, finalQuantity), // Durasi dalam jam
                                // Untuk PLONG: Machine dan MachineCode dibiarkan null/kosong
                                // Untuk CETAK dan proses lain: ambil dari mapping mesin (m_ctk)
                                Machine: (processes === 'PLONG' || processes === 'PLG') ? null : 'Default Machine',
                                UP: parseInt(row.Up),
                                Unit: String(row.Unit).trim(),
                                BOM: 'Default BOM',
                                // 🔧 TAMBAHAN: Data untuk kolom yang kosong di database
                                Process: String(processes || '')
                            .trim(), // Nama proses (CTK, PTG, dll)
                                Department: 'PRODUCTION', // Default department
                                MaterialName: String(row.MaterialName || row.materialName || '')
                                    .trim(), // Pastikan terkirim
                                // 🔧 TAMBAHAN: Data tambahan untuk mapping
                                ProcessType: String(processes || '')
                            .trim(), // Tipe proses untuk mapping
                                MachineCode: (processes === 'PLONG' || processes === 'PLG') ? null : 'CTK-001', // Untuk PLONG: null, untuk CETAK: akan diambil dari mapping mesin (m_ctk)
                                CapacityPerHour: 1000, // Default capacity
                                // 🔧 TAMBAHAN: Data untuk timeline calculation
                                StartTime: '08:00:00', // Default start time
                                EndTime: null, // Akan dihitung di backend
                                Duration: Math.max(1, finalQuantity) /
                                1000, // Default duration calculation
                                Priority: Math.max(1, parseInt(orders[index]) ||
                                0), // Priority order
                                Status: 'PLANNED', // Default status
                                // Untuk PLONG: tambahkan Material Code BOM
                                CodeItemBOM: (processes === 'PLONG' || processes === 'PLG') ? (row.CodeItemBOM || row.MaterialCodeBOM || null) : null
                            };

                            // Log setiap row yang diproses
                            console.log(`Processing row ${index + 1}:`, sanitizedRow);
                            // Log khusus untuk PLONG dengan CodeItemBOM
                            if ((processes === 'PLONG' || processes === 'PLG') && sanitizedRow.CodeItemBOM) {
                                console.log(`PLONG row ${index + 1} - CodeItemBOM:`, sanitizedRow.CodeItemBOM);
                            }
                            if (row.WODocNo && row.WODocNo.startsWith('WOP')) {
                                console.log(
                                    `⚠️ WOP Quantity Adjustment: ${row.Quantity} → ${finalQuantity} PCS`
                                    );
                            }

                            // 🔧 LOGGING: Pastikan kolom yang dibutuhkan database terisi
                            console.log(`🔧 Database Fields Check for Row ${index + 1}:`, {
                                Process: sanitizedRow.Process,
                                Department: sanitizedRow.Department,
                                MaterialName: sanitizedRow.MaterialName,
                                MaterialCode: sanitizedRow.MaterialCode,
                                Machine: sanitizedRow.Machine,
                                ProcessType: sanitizedRow.ProcessType,
                                Status: sanitizedRow.Status
                            });

                            // 🔧 VALIDATION: Pastikan MaterialName tidak kosong
                            if (!sanitizedRow.MaterialName || sanitizedRow.MaterialName.trim() ===
                                '') {
                                console.error(
                                    `❌ ERROR: MaterialName kosong untuk row ${index + 1}!`);
                                console.error('  Original row.MaterialName:', row.MaterialName);
                                console.error('  Original row.materialName:', row.materialName);
                                console.error('  Sanitized MaterialName:', sanitizedRow
                                    .MaterialName);
                            } else {
                                console.log(
                                    `✅ MaterialName OK untuk row ${index + 1}: "${sanitizedRow.MaterialName}"`
                                    );
                            }

                            return sanitizedRow;
                        }),
                        start_date: String(startDate || '').trim(),
                        end_date: String(endDate || '').trim(),
                        processes: String(processes || '').trim(),
                        selectedProcess: String(processes || '').trim(),
                        total_items: Math.max(0, expandedRows.length) // Gunakan expandedRows untuk PLONG
                    };

                    console.log('=== DATA YANG AKAN DIKIRIM KE BACKEND ===');
                    console.log('JSON String:', JSON.stringify(planningData, null, 2));
                    console.log('data length:', planningData.data.length);
                    console.log('data type:', typeof planningData.data);
                    console.log('data is array:', Array.isArray(planningData.data));
                    console.log('Selected process:', planningData.selectedProcess);
                    console.log('Start date:', planningData.start_date);
                    console.log('End date:', planningData.end_date);

                    // 🔧 LOGGING: Validasi data untuk database
                    console.log('=== 🔧 DATABASE FIELDS VALIDATION ===');
                    planningData.data.forEach((item, index) => {
                        console.log(`Item ${index + 1} Database Fields:`, {
                            MaterialCode: item.MaterialCode,
                            MaterialName: item.MaterialName,
                            Process: item.Process,
                            Department: item.Department,
                            Machine: item.Machine,
                            Status: item.Status,
                            ProcessType: item.ProcessType
                        });
                    });
                    console.log('=== END DATABASE FIELDS VALIDATION ===');

                    // Validasi jumlah item yang dikirim
                    // Untuk PLONG: skip validasi karena data sudah di-duplikasi sesuai JumlahPlong
                    // expandedRows.length sudah sesuai dengan planningData.data.length
                    if (processes !== 'PLONG' && processes !== 'PLG') {
                        if (planningData.data.length !== selectedRows.length) {
                            console.error('DATA MISMATCH DETECTED!');
                            console.error('selectedRows.length:', selectedRows.length);
                            console.error('planningData.data.length:', planningData.data.length);
                            console.error('selectedRows:', selectedRows);
                            console.error('planningData.data:', planningData.data);

                            Swal.fire({
                                icon: 'error',
                                title: 'Data Mismatch',
                                text: `Jumlah item tidak sesuai! Dipilih: ${selectedRows.length}, Dikirim: ${planningData.data.length}`,
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    } else {
                        // Untuk PLONG: validasi menggunakan expandedRows.length
                        console.log('=== PLONG VALIDATION ===');
                        console.log('selectedRows.length:', selectedRows.length);
                        console.log('expandedRows.length:', expandedRows.length);
                        console.log('planningData.data.length:', planningData.data.length);

                        if (planningData.data.length !== expandedRows.length) {
                            console.error('PLONG Data Mismatch!');
                            console.error('expandedRows.length:', expandedRows.length);
                            console.error('planningData.data.length:', planningData.data.length);

                            Swal.fire({
                                icon: 'error',
                                title: 'Data Mismatch',
                                text: `Jumlah item tidak sesuai! Expanded: ${expandedRows.length}, Dikirim: ${planningData.data.length}`,
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    }

                    // Log detail setiap item
                    console.log('=== DETAIL SETIAP ITEM ===');
                    planningData.data.forEach((item, index) => {
                        console.log(`Item ${index + 1}:`, {
                            MaterialCode: item.MaterialCode,
                            MaterialName: item.MaterialName,
                            Quantity: item.Quantity,
                            WODocNo: item.WODocNo,
                            SODocNo: item.SODocNo,
                            processes: item.processes,
                            order: item.order,
                            UP: item.UP,
                            Unit: item.Unit
                        });
                    });

                    // Log selectedRows untuk comparison
                    console.log('=== SELECTED ROWS COMPARISON ===');
                    selectedRows.forEach((row, index) => {
                        console.log(`Selected Row ${index + 1}:`, {
                            MaterialCode: row.MaterialCode,
                            MaterialName: row.MaterialName,
                            Quantity: row.Quantity,
                            WODocNo: row.WODocNo,
                            SODocNo: row.SODocNo,
                            UP: row.Up,
                            Unit: row.Unit
                        });
                    });
                    console.log('==========================================');

                    // Validasi data sebelum dikirim
                    if (!planningData.data || planningData.data.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Error',
                            text: 'Tidak ada item yang dipilih untuk planning!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    if (!planningData.processes) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Error',
                            text: 'Proses tidak dipilih!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Validasi setiap item dalam data
                    const invalidItems = planningData.data.filter(item => {
                        return !item.MaterialCode || !item.MaterialName || !item.WODocNo || !item
                            .SODocNo || !item.Process || !item.Department;
                    });

                    if (invalidItems.length > 0) {
                        console.error('Invalid items found:', invalidItems);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Error',
                            text: `Ditemukan ${invalidItems.length} item dengan data tidak lengkap. Silakan cek console untuk detail.`,
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Validasi tipe data
                    const hasInvalidTypes = planningData.data.some(item => {
                        return typeof item.MaterialCode !== 'string' ||
                            typeof item.MaterialName !== 'string' ||
                            typeof item.WODocNo !== 'string' ||
                            typeof item.SODocNo !== 'string' ||
                            typeof item.Quantity !== 'number' ||
                            typeof item.order !== 'number';
                    });

                    if (hasInvalidTypes) {
                        console.error('Invalid data types found in planPerItem');
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Error',
                            text: 'Tipe data tidak sesuai. Silakan cek console untuk detail.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Kirim data ke submit-plan-first untuk mendapatkan response dengan data planning yang sudah dihitung
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route('plan-first.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(planningData)
                        })
                        .then(async response => {
                            let res;
                            try {
                                res = await response.json();
                            } catch (e) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Data error. Cek console/log atau coba hubungi Team IT.',
                                    showConfirmButton: true
                                });
                                return;
                            }

                            console.log('Response dari submit-plan-first:', res);

                            // Jika ada preview data, tampilkan modal preview
                            if (res.success === false && res.preview === true && res.data) {
                                console.log('Received preview response:', res);
                                console.log('=== BACKEND RESPONSE ANALYSIS ===');
                                console.log('Response data type:', typeof res.data);
                                console.log('Response data keys:', Object.keys(res.data || {}));
                                console.log('Response data.planPerItem type:', typeof res.data
                                    .planPerItem);
                                console.log('Response data.planPerItem keys:', Object.keys(res.data
                                    .planPerItem || {}));
                                console.log('Response data.planPerItem length:', Array.isArray(res.data
                                        .planPerItem) ? res.data.planPerItem.length :
                                    'Not an array');

                                if (res.data.planPerItem && typeof res.data.planPerItem === 'object') {
                                    const itemCount = Array.isArray(res.data.planPerItem) ? res.data
                                        .planPerItem.length : Object.keys(res.data.planPerItem).length;
                                    console.log('Total items in planPerItem:', itemCount);

                                    // Cek apakah ada item yang hilang dari backend
                                    if (itemCount !== planningData.data.length) {
                                        console.warn('⚠️ ITEM COUNT MISMATCH DETECTED!');
                                        console.warn('Items sent to backend:', planningData.data
                                        .length);
                                        console.warn('Items returned from backend:', itemCount);
                                        console.warn('Missing items count:', planningData.data.length -
                                            itemCount);

                                        // Tampilkan warning di UI
                                        const missingCount = planningData.data.length - itemCount;
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Beberapa Item Tidak Diproses',
                                            html: `<p><strong>${missingCount} item</strong> tidak dapat diproses oleh backend.</p>
                                                   <p>Kemungkinan penyebab:</p>
                                                   <ul style="text-align: left;">
                                                       <li>Mapping mesin belum ada di tb_mapping_items</li>
                                                       <li>Data item tidak lengkap</li>
                                                       <li>Error di backend processing</li>
                                                   </ul>
                                                   <p>Silakan cek console untuk detail item yang hilang.</p>`,
                                            confirmButtonText: 'OK'
                                        });

                                        // Log detail item yang hilang
                                        console.log('=== MISSING ITEMS ANALYSIS ===');
                                        const backendItemKeys = Object.keys(res.data.planPerItem || {});
                                        const frontendItemKeys = planningData.data.map(item =>
                                            `${item.MaterialCode}_${item.WODocNo}`
                                        );

                                        console.log('Frontend item keys:', frontendItemKeys);
                                        console.log('Backend item keys:', backendItemKeys);

                                        const missingItems = planningData.data.filter(item => {
                                            const itemKey =
                                                `${item.MaterialCode}_${item.WODocNo}`;
                                            return !backendItemKeys.includes(itemKey);
                                        });

                                        console.log('Missing items details:', missingItems);
                                        missingItems.forEach((item, index) => {
                                            console.log(`Missing Item ${index + 1}:`, {
                                                MaterialCode: item.MaterialCode,
                                                MaterialName: item.MaterialName,
                                                WODocNo: item.WODocNo,
                                                SODocNo: item.SODocNo,
                                                Quantity: item.Quantity,
                                                UP: item.UP,
                                                Unit: item.Unit
                                            });
                                        });
                                        console.log('=== END MISSING ITEMS ANALYSIS ===');
                                    }

                                    if (Array.isArray(res.data.planPerItem)) {
                                        res.data.planPerItem.forEach((item, index) => {
                                            console.log(`Backend Item ${index + 1}:`, item);
                                        });
                                    } else {
                                        Object.entries(res.data.planPerItem).forEach(([key, item],
                                            index) => {
                                            console.log(`Backend Item ${index + 1} (${key}):`,
                                                item);
                                        });
                                    }
                                }
                                console.log('=== END BACKEND RESPONSE ANALYSIS ===');

                                // Validasi struktur data
                                if (!res.data.planPerItem) {
                                    console.error('planPerItem is missing from response data');
                                    console.log('Available data keys:', Object.keys(res.data || {}));

                                    // Jika tidak ada planPerItem dari backend, gunakan data yang dikirim user
                                    console.log('Using user data instead of backend data');
                                    const userData = {
                                        planPerItem: planningData
                                            .data, // Data array dari user (format lama)
                                        selectedProcess: planningData.selectedProcess,
                                        startTime: planningData.start_date
                                    };

                                    // Tutup modal planning
                                    $('#planningModal').modal('hide');

                                    // Generate HTML preview dari data user
                                    try {
                                        console.log('Using user data for preview:', userData);
                                        const previewHtml = await generatePreviewHtml(userData);
                                        $('#planPreviewModalBody').html(previewHtml);
                                    } catch (error) {
                                        console.error('Error generating preview HTML from user data:',
                                            error);
                                        $('#planPreviewModalBody').html(`
                                        <div class="alert alert-danger">
                                            <h5><i class="fas fa-exclamation-triangle"></i> Error Generating Preview</h5>
                                            <p><strong>Error:</strong> ${error.message}</p>
                                            <p><strong>User Data:</strong></p>
                                            <pre>${JSON.stringify(userData, null, 2)}</pre>
                                        </div>
                                    `);
                                    }

                                    // Tampilkan modal preview
                                    setTimeout(() => {
                                        try {
                                            $('#planPreviewModal').modal('show');
                                            console.log('Preview modal show triggered');

                                            // Inisialisasi tooltip setelah modal ditampilkan
                                            setTimeout(() => {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            }, 200);
                                        } catch (error) {
                                            console.error('Error showing preview modal:',
                                                error);
                                        }
                                    }, 100);

                                    // Simpan data untuk save
                                    window.previewData = userData;
                                    console.log('Preview data saved to window.previewData');
                                    return;
                                }

                                console.log('planPerItem structure:', res.data.planPerItem);
                                console.log('planPerItem type:', typeof res.data.planPerItem);
                                console.log('planPerItem keys aa:', Object.keys(res.data.planPerItem ||
                                    {}));

                                // Cek item yang belum punya mapping mesin
                                // Untuk PLONG: skip validasi mapping mesin karena code_machine harus null
                                try {
                                    console.log('=== CHECKING MACHINE MAPPING ===');
                                    console.log('Current process:', planningData.selectedProcess);

                                    let missingMappingItems = [];

                                    // Untuk PLONG, skip validasi mapping mesin
                                    if (planningData.selectedProcess === 'PLONG' || planningData.selectedProcess === 'PLG') {
                                        console.log('PLONG process detected - skipping machine mapping validation');
                                        // Tidak perlu cek mapping untuk PLONG karena code_machine harus null
                                        missingMappingItems = []; // Set empty untuk PLONG
                                    } else {
                                        const itemsForCheck = Object.values(res.data.planPerItem || {});
                                        console.log('Items to check:', itemsForCheck);
                                        console.log('Total items to check:', itemsForCheck.length);

                                        missingMappingItems = itemsForCheck.filter(it => {
                                            console.log('Checking item:', it);
                                            console.log('Item Machine field:', it.Machine);
                                            console.log('Item Machine type:', typeof it.Machine);
                                            console.log('Item Machine trimmed:', String(it.Machine || '').trim());
                                            console.log('Item MaterialCode:', it.MaterialCode || it.CodeItem);
                                            console.log('Item WODocNo:', it.WODocNo);

                                            // Cek apakah Machine kosong, null, undefined, atau default value
                                            const machineValue = String(it.Machine || '').trim();
                                            const hasMachine = machineValue !== '' &&
                                                              machineValue !== 'null' &&
                                                              machineValue !== 'undefined' &&
                                                              machineValue !== 'Default Machine' &&
                                                              machineValue !== 'CTK-001'; // Default fallback

                                            console.log('Has machine mapping:', hasMachine);
                                            console.log('Machine value:', machineValue);

                                            if (!hasMachine) {
                                                console.warn('⚠️ Item tanpa mapping:', {
                                                    MaterialCode: it.MaterialCode || it.CodeItem,
                                                    MaterialName: it.MaterialName,
                                                    WODocNo: it.WODocNo,
                                                    Machine: it.Machine
                                                });
                                            }

                                            return !hasMachine;
                                        });

                                        console.log('Missing mapping items:', missingMappingItems);
                                        console.log('Missing mapping count:', missingMappingItems.length);

                                        if (missingMappingItems.length > 0) {
                                            console.warn('⚠️ DITEMUKAN ITEM TANPA MAPPING:', missingMappingItems);
                                        }
                                    }

                                    // Simpan info item tanpa mapping untuk styling di preview
                                    if (missingMappingItems.length > 0) {
                                        console.log('Items without machine mapping found:',
                                            missingMappingItems);

                                        // Tampilkan alert dengan detail item yang tidak ada mapping
                                        const missingItemsList = missingMappingItems.map((item, index) => {
                                            const materialCode = item.MaterialCode || item.CodeItem || item.code_item || '-';
                                            const materialName = item.MaterialName || item.material_name || '-';
                                            const woDocNo = item.WODocNo || item.wo_docno || '-';
                                            return `${index + 1}. <strong>${materialCode}</strong> - ${materialName}<br>&nbsp;&nbsp;&nbsp;&nbsp;WO: ${woDocNo}`;
                                        }).join('<br>');

                                        // Tambahkan flag ke data untuk styling di preview
                                        missingMappingItems.forEach(item => {
                                            item.hasNoMapping = true;
                                            item.mappingWarning =
                                                `⚠️ Belum ada mapping mesin untuk proses ${planningData.selectedProcess || ''}`;
                                        });

                                        // Tampilkan alert sebelum preview
                                        await Swal.fire({
                                            icon: 'warning',
                                            title: 'Item Tanpa Mapping Mesin',
                                            html: `
                                                <p><strong>${missingMappingItems.length} item</strong> belum memiliki mapping mesin di tb_mapping_items:</p>
                                                <div style="text-align: left; max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; border: 1px solid #ffc107;">
                                                    ${missingItemsList}
                                                </div>
                                                <p style="margin-top: 15px; color: #856404;"><small><i class="fas fa-info-circle"></i> Silakan lengkapi mapping di tb_mapping_items terlebih dahulu. Preview akan tetap ditampilkan dengan item-item ini ditandai.</small></p>
                                            `,
                                            confirmButtonText: 'Lanjutkan ke Preview',
                                            width: '700px',
                                            allowOutsideClick: false
                                        });
                                    } else {
                                        console.log(
                                            'All items have machine mapping, proceeding with preview'
                                            );
                                    }
                                } catch (e) {
                                    console.error('Failed to check machine mapping presence:', e);
                                    console.error('Error details:', e.message, e.stack);
                                }

                                // Tutup modal planning
                                $('#planningModal').modal('hide');

                                // Generate HTML preview dari data JSON yang sudah diproses controller
                                try {
                                    console.log('Response data structure:', res.data);
                                    console.log('Response data type:', typeof res.data);
                                    console.log('Response data keys:', Object.keys(res.data || {}));

                                    const previewHtml = await generatePreviewHtml(res.data);
                                    $('#planPreviewModalBody').html(previewHtml);
                                } catch (error) {
                                    console.error('Error generating preview HTML:', error);
                                    $('#planPreviewModalBody').html(`
                                    <div class="alert alert-danger">
                                        <h5><i class="fas fa-exclamation-triangle"></i> Error Generating Preview</h5>
                                        <p><strong>Error:</strong> ${error.message}</p>
                                        <p><strong>Response Data:</strong></p>
                                        <pre>${JSON.stringify(res.data, null, 2)}</pre>
                                    </div>
                                `);
                                }

                                // Tampilkan modal preview
                                setTimeout(() => {
                                    try {
                                        $('#planPreviewModal').modal('show');
                                        console.log('Preview modal show triggered');

                                        // Inisialisasi tooltip setelah modal ditampilkan
                                        setTimeout(() => {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        }, 200);
                                    } catch (error) {
                                        console.error('Error showing preview modal:', error);
                                    }
                                }, 100);

                                // Simpan data untuk save
                                window.previewData = res.data;

                                // Tambahkan data timeline yang sudah dihitung dengan benar
                                if (res.data.planPerItem) {
                                    console.log('=== ENHANCING PREVIEW DATA WITH TIMELINE ===');

                                    // Ambil data timeline yang sudah dihitung
                                    const timelineData = await generateIntegratedProcessTable(
                                        filterAndSortItemsByProcess(res.data.planPerItem, res.data
                                            .selectedProcess || 'CETAK'),
                                        res.data.startDate || $('#start_date').val(),
                                        res.data.selectedProcess || 'CETAK'
                                    );

                                    // Simpan data timeline ke previewData
                                    window.previewData.timelineData = timelineData;
                                    window.previewData.calculatedItems = filterAndSortItemsByProcess(
                                        res.data.planPerItem,
                                        res.data.selectedProcess || 'CETAK'
                                    );

                                    console.log('Timeline data enhanced:', {
                                        hasTimelineData: !!window.previewData.timelineData,
                                        calculatedItemsCount: window.previewData.calculatedItems
                                            .length,
                                        calculatedItems: window.previewData.calculatedItems
                                    });
                                }

                                console.log('Preview data saved to window.previewData');
                                return;
                            }

                            // Handle response lainnya sesuai kebutuhan
                            if (res.success === true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message || 'Plan berhasil dibuat!',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            } else {
                                // Jika tidak ada response yang sesuai, gunakan data user untuk preview
                                console.log('No valid response, using user data for preview');
                                const userData = {
                                    planPerItem: planningData
                                        .data, // Data array dari user (format lama)
                                    selectedProcess: planningData.selectedProcess,
                                    startTime: planningData.start_date
                                };

                                // Tutup modal planning
                                $('#planningModal').modal('hide');

                                // Generate HTML preview dari data user
                                try {
                                    console.log('Using user data for preview:', userData);
                                    const previewHtml = await generatePreviewHtml(userData);
                                    $('#planPreviewModalBody').html(previewHtml);

                                    // Tampilkan modal preview
                                    setTimeout(() => {
                                        try {
                                            $('#planPreviewModal').modal('show');
                                            console.log('Preview modal show triggered');

                                            // Inisialisasi tooltip setelah modal ditampilkan
                                            setTimeout(() => {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            }, 200);
                                        } catch (error) {
                                            console.error('Error showing preview modal:',
                                                error);
                                        }
                                    }, 100);

                                    // Simpan data untuk save
                                    window.previewData = userData;
                                    console.log('Preview data saved to window.previewData');

                                } catch (error) {
                                    console.error('Error generating preview HTML from user data:',
                                        error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan saat membuat preview!',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Gagal',
                                text: error.message || 'Terjadi kesalahan saat submit data!',
                                icon: 'error'
                            });
                        });
                });

                // Simpan dari preview
                $('#savePlanFromPreview').on('click', function() {
                    console.log('=== SAVE TO DATABASE START ===');

                    // Validasi data preview
                    if (!window.previewData) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Tidak Ditemukan',
                            text: 'Data preview tidak tersedia. Silakan buat preview terlebih dahulu.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    console.log('Preview data available:', window.previewData);

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan ke Database...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AMBIL DATA LANGSUNG DARI WINDOW.PREVIEWDATA (YANG SUDAH BENAR DARI DATABASE)
                    console.log('=== MULAI AMBIL DATA DARI PREVIEW DATA ===');

                    // Debug: cek struktur data
                    console.log('window.previewData structure:', {
                        hasPlanPerItem: !!window.previewData.planPerItem,
                        planPerItemType: typeof window.previewData.planPerItem,
                        planPerItemKeys: window.previewData.planPerItem ? Object.keys(window.previewData
                            .planPerItem) : [],
                        selectedProcess: window.previewData.selectedProcess,
                        startDate: window.previewData.startDate
                    });

                    // Ambil data dari planPerItem yang sudah benar dari database
                    let tableData = [];
                    if (window.previewData.planPerItem && typeof window.previewData.planPerItem === 'object') {
                        // Convert object ke array
                        tableData = Object.values(window.previewData.planPerItem);
                        console.log('Data dari planPerItem (database):', tableData);
                    } else {
                        console.error('planPerItem tidak valid atau kosong');
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Tidak Valid',
                            text: 'Data preview tidak valid. Silakan buat preview ulang.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    console.log('=== DATA YANG AKAN DISAVE ===');
                    console.log('Total items:', tableData.length);
                    console.log('Table data:', tableData);

                    if (tableData.length === 0) {
                        console.error('Tidak ada data untuk disave');
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Kosong',
                            text: 'Tidak ada data yang bisa disimpan.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Ambil data PPOS dari localStorage
                    const pposSchedules = JSON.parse(localStorage.getItem('ppos_schedules') || '{}');
                    console.log('PPOS Schedules from localStorage:', pposSchedules);

                    // Siapkan data untuk save dengan data dari database
                    const saveData = {
                        data: tableData,
                        selectedProcess: window.previewData.selectedProcess || 'CETAK',
                        start_date: window.previewData.startDate || $('#start_date').val(),
                        end_date: null,
                        processes: window.previewData.selectedProcess || 'CETAK',
                        save_to_database: true,
                        ppos_schedules: pposSchedules // Tambahkan data PPOS
                    };

                    // Hitung end_date dari data database
                    if (tableData.length > 0) {
                        const lastTableItem = tableData[tableData.length - 1];
                        if (lastTableItem.EndJam) {
                            saveData.end_date = lastTableItem.EndJam;
                            console.log('Using EndJam from database:', lastTableItem.EndJam);
                        } else {
                            // Fallback sederhana
                            const startDate = new Date(saveData.start_date);
                            const endDate = new Date(startDate.getTime() + (24 * 60 * 60 * 1000)); // +1 hari
                            saveData.end_date = endDate.toISOString().split('T')[0];
                            console.log('Using fallback end_date:', saveData.end_date);
                        }
                    }

                    console.log('Final save data:', saveData);

                    // Kirim ke backend untuk save
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route('plan-first.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(saveData)
                        })
                        .then(async response => {
                            let res;
                            try {
                                res = await response.json();
                            } catch (e) {
                                throw new Error('Gagal parse response dari server');
                            }

                            console.log('Save response:', res);

                            if (res.success === true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Disimpan!',
                                    text: res.message ||
                                        'Data planning berhasil disimpan ke database.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Tutup modal preview
                                    $('#planPreviewModal').modal('hide');
                                    // route to plan-first-production
                                    window.location.href =
                                        '{{ route('process.plan-first-prd') }}';
                                    // Refresh halaman untuk menampilkan data yang baru disimpan
                                    // window.location.reload();
                                });
                            } else {
                                throw new Error(res.message || 'Gagal menyimpan data');
                            }
                        })
                        .catch(error => {
                            console.error('Error saving to database:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: error.message ||
                                    'Terjadi kesalahan saat menyimpan ke database.',
                                confirmButtonText: 'OK'
                            });
                        });
                });

                // FUNGSI UNTUK MENGAMBIL DATA LANGSUNG DARI TABEL PREVIEW DENGAN ID SPESIFIK
                function extractDataFromPreviewTable() {
                    console.log('=== EXTRACTING DATA FROM PREVIEW TABLE WITH SPECIFIC IDS ===');

                    // Cari tabel dengan ID yang spesifik
                    const table = document.querySelector('#previewTimelineTableBody');
                    if (!table) {
                        console.error('Table body with ID previewTimelineTableBody not found');
                        console.error('Available tables:', document.querySelectorAll('table'));
                        console.error('Available tbody:', document.querySelectorAll('tbody'));
                        return [];
                    }

                    console.log('Table found:', table);
                    console.log('Table HTML structure:', table.outerHTML.substring(0, 500) + '...');

                    const rows = table.querySelectorAll('tr');
                    console.log('Found table rows:', rows.length);

                    const extractedData = [];

                    rows.forEach((row, index) => {
                        console.log(`=== PROCESSING ROW ${index + 1} ===`);
                        console.log('Row HTML:', row.outerHTML);

                        // Skip warning rows dan header
                        if (row.classList.contains('table-warning') || row.querySelector('th')) {
                            console.log(`Skipping row ${index + 1} (warning/header)`);
                            return;
                        }

                        try {
                            // Extract data menggunakan ID yang spesifik
                            const rowData = {
                                // No
                                rowNumber: document.querySelector(`#row-${index + 1}-no`)?.textContent
                                .trim() || (index + 1),
                                // WO DocNo
                                WODocNo: document.querySelector(`#row-${index + 1}-wodocno`)?.textContent
                                    .trim() || '-',
                                // Material Code
                                MaterialCode: document.querySelector(`#row-${index + 1}-materialcode`)
                                    ?.textContent.trim() || '-',
                                // Material Name
                                MaterialName: document.querySelector(`#row-${index + 1}-materialname`)
                                    ?.textContent.trim() || '-',
                                // Quantity
                                Quantity: parseInt(document.querySelector(`#row-${index + 1}-quantity`)
                                    ?.textContent.trim()) || 0,
                                // Start Time (format: DD/MM/YYYY, HH.mm.ss)
                                StartTime: document.querySelector(`#row-${index + 1}-starttime`)
                                    ?.textContent.trim() || '',
                                // End Time (format: DD/MM/YYYY, HH.mm.ss)
                                EndTime: document.querySelector(`#row-${index + 1}-endtime`)?.textContent
                                    .trim() || '',
                                // Duration
                                Duration: parseFloat(document.querySelector(`#row-${index + 1}-duration`)
                                    ?.textContent.replace(' jam', '')) || 0,
                                // Machine
                                Machine: document.querySelector(`#row-${index + 1}-machine`)?.textContent
                                    .trim() || '-',
                                // UP
                                UP: parseInt(document.querySelector(`#row-${index + 1}-up`)?.textContent
                                    .trim()) || 1,
                                // Status
                                Status: document.querySelector(`#row-${index + 1}-status`)?.textContent
                                    .trim() || '-'
                            };

                            console.log(`Raw data from row ${index + 1}:`, rowData);

                            // Convert StartTime dan EndTime ke format ISO untuk database
                            if (rowData.StartTime && rowData.EndTime) {
                                try {
                                    // Parse format "DD/MM/YYYY, HH.mm.ss" ke Date object
                                    const startParts = rowData.StartTime.split(', ')[0].split('/');
                                    const startTimeParts = rowData.StartTime.split(', ')[1].split('.');
                                    const startDate = new Date(
                                        parseInt(startParts[2]), // year
                                        parseInt(startParts[1]) - 1, // month (0-based)
                                        parseInt(startParts[0]), // day
                                        parseInt(startTimeParts[0]), // hour
                                        parseInt(startTimeParts[1]), // minute
                                        parseInt(startTimeParts[2]) // second
                                    );

                                    const endParts = rowData.EndTime.split(', ')[0].split('/');
                                    const endTimeParts = rowData.EndTime.split(', ')[1].split('.');
                                    const endDate = new Date(
                                        parseInt(endParts[2]), // year
                                        parseInt(endParts[1]) - 1, // month (0-based)
                                        parseInt(endParts[0]), // day
                                        parseInt(endTimeParts[0]), // hour
                                        parseInt(endTimeParts[1]), // minute
                                        parseInt(endTimeParts[2]) // second
                                    );

                                    // Convert ke ISO string untuk database
                                    rowData.StartJam = startDate.toISOString();
                                    rowData.EndJam = endDate.toISOString();

                                    console.log(`Row ${index + 1} time conversion:`, {
                                        original: {
                                            StartTime: rowData.StartTime,
                                            EndTime: rowData.EndTime
                                        },
                                        converted: {
                                            StartJam: rowData.StartJam,
                                            EndJam: rowData.EndJam
                                        }
                                    });

                                } catch (timeError) {
                                    console.error(`Error converting time for row ${index + 1}:`, timeError);
                                    console.error('Time parsing error details:', timeError.message);
                                    // Fallback ke format sederhana
                                    rowData.StartJam = new Date().toISOString();
                                    rowData.EndJam = new Date().toISOString();
                                }
                            }

                            // Tambahkan data yang diperlukan untuk backend
                            rowData.Estimation = rowData.Duration;
                            rowData.Unit = 'PCS';
                            rowData.BOM = 'Default BOM';
                            rowData.Proses = 'CETAK'; // Default process

                            extractedData.push(rowData);
                            console.log(`Extracted row ${index + 1}:`, rowData);

                        } catch (rowError) {
                            console.error(`Error extracting row ${index + 1}:`, rowError);
                        }
                    });

                    console.log('=== EXTRACTION COMPLETE ===');
                    console.log('Total extracted rows:', extractedData.length);
                    console.log('Extracted data:', extractedData);

                    return extractedData;
                }

                // Fungsi helper untuk validasi urutan
                function isSequential(arr) {
                    const intArr = arr.map(Number);
                    intArr.sort((a, b) => a - b);
                    for (let i = 0; i < intArr.length - 1; i++) {
                        if (intArr[i] + 1 !== intArr[i + 1]) {
                            return false;
                        }
                    }
                    return true;
                }

                // Fungsi untuk generate HTML preview yang hanya menampilkan proses yang dipilih
                async function generatePreviewHtml(data) {
                    console.log('generatePreviewHtml called with data:', data);

                    try {
                        // Validasi data input
                        if (!data || typeof data !== 'object') {
                            console.error('Invalid data parameter:', data);
                            throw new Error('Data parameter is invalid');
                        }

                        if (!data.planPerItem || typeof data.planPerItem !== 'object') {
                            console.error('planPerItem is missing or invalid:', data.planPerItem);
                            throw new Error('planPerItem data is missing or invalid');
                        }

                        // Ambil proses yang dipilih dari data atau dari URL parameter
                        const urlParams = new URLSearchParams(window.location.search);
                        const selectedProcess = urlParams.get('process') || data.selectedProcess || 'PROSES';

                        console.log('Selected process:', selectedProcess);
                        console.log('Data structure:', {
                            hasPlanPerItem: !!data.planPerItem,
                            planPerItemKeys: Object.keys(data.planPerItem || {}),
                            planPerItemType: typeof data.planPerItem,
                            selectedProcess: selectedProcess
                        });

                        let html = `
                            <div class="timeline-content">
                                <div class="items-container">
                        `;

                        // Filter dan urutkan item berdasarkan delivery date dan proses yang dipilih
                        // Handle struktur data yang berbeda dari backend
                        let planPerItemData = data.planPerItem;

                        // Jika backend mengirim data dengan struktur { key: item }, pastikan format yang benar
                        if (planPerItemData && typeof planPerItemData === 'object' && !Array.isArray(
                                planPerItemData)) {
                            // Backend mengirim: { "TC.0021.0314_WOT-250801-0001": {...}, "TC.0021.0314_WOT-250801-0002": {...} }
                            // Data sudah dalam format yang diharapkan oleh filterAndSortItemsByProcess
                            console.log('Backend data format detected (object with keys)');
                            console.log('Backend data keys:', Object.keys(planPerItemData));
                            console.log('Sample backend data:', Object.values(planPerItemData)[0]);
                        } else if (Array.isArray(planPerItemData)) {
                            // Data dari user fallback (array format)
                            console.log('User data format detected (array)');
                            console.log('User data length:', planPerItemData.length);
                        }

                        const filteredAndSortedItems = filterAndSortItemsByProcess(planPerItemData, selectedProcess);

                        console.log('Filtered and sorted items:', filteredAndSortedItems);

                        // Generate item views hanya untuk proses yang dipilih dalam 1 tabel terintegrasi
                        let itemViewHtml = await generateIntegratedProcessTable(filteredAndSortedItems, data
                            .startTime || data.start_date || '08:00', selectedProcess);

                        html += itemViewHtml;

                        // Update summary data
                        const totalItems = filteredAndSortedItems.length;
                        const totalDuration = filteredAndSortedItems.reduce((total, item) => {
                            return total + (item.totalDuration || 0);
                        }, 0);

                        // Update summary data menggunakan string replacement
                        html = html.replace('<span id="totalItems">-</span>',
                            `<span id="totalItems">${totalItems}</span>`);
                        html = html.replace('<span id="totalProcessDuration">-</span>',
                            `<span id="totalProcessDuration">${totalDuration.toFixed(2)} jam</span>`);

                        // Update start date dan end date juga
                        const startDateInput = document.getElementById('start_date');
                        if (startDateInput && startDateInput.value) {
                            const startDate = new Date(startDateInput.value);
                            const startDateDisplay = startDate.toLocaleDateString('id-ID', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            html = html.replace('<span id="startDate">-</span>',
                                `<span id="startDate">${startDateDisplay}</span>`);

                            // Hitung end date berdasarkan total duration
                            const endDate = new Date(startDate.getTime() + (totalDuration * 60 * 60 * 1000));
                            const endDateDisplay = endDate.toLocaleDateString('id-ID', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            html = html.replace('<span id="endDate">-</span>',
                                `<span id="endDate">${endDateDisplay}</span>`);

                            // Update total duration
                            const totalDays = (totalDuration / 24).toFixed(1);
                            html = html.replace('<span id="totalDuration">Menghitung...</span>',
                                `<span id="totalDuration">${totalDays} hari (${totalDuration.toFixed(2)} jam)</span>`
                            );
                        }

                        html += `
                                </div>
                            </div>
                        `;

                        html += `
                        <style>
                            /* Modal scrolling styles */
                            #planPreviewModal .modal-body {
                                max-height: 70vh;
                                overflow-y: auto;
                                padding: 20px;
                            }

                            #planPreviewModal .modal-content {
                                max-height: 90vh;
                            }

                            /* Custom scrollbar untuk modal */
                            #planPreviewModal .modal-body::-webkit-scrollbar {
                                width: 8px;
                            }

                            #planPreviewModal .modal-body::-webkit-scrollbar-track {
                                background: #f1f1f1;
                                border-radius: 4px;
                            }

                            #planPreviewModal .modal-body::-webkit-scrollbar-thumb {
                                background: #888;
                                border-radius: 4px;
                            }

                            #planPreviewModal .modal-body::-webkit-scrollbar-thumb:hover {
                                background: #555;
                            }

                            /* Timeline content styles */
                            .process-row {
                                background: #f8f9fa;
                                border: 1px solid #dee2e6;
                                border-radius: 4px;
                                margin-bottom: 4px;
                                transition: all 0.3s ease;
                            }

                            .process-row:hover {
                                background: #e9ecef;
                                border-color: #007bff;
                                transform: translateY(-1px);
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            }

                            .process-row.moving {
                                background: #fff3cd;
                                border-color: #ffc107;
                                animation: pulse 0.5s ease-in-out;
                            }

                            .item-section.moving {
                                background: #fff3cd;
                                border-color: #ffc107;
                                animation: pulse 0.5s ease-in-out;
                            }

                            .timeline-overview .badge {
                                font-size: 0.8rem;
                                margin-left: 5px;
                            }

                            .item-section .card-header {
                                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
                            }

                            .btn-group-vertical .btn {
                                margin-bottom: 1px;
                                padding: 2px 6px;
                                font-size: 0.75rem;
                            }

                            .btn-group-vertical .btn:last-child {
                                margin-bottom: 0;
                            }

                            .process-row .btn-group-vertical {
                                display: flex;
                                flex-direction: column;
                                gap: 1px;
                            }

                            .start-time, .end-time {
                                font-size: 0.85rem;
                                font-weight: 500;
                                color: #495057;
                            }

                            .start-time {
                                color: #28a745;
                            }

                            .end-time {
                                color: #dc3545;
                            }

                            @keyframes pulse {
                                0% { transform: scale(1); }
                                50% { transform: scale(1.02); }
                                100% { transform: scale(1); }
                            }

                            .item-section[data-item-index="0"] .card-header {
                                background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
                            }

                            .process-row.border-warning {
                                border: 2px solid #ffc107 !important;
                                background: #fff3cd !important;
                            }

                            .badge-warning {
                                background-color: #ffc107 !important;
                                color: #212529 !important;
                            }

                            .badge-danger {
                                background-color: #dc3545 !important;
                                color: white !important;
                            }

                                                        /* Process button styling */
                            .btn-outline-primary {
                                font-weight: bold;
                                min-width: 40px;
                                height: 30px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                cursor: help;
                            }

                            /* Tooltip styling */
                            .tooltip-inner {
                                background-color: #333;
                                color: white;
                                font-size: 12px;
                                padding: 8px 12px;
                                border-radius: 4px;
                                max-width: 200px;
                            }

                            .tooltip.show {
                                opacity: 1;
                            }

                            /* Responsive modal sizing */
                            @media (max-width: 768px) {
                                #planPreviewModal .modal-dialog {
                                    margin: 10px;
                                    max-width: calc(100% - 20px);
                                }

                                #planPreviewModal .modal-body {
                                    max-height: 60vh;
                                    padding: 15px;
                                }
                            }

                            /* Styling untuk tabel terintegrasi */
                            .table-responsive {
                                border-radius: 8px;
                                overflow: hidden;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                            }

                            .table thead th {
                                background: linear-gradient(135deg, #343a40 0%, #495057 100%);
                                color: white;
                                border: none;
                                padding: 12px 8px;
                                font-weight: 600;
                                text-align: center;
                                vertical-align: middle;
                                z-index: 1;
                            }

                            .table tbody tr {
                                transition: all 0.2s ease;
                            }

                            .table tbody tr:hover {
                                background-color: #f8f9fa;
                                transform: translateY(-1px);
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            }

                            .table tbody td {
                                padding: 10px 8px;
                                vertical-align: middle;
                                border-color: #dee2e6;
                            }

                            /* Styling untuk item tanpa mapping */
                            .table-danger {
                                background-color: #f8d7da !important;
                                border-color: #f5c6cb !important;
                            }

                            .table-danger:hover {
                                background-color: #f1b0b7 !important;
                            }

                            .table-warning {
                                background-color: #fff3cd !important;
                                border-color: #ffeaa7 !important;
                            }

                            .badge-danger {
                                background-color: #dc3545 !important;
                                color: white !important;
                                font-size: 0.75rem;
                                padding: 0.25rem 0.5rem;
                            }

                            .badge-success {
                                background-color: #28a745 !important;
                                color: white !important;
                                font-size: 0.75rem;
                                padding: 0.25rem 0.5rem;
                            }

                            .alert-warning {
                                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
                                color: #856404;
                                border: 1px solid #ffeaa7;
                                border-radius: 8px;
                                margin: 0;
                            }

                            .text-success {
                                color: #28a745 !important;
                                font-weight: 600;
                            }

                            .text-danger {
                                color: #dc3545 !important;
                                font-weight: 600;
                            }

                            .card-header.bg-primary {
                                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
                                border: none;
                            }

                            .alert {
                                border-radius: 8px;
                                border: none;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            }

                            .alert-info {
                                background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
                                color: #0c5460;
                                border-left: 4px solid #17a2b8;
                            }

                            .alert-success {
                                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                                color: #155724;
                                border-left: 4px solid #28a745;
                            }
                        </style>
                    `;

                        return html;

                    } catch (error) {
                        console.error('Error in generatePreviewHtml:', error);

                        // Return error message yang user-friendly
                        return `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error Generating Preview</h5>
                            <p><strong>Error:</strong> ${error.message}</p>
                            <p><strong>Details:</strong> ${error.stack || 'No stack trace available'}</p>
                            <hr>
                            <p class="mb-0">
                                <strong>Debug Info:</strong><br>
                                - Data type: ${typeof data}<br>
                                - Has planPerItem: ${!!(data && data.planPerItem)}<br>
                                - planPerItem type: ${data && data.planPerItem ? typeof data.planPerItem : 'N/A'}<br>
                                - planPerItem keys: ${data && data.planPerItem ? Object.keys(data.planPerItem).join(', ') : 'N/A'}
                            </p>
                        </div>
                    `;
                    }
                }

                // Fungsi untuk filter dan urutkan item berdasarkan proses yang dipilih dan delivery date
                function filterAndSortItemsByProcess(planPerItem, selectedProcess) {
                    console.log('=== FILTERING ITEMS START ===');
                    console.log('Filtering items for process:', selectedProcess);
                    console.log('planPerItem structure:', planPerItem);
                    console.log('planPerItem type:', typeof planPerItem);
                    console.log('planPerItem is array:', Array.isArray(planPerItem));

                    let filteredItems = [];

                    // Pastikan planPerItem ada dan valid
                    if (!planPerItem || typeof planPerItem !== 'object') {
                        console.error('planPerItem is invalid:', planPerItem);
                        return [];
                    }

                    try {
                        // Jika planPerItem adalah array (dari data yang dikirim user), gunakan langsung
                        if (Array.isArray(planPerItem)) {
                            console.log('planPerItem is array, processing directly');
                            console.log('Array length:', planPerItem.length);

                            planPerItem.forEach((item, index) => {
                                console.log(`Processing array item ${index + 1}:`, item);

                                // Buat struktur yang sesuai dengan yang diharapkan
                                const itemCode = item.MaterialCode || item.materialCode || `item_${index}`;
                                const plans = [{
                                    Proses: selectedProcess, // Gunakan proses yang dipilih
                                    Estimation: item.Quantity ||
                                    1, // Gunakan quantity sebagai estimation
                                    Machine: item.Machine ||
                                    'Default Machine', // Ambil dari item jika ada
                                    Quantity: item.Quantity || 1,
                                    UP: item.Up || item.up || 1,
                                    BOM: 'Default BOM',
                                    WODocNo: item.WODocNo || item.woDocNo || '',
                                    CodeItem: item.MaterialCode || item.materialCode || '',
                                    MaterialName: item.MaterialName || item.materialName || '',
                                    Unit: item.Unit || item.unit || 'PCS'
                                }];

                                const deliveryDate = new Date(item.DeliveryDate || item.deliveryDate ||
                                    new Date());

                                const filteredItem = {
                                    itemCode: itemCode,
                                    plans: plans,
                                    deliveryDate: deliveryDate,
                                    totalDuration: parseFloat(item.Quantity || 1),
                                    WODocNo: item.WODocNo || item.woDocNo || '',
                                    CodeItem: item.MaterialCode || item.materialCode || '',
                                    MaterialName: item.MaterialName || item.materialName || '',
                                    UP: item.Up || item.up || 1,
                                    Unit: item.Unit || item.unit || 'PCS',
                                    // Tambahkan data asli untuk debugging
                                    originalItem: item
                                };

                                console.log(`Created filtered item ${index + 1}:`, filteredItem);
                                filteredItems.push(filteredItem);
                            });
                        } else {
                            // Convert planPerItem dari Object.entries ke array yang bisa difilter (untuk data dari backend)
                            console.log('planPerItem is object, processing with Object.entries');
                            console.log('Object keys:', Object.keys(planPerItem));

                            Object.entries(planPerItem).forEach(([itemCode, plans], index) => {
                                console.log(`Processing object item ${index + 1}:`, itemCode, 'plans:', plans);

                                // Handle jika plans bukan array (backend baru mengirim object langsung)
                                if (!Array.isArray(plans)) {
                                    console.log(
                                        'Plans is not an array, converting to array format for itemCode:',
                                        itemCode);
                                    // Convert single plan object to array format
                                    plans = [plans];
                                }

                                // Untuk data dari backend, kita terima semua item (tidak filter berdasarkan proses)
                                // karena backend sudah mengirim data yang sesuai dengan proses yang dipilih
                                console.log('Accepting all plans from backend for itemCode:', itemCode);

                                // Hitung total durasi untuk item ini
                                const totalDuration = plans.reduce((total, plan) => {
                                    const estimation = parseFloat(plan.Estimation || plan.estimation ||
                                        plan.EstimationTime || 0);
                                    return total + (isNaN(estimation) ? 0 : estimation);
                                }, 0);

                                // Ambil delivery date dari plan pertama dengan fallback yang lebih robust
                                let deliveryDate;
                                try {
                                    deliveryDate = plans[0].DeliveryDate || plans[0].deliveryDate || plans[0]
                                        .created_at || plans[0].CreatedAt || new Date();

                                    // Pastikan deliveryDate adalah Date object yang valid
                                    if (!(deliveryDate instanceof Date) || isNaN(deliveryDate.getTime())) {
                                        deliveryDate = new Date();
                                    }
                                } catch (dateError) {
                                    console.warn('Error parsing delivery date:', dateError);
                                    deliveryDate = new Date();
                                }

                                const filteredItem = {
                                    itemCode: itemCode,
                                    plans: plans,
                                    deliveryDate: deliveryDate,
                                    totalDuration: totalDuration,
                                    WODocNo: plans[0].WODocNo || plans[0].wo_docno || plans[0].WODocNo ||
                                        '',
                                    CodeItem: plans[0].CodeItem || plans[0].code_item || plans[0]
                                        .MaterialCode || '',
                                    MaterialName: plans[0].MaterialName || plans[0].material_name || plans[
                                        0].MaterialName || '',
                                    UP: plans[0].Up || 1,
                                    Unit: plans[0].Unit || 'PCS'
                                };

                                console.log(`Created filtered item ${index + 1}:`, filteredItem);
                                filteredItems.push(filteredItem);
                            });
                        }

                        // Urutkan berdasarkan delivery date (ascending)
                        filteredItems.sort((a, b) => {
                            try {
                                return new Date(a.deliveryDate) - new Date(b.deliveryDate);
                            } catch (sortError) {
                                console.warn('Error sorting by delivery date:', sortError);
                                return 0;
                            }
                        });

                        console.log('=== FILTERING ITEMS END ===');
                        console.log('Final filtered items count:', filteredItems.length);
                        console.log('Final filtered items:', filteredItems);
                        return filteredItems;

                    } catch (error) {
                        console.error('Error in filterAndSortItemsByProcess:', error);
                        return [];
                    }
                }

                // Fungsi untuk generate tabel terintegrasi untuk semua item dalam 1 proses
                async function generateIntegratedProcessTable(filteredItems, startTime, selectedProcess) {
                    let html = '';

                    if (filteredItems.length === 0) {
                        return `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Tidak ada item untuk proses ${getProcessCode(selectedProcess)} pada tanggal yang dipilih.
                            </div>
                        `;
                    }

                    // Set waktu mulai dari jam 8 pagi
                    let currentTime = new Date();
                    const startDateInput = document.getElementById('start_date');

                    if (startDateInput && startDateInput.value) {
                        const [year, month, day] = startDateInput.value.split('-').map(Number);
                        currentTime = new Date(year, month - 1, day, 8, 0, 0, 0);
                    } else {
                        currentTime.setHours(8, 0, 0, 0);
                    }

                    // Function untuk menghitung estimasi waktu berdasarkan kapasitas mesin
                    async function calculateMachineTime(item, processType) {
                        try {
                            // 1. Cari mesin dari tb_mapping_items berdasarkan m_ctk (untuk CETAK)
                            const mappingResponse = await fetch('{{ route('get-machine-mapping') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    materialCode: item.CodeItem || item.MaterialCode,
                                    processType: processType
                                })
                            });

                            if (!mappingResponse.ok) {
                                console.warn('Failed to get machine mapping for:', item.CodeItem);
                                return {
                                    machine: 'CTK-001',
                                    capacity: 1000,
                                    estHours: 1
                                }; // Fallback
                            }

                            const mappingData = await mappingResponse.json();
                            console.log('Machine mapping data:', mappingData);

                            if (!mappingData.success || !mappingData.machine) {
                                console.warn('No machine mapping found for:', item.CodeItem);
                                return {
                                    machine: 'CTK-001',
                                    capacity: 1000,
                                    estHours: 1
                                }; // Fallback
                            }

                            // 2. Cari kapasitas mesin dari mastermachine di mysql3
                            const capacityResponse = await fetch('{{ route('get-machine-capacity') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    machineName: mappingData.machine
                                })
                            });

                            if (!capacityResponse.ok) {
                                console.warn('Failed to get machine capacity for:', mappingData.machine);
                                return {
                                    machine: mappingData.machine,
                                    capacity: 1000,
                                    estHours: 1
                                }; // Fallback
                            }

                            const capacityData = await capacityResponse.json();
                            console.log('Machine capacity data:', capacityData);

                            if (!capacityData.success || !capacityData.capacity) {
                                console.warn('No capacity data found for machine:', mappingData.machine);
                                return {
                                    machine: mappingData.machine,
                                    capacity: 1000,
                                    estHours: 1
                                }; // Fallback
                            }

                            // 3. Hitung estimasi jam dengan pengecekan unit conversion
                            let quantity = parseFloat(item.plans?.[0]?.Quantity || item.totalDuration || 1);
                            const capacity = parseFloat(capacityData.capacity);

                            console.log('item', item);

                            // Ambil UP dan Unit dari berbagai kemungkinan field
                            const up = parseFloat(item.Up);
                            const woUnit = item.Unit;

                            // Cek apakah perlu unit conversion
                            // Jika unit WO berbeda dengan unit mesin, maka qty dibagi UP
                            if (woUnit !== 'PCS' && up > 1) {
                                const originalQuantity = quantity;
                                quantity = quantity / up;
                                console.log(
                                    `Unit conversion applied: ${originalQuantity} ${woUnit} / ${up} = ${quantity} PCS`
                                );
                            }

                            const estHours = quantity / capacity;

                            // Validasi perhitungan waktu
                            if (estHours <= 0 || isNaN(estHours)) {
                                console.warn('Invalid estHours calculated, using fallback:', estHours);
                                return {
                                    machine: mappingData.machine,
                                    capacity: capacity,
                                    estHours: 1
                                };
                            }

                            console.log(`Time calculation: ${quantity} / ${capacity} = ${estHours} hours`);
                            console.log(
                                `  Original Quantity: ${item.plans?.[0]?.Quantity || item.totalDuration || 1}`
                            );
                            console.log(`  WO Unit: ${woUnit}`);
                            console.log(`  UP: ${up}`);
                            console.log(`  Converted Quantity: ${quantity} PCS`);
                            console.log(`  Machine Capacity: ${capacity}`);
                            console.log(`  Est Hours: ${estHours}`);

                            // Debug info untuk item data
                            console.log(`  Item data structure:`, {
                                plans: item.plans,
                                Up: item.Up,
                                Unit: item.Unit,
                                quantity: item.quantity,
                                Quantity: item.Quantity
                            });

                            return {
                                machine: mappingData.machine,
                                capacity: capacity,
                                estHours: estHours
                            };

                        } catch (error) {
                            console.error('Error calculating machine time:', error);
                            return {
                                machine: 'CTK-001',
                                capacity: 1000,
                                estHours: 1
                            }; // Fallback
                        }
                    }

                    // Generate 1 tabel untuk semua item
                    html += `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-table"></i> PROSES ${getProcessCode(selectedProcess)} - ${selectedProcess}
                                </h5>
                            </div>
                            <div class="card-body">
                    `;

                    // Group items by machine untuk GLUEING
                    let itemsToProcess = [];
                    const machineGroups = {};
                    filteredItems.forEach(item => {
                        const machine = item.machine || item.plans?.[0]?.Machine || 'CTK-001';
                        if (!machineGroups[machine]) {
                            machineGroups[machine] = [];
                        }
                        machineGroups[machine].push(item);
                    });

                    // Convert ke array untuk processing
                    itemsToProcess = Object.keys(machineGroups).map(machine => ({
                        machine: machine,
                        items: machineGroups[machine]
                    }));

                    // Generate table - Loop per mesin dengan grouping untuk GLUEING
                    // Loop per mesin dengan grouping
                        for (let groupIndex = 0; groupIndex < itemsToProcess.length; groupIndex++) {
                            const { machine, items } = itemsToProcess[groupIndex];

                            // Proses lain: Header dengan grouping mesin
                            html += `
                                <div class="machine-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-primary mb-0">
                                            <i class="fas fa-cogs"></i> MESIN: ${machine}
                                        </h6>
                                        <div>
                                            <button type="button"
                                                    class="btn btn-info btn-sm"
                                                    onclick="addPPOSToTimeline('${machine}')"
                                                    title="Tambah/Edit Jadwal PPOS">
                                                <i class="fas fa-calendar-plus"></i> PPOS
                                            </button>
                                            <button type="button"
                                                    class="btn btn-warning btn-sm ml-1"
                                                    onclick="removePPOSFromTimeline('${machine}')"
                                                    title="Hapus Jadwal PPOS"
                                                    style="display: none;"
                                                    id="remove-ppos-${machine}">
                                                <i class="fas fa-calendar-minus"></i> Hapus PPOS
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width: 60px;">No</th>
                                                    <th>WO DocNo</th>
                                                    <th>Material Code</th>
                                                    <th>Material Name</th>
                                                    <th>Quantity</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Duration</th>
                                                    <th>UP</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;

                            // Cek apakah ada item tanpa mapping untuk tampilkan warning
                            const itemsWithoutMapping = items.filter(item => {
                                const itemMachine = item.machine || item.plans?.[0]?.Machine || 'CTK-001';
                                return item.hasNoMapping || !itemMachine || itemMachine === 'CTK-001';
                            });

                            if (itemsWithoutMapping.length > 0) {
                                // Buat daftar item yang tidak ada mapping
                                const missingItemsList = itemsWithoutMapping.map((item, index) => {
                                    const materialCode = item.CodeItem || item.MaterialCode || item.code_item || '-';
                                    const materialName = item.MaterialName || item.material_name || '-';
                                    const woDocNo = item.WODocNo || item.wo_docno || '-';
                                    return `${index + 1}. <strong>${materialCode}</strong> - ${materialName} (WO: ${woDocNo})`;
                                }).join('<br>');

                                html += `
                                    <tr class="table-warning">
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-warning mb-0" style="text-align: left;">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Warning:</strong> ${itemsWithoutMapping.length} item belum memiliki mapping mesin.
                                                <br><br>
                                                <strong>Daftar Item:</strong>
                                                <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px; max-height: 200px; overflow-y: auto;">
                                                    ${missingItemsList}
                                                </div>
                                                <br>
                                                <small>Silakan lengkapi mapping di tb_mapping_items terlebih dahulu.</small>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }

                            // Generate row untuk setiap item (proses lain: per mesin)
                            let groupTotalProcessTime = 0;
                            let lastEndTime = null;

                            // Tambahkan baris PPOS di awal jika ada jadwal PPOS tersimpan
                            const existingSchedules = JSON.parse(localStorage.getItem('ppos_schedules') || '{}');
                            let pposSchedules = existingSchedules[machine] || [];

                            // Pastikan pposSchedules adalah array
                            if (!Array.isArray(pposSchedules)) {
                                if (pposSchedules && typeof pposSchedules === 'object') {
                                    pposSchedules = [pposSchedules];
                                } else {
                                    pposSchedules = [];
                                }
                            }

                            // Sort PPOS schedules by start time
                            const sortedPposSchedules = pposSchedules.sort((a, b) => {
                                const timeA = new Date(a.start_date + 'T' + a.start_time);
                                const timeB = new Date(b.start_date + 'T' + b.start_time);
                                return timeA - timeB;
                            });

                            if (sortedPposSchedules.length > 0) {
                                sortedPposSchedules.forEach((pposSchedule, pposIndex) => {
                                    const pposStartDateTime = new Date(pposSchedule.start_date + 'T' + pposSchedule.start_time);
                                    const pposEndDateTime = new Date(pposSchedule.end_date + 'T' + pposSchedule.end_time);

                                    const pposStartFormatted = pposStartDateTime.toLocaleString('id-ID', {
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });

                                    const pposEndFormatted = pposEndDateTime.toLocaleString('id-ID', {
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });

                                    const pposDuration = (pposEndDateTime - pposStartDateTime) / (1000 * 60 * 60);

                                    html += `
                                        <tr class="table-info" style="background-color: #d1ecf1 !important;">
                                            <td class="text-center"><strong>PPOS ${pposIndex + 1}</strong></td>
                                            <td><strong>PPOS-${machine}-${pposIndex + 1}</strong></td>
                                            <td><strong>JADWAL PPOS</strong></td>
                                            <td><strong>${pposSchedule.item || 'ITEM PPOS'}</strong></td>
                                            <td class="text-center"><strong>-</strong></td>
                                            <td class="text-info font-weight-bold">${pposStartFormatted}</td>
                                            <td class="text-info font-weight-bold">${pposEndFormatted}</td>
                                            <td class="text-center"><strong>${pposDuration.toFixed(2)} jam</strong></td>
                                            <td class="text-center"><strong>-</strong></td>
                                            <td class="text-center">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-calendar-alt"></i> PPOS
                                                </span>
                                            </td>
                                        </tr>
                                    `;

                                    if (pposIndex === sortedPposSchedules.length - 1) {
                                        currentTime = new Date(pposEndDateTime);
                                        groupTotalProcessTime += pposDuration;
                                        lastEndTime = pposEndDateTime;
                                    }
                                });
                            }

                            for (let itemIndex = 0; itemIndex < items.length; itemIndex++) {
                                const item = items[itemIndex];
                            // console.log('11 item', item);
                            let displayQuantity = item.plans?.[0]?.Quantity || item.totalDuration || 1;

                            if (item.WODocNo && item.WODocNo.startsWith('WOP')) {
                                displayQuantity = 0;
                            }

                            let estHours = 0;
                            let machineName = 'Unknown';

                            if (item.WODocNo && item.WODocNo.startsWith('WOP')) {
                                estHours = 8;
                            } else {
                                if (item.estHours && item.estHours > 0) {
                                    estHours = parseFloat(item.estHours);
                                } else if (item.totalDuration && item.totalDuration > 0) {
                                    estHours = parseFloat(item.totalDuration);
                                } else {
                                    const quantity = parseFloat(displayQuantity) || 1;
                                    const up = parseFloat(item.Up || item.plans?.[0]?.UP || 1);

                                    let machineCapacity = 1000;

                                    // Untuk GLUEING: Cek mapping mesin
                                    try {
                                        const machineData = await calculateMachineTime(item, selectedProcess);
                                        machineCapacity = machineData.capacity;
                                        machineName = machineData.machine || 'Unknown';
                                    } catch (error) {
                                        if (machine && machine.includes('CD6')) {
                                            machineCapacity = 10000;
                                            machineName = 'CD6-Series (Fallback)';
                                        } else if (machine && machine.includes('CX')) {
                                            machineCapacity = 5000;
                                            machineName = 'CX-Series (Fallback)';
                                        } else {
                                            machineCapacity = 1000;
                                            machineName = 'Generic (Fallback)';
                                        }
                                    }

                                    let effectiveQuantity = quantity;
                                    if (up > 1) {
                                        effectiveQuantity = quantity / up;
                                    }

                                    estHours = effectiveQuantity / machineCapacity;

                                    if (estHours <= 0 || isNaN(estHours)) {
                                        estHours = 0.1;
                                    }

                                    if (estHours < 0.001) {
                                        estHours = 0.001;
                                    }
                                }
                            }

                            // Cek mapping mesin untuk GLUEING
                            const hasNoMapping = item.hasNoMapping || !machine || machine === 'CTK-001';
                            const rowClass = hasNoMapping ? 'table-danger' : '';
                            const statusDisplay = hasNoMapping ?
                                `<span class="badge badge-danger">⚠️ Belum ada mapping mesin</span>` :
                                `<span class="badge badge-success">✓ Ready</span>`;

                            const processStartTime = new Date(currentTime);
                            const processEndTime = new Date(currentTime.getTime() + (estHours * 60 * 60 * 1000));

                            if (estHours < 0.001) {

                            }

                            // Format waktu dengan tanggal lengkap
                            const startTimeFormatted = processStartTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            const endTimeFormatted = processEndTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            // Gunakan machineIndex untuk GLUEING
                            const rowIdPrefix = `row-${groupIndex + 1}-${itemIndex + 1}`;
                            const rowNumber = itemIndex + 1;

                            // Ambil machine name dari item atau mapping
                            const itemMachine = item.machine || item.plans?.[0]?.Machine || machineName || 'CTK-001';

                            html += `
                                <tr class="${rowClass}">
                                    <td class="text-center" id="${rowIdPrefix}-no"><strong>${rowNumber}</strong></td>
                                    <td id="${rowIdPrefix}-wodocno"><strong>${item.WODocNo || '-'}</strong></td>
                                    <td id="${rowIdPrefix}-materialcode">${item.CodeItem || '-'}</td>
                                    <td id="${rowIdPrefix}-materialname">${item.MaterialName || '-'}</td>
                                    <td class="text-center" id="${rowIdPrefix}-quantity">${displayQuantity}</td>
                                    <td class="text-success font-weight-bold" id="${rowIdPrefix}-starttime">${startTimeFormatted}</td>
                                    <td class="text-danger font-weight-bold" id="${rowIdPrefix}-endtime">${endTimeFormatted}</td>
                                    <td class="text-center" id="${rowIdPrefix}-duration">${estHours.toFixed(2)} jam</td>
                                    <td class="text-center" id="${rowIdPrefix}-up">${item.Up || item.plans?.[0]?.UP || '1'}</td>
                                    <td class="text-center" id="${rowIdPrefix}-status">${statusDisplay}</td>
                                </tr>
                            `;

                            // Update waktu untuk item berikutnya (per mesin untuk GLUEING)
                            currentTime = new Date(processEndTime);
                            lastEndTime = processEndTime;
                            groupTotalProcessTime += estHours;

                            item.EndJam = processEndTime.toISOString();
                            console.log(`EndJam saved for ${item.WODocNo}: ${item.EndJam}`);

                            console.log(`  Updated currentTime for next item: ${currentTime.toISOString()}`);
                            console.log(`  Group process time: ${groupTotalProcessTime} hours`);
                            console.log('---');
                            } // Tutup for loop items

                            // Proses lain: Tutup tabel per mesin dengan summary mesin
                            html += `
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Summary Mesin ${machine}</h6>
                                        <p class="mb-1"><strong>Total Items:</strong> ${items.length}</p>
                                        <p class="mb-1"><strong>Total Duration:</strong> ${groupTotalProcessTime.toFixed(2)} jam</p>
                                        <p class="mb-0"><strong>Start Time:</strong> ${new Date(startDateInput?.value || new Date()).toLocaleDateString('id-ID')} 08:00:00</p>
                                    </div>
                                </div>
                            </div>
                            `;

                            // Reset waktu untuk mesin berikutnya (mulai dari jam 8 pagi lagi)
                            if (startDateInput && startDateInput.value) {
                                const [year, month, day] = startDateInput.value.split('-').map(Number);
                                currentTime = new Date(year, month - 1, day, 8, 0, 0, 0);
                            } else {
                                currentTime.setHours(8, 0, 0, 0);
                            }
                        } // Tutup for loop groups

                    html += `
                            </div>
                        </div>
                    `;

                    return html;
                }

                // Fungsi untuk generate view per item berdasarkan proses yang dipilih (dihapus karena sudah diganti)
                function generateItemViewByProcess(filteredItems, startTime, selectedProcess) {
                    let html = '';

                    if (filteredItems.length === 0) {
                        return `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Tidak ada item untuk proses ${getProcessCode(selectedProcess)} pada tanggal yang dipilih.
                            </div>
                        `;
                    }

                    // Set waktu mulai dari jam 8 pagi
                    let currentTime = new Date();
                    const startDateInput = document.getElementById('start_date');

                    if (startDateInput && startDateInput.value) {
                        const [year, month, day] = startDateInput.value.split('-').map(Number);
                        currentTime = new Date(year, month - 1, day, 8, 0, 0, 0);
                    } else {
                        currentTime.setHours(8, 0, 0, 0);
                    }

                    // Update start date display
                    const startDateDisplay = currentTime.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    // Update start date di HTML - gunakan string replacement daripada DOM manipulation
                    // document.getElementById('startDate').textContent = startDateDisplay;

                    // Generate HTML untuk setiap item
                    filteredItems.forEach((item, itemIndex) => {
                        const isFirstItem = itemIndex === 0;

                        html += `
                            <div class="item-section mb-4" data-item-index="${itemIndex}">
                                <div class="card">
                                    <div class="card-header ${isFirstItem ? 'bg-warning' : 'bg-info'}">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-0 text-white">
                                                    <i class="fas fa-box"></i> <strong>WO DocNo:</strong> ${item.WODocNo || '-'} || <strong>Material Code:</strong> ${item.CodeItem || '-'} || <strong>Material Name:</strong> ${item.MaterialName || '-'}
                                                </h6>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <span class="badge badge-light">
                                                    <i class="fas fa-clock"></i> ${item.deliveryDate.toLocaleDateString('id-ID')}
                                                </span>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-hourglass-half"></i> ${item.totalDuration.toFixed(2)} jam
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="process-timeline">
                                            <div class="process-header mb-2">
                                                <div class="row">
                                                    <div class="col-md-1"><strong>Proses</strong></div>
                                                    <div class="col-md-1"><strong>Mesin</strong></div>
                                                    <div class="col-md-2"><strong>Waktu Mulai</strong></div>
                                                    <div class="col-md-2"><strong>Waktu Selesai</strong></div>
                                                    <div class="col-md-1"><strong>Durasi</strong></div>
                                                    <div class="col-md-1"><strong>Qty</strong></div>
                                                    <div class="col-md-1"><strong>UP</strong></div>
                                                    <div class="col-md-2"><strong>BOM</strong></div>
                                                    <div class="col-md-1"><strong>Aksi</strong></div>
                                                </div>
                                            </div>
                        `;

                        // Generate proses untuk item ini
                        item.plans.forEach((plan, processIndex) => {
                            const processStartTime = new Date(currentTime);
                            const processEndTime = new Date(currentTime.getTime() + (parseFloat(plan
                                .Estimation || plan.estimation || 0) * 60 * 60 * 1000));

                            const startTimeStr = processStartTime.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            const endTimeStr = processEndTime.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            html += `
                                <div class="process-row">
                                    <div class="row align-items-center">
                                        <div class="col-md-1">
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary mr-2" title="${plan.Proses || plan.proses}" data-toggle="tooltip" data-placement="top">${getProcessCode(plan.Proses || plan.proses)}</button>
                                                <span class="text-muted process-number" data-original-index="${processIndex}">#${processIndex + 1}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="machine-name">${plan.Machine || plan.machine || '-'}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="start-time">${startTimeStr}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="end-time">${endTimeStr}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="duration">${plan.Estimation || plan.estimation || 0} jam</span>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="quantity">${plan.Quantity || plan.quantity || '-'}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="up">${plan.UP || plan.up || '-'}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="bom">${plan.BOM || plan.bom || '-'}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="btn-group-vertical">
                                                <button type="button" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            // Update waktu untuk proses berikutnya
                            currentTime = new Date(processEndTime);
                        });

                        html += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Update end date dan total duration
                    const endDateDisplay = currentTime.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    const totalDuration = ((currentTime.getTime() - new Date(startDateInput.value).getTime()) / (1000 *
                        60 * 60 * 24)).toFixed(1);

                    // Gunakan string replacement daripada DOM manipulation
                    // document.getElementById('endDate').textContent = endDateDisplay;
                    // document.getElementById('totalDuration').textContent = `${totalDuration} hari`;

                    return html;
                }

                // Fungsi untuk mengkonversi nama proses menjadi kode singkat
                function getProcessCode(processName) {
                    const processMap = {
                        'CETAK': 'CTK',
                        'POTONG': 'PTG',
                        'EMBOSS': 'EMB',
                        'SORTIR': 'STR',
                        'GLUEING': 'GLU',
                        'LEM': 'LEM',
                        'HOT PRINT': 'HP',
                        'KUPAS': 'KPS',
                        'FINISHING': 'FIN',
                        'PACKING': 'PKG'
                    };

                    // Cek apakah ada mapping yang exact match
                    if (processMap[processName]) {
                        return processMap[processName];
                    }

                    // Cek apakah ada yang mengandung kata tersebut (case insensitive)
                    const upperProcessName = processName.toUpperCase();
                    for (const [key, value] of Object.entries(processMap)) {
                        if (upperProcessName.includes(key) || key.includes(upperProcessName)) {
                            return value;
                        }
                    }

                    // Jika tidak ada mapping, return 3 huruf pertama
                    return processName.substring(0, 3).toUpperCase();
                }

                // Fungsi untuk generate view per item dengan waktu sequential berdasarkan PROSES TYPE
                function generateItemView(sortedItems, currentGlobalTime) {
                    let html = '';

                    // Hitung waktu untuk setiap proses berdasarkan TIPE PROSES
                    let processTimings = {};
                    let currentTimeForProcessTimings = new Date(currentGlobalTime);

                    // Flatten semua proses dari semua item dan urutkan berdasarkan tipe proses
                    let allProcesses = [];
                    sortedItems.forEach(([itemCode, plans], itemIndex) => {
                        plans.forEach((plan, processIndex) => {
                            allProcesses.push({
                                itemCode: itemCode,
                                itemIndex: itemIndex,
                                plan: plan,
                                processIndex: processIndex,
                                uniqueItemId: `${itemCode}_${plans[0]?.WODocNo || 'unknown'}`
                            });
                        });
                    });

                    // Urutkan berdasarkan jenis proses
                    const processOrder = ['CTK', 'PTG', 'TUM', 'EMB', 'GLU', 'KPS', 'STR', 'LEM', 'EPL', 'Finishing',
                        'Packing'
                    ];

                    allProcesses.sort((a, b) => {
                        const aProcessIndex = processOrder.indexOf(a.plan.Proses);
                        const bProcessIndex = processOrder.indexOf(b.plan.Proses);

                        if (aProcessIndex !== bProcessIndex) {
                            return aProcessIndex - bProcessIndex;
                        }

                        return a.itemIndex - b.itemIndex;
                    });

                    // Hitung waktu untuk setiap proses secara berurutan berdasarkan tipe
                    let currentTimeForProcessType = new Date(currentGlobalTime);

                    // Reset ke jam 8 pagi untuk proses pertama
                    const startDateInput = document.getElementById('start_date');
                    if (startDateInput && startDateInput.value) {
                        const [year, month, day] = startDateInput.value.split('-').map(Number);
                        currentTimeForProcessType = new Date(year, month - 1, day, 8, 0, 0, 0);
                        console.log('Reset to 8 AM for first process:', currentTimeForProcessType.toISOString());
                    }

                    allProcesses.forEach((processData, index) => {
                        const {
                            itemCode,
                            itemIndex,
                            plan,
                            processIndex,
                            uniqueItemId
                        } = processData;

                        const processStartTime = new Date(currentTimeForProcessType);
                        const processEndTime = new Date(currentTimeForProcessType.getTime() + (parseFloat(plan
                                .Estimation) *
                            60 * 60 * 1000));

                        console.log(`Process ${index + 1}: ${plan.Proses} (Item ${itemIndex + 1})`);
                        console.log(`  Start: ${processStartTime.toISOString()}`);
                        console.log(`  End: ${processEndTime.toISOString()}`);
                        console.log(`  Duration: ${plan.Estimation} hours`);

                        // Simpan timing untuk setiap item dan proses
                        if (!processTimings[uniqueItemId]) {
                            processTimings[uniqueItemId] = {};
                        }
                        processTimings[uniqueItemId][processIndex] = {
                            startTime: processStartTime,
                            endTime: processEndTime,
                            processType: plan.Proses
                        };

                        // Update waktu untuk proses berikutnya (berdasarkan tipe proses)
                        currentTimeForProcessType = new Date(processEndTime);
                    });

                    // Generate HTML untuk setiap item
                    sortedItems.forEach(([itemCode, plans], itemIndex) => {
                        console.log('itemCode:', itemCode);
                        console.log('plans:', plans);

                        // Buat unique identifier yang menggabungkan MaterialCode dan WODocNo
                        const uniqueItemId = `${itemCode}_${plans[0]?.WODocNo || 'unknown'}`;

                        // Ekstrak MaterialCode bersih dari itemCode
                        let cleanMaterialCode = itemCode;
                        if (itemCode.includes('_')) {
                            cleanMaterialCode = itemCode.split('_')[0];
                        }

                        // Hitung durasi total untuk item ini berdasarkan timing yang sudah dihitung
                        const itemTimings = processTimings[uniqueItemId] || {};
                        let itemStartTime = null;
                        let itemEndTime = null;

                        Object.values(itemTimings).forEach(timing => {
                            if (!itemStartTime || timing.startTime < itemStartTime) {
                                itemStartTime = timing.startTime;
                            }
                            if (!itemEndTime || timing.endTime > itemEndTime) {
                                itemEndTime = timing.endTime;
                            }
                        });

                        const totalItemDuration = plans.reduce((total, plan) => total + parseFloat(plan
                            .Estimation), 0);

                        // Tandai item pertama yang seharusnya mulai jam 8 pagi
                        const isFirstItem = itemIndex === 0;

                        html += `
                            <div class="item-section mb-4" data-item-code="${uniqueItemId}" data-material-code="${itemCode}" data-wo-docno="${plans[0]?.WODocNo || ''}" data-code-item="${plans[0]?.CodeItem || ''}" data-item-index="${itemIndex}">
                                <div class="card">
                                <div class="card-header ${isFirstItem ? 'bg-warning' : 'bg-info'}">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                        <h6 class="mb-0 text-white">
                                            <i class="fas fa-box"></i> <strong>WO DocNo:</strong> ${plans[0]?.WODocNo || '-'} || <strong>Material Code:</strong> ${plans[0]?.CodeItem || '-'} || <strong>Material Name:</strong> ${plans[0]?.MaterialName || '-'}
                                        </h6>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <span class="badge badge-light">
                                                <i class="fas fa-clock"></i> ${itemStartTime ? itemStartTime.toLocaleDateString('id-ID') : '-'} - ${itemEndTime ? itemEndTime.toLocaleDateString('id-ID') : '-'}
                                            </span>
                                            <span class="badge badge-info">
                                                <i class="fas fa-hourglass-half"></i> ${totalItemDuration.toFixed(2)} jam
                                            </span>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="process-timeline" data-item-code="${itemCode}">
                        `;

                        // Header untuk proses item ini
                        html += `
                            <div class="process-header mb-2">
                                <div class="row">
                                    <div class="col-md-1"><strong>Proses</strong></div>
                                <div class="col-md-1"><strong>Mesin</strong></div>
                                    <div class="col-md-2"><strong>Waktu Mulai</strong></div>
                                    <div class="col-md-2"><strong>Waktu Selesai</strong></div>
                                    <div class="col-md-1"><strong>Durasi</strong></div>
                                    <div class="col-md-1"><strong>Qty</strong></div>
                                    <div class="col-md-1"><strong>UP</strong></div>
                                    <div class="col-md-2"><strong>BOM</strong></div>
                                    <div class="col-md-1"><strong>Aksi</strong></div>
                                </div>
                            </div>
                        `;

                        // Proses untuk item ini dengan timing yang berurutan
                        plans.forEach((plan, processIndex) => {
                            console.log('plan:', plan);

                            // Ambil timing yang sudah dihitung
                            const timing = processTimings[uniqueItemId]?.[processIndex];
                            let processStartTime, processEndTime;

                            if (timing) {
                                processStartTime = timing.startTime;
                                processEndTime = timing.endTime;
                            } else {
                                // Fallback jika timing tidak ditemukan
                                processStartTime = new Date(currentGlobalTime);
                                processEndTime = new Date(currentGlobalTime.getTime() + (parseFloat(plan
                                    .Estimation) * 60 * 60 * 1000));
                            }

                            console.log(
                                `Item ${itemIndex + 1} - Process ${processIndex + 1} (${plan.Proses}): Start=${processStartTime.toISOString()}, End=${processEndTime.toISOString()}, Duration=${plan.Estimation}h`
                            );

                            const startTime = processStartTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            const endTime = processEndTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            const duration = parseFloat(plan.Estimation);

                            html += `
                                <div class="process-row" data-item-code="${uniqueItemId}" data-process-index="${processIndex}" data-process="${plan.Proses}" data-start-time="${processStartTime.toISOString()}" data-end-time="${processEndTime.toISOString()}" data-duration="${duration}">
                                    <div class="row align-items-center py-2 border-bottom">
                                        <div class="col-md-1">
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary mr-2" title="${plan.Proses}" data-toggle="tooltip" data-placement="top">${getProcessCode(plan.Proses)}</button>
                                                <span class="text-muted process-number" data-original-index="${processIndex}">#${processIndex + 1}</span>
                                            </div>
                                        </div>
                                    <div class="col-md-1">${plan.Machine}</div>
                                    <div class="col-md-2 start-time" data-original="${processStartTime.toISOString()}">${startTime}</div>
                                    <div class="col-md-2 end-time" data-original="${processEndTime.toISOString()}">${endTime}</div>
                                    <div class="col-md-1">${duration.toFixed(2)} jam</div>
                                    <div class="col-md-1">${plan.Quantity}</div>
                                    <div class="col-md-1">${plan.Up}</div>
                                    <div class="col-md-2">${plan.Formula}</div>
                                    <div class="col-md-1">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showProcessDetails('${uniqueItemId}', ${processIndex})" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        html += `
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    });

                    return html;
                }

                // Auto-load jadwal PPOS yang sudah tersimpan saat modal preview dibuka
                $(document).on('shown.bs.modal', '#planPreviewModal', function() {
                    // Update tombol hapus PPOS berdasarkan data tersimpan
                    setTimeout(() => {
                        updatePPOSButtons();
                    }, 500);
                });

                // Fungsi untuk menambah PPOS ke timeline (global function)
                window.addPPOSToTimeline = function(machineCode) {
                    console.log('Adding PPOS to timeline for machine:', machineCode);

                    // Set machine code di hidden input
                    document.getElementById('ppos_machine_code').value = machineCode;

                    // Set default values untuk form baru
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('ppos_start_date').value = today;
                    document.getElementById('ppos_start_time').value = '08:00';
                    document.getElementById('ppos_end_date').value = today;
                    document.getElementById('ppos_end_time').value = '17:00';
                    document.getElementById('ppos_item').value = '';
                    document.getElementById('ppos_notes').value = '';

                    // Tampilkan modal
                    $('#pposScheduleModal').modal('show');
                };

                // Fungsi untuk menghapus PPOS dari timeline (global function)
                window.removePPOSFromTimeline = function(machineCode) {
                    console.log('Removing PPOS from timeline for machine:', machineCode);

                    const existingSchedules = JSON.parse(localStorage.getItem('ppos_schedules') || '{}');
                    let pposSchedules = existingSchedules[machineCode] || [];

                    // Pastikan pposSchedules adalah array
                    if (!Array.isArray(pposSchedules)) {
                        if (pposSchedules && typeof pposSchedules === 'object') {
                            pposSchedules = [pposSchedules];
                        } else {
                            pposSchedules = [];
                        }
                    }

                    if (pposSchedules.length === 0) {
                        alert('Tidak ada jadwal PPOS untuk mesin ' + machineCode);
                        return;
                    }

                    if (confirm(`Apakah Anda yakin ingin menghapus ${pposSchedules.length} jadwal PPOS untuk mesin ${machineCode}?`)) {
                        // Hapus semua jadwal PPOS untuk mesin ini
                        delete existingSchedules[machineCode];
                        localStorage.setItem('ppos_schedules', JSON.stringify(existingSchedules));

                        // Refresh preview timeline
                        refreshPreviewTimeline();

                        alert('Semua jadwal PPOS berhasil dihapus dari timeline.');
                    }
                };

                // Event handler untuk form PPOS
                $('#pposScheduleForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const machineCode = formData.get('machine_code');

                    // Validasi data
                    const startDate = formData.get('start_date');
                    const startTime = formData.get('start_time');
                    const endDate = formData.get('end_date');
                    const endTime = formData.get('end_time');
                    const item = formData.get('item');

                    if (!startDate || !startTime || !endDate || !endTime || !item) {
                        alert('Semua field wajib harus diisi!');
                        return;
                    }

                    // Validasi tanggal
                    const startDateTime = new Date(startDate + 'T' + startTime);
                    const endDateTime = new Date(endDate + 'T' + endTime);

                    if (endDateTime <= startDateTime) {
                        alert('Tanggal selesai harus setelah tanggal mulai!');
                        return;
                    }

                    // Simpan jadwal PPOS
                    const pposData = {
                        start_date: startDate,
                        start_time: startTime,
                        end_date: endDate,
                        end_time: endTime,
                        item: item,
                        notes: formData.get('notes') || ''
                    };

                    const existingSchedules = JSON.parse(localStorage.getItem('ppos_schedules') || '{}');

                    // Inisialisasi array jika belum ada
                    if (!existingSchedules[machineCode]) {
                        existingSchedules[machineCode] = [];
                    }

                    // Tambahkan PPOS baru ke array
                    existingSchedules[machineCode].push(pposData);

                    localStorage.setItem('ppos_schedules', JSON.stringify(existingSchedules));

                    // Tutup modal
                    $('#pposScheduleModal').modal('hide');

                    // Refresh preview timeline
                    refreshPreviewTimeline();

                    alert('Jadwal PPOS berhasil disimpan!');
                });

                // Fungsi untuk refresh preview timeline
                function refreshPreviewTimeline() {
                    // Tutup modal preview
                    $('#planPreviewModal').modal('hide');

                    // Regenerate preview dengan data terbaru
                    setTimeout(() => {
                        $('#btnPreviewPlanning').click();
                    }, 300);
                }

                // Fungsi untuk update tombol hapus PPOS
                function updatePPOSButtons() {
                    const existingSchedules = JSON.parse(localStorage.getItem('ppos_schedules') || '{}');

                    Object.keys(existingSchedules).forEach(machineCode => {
                        let pposSchedules = existingSchedules[machineCode] || [];

                        // Pastikan pposSchedules adalah array
                        if (!Array.isArray(pposSchedules)) {
                            if (pposSchedules && typeof pposSchedules === 'object') {
                                pposSchedules = [pposSchedules];
                            } else {
                                pposSchedules = [];
                            }
                        }

                        const removeButton = document.getElementById(`remove-ppos-${machineCode}`);
                        if (removeButton) {
                            if (pposSchedules.length > 0) {
                                removeButton.style.display = 'inline-block';
                                removeButton.innerHTML = `<i class="fas fa-calendar-minus"></i> Hapus PPOS (${pposSchedules.length})`;
                            } else {
                                removeButton.style.display = 'none';
                            }
                        }
                    });
                }
            });
        </script>
    @endsection

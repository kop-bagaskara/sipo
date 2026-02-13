@extends('main.layouts.main')

@section('title')
    Forecast List
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

    <style type="text/css">
        th {
            white-space: nowrap;
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('page-title')
    Forecast List
@endsection

@section('body')
    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Forecast List</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('forecasting.index') }}">Forecasting</a></li>
                    <li class="breadcrumb-item active">Forecast List</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col">
                                <button type="button" class="btn btn-success waves-effect waves-light" id="btn-import-excel" data-toggle="modal" data-target="#modal-import-excel">
                                    <i class="mdi mdi-upload"></i> Import dari Excel
                                </button>
                                <button type="button" class="btn btn-info waves-effect waves-light" id="btn-add-forecast">
                                    <i class="mdi mdi-plus"></i> Input Manual
                                </button>
                                <a href="{{ route('forecasting.download-template') }}" class="btn btn-secondary waves-effect waves-light">
                                    <i class="mdi mdi-download"></i> Download Template
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable-forecast"
                                class="table table-hover table-bordered table-responsive-md w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Forecast Number</th>
                                        <th>Customer</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
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

        <!-- Modal Import Excel -->
        <div class="modal fade" id="modal-import-excel" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Forecast dari Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form-import-excel" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Pilih File Excel <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="file" accept=".xlsx,.xls" required>
                                <small class="form-text text-muted">
                                    Format: XLSX, XLS (Maksimal 10MB)<br>
                                    <a href="{{ route('forecasting.download-template') }}" target="_blank">Download Template Excel</a>
                                </small>
                            </div>
                            <div class="alert alert-success">
                                <strong><i class="mdi mdi-lightbulb-on"></i> Fitur Auto-Detect:</strong><br>
                                - Sistem akan otomatis membaca semua sheet yang dimulai dengan "FC"<br>
                                - Customer dan periode akan diambil dari nama sheet (contoh: "FC Unilever Januari 2026")<br>
                                - Sistem akan membaca header baris 1 untuk mencari kolom bulan (Februari, Maret, dll)<br>
                                - QTY Forecast akan diambil dari kolom bulan yang ditemukan<br>
                                - Tidak perlu input manual customer dan periode!
                            </div>
                            <div class="alert alert-info">
                                <strong>Format Sheet Name:</strong><br>
                                - Sheet harus dimulai dengan "FC" (contoh: "FC Unilever Januari 2026")<br>
                                - Format: "FC [Customer] [Bulan] [Tahun]"<br>
                                - Contoh: "FC Unilever Januari 2026", "FC Nabati Februari 2026"<br><br>
                                <strong>Format Header:</strong><br>
                                - Baris 1 harus berisi nama bulan (Februari, Maret, dll)<br>
                                - Sistem akan mencari kolom "QTY" dan "TON" di bawah bulan tersebut<br>
                                - Kolom Item Code di kolom C, Item Name di kolom D
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-upload"></i> Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-forecast').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('forecasting.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        }
                    },
                    order: [[0, 'desc']],
                    pageLength: 25,
                    responsive: true,
                    language: {
                        processing: "Memproses...",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                        infoEmpty: "Tidak ada data yang tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'forecast_number',
                            name: 'forecast_number'
                        },
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'period',
                            name: 'period'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                var badgeClass = 'badge-secondary';
                                if (data === 'draft') {
                                    badgeClass = 'badge-warning';
                                } else if (data === 'submitted') {
                                    badgeClass = 'badge-info';
                                } else if (data === 'approved') {
                                    badgeClass = 'badge-success';
                                } else if (data === 'rejected') {
                                    badgeClass = 'badge-danger';
                                }
                                return '<span class="badge ' + badgeClass + '">' + (data ? data.toUpperCase() : '-') + '</span>';
                            }
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            defaultContent: '-'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (type === 'sort' || type === 'search') {
                                    return data;
                                }
                                return moment(data).format('DD/MM/YYYY HH:mm');
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ]
                });

                // Button Add Forecast
                $('#btn-add-forecast').on('click', function() {
                    window.location.href = "{{ route('forecasting.create') }}";
                });

                // Form Import Excel - Preview First
                $('#form-import-excel').on('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Memproses file...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    var formData = new FormData(this);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: "{{ route('forecasting.preview-import') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.close();

                            if (response.success && response.preview_data) {
                                // Show preview modal
                                showPreviewModal(response.preview_data, response.temp_file_path, response.errors);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Terjadi kesalahan saat preview'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat preview'
                            });
                        }
                    });
                });

                // Function to show preview modal
                function showPreviewModal(previewData, tempFilePath, errors) {
                    var html = '<div class="preview-container" style="max-height: 70vh; overflow-y: auto;">';

                    previewData.forEach(function(group, groupIndex) {
                        html += '<div class="card mb-3">';
                        html += '<div class="card-header bg-primary text-white">';
                        html += '<h5 class="mb-0"><strong>Grup ' + String.fromCharCode(65 + groupIndex) + ': ' + group.customer + '</strong></h5>';
                        html += '<small>Total Item: ' + group.total_items + '</small>';
                        html += '</div>';
                        html += '<div class="card-body">';

                        // Show periods
                        Object.keys(group.periods).forEach(function(periodKey) {
                            var period = group.periods[periodKey];
                            html += '<div class="period-section mb-4" style="border-left: 4px solid #007bff; padding-left: 15px;">';
                            html += '<h6><strong>Periode: ' + period.month + ' ' + period.year + '</strong></h6>';
                            html += '<p class="text-muted">Total QTY: ' + period.total_qty.toLocaleString('id-ID') + ' | Total TON: ' + period.total_ton.toLocaleString('id-ID', {minimumFractionDigits: 4, maximumFractionDigits: 4}) + '</p>';

                            // Show items table
                            html += '<div class="table-responsive">';
                            html += '<table class="table table-sm table-bordered table-hover">';
                            html += '<thead class="thead-light">';
                            html += '<tr>';
                            html += '<th>No</th>';
                            html += '<th>Material Code</th>';
                            html += '<th>Item Name</th>';
                            html += '<th>Forecast QTY</th>';
                            html += '<th>Forecast TON</th>';
                            html += '<th>Weekly Data</th>';
                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody>';

                            period.items.forEach(function(item, itemIndex) {
                                html += '<tr>';
                                html += '<td>' + (itemIndex + 1) + '</td>';
                                html += '<td>' + (item.material_code || '-') + '</td>';
                                html += '<td>' + item.item_name + '</td>';
                                html += '<td>' + (item.forecast_qty || 0).toLocaleString('id-ID') + '</td>';
                                html += '<td>' + (item.forecast_ton || 0).toLocaleString('id-ID', {minimumFractionDigits: 4, maximumFractionDigits: 4}) + '</td>';
                                html += '<td>';
                                if (item.weekly_data && item.weekly_data.length > 0) {
                                    item.weekly_data.forEach(function(week) {
                                        html += '<small>' + week.week + ': QTY=' + (week.qty || 0).toLocaleString('id-ID') + ', TON=' + (week.ton || 0).toLocaleString('id-ID', {minimumFractionDigits: 4, maximumFractionDigits: 4}) + '</small><br>';
                                    });
                                } else {
                                    html += '<small class="text-muted">-</small>';
                                }
                                html += '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody>';
                            html += '</table>';
                            html += '</div>';
                            html += '</div>';
                        });

                        html += '</div>';
                        html += '</div>';
                    });

                    if (errors && errors.length > 0) {
                        html += '<div class="alert alert-warning">';
                        html += '<strong>Peringatan:</strong> Terdapat ' + errors.length + ' error saat membaca file';
                        html += '</div>';
                    }

                    html += '</div>';

                    Swal.fire({
                        title: 'Preview Import Forecast',
                        html: html,
                        width: '90%',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Import',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#28a745',
                        didOpen: () => {
                            // Store temp file path for confirm
                            $('#swal2-content').data('temp-file-path', tempFilePath);
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            confirmImport(tempFilePath);
                        }
                    });
                }

                // Function to confirm and save import
                function confirmImport(tempFilePath) {
                    Swal.fire({
                        title: 'Mengimport...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('forecasting.confirm-import') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            temp_file_path: tempFilePath
                        },
                        success: function(response) {
                            var message = response.message || 'Forecast berhasil diimport';

                            if (response.data && response.data.imported_forecasts) {
                                var details = '<ul style="text-align: left; margin-top: 10px;">';
                                response.data.imported_forecasts.forEach(function(forecast) {
                                    details += '<li><strong>' + forecast.forecast_number + '</strong> - ' +
                                              forecast.customer + ' (' + forecast.period + ') - ' +
                                              forecast.item_count + ' item</li>';
                                });
                                details += '</ul>';
                                message += details;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                html: message,
                                width: '600px'
                            }).then(() => {
                                $('#modal-import-excel').modal('hide');
                                datatable.ajax.reload();
                                $('#form-import-excel')[0].reset();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat import'
                            });
                        }
                    });
                }

                // Button Edit Forecast (handled by link href)

                // Button Delete Forecast
                $(document).on('click', '.delete-forecast', function() {
                    var id = $(this).data('id');
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('sipo/forecasting') }}/" + id,
                                type: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message || 'Forecast berhasil dihapus'
                                    });
                                    datatable.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus forecast'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection


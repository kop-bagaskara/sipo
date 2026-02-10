@extends('main.layouts.main')
@section('title')
    Data Plan
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css"/>
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css"/>
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css"/>
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
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
                <h3 class="text-themecolor">Pengajuan Pembelian Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Pengajuan Pembelian Kertas</li>
                </ol>
            </div>
        </div>

        <!-- Tabel Daftar Pengajuan -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header dengan tombol tambah -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5 class="card-title mb-0">Daftar Pengajuan Meeting Kertas Bulanan</h5>
                                <p class="text-muted mb-0">Data pengajuan kebutuhan kertas per customer dan bulan</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('paper-procurement.check-stock') }}" class="btn btn-warning me-2">
                                    <i class="mdi mdi-magnify me-1"></i> Check Stock
                                </a>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createMeetingModal">
                                    <i class="mdi mdi-plus me-1"></i> Buat Pengajuan Baru
                                </button>
                            </div>
                        </div>

                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter Status</label>
                                <select class="form-select form-control" id="filter-status">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter Customer</label>
                                <select class="form-select form-control" id="filter-customer">
                                    <option value="">Semua Customer</option>
                                    <option value="TSPM">TSPM</option>
                                    <option value="UNILEVER">UNILEVER</option>
                                    <option value="NABATI">NABATI</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="VDR">VDR</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cari</label>
                                <input type="text" class="form-control form-control" id="search-input"
                                    placeholder="Cari nomor meeting, customer, atau bulan...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </button>
                            </div>
                        </div>

                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="paper-meeting-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nomor Meeting</th>
                                        <th>Customer</th>
                                        <th>Bulan Meeting</th>
                                        <th>Periode</th>
                                        <th>Toleransi</th>
                                        <th>Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Tanggal Dibuat</th>
                                        <th style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($meetings as $index => $meeting)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $meeting->meeting_number }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info" style="color: white;">{{ $meeting->customer_name }}</span>
                                            </td>
                                            <td>{{ $meeting->meeting_month }}</td>
                                            <td>
                                                <small>{{ $meeting->period_month_1 }}, {{ $meeting->period_month_2 }}, {{ $meeting->period_month_3 }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ number_format($meeting->tolerance_percentage, 1) }}%</span>
                                            </td>
                                            <td>
                                                @if ($meeting->status == 'draft')
                                                    <span class="badge bg-warning">Draft</span>
                                                @elseif($meeting->status == 'submitted')
                                                    <span class="badge bg-primary">Submitted</span>
                                                @elseif($meeting->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($meeting->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($meeting->status == 'completed')
                                                    <span class="badge bg-info">Completed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $meeting->creator->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $meeting->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('paper-procurement.show', $meeting->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    @if ($meeting->status == 'draft')
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="editMeeting({{ $meeting->id }})" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    @endif
                                                    @if ($meeting->status == 'submitted')
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="approveMeeting({{ $meeting->id }})" title="Approve">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="rejectMeeting({{ $meeting->id }})" title="Reject">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline" style="font-size: 48px;"></i>
                                                    <p class="mt-2 mb-0">Belum ada data pengajuan meeting kertas</p>
                                                    <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                                                        <i class="mdi mdi-plus me-1"></i> Buat Pengajuan Pertama
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Info Card -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="mdi mdi-help-circle-outline text-primary"></i> Informasi
                                </h5>
                                <ul class="mb-0">
                                    <li>Pengajuan meeting kertas dibuat untuk kebutuhan <strong>3 bulan ke depan</strong></li>
                                    <li>Setiap customer memiliki daftar produk dan spesifikasi kertas yang berbeda</li>
                                    <li>Data akan digunakan untuk <strong>meeting bulanan PPIC</strong> pengadaan kertas</li>
                                    <li>Status <span class="badge bg-warning">Draft</span> dapat diedit, <span class="badge bg-primary">Submitted</span> menunggu approval</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Buat Pengajuan Baru -->
        <div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="createMeetingModalLabel">
                            <i class="mdi mdi-file-document-outline me-2"></i> Buat Pengajuan Meeting Kertas Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('paper-procurement.create') }}" method="GET">
                        <div class="modal-body">
                            <div class="alert alert-info mb-3">
                                <i class="mdi mdi-information-outline"></i>
                                <small>Pilih bulan periode pertama, sistem akan otomatis menghitung 2 bulan berikutnya</small>
                            </div>

                            <!-- Pilih Bulan Periode Pertama -->
                            <div class="mb-3">
                                <label for="modal_month" class="form-label">Pilih Bulan Periode Pertama <span class="text-danger">*</span></label>
                                <select class="form-select form-control" id="modal_month" name="month" required>
                                    <option value="">-- Pilih Bulan Periode Pertama --</option>
                                    <option value="JAN">Januari 2025</option>
                                    <option value="FEB">Februari 2025</option>
                                    <option value="MAR">Maret 2025</option>
                                    <option value="APR">April 2025</option>
                                    <option value="MAY">Mei 2025</option>
                                    <option value="JUN">Juni 2025</option>
                                    <option value="JUL">Juli 2025</option>
                                    <option value="AUG">Agustus 2025</option>
                                    <option value="SEP">September 2025</option>
                                    <option value="OCT">Oktober 2025</option>
                                    <option value="NOV">November 2025</option>
                                    <option value="DEC">Desember 2025</option>
                                </select>
                                <div class="form-text">
                                    <small class="text-muted">Contoh: Pilih OKTOBER → Periode: OKTOBER, NOVEMBER, DESEMBER</small>
                                </div>
                            </div>

                            <!-- Pilih Customer Group -->
                            <div class="mb-3">
                                <label for="modal_customer_group" class="form-label">Pilih Customer Group <span class="text-danger">*</span></label>
                                <select class="form-select form-control" id="modal_customer_group" name="customer_group" required>
                                    <option value="">-- Pilih Customer Group --</option>
                                    <option value="TSPM">TSPM</option>
                                    <option value="UNILEVER">UNILEVER</option>
                                    <option value="NABATI">NABATI</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="VDR">VDR</option>
                                </select>
                            </div>

                            <!-- Pilih Template -->
                            <div class="mb-3" id="template-selection" style="display: none;">
                                <label for="modal_template" class="form-label">Pilih Template <span class="text-danger">*</span></label>
                                <select class="form-select form-control" id="modal_template" name="template" required>
                                    <option value="">-- Pilih Template --</option>
                                    <option value="template1">Template 1</option>
                                    <option value="template2">Template 2</option>
                                </select>
                                <div class="form-text">
                                    <small class="text-muted">Pilih template formulir yang sesuai untuk pengajuan ini</small>
                                </div>
                            </div>

                            <!-- Preview Periode -->
                            <div class="alert alert-light" id="modal-preview" style="display: none;">
                                <h6 class="alert-heading mb-2"><i class="mdi mdi-eye-outline"></i> Preview Periode</h6>
                                <p class="mb-1"><strong>Bulan Meeting:</strong> <span id="modal-preview-month" class="text-primary">-</span></p>
                                <p class="mb-0"><strong>Periode (3 Bulan):</strong> <span id="modal-preview-period">-</span></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="mdi mdi-close me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="mdi mdi-arrow-right me-1"></i> Lanjutkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form Buat Pengajuan (Collapsed) -->
        <div class="row justify-content-center mt-3">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Buat Pengajuan Pembelian Kertas</h4>

                        <form action="{{ url('paper-procurement/create') }}" method="GET">
                            <!-- Pilih Bulan Periode Pertama -->
                            <div class="mb-4">
                                <label for="month" class="form-label">Pilih Bulan Periode Pertama <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-control form-select-lg" id="month" name="month" required>
                                    <option value="">-- Pilih Bulan Periode Pertama --</option>
                                    <option value="JAN">Januari 2025</option>
                                    <option value="FEB">Februari 2025</option>
                                    <option value="MAR">Maret 2025</option>
                                    <option value="APR">April 2025</option>
                                    <option value="MAY">Mei 2025</option>
                                    <option value="JUN">Juni 2025</option>
                                    <option value="JUL">Juli 2025</option>
                                    <option value="AUG">Agustus 2025</option>
                                    <option value="SEP">September 2025</option>
                                    <option value="OCT" selected>Oktober 2025</option>
                                    <option value="NOV">November 2025</option>
                                    <option value="DEC">Desember 2025</option>
                                </select>
                                <div class="form-text">
                                    <i class="mdi mdi-information-outline"></i>
                                    Periode akan otomatis menghitung 3 bulan: bulan yang dipilih + 2 bulan berikutnya
                                    <br>
                                    <small class="text-muted">Contoh: Pilih OKTOBER → Periode: OKTOBER, NOVEMBER, DESEMBER</small>
                                </div>
                            </div>

                            <!-- Pilih Customer Group -->
                            <div class="mb-4">
                                <label for="customer_group" class="form-label">Pilih Customer Group <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-control form-select-lg" id="customer_group" name="customer_group"
                                    required>
                                    <option value="">-- Pilih Customer Group --</option>
                                    <option value="TSPM">TSPM</option>
                                    <option value="UNILEVER">UNILEVER</option>
                                    <option value="NABATI">NABATI</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="VDR">VDR</option>
                                </select>
                                <div class="form-text">
                                    <i class="mdi mdi-information-outline"></i>
                                    Setiap customer group memiliki daftar produk dan kebutuhan kertas yang berbeda
                                </div>
                            </div>

                            <!-- Preview Info -->
                            <div class="alert alert-info" role="alert" id="preview-info" style="display: none;">
                                <h5 class="alert-heading"><i class="mdi mdi-information-outline"></i> Preview Pengajuan</h5>
                                <p class="mb-2">
                                    <strong>Bulan Meeting:</strong> <span id="preview-month">-</span><br>
                                    <strong>Periode Pengajuan:</strong> <span id="preview-period">-</span><br>
                                    <strong>Customer Group:</strong> <span id="preview-customer">-</span>
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-end">
                                <a href="{{ route('paper-procurement.list') }}" class="btn btn-outline-secondary me-2">
                                    <i class="mdi mdi-format-list-bulleted me-1"></i> Lihat Daftar Pengajuan
                                </a>
                                <button type="submit" class="btn btn-info btn-lg">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Buat Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="mdi mdi-help-circle-outline text-primary"></i> Informasi
                        </h5>
                        <ul class="mb-0">
                            <li>Pilih <strong>bulan periode pertama</strong>, sistem akan otomatis menghitung 2 bulan berikutnya</li>
                            <li>Contoh: Pilih <strong>OKTOBER</strong> → Periode: <strong>OKTOBER, NOVEMBER, DESEMBER</strong></li>
                            <li>Setiap customer group memiliki daftar produk dan spesifikasi kertas yang berbeda</li>
                            <li>Data akan digunakan untuk <strong>meeting bulanan PPIC</strong> pengadaan kertas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        @section('scripts')
            <script>
                $(document).ready(function() {
                    const monthNames = {
                        'JAN': 'Januari',
                        'FEB': 'Februari',
                        'MAR': 'Maret',
                        'APR': 'April',
                        'MAY': 'Mei',
                        'JUN': 'Juni',
                        'JUL': 'Juli',
                        'AUG': 'Agustus',
                        'SEP': 'September',
                        'OCT': 'Oktober',
                        'NOV': 'November',
                        'DEC': 'Desember'
                    };

                    const monthOrder = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

                    function generateThreeMonthPeriod(selectedMonth) {
                        const currentIndex = monthOrder.indexOf(selectedMonth);
                        let months = [];

                        for (let i = 0; i < 3; i++) {
                            const index = (currentIndex + i) % 12;
                            months.push(monthNames[monthOrder[index]]);
                        }

                        return months.join(', ');
                    }

                    function updatePreview() {
                        const month = $('#month').val();
                        const customerGroup = $('#customer_group').val();

                        if (month && customerGroup) {
                            const monthIndex = monthOrder.indexOf(month);
                            const month2 = monthOrder[(monthIndex + 1) % 12];
                            const month3 = monthOrder[(monthIndex + 2) % 12];

                            $('#preview-month').text(monthNames[month] + ' 2025');
                            $('#preview-period').html(
                                '<span class="badge bg-primary">' + monthNames[month] + '</span>, ' +
                                '<span class="badge bg-primary">' + monthNames[month2] + '</span>, ' +
                                '<span class="badge bg-primary">' + monthNames[month3] + '</span>'
                            );
                            $('#preview-customer').text(customerGroup);
                            $('#preview-info').slideDown();
                        } else {
                            $('#preview-info').slideUp();
                        }
                    }

                    $('#month, #customer_group').on('change', updatePreview);

                    // Modal preview functionality
                    function updateModalPreview() {
                        const month = $('#modal_month').val();
                        const customerGroup = $('#modal_customer_group').val();

                        if (month) {
                            const monthIndex = monthOrder.indexOf(month);
                            const month2 = monthOrder[(monthIndex + 1) % 12];
                            const month3 = monthOrder[(monthIndex + 2) % 12];

                            $('#modal-preview-month').text(monthNames[month] + ' 2025');
                            $('#modal-preview-period').html(
                                '<span class="badge bg-info text-white">' + monthNames[month] + '</span>, ' +
                                '<span class="badge bg-info text-white">' + monthNames[month2] + '</span>, ' +
                                '<span class="badge bg-info text-white">' + monthNames[month3] + '</span>'
                            );
                            $('#modal-preview').slideDown();
                        } else {
                            $('#modal-preview').slideUp();
                        }
                    }

                    $('#modal_month, #modal_customer_group').on('change', updateModalPreview);

                    // Show template selection when customer group is selected
                    $('#modal_customer_group').on('change', function() {
                        const customerGroup = $(this).val();
                        if (customerGroup) {
                            $('#template-selection').slideDown();
                            $('#modal_template').prop('required', true);
                        } else {
                            $('#template-selection').slideUp();
                            $('#modal_template').val('').prop('required', false);
                        }
                    });

                    // Filter functionality untuk tabel
                    function filterTable() {
                        const statusFilter = $('#filter-status').val().toLowerCase();
                        const customerFilter = $('#filter-customer').val().toLowerCase();
                        const searchTerm = $('#search-input').val().toLowerCase();

                        $('#paper-meeting-table tbody tr').each(function() {
                            const row = $(this);
                            const status = row.find('td:eq(6)').text().toLowerCase();
                            const customer = row.find('td:eq(2)').text().toLowerCase();
                            const searchText = row.text().toLowerCase();

                            const statusMatch = !statusFilter || status.includes(statusFilter);
                            const customerMatch = !customerFilter || customer.includes(customerFilter);
                            const searchMatch = !searchTerm || searchText.includes(searchTerm);

                            if (statusMatch && customerMatch && searchMatch) {
                                row.show();
                            } else {
                                row.hide();
                            }
                        });
                    }

                    $('#filter-status, #filter-customer').on('change', filterTable);
                    $('#search-input').on('keyup', filterTable);
                });

                // Action functions - didefinisikan di global scope
                window.clearFilters = function() {
                    $('#filter-status, #filter-customer').val('');
                    $('#search-input').val('');
                    $('#paper-meeting-table tbody tr').show();
                };

                window.editMeeting = function(id) {
                    // Redirect ke halaman edit
                    window.location.href = '{{ url("sipo/paper-procurement") }}/' + id + '/edit';
                };

                window.approveMeeting = function(id) {
                    if (confirm('Apakah Anda yakin ingin menyetujui pengajuan meeting ini?')) {
                        // TODO: Implement approve functionality
                        alert('Meeting ID ' + id + ' approved! - Fitur akan segera tersedia');
                    }
                };

                window.rejectMeeting = function(id) {
                    if (confirm('Apakah Anda yakin ingin menolak pengajuan meeting ini?')) {
                        // TODO: Implement reject functionality
                        alert('Meeting ID ' + id + ' rejected! - Fitur akan segera tersedia');
                    }
                };
            </script>
        @endsection
    @endsection

@extends('main.layouts.main')
@section('title')
    Admin - Supplier Tickets - Create PQC Form
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
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
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
    Create PQC Form
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Admin - Supplier Tickets - Create GRD Form</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Admin - Supplier Tickets - Create GRD Form</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <!-- Action Selection -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-cog"></i> Pilih Aksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createGoodReceipt" checked>
                                    <label class="form-check-label" for="createGoodReceipt">
                                        <strong>Buat PQC</strong>
                                    </label>
                                    <small class="text-muted d-block">
                                        Product Quality Control untuk POM standard
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createReject">
                                    <label class="form-check-label" for="createReject">
                                        <strong>Buat Surat Reject</strong>
                                    </label>
                                    <small class="text-muted d-block">
                                        Surat penolakan untuk barang yang tidak sesuai atau partial delivery
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Good Receipt Form -->
                <div class="card" id="goodReceiptCard">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Product Quality Control Document (PQC)
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="pqcTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="header-tab" data-bs-toggle="tab"
                                    data-bs-target="#header" type="button" role="tab">
                                    Header
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail"
                                    type="button" role="tab">
                                    Detail
                                </button>
                            </li>
                        </ul>

                        <form id="pqcForm" method="POST"
                            action="{{ route('admin.supplier-tickets.process-combined', $supplierTicket->id) }}">
                            @csrf
                            <input type="hidden" name="action" value="pqc">
                            
                            <!-- Hidden fields for reject data (will be populated by JavaScript) -->
                            <input type="hidden" name="rejection_reason" id="rejection_reason_hidden">
                            <div id="rejected_items_hidden"></div>

                            <div class="tab-content mt-3" id="pqcTabsContent">
                                <!-- Header Tab -->
                                <div class="tab-pane fade show active" id="header" role="tabpanel">
                                    <div class="container-fluid">

                                        {{-- hidden input --}}
                                        <input type="hidden" name="series" value="PQC">
                                        <input type="hidden" name="zone" value="">

                                        <br>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="pqc_number" class="form-label">Doc No</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="docno" name="docno"
                                                        value="{{ $pqcNumber }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="docdate" class="form-label">Doc Date</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="mb-3">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="docdate"
                                                                name="docdate"
                                                                value="{{ $supplierTicket->arrival_date ? \Carbon\Carbon::parse($supplierTicket->arrival_date)->format('d M Y') : now()->format('d M Y') }}"
                                                                readonly>
                                                            <button class="btn btn-outline-secondary" type="button">
                                                                <i class="mdi mdi-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="location" class="form-label">Location</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <select class="form-control" id="location" name="location"
                                                            required>
                                                            <option value="">Select Location</option>
                                                            <option value="QC">QC</option>
                                                            {{-- <option value="G23BK">G23BK</option> --}}
                                                            {{-- <option value="GLAIN">GLAIN</option> --}}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="supplier" class="form-label">Supplier</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="supplier"
                                                            name="supplier"
                                                            value="{{ $poItemsForGRD->first()->SupplierCode ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="po_number" class="form-label">PO Number</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="po_number"
                                                            name="po_number" value="{{ $supplierTicket->po_number }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="supplier_delivery_no" class="form-label">Supplier Delivery
                                                    No</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="supplier_delivery_no"
                                                        name="supplier_delivery_no"
                                                        value="{{ $supplierTicket->supplier_delivery_doc }}">
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="vehicle_no" class="form-label">Vehicle No</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="vehicle_no"
                                                        name="vehicle_no" value="{{ $supplierTicket->vehicle_number }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="information" class="form-label">Information</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <textarea class="form-control" id="information" name="information" rows="3"
                                                        placeholder="Additional information..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Detail Tab -->
                                <div class="tab-pane fade" id="detail" role="tabpanel">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Item Details</h6>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-file-export"></i> Export To
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                                    <i class="mdi mdi-printer"></i> Print
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info">
                                                    <i class="mdi mdi-content-copy"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grouping Area -->
                                    <div class="alert alert-light mb-3">
                                        <small class="text-muted">Drag a column header here to group by that column</small>
                                    </div>

                                    <!-- Detail Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="detailTable">
                                            <thead>
                                                <tr>
                                                    <th>Number</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Tag No</th>
                                                    <th>Expiry Date</th>
                                                    <th>Unit</th>
                                                    <th style="white-space: nowrap;">Qty PO Total</th>
                                                    <th style="white-space: nowrap;">Qty PO Remain</th>
                                                    <th>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($poItemsForGRD && $poItemsForGRD->isNotEmpty())
                                                    @foreach ($poItemsForGRD as $index => $item)
                                                        <tr>
                                                            <td>{{ $item->Number ?? $index + 1 }}</td>
                                                            <td><code>{{ $item->Code ?? 'N/A' }}</code></td>
                                                            <td>{{ $item->Name ?? 'N/A' }}</td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control form-control-sm tag-no-field"
                                                                    name="tag_no[{{ $index }}]" readonly
                                                                    style="background-color: #f8f9fa; color: #6c757d;"
                                                                    title="Tag No akan otomatis di-generate berdasarkan DocDate untuk FIFO">
                                                            </td>
                                                            <td>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    name="expiry_date[{{ $index }}]"
                                                                    value="{{ $item->ExpiryDate ? \Carbon\Carbon::parse($item->ExpiryDate)->format('Y-m-d') : '' }}">
                                                            </td>
                                                            <td>{{ $item->Unit ?? 'PCS' }}</td>
                                                            <td class="text-right">
                                                                {{ number_format((float)($item->QtyPOTotal ?? 0), 4) }}</td>
                                                            <td class="text-right">
                                                                {{ number_format((float)($item->QtyPORemain ?? 0), 4) }}</td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control form-control-sm qty-received"
                                                                    name="qty_received[{{ $index }}]"
                                                                    value="0" step="0.0001" min="0"
                                                                    max="{{ $item->QtyPORemain ?? 0 }}"
                                                                    data-original-qty="{{ $item->QtyPORemain ?? 0 }}"
                                                                    data-material-code="{{ $item->Code ?? '' }}" required>
                                                                <small class="text-muted">Max:
                                                                    {{ number_format((float)($item->QtyPORemain ?? 0), 4) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">
                                                            <i class="mdi mdi-information-outline"></i>
                                                            Tidak ada item ditemukan
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Status Bar -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <select class="form-select form-select-sm"
                                                    style="width: auto; display: inline-block;">
                                                    <option>Select All</option>
                                                </select>
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $poItemsForGRD ? $poItemsForGRD->count() : 0 }} row(s) returned.
                                                    0 row(s) selected.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer">
                        {{-- <div class="d-flex justify-content-between"> --}}
                        {{-- <div>
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left"></i> Prev Doc
                            </button>
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-right"></i> Next Doc
                            </button>
                        </div> --}}
                        {{-- <div> --}}
                        {{-- <button type="button" class="btn btn-outline-primary">
                                <i class="mdi mdi-printer"></i> Print
                            </button>
                            <button type="button" class="btn btn-outline-info">
                                <i class="mdi mdi-qrcode"></i> Print QR
                            </button> --}}
                        <button type="button" class="btn btn-success" id="submitBtn">
                            <i class="mdi mdi-content-save"></i> Submit Document
                        </button>
                        {{-- <button type="button" class="btn btn-outline-danger">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                            <a href="{{ route('admin.supplier-tickets.show', $supplierTicket->id) }}" class="btn btn-outline-warning">
                                <i class="mdi mdi-arrow-left"></i> Cancel
                            </a> --}}
                        {{-- </div> --}}
                    </div>
                </div>
            </div>

            <!-- Reject Form -->
            <div class="card mt-3" id="rejectCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-close-circle text-danger"></i> Form Penolakan Supplier Ticket
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Ticket Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Ticket</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Ticket ID:</strong></td>
                                    <td>{{ $supplierTicket->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>PO Number:</strong></td>
                                    <td>{{ $supplierTicket->po_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $supplierTicket->supplier->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Arrival Date:</strong></td>
                                    <td>{{ $supplierTicket->arrival_date ? \Carbon\Carbon::parse($supplierTicket->arrival_date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status Saat Ini</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if ($supplierTicket->status === 'processed')
                                            <span class="badge bg-warning">Processed</span>
                                        @elseif($supplierTicket->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($supplierTicket->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($supplierTicket->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>GRD Number:</strong></td>
                                    <td>{{ $supplierTicket->grd_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>PQC Number:</strong></td>
                                    <td>{{ $supplierTicket->pqc_number ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Reject Form -->
                    <form id="rejectForm" method="POST"
                        action="{{ route('admin.supplier-tickets.process-combined', $supplierTicket->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="reject">

                        <!-- General Rejection Reason -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="rejection_reason" class="form-label">
                                    <strong>Alasan Penolakan Umum</strong> <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason"
                                    name="rejection_reason" rows="3" placeholder="Masukkan alasan penolakan secara umum..." required>{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Detail Item yang Ditolak</h6>

                                @if ($poItemsForGRD && $poItemsForGRD->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="rejectItemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="15%">Material Code</th>
                                                    <th width="25%">Material Name</th>
                                                    <th width="10%">Unit</th>
                                                    <th width="15%">Qty PO Total</th>
                                                    <th width="15%">Qty PO Remain</th>
                                                    <th width="15%">Qty Rejected</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($poItemsForGRD as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td><code>{{ $item->Code ?? 'N/A' }}</code></td>
                                                        <td>{{ $item->Name ?? 'N/A' }}</td>
                                                        <td>{{ $item->Unit ?? 'PCS' }}</td>
                                                        <td class="text-right">
                                                            {{ number_format((float)($item->QtyPOTotal ?? 0), 4) }}</td>
                                                        <td class="text-right">
                                                            {{ number_format((float)($item->QtyPORemain ?? 0), 4) }}</td>
                                                        <td>
                                                            <input type="hidden"
                                                                name="rejected_items[{{ $index }}][material_code]"
                                                                value="{{ $item->Code ?? '' }}">
                                                            <input type="number"
                                                                class="form-control form-control-sm rejected-qty"
                                                                name="rejected_items[{{ $index }}][rejected_qty]"
                                                                value="0" step="0.0001" min="0"
                                                                max="{{ $item->QtyPORemain ?? 0 }}"
                                                                data-max-qty="{{ $item->QtyPORemain ?? 0 }}" required>
                                                            <input type="hidden"
                                                                name="rejected_items[{{ $index }}][reason]"
                                                                value="Rejected by admin">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert-circle"></i>
                                        Tidak ada item PO yang ditemukan.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="hideRejectForm()">
                                        <i class="mdi mdi-arrow-left"></i> Kembali
                                    </button>

                                    <div class="text-muted">
                                        <small><i class="mdi mdi-information-outline"></i> Gunakan tombol Submit di atas
                                            untuk menyimpan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        </div>
    @endsection

    @section('scripts')
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                // Simple tab switching without Bootstrap dependencies
                $('.nav-link').on('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all tabs and panes
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');

                    // Add active class to clicked tab
                    $(this).addClass('active');

                    // Show corresponding tab pane
                    var target = $(this).attr('data-bs-target');
                    $(target).addClass('show active');
                });

                // Generate TagNo for all items when form loads
                console.log('Document ready, calling generateTagNumbers()');

                // Wait a bit for DOM to be fully ready
                setTimeout(function() {
                    console.log('Timeout reached, calling generateTagNumbers()');
                    generateTagNumbers();
                }, 500);

                // Quantity validation and rejection logic
                $('.qty-received').on('input', function() {
                    var $this = $(this);
                    var receivedQty = parseFloat($this.val()) || 0;
                    var originalQty = parseFloat($this.data('original-qty')) || 0;

                    if (receivedQty > originalQty) {
                        $this.val(originalQty);
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Quantity yang diterima tidak boleh melebihi quantity PO',
                            icon: 'warning',
                            timer: 2000
                        });
                    }
                });

                // Single submit button handler
                $('#submitBtn').on('click', function() {
                    // Check which actions are selected
                    var isPQCSelected = $('#createGoodReceipt').is(':checked');
                    var isRejectSelected = $('#createReject').is(':checked');
                    var confirmText = '';
                    var formToSubmit = '';

                    if (isPQCSelected && isRejectSelected) {
                        // Both PQC and Reject selected
                        confirmText = 'Apakah Anda yakin ingin menyimpan dokumen PQC dan membuat surat reject?';
                        formToSubmit = 'pqcForm'; // Use PQC form as base, reject data will be included
                    } else if (isPQCSelected) {
                        // Only PQC selected
                        confirmText = 'Apakah Anda yakin ingin menyimpan dokumen PQC ini?';
                        formToSubmit = 'pqcForm';
                    } else if (isRejectSelected) {
                        // Only Reject selected
                        confirmText = 'Apakah Anda yakin ingin membuat surat reject?';
                        formToSubmit = 'rejectForm';
                    } else {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Pilih minimal satu aksi: Buat PQC atau Buat Surat Reject',
                            icon: 'warning',
                            timer: 3000
                        });
                        return;
                    }

                    // Validate reject form if reject is selected
                    if (isRejectSelected) {
                        var hasRejection = false;
                        $('.rejected-qty').each(function() {
                            if (parseFloat($(this).val()) > 0) {
                                hasRejection = true;
                                return false;
                            }
                        });

                        if (!hasRejection) {
                            Swal.fire({
                                title: 'Peringatan',
                                text: 'Minimal satu item harus memiliki quantity yang ditolak',
                                icon: 'warning',
                                timer: 3000
                            });
                            return;
                        }
                    }

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: confirmText,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: isRejectSelected ? '#d33' : '#3085d6',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: isRejectSelected ? 'Ya, Proses!' : 'Ya, Simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            var loadingTitle = 'Memproses...';
                            var loadingText = '';
                            
                            if (isPQCSelected && isRejectSelected) {
                                loadingText = 'Sedang menyimpan dokumen PQC dan membuat surat reject';
                            } else if (isPQCSelected) {
                                loadingText = 'Sedang menyimpan dokumen PQC';
                            } else if (isRejectSelected) {
                                loadingText = 'Sedang membuat surat reject';
                            }
                            
                            Swal.fire({
                                title: loadingTitle,
                                text: loadingText,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            });

                            // If both PQC and Reject are selected, copy reject data to PQC form
                            if (isPQCSelected && isRejectSelected) {
                                // Copy rejection reason
                                $('#rejection_reason_hidden').val($('#rejection_reason').val());
                                
                                // Copy rejected items
                                $('#rejected_items_hidden').empty();
                                $('.rejected-qty').each(function(index) {
                                    var $this = $(this);
                                    var rejectedQty = parseFloat($this.val()) || 0;
                                    var materialCode = $this.closest('tr').find('td:nth-child(2) code').text();
                                    
                                    if (rejectedQty > 0) {
                                        var hiddenInput = '<input type="hidden" name="rejected_items[' + index + '][material_code]" value="' + materialCode + '">' +
                                                        '<input type="hidden" name="rejected_items[' + index + '][rejected_qty]" value="' + rejectedQty + '">' +
                                                        '<input type="hidden" name="rejected_items[' + index + '][reason]" value="Rejected by admin">';
                                        $('#rejected_items_hidden').append(hiddenInput);
                                    }
                                });
                                
                                // Change action to combined
                                $('input[name="action"]').val('combined');
                            }

                            // Submit the appropriate form
                            document.getElementById(formToSubmit).submit();
                        }
                    });
                });

                // Function to generate TagNo for all items
                function generateTagNumbers() {
                    console.log('generateTagNumbers() called');
                    var docDate = $('#docdate').val();
                    console.log('docDate', docDate);
                    console.log('docdate element found:', $('#docdate').length);

                    if (!docDate) {
                        console.log('DocDate not available yet');
                        return;
                    }

                    // Get TagNo from server
                    $.ajax({
                        url: '{{ route('admin.supplier-tickets.generate-tagno-pqc') }}',
                        method: 'POST',
                        data: {
                            docdate: docDate,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('AJAX success response:', response);
                            if (response.success && response.tagNo) {
                                // Fill all TagNo fields with the generated number
                                $('.tag-no-field').val(response.tagNo);
                                console.log('TagNo generated and filled:', response.tagNo);
                                console.log('TagNo fields found:', $('.tag-no-field').length);
                            } else {
                                console.error('Failed to generate TagNo:', response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error generating TagNo:', xhr.responseText);
                            console.error('Status:', xhr.status);
                            console.error('Response:', xhr.responseJSON);
                        }
                    });
                }

                // Generate TagNo when DocDate changes
                $('#docdate').on('change', function() {
                    generateTagNumbers();
                });

                // Toggle forms based on checkbox selection
                $('#createGoodReceipt').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#goodReceiptCard').show();
                    } else {
                        $('#goodReceiptCard').hide();
                    }
                });

                $('#createReject').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#rejectCard').show();
                        // $('#goodReceiptCard').hide();
                    } else {
                        $('#rejectCard').hide();
                    }
                });

                // Reject form validation
                $('.rejected-qty').on('input', function() {
                    var $this = $(this);
                    var rejectedQty = parseFloat($this.val()) || 0;
                    var maxQty = parseFloat($this.data('max-qty')) || 0;

                    if (rejectedQty > maxQty) {
                        $this.val(maxQty);
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Quantity yang ditolak tidak boleh melebihi quantity PO yang tersisa',
                            icon: 'warning',
                            timer: 2000
                        });
                    }
                });

                // Note: Reject form now uses the main submit button only
            });

            // Function to hide reject form
            function hideRejectForm() {
                $('#createReject').prop('checked', false);
                $('#rejectCard').hide();
                $('#goodReceiptCard').show();
            }
        </script>
    @endsection

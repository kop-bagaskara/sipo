@extends('main.layouts.main')
@section('title')
    Admin - Supplier Tickets
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
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Admin - Supplier Tickets - {{ $supplierTicket->ticket_number }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Admin - Supplier Tickets - {{ $supplierTicket->ticket_number }}</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Informasi Ticket</h5>
                            </div>
                            <div class="col-auto">
                                @if ($supplierTicket->status === 'processed')
                                    <span class="badge bg-warning fs-6">{{ strtoupper($supplierTicket->status) }}</span>
                                @elseif($supplierTicket->status === 'approved')
                                    <span class="badge bg-success fs-6">{{ strtoupper($supplierTicket->status) }}</span>
                                @elseif($supplierTicket->status === 'rejected')
                                    <span class="badge bg-danger fs-6">{{ strtoupper($supplierTicket->status) }}</span>
                                @elseif($supplierTicket->status === 'completed')
                                    <span class="badge bg-info fs-6"
                                        style="color: #fff;">{{ strtoupper($supplierTicket->status) }}</span>
                                @else
                                    <span class="badge bg-secondary fs-6">{{ strtoupper($supplierTicket->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Nomor Ticket:</strong></td>
                                        <td>{{ $supplierTicket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier:</strong></td>
                                        <td>{{ $supplierTicket->supplier_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nomor PO:</strong></td>
                                        <td><code>{{ $supplierTicket->po_number }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Delivery Document:</strong></td>
                                        <td>{{ $supplierTicket->supplier_delivery_doc }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Delivery:</strong></td>
                                        <td>{{ $supplierTicket->delivery_date->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if ($supplierTicket->status === 'processed')
                                                <span
                                                    class="badge bg-warning">{{ strtoupper($supplierTicket->status) }}</span>
                                            @elseif($supplierTicket->status === 'approved')
                                                <span
                                                    class="badge bg-success">{{ strtoupper($supplierTicket->status) }}</span>
                                            @elseif($supplierTicket->status === 'rejected')
                                                <span
                                                    class="badge bg-danger">{{ strtoupper($supplierTicket->status) }}</span>
                                            @elseif($supplierTicket->status === 'completed')
                                                <span class="badge bg-info"
                                                    style="color: #fff;">{{ strtoupper($supplierTicket->status) }}</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ strtoupper($supplierTicket->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dibuat oleh:</strong></td>
                                        <td>{{ $supplierTicket->creator->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Dibuat:</strong></td>
                                        <td>{{ $supplierTicket->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if ($supplierTicket->processed_by)
                                        <tr>
                                            <td><strong>Diproses oleh:</strong></td>
                                            <td>{{ $supplierTicket->processor->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Diproses:</strong></td>
                                            <td>{{ $supplierTicket->processed_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if ($supplierTicket->description)
                            <div class="row">
                                <div class="col-12">
                                    <h6><strong>Deskripsi:</strong></h6>
                                    <p class="text-muted">{{ $supplierTicket->description }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($supplierTicket->notes)
                            <div class="row">
                                <div class="col-12">
                                    <h6><strong>Notes:</strong></h6>
                                    <p class="text-muted">{{ $supplierTicket->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($supplierTicket->rejection_reason)
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        <h6><strong>Alasan Reject:</strong></h6>
                                        <p>{{ $supplierTicket->rejection_reason }}</p>
                                        @if ($supplierTicket->rejected_quantity || $supplierTicket->accepted_quantity)
                                            <div class="row mt-2">
                                                @if ($supplierTicket->accepted_quantity)
                                                    <div class="col-md-6">
                                                        <strong>Jumlah Diterima:</strong>
                                                        {{ $supplierTicket->accepted_quantity }}
                                                    </div>
                                                @endif
                                                @if ($supplierTicket->rejected_quantity)
                                                    <div class="col-md-6">
                                                        <strong>Jumlah Ditolak:</strong>
                                                        {{ $supplierTicket->rejected_quantity }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Supplier</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Nama:</strong></td>
                                <td>{{ $supplierTicket->supplier_name }}</td>
                            </tr>
                            @if ($supplierTicket->supplier_contact)
                                <tr>
                                    <td><strong>Kontak:</strong></td>
                                    <td>{{ $supplierTicket->supplier_contact }}</td>
                                </tr>
                            @endif
                            @if ($supplierTicket->supplier_email)
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $supplierTicket->supplier_email }}</td>
                                </tr>
                            @endif
                            @if ($supplierTicket->supplier_address)
                                <tr>
                                    <td><strong>Alamat:</strong></td>
                                    <td>{{ $supplierTicket->supplier_address }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- PO Details -->
                @if ($poDetails && $poItems && $poItems->isNotEmpty())
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detail Purchase Order</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $firstItem = $poItems->first();
                            @endphp

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>PO Number:</strong></td>
                                            <td><code>{{ $firstItem->DocNo ?? 'N/A' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier Code:</strong></td>
                                            <td>{{ $firstItem->SupplierCode ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier Name:</strong></td>
                                            <td>{{ $firstItem->supplierName ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Supplier Contact:</strong></td>
                                            <td>{{ $firstItem->supplierContact ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier Email:</strong></td>
                                            <td>{{ $firstItem->supplierEmail ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Items:</strong></td>
                                            <td><span class="badge bg-info">{{ $poItems->count() }} Items</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if ($firstItem->supplierAddress)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <strong>Supplier Address:</strong><br>
                                        <span class="text-muted">{{ $firstItem->supplierAddress }}</span>
                                    </div>
                                </div>
                            @endif

                            <h6><strong>Item Details:</strong></h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Material Code</th>
                                            <th>Material Name</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($poItems as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $item->MaterialCode ?? 'N/A' }}</code></td>
                                                <td>{{ $item->materialName ?? 'N/A' }}</td>
                                                <td class="text-end">{{ number_format($item->Qty ?? 0, 4) }}</td>
                                                <td>{{ $item->unit ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detail Purchase Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle"></i>
                                Detail PO tidak dapat dimuat. Silakan periksa nomor PO:
                                <code>{{ $supplierTicket->po_number }}</code>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($supplierTicket->hasRejectedItems())
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-close-circle text-danger"></i>
                                Detail Item yang Ditolak
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-3">
                                <i class="mdi mdi-alert-circle"></i>
                                <strong>Alasan Penolakan:</strong> {{ $supplierTicket->rejection_reason }}
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Material Code</th>
                                            <th width="35%">Material Name</th>
                                            <th width="15%">Unit</th>
                                            <th width="15%">Qty Rejected</th>
                                            <th width="10%">Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supplierTicket->getRejectedItems() as $index => $rejectedItem)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $rejectedItem['material_code'] }}</code></td>
                                                <td>
                                                    @php
                                                        // Find material name from PO items
                                                        $poItem = $poItems->firstWhere(
                                                            'MaterialCode',
                                                            $rejectedItem['material_code'],
                                                        );
                                                    @endphp
                                                    {{ $poItem->materialName ?? 'N/A' }}
                                                </td>
                                                <td>{{ $poItem->unit ?? 'PCS' }}</td>
                                                <td class="text-end">
                                                    <span
                                                        class="badge bg-danger">{{ number_format($rejectedItem['rejected_qty'], 4) }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $rejectedItem['reason'] }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Total Rejected Quantity:</strong>
                                    <span
                                        class="badge bg-danger fs-6">{{ number_format($supplierTicket->rejected_quantity, 4) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Rejection Date:</strong>
                                    <span
                                        class="text-muted">{{ $supplierTicket->rejection_date ? \Carbon\Carbon::parse($supplierTicket->rejection_date)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        @if ($supplierTicket->status === 'processed')
                            <div class="d-grid gap-2">
                                @php
                                    // Determine document type for display
                                    $poNumber = $supplierTicket->po_number;
                                    $isPON = str_contains($poNumber, 'PON');
                                    $isPOM = str_contains($poNumber, 'POM');

                                    // Check if POM has BP material (simplified check)
                                    $hasBPMaterial = false;
                                    if ($poItems && $poItems->isNotEmpty()) {
                                        $hasBPMaterial = $poItems
                                            ->filter(function ($item) {
                                                return str_starts_with($item->MaterialCode ?? '', 'BP');
                                            })
                                            ->isNotEmpty();
                                    }

                                    $documentType = 'GRD'; // Default
                                    if ($isPON) {
                                        $documentType = 'GRD';
                                    } elseif ($isPOM) {
                                        $documentType = $hasBPMaterial ? 'GRD' : 'PQC';
                                    }
                                @endphp

                                @if ($documentType === 'GRD')
                                    <a href="{{ route('admin.supplier-tickets.create-grd-form', $supplierTicket->id) }}"
                                        class="btn btn-info btn-lg" style="width: 100%;">
                                        <i class="mdi mdi-file-document"></i> Create GRD Document
                                    </a>
                                    <small class="text-muted text-center">
                                        <i class="mdi mdi-information"></i>
                                        PO Type: {{ $isPON ? 'PON' : ($isPOM ? 'POM' : 'Unknown') }}
                                        @if ($isPOM && $hasBPMaterial)
                                            (BP Material detected)
                                        @endif
                                    </small>
                                @else
                                    <a href="{{ route('admin.supplier-tickets.create-pqc-form', $supplierTicket->id) }}"
                                        class="btn btn-warning btn-lg" style="width: 100%;">
                                        <i class="mdi mdi-clipboard-check"></i> Create PQC Document
                                    </a>
                                    <br>
                                    <small class="text-muted text-center">
                                        <i class="mdi mdi-information"></i>
                                        PO Type: POM (Purchase Order Stock)
                                    </small>
                                @endif

                                <hr>

                                {{-- <a href="{{ route('admin.supplier-tickets.reject-form', $supplierTicket->id) }}" 
                                    class="btn btn-danger btn-lg" style="width: 100%;">
                                    <i class="mdi mdi-close"></i> Reject Ticket
                                </a> --}}
                            </div>
                        @elseif($supplierTicket->status === 'completed')
                            <div class="alert alert-info mb-3">
                                <i class="mdi mdi-information"></i>
                                Ticket telah selesai diproses.
                            </div>

                            <div class="d-grid">
                                @if ($supplierTicket->hasRejectedItems())
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#sendRejectionModal" style="width: 100%;">
                                        <i class="mdi mdi-email"></i> Kirim Surat
                                    </button>
                                @endif
                            </div>
                        @elseif($supplierTicket->status === 'rejected')
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle"></i>
                                Ticket telah direject.
                            </div>
                        @endif



                        <hr>
                        <div class="d-grid">
                            <a href="{{ route('admin.supplier-tickets.index') }}" class="btn btn-outline-secondary"
                                style="width: 100%;">
                                <i class="mdi mdi-arrow-left"></i> Kembali ke List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Send Rejection Letter Modal -->
        <div class="modal fade" id="sendRejectionModal" tabindex="-1" aria-labelledby="sendRejectionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendRejectionModalLabel">
                            <i class="mdi mdi-email"></i> Kirim Surat Penolakan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Preview Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">Preview Surat Penolakan:</h6>
                                <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                                    <iframe
                                        src="{{ route('admin.supplier-tickets.rejection-letter', $supplierTicket->id) }}"
                                        width="100%" height="400" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Email Form -->
                        <form id="sendRejectionForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recipient_email" class="form-label">Email Tujuan</label>
                                        <input type="email" class="form-control" id="recipient_email"
                                            name="recipient_email"
                                            value="{{ $supplierTicket->supplier_email ?? 'jalu.bagaskara@krisanthium.com' }}"
                                            required>
                                        <div class="form-text">Email akan dikirim ke alamat ini</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cc_email" class="form-label">CC Email (Opsional)</label>
                                        <input type="email" class="form-control" id="cc_email" name="cc_email"
                                            value="jalu.bagaskara@krisanthium.com">
                                        <div class="form-text">Email tambahan untuk copy</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email_subject" class="form-label">Subject Email</label>
                                <input type="text" class="form-control" id="email_subject" name="email_subject"
                                    value="SURAT PENOLAKAN - {{ $supplierTicket->po_number }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_message" class="form-label">Pesan Tambahan</label>
                                <textarea class="form-control" id="email_message" name="email_message" rows="3"
                                    placeholder="Pesan tambahan yang akan ditambahkan di email..."></textarea>
                            </div>

                            <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                <strong>Informasi:</strong> Surat penolakan akan dikirim sebagai PDF attachment. Email akan
                                berisi pesan singkat dan file PDF terlampir.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="sendRejectionBtn">
                            <i class="mdi mdi-email"></i> Kirim Email
                        </button>
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
                // Auto-hide alerts
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);

                // Send rejection email functionality
                $('#sendRejectionBtn').click(function() {
                    const formData = new FormData(document.getElementById('sendRejectionForm'));
                    const btn = this;

                    // Disable button and show loading
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Mengirim...';

                    // Send AJAX request
                    fetch('{{ route('admin.supplier-tickets.send-rejection-email', $supplierTicket->id) }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Surat penolakan telah dikirim ke supplier',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Close modal
                                    $('#sendRejectionModal').modal('hide');
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan saat mengirim email',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengirim email',
                                confirmButtonText: 'OK'
                            });
                        })
                        .finally(() => {
                            // Re-enable button
                            btn.disabled = false;
                            btn.innerHTML = '<i class="mdi mdi-email"></i> Kirim Email';
                        });
                });
            });
        </script>
    @endsection

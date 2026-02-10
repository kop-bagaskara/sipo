@extends('main.layouts.main')
@section('title')
    Ticketing Supplier
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
    </style>
@endsection
@section('page-title')
    Ticketing Supplier
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Ticketing Supplier</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Ticketing Supplier</li>
            </ol>
        </div>
    </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Supplier Ticket: {{ $supplierTicket->ticket_number }}</h3>
                        <div class="btn-group">
                            <a href="{{ route('supplier-tickets.edit', $supplierTicket) }}" class="btn btn-warning">
                                <i class="mdi mdi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('supplier-tickets.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Ticket Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Informasi Ticket</h5>

                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Ticket Number:</strong></td>
                                        <td>{{ $supplierTicket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PO Number:</strong></td>
                                        <td><code>{{ $supplierTicket->po_number }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier Delivery Doc:</strong></td>
                                        <td>{{ $supplierTicket->supplier_delivery_doc }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Delivery Date:</strong></td>
                                        <td>{{ $supplierTicket->delivery_date->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge {{ $supplierTicket->getStatusBadgeClass() }}">
                                                {{ ucfirst($supplierTicket->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $supplierTicket->creator->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $supplierTicket->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if ($supplierTicket->processed_by)
                                        <tr>
                                            <td><strong>Processed By:</strong></td>
                                            <td>{{ $supplierTicket->processor->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Processed At:</strong></td>
                                            <td>{{ $supplierTicket->processed_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Supplier Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Informasi Supplier</h5>

                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Nama:</strong></td>
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


                        <!-- Description and Notes -->
                        @if ($supplierTicket->description || $supplierTicket->notes)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Deskripsi & Catatan</h5>

                                    @if ($supplierTicket->description)
                                        <div class="mb-3">
                                            <strong>Deskripsi Pengiriman:</strong>
                                            <p class="mt-1">{{ $supplierTicket->description }}</p>
                                        </div>
                                    @endif

                                    @if ($supplierTicket->notes)
                                        <div class="mb-3">
                                            <strong>Catatan:</strong>
                                            <p class="mt-1">{{ $supplierTicket->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif


                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    @if ($supplierTicket->status === 'pending')
                                        <form method="POST"
                                            action="{{ route('supplier-tickets.approve', $supplierTicket) }}"
                                            class="d-inline" onsubmit="return confirm('Approve ticket ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @endif

                                    @if ($supplierTicket->canBeProcessed())
                                        <form method="POST"
                                            action="{{ route('supplier-tickets.process', $supplierTicket) }}"
                                            class="d-inline" onsubmit="return confirm('Mark as processed?')">
                                            @csrf
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-cogs"></i> Process
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('supplier-tickets.reject', $supplierTicket) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Alasan Rejection</label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

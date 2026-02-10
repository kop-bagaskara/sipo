@extends('main.layouts.main')

@section('title', 'GRD Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">GRD Details: {{ $supplierTicket->ticket_number }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('grd.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Ticket Information -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Supplier Ticket Information</h5>
                            
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
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $supplierTicket->getStatusBadgeClass() }}">
                                            {{ ucfirst($supplierTicket->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $supplierTicket->supplier_name }}</td>
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
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $supplierTicket->creator->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $supplierTicket->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- GRD Information -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">GRD Information</h5>
                            
                            @if($grdDetails && $grdDetails['header'])
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>GRD Number:</strong></td>
                                        <td><code>{{ $grdDetails['header']->DocNo }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Series:</strong></td>
                                        <td>{{ $grdDetails['header']->Series }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Document Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($grdDetails['header']->DocDate)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier Code:</strong></td>
                                        <td>{{ $grdDetails['header']->SupplierCode }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Location:</strong></td>
                                        <td>{{ $grdDetails['header']->Location }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Zone:</strong></td>
                                        <td>{{ $grdDetails['header']->Zone }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vehicle No:</strong></td>
                                        <td>{{ $grdDetails['header']->VehicleNo ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ $grdDetails['header']->Status }}</span>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    GRD belum dibuat untuk ticket ini.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- GRD Items -->
                    @if($grdDetails && $grdDetails['details'] && count($grdDetails['details']) > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Received Items</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Material Code</th>
                                            <th>Batch No</th>
                                            <th>Batch Info</th>
                                            <th>Expiry Date</th>
                                            <th>Tag No</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Info</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grdDetails['details'] as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $item->MaterialCode }}</code></td>
                                                <td>{{ $item->BatchNo }}</td>
                                                <td>{{ $item->BatchInfo }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->ExpiryDate)->format('d/m/Y') }}</td>
                                                <td>{{ $item->TagNo }}</td>
                                                <td>{{ $item->Unit }}</td>
                                                <td class="text-end">{{ number_format($item->Qty, 4) }}</td>
                                                <td>{{ $item->Info ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($supplierTicket->description || $supplierTicket->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Notes</h5>
                            
                            @if($supplierTicket->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mt-1">{{ $supplierTicket->description }}</p>
                            </div>
                            @endif
                            
                            @if($supplierTicket->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong>
                                <p class="mt-1">{{ $supplierTicket->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

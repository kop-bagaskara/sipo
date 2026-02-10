@extends('layouts.app')

@section('title', 'Detail Good Receipt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detail Good Receipt: {{ $goodReceipt->receipt_number }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('good-receipts.edit', $goodReceipt) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('good-receipts.print', $goodReceipt) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('good-receipts.index') }}" class="btn btn-secondary">
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
                        <!-- Receipt Information -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Informasi Receipt</h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Receipt Number:</strong></td>
                                    <td>{{ $goodReceipt->receipt_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $goodReceipt->getStatusBadgeClass() }}">
                                            {{ ucfirst($goodReceipt->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Received By:</strong></td>
                                    <td>{{ $goodReceipt->receiver->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Received Date:</strong></td>
                                    <td>{{ $goodReceipt->received_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Quality Check:</strong></td>
                                    <td>
                                        @if($goodReceipt->quality_check)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Quantity Match:</strong></td>
                                    <td>
                                        @if($goodReceipt->quantity_match)
                                            <span class="badge bg-success">Match</span>
                                        @else
                                            <span class="badge bg-warning">Not Match</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Supplier Ticket Information -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Supplier Ticket Information</h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Ticket Number:</strong></td>
                                    <td>
                                        <a href="{{ route('supplier-tickets.show', $goodReceipt->supplier_ticket_id) }}" 
                                           class="text-decoration-none">
                                            {{ $goodReceipt->supplierTicket->ticket_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $goodReceipt->supplierTicket->supplier_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery Date:</strong></td>
                                    <td>{{ $goodReceipt->supplierTicket->delivery_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ticket Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $goodReceipt->supplierTicket->getStatusBadgeClass() }}">
                                            {{ ucfirst($goodReceipt->supplierTicket->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Received Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Item yang Diterima</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Item</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Condition</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goodReceipt->received_items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item['item_name'] }}</td>
                                                <td>{{ $item['quantity'] }}</td>
                                                <td>{{ $item['unit'] }}</td>
                                                <td>
                                                    @php
                                                        $conditionClass = match($item['condition']) {
                                                            'good' => 'bg-success',
                                                            'damaged' => 'bg-warning',
                                                            'defective' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $conditionClass }}">
                                                        {{ ucfirst($item['condition']) }}
                                                    </span>
                                                </td>
                                                <td>{{ $item['notes'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($goodReceipt->notes || $goodReceipt->condition_notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Catatan</h5>
                            
                            @if($goodReceipt->notes)
                            <div class="mb-3">
                                <strong>Catatan Receipt:</strong>
                                <p class="mt-1">{{ $goodReceipt->notes }}</p>
                            </div>
                            @endif
                            
                            @if($goodReceipt->condition_notes)
                            <div class="mb-3">
                                <strong>Catatan Kondisi Barang:</strong>
                                <p class="mt-1">{{ $goodReceipt->condition_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                @if($goodReceipt->status === 'pending')
                                    <form method="POST" action="{{ route('good-receipts.accept', $goodReceipt) }}" 
                                          class="d-inline" onsubmit="return confirm('Accept good receipt ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject
                                    </button>

                                    <form method="POST" action="{{ route('good-receipts.partial', $goodReceipt) }}" 
                                          class="d-inline" onsubmit="return confirm('Mark as partial receipt?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Mark as Partial
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
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('good-receipts.reject', $goodReceipt) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Good Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Rejection</label>
                        <textarea name="rejection_reason" id="rejection_reason" 
                                  class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

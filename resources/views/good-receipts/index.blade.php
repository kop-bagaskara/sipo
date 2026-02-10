@extends('layouts.app')

@section('title', 'Good Receipts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Good Receipts</h3>
                    <a href="{{ route('good-receipts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Good Receipt Baru
                    </a>
                </div>
                
                <!-- Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('good-receipts.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="ticket_number" class="form-label">Ticket Number</label>
                            <input type="text" name="ticket_number" id="ticket_number" class="form-control" 
                                   value="{{ request('ticket_number') }}" placeholder="Nomor ticket...">
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('good-receipts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </form>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Receipt Number</th>
                                    <th>Ticket Number</th>
                                    <th>Supplier</th>
                                    <th>Received Date</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Received By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                    <tr>
                                        <td>
                                            <strong>{{ $receipt->receipt_number }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('supplier-tickets.show', $receipt->supplier_ticket_id) }}" 
                                               class="text-decoration-none">
                                                {{ $receipt->supplierTicket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>{{ $receipt->supplierTicket->supplier_name }}</td>
                                        <td>{{ $receipt->received_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ count($receipt->received_items) }} items</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $receipt->getStatusBadgeClass() }}">
                                                {{ ucfirst($receipt->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $receipt->receiver->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('good-receipts.show', $receipt) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('good-receipts.edit', $receipt) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                @if($receipt->status === 'pending')
                                                    <form method="POST" action="{{ route('good-receipts.accept', $receipt) }}" 
                                                          class="d-inline" onsubmit="return confirm('Accept good receipt ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Accept">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $receipt->id }}" title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <form method="POST" action="{{ route('good-receipts.partial', $receipt) }}" 
                                                          class="d-inline" onsubmit="return confirm('Mark as partial receipt?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Partial">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('good-receipts.print', $receipt) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Print" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $receipt->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('good-receipts.reject', $receipt) }}">
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
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Tidak ada good receipt ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $receipts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

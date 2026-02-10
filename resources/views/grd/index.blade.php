@extends('main.layouts.main')

@section('title', 'GRD Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">GRD Management</h3>
                    <a href="{{ route('grd.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> CREATE GRD
                    </a>
                </div>
                
                <!-- Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('grd.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="po_number" class="form-label">PO Number</label>
                            <input type="text" name="po_number" id="po_number" class="form-control" 
                                   value="{{ request('po_number') }}" placeholder="Nomor PO...">
                        </div>
                        <div class="col-md-2">
                            <label for="supplier" class="form-label">Supplier</label>
                            <input type="text" name="supplier" id="supplier" class="form-control" 
                                   value="{{ request('supplier') }}" placeholder="Nama supplier...">
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
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('grd.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Ticket Number</th>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <strong>{{ $ticket->ticket_number }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $ticket->po_number }}</code>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $ticket->supplier_name }}</strong>
                                                @if($ticket->supplier_contact)
                                                    <br><small class="text-muted">{{ $ticket->supplier_contact }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $ticket->delivery_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('grd.show', $ticket) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(in_array($ticket->status, ['approved', 'processed']))
                                                    <a href="{{ route('grd.create', ['ticket_id' => $ticket->id]) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Create GRD">
                                                        <i class="fas fa-plus"></i> GRD
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Tidak ada ticket ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

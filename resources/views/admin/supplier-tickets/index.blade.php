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
@section('page-title')
    Admin - Supplier Tickets
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Admin - Supplier Tickets</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Admin - Supplier Tickets</li>
                </ol>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filter Tickets</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.supplier-tickets.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select form-control">
                                            <option value="">Semua Status</option>
                                            <option value="processed"
                                                {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                                            <option value="approved"
                                                {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected"
                                                {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <input type="text" name="supplier" id="supplier" class="form-control"
                                            value="{{ request('supplier') }}" placeholder="Nama supplier...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="date_from" class="form-label">Dari Tanggal</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Daftar Supplier Tickets</h5>
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-pill bg-info text-white" >{{ $tickets->total() }} Total Tickets</span>
                            </div>
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

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ticket</th>
                                        <th>Supplier</th>
                                        <th>PO Number</th>
                                        <th>Delivery Doc</th>
                                        <th>Del. Date</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $index => $ticket)
                                        <tr>
                                            <td>{{ $tickets->firstItem() + $index }}</td>
                                            <td>
                                                <a href="{{ route('admin.supplier-tickets.show', $ticket->id) }}"
                                                    class="text-primary fw-bold">
                                                    {{ $ticket->ticket_number }}
                                                </a>
                                            </td>
                                            <td>{{ $ticket->supplier_name }}</td>
                                            <td><code>{{ $ticket->po_number }}</code></td>
                                            <td>{{ $ticket->supplier_delivery_doc }}</td>
                                            <td>{{ $ticket->delivery_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($ticket->status === 'processed')
                                                    <span
                                                        class="badge bg-warning">{{ strtoupper($ticket->status) }}</span>
                                                @elseif($ticket->status === 'approved')
                                                    <span
                                                        class="badge bg-success">{{ strtoupper($ticket->status) }}</span>
                                                @elseif($ticket->status === 'rejected')
                                                    <span class="badge bg-danger">{{ strtoupper($ticket->status) }}</span>
                                                @elseif($ticket->status === 'completed')
                                                    <span class="badge bg-info" style="color: #fff;">{{ strtoupper($ticket->status) }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary">{{ strtoupper($ticket->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->creator->name ?? 'N/A' }}</td>
                                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.supplier-tickets.show', $ticket->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>

                                                    {{-- @if ($ticket->status === 'processed')
                                                        <form method="POST"
                                                            action="{{ route('admin.supplier-tickets.approve', $ticket->id) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Approve ticket ini?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                                title="Approve">
                                                                <i class="mdi mdi-check"></i>
                                                            </button>
                                                        </form>

                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $ticket->id }}"
                                                            title="Reject">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @endif

                                                    @if ($ticket->status === 'approved')
                                                        <a href="{{ route('admin.supplier-tickets.create-document', $ticket->id) }}"
                                                            class="btn btn-sm btn-outline-info" title="Create Document">
                                                            <i class="mdi mdi-file-document"></i>
                                                        </a>
                                                    @endif --}}
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $ticket->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Ticket -
                                                            {{ $ticket->ticket_number }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('admin.supplier-tickets.reject', $ticket->id) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="rejection_date{{ $ticket->id }}"
                                                                            class="form-label">
                                                                            Tanggal Reject <span
                                                                                class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="date" name="rejection_date"
                                                                            id="rejection_date{{ $ticket->id }}"
                                                                            class="form-control" required
                                                                            value="{{ date('Y-m-d') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="accepted_quantity{{ $ticket->id }}"
                                                                            class="form-label">
                                                                            Jumlah yang Diterima
                                                                        </label>
                                                                        <input type="number" name="accepted_quantity"
                                                                            id="accepted_quantity{{ $ticket->id }}"
                                                                            class="form-control" step="0.01"
                                                                            min="0">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="rejected_quantity{{ $ticket->id }}"
                                                                    class="form-label">
                                                                    Jumlah yang Ditolak
                                                                </label>
                                                                <input type="number" name="rejected_quantity"
                                                                    id="rejected_quantity{{ $ticket->id }}"
                                                                    class="form-control" step="0.01" min="0">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="rejection_reason{{ $ticket->id }}"
                                                                    class="form-label">
                                                                    Alasan Reject <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea name="rejection_reason" id="rejection_reason{{ $ticket->id }}" class="form-control" rows="4"
                                                                    required placeholder="Masukkan alasan reject secara detail..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="mdi mdi-close"></i> Reject Ticket
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                <i class="mdi mdi-information-outline"></i>
                                                Tidak ada supplier tickets ditemukan
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
    @endsection

    @section('script')
        <script>
            $(document).ready(function() {
                // Auto-hide alerts
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endsection

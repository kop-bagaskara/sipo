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
                <h3 class="text-themecolor">Ticketing Supplier</h3>
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
                        <h3 class="card-title">Daftar Supplier Tickets</h3>
                        <a href="{{ route('supplier-tickets.create') }}" class="btn btn-info">
                            <i class="mdi mdi-plus"></i> Buat Ticket Baru
                        </a>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('supplier-tickets.index') }}" class="row g-3">
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
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-info me-2" style="margin-right: 20px;">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <a href="{{ route('supplier-tickets.index') }}" class="btn btn-outline-info">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="ticketsTable" class="table table-striped table-hover">
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
                                                    @if ($ticket->supplier_contact)
                                                        <br><small
                                                            class="text-muted">{{ $ticket->supplier_contact }}</small>
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
                                                    <a href="{{ route('supplier-tickets.show', $ticket) }}"
                                                        class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    @if($ticket->status === 'processed')
                                                        <a href="{{ route('supplier-tickets.edit', $ticket) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete({{ $ticket->id }}, '{{ $ticket->ticket_number }}')" 
                                                            title="Delete">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat diedit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat dihapus">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $ticket->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('supplier-tickets.reject', $ticket) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Ticket</h5>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="rejection_reason" class="form-label">Alasan
                                                                    Rejection</label>
                                                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject
                                                                Ticket</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-inbox"></i>
                                                    <p>Tidak ada supplier ticket ditemukan.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- DataTables init -->
                        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
                        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
                        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
                        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
                        <script>
                            $(function () {
                                $('#ticketsTable').DataTable({
                                    pageLength: 10,
                                    order: [[6, 'desc']],
                                    responsive: true,
                                });
                            });

                            // Define confirmDelete function globally
                            window.confirmDelete = function(ticketId, ticketNumber) {
                                Swal.fire({
                                    title: 'Konfirmasi Hapus',
                                    text: `Apakah Anda yakin ingin menghapus ticket ${ticketNumber}?`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Ya, Hapus!',
                                    cancelButtonText: 'Batal',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show loading
                                        Swal.fire({
                                            title: 'Menghapus...',
                                            text: 'Sedang menghapus ticket',
                                            allowOutsideClick: false,
                                            didOpen: () => {
                                                Swal.showLoading();
                                            }
                                        });

                                        // Create form and submit
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = `/sipo/supplier-tickets/${ticketId}`;
                                        
                                        // Add CSRF token
                                        const csrfToken = document.createElement('input');
                                        csrfToken.type = 'hidden';
                                        csrfToken.name = '_token';
                                        csrfToken.value = '{{ csrf_token() }}';
                                        form.appendChild(csrfToken);
                                        
                                        // Add method override for DELETE
                                        const methodField = document.createElement('input');
                                        methodField.type = 'hidden';
                                        methodField.name = '_method';
                                        methodField.value = 'DELETE';
                                        form.appendChild(methodField);
                                        
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                });
                            };
                        </script>
                    </div>
                </div>
            </div>
        </div>
    @endsection

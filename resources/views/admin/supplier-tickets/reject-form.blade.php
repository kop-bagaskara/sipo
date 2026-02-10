@extends('main.layouts.main')
@section('title')
    Reject Form
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
    Reject Form
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Reject Form</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Reject Form</li>
                </ol>
            </div>
        </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-close-circle text-danger"></i>
                        Form Penolakan Supplier Ticket
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
                                    <td>{{ $supplierTicket->supplier_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Arrival Date:</strong></td>
                                    <td>{{ $supplierTicket->arrival_date ? \Carbon\Carbon::parse($supplierTicket->arrival_date)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status Saat Ini</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($supplierTicket->status === 'processed')
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
                    <form action="{{ route('admin.supplier-tickets.process-reject', $supplierTicket->id) }}" method="POST" id="rejectForm">
                        @csrf


                        <!-- Item Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Detail Item yang Ditolak</h6>
                                
                                @if ($supplierTicket->hasRejectedItems())
                                    <!-- Show rejected items from JSON data -->
                                    <div class="alert alert-info mb-3">
                                        <i class="mdi mdi-information"></i>
                                        <strong>Item yang sudah ditolak:</strong> Berikut adalah detail item yang telah ditolak sebelumnya.
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="rejectedItemsTable">
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
                                                @php
                                                    $rejectedItems = $supplierTicket->getRejectedItems();
                                                @endphp
                                                @foreach ($rejectedItems as $index => $rejectedItem)
                                                    @php
                                                        // Find the corresponding PO item for additional details
                                                        $poItem = $poItemsForGRD->firstWhere('Code', $rejectedItem['material_code']);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td><code>{{ $rejectedItem['material_code'] }}</code></td>
                                                        <td>{{ $poItem->Name ?? 'N/A' }}</td>
                                                        <td>{{ $poItem->Unit ?? 'PCS' }}</td>
                                                        <td class="text-right">{{ number_format($poItem->QtyPOTotal ?? 0, 4) }}</td>
                                                        <td class="text-right">{{ number_format($poItem->QtyPORemain ?? 0, 4) }}</td>
                                                        <td class="text-right">
                                                            <span class="badge bg-danger">{{ number_format($rejectedItem['rejected_qty'], 4) }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6 class="text-muted">Alasan Penolakan:</h6>
                                        <div class="alert alert-light">
                                            {{ $supplierTicket->rejection_reason }}
                                        </div>
                                    </div>
                                    
                                @elseif ($poItemsForGRD && $poItemsForGRD->isNotEmpty())
                                    <!-- Show input form for new rejections -->
                                    <div class="alert alert-warning mb-3">
                                        <i class="mdi mdi-alert-circle"></i>
                                        <strong>Belum ada item yang ditolak.</strong> Silakan isi quantity yang akan ditolak di bawah ini.
                                    </div>
                                    
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
                                                        <td class="text-right">{{ number_format($item->QtyPOTotal ?? 0, 4) }}</td>
                                                        <td class="text-right">{{ number_format($item->QtyPORemain ?? 0, 4) }}</td>
                                                        <td>
                                                            <input type="hidden" name="rejected_items[{{ $index }}][material_code]" value="{{ $item->Code ?? '' }}">
                                                            <input type="number" 
                                                                class="form-control form-control-sm rejected-qty" 
                                                                name="rejected_items[{{ $index }}][rejected_qty]"
                                                                value="0"
                                                                step="0.0001" 
                                                                min="0" 
                                                                max="{{ $item->QtyPORemain ?? 0 }}"
                                                                data-max-qty="{{ $item->QtyPORemain ?? 0 }}"
                                                                required>
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
                                    <a href="{{ route('admin.supplier-tickets.show', $supplierTicket->id) }}" 
                                        class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Kembali
                                    </a>
                                    
                                    @if ($supplierTicket->hasRejectedItems())
                                        <!-- Show info when items are already rejected -->
                                        <div class="alert alert-success d-inline-block mb-0">
                                            <i class="mdi mdi-check-circle"></i>
                                            <strong>Item sudah ditolak!</strong> Surat reject telah dibuat sebelumnya.
                                        </div>
                                    @else
                                        <!-- Show submit button for new rejections -->
                                        <button type="submit" class="btn btn-danger" id="submitRejectBtn">
                                            <i class="mdi mdi-close-circle"></i> Buat Surat Reject
                                        </button>
                                    @endif
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
    // Only run validation if there are input fields (no rejected items yet)
    @if (!$supplierTicket->hasRejectedItems())
        // Quantity validation
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

        // Form submission with confirmation
        $('#rejectForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check if any rejection quantity is entered
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

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin membuat surat reject?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Buat Surat Reject!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membuat surat reject',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Submit form
                    document.getElementById('rejectForm').submit();
                }
            });
        });
    @endif
});
</script>
@endsection

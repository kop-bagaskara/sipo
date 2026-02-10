@extends('main.layouts.main')
@section('title')
    Admin - Supplier Tickets - Create GRD Form
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
    Create GRD Form
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Goods Receipt</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="grdTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="header-tab" data-toggle="tab" data-target="#header"
                                    type="button" role="tab">
                                    Header
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detail-tab" data-toggle="tab" data-target="#detail"
                                    type="button" role="tab">
                                    Detail
                                </button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="files-tab" data-toggle="tab" data-target="#files"
                                    type="button" role="tab">
                                    Files
                                </button>
                            </li> --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejection-tab" data-toggle="tab" data-target="#rejection"
                                    type="button" role="tab">
                                    Rejection
                                </button>
                            </li>
                        </ul>

                        <form id="grdForm" method="POST"
                            action="{{ route('admin.supplier-tickets.create-grd', $supplierTicket->id) }}">
                            @csrf

                            <div class="tab-content mt-3" id="grdTabsContent">
                                <!-- Header Tab -->
                                <div class="tab-pane fade show active" id="header" role="tabpanel">

                                    {{-- hidden input --}}
                                    <input type="hidden" name="zone" value="">

                                    <br>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="series" class="form-label">Series</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <select class="form-control" id="series" name="series" required>
                                                    <option value="">Select Series</option>
                                                    <option value="GRD" {{ old('series') == 'GRD' ? 'selected' : '' }}>GRD - Goods Receipt Direct</option>
                                                    <option value="GRO" {{ old('series') == 'GRO' ? 'selected' : '' }}>GRO - Goods Receipt Order</option>
                                                    <option value="GRM" {{ old('series') == 'GRM' ? 'selected' : '' }}>GRM - Goods Receipt Manual</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="grd_number" class="form-label">Doc No</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <input type="text" class="form-control" id="grd_number" name="grd_number"
                                                    value="{{ $grdNumber }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="grd_number" class="form-label">Doc Date</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="docdate"
                                                            name="docdate"
                                                            value="{{ $supplierTicket->arrival_date ? \Carbon\Carbon::parse($supplierTicket->arrival_date)->format('d M Y') : now()->format('d M Y') }}"
                                                            readonly>
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
                                                    <select class="form-control" id="location" name="location" required>
                                                        <option value="">Select Location</option>
                                                        <option value="G19BK" {{ old('location') == 'G19BK' ? 'selected' : '' }}>G19BK</option>
                                                        <option value="G19SP" {{ old('location') == 'G19SP' ? 'selected' : '' }}>G19SP</option>
                                                        <option value="G23BK" {{ old('location') == 'G23BK' ? 'selected' : '' }}>G23BK</option>
                                                        <option value="GLAIN" {{ old('location') == 'GLAIN' ? 'selected' : '' }}>GLAIN</option>
                                                        <option value="GSERV" {{ old('location') == 'GSERV' ? 'selected' : '' }}>GSERV</option>
                                                        <option value="QC" {{ old('location') == 'QC' ? 'selected' : '' }}>QC</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="grd_number" class="form-label">Supplier</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="supplier"
                                                        name="supplier"
                                                        value="{{ $poItems->first()->SupplierCode ?? '' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="grd_number" class="form-label">PO Number</label>
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
                                            <label for="grd_number" class="form-label">Supplier Delivery No</label>
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
                                            <label for="grd_number" class="form-label">Vehicle No</label>
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
                                            <label for="grd_number" class="form-label">Information</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <textarea class="form-control" id="information" name="information" rows="3"
                                                    placeholder="Additional information..."></textarea>
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
                                                                <input type="text" class="form-control form-control-sm tag-no-field"
                                                                    name="tag_no[{{ $index }}]"
                                                                    readonly
                                                                    style="background-color: #f8f9fa; color: #6c757d;"
                                                                    title="Tag No akan otomatis di-generate berdasarkan DocDate untuk FIFO">
                                                            </td>
                                                            <td>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    name="expiry_date[{{ $index }}]"
                                                                    value="{{ $item->ExpiryDate ? \Carbon\Carbon::parse($item->ExpiryDate)->format('Y-m-d') : '' }}">
                                                            </td>
                                                            <td>{{ $item->Unit ?? 'PCS' }}</td>
                                                            <td class="text-right">{{ number_format($item->QtyPOTotal ?? 0, 4) }}</td>
                                                            <td class="text-right">{{ number_format($item->QtyPORemain ?? 0, 4) }}</td>
                                                            <td>
                                                                <input type="number" class="form-control form-control-sm qty-received"
                                                                    name="qty_received[{{ $index }}]"
                                                                    value="0"
                                                                    step="0.0001" min="0" max="{{ $item->QtyPORemain ?? 0 }}"
                                                                    data-original-qty="{{ $item->QtyPORemain ?? 0 }}"
                                                                    data-material-code="{{ $item->Code ?? '' }}"
                                                                    required>
                                                                <small class="text-muted">Max: {{ number_format($item->QtyPORemain ?? 0, 4) }}</small>
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
                                    {{-- <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <select class="form-select form-select-sm"
                                                    style="width: auto; display: inline-block;">
                                                    <option>Select All</option>
                                                </select>
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $poItems ? $poItems->count() : 0 }} row(s) returned. 0 row(s)
                                                    selected.</small>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>

                                <!-- Files Tab -->
                                {{-- <div class="tab-pane fade" id="files" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Attachments</label>
                                        <input type="file" class="form-control" id="attachments" name="attachments[]"
                                            multiple>
                                        <div class="form-text">Upload supporting documents (delivery notes, invoices, etc.)
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Rejection Tab -->
                                <div class="tab-pane fade" id="rejection" role="tabpanel">
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert-circle mr-2"></i>
                                        <strong>Perhatian:</strong> Jika ada item yang tidak sesuai standar, silakan isi form rejection di bawah ini.
                                    </div>

                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">Alasan Rejection</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                            placeholder="Jelaskan alasan rejection secara detail..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Item yang Di-reject</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="rejectionTable">
                                                <thead>
                                                    <tr>
                                                        <th>Material Code</th>
                                                        <th>Material Name</th>
                                                        <th>Qty PO</th>
                                                        <th>Qty Reject</th>
                                                        <th>Alasan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rejectionTableBody">
                                                    <!-- Rejection items will be added here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addRejectionItem">
                                            <i class="mdi mdi-plus"></i> Tambah Item Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            {{-- <div>
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Prev Doc
                                </button>
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-right"></i> Next Doc
                                </button>
                            </div> --}}
                            <div>
                                {{-- <button type="button" class="btn btn-outline-primary">
                                    <i class="mdi mdi-printer"></i> Print
                                </button>
                                <button type="button" class="btn btn-outline-info">
                                    <i class="mdi mdi-qrcode"></i> Print QR
                                </button> --}}
                                <button type="submit" form="grdForm" class="btn btn-success">
                                    <i class="mdi mdi-content-save"></i> Save
                                </button>
                                {{-- <button type="button" class="btn btn-outline-danger">
                                    <i class="mdi mdi-delete"></i> Delete
                                </button>
                                <a href="{{ route('admin.supplier-tickets.show', $supplierTicket->id) }}"
                                    class="btn btn-outline-warning">
                                    <i class="mdi mdi-arrow-left"></i> Cancel
                                </a> --}}
                            </div>
                        </div>
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
        console.log('Script loaded');
        
        $(document).ready(function() {

            generateTagNumbers();
            
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

                // Form validation and submission
                $('#grdForm').on('submit', function(e) {
                    console.log('Form submit triggered');
                    
                    // Check if rejection form has data
                    const hasRejectionData = $('#rejection_reason').val().trim() !== '' || 
                                          $('#rejectionTableBody tr').length > 0;
                    
                    if (hasRejectionData) {
                        // Validate rejection form
                        const rejectionReason = $('#rejection_reason').val().trim();
                        const rejectionItems = $('#rejectionTableBody tr').length;
                        
                        if (!rejectionReason) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Peringatan',
                                text: 'Alasan rejection harus diisi',
                                icon: 'warning'
                            });
                            return;
                        }
                        
                        if (rejectionItems === 0) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Peringatan',
                                text: 'Minimal harus ada 1 item yang di-reject',
                                icon: 'warning'
                            });
                            return;
                        }
                        
                        // Validate each rejection item
                        let hasValidRejection = false;
                        $('#rejectionTableBody tr').each(function() {
                            const materialCode = $(this).find('.material-select').val();
                            const rejectedQty = parseFloat($(this).find('input[name*="rejected_qty"]').val()) || 0;
                            const reason = $(this).find('input[name*="reason"]').val().trim();
                            
                            if (materialCode && rejectedQty > 0 && reason) {
                                hasValidRejection = true;
                            }
                        });
                        
                        if (!hasValidRejection) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Peringatan',
                                text: 'Semua item rejection harus memiliki material, quantity, dan alasan yang valid',
                                icon: 'warning'
                            });
                            return;
                        }
                    }
                    
                    console.log('Form will submit normally');
                    // Let form submit normally
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
                url: '{{ route("admin.supplier-tickets.generate-tagno") }}',
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

        // Rejection form handling
        let rejectionItemIndex = 0;

        // Add rejection item
        $('#addRejectionItem').on('click', function() {
            const poItems = @json($poItemsForGRD);
            if (poItems.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Tidak ada item PO yang tersedia',
                    icon: 'warning'
                });
                return;
            }

            let optionsHtml = '<option value="">Pilih Material</option>';
            poItems.forEach(function(item) {
                optionsHtml += `<option value="${item.Code}" data-name="${item.Name}" data-qty="${item.QtyPORemain}">${item.Code} - ${item.Name}</option>`;
            });

            const rowHtml = `
                <tr data-index="${rejectionItemIndex}">
                    <td>
                        <select class="form-control form-control-sm material-select" name="rejected_items[${rejectionItemIndex}][material_code]" required>
                            ${optionsHtml}
                        </select>
                    </td>
                    <td class="material-name">-</td>
                    <td class="po-qty">-</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" name="rejected_items[${rejectionItemIndex}][rejected_qty]" 
                               step="0.0001" min="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="rejected_items[${rejectionItemIndex}][reason]" 
                               placeholder="Alasan rejection" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-rejection-item">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#rejectionTableBody').append(rowHtml);
            rejectionItemIndex++;
        });

        // Handle material selection change
        $(document).on('change', '.material-select', function() {
            const $row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');
            const materialName = selectedOption.data('name');
            const poQty = selectedOption.data('qty');

            $row.find('.material-name').text(materialName || '-');
            $row.find('.po-qty').text(poQty || '-');
            $row.find('input[name*="rejected_qty"]').attr('max', poQty || 0);
        });

        // Remove rejection item
        $(document).on('click', '.remove-rejection-item', function() {
            $(this).closest('tr').remove();
        });

        // Update Doc Number when Series changes
        $('#series').on('change', function() {
            var selectedSeries = $(this).val();
            if (selectedSeries) {
                // Generate new doc number based on selected series
                $.ajax({
                    url: '{{ route("admin.supplier-tickets.generate-doc-number") }}',
                    method: 'POST',
                    data: {
                        series: selectedSeries,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.docNumber) {
                            $('#grd_number').val(response.docNumber);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error generating doc number:', xhr.responseText);
                    }
                });
            }
        });
    </script>
    @endsection

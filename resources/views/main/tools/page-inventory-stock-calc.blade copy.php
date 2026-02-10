@extends('main.layouts.main')
@section('title')
    Inventory Calculation Stock
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
@section('page-title')
    Inventory Calculation Stock
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Inventory Calculation Stock</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tools</a></li>
                            <li class="breadcrumb-item active">Inventory Calculation Stock</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <label for="" class="col-md-2 form-label">Kode Design</label>
                                <div class="col-md-3">
                                    <input type="text" name="kode_design" id="kode_design" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="btnSearch" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>

                        <!-- Table to display results -->
                        <div class="table-responsive mt-4">
                            <table id="inventoryTable" class="table table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th> <!-- Kolom untuk kontrol accordion -->
                                        <th>No</th>
                                        <th>Formula</th>
                                        <th>Material Code</th>
                                        <th>Quantity</th>
                                        <th>Kebutuhan</th>
                                        <th>Unit</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                            <div id="loading-spinner" class="text-center" style="display: none;">
                                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('new/assets/pages/datatables-demo.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var inventoryTableBody = $('#inventoryTable tbody');
                var loadingSpinner = $('#loading-spinner');

                // Search button click handler
                $('#btnSearch').click(function() {
                    var kodeDesign = $('#kode_design').val();

                    // Clear existing table data
                    inventoryTableBody.empty();

                    // Show loading spinner
                    loadingSpinner.show();

                    // Make AJAX request to search endpoint
                    $.ajax({
                        url: '{{ route("inventory.search") }}',
                        type: 'POST',
                        data: {
                            kode_design: kodeDesign
                        },
                        success: function(response) {
                            // Hide loading spinner
                            loadingSpinner.hide();

                            if (response.success) {
                                if (response.data.length > 0) {
                                    var tableHtml = '';
                                    response.data.forEach(function(item, index) {
                                        var collapseId = 'details-' + item.Formula.replace(/[^a-zA-Z0-9]/g, ''); // Clean formula for ID
                                        tableHtml += `
                                            <tr>
                                                <td>
                                                    <button class="btn btn-sm btn-link p-0 collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                                        <i class="fa fa-plus-square"></i>
                                                    </button>
                                                </td>
                                                <td>${index + 1}</td>
                                                <td>${item.Formula}</td>
                                                <td>${item.MaterialCode}</td>
                                                <td class="original-formula-qty">${Number(item.Qty).toLocaleString('en-US')}</td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm kebutuhan-input" data-original-formula-qty="${item.Qty}" data-formula-id="${collapseId}" min="0">
                                                </td>
                                                <td>${item.Unit}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-details" data-id="${item.Formula}">View</button>
                                                </td>
                                            </tr>
                                            <tr class="collapse-row">
                                                <td colspan="8">
                                                    <div class="collapse" id="${collapseId}">
                                                        <div class="card card-body p-0">
                                                            <table class="table table-bordered table-sm mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Material Code</th>
                                                                        <th>Material Name</th>
                                                                        <th>Unit</th>
                                                                        <th>Qty</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                        `;

                                        if (item.details && item.details.length > 0) {
                                            item.details.forEach(function(detail) {
                                                tableHtml += `<tr><td>${detail.MaterialCode}</td><td>${detail.Name}</td><td>${detail.Unit}</td><td class="calculated-detail-qty" data-original-detail-qty="${detail.Qty}">${Number(detail.Qty).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 4 })}</td></tr>`;
                                                // tableHtml += `<tr><td>${detail.MaterialCode}</td><td>${detail.Name}</td><td>${detail.Unit}</td><td class="calculated-detail-qty" data-original-detail-qty="${detail.Qty}">${detail.Qty}</td></tr>`;
                                            });
                                        } else {
                                            tableHtml += `<tr><td colspan="3">No details available.</td></tr>`;
                                        }

                                        tableHtml += `
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        `;
                                    });
                                    inventoryTableBody.html(tableHtml);
                                    // Add a small delay to ensure Bootstrap's JS can process the new DOM elements
                                    // Although data-bs-toggle should work, sometimes a slight re-trigger helps
                                    setTimeout(function() {
                                        // This line is often not strictly needed with data-bs-toggle,
                                        // but it confirms jQuery is finding the elements
                                        console.log('Buttons found after HTML insertion:', $('.collapse-toggle').length);
                                    }, 100);

                                } else {
                                    inventoryTableBody.html('<tr><td colspan="8" class="text-center">No data found.</td></tr>');
                                }
                            } else {
                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to fetch data'
                                });
                            }
                        },
                        error: function(xhr) {
                            // Hide loading spinner
                            loadingSpinner.hide();

                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while fetching data'
                            });
                        }
                    });
                });

                // View details button click handler
                $(document).on('click', '.view-details', function() {
                    var id = $(this).data('id');
                    // Add your view details logic here
                });

                // Optional: Change icon on collapse show/hide (requires jQuery for this method)
                $('#inventoryTable').on('show.bs.collapse', '.collapse', function() {
                    $(this).closest('tr').prev('tr').find('.fa-plus-square').removeClass('fa-plus-square').addClass('fa-minus-square');
                }).on('hide.bs.collapse', '.collapse', function() {
                    $(this).closest('tr').prev('tr').find('.fa-minus-square').removeClass('fa-minus-square').addClass('fa-plus-square');
                });

                // Handle input on the 'Kebutuhan' field
                $(document).on('input', '.kebutuhan-input', function() {
                    var $this = $(this);
                    var newKebutuhan = parseFloat($this.val());
                    var originalFormulaQty = parseFloat($this.data('original-formula-qty'));
                    var formulaId = $this.data('formula-id');

                    if (isNaN(newKebutuhan) || newKebutuhan < 0) {
                        newKebutuhan = 0;
                    }

                    if (originalFormulaQty === 0) {
                        $('#' + formulaId).find('.calculated-detail-qty').text('0.0000'); // Or handle as appropriate
                        return;
                    }

                    var ratio = newKebutuhan / originalFormulaQty;

                    $('#' + formulaId).find('tbody tr').each(function() {
                        var $detailQtyCell = $(this).find('.calculated-detail-qty');
                        var originalDetailQty = parseFloat($detailQtyCell.data('original-detail-qty'));
                        if (!isNaN(originalDetailQty)) {
                            var newDetailQty = originalDetailQty * ratio;
                            $detailQtyCell.text(newDetailQty.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 4 }));
                            // $detailQtyCell.text(newDetailQty.toFixed(4)); // Format to 4 decimal places
                        }
                    });
                });
            });
        </script>
    @endsection

@extends('main.layouts.app')

@section('title', 'Create Document - ' . $supplierTicket->ticket_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.supplier-tickets.index') }}">Admin Supplier Tickets</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.supplier-tickets.show', $supplierTicket->id) }}">Detail Ticket</a></li>
                        <li class="breadcrumb-item active">Create Document</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Document - {{ $supplierTicket->ticket_number }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Ticket Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ticket Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Ticket #:</strong></td>
                            <td>{{ $supplierTicket->ticket_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Supplier:</strong></td>
                            <td>{{ $supplierTicket->supplier_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>PO Number:</strong></td>
                            <td><code>{{ $supplierTicket->po_number }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Delivery Doc:</strong></td>
                            <td>{{ $supplierTicket->supplier_delivery_doc }}</td>
                        </tr>
                        <tr>
                            <td><strong>Delivery Date:</strong></td>
                            <td>{{ $supplierTicket->delivery_date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Document Status -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Document Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>GRD Document:</span>
                        @if($hasGRD)
                            <span class="badge bg-success">Created</span>
                        @else
                            <span class="badge bg-warning">Not Created</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>PQC Document:</span>
                        @if($hasPQC)
                            <span class="badge bg-success">Created</span>
                        @else
                            <span class="badge bg-warning">Not Created</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Document Creation Forms -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create Documents</h5>
                </div>
                <div class="card-body">
                    <!-- GRD Form -->
                    <div class="mb-4">
                        <h6 class="text-primary">
                            <i class="mdi mdi-file-document"></i> GRD (Good Receipt Document)
                        </h6>
                        <p class="text-muted small">
                            GRD digunakan untuk material dengan kode berawalan BP atau untuk PON (Purchase Order Non-Material).
                        </p>
                        
                        @if(!$hasGRD)
                        <form method="POST" action="{{ route('admin.supplier-tickets.create-grd', $supplierTicket->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="grd_number" class="form-label">GRD Number <span class="text-danger">*</span></label>
                                        <input type="text" name="grd_number" id="grd_number" class="form-control" 
                                               value="GRD-{{ date('Ymd') }}-{{ str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="grd_date" class="form-label">GRD Date <span class="text-danger">*</span></label>
                                        <input type="date" name="grd_date" id="grd_date" class="form-control" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Material Codes <span class="text-danger">*</span></label>
                                <div id="grd-materials">
                                    <div class="input-group mb-2">
                                        <input type="text" name="material_codes[]" class="form-control" placeholder="Material Code" required>
                                        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" step="0.01" min="0.01" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeMaterialRow(this)">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow('grd-materials')">
                                    <i class="mdi mdi-plus"></i> Add Material
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label for="grd_notes" class="form-label">Notes</label>
                                <textarea name="grd_notes" id="grd_notes" class="form-control" rows="3" 
                                          placeholder="Additional notes for GRD..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-file-document"></i> Create GRD
                            </button>
                        </form>
                        @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            GRD document sudah dibuat untuk ticket ini.
                        </div>
                        @endif
                    </div>

                    <hr>

                    <!-- PQC Form -->
                    <div class="mb-4">
                        <h6 class="text-success">
                            <i class="mdi mdi-file-document"></i> PQC (Purchase Quality Control)
                        </h6>
                        <p class="text-muted small">
                            PQC digunakan untuk kiriman atas POM (Purchase Order Material).
                        </p>
                        
                        @if(!$hasPQC)
                        <form method="POST" action="{{ route('admin.supplier-tickets.create-pqc', $supplierTicket->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pqc_number" class="form-label">PQC Number <span class="text-danger">*</span></label>
                                        <input type="text" name="pqc_number" id="pqc_number" class="form-control" 
                                               value="PQC-{{ date('Ymd') }}-{{ str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pqc_date" class="form-label">PQC Date <span class="text-danger">*</span></label>
                                        <input type="date" name="pqc_date" id="pqc_date" class="form-control" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Material Codes <span class="text-danger">*</span></label>
                                <div id="pqc-materials">
                                    <div class="input-group mb-2">
                                        <input type="text" name="material_codes[]" class="form-control" placeholder="Material Code" required>
                                        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" step="0.01" min="0.01" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeMaterialRow(this)">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow('pqc-materials')">
                                    <i class="mdi mdi-plus"></i> Add Material
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pqc_notes" class="form-label">Notes</label>
                                <textarea name="pqc_notes" id="pqc_notes" class="form-control" rows="3" 
                                          placeholder="Additional notes for PQC..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-file-document"></i> Create PQC
                            </button>
                        </form>
                        @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            PQC document sudah dibuat untuk ticket ini.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function addMaterialRow(containerId) {
    const container = document.getElementById(containerId);
    const newRow = document.createElement('div');
    newRow.className = 'input-group mb-2';
    newRow.innerHTML = `
        <input type="text" name="material_codes[]" class="form-control" placeholder="Material Code" required>
        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" step="0.01" min="0.01" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeMaterialRow(this)">
            <i class="mdi mdi-minus"></i>
        </button>
    `;
    container.appendChild(newRow);
}

function removeMaterialRow(button) {
    const container = button.closest('.input-group').parentElement;
    const rows = container.querySelectorAll('.input-group');
    if (rows.length > 1) {
        button.closest('.input-group').remove();
    }
}

$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endsection

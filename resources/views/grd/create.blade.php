@extends('main.layouts.main')

@section('title', 'Create GRD')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create GRD (Good Receipt Document)</h3>
                    <div class="card-tools">
                        <a href="{{ route('grd.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('grd.store') }}">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Supplier Ticket Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Supplier Ticket Information</h5>
                                
                                @if($supplierTicket)
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Ticket Number:</strong> {{ $supplierTicket->ticket_number }}<br>
                                                <strong>PO Number:</strong> {{ $supplierTicket->po_number }}<br>
                                                <strong>Supplier:</strong> {{ $supplierTicket->supplier_name }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Delivery Date:</strong> {{ $supplierTicket->delivery_date->format('d/m/Y H:i') }}<br>
                                                <strong>Supplier Delivery Doc:</strong> {{ $supplierTicket->supplier_delivery_doc }}
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="supplier_ticket_id" value="{{ $supplierTicket->id }}">
                                @else
                                    <div class="mb-3">
                                        <label for="supplier_ticket_id" class="form-label">Supplier Ticket <span class="text-danger">*</span></label>
                                        <select name="supplier_ticket_id" id="supplier_ticket_id" 
                                                class="form-select @error('supplier_ticket_id') is-invalid @enderror" required>
                                            <option value="">Pilih Supplier Ticket</option>
                                            @foreach(\App\Models\SupplierTicket::whereIn('status', ['approved', 'processed'])->get() as $ticket)
                                                <option value="{{ $ticket->id }}" {{ old('supplier_ticket_id') == $ticket->id ? 'selected' : '' }}>
                                                    {{ $ticket->ticket_number }} - {{ $ticket->po_number }} - {{ $ticket->supplier_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <!-- GRD Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">GRD Information</h5>
                                
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location') }}" maxlength="5" required>
                                    <div class="form-text">Kode lokasi gudang (max 5 karakter)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="zone" class="form-label">Zone <span class="text-danger">*</span></label>
                                    <input type="text" name="zone" id="zone" 
                                           class="form-control @error('zone') is-invalid @enderror" 
                                           value="{{ old('zone') }}" maxlength="10" required>
                                    <div class="form-text">Kode zona gudang (max 10 karakter)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="vehicle_no" class="form-label">Vehicle Number</label>
                                    <input type="text" name="vehicle_no" id="vehicle_no" 
                                           class="form-control @error('vehicle_no') is-invalid @enderror" 
                                           value="{{ old('vehicle_no') }}" maxlength="10">
                                </div>

                                <div class="mb-3">
                                    <label for="information" class="form-label">Information</label>
                                    <textarea name="information" id="information" rows="3" 
                                              class="form-control @error('information') is-invalid @enderror" 
                                              maxlength="255">{{ old('information') }}</textarea>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Additional Information</h5>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Received Items -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Received Items</h5>
                                
                                <div id="items-container">
                                    <div class="item-row row mb-3">
                                        <div class="col-md-2">
                                            <label class="form-label">Material Code <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][material_code]" 
                                                   class="form-control @error('received_items.0.material_code') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.material_code') }}" maxlength="20" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Batch No <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][batch_no]" 
                                                   class="form-control @error('received_items.0.batch_no') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.batch_no') }}" maxlength="20" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Batch Info <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][batch_info]" 
                                                   class="form-control @error('received_items.0.batch_info') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.batch_info') }}" maxlength="50" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                            <input type="date" name="received_items[0][expiry_date]" 
                                                   class="form-control @error('received_items.0.expiry_date') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.expiry_date') }}" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">Tag No <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][tag_no]" 
                                                   class="form-control @error('received_items.0.tag_no') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.tag_no') }}" maxlength="10" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][unit]" 
                                                   class="form-control @error('received_items.0.unit') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.unit') }}" maxlength="5" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="received_items[0][qty]" 
                                                   class="form-control @error('received_items.0.qty') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.qty', 0) }}" step="0.0001" min="0" required>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-item" class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('grd.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create GRD
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    
    // Add item
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = document.querySelector('.item-row').cloneNode(true);
        
        // Update input names and clear values
        newItem.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', '[' + itemIndex + ']'));
                input.value = '';
            }
        });
        
        // Show remove button
        newItem.querySelector('.remove-item').style.display = 'block';
        
        container.appendChild(newItem);
        itemIndex++;
    });
    
    // Remove item
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const itemRow = e.target.closest('.item-row');
            const container = document.getElementById('items-container');
            
            if (container.children.length > 1) {
                itemRow.remove();
            }
        }
    });
});
</script>
@endsection

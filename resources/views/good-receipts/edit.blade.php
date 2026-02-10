@extends('layouts.app')

@section('title', 'Edit Good Receipt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Good Receipt: {{ $goodReceipt->receipt_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('good-receipts.show', $goodReceipt) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('good-receipts.update', $goodReceipt) }}">
                    @csrf
                    @method('PUT')
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

                        <!-- Supplier Ticket Information (Read Only) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Supplier Ticket Information</h5>
                                
                                <div class="alert alert-info">
                                    <strong>Ticket Number:</strong> 
                                    <a href="{{ route('supplier-tickets.show', $goodReceipt->supplier_ticket_id) }}" 
                                       class="text-decoration-none">
                                        {{ $goodReceipt->supplierTicket->ticket_number }}
                                    </a>
                                    <br>
                                    <strong>Supplier:</strong> {{ $goodReceipt->supplierTicket->supplier_name }}
                                    <br>
                                    <strong>Delivery Date:</strong> {{ $goodReceipt->supplierTicket->delivery_date->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Receipt Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Informasi Receipt</h5>
                                
                                <div class="mb-3">
                                    <label for="received_date" class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="received_date" id="received_date" 
                                           class="form-control @error('received_date') is-invalid @enderror" 
                                           value="{{ old('received_date', $goodReceipt->received_date->format('Y-m-d\TH:i')) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $goodReceipt->notes) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="condition_notes" class="form-label">Catatan Kondisi Barang</label>
                                    <textarea name="condition_notes" id="condition_notes" rows="3" 
                                              class="form-control @error('condition_notes') is-invalid @enderror">{{ old('condition_notes', $goodReceipt->condition_notes) }}</textarea>
                                </div>
                            </div>

                            <!-- Quality Check -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Quality Check</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="quality_check" id="quality_check" 
                                               value="1" {{ old('quality_check', $goodReceipt->quality_check) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quality_check">
                                            Quality Check Passed
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="quantity_match" id="quantity_match" 
                                               value="1" {{ old('quantity_match', $goodReceipt->quantity_match) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quantity_match">
                                            Quantity Match dengan Expected Items
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Received Items -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Item yang Diterima</h5>
                                
                                <div id="items-container">
                                    @foreach($goodReceipt->received_items as $index => $item)
                                    <div class="item-row row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[{{ $index }}][item_name]" 
                                                   class="form-control @error('received_items.' . $index . '.item_name') is-invalid @enderror" 
                                                   value="{{ old('received_items.' . $index . '.item_name', $item['item_name']) }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="received_items[{{ $index }}][quantity]" 
                                                   class="form-control @error('received_items.' . $index . '.quantity') is-invalid @enderror" 
                                                   value="{{ old('received_items.' . $index . '.quantity', $item['quantity']) }}" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                                            <select name="received_items[{{ $index }}][unit]" 
                                                    class="form-select @error('received_items.' . $index . '.unit') is-invalid @enderror" required>
                                                <option value="">Pilih Unit</option>
                                                <option value="pcs" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                                <option value="kg" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'kg' ? 'selected' : '' }}>Kg</option>
                                                <option value="gram" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'gram' ? 'selected' : '' }}>Gram</option>
                                                <option value="liter" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'liter' ? 'selected' : '' }}>Liter</option>
                                                <option value="meter" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'meter' ? 'selected' : '' }}>Meter</option>
                                                <option value="box" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'box' ? 'selected' : '' }}>Box</option>
                                                <option value="pack" {{ old('received_items.' . $index . '.unit', $item['unit']) == 'pack' ? 'selected' : '' }}>Pack</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Condition <span class="text-danger">*</span></label>
                                            <select name="received_items[{{ $index }}][condition]" 
                                                    class="form-select @error('received_items.' . $index . '.condition') is-invalid @enderror" required>
                                                <option value="">Pilih Condition</option>
                                                <option value="good" {{ old('received_items.' . $index . '.condition', $item['condition']) == 'good' ? 'selected' : '' }}>Good</option>
                                                <option value="damaged" {{ old('received_items.' . $index . '.condition', $item['condition']) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                                <option value="defective" {{ old('received_items.' . $index . '.condition', $item['condition']) == 'defective' ? 'selected' : '' }}>Defective</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Notes</label>
                                            <input type="text" name="received_items[{{ $index }}][notes]" 
                                                   class="form-control" 
                                                   value="{{ old('received_items.' . $index . '.notes', $item['notes'] ?? '') }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" 
                                                    {{ count($goodReceipt->received_items) <= 1 ? 'style=display:none;' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" id="add-item" class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('good-receipts.show', $goodReceipt) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Good Receipt
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
    let itemIndex = {{ count($goodReceipt->received_items) }};
    
    // Add item
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = document.querySelector('.item-row').cloneNode(true);
        
        // Update input names and clear values
        newItem.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
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

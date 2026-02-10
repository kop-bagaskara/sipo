@extends('layouts.app')

@section('title', 'Buat Good Receipt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Buat Good Receipt Baru</h3>
                    <div class="card-tools">
                        <a href="{{ route('good-receipts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('good-receipts.store') }}">
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

                        <!-- Supplier Ticket Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Pilih Supplier Ticket</h5>
                                
                                @if($supplierTicket)
                                    <div class="alert alert-info">
                                        <strong>Selected Ticket:</strong> {{ $supplierTicket->ticket_number }} - {{ $supplierTicket->supplier_name }}
                                        <br><small>Delivery Date: {{ $supplierTicket->delivery_date->format('d/m/Y H:i') }}</small>
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
                                                    {{ $ticket->ticket_number }} - {{ $ticket->supplier_name }} ({{ $ticket->delivery_date->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
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
                                           value="{{ old('received_date', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="condition_notes" class="form-label">Catatan Kondisi Barang</label>
                                    <textarea name="condition_notes" id="condition_notes" rows="3" 
                                              class="form-control @error('condition_notes') is-invalid @enderror">{{ old('condition_notes') }}</textarea>
                                </div>
                            </div>

                            <!-- Quality Check -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Quality Check</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="quality_check" id="quality_check" 
                                               value="1" {{ old('quality_check') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quality_check">
                                            Quality Check Passed
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="quantity_match" id="quantity_match" 
                                               value="1" {{ old('quantity_match') ? 'checked' : '' }}>
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
                                
                                @if($supplierTicket)
                                    <div class="alert alert-info mb-3">
                                        <strong>Expected Items:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($supplierTicket->expected_items as $item)
                                                <li>{{ $item['item_name'] }} - {{ $item['quantity'] }} {{ $item['unit'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div id="items-container">
                                    <div class="item-row row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                                            <input type="text" name="received_items[0][item_name]" 
                                                   class="form-control @error('received_items.0.item_name') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.item_name') }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="received_items[0][quantity]" 
                                                   class="form-control @error('received_items.0.quantity') is-invalid @enderror" 
                                                   value="{{ old('received_items.0.quantity', 0) }}" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                                            <select name="received_items[0][unit]" 
                                                    class="form-select @error('received_items.0.unit') is-invalid @enderror" required>
                                                <option value="">Pilih Unit</option>
                                                <option value="pcs" {{ old('received_items.0.unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                                <option value="kg" {{ old('received_items.0.unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                                                <option value="gram" {{ old('received_items.0.unit') == 'gram' ? 'selected' : '' }}>Gram</option>
                                                <option value="liter" {{ old('received_items.0.unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                                <option value="meter" {{ old('received_items.0.unit') == 'meter' ? 'selected' : '' }}>Meter</option>
                                                <option value="box" {{ old('received_items.0.unit') == 'box' ? 'selected' : '' }}>Box</option>
                                                <option value="pack" {{ old('received_items.0.unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Condition <span class="text-danger">*</span></label>
                                            <select name="received_items[0][condition]" 
                                                    class="form-select @error('received_items.0.condition') is-invalid @enderror" required>
                                                <option value="">Pilih Condition</option>
                                                <option value="good" {{ old('received_items.0.condition') == 'good' ? 'selected' : '' }}>Good</option>
                                                <option value="damaged" {{ old('received_items.0.condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                                <option value="defective" {{ old('received_items.0.condition') == 'defective' ? 'selected' : '' }}>Defective</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Notes</label>
                                            <input type="text" name="received_items[0][notes]" 
                                                   class="form-control" 
                                                   value="{{ old('received_items.0.notes') }}">
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
                            <a href="{{ route('good-receipts.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Good Receipt
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
        newItem.querySelectorAll('input, select').forEach(input => {
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

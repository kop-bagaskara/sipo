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
        
        /* Ensure modal is visible */
        .modal {
            display: none;
        }
        .modal.show {
            display: block !important;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
            opacity: 0.5;
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
                <h3 class="text-themecolor">Detail Ticketing Supplier</h3>
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
                    <div class="card-header">
                        <h3 class="card-title">Edit Supplier Ticket: {{ $supplierTicket->ticket_number }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('supplier-tickets.show', $supplierTicket) }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if($supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED)
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle"></i>
                                <strong>Perhatian:</strong> Ticket ini tidak dapat diedit karena statusnya sudah <span class="badge {{ $supplierTicket->getStatusBadgeClass() }}">{{ $supplierTicket->status }}</span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('supplier-tickets.update', $supplierTicket) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <h5 class="text-primary mb-3">Detail Purchase Order Supplier : {{ Auth::user()->name }}</h5>

                            <div class="row">
                                <!-- PO Information -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Informasi Surat Jalan Supplier</h5>

                                    <div class="mb-3">
                                        <label for="po_number" class="form-label">Nomor PO <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" name="po_number" id="po_number"
                                                class="form-control @error('po_number') is-invalid @enderror"
                                                value="{{ old('po_number', $supplierTicket->po_number) }}" 
                                                {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }} required>
                                            <button type="button" class="btn btn-outline-secondary" id="btn-change-po"
                                                {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'disabled' : '' }}>
                                                <i class="mdi mdi-pencil"></i> Ganti PO
                                            </button>
                                        </div>
                                        <div class="form-text">Klik "Ganti PO" untuk memilih dari daftar PO yang tersedia</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_delivery_doc" class="form-label">Surat Jalan Supplier <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="supplier_delivery_doc" id="supplier_delivery_doc"
                                            class="form-control @error('supplier_delivery_doc') is-invalid @enderror"
                                            value="{{ old('supplier_delivery_doc', $supplierTicket->supplier_delivery_doc) }}"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }} required>
                                        <div class="form-text">Nomor surat jalan dari supplier</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_date" class="form-label">Tanggal Pengiriman <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" name="delivery_date" id="delivery_date"
                                            class="form-control @error('delivery_date') is-invalid @enderror"
                                            value="{{ old('delivery_date', $supplierTicket->delivery_date->format('Y-m-d\TH:i')) }}"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }} required>
                                    </div>

                                    <!-- PO Selection Section -->
                                    <div id="changePOModal" style="display: none;" class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">Pilih PO Baru</h6>
                                            <button type="button" class="btn btn-sm btn-secondary" id="btn-close-po">✕ Tutup</button>
                                        </div>
                                        <div class="card-body">
                                            <!-- PO Search Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <label class="form-label">Cari PO Manual</label>
                                                    <div class="input-group">
                                                        <input type="text" id="po_search_edit" class="form-control" placeholder="Masukkan nomor PO...">
                                                        <button type="button" id="btn_search_po_edit" class="btn btn-outline-primary">
                                                            <i class="mdi mdi-magnify"></i> Cari PO
                                                        </button>
                                                        <span id="po_status_icon_edit" class="ms-2"></span>
                                                    </div>
                                                    <div id="po_status_edit" class="form-text"></div>
                                                </div>
                                            </div>

                                            <!-- PO List Section -->
                                            <div class="mb-3">
                                                <button type="button" id="btn-load-pos-edit" class="btn btn-outline-secondary btn-sm">
                                                    <i class="mdi mdi-format-list-bulleted"></i> Tampilkan daftar PO Supplier
                                                </button>
                                            </div>
                                            <div id="po-list-edit" style="display:none;">
                                                <label class="form-label">Pilih PO Supplier (hanya 1 PO):</label>
                                                <div id="po-checkboxes-edit" class="border rounded p-2" style="max-height: 300px; overflow:auto"></div>
                                            </div>

                                            <!-- PO Items Preview -->
                                            <div class="row mt-3" id="po-items-preview-edit" style="display: none;">
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        <div id="po-items-list-edit">
                                                            <!-- PO items will be loaded here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="button" class="btn btn-primary" id="btn-apply-po-edit" disabled>Gunakan PO Terpilih</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Supplier Information -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Informasi Supplier</h5>

                                    <div class="mb-3">
                                        <label for="supplier_name" class="form-label">Nama Supplier <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="supplier_name" id="supplier_name"
                                            class="form-control @error('supplier_name') is-invalid @enderror"
                                            value="{{ old('supplier_name', $supplierTicket->supplier_name) }}" 
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }} required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_contact" class="form-label">Kontak Supplier</label>
                                        <input type="text" name="supplier_contact" id="supplier_contact"
                                            class="form-control @error('supplier_contact') is-invalid @enderror"
                                            value="{{ old('supplier_contact', $supplierTicket->supplier_contact) }}"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }}>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_email" class="form-label">Email Supplier</label>
                                        <input type="email" name="supplier_email" id="supplier_email"
                                            class="form-control @error('supplier_email') is-invalid @enderror"
                                            value="{{ old('supplier_email', $supplierTicket->supplier_email) }}"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }}>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_address" class="form-label">Alamat Supplier</label>
                                        <textarea name="supplier_address" id="supplier_address" rows="3"
                                            class="form-control @error('supplier_address') is-invalid @enderror"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }}>{{ old('supplier_address', $supplierTicket->supplier_address) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Informasi Tambahan</h5>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi Pengiriman</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="form-control @error('description') is-invalid @enderror"
                                                {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }}>{{ old('description', $supplierTicket->description) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Catatan</label>
                                        <textarea name="notes" id="notes" rows="3" 
                                            class="form-control @error('notes') is-invalid @enderror"
                                            {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'readonly' : '' }}>{{ old('notes', $supplierTicket->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('supplier-tickets.show', $supplierTicket) }}"
                                    class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary"
                                    {{ $supplierTicket->status !== \App\Models\SupplierTicket::STATUS_PROCESSED ? 'disabled' : '' }}>
                                    <i class="mdi mdi-content-save"></i> Update Ticket
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
                console.log('Edit page loaded');
                
                const btnLoadPOs = document.getElementById('btn-load-pos-edit');
                const poListContainer = document.getElementById('po-list-edit');
                const poCheckboxesContainer = document.getElementById('po-checkboxes-edit');
                const btnApplyPO = document.getElementById('btn-apply-po-edit');
                const poNumberInput = document.getElementById('po_number');
                
                // Helper function to show/hide PO section
                function showPOSelector() {
                    const poSection = document.getElementById('changePOModal');
                    if (poSection) {
                        poSection.style.display = 'block';
                    }
                }
                
                function hidePOSelector() {
                    const poSection = document.getElementById('changePOModal');
                    if (poSection) {
                        poSection.style.display = 'none';
                    }
                }
                
                // Change PO button
                const changePOButton = document.getElementById('btn-change-po');
                if (changePOButton) {
                    changePOButton.addEventListener('click', function() {
                        showPOSelector();
                    });
                }
                
                // Close PO selector button
                const closePOButton = document.getElementById('btn-close-po');
                if (closePOButton) {
                    closePOButton.addEventListener('click', function() {
                        hidePOSelector();
                    });
                }
                
                // PO Search elements
                const poSearchInput = document.getElementById('po_search_edit');
                const btnSearchPO = document.getElementById('btn_search_po_edit');
                const poStatusIcon = document.getElementById('po_status_icon_edit');
                const poStatus = document.getElementById('po_status_edit');
                const poItemsPreview = document.getElementById('po-items-preview-edit');
                const poItemsList = document.getElementById('po-items-list-edit');

                // PO Search functionality
                if (btnSearchPO) {
                    btnSearchPO.addEventListener('click', function() {
                        const poNumber = poSearchInput.value.trim();
                        resetPOStatus();
                        
                        if (poNumber.length < 3) {
                            setPOStatus('Masukkan minimal 3 karakter nomor PO', 'text-danger');
                            poSearchInput.classList.remove('is-valid');
                            poSearchInput.classList.add('is-invalid');
                            hidePOItems();
                            return;
                        }

                        // Show loading
                        setPOIcon('spinner');
                        poItemsList.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari data PO...';
                        poItemsPreview.style.display = 'block';

                        // Fetch PO details
                        fetch(`{{ route('supplier-tickets.search-po') }}?q=${encodeURIComponent(poNumber)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    return fetch(`{{ route('supplier-tickets.get-po-details') }}?po_number=${encodeURIComponent(poNumber)}`);
                                } else {
                                    throw new Error('PO tidak ditemukan');
                                }
                            })
                            .then(response => response.json())
                            .then(poDetails => {
                                if (poDetails.error) {
                                    throw new Error(poDetails.error);
                                }

                                setPOIcon('ok');
                                poSearchInput.classList.remove('is-invalid');
                                poSearchInput.classList.add('is-valid');
                                displayPOItems(poDetails);
                                
                                // Auto-fill the PO number input
                                poNumberInput.value = poNumber;
                                btnApplyPO.disabled = false;
                            })
                            .catch(error => {
                                setPOIcon('fail');
                                poSearchInput.classList.remove('is-valid');
                                poSearchInput.classList.add('is-invalid');
                                poItemsList.innerHTML = `<div class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${error.message}</div>`;
                            });
                    });
                }

                // Load PO list functionality
                if (btnLoadPOs) {
                    btnLoadPOs.addEventListener('click', function() {
                        poCheckboxesContainer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat daftar PO...';
                        poListContainer.style.display = 'block';

                        fetch('{{ route("supplier-tickets.my-pos") }}')
                            .then(r => r.json())
                            .then(res => {
                                const items = res.pos || [];
                                if (!items.length) {
                                    poCheckboxesContainer.innerHTML = '<div class="text-muted">Tidak ada PO untuk akun ini.</div>';
                                    return;
                                }
                                
                                let html = '<table class="table table-bordered table-sm mb-0">\n<thead>\n<tr>\n<th style="width:40px;">Pilih</th>\n<th>Nomor PO</th>\n<th>Nama Item</th>\n<th style="width:120px;" class="text-end">Qty</th>\n<th style="width:90px;">Unit</th>\n<th style="width:160px;">Tanggal</th>\n</tr>\n</thead><tbody>';
                                items.forEach((po, idx) => {
                                    const doc = po.DocNo || po.docno || po.docNo;
                                    const date = po.DocDate ? new Date(po.DocDate).toLocaleDateString() : '';
                                    html += `<tr>\n<td class=\"text-center\"><input type=\"radio\" name=\"select-po-edit\" class=\"form-check-input\" value=\"${doc}\" id=\"po_edit_${idx}\" data-doc=\"${doc}\"><label for=\"po_edit_${idx}\" class=\"m-0\">&nbsp;</label></td>\n<td><code>${doc}</code></td>\n<td>${po.materialName || ''}</td>\n<td class=\"text-end\">${(po.Qty ? parseFloat(po.Qty).toLocaleString() : '0')}</td>\n<td>${po.unit || ''}</td>\n<td>${date}</td>\n</tr>`;
                                });
                                html += '</tbody></table>';
                                poCheckboxesContainer.innerHTML = html;

                                // Add event listeners for radio buttons
                                poCheckboxesContainer.querySelectorAll('input[name="select-po-edit"]').forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        poNumberInput.value = this.getAttribute('data-doc');
                                        btnApplyPO.disabled = false;
                                    });
                                });
                            })
                            .catch(() => {
                                poCheckboxesContainer.innerHTML = '<div class="text-danger">Gagal memuat daftar PO.</div>';
                            });
                    });
                }

                if (btnApplyPO) {
                    btnApplyPO.addEventListener('click', function() {
                        // Close PO selector
                        hidePOSelector();
                    });
                }

                // Helper functions
                function displayPOItems(poDetails) {
                    let html = '<h6>Item dalam PO:</h6><ul class="mb-0">';
                    if (poDetails.details && poDetails.details.length > 0) {
                        poDetails.details.forEach((item, index) => {
                            html += `<li><strong>${item.materialName || 'N/A'}</strong> - ${parseInt(item.Qty || 0).toLocaleString()} pcs</li>`;
                        });
                    } else {
                        html += '<li>Tidak ada item ditemukan</li>';
                    }
                    html += '</ul>';
                    poItemsList.innerHTML = html;
                }

                function hidePOItems() {
                    poItemsPreview.style.display = 'none';
                    poItemsList.innerHTML = '';
                }

                function resetPOStatus() {
                    setPOIcon('none');
                }

                function setPOIcon(state) {
                    if (!poStatusIcon) return;
                    poStatusIcon.className = 'ms-2';
                    poStatusIcon.innerHTML = '';
                    if (state === 'spinner') {
                        poStatusIcon.innerHTML = '<i class="fas fa-spinner fa-spin text-muted"></i>';
                    } else if (state === 'ok') {
                        poStatusIcon.innerHTML = '<i class="mdi mdi-check-bold text-success" style="font-size: 20px;"></i>';
                    } else if (state === 'fail') {
                        poStatusIcon.innerHTML = '<i class="mdi mdi-close-thick text-danger" style="font-size: 20px;"></i>';
                    }
                }

                function setPOStatus(message, className) {
                    if (poStatus) {
                        poStatus.innerHTML = message;
                        poStatus.className = `form-text ${className}`;
                    }
                }
            });
        </script>
    @endsection

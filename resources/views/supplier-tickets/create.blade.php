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
            /* Outline states for PO input */
            #po_number.is-valid {
                border-color: #28a745 !important;
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, .25);
            }
            #po_number.is-invalid {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
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
                    <div class="card-header">
                        <h3 class="card-title">Buat Supplier Ticket</h3>
                    </div>

                    <form method="POST" action="{{ route('supplier-tickets.store') }}">
                        @csrf
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
                            <div class="mb-3">
                                <button type="button" id="btn-load-my-pos" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-format-list-bulleted"></i> Tampilkan daftar PO Supplier
                                </button>
                            </div>
                            <div id="my-po-list" class="mb-3" style="display:none;">
                                <label class="form-label">Pilih PO Supplier (hanya 1 PO):</label>
                                <div id="my-po-checkboxes" class="border rounded p-2" style="max-height: 220px; overflow:auto"></div>
                            </div>
                            <div class="row mt-4" id="po-items-preview" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div id="po-items-list">
                                            <!-- PO items will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <!-- PO Information -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Informasi Surat Jalan Supplier</h5>
                                    <div class="mb-3">
                                        <label for="supplier_delivery_doc" class="form-label">Surat Jalan Supplier <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="supplier_delivery_doc" id="supplier_delivery_doc"
                                            class="form-control @error('supplier_delivery_doc') is-invalid @enderror"
                                            value="{{ old('supplier_delivery_doc') }}" required>
                                        <div class="form-text">Nomor surat jalan dari supplier</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_date" class="form-label">Tanggal Pengiriman <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" name="delivery_date" id="delivery_date"
                                            class="form-control @error('delivery_date') is-invalid @enderror"
                                            value="{{ old('delivery_date', now()->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>

                                <!-- Supplier Information (Auto-filled from PO) -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Informasi Supplier</h5>

                                    <div class="mb-3">
                                        <label for="supplier_name" class="form-label">Nama Supplier <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="supplier_name" id="supplier_name"
                                            class="form-control @error('supplier_name') is-invalid @enderror"
                                            value="{{ old('supplier_name') }}" required>
                                        <div class="form-text">Akan terisi otomatis dari data PO</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_contact" class="form-label">Kontak Supplier</label>
                                        <input type="text" name="supplier_contact" id="supplier_contact"
                                            class="form-control @error('supplier_contact') is-invalid @enderror"
                                            value="{{ old('supplier_contact') }}">
                                        <div class="form-text">Kontak supplier</div>

                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_email" class="form-label">Email Supplier</label>
                                        <input type="email" name="supplier_email" id="supplier_email"
                                            class="form-control @error('supplier_email') is-invalid @enderror"
                                            value="{{ old('supplier_email') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_address" class="form-label">Alamat Supplier</label>
                                        <textarea name="supplier_address" id="supplier_address" rows="3"
                                            class="form-control @error('supplier_address') is-invalid @enderror">{{ old('supplier_address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Informasi Tambahan</h5>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi Pengiriman</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Catatan</label>
                                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- PO Items Preview -->
                            <hr>
                           
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('supplier-tickets.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-content-save"></i> Simpan Ticket
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const poNumberInput = document.getElementById('po_number');
                const poItemsPreview = document.getElementById('po-items-preview');
                const poItemsList = document.getElementById('po-items-list');
                const btnSearchPO = document.getElementById('btn-search-po');
                const poStatus = document.getElementById('po_status');
                const poStatusIcon = document.getElementById('po_status_icon');

                // Auto-fill supplier info on page load
                loadSupplierInfo();

                // Search when button clicked (no keyup/change listener)
                if (btnSearchPO) btnSearchPO.addEventListener('click', function() {
                    const poNumber = poNumberInput.value.trim();

                    resetPOStatus();

                    if (poNumber.length < 3) {
                        setPOStatus('Masukkan minimal 3 karakter nomor PO', 'text-danger');
                        poNumberInput.classList.remove('is-valid');
                        poNumberInput.classList.add('is-invalid');
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
                                // Get full PO details
                                return fetch(
                                    `{{ route('supplier-tickets.get-po-details') }}?po_number=${encodeURIComponent(poNumber)}`
                                    );
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
                            poNumberInput.classList.remove('is-invalid');
                            poNumberInput.classList.add('is-valid');
                            displayPOItems(poDetails);
                            populateSupplierInfo(poDetails);
                        })
                        .catch(error => {
                            setPOIcon('fail');
                            poNumberInput.classList.remove('is-valid');
                            poNumberInput.classList.add('is-invalid');
                            poItemsList.innerHTML =
                                `<div class="text-danger"><i class=\"fas fa-exclamation-triangle\"></i> ${error.message}</div>`;
                        });
                });

                // Load PO list for current user's supplier
                const btnLoadMyPOs = document.getElementById('btn-load-my-pos');
                if (btnLoadMyPOs) btnLoadMyPOs.addEventListener('click', function(){
                    const container = document.getElementById('my-po-list');
                    const holder = document.getElementById('my-po-checkboxes');
                    holder.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat daftar PO...';
                    container.style.display = 'block';

                    fetch(`{{ route('supplier-tickets.my-pos') }}`)
                        .then(r => r.json())
                        .then(res => {
                            const items = res.pos || [];
                            if (!items.length) {
                                holder.innerHTML = '<div class="text-muted">Tidak ada PO untuk akun ini.</div>';
                                return;
                            }
                            let html = '<table class="table table-bordered table-sm mb-0">\n<thead>\n<tr>\n<th style="width:40px;">Pilih</th>\n<th>Nomor PO</th>\n<th>Nama Item</th>\n<th style="width:120px;" class="text-end">Qty</th>\n<th style="width:90px;">Unit</th>\n<th style="width:160px;">Tanggal</th>\n</tr>\n</thead><tbody>';
                            items.forEach((po, idx) => {
                                const doc = po.DocNo || po.docno || po.docNo;
                                const date = po.DocDate ? new Date(po.DocDate).toLocaleDateString() : '';
                                // label is tied to checkbox id per requirement
                                html += `<tr>\n<td class=\"text-center\"><input type=\"radio\" name=\"po_number\" class=\"form-check-input\" value=\"${doc}\" id=\"po_${idx}\" data-doc=\"${doc}\"><label for=\"po_${idx}\" class=\"m-0\">&nbsp;</label></td>\n<td><code>${doc}</code></td>\n<td>${po.materialName || ''}</td>\n<td class=\"text-end\">${(po.Qty ? parseFloat(po.Qty).toLocaleString() : '0')}</td>\n<td>${po.unit || ''}</td>\n<td>${date}</td>\n</tr>`;
                            });
                            html += '</tbody></table>\n<div class="mt-2"><button type="button" id="btn-apply-pos" class="btn btn-sm btn-info"><i class="mdi mdi-check"></i> Gunakan</button></div>';
                            holder.innerHTML = html;

                            document.getElementById('btn-apply-pos').addEventListener('click', function(){
                                const checked = holder.querySelector('input[name="po_number"]:checked');
                                if (checked) {
                                    poNumberInput.value = checked.getAttribute('data-doc');
                                    poNumberInput.classList.remove('is-invalid');
                                    poNumberInput.classList.add('is-valid');
                                } else {
                                    alert('Pilih salah satu PO terlebih dahulu');
                                }
                            });
                        })
                        .catch(() => {
                            holder.innerHTML = '<div class="text-danger">Gagal memuat daftar PO.</div>';
                        });
                });

                function displayPOItems(poDetails) {
                    let html = '<h6>Item dalam PO:</h6><ul class="mb-0">';

                    if (poDetails.details && poDetails.details.length > 0) {
                        poDetails.details.forEach((item, index) => {
                            html +=
                                `<li><strong>${item.materialName || 'N/A'}</strong> - ${parseInt(item.Qty || 0).toLocaleString()} pcs</li>`;
                        });
                    } else {
                        html += '<li>Tidak ada item ditemukan</li>';
                    }

                    html += '</ul>';
                    poItemsList.innerHTML = html;
                }

                function populateSupplierInfo(poDetails) {
                    if (poDetails.header && poDetails.header.SupplierCode) {
                        // Fetch supplier info
                        fetch(
                                `{{ route('supplier-tickets.get-po-details') }}?po_number=${encodeURIComponent(poDetails.header.DocNo)}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                // console.log(data);
                                if (data) {
                                    document.getElementById('supplier_name').value = data.header.supplierName ||'';
                                    document.getElementById('supplier_contact').value = data.header.supplierContact || '';
                                    document.getElementById('supplier_email').value = data.header.supplierEmail || '';
                                    document.getElementById('supplier_address').value = data.header.supplierAddress || '';
                                }
                            });
                    }
                }

                function hidePOItems() {
                    poItemsPreview.style.display = 'none';
                    poItemsList.innerHTML = '';
                }

                function resetPOStatus() {
                    setPOIcon('none');
                }

                function setPOIcon(state) {
                    // state: none | spinner | ok | fail
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
            });

            // Function to load supplier info based on current user
            function loadSupplierInfo() {
                fetch('{{ route("supplier-tickets.get-supplier-info") }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data) {
                            const supplier = data.data;
                            // console.log(supplier);
                            document.getElementById('supplier_name').value = supplier.Name || '';
                            document.getElementById('supplier_contact').value = supplier.Contact || '';
                            document.getElementById('supplier_email').value = supplier.Email || '';
                            document.getElementById('supplier_address').value = supplier.Address || '';
                        }
                    })
                    .catch(error => {
                        console.log('Error loading supplier info:', error);
                    });
            }
        </script>
    @endsection

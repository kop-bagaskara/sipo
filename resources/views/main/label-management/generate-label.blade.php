@extends('main.layouts.main')
@section('title')
    Generate Label - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            line-height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }
        .generate-form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .template-info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #4472C4;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .field-group {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .field-required {
            color: #d33;
        }
    </style>
@endsection
@section('page-title')
    Generate Label - {{ $template->template_name }}
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Generate Label</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('label-management.customer.show', $template->customer_id) }}">{{ $template->customer->customer_name }}</a></li>
                <li class="breadcrumb-item active">Generate Label</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Generate Label</h4>
                            <p class="text-muted mb-0">
                                <i class="mdi mdi-file-excel"></i> Template: <strong>{{ $template->template_name }}</strong>
                            </p>
                        </div>
                        <a href="{{ route('label-management.customer.show', $template->customer_id) }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="template-info-box">
                        <i class="mdi mdi-information"></i>
                        <strong>Informasi:</strong> Header (PT. KRISANTHIUM OFFSET PRINTING) dan Customer sudah PATEN di template PDF.
                        Anda hanya perlu mengisi field-field di bawah ini, lalu system akan langsung generate PDF.
                    </div>

                    <form method="POST" action="{{ route('label-management.template.generate', $template->id) }}" id="generateForm">
                        @csrf

                        <div class="generate-form-section">
                            <h5><i class="mdi mdi-form-select"></i> Isi Data Label</h5>
                            <p class="text-muted mb-3">Isi field-field yang diperlukan untuk generate label</p>

                            @php
                                $fieldMapping = $template->field_mapping ?? [];
                            @endphp

                            @if(empty($fieldMapping))
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert"></i> Template belum memiliki field mapping.
                                    Silakan <a href="{{ route('label-management.template.edit', $template->id) }}">edit template</a> terlebih dahulu.
                                </div>
                            @else
                                <!-- WOT Search Section -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="mdi mdi-information"></i>
                                            <strong>Cara Pengisian:</strong> Pilih atau ketik WOT (Work Order Number) terlebih dahulu, kemudian field lainnya akan terisi otomatis dari database.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">
                                                WOT (Work Order Number)
                                                <span class="field-required">*</span>
                                            </label>
                                            <select class="form-control" id="wot_search" name="wot_search" style="width: 100%;">
                                                <option value="">Pilih atau ketik WOT...</option>
                                            </select>
                                            <input type="hidden" id="wot_value" name="WOT" value="{{ old('WOT') }}">
                                            <small class="form-text text-muted">Pilih WOT untuk auto-fill field lainnya</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Special fields: MESIN, SHIFT, and OPERATOR -->
                                @php
                                    $hasMesinShift = false;
                                    $hasOperator = false;
                                    foreach($fieldMapping as $field) {
                                        if ($field['field_name'] === 'MESIN_SHIFT') {
                                            $hasMesinShift = true;
                                        }
                                        if ($field['field_name'] === 'OPERATOR') {
                                            $hasOperator = true;
                                        }
                                    }
                                @endphp

                                @if($hasMesinShift)
                                    <div class="row mb-3">
                                        <div class="col-md-6 field-group">
                                            <label class="field-label">
                                                MESIN
                                                <span class="field-required">*</span>
                                            </label>
                                            <select class="form-control select2" id="MESIN" name="MESIN" style="width: 100%;">
                                                <option value="">Pilih Mesin...</option>
                                                @foreach($machines as $machine)
                                                    <option value="{{ $machine->Code }}">{{ $machine->Code }} - {{ $machine->Description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 field-group">
                                            <label class="field-label">
                                                SHIFT
                                                <span class="field-required">*</span>
                                            </label>
                                            <select class="form-control" id="SHIFT" name="SHIFT">
                                                <option value="">Pilih Shift...</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Hidden field for MESIN_SHIFT (combined value) -->
                                    <input type="hidden" id="MESIN_SHIFT" name="MESIN_SHIFT" value="{{ old('MESIN_SHIFT') }}">
                                @endif

                                <div class="row">
                                    @php
                                        $operatorField = null;
                                        if ($hasOperator) {
                                            $operatorField = collect($fieldMapping)->firstWhere('field_name', 'OPERATOR');
                                        }
                                        $otherFields = collect($fieldMapping)->filter(function($field) {
                                            return $field['field_name'] !== 'MESIN_SHIFT' && $field['field_name'] !== 'OPERATOR';
                                        });
                                        $fieldCount = $otherFields->count();
                                        $operatorIncluded = $hasOperator ? 1 : 0;
                                        $totalFields = $fieldCount + $operatorIncluded;
                                    @endphp

                                    @if($hasOperator && $operatorField)
                                        <div class="col-md-6 field-group">
                                            <label class="field-label">
                                                OPERATOR
                                                @if(isset($operatorField['required']) && $operatorField['required'])
                                                    <span class="field-required">*</span>
                                                @endif
                                            </label>
                                            <select class="form-control select2" id="OPERATOR" name="OPERATOR" style="width: 100%;">
                                                <option value="">Pilih Operator...</option>
                                                @foreach($operators as $operator)
                                                    <option value="{{ $operator->Kode }}">{{ $operator->Kode }} - {{ $operator->Nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @foreach($otherFields as $field)
                                        <div class="col-md-6 field-group">
                                            <label class="field-label">
                                                {{ $field['label'] ?? $field['field_name'] }}
                                                @if(isset($field['required']) && $field['required'])
                                                    <span class="field-required">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="{{ $field['field_name'] }}"
                                                   id="{{ $field['field_name'] }}"
                                                   value="{{ old($field['field_name']) }}"
                                                   placeholder="Masukkan {{ $field['label'] ?? $field['field_name'] }}"
                                                   @if(isset($field['required']) && $field['required']) required @endif>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">Jumlah Label</label>
                                            <input type="number"
                                                   class="form-control"
                                                   name="quantity"
                                                   id="quantity"
                                                   value="{{ old('quantity', 1) }}"
                                                   min="1"
                                                   max="10"
                                                   placeholder="Jumlah label per halaman">
                                            <small class="form-text text-muted">Berapa label yang ingin digenerate (1-10 per halaman)</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group d-flex justify-content-between mt-4">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                                    <i class="mdi mdi-file-document-edit"></i> Generate Label
                                </button>
                                <a href="{{ route('label-management.customer.show', $template->customer_id) }}" class="btn btn-secondary btn-lg">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="mdi mdi-information"></i> File akan di-download setelah generate
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Function to combine MESIN and SHIFT into MESIN_SHIFT
        function updateMesinShift() {
            const mesin = $('#MESIN').val() || '';
            const shift = $('#SHIFT').val() || '';
            if (mesin && shift) {
                $('#MESIN_SHIFT').val(mesin + '/' + shift);
            } else {
                $('#MESIN_SHIFT').val('');
            }
        }

        // Initialize Select2 for MESIN dropdown
        $('#MESIN').select2({
            placeholder: 'Pilih Mesin...',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for OPERATOR dropdown
        $('#OPERATOR').select2({
            placeholder: 'Pilih Operator...',
            allowClear: true,
            width: '100%'
        });

        // Handle MESIN and SHIFT change to combine into MESIN_SHIFT
        $('#MESIN, #SHIFT').on('change', function() {
            updateMesinShift();
        });

        // Initialize Select2 for WOT search
        $('#wot_search').select2({
            placeholder: 'Pilih atau ketik WOT (Work Order Number)...',
            allowClear: true,
            ajax: {
                url: '{{ route("label-management.search-work-order") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.work_orders, function (wo) {
                            return {
                                id: wo.DocNo,
                                text: wo.DocNo + (wo.SODocNo ? ' - ' + wo.SODocNo : '') + (wo.MaterialCode ? ' (' + wo.MaterialCode + ')' : ''),
                                wo: wo
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // Field mapping dari template (dari PHP ke JavaScript)
        const templateFieldMapping = @json($template->field_mapping ?? []);
        
        // Helper function untuk mencari field berdasarkan field_name atau variasi nama
        function findField(fieldName, variations = []) {
            let $field = null;
            const searchNames = [fieldName].concat(variations);
            
            for (let i = 0; i < searchNames.length; i++) {
                const name = searchNames[i];
                // Try by ID
                $field = $('#' + name);
                // Try by name attribute (input)
                if ($field.length === 0) {
                    $field = $('input[name="' + name + '"]');
                }
                // Try by name attribute (select)
                if ($field.length === 0) {
                    $field = $('select[name="' + name + '"]');
                }
                // Try case-insensitive
                if ($field.length === 0) {
                    $field = $('input[name*="' + name.toLowerCase() + '"], input[name*="' + name.toUpperCase() + '"]');
                }
                if ($field.length > 0) break;
            }
            return $field;
        }

        // Helper function untuk fill field
        function fillField(fieldName, value, dataSource = '') {
            if (!value || value === '') return false;
            
            const $field = findField(fieldName);
            if ($field && $field.length > 0) {
                $field.val(value).trigger('change');
                console.log('✓ Field ' + fieldName + (dataSource ? ' (' + dataSource + ')' : '') + ' = ' + value);
                return true;
            }
            return false;
        }

        // Handle WOT selection
        $('#wot_search').on('select2:select', function (e) {
            const data = e.params.data;
            const wot = data.id;

            // Set hidden input
            $('#wot_value').val(wot);

            // Fetch work order details
            $.ajax({
                url: '{{ route("label-management.work-order.get", ":wot") }}'.replace(':wot', wot),
                type: 'GET',
                data: {
                    template_id: {{ $template->id }}
                },
                dataType: 'json',
                success: function(response) {
                    // Mapping data work order ke field template
                    // 1. WOT -> field yang namanya mengandung "wo", "wot", "no.wo", "nomor wo"
                    const wotFieldNames = ['WOT', 'NO_WO', 'NO_WOT', 'NOMOR_WO', 'nomor_wo', 'no_wo'];
                    for (let i = 0; i < wotFieldNames.length; i++) {
                        if (fillField(wotFieldNames[i], response.WOT, 'WOT')) break;
                    }
                    
                    // 2. MaterialCode/KODE_DESIGN -> field yang namanya mengandung "kode", "design", "desain", "material"
                    // Prioritize KODE_DESIGN for TSPM, MaterialCode for others
                    const materialCodeValue = response.KODE_DESIGN || response.MaterialCode || '';
                    const materialCodeFieldNames = ['KODE_DESIGN', 'KODE_DESAIN', 'MaterialCode', 'MATERIAL_CODE', 'kode_design', 'kode_desain'];
                    for (let i = 0; i < materialCodeFieldNames.length; i++) {
                        if (fillField(materialCodeFieldNames[i], materialCodeValue, 'KODE_DESIGN/MaterialCode')) break;
                    }
                    
                    // 3. ITEM/NAMA_PRODUK -> field yang namanya mengandung "item", "produk", "nama"
                    // Prioritize NAMA_PRODUK for TSPM, ITEM for others
                    const itemValue = response.NAMA_PRODUK || response.ITEM || '';
                    const itemFieldNames = ['NAMA_PRODUK', 'ITEM', 'PRODUK', 'NAMA', 'nama_produk', 'item', 'produk'];
                    for (let i = 0; i < itemFieldNames.length; i++) {
                        if (fillField(itemFieldNames[i], itemValue, 'NAMA_PRODUK/ITEM')) break;
                    }
                    
                    // 4. TGL_PRODUKSI -> field yang namanya mengandung "tgl", "tanggal", "produksi"
                    const tglProduksiFieldNames = ['TGL_PRODUKSI', 'TANGGAL_PRODUKSI', 'TanggalProduksi', 'tgl_produksi', 'tanggal_produksi'];
                    for (let i = 0; i < tglProduksiFieldNames.length; i++) {
                        if (fillField(tglProduksiFieldNames[i], response.TGL_PRODUKSI, 'TGL_PRODUKSI')) break;
                    }
                    
                    // 5. ISI/QTY -> field yang namanya mengandung "isi", "qty", "quantity"
                    const isiValue = response.ISI || response.Qty_PC || response.Qty || '';
                    const isiFieldNames = ['ISI', 'QTY', 'QUANTITY', 'isi', 'qty', 'quantity'];
                    for (let i = 0; i < isiFieldNames.length; i++) {
                        if (fillField(isiFieldNames[i], isiValue, 'ISI/QTY')) break;
                    }
                    
                    // 6. BATCH_NO -> field yang namanya mengandung "batch"
                    const batchNoFieldNames = ['BATCH_NO', 'BATCHNO', 'BatchNo', 'batch_no', 'no_batch'];
                    for (let i = 0; i < batchNoFieldNames.length; i++) {
                        if (fillField(batchNoFieldNames[i], response.BATCH_NO, 'BATCH_NO')) break;
                    }
                    
                    // 7. NO_BOX -> field yang namanya mengandung "box"
                    const noBoxFieldNames = ['NO_BOX', 'NOBOX', 'NoBox', 'no_box', 'box_no'];
                    for (let i = 0; i < noBoxFieldNames.length; i++) {
                        if (fillField(noBoxFieldNames[i], response.BatchInfo || '', 'NO_BOX')) break;
                    }
                    
                    // 8. TGL_EXPIRED/TANGGAL_KIRIM -> field yang namanya mengandung "expired", "kirim", "exp"
                    // Prioritize TGL_EXPIRED for TSPM, TANGGAL_KIRIM for others
                    const expiredValue = response.TGL_EXPIRED || response.ExpiryDate || '';
                    const expiredFieldNames = ['TGL_EXPIRED', 'TANGGAL_KIRIM', 'TGL_KIRIM', 'EXPIRED_DATE', 'tgl_expired', 'tanggal_kirim'];
                    for (let i = 0; i < expiredFieldNames.length; i++) {
                        if (fillField(expiredFieldNames[i], expiredValue, 'TGL_EXPIRED/TANGGAL_KIRIM')) break;
                    }
                    
                    // 8b. NO_PO -> field yang namanya mengandung "po", "purchase order"
                    if (response.NO_PO) {
                        const poFieldNames = ['NO_PO', 'NOPO', 'NoPO', 'no_po', 'po'];
                        for (let i = 0; i < poFieldNames.length; i++) {
                            if (fillField(poFieldNames[i], response.NO_PO, 'NO_PO')) break;
                        }
                    }
                    
                    // 9. PC_NO (hanya untuk Unilever) -> field yang namanya mengandung "pc"
                    if (response.PC_NO) {
                        const pcNoFieldNames = ['PC_NO', 'PCNO', 'PCNo', 'pc_no', 'pc'];
                        for (let i = 0; i < pcNoFieldNames.length; i++) {
                            if (fillField(pcNoFieldNames[i], response.PC_NO, 'PC_NO')) break;
                        }
                    }
                    
                    // 10. MC_NO (hanya untuk Unilever) -> field yang namanya mengandung "mc"
                    if (response.MC_NO) {
                        const mcNoFieldNames = ['MC_NO', 'MCNO', 'MCNo', 'mc_no', 'mc'];
                        for (let i = 0; i < mcNoFieldNames.length; i++) {
                            if (fillField(mcNoFieldNames[i], response.MC_NO, 'MC_NO')) break;
                        }
                    }
                    
                    // 11. Coba mapping berdasarkan field_mapping template (lebih spesifik)
                    // Loop melalui field_mapping dan coba match dengan data work order
                    if (templateFieldMapping && templateFieldMapping.length > 0) {
                        templateFieldMapping.forEach(function(field) {
                            const fieldName = field.field_name || '';
                            if (!fieldName) return;
                            
                            // Mapping berdasarkan field_name yang ada di template
                            let value = null;
                            const fieldNameUpper = fieldName.toUpperCase();
                            
                            if (fieldNameUpper.includes('WOT') || fieldNameUpper.includes('WO') || fieldNameUpper.includes('NOMOR')) {
                                value = response.WOT;
                            } else if (fieldNameUpper.includes('KODE') && (fieldNameUpper.includes('DESAIN') || fieldNameUpper.includes('DESIGN') || fieldNameUpper.includes('MATERIAL'))) {
                                value = response.MaterialCode;
                            } else if (fieldNameUpper.includes('ITEM') || fieldNameUpper.includes('PRODUK') || fieldNameUpper.includes('NAMA')) {
                                value = response.ITEM;
                            } else if (fieldNameUpper.includes('TGL') && fieldNameUpper.includes('PRODUKSI')) {
                                value = response.TGL_PRODUKSI;
                            } else if (fieldNameUpper.includes('ISI') || (fieldNameUpper.includes('QTY') && !fieldNameUpper.includes('PC'))) {
                                value = response.ISI || response.Qty || '';
                            } else if (fieldNameUpper.includes('BATCH')) {
                                value = response.BATCH_NO || response.BatchNo || '';
                            } else if (fieldNameUpper.includes('BOX')) {
                                value = response.BatchInfo || '';
                            } else if (fieldNameUpper.includes('KIRIM') || fieldNameUpper.includes('EXPIRED') || fieldNameUpper.includes('EXP')) {
                                value = response.ExpiryDate || '';
                            } else if (fieldNameUpper.includes('PC') && !fieldNameUpper.includes('NO')) {
                                value = response.PC_NO || '';
                            } else if (fieldNameUpper === 'PC_NO' || fieldNameUpper === 'PCNO') {
                                value = response.PC_NO || '';
                            } else if (fieldNameUpper === 'MC_NO' || fieldNameUpper === 'MCNO') {
                                value = response.MC_NO || '';
                            }
                            
                            if (value) {
                                fillField(fieldName, value, 'template mapping');
                            }
                        });
                    }


                    // Handle MESIN_SHIFT if provided (parse into MESIN and SHIFT)
                    if (response.MESIN_SHIFT) {
                        const mesinShift = response.MESIN_SHIFT;
                        const parts = mesinShift.split('/');
                        if (parts.length >= 2) {
                            $('#MESIN').val(parts[0]).trigger('change');
                            $('#SHIFT').val(parts[1]).trigger('change');
                            updateMesinShift();
                            console.log('✓ MESIN_SHIFT parsed:', parts[0], '/', parts[1]);
                        }
                    }

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Data berhasil diambil!',
                        text: 'Field telah terisi otomatis dari Work Order',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil data Work Order'
                    });
                }
            });
        });

        // Clear fields when WOT is cleared
        $('#wot_search').on('select2:clear', function (e) {
            $('#wot_value').val('');
        });

        $('#generateForm').on('submit', function(e) {
            e.preventDefault();

            // Ensure MESIN_SHIFT is updated before submit
            updateMesinShift();

            const form = $(this);
            const formData = new FormData(this);

            // Show loading
            Swal.fire({
                title: 'Generate Label...',
                text: 'Sedang memproses generate label',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit via AJAX
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(response) {
                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Label berhasil digenerate',
                        showCancelButton: true,
                        confirmButtonText: 'Download',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed && response.download_url) {
                            // Download file
                            const link = document.createElement('a');
                            link.href = response.download_url;
                            link.download = response.filename;
                            link.target = '_blank';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                        // Reload halaman untuk tampilkan label baru di history
                        window.location.href = '{{ route("label-management.template.show", $template->id) }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Terjadi kesalahan saat generate label'
                    });
                }
            }).catch(function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat generate label';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        let errorList = '';
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorList += '<li>' + message + '</li>';
                            });
                        });
                        errorMessage = '<div style="text-align: left;"><strong>Validasi gagal:</strong><ul style="margin-top: 10px;">' + errorList + '</ul></div>';
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage
                });
            });
        });
    </script>
@endsection


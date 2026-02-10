@extends('main.layouts.main')
@section('title')
    Simple Builder - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        body {
            overflow: hidden;
        }
        .builder-container {
            display: flex;
            height: calc(100vh - 60px);
        }
        .builder-sidebar {
            width: 350px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }
        .builder-preview {
            flex: 1;
            background: #f5f5f5;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .preview-label {
            background: white;
            width: 800px;
            min-height: 500px;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .element-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: move;
            position: relative;
        }
        .element-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0,123,255,0.2);
        }
        .element-item.selected {
            border-color: #007bff;
            background: #e7f3ff;
        }
        .element-controls {
            display: flex;
            gap: 5px;
            margin-top: 8px;
        }
        .element-controls button {
            padding: 4px 8px;
            font-size: 11px;
        }
        .preview-element {
            position: absolute;
            border: 2px dashed transparent;
            cursor: pointer;
            min-width: 50px;
            min-height: 20px;
            transition: all 0.2s;
        }
        .preview-element:hover {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        .preview-element.selected {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            box-shadow: 0 0 8px rgba(40,167,69,0.4);
        }
        .preview-element.is-field {
            background: #fff3cd !important;
            border: 2px dashed #ffc107 !important;
            padding: 4px 8px;
            border-radius: 3px;
        }
        .preview-element.is-label {
            background: transparent;
        }
        .field-placeholder {
            background: #fff3cd;
            border: 1px dashed #ffc107;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .element-context-menu {
            position: fixed;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 5px 0;
            z-index: 10000;
            min-width: 180px;
            display: none;
        }
        .element-context-menu-item {
            padding: 8px 15px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .element-context-menu-item:hover {
            background: #f8f9fa;
        }
        .element-context-menu-item i {
            width: 20px;
            text-align: center;
        }
        .element-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }
        .element-badge.field {
            background: #fff3cd;
            color: #856404;
        }
        .element-badge.label {
            background: #d1ecf1;
            color: #0c5460;
        }
        .section-title {
            font-weight: bold;
            color: #4472C4;
            margin: 20px 0 10px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #4472C4;
        }
        .form-group-sm {
            margin-bottom: 10px;
        }
        .form-group-sm label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .form-group-sm input,
        .form-group-sm select,
        .form-group-sm textarea {
            font-size: 12px;
            padding: 6px;
        }
    </style>
@endsection

@section('content')
<!-- Toolbar -->
<div style="background: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('label-management.template.edit', $template->id) }}" class="btn btn-sm btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-sm btn-primary" onclick="$('#excelFileInput').click()" title="Upload Excel & Konversi ke HTML">
            <i class="mdi mdi-file-excel"></i> Upload Excel
        </button>
        <input type="file" id="excelFileInput" accept=".xlsx,.xls" style="display: none;" onchange="convertExcel()">
        <button class="btn btn-sm btn-success" onclick="saveTemplate()" title="Simpan (Ctrl+S)">
            <i class="mdi mdi-content-save"></i> Simpan
        </button>
        <a href="{{ route('label-management.template.preview', $template->id) }}" class="btn btn-sm btn-info" target="_blank">
            <i class="mdi mdi-file-pdf-box"></i> Preview
        </a>
    </div>
    <div>
        <span class="text-muted" style="font-size: 12px;">
            <i class="mdi mdi-wizard-hat"></i> Simple Builder - Mudah & Cepat
        </span>
    </div>
</div>

<div class="builder-container">
    <!-- Sidebar -->
    <div class="builder-sidebar">
        <h5 style="margin-bottom: 15px;">
            <i class="mdi mdi-palette"></i> Buat Layout Label
        </h5>

        <!-- Info Upload Excel -->
        <div class="alert alert-info" style="font-size: 11px; padding: 10px; margin-bottom: 15px;">
            <i class="mdi mdi-lightbulb-on"></i> <strong>Tips:</strong> Buat layout di Excel, lalu klik tombol "Upload Excel" di toolbar untuk konversi otomatis ke HTML!
        </div>

        <!-- Tambah Elemen -->
        <div class="section-title">Tambah Elemen</div>
        <button class="btn btn-primary btn-sm btn-block mb-2" onclick="addElement('text')">
            <i class="mdi mdi-format-text"></i> Tambah Text
        </button>
        <button class="btn btn-warning btn-sm btn-block mb-2" onclick="addElement('field')">
            <i class="mdi mdi-tag"></i> Tambah Field Data
        </button>
        <button class="btn btn-success btn-sm btn-block mb-2" onclick="addElement('table')">
            <i class="mdi mdi-table"></i> Tambah Tabel
        </button>
        <button class="btn btn-info btn-sm btn-block mb-2" onclick="addElement('line')">
            <i class="mdi mdi-minus"></i> Tambah Garis
        </button>

        <!-- Daftar Elemen -->
        <div class="section-title">Elemen di Label</div>
        <div id="elementsList">
            <div class="text-center text-muted py-3" style="font-size: 12px;">
                Belum ada elemen. Klik tombol di atas untuk menambahkan.
            </div>
        </div>

        <!-- Field Mapping -->
        <div class="section-title">Field yang Tersedia</div>
        <div style="max-height: 200px; overflow-y: auto;">
            @if(count($availableFields) > 0)
                @foreach($availableFields as $field)
                <div class="field-item mb-2 p-2 border rounded" style="font-size: 11px; background: white;">
                    <strong>{{ $field['label'] }}</strong>
                    <small class="d-block text-muted">{{ $field['name'] }}</small>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning" style="font-size: 11px; padding: 8px;">
                    Belum ada field mapping
                </div>
            @endif
        </div>
    </div>

    <!-- Preview Area -->
    <div class="builder-preview">
        <div class="preview-label" id="previewLabel">
            <!-- Elements will be added here -->
        </div>
    </div>
</div>

<!-- Context Menu untuk Edit Elemen -->
<div class="element-context-menu" id="elementContextMenu">
    <div class="element-context-menu-item" onclick="convertToField()">
        <i class="mdi mdi-tag" style="color: #ffc107;"></i>
        <span>Jadikan Field (Bisa Diisi)</span>
    </div>
    <div class="element-context-menu-item" onclick="convertToLabel()">
        <i class="mdi mdi-format-text" style="color: #17a2b8;"></i>
        <span>Jadikan Label (Static Text)</span>
    </div>
    <div class="element-context-menu-item" onclick="editSelectedElement()">
        <i class="mdi mdi-pencil" style="color: #007bff;"></i>
        <span>Edit Elemen</span>
    </div>
    <div class="element-context-menu-item" onclick="deleteSelectedElement()" style="color: #dc3545;">
        <i class="mdi mdi-delete"></i>
        <span>Hapus Elemen</span>
    </div>
</div>

<!-- Modal Edit Elemen -->
<div class="modal fade" id="modalEditElement" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-pencil"></i> Edit Elemen
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group-sm">
                    <label>Jenis Elemen</label>
                    <input type="text" class="form-control" id="editElementType" readonly>
                </div>

                <div id="editContentFields">
                    <!-- Dynamic fields based on element type -->
                </div>

                <div class="form-group-sm">
                    <label>Posisi X (px)</label>
                    <input type="number" class="form-control" id="editPosX" min="0">
                </div>

                <div class="form-group-sm">
                    <label>Posisi Y (px)</label>
                    <input type="number" class="form-control" id="editPosY" min="0">
                </div>

                <div class="form-group-sm">
                    <label>Font Size (px)</label>
                    <input type="number" class="form-control" id="editFontSize" min="8" max="72" value="12">
                </div>

                <div class="form-group-sm">
                    <label>Font Weight</label>
                    <select class="form-control" id="editFontWeight">
                        <option value="normal">Normal</option>
                        <option value="bold">Bold</option>
                    </select>
                </div>

                <div class="form-group-sm">
                    <label>Text Align</label>
                    <select class="form-control" id="editTextAlign">
                        <option value="left">Kiri</option>
                        <option value="center">Tengah</option>
                        <option value="right">Kanan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteElement()">Hapus</button>
                <button type="button" class="btn btn-primary" onclick="updateElement()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        let elements = [];
        let selectedElementId = null;
        let elementCounter = 0;
        let contextMenuElementId = null;
        let contextMenuElement = null;
        let contextMenuPosition = { x: 0, y: 0 };

        // Attach handlers to Excel-imported elements
        function attachExcelElementHandlers() {
            // Find all elements in preview that don't have data-id (from Excel import)
            $('#previewLabel').find('[style*="position: absolute"]').each(function() {
                const $el = $(this);
                if (!$el.attr('data-id') && !$el.hasClass('preview-element')) {
                    // Make it clickable
                    $el.addClass('preview-element is-label');
                    $el.css('cursor', 'pointer');
                    $el.attr('data-excel-element', 'true');
                    
                    // Add click handler
                    $el.off('click.excel').on('click.excel', function(e) {
                        e.stopPropagation();
                        selectExcelElement($(this));
                    });
                    
                    // Add right-click handler
                    $el.off('contextmenu.excel').on('contextmenu.excel', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        showContextMenuForExcelElement(e, $(this));
                    });
                }
            });
        }

        function selectExcelElement($element) {
            // Remove previous selection
            $('#previewLabel').find('.selected').removeClass('selected');
            $element.addClass('selected');
        }

        function showContextMenuForExcelElement(event, $element) {
            contextMenuPosition = { x: event.pageX, y: event.pageY };
            contextMenuElementId = null;
            contextMenuElement = $element;
            
            $('#elementContextMenu').css({
                display: 'block',
                left: event.pageX + 'px',
                top: event.pageY + 'px'
            });
        }

        function showContextMenu(event, elementId) {
            event.preventDefault();
            event.stopPropagation();
            
            contextMenuPosition = { x: event.pageX, y: event.pageY };
            contextMenuElementId = elementId;
            contextMenuElement = null;
            
            $('#elementContextMenu').css({
                display: 'block',
                left: event.pageX + 'px',
                top: event.pageY + 'px'
            });
        }

        function convertToField() {
            if (contextMenuElementId !== null) {
                // Convert from elements array
                const element = elements.find(el => el.id === contextMenuElementId);
                if (element && element.type === 'text') {
                    element.type = 'field';
                    element.fieldName = '';
                    element.content = 'FIELD_NAME';
                    
                    // Auto-select first available field if exists
                    @if(count($availableFields) > 0)
                        element.fieldName = '{{ $availableFields[0]['name'] }}';
                        element.content = '{{ $availableFields[0]['label'] }}';
                    @endif
                    
                    renderElements();
                }
            } else if (contextMenuElement) {
                // Convert Excel-imported element
                const $el = contextMenuElement;
                const currentText = $el.text().trim();
                
                // Extract field name from text or use default
                let fieldName = 'FIELD_NAME';
                @if(count($availableFields) > 0)
                    fieldName = '{{ $availableFields[0]['name'] }}';
                @endif
                
                // Replace content with field placeholder
                $el.removeClass('is-label').addClass('is-field field-placeholder');
                $el.html('{' + '{' + fieldName + '}' + '}');
                $el.attr('data-field-name', fieldName);
            }
            
            hideContextMenu();
        }

        function convertToLabel() {
            if (contextMenuElementId !== null) {
                // Convert from elements array
                const element = elements.find(el => el.id === contextMenuElementId);
                if (element && element.type === 'field') {
                    element.type = 'text';
                    element.content = element.content || 'Label Text';
                    element.fieldName = null;
                    renderElements();
                }
            } else if (contextMenuElement) {
                // Convert Excel-imported element
                const $el = contextMenuElement;
                const currentText = $el.text().trim();
                
                // If it's a field placeholder, extract the text
                if (currentText.match(/^\{\{[A-Z_]+\}\}$/)) {
                    const fieldName = currentText.replace(/\{\{|\}\}/g, '');
                    // Use field label if available
                    @if(count($availableFields) > 0)
                        const fieldLabels = {
                            @foreach($availableFields as $field)
                            '{{ $field['name'] }}': '{{ $field['label'] }}',
                            @endforeach
                        };
                        const labelText = fieldLabels[fieldName] || fieldName;
                        $el.html(labelText);
                    @else
                        $el.html(fieldName);
                    @endif
                }
                
                $el.removeClass('is-field field-placeholder').addClass('is-label');
                $el.removeAttr('data-field-name');
            }
            
            hideContextMenu();
        }

        function editSelectedElement() {
            if (contextMenuElementId !== null) {
                editElement(contextMenuElementId);
            } else if (contextMenuElement) {
                // Edit Excel-imported element
                const $el = contextMenuElement;
                const currentText = $el.text().trim();
                const isField = $el.hasClass('is-field');
                const fieldName = $el.attr('data-field-name') || '';
                
                Swal.fire({
                    title: 'Edit Elemen',
                    html: `
                        <div class="form-group text-left">
                            <label>${isField ? 'Pilih Field Data' : 'Text Label'}</label>
                            ${isField ? `
                                <select id="swal-field-select" class="form-control">
                                    <option value="">-- Pilih Field --</option>
                                    @foreach($availableFields as $field)
                                    <option value="{{ $field['name'] }}" ${fieldName === '{{ $field['name'] }}' ? 'selected' : ''}>
                                        {{ $field['label'] }} ({{ $field['name'] }})
                                    </option>
                                    @endforeach
                                </select>
                            ` : `
                                <input type="text" id="swal-text-input" class="form-control" value="${currentText.replace(/"/g, '&quot;')}" placeholder="Masukkan text label">
                            `}
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        if (isField) {
                            const selectedField = document.getElementById('swal-field-select').value;
                            if (!selectedField) {
                                Swal.showValidationMessage('Pilih field terlebih dahulu');
                                return false;
                            }
                            return selectedField;
                        } else {
                            const text = document.getElementById('swal-text-input').value;
                            if (!text.trim()) {
                                Swal.showValidationMessage('Text tidak boleh kosong');
                                return false;
                            }
                            return text;
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (isField) {
                            const fieldName = result.value;
                            @if(count($availableFields) > 0)
                                const fieldLabels = {
                                    @foreach($availableFields as $field)
                                    '{{ $field['name'] }}': '{{ $field['label'] }}',
                                    @endforeach
                                };
                                const labelText = fieldLabels[fieldName] || fieldName;
                                $el.html('{' + '{' + fieldName + '}' + '}');
                                $el.attr('data-field-name', fieldName);
                            @else
                                $el.html('{' + '{' + fieldName + '}' + '}');
                                $el.attr('data-field-name', fieldName);
                            @endif
                        } else {
                            $el.html(result.value);
                        }
                    }
                });
            }
            
            hideContextMenu();
        }

        function deleteSelectedElement() {
            if (contextMenuElementId !== null) {
                removeElement(contextMenuElementId);
            } else if (contextMenuElement) {
                contextMenuElement.remove();
            }
            
            hideContextMenu();
        }

        function hideContextMenu() {
            $('#elementContextMenu').hide();
            contextMenuElementId = null;
            contextMenuElement = null;
        }

        // Close context menu when clicking outside
        $(document).on('click', function() {
            hideContextMenu();
        });

        // Load saved template
        @if($template->html_template)
            @php
                $savedData = json_decode($template->html_template, true);
                $savedElements = $savedData['elements'] ?? [];
                $savedHtml = $savedData['html'] ?? '';
                $isExcelImport = isset($savedData['source']) && $savedData['source'] === 'excel_import';
            @endphp
            @if($isExcelImport && !empty($savedHtml))
                // Load HTML directly from Excel import
                $(document).ready(function() {
                    try {
                        const savedHtml = @json($savedHtml);
                        if (savedHtml && typeof savedHtml === 'string' && savedHtml.trim() !== '') {
                            $('#previewLabel').html(savedHtml);
                            // Attach handlers to Excel-imported elements
                            setTimeout(() => {
                                attachExcelElementHandlers();
                            }, 100);
                        }
                    } catch(e) {
                        console.error('Error loading Excel template:', e);
                    }
                });
            @elseif(count($savedElements) > 0)
                // Load elements array (from Simple Builder)
                elements = @json($savedElements);
                elementCounter = elements.length > 0 ? Math.max(...elements.map(e => e.id)) + 1 : 0;
                renderElements();
            @elseif(!empty($savedHtml))
                // Fallback: if there's HTML but no elements and not Excel import, load HTML directly
                $(document).ready(function() {
                    try {
                        const savedHtml = @json($savedHtml);
                        if (savedHtml && typeof savedHtml === 'string' && savedHtml.trim() !== '') {
                            $('#previewLabel').html(savedHtml);
                            // Attach handlers to elements
                            setTimeout(() => {
                                attachExcelElementHandlers();
                            }, 100);
                        }
                    } catch(e) {
                        console.error('Error loading template HTML:', e);
                    }
                });
            @endif
        @endif

        function addElement(type) {
            const element = {
                id: elementCounter++,
                type: type,
                content: type === 'text' ? 'Text Baru' :
                        type === 'field' ? 'FIELD_NAME' :
                        type === 'table' ? 'table' : 'line',
                fieldName: type === 'field' ? '' : null,
                x: 50,
                y: 50,
                fontSize: 12,
                fontWeight: 'normal',
                textAlign: 'left',
                width: type === 'table' ? 300 : null,
                height: type === 'table' ? 100 : null,
                rows: type === 'table' ? 3 : null,
                cols: type === 'table' ? 3 : null,
            };

            if (type === 'field' && elements.length === 0) {
                // Auto-select first available field
                @if(count($availableFields) > 0)
                    element.fieldName = '{{ $availableFields[0]['name'] }}';
                    element.content = '{{ $availableFields[0]['label'] }}';
                @endif
            }

            elements.push(element);
            renderElements();
            selectElement(element.id);
        }

        function renderElements() {
            // Render sidebar list
            const listHtml = elements.length > 0 ?
                elements.map(el => {
                    const isField = el.type === 'field';
                    const badgeClass = isField ? 'field' : 'label';
                    const badgeText = isField ? 'FIELD' : 'LABEL';
                    return `
                    <div class="element-item ${selectedElementId === el.id ? 'selected' : ''}"
                         onclick="selectElement(${el.id})">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="font-size: 12px;">
                                    ${getElementTypeLabel(el.type)}
                                    <span class="element-badge ${badgeClass}">${badgeText}</span>
                                </strong>
                                <small class="d-block text-muted" style="font-size: 10px;">
                                    ${el.type === 'field' ? el.fieldName || 'Pilih Field' : el.content.substring(0, 30)}
                                </small>
                            </div>
                            <i class="mdi mdi-${el.type === 'text' ? 'format-text' : el.type === 'field' ? 'tag' : el.type === 'table' ? 'table' : 'minus'}"></i>
                        </div>
                        <div class="element-controls">
                            <button class="btn btn-sm btn-info" onclick="editElement(${el.id})" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="removeElement(${el.id})" title="Hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                `;
                }).join('') :
                '<div class="text-center text-muted py-3" style="font-size: 12px;">Belum ada elemen. Klik tombol di atas untuk menambahkan.</div>';

            $('#elementsList').html(listHtml);

            // Render preview
            const previewHtml = elements.map(el => {
                const style = `position: absolute; left: ${el.x}px; top: ${el.y}px; font-size: ${el.fontSize}px; font-weight: ${el.fontWeight}; text-align: ${el.textAlign};`;
                const isField = el.type === 'field';
                const fieldClass = isField ? 'is-field' : 'is-label';

                if (el.type === 'text') {
                    return `<div class="preview-element ${fieldClass} ${selectedElementId === el.id ? 'selected' : ''}"
                                data-id="${el.id}"
                                data-type="${el.type}"
                                onclick="selectElement(${el.id}); event.stopPropagation();"
                                oncontextmenu="showContextMenu(event, ${el.id}); return false;"
                                style="${style}">${el.content}</div>`;
                } else if (el.type === 'field') {
                    const fieldName = el.fieldName || 'FIELD_NAME';
                    return `<span class="preview-element field-placeholder ${fieldClass} ${selectedElementId === el.id ? 'selected' : ''}"
                                data-id="${el.id}"
                                data-type="${el.type}"
                                onclick="selectElement(${el.id}); event.stopPropagation();"
                                oncontextmenu="showContextMenu(event, ${el.id}); return false;"
                                style="${style}">{` + '{' + fieldName + '}' + '}</span>';
                } else if (el.type === 'table') {
                    let tableHtml = `<table class="preview-element ${selectedElementId === el.id ? 'selected' : ''}"
                                    data-id="${el.id}"
                                    data-type="${el.type}"
                                    onclick="selectElement(${el.id}); event.stopPropagation();"
                                    oncontextmenu="showContextMenu(event, ${el.id}); return false;"
                                    style="${style} border: 1px solid #000; border-collapse: collapse; width: ${el.width || 300}px; height: ${el.height || 100}px;">`;
                    for (let r = 0; r < (el.rows || 3); r++) {
                        tableHtml += '<tr>';
                        for (let c = 0; c < (el.cols || 3); c++) {
                            tableHtml += '<td style="border: 1px solid #000; padding: 5px;">&nbsp;</td>';
                        }
                        tableHtml += '</tr>';
                    }
                    tableHtml += '</table>';
                    return tableHtml;
                } else if (el.type === 'line') {
                    return `<hr class="preview-element ${selectedElementId === el.id ? 'selected' : ''}"
                            data-id="${el.id}"
                            data-type="${el.type}"
                            onclick="selectElement(${el.id}); event.stopPropagation();"
                            oncontextmenu="showContextMenu(event, ${el.id}); return false;"
                            style="${style} width: 100%; border: 1px solid #000; margin: 10px 0;">`;
                }
            }).join('');

            $('#previewLabel').html(previewHtml);
            
            // Attach click handlers to Excel-imported elements
            attachExcelElementHandlers();
        }

        function getElementTypeLabel(type) {
            const labels = {
                'text': 'Text',
                'field': 'Field Data',
                'table': 'Tabel',
                'line': 'Garis'
            };
            return labels[type] || type;
        }

        function selectElement(id) {
            selectedElementId = id;
            renderElements();
        }

        function editElement(id) {
            const element = elements.find(el => el.id === id);
            if (!element) return;

            selectedElementId = id;
            renderElements();

            $('#editElementType').val(getElementTypeLabel(element.type));

            // Dynamic content fields
            let contentHtml = '';
            if (element.type === 'text') {
                contentHtml = `
                    <div class="form-group-sm">
                        <label>Isi Text</label>
                        <textarea class="form-control" id="editContent" rows="3">${element.content}</textarea>
                    </div>
                `;
            } else if (element.type === 'field') {
                contentHtml = `
                    <div class="form-group-sm">
                        <label>Pilih Field</label>
                        <select class="form-control" id="editFieldName">
                            <option value="">-- Pilih Field --</option>
                            @foreach($availableFields as $field)
                            <option value="{{ $field['name'] }}" ${element.fieldName === '{{ $field['name'] }}' ? 'selected' : ''}>
                                {{ $field['label'] }} ({{ $field['name'] }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                `;
            } else if (element.type === 'table') {
                contentHtml = `
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group-sm">
                                <label>Baris</label>
                                <input type="number" class="form-control" id="editRows" min="1" max="10" value="${element.rows || 3}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-sm">
                                <label>Kolom</label>
                                <input type="number" class="form-control" id="editCols" min="1" max="10" value="${element.cols || 3}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-sm">
                                <label>Lebar (px)</label>
                                <input type="number" class="form-control" id="editWidth" min="50" value="${element.width || 300}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-sm">
                                <label>Tinggi (px)</label>
                                <input type="number" class="form-control" id="editHeight" min="50" value="${element.height || 100}">
                            </div>
                        </div>
                    </div>
                `;
            }

            $('#editContentFields').html(contentHtml);
            $('#editPosX').val(element.x);
            $('#editPosY').val(element.y);
            $('#editFontSize').val(element.fontSize);
            $('#editFontWeight').val(element.fontWeight);
            $('#editTextAlign').val(element.textAlign);

            $('#modalEditElement').modal('show');
        }

        function updateElement() {
            const element = elements.find(el => el.id === selectedElementId);
            if (!element) return;

            if (element.type === 'text') {
                element.content = $('#editContent').val();
            } else if (element.type === 'field') {
                const fieldName = $('#editFieldName').val();
                element.fieldName = fieldName;
                @foreach($availableFields as $field)
                    if (fieldName === '{{ $field['name'] }}') {
                        element.content = '{{ $field['label'] }}';
                    }
                @endforeach
            } else if (element.type === 'table') {
                element.rows = parseInt($('#editRows').val()) || 3;
                element.cols = parseInt($('#editCols').val()) || 3;
                element.width = parseInt($('#editWidth').val()) || 300;
                element.height = parseInt($('#editHeight').val()) || 100;
            }

            element.x = parseInt($('#editPosX').val()) || 0;
            element.y = parseInt($('#editPosY').val()) || 0;
            element.fontSize = parseInt($('#editFontSize').val()) || 12;
            element.fontWeight = $('#editFontWeight').val();
            element.textAlign = $('#editTextAlign').val();

            renderElements();
            $('#modalEditElement').modal('hide');
        }

        function deleteElement() {
            Swal.fire({
                title: 'Hapus Elemen?',
                text: 'Apakah Anda yakin ingin menghapus elemen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    removeElement(selectedElementId);
                    $('#modalEditElement').modal('hide');
                }
            });
        }

        function removeElement(id) {
            elements = elements.filter(el => el.id !== id);
            if (selectedElementId === id) {
                selectedElementId = null;
            }
            renderElements();
        }

        function saveTemplate() {
            // Always get HTML from preview first (this includes Excel import)
            // Process Excel-imported elements to preserve field/label conversions
            let previewHtml = $('#previewLabel').html();
            
            // Update HTML with current field/label states from Excel elements
            $('#previewLabel').find('[data-excel-element="true"]').each(function() {
                const $el = $(this);
                const isField = $el.hasClass('is-field');
                const fieldName = $el.attr('data-field-name');
                
                if (isField && fieldName) {
                    // Ensure field placeholder format
                    $el.html('{' + '{' + fieldName + '}' + '}');
                }
            });
            
            // Get updated HTML
            previewHtml = $('#previewLabel').html();
            const hasPreviewHtml = previewHtml && previewHtml.trim() !== '';
            
            let html = '';
            let css = '@page { margin: 0; size: A4; } body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }';
            let saveData = {};

            // Priority: Use preview HTML if available (from Excel import or edited)
            if (hasPreviewHtml) {
                html = previewHtml;
                
                // Check if this is from Excel import (has Excel elements or was originally Excel)
                const hasExcelElements = $('#previewLabel').find('[data-excel-element="true"]').length > 0;
                const isExcelImport = (hasExcelElements || previewHtml.includes('position: absolute')) && elements.length === 0;
                
                saveData = {
                    html: html,
                    css: css,
                    pageOrientation: 'portrait'
                };
                
                if (isExcelImport || hasExcelElements) {
                    saveData.source = 'excel_import';
                } else if (elements.length > 0) {
                    // If there are elements, save them too
                    saveData.elements = elements;
                }
            } else if (elements.length > 0) {
                // Convert elements to HTML (from Simple Builder)
                elements.forEach(el => {
                    const style = `position: absolute; left: ${el.x}px; top: ${el.y}px; font-size: ${el.fontSize}px; font-weight: ${el.fontWeight}; text-align: ${el.textAlign};`;

                    if (el.type === 'text') {
                        html += `<div style="${style}">${el.content}</div>`;
                    } else if (el.type === 'field') {
                        const fieldName = el.fieldName || 'FIELD_NAME';
                        html += `<span class="field-placeholder" style="${style}">{` + '{' + fieldName + '}' + '}</span>';
                    } else if (el.type === 'table') {
                        html += `<table style="${style} border: 1px solid #000; border-collapse: collapse; width: ${el.width || 300}px; height: ${el.height || 100}px;">`;
                        for (let r = 0; r < (el.rows || 3); r++) {
                            html += '<tr>';
                            for (let c = 0; c < (el.cols || 3); c++) {
                                html += '<td style="border: 1px solid #000; padding: 5px;">&nbsp;</td>';
                            }
                            html += '</tr>';
                        }
                        html += '</table>';
                    } else if (el.type === 'line') {
                        html += `<hr style="${style} width: 100%; border: 1px solid #000; margin: 10px 0;">`;
                    }
                });

                saveData = {
                    html: html,
                    css: css,
                    elements: elements,
                    pageOrientation: 'portrait'
                };
            }

            // Validate data before save
            if (!html || html.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Tidak ada konten untuk disimpan. Silakan tambahkan elemen atau upload Excel terlebih dahulu.'
                });
                return;
            }

            // Ensure saveData has html
            saveData.html = html;

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("label-management.template.save-workspace", $template->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    html_template: JSON.stringify(saveData),
                    css_styles: css,
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Template berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }

        // Convert Excel to HTML
        function convertExcel() {
            const fileInput = document.getElementById('excelFileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                return;
            }

            Swal.fire({
                title: 'Mengkonversi Excel...',
                html: 'Sedang memproses file Excel dan mengkonversi ke HTML template. Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('excel_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("label-management.template.convert-excel", $template->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Update preview immediately with converted HTML
                        if (response.html) {
                            $('#previewLabel').html(response.html);
                            // Attach handlers to Excel-imported elements
                            setTimeout(() => {
                                attachExcelElementHandlers();
                            }, 100);
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: 'Excel berhasil dikonversi ke HTML template.<br><br>' +
                                  '<a href="' + response.preview_url + '" target="_blank" class="btn btn-primary">Lihat Preview PDF</a>',
                            confirmButtonText: 'OK'
                        });
                        // Tidak reload agar preview tetap terlihat
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Gagal mengkonversi Excel'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengkonversi Excel';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                },
                complete: function() {
                    fileInput.value = '';
                }
            });
        }

        // Keyboard shortcut
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveTemplate();
            }
        });

        // Make elements draggable in preview
        let isDragging = false;
        let dragElement = null;
        let dragOffset = { x: 0, y: 0 };

        $(document).on('mousedown', '.preview-element', function(e) {
            isDragging = true;
            dragElement = $(this);
            const elementId = parseInt(dragElement.data('id'));
            const element = elements.find(el => el.id === elementId);
            if (element) {
                const rect = dragElement[0].getBoundingClientRect();
                const previewRect = $('#previewLabel')[0].getBoundingClientRect();
                dragOffset.x = e.clientX - rect.left;
                dragOffset.y = e.clientY - rect.top;
            }
            e.preventDefault();
        });

        $(document).on('mousemove', function(e) {
            if (isDragging && dragElement) {
                const elementId = parseInt(dragElement.data('id'));
                const element = elements.find(el => el.id === elementId);
                if (element) {
                    const previewRect = $('#previewLabel')[0].getBoundingClientRect();
                    const newX = e.clientX - previewRect.left - dragOffset.x;
                    const newY = e.clientY - previewRect.top - dragOffset.y;

                    element.x = Math.max(0, newX);
                    element.y = Math.max(0, newY);

                    renderElements();
                }
            }
        });

        $(document).on('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                dragElement = null;
            }
        });
    </script>
@endsection

